<?php

namespace App\Http\Controllers;

use App\Models\Skills;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    
public function show()
{
    $member =
        auth()->user()
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

    $monthlyAttendance =
        $member
        ?->attendances()
        ->whereMonth(
            'tanggal',
            now()->month
        )
        ->whereYear(
            'tanggal',
            now()->year
        )
        ->count() ?? 0;

    $tasks =
        \App\Models\Task::with([
            'project',
            'status',
            'priority'
        ])
        ->whereHas(
            'assignees',
            function ($query) use ($member) {

                $query->where(
                    'members.id_member',
                    $member->id_member
                );

            }
        )
        ->get();

    return view(
        'profile.show',
        compact(
            'member',
            'monthlyAttendance',
            'tasks'
        )
    );
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