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
        $member = auth()->user()->member;

        $activeOvertime = RiwayatOvertime::with('task')
            ->where('id_member', $member->id_member)
            ->whereNull('end_overtime')
            ->latest()
            ->first();

        // PERBAIKAN DI SINI: Filter menggunakan id_status != 8 (Finished)
        $tasks = Task::whereHas('assignees', function ($query) use ($member) {
                    $query->where('members.id_member', $member->id_member);
                })
                ->where('id_status', '!=', 8) // <--- Menggunakan ID 8 untuk status 'Finished'
                ->orderBy('nama_task')
                ->get();

        $isWeekend = now()->isWeekend();

        return view('overtimes.index', compact('activeOvertime', 'tasks', 'isWeekend'));
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
        VALIDASI WEEKEND VS WEEKDAY
        =====================================
        */
        $isWeekend =
            now()->isWeekend();

        // Jika HARI KERJA tapi TIDAK ADA ABSENSI, maka tolak.
        if (
            !$isWeekend
            &&
            !$attendance
        ) {
            return back()->with(
                'error',
                'Gagal! Pada hari kerja, Anda harus melakukan absensi terlebih dahulu sebelum mengajukan overtime.'
            );
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

    /*
|--------------------------------------------------------------------------
| ALL HISTORY OVERTIME (SUPERADMIN & PM ONLY)
|--------------------------------------------------------------------------
*/
public function allHistory(Request $request)
{
    $user = auth()->user();

    // 1. PROTEKSI AKSES: Tolak jika yang login adalah Member biasa (id_role == 3)
    if ($user->id_role == 3) {
        abort(403, 'Akses Ditolak: Halaman ini hanya untuk Superadmin dan Project Manager.');
    }

    // 2. DATE NAVIGATION (mengikuti pola halaman Attendance)
    $today        = now()->format('Y-m-d');
    $selectedDate = $request->filled('date') ? $request->date : $today;
    $previousDate = \Carbon\Carbon::parse($selectedDate)->subDay()->format('Y-m-d');
    $nextDate     = \Carbon\Carbon::parse($selectedDate)->addDay()->format('Y-m-d');

    // 3. QUERY UTAMA: Ambil data Riwayat Overtime beserta relasi
    $query = RiwayatOvertime::with([
        'member',
        'task.project',
    ]);

    // 4. FILTER ROLE PM: Jika PM (id_role == 2), hanya tampilkan overtime dari proyeknya
    if ($user->id_role == 2) {
        $query->whereHas('task.project', function ($q) use ($user) {
            $q->where('id_project_manager', $user->member->id_member);
        });
    }

    // 5. FILTER TANGGAL SPESIFIK (dari date navigation / dropdown)
    if ($request->filled('date')) {
        $query->whereDate('tanggal', $request->date);
    }

    // 6. FILTER BULAN
    if ($request->filled('month')) {
        $query->whereMonth('tanggal', $request->month);
    }

    // 7. FILTER TAHUN
    if ($request->filled('year')) {
        $query->whereYear('tanggal', $request->year);
    }

    // 8. FILTER STATUS APPROVAL
    if ($request->filled('status')) {
        $query->where('status_approval', $request->status);
    }

    // Ambil data dan Pagination (withQueryString biar filter kebawa pas ganti halaman)
    $histories = $query->latest('updated_at')
        ->paginate(20)
        ->withQueryString();

    // Daftar tahun yang tersedia di data, untuk isi dropdown Year
    $years = RiwayatOvertime::selectRaw('YEAR(tanggal) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year');

    // Daftar bulan untuk isi dropdown Month
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    return view('overtimes.all-history', compact(
        'histories', 'years', 'months', 'selectedDate', 'previousDate', 'nextDate', 'today'
    ));
}
}