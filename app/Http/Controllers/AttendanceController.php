<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Jabatan;
use App\Models\StatusMembers;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->date ?? now()->toDateString();

        // 1. Ubah query Attendance menjadi instance builder agar bisa difilter
        $query = Attendance::with(['member.jabatan', 'status'])
            ->whereDate('tanggal', $selectedDate);

        // 2. Tambahkan logika filter Position
        if ($request->filled('position')) {
            $query->whereHas('member', function ($q) use ($request) {
                $q->where('id_position', $request->position);
            });
        }

        // 3. Tambahkan logika filter Status
        if ($request->filled('status')) {
            $query->where('id_status', $request->status);
        }

        // 4. Eksekusi query
        $attendances = $query->orderBy('id_attendance', 'asc')->get();

        $currentDate = Carbon::parse($selectedDate);
        $previousDate = $currentDate->copy()->subDay()->toDateString();
        $nextDate = $currentDate->copy()->addDay()->toDateString();
        $today = now()->toDateString();

        $hadir = $attendances->filter(fn($a) => $a->status?->status_name === 'Hadir')->count();
        $wfh = $attendances->filter(fn($a) => $a->status?->status_name === 'WFH')->count();
        $cuti = $attendances->filter(fn($a) => $a->status?->status_name === 'Cuti')->count();
        $tanpaKeterangan = $attendances->filter(fn($a) => $a->status?->status_name === 'Tanpa Keterangan')->count();

        // 5. Ambil data Position dan Status untuk dikirim ke Dropdown Filter
        $positions = Jabatan::all();
        $statuses = StatusMembers::all();

        return view('attendances.index', compact(
            'attendances',
            'selectedDate',
            'previousDate',
            'nextDate',
            'today',
            'hadir',
            'wfh',
            'cuti',
            'tanpaKeterangan',
            'positions', // Kirim ke view
            'statuses'   // Kirim ke view
        ));
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