<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Members;
use App\Models\Attendance;
use App\Models\RiwayatOvertime;

use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | OVERTIME PAGE
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $member =
            auth()->user()->member;

        $activeOvertime =
            RiwayatOvertime::with('task')
            ->where(
                'id_member',
                $member->id_member
            )
            ->whereNull(
                'end_overtime'
            )
            ->latest()
            ->first();

        $tasks =
            Task::whereHas(
                'assignees',
                function ($query) use ($member) {

                    $query->where(
                        'members.id_member',
                        $member->id_member
                    );

                }
            )
            ->orderBy(
                'nama_task'
            )
            ->get();

        $isWeekend =
            now()->isWeekend();

        return view(
            'overtimes.index',
            compact(
                'activeOvertime',
                'tasks',
                'isWeekend'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | START OVERTIME
    |--------------------------------------------------------------------------
    */
    public function start(
        Request $request
    )
    {
        $member =
            auth()->user()->member;

        $request->validate([

            'id_task' =>
                'required|exists:tasks,id_task',

            'reason' =>
                'required|string|max:1000'

        ]);

        $activeOvertime =
            RiwayatOvertime::where(
                'id_member',
                $member->id_member
            )
            ->whereNull(
                'end_overtime'
            )
            ->exists();

        if ($activeOvertime) {

            return back()->with(
                'error',
                'You still have active overtime.'
            );
        }

        /*
        =====================================
        AMBIL ATTENDANCE DARI SESSION
        =====================================
        */

        $attendance = null;

        if(
            session()->has(
                'attendance_id'
            )
        ){

            $attendance =
                Attendance::find(
                    session(
                        'attendance_id'
                    )
                );

        }
        else{

            $attendance =
                Attendance::where(
                    'id_member',
                    $member->id_member
                )
                ->whereDate(
                    'tanggal',
                    today()
                )
                ->first();

        }

        /*
        =====================================
        TUTUP ATTENDANCE NORMAL
        =====================================
        */

        if(
            $attendance
            &&
            !$attendance->leave_hour
        ){

            $attendance->update([

                'leave_hour' =>
                    now()->format('H:i:s')

            ]);

        }

        /*
        =====================================
        CREATE OVERTIME
        =====================================
        */

        RiwayatOvertime::create([

            'id_member' =>
                $member->id_member,

            'id_attendance' =>
                optional($attendance)
                    ->id_attendance,

            'id_task' =>
                $request->id_task,

            'reason' =>
                $request->reason,

            'start_overtime' =>
                now(),

            'tanggal' =>
                today(),

            'durasi_jam' =>
                0,

            'status_approval' =>
                'pending'

        ]);

        session()->forget(
            'attendance_id'
        );

        return back()->with(
            'success',
            'Overtime started.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STOP OVERTIME
    |--------------------------------------------------------------------------
    */
    public function stop()
    {
        $member =
            auth()->user()->member;

        $overtime =
            RiwayatOvertime::where(
                'id_member',
                $member->id_member
            )
            ->whereNull(
                'end_overtime'
            )
            ->latest()
            ->first();

        if (!$overtime) {

            return back()->with(
                'error',
                'No active overtime.'
            );
        }

        $start =
            strtotime(
                $overtime->start_overtime
            );

        $end =
            time();

        $hours =
            round(
                ($end - $start)
                / 3600,
                2
            );

        $overtime->update([

            'end_overtime' =>
                now(),

            'durasi_jam' =>
                $hours

        ]);

        return back()->with(
            'success',
            'Overtime finished.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MY HISTORY
    |--------------------------------------------------------------------------
    */
    public function history()
    {
        $member =
            auth()->user()->member;

        $histories =
            RiwayatOvertime::with([
                'task'
            ])
            ->where(
                'id_member',
                $member->id_member
            )
            ->latest()
            ->paginate(15);

        return view(
            'overtimes.history',
            compact(
                'histories'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PM APPROVAL PAGE
    |--------------------------------------------------------------------------
    */
    public function approvals()
    {
        $overtimes =
            RiwayatOvertime::with([
                'member',
                'task'
            ])
            ->where(
                'status_approval',
                'pending'
            )
            ->latest()
            ->paginate(20);

        return view(
            'overtimes.approvals',
            compact(
                'overtimes'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */
    public function approve(
        $id
    )
    {
        $overtime =
            RiwayatOvertime::findOrFail(
                $id
            );

        $member =
            auth()->user()->member;

        $overtime->update([

            'status_approval' =>
                'approved',

            'id_approved_by' =>
                $member->id_member,

            'approved_at' =>
                now()

        ]);

        Members::where(
            'id_member',
            $overtime->id_member
        )->increment(
            'total_overtime',
            $overtime->durasi_jam
        );

        return back()->with(
            'success',
            'Overtime approved.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */
    public function reject(
        $id
    )
    {
        $overtime =
            RiwayatOvertime::findOrFail(
                $id
            );

        $member =
            auth()->user()->member;

        $overtime->update([

            'status_approval' =>
                'rejected',

            'id_approved_by' =>
                $member->id_member,

            'approved_at' =>
                now()

        ]);

        return back()->with(
            'success',
            'Overtime rejected.'
        );
    }
}