<?php

namespace App\Http\Controllers;

use App\Models\Proyeks;
use App\Models\Task;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        // ====================================================================
        // LOGIKA KHUSUS MEMBER (id_role = 3)
        // ====================================================================
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

            // 4. Total Work Hour (Menggunakan kolom work_hours dari tabel attendances)
            $totalWorkHours = Attendance::where('id_member', $memberId)->sum('work_hours') ?? 0;

            // 5. Work Hour Graphic (Data absensi jam kerja 7 hari terakhir)
            $graphicData = Attendance::where('id_member', $memberId)
                ->where('tanggal', '>=', Carbon::today()->subDays(7)->toDateString())
                ->orderBy('tanggal', 'asc')
                ->get(['tanggal', 'work_hours']);

            $chartLabels = $graphicData->pluck('tanggal')->map(fn($date) => Carbon::parse($date)->format('d M'))->toArray();
            $chartDataMember = $graphicData->pluck('work_hours')->toArray();

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