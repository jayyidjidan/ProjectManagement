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
    $member = auth()->user()->member;

    $attendance = Attendance::where('id_member', $member->id_member)
        ->whereDate('tanggal', today())
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
    if (in_array($attendance->id_status, [3, 6, 7])) {
        return back()->with(
            'error',
            'You cannot check in today.'
        );
    }

    if ($attendance->start_hour) {
        return back()->with(
            'error',
            'Already checked in.'
        );
    }

    /*
    ===================================
    VALIDASI BATAS WAKTU CHECK IN
    ===================================
    */
    $workSetting = WorkSetting::first();
    $currentTime = now()->format('H:i:s');

    // JIKA TERLALU CEPAT: Belum waktunya absen masuk
    if ($currentTime < $workSetting->checkin_start) {
        return back()->with(
            'error',
            'Belum waktunya Check In. Absensi dibuka mulai jam ' . $workSetting->checkin_start
        );
    }

    // JIKA TERLAMBAT: Sudah lewat batas toleransi absen masuk
    if ($currentTime > $workSetting->checkin_end) {
        return back()->with(
            'error',
            'Batas waktu Check In sudah habis (' . $workSetting->checkin_end . '). Anda terlambat, silakan lapor Admin.'
        );
    }

    /*
    ===================================
    NORMAL CHECK IN
    ===================================
    */
    $attendance->update([
        'start_hour' => $currentTime
    ]);

    return back()->with(
        'success',
        'Check In Success.'
    );
}

public function checkOut(Request $request)
{
    $member = auth()->user()->member;

    $attendance = Attendance::where('id_member', $member->id_member)
        ->whereDate('tanggal', today())
        ->first();

    if (!$attendance) {
        return back()->with('error', 'Attendance not found.');
    }

    if (!$attendance->start_hour) {
        return back()->with('error', 'Please check in first.');
    }

    if ($attendance->leave_hour) {
        return back()->with('error', 'Already checked out.');
    }

    $workSetting = WorkSetting::first();
    $currentTime = now()->format('H:i:s');

    /*
    ===================================
    VALIDASI BELUM WAKTUNYA PULANG
    ===================================
    */
    if ($currentTime < $workSetting->checkout_start) {
        return back()->with(
            'error',
            'Belum waktunya jam pulang. Checkout baru bisa dilakukan mulai jam ' . $workSetting->checkout_start
        );
    }

    /*
    ===================================
    OVERTIME POPUP
    ===================================
    */
    if (
        !$request->has('force_checkout')
        &&
        $currentTime > $workSetting->checkout_end
    ) {
        session([
            'attendance_id' => $attendance->id_attendance
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
    $start = \Carbon\Carbon::parse($attendance->start_hour);
    $end = \Carbon\Carbon::parse($currentTime);
    $workHours = $start->diff($end)->format('%H:%I:%S');

    $attendance->update([
        'leave_hour' => $currentTime,
        'work_hours' => $workHours
    ]);

    return back()->with(
        'success',
        'Check Out Success.'
    );
}
}