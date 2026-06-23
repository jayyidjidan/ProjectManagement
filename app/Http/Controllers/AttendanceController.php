<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(
        Request $request
    )
    {
        $selectedDate =
            $request->date
            ??
            now()->toDateString();

        $attendances =
            Attendance::with([
                'member.jabatan',
                'status'
            ])
            ->whereDate(
                'tanggal',
                $selectedDate
            )
            ->orderBy(
                'id_attendance',
                'asc'
            )
            ->get();

        $currentDate =
            Carbon::parse(
                $selectedDate
            );

        $previousDate =
            $currentDate
            ->copy()
            ->subDay()
            ->toDateString();

        $nextDate =
            $currentDate
            ->copy()
            ->addDay()
            ->toDateString();

        $today =
            now()->toDateString();

        $hadir =
            $attendances
            ->filter(
                fn($a) =>
                $a->status?->status_name
                === 'Hadir'
            )
            ->count();

        $wfh =
            $attendances
            ->filter(
                fn($a) =>
                $a->status?->status_name
                === 'WFH'
            )
            ->count();

        $cuti =
            $attendances
            ->filter(
                fn($a) =>
                $a->status?->status_name
                === 'Cuti'
            )
            ->count();

        $tanpaKeterangan =
            $attendances
            ->filter(
                fn($a) =>
                $a->status?->status_name
                === 'Tanpa Keterangan'
            )
            ->count();

        return view(
            'attendances.index',
            compact(
                'attendances',
                'selectedDate',
                'previousDate',
                'nextDate',
                'today',
                'hadir',
                'wfh',
                'cuti',
                'tanpaKeterangan'
            )
        );
    }

    public function myAttendance(
        Request $request
    )
    {
        $member =
            auth()->user()->member;

        $query =
            Attendance::with([
                'status'
            ])
            ->where(
                'id_member',
                $member->id_member
            );

        if(
            $request->filled(
                'tanggal'
            )
        ){

            $query->whereDate(
                'tanggal',
                $request->tanggal
            );

        }

        $attendances =
            $query
            ->latest('tanggal')
            ->paginate(15);

        return view(
            'attendances.my-index',
            compact(
                'attendances'
            )
        );
    }
}