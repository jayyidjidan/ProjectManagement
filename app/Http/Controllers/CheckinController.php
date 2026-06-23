<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\WorkSetting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckInController extends Controller
{
    public function index()
    {
        $member =
            auth()->user()->member;

        $attendance =
            Attendance::with(
                'status'
            )
            ->where(
                'id_member',
                $member->id_member
            )
            ->whereDate(
                'tanggal',
                today()
            )
            ->first();

        $workSetting =
            WorkSetting::first();

        return view(
            'checkin.index',
            compact(
                'attendance',
                'workSetting'
            )
        );
    }

    public function checkIn()
    {
        $member =
            auth()->user()->member;

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

        if (!$attendance) {

            return back()->with(
                'error',
                'Attendance today not found. Please wait until Scrum is finalized.'
            );
        }

        /*
        Izin
        Sakit
        Cuti
        */
        if (
            in_array(
                $attendance->id_status,
                [3, 6, 7]
            )
        ) {

            return back()->with(
                'error',
                'You cannot check in today.'
            );
        }

        if (
            $attendance->start_hour
        ) {

            return back()->with(
                'error',
                'Already checked in.'
            );
        }

        $attendance->update([

            'start_hour' =>
                now()->format('H:i:s')

        ]);

        return back()->with(
            'success',
            'Check In Success.'
        );
    }

public function checkOut(Request $request)
{
    $member =
        auth()->user()->member;

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

    if (!$attendance) {

        return back()->with(
            'error',
            'Attendance not found.'
        );
    }

    if (
        !$attendance->start_hour
    ) {

        return back()->with(
            'error',
            'Please check in first.'
        );
    }

    if (
        $attendance->leave_hour
    ) {

        return back()->with(
            'error',
            'Already checked out.'
        );
    }

    $workSetting =
        WorkSetting::first();

    /*
    ===================================
    OVERTIME POPUP
    ===================================
    */

    if (
        !$request->has(
            'force_checkout'
        )
        &&
        now()->format('H:i:s')
        >
        $workSetting->checkout_end
    ) {

        session([
            'attendance_id' =>
                $attendance->id_attendance
        ]);

        return back()->with(
            'show_overtime_prompt',
            true
        );
    }

    /*
    ===================================
    NORMAL CHECKOUT
    ===================================
    */

    $attendance->update([

        'leave_hour' =>
            now()->format('H:i:s')

    ]);

    return back()->with(
        'success',
        'Check Out Success.'
    );
}
}