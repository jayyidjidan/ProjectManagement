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

        // JIKA YANG LOGIN ADALAH MEMBER (id_role = 3), DIALIHKAN KE HALAMAN PROFILE
        if ($user && $user->id_role == 3) {
            return redirect()->route('profile.show');
        }

        // --- PROSES UNTUK SUPERADMIN & PROJECT MANAGER ---

        // 1. Total Proyek (Menggantikan Revenue karena tidak ada kolom budget di tabel proyeks)
        $totalProjects = Proyeks::count('id_proyek') ?? 0;

        // 2. Total Klien Unik dari tabel proyeks
        $totalClients = Proyeks::distinct('id_klien')->count('id_klien') ?? 0;

        // 3. Attendance Hari Ini (menggunakan kolom 'tanggal')
        $today = Carbon::today()->toDateString();
        
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

        // 5. Task yang mendekati deadline (5 hari)
        $upcomingDeadlines = Task::with(['project', 'status'])
            ->where('deadline_task', '>=', $today)
            ->where('deadline_task', '<=', Carbon::today()->addDays(5)->toDateString())
            // Mengecualikan yang sudah selesai (Asumsi di tabel status_tasks namanya 'Finished' atau 'Selesai')
            // Jika memunculkan error, baris whereHas ini bisa dihapus
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