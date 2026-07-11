<?php

namespace App\Http\Controllers;

use App\Models\Proyeks;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\RiwayatOvertime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        
        // LOGIKA KHUSUS MEMBER (id_role = 3)
        
        if ($user && $user->id_role == 3) {
            if (!$user->member) {
                abort(403, 'Profil member Anda belum terdaftar di sistem.');
            }

            $memberId = $user->member->id_member;

            // 1. Total Task milik Member
            $totalTasks = Task::whereHas('assignees', function($q) use ($memberId) {
                $q->where('members.id_member', $memberId);
            })->count();

            // 2. Overdue Task (Deadline terlewat & status belum selesai/finished)
            $overdueTasks = Task::whereHas('assignees', function($q) use ($memberId) {
                $q->where('members.id_member', $memberId);
            })
            ->where('deadline_task', '<', $today)
            ->whereHas('status', function($query) {
                $query->where('status_name', '!=', 'Finished')->where('status_name', '!=', 'Selesai');
            })
            ->count();

            // 3. Upcoming Deadline Task (Deadline dalam 7 hari ke depan & belum selesai)
            $upcomingTasks = Task::whereHas('assignees', function($q) use ($memberId) {
                $q->where('members.id_member', $memberId);
            })
            ->where('deadline_task', '>=', $today)
            ->where('deadline_task', '<=', Carbon::today()->addDays(7)->toDateString())
            ->whereHas('status', function($query) {
                $query->where('status_name', '!=', 'Finished')->where('status_name', '!=', 'Selesai');
            })
            ->count();

            // 4. Total Work Hours
            //    = jam absensi (selisih leave_hour - start_hour) + jam overtime yang sudah approved
            $attendanceHours = Attendance::where('id_member', $memberId)
                ->whereNotNull('start_hour')
                ->whereNotNull('leave_hour')
                ->selectRaw('SUM(TIME_TO_SEC(TIMEDIFF(leave_hour, start_hour))) / 3600 as total_hours')
                ->value('total_hours') ?? 0;

            $overtimeHours = RiwayatOvertime::where('id_member', $memberId)
                ->where('status_approval', 'approved')
                ->sum('durasi_jam') ?? 0;

            $totalWorkHours = round($attendanceHours + $overtimeHours, 2);

            // 5. Work Hour Graphic (Data jam kerja 7 hari terakhir: absensi + overtime approved)
            $startDate = Carbon::today()->subDays(6);
            $endDate   = Carbon::today();

            $attendanceDaily = Attendance::where('id_member', $memberId)
                ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
                ->whereNotNull('start_hour')
                ->whereNotNull('leave_hour')
                ->selectRaw('tanggal, SUM(TIME_TO_SEC(TIMEDIFF(leave_hour, start_hour))) / 3600 as hours')
                ->groupBy('tanggal')
                ->pluck('hours', 'tanggal');

            $overtimeDaily = RiwayatOvertime::where('id_member', $memberId)
                ->where('status_approval', 'approved')
                ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
                ->selectRaw('tanggal, SUM(durasi_jam) as hours')
                ->groupBy('tanggal')
                ->pluck('hours', 'tanggal');

            $chartLabels = [];
            $chartDataMember = [];

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dateKey = $date->toDateString();
                $chartLabels[] = $date->format('d M');

                $hours = (float) ($attendanceDaily[$dateKey] ?? 0) + (float) ($overtimeDaily[$dateKey] ?? 0);
                $chartDataMember[] = round($hours, 2);
            }

            // Return ke view khusus dashboard member
            return view('dashboard.member', compact(
                'totalTasks',
                'overdueTasks',
                'upcomingTasks',
                'totalWorkHours',
                'chartLabels',
                'chartDataMember'
            ));
        }

        // ====================================================================
        // LOGIKA UNTUK SUPERADMIN & PROJECT MANAGER (id_role = 1, 2)
        // ====================================================================
        
        // 1. Total Proyek
        $totalProjects = Proyeks::count('id_proyek') ?? 0;

        // 2. Total Klien Unik
        $totalClients = Proyeks::distinct('id_klien')->count('id_klien') ?? 0;

        // 3. Attendance Hari Ini
        $attendanceToday = Attendance::with(['member', 'status'])
            ->whereDate('tanggal', $today)
            ->get();

        // 4. Grafik Pertumbuhan Project
        $projectsMonthly = Proyeks::select(
                DB::raw('count(id_proyek) as total'),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"),
                DB::raw("MAX(created_at) as sort_date")
            )
            ->groupBy('month')
            ->orderBy('sort_date', 'asc')
            ->take(12)
            ->get();

        $chartLabels = $projectsMonthly->pluck('month')->toArray();
        $chartData = $projectsMonthly->pluck('total')->toArray();

        // 5. Task mendakati deadline (5 hari)
        $upcomingDeadlines = Task::with(['project', 'status'])
            ->where('deadline_task', '>=', $today)
            ->where('deadline_task', '<=', Carbon::today()->addDays(5)->toDateString())
            ->whereHas('status', function($query) {
                $query->where('status_name', '!=', 'Finished')->where('status_name', '!=', 'Selesai');
            })
            ->orderBy('deadline_task', 'asc')
            ->get();

        return view('dashboard.admin', compact(
            'totalProjects',
            'totalClients',
            'attendanceToday',
            'chartLabels',
            'chartData',
            'upcomingDeadlines'
        ));
    }
}