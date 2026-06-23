<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyeks;
use App\Models\Kategoris; 
use App\Models\StatusProyeks;
use App\Models\Tipes;
use App\Models\Members;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportProjectController extends Controller
{
    // Menampilkan halaman form filter saja
    public function index()
    {
        // Ambil data untuk opsi dropdown filter
        $filterCategories = Kategoris::all();
        $filterTypes = Tipes::all();
        $filterStatuses = StatusProyeks::all();
        $filterManagers = Members::orderBy('member_name')->get(); 

        return view('reports.projects.index', compact(
            'filterCategories', 'filterTypes', 'filterStatuses', 'filterManagers'
        ));
    }

    // Memproses filter dan generate PDF
    public function download(Request $request)
    {
        $user = auth()->user();

        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $keyword = request('q'); 

        $projectQuery = Proyeks::query();

        // Jika Project Manager hanya tampilkan project miliknya
        if ($user->id_role == 2) {
            $projectQuery->where('id_project_manager', $user->member->id_member);
        }

        // Logic filter yang sama persis dengan ProjectController lu
        $projects = $projectQuery
            ->with(['status', 'klien', 'tipe', 'projectManager', 'kategoris'])
            ->when(request('status'), function ($query) {
                $query->where('id_status', request('status'));
            })
            ->when(request('type'), function ($query) {
                $query->where('id_tipe', request('type'));
            })
            ->when(request('manager'), function ($query) {
                $query->where('id_project_manager', request('manager'));
            })
            ->when(request('categories'), function ($query) {
                $query->whereHas('kategoris', function ($q) {
                    $q->whereIn('kategoris.id_kategori', request('categories'));
                });
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where('nama_proyek', 'like', "%{$keyword}%");
            })
            ->orderBy($sort, $direction)
            ->get(); // PENTING: Gunakan get(), bukan paginate() agar semua data masuk PDF

        // Load view PDF dan set ukuran kertas ke Landscape agar tabel tidak terpotong
        $pdf = Pdf::loadView('reports.projects.pdf', compact('projects'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Project_' . date('Y-m-d') . '.pdf');
    }
}