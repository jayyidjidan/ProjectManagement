<?php

namespace App\Http\Controllers;

use App\Models\Skills;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

public function show()
{
    $member = auth()->user()
        ->member()
        ->with([
            'user.role',
            'jabatan',
            'skills',
            'attendances',
            'scrumUpdates.task1',
            'scrumUpdates.task2'
        ])
        ->first();

    $monthlyAttendance = $member
        ?->attendances()
        ->whereMonth('tanggal', now()->month)
        ->whereYear('tanggal', now()->year)
        ->count() ?? 0;

    // --- TAMBAHAN BARU: SUM OVERTIME BULAN INI ---
    $monthlyOvertime = $member
        ?->overtimes()
        ->whereMonth('tanggal', now()->month)
        ->whereYear('tanggal', now()->year)
        ->sum('durasi_jam') ?? 0; // Ganti 'duration' dengan nama kolom total jam lemburnya

    $tasks = \App\Models\Task::with([
            'project',
            'status',
            'priority'
        ])
        ->whereHas('assignees', function ($query) use ($member) {
            $query->where('members.id_member', $member->id_member);
        })
        ->get();

    // Mengelompokkan task berdasarkan status
    $planning = $tasks->filter(fn($task) => strtolower($task->status?->status_name) === 'planning');
    $ongoing  = $tasks->filter(fn($task) => strtolower($task->status?->status_name) === 'on going' || strtolower($task->status?->status_name) === 'ongoing');
    $reviewed = $tasks->filter(fn($task) => strtolower($task->status?->status_name) === 'reviewed');
    $finished = $tasks->filter(fn($task) => strtolower($task->status?->status_name) === 'finished');
    
    // Filter untuk task yang melewati tenggat waktu (Overdue)
    $overdue  = $tasks->filter(fn($task) => 
        strtolower($task->status?->status_name) !== 'finished' && 
        $task->deadline_task && 
        \Carbon\Carbon::parse($task->deadline_task)->isPast()
    );

    return view('profile.show', compact(
        'member',
        'monthlyAttendance',
        'monthlyOvertime',
        'tasks',
        'planning',
        'ongoing',
        'reviewed',
        'finished',
        'overdue'
    ));
}

    public function edit()
    {
        $member =
            auth()->user()
            ->member()
            ->with('skills')
            ->first();

        $skills =
            Skills::orderBy(
                'skill_name'
            )->get();

        return view(
            'profile.edit',
            compact(
                'member',
                'skills'
            )
        );
    }

    public function update(
        Request $request
    )
    {
        $member =
            auth()->user()->member;

        $request->validate([

            'member_name' =>
                'required|max:150',

            'profile_photo' =>
                'nullable|image|max:2048',

            'skills' =>
                'nullable|array'

        ]);

        $photo =
            $member->profile_photo;

        if (
            $request->hasFile(
                'profile_photo'
            )
        ) {

            $photo =
                $request
                ->file(
                    'profile_photo'
                )
                ->store(
                    'profile-photos',
                    'public'
                );
        }

        $member->update([

            'member_name' =>
                $request->member_name,

            'profile_photo' =>
                $photo

        ]);

        $member->skills()->sync(
            $request->skills ?? []
        );

        return redirect()
            ->route(
                'profile.show'
            )
            ->with(
                'success',
                'Profile updated.'
            );
    }

    public function passwordForm()
    {
        return view(
            'profile.password'
        );
    }

    public function changePassword(
        Request $request
    )
    {
        $request->validate([

            'current_password' =>
                'required',

            'password' =>
                'required|min:8|confirmed'

        ]);

        $user =
            auth()->user();

        if (
            !Hash::check(
                $request->current_password,
                $user->password
            )
        ) {

            return back()->withErrors([

                'current_password' =>
                    'Current password incorrect.'

            ]);
        }

        $user->update([

            'password' =>
                Hash::make(
                    $request->password
                )

        ]);

        return back()->with(
            'success',
            'Password changed successfully.'
        );
    }
}