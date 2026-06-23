<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyeks;
use App\Models\Kliens;
use App\Models\Kategoris; 
use App\Models\Pembayaran;
use App\Models\StatusProyeks;
use App\Models\Tipes;
use App\Models\Members;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        
        // 1. Tangkap keyword pencarian dari header global
        $keyword = request('q'); 

        $projectQuery = Proyeks::query();

        // Jika Project Manager hanya tampilkan project miliknya
        if ($user->id_role == 2) {
            $projectQuery->where(
                'id_project_manager',
                $user->member->id_member
            );
        }

        $total = (clone $projectQuery)->count();

        $planning = (clone $projectQuery)
            ->whereHas(
                'status',
                fn ($q) =>
                    $q->where(
                        'nama_status',
                        'Planning'
                    )
            )
            ->count();

        $ongoing = (clone $projectQuery)
            ->whereHas(
                'status',
                fn ($q) =>
                    $q->where(
                        'nama_status',
                        'On Going'
                    )
            )
            ->count();

        $finished = (clone $projectQuery)
            ->whereHas(
                'status',
                fn ($q) =>
                    $q->where(
                        'nama_status',
                        'Finished'
                    )
            )
            ->count();

        $cancelled = (clone $projectQuery)
            ->whereHas(
                'status',
                fn ($q) =>
                    $q->where(
                        'nama_status',
                        'Cancelled'
                    )
            )
            ->count();

        $projects = $projectQuery
            ->with([
                'status',
                'klien',
                'tipe',
                'projectManager',
                'kategoris'
            ])
            ->when(
                request('status'),
                function ($query) {
                    $query->where(
                        'id_status',
                        request('status')
                    );
                }
            )
            // 2. TAMBAHKAN LOGIKA SEARCH DI SINI
            ->when($keyword, function ($query, $keyword) {
                // Sesuaikan 'nama_proyek' dengan nama kolom asli di tokomu (misal: 'nama_project' atau 'title')
                return $query->where('nama_proyek', 'like', "%{$keyword}%");
                
                // Jika ingin mencari berdasarkan nama klien juga, kamu bisa gunakan ini:
                // return $query->where('nama_proyek', 'like', "%{$keyword}%")
                //              ->orWhereHas('klien', fn($q) => $q->where('nama_klien', 'like', "%{$keyword}%"));
            })
            ->orderBy(
                $sort,
                $direction
            )
            ->paginate(10)
            ->withQueryString(); // Ini penting agar keyword 'q' tidak hilang saat ganti halaman pagination

        return view(
            'projects.index',
            compact(
                'projects',
                'total',
                'planning',
                'ongoing',
                'finished',
                'cancelled'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = StatusProyeks::all();
        $types = Tipes::all();
        $categories = Kategoris::all();
        $clients = Kliens::all();

        return view(
            'projects.create',
            compact(
                'statuses',
                'types',
                'categories',
                'clients'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_proyek' => 'required|max:200',
            'deadline' => 'nullable|date',
            'id_status' => 'required',
            'id_tipe' => 'nullable',
            'nama_klien' => 'required|max:100',
            'total_pembayaran' => 'required|numeric|min:0',
            'categories' => 'required|array'
        ]);

        $client = Kliens::where(
            'nama_klien',
            $request->nama_klien
        )->first();

        if (!$client) {

            $client = Kliens::create([
                'nama_klien' => $request->nama_klien
            ]);
        }

        $payment = Pembayaran::create([
            'total_pembayaran' => $request->total_pembayaran,
            'total_dibayarkan' => 0,
            'sisa_pembayaran' => $request->total_pembayaran,
            'jumlah_pembayaran' => 1
        ]);

        $member = auth()->user()->member;

        $project = Proyeks::create([
            'nama_proyek' => $request->nama_proyek,
            'deadline' => $request->deadline,
            'id_status' => $request->id_status,
            'id_klien' => $client->id_klien,
            'id_pembayaran' => $payment->id_pembayaran,
            'id_tipe' => $request->id_tipe,
            'id_project_manager' => $member?->id_member
        ]);

        $project->kategoris()->sync(
            $request->categories
        );

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proyeks $project)
    {
        $project->load([
        'status',
        'klien',
        'tipe',
        'pembayaran.transaksis.jenis',
        'projectManager',
        'kategoris',

        'tasks.priority',
        'tasks.status',
        'tasks.assignees'
    ]);

    return view(
        'projects.show',
        compact('project')
    );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proyeks $project)
    {
        $statuses = StatusProyeks::all();
        $types = Tipes::all();
        $categories = Kategoris::all();
        $clients = Kliens::all();

        return view(
            'projects.edit',
            compact(
                'project',
                'statuses',
                'types',
                'categories',
                'clients'
            )
        );
    }
/**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proyeks $project)
    {
        $request->validate([
            'nama_proyek' => 'required|max:200',
            'deadline' => 'nullable|date',
            'id_status' => 'required',
            'id_tipe' => 'nullable',
            'nama_klien' => 'required|max:100',
            'total_pembayaran' => 'required|numeric|min:0',
            'categories' => 'required|array'
        ]);

        // VALIDASI STATUS FINISHED
        // Berdasarkan database, status_proyeks "Finished" adalah ID 5
        if ($request->id_status == 5) {
            
            // Cek apakah ada Task yang statusnya BUKAN "FInished" (ID 8 di status_tasks)
            // Catatan: Jika task "Canceled" (ID 6) juga tidak menghalangi project selesai, 
            // ganti menjadi: whereNotIn('id_status', [6, 8])
            $hasUnfinishedTasks = $project->tasks()
                ->where('id_status', '!=', 8)
                ->exists();

            if ($hasUnfinishedTasks) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Tidak dapat mengubah status project ke Finished karena masih ada Task yang belum selesai.');
            }
        }

        $client = Kliens::where(
            'nama_klien',
            $request->nama_klien
        )->first();

        if (!$client) {
            $client = Kliens::create([
                'nama_klien' => $request->nama_klien
            ]);
        }

        $project->update([
            'nama_proyek' => $request->nama_proyek,
            'deadline' => $request->deadline,
            'id_status' => $request->id_status,
            'id_klien' => $client->id_klien,
            'id_tipe' => $request->id_tipe,
        ]);

        $project->kategoris()->sync(
            $request->categories
        );

        if ($project->pembayaran) {
            $project->pembayaran->update([
                'total_pembayaran' => $request->total_pembayaran,
                'sisa_pembayaran' =>
                    $request->total_pembayaran -
                    $project->pembayaran->total_dibayarkan
            ]);
        }

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proyeks $project)
    {
        if ($project->id_pembayaran) {

            Pembayaran::where(
                'id_pembayaran',
                $project->id_pembayaran
            )->delete();
        }

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project berhasil dihapus');
    }
}