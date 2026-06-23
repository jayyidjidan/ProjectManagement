<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Members;
use App\Models\Roles;
use App\Models\Jabatan;
use App\Models\Users;
use App\Models\Skills;

class MemberController extends Controller
{
    public function index()
    {
        // 1. Tangkap keyword pencarian global
        $keyword = request('q');

        $members = Members::with([
            'user.role',
            'jabatan'
        ])
        // 2. TAMBAHKAN LOGIKA SEARCH DI SINI
        ->when($keyword, function ($query, $keyword) {
            // Mencari berdasarkan nama member
            $query->where('member_name', 'like', "%{$keyword}%")
                  // ATAU mencari berdasarkan nama jabatannya (via relasi)
                  ->orWhereHas('jabatan', function ($q) use ($keyword) {
                      // Sesuaikan 'nama_jabatan' dengan kolom di tabel jabatan milikmu
                      $q->where('position_name', 'like', "%{$keyword}%"); 
                  })
                  // Opsional: ATAU mencari berdasarkan email/username akunnya
                  ->orWhereHas('user', function ($q) use ($keyword) {
                      $q->where('email', 'like', "%{$keyword}%")
                        ->orWhere('username', 'like', "%{$keyword}%");
                  });
        })
        ->orderBy('member_name')
        ->paginate(10)
        ->withQueryString(); // Wajib ditambahkan agar keyword search terbawa ke halaman 2, 3, dst.

        return view(
            'members.index',
            compact('members')
        );
    }

public function show(Members $member)
{
    $member->load([
        'user.role',
        'jabatan',
        'skills',
        'scrumUpdates.task1',
        'scrumUpdates.task2'
    ]);

    // Ambil semua task yang di-assign ke member ini
    $tasks = \App\Models\Task::whereHas('assignees', function($q) use ($member) {
            $q->where('members.id_member', $member->id_member);
        })
        ->with(['project', 'status', 'priority'])
        ->latest()
        ->get();

    // Mengelompokkan task berdasarkan status (case-insensitive / pastikan string sesuai dengan database Anda)
    // Ubah string 'Planning', 'On Going', dll. jika di database Anda namanya berbeda
    $planning = $tasks->filter(fn($task) => strtolower($task->status?->status_name) === 'planning');
    $ongoing  = $tasks->filter(fn($task) => strtolower($task->status?->status_name) === 'on going' || strtolower($task->status?->status_name) === 'ongoing');
    $reviewed = $tasks->filter(fn($task) => strtolower($task->status?->status_name) === 'reviewed');
    $finished = $tasks->filter(fn($task) => strtolower($task->status?->status_name) === 'finished');
    $canceled = $tasks->filter(fn($task) => strtolower($task->status?->status_name) === 'canceled');
    
    // Untuk Overdue, kita filter yang statusnya bukan finished dan tanggal deadline-nya sudah lewat dari hari ini
    $overdue  = $tasks->filter(fn($task) => 
        strtolower($task->status?->status_name) !== 'finished' && 
        $task->deadline_task && 
        \Carbon\Carbon::parse($task->deadline_task)->isPast()
    );

    $monthlyAttendance = $member->attendances()
        ->whereMonth('tanggal', now()->month)
        ->count();

    return view('members.show', compact(
        'member',
        'tasks',
        'planning',
        'ongoing',
        'reviewed',
        'finished',
        'canceled',
        'overdue',
        'monthlyAttendance'
    ));
}

public function edit(
    Members $member
)
{
    $member->load(
        'skills'
    );

    $positions =
        Jabatan::orderBy(
            'position_name'
        )->get();

    $skills =
        Skills::orderBy(
            'skill_name'
        )->get();

    return view(
        'members.edit',
        compact(
            'member',
            'positions',
            'skills'
        )
    );
}

public function update(
    Request $request,
    Members $member
)
{
    $request->validate([

        'member_name' =>
            'required|max:150',

        'id_position' =>
            'required|exists:jabatans,id_position',

        'profile_photo' =>
            'nullable|image|max:2048',

        'skills' =>
            'nullable|array'

    ]);

    if (
        $request->hasFile(
            'profile_photo'
        )
    ) {

        $photoPath =
            $request
            ->file(
                'profile_photo'
            )
            ->store(
                'profile-photos',
                'public'
            );

        $member->profile_photo =
            $photoPath;
    }

    $member->update([

        'member_name' =>
            $request->member_name,

        'id_position' =>
            $request->id_position

    ]);

    $skillIds = [];

    foreach (
        $request->skills ?? []
        as $skillInput
    )
    {

        if (
            is_numeric(
                $skillInput
            )
        )
        {

            $skillIds[] =
                $skillInput;
        }
        else
        {

            $skill =
                Skills::firstOrCreate([

                    'skill_name' =>
                        trim(
                            $skillInput
                        )

                ]);

            $skillIds[] =
                $skill->id_skill;
        }
    }

    $member->skills()
        ->sync(
            $skillIds
        );

    return redirect()
        ->route(
            'members.show',
            $member
        )
        ->with(
            'success',
            'Employee updated successfully.'
        );
}
}
