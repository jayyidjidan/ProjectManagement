<?php

namespace App\Http\Controllers;

use App\Models\Scrum;
use App\Models\StatusMembers;
use App\Models\Members;
use Illuminate\Http\Request;
use App\Models\Task;
use Carbon\Carbon;

class ScrumController extends Controller
{
    public function index()
    {
        // 1. Tangkap keyword pencarian global
        $keyword = request('q');

        $scrums = Scrum::with('responsible')
            // 2. TAMBAHKAN LOGIKA SEARCH DI SINI
            ->when($keyword, function ($query, $keyword) {
                // Mencari berdasarkan kolom 'day' (hari) di tabel scrums
                return $query->where('day', 'like', "%{$keyword}%")
                             // ATAU mencari berdasarkan nama di tabel members via relasi 'responsible'
                             ->orWhereHas('responsible', function ($q) use ($keyword) {
                                 $q->where('member_name', 'like', "%{$keyword}%");
                             });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Memastikan keyword search tidak hilang saat pindah halaman pagination

        return view('scrums.index', compact('scrums'));
    }

    public function create()
    {
        $members = Members::orderBy('member_name')->get();

        return view('scrums.create', compact('members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'day' => 'required',
            'date_scrum' => 'required|date',
            'id_responsible' => 'nullable',
            'scrum_password' => 'nullable'
        ]);

        // CEK: Apakah scrum untuk tanggal ini sudah ada?
        $existingScrum = Scrum::whereDate('date_scrum', $request->date_scrum)->first();

        if ($existingScrum) {
            // Jika sudah ada, langsung arahkan ke scrum yang sudah ada
            return redirect()
                ->route('scrums.show', $existingScrum)
                ->with('error', 'Scrum untuk tanggal ini sudah dibuat sebelumnya.');
        }

        // Jika belum ada, buat baru
        $scrum = Scrum::create([
            'day' => $request->day,
            'date_scrum' => $request->date_scrum,
            'id_responsible' => $request->id_responsible,
            'scrum_password' => $request->scrum_password
        ]);

        return redirect()
            ->route('scrums.show', $scrum)
            ->with('success', 'Scrum created successfully');
    }

    public function show(Scrum $scrum)
    {
        $scrum->load([
            'responsible',
            'members',
            'updates.member',
            'updates.task1',
            'updates.task2'
        ]);

        $members = Members::orderBy('member_name')->get();
        $tasks = Task::with('assignees')->orderBy('nama_task')->get();
        $statuses = StatusMembers::orderBy('status_name')->get();

        return view(
            'scrums.show',
            compact('scrum', 'members', 'tasks', 'statuses')
        );
    }

    public function destroy(Scrum $scrum)
    {
        $scrum->delete();

        return redirect()
            ->route('scrums.index')
            ->with('success', 'Scrum deleted successfully');
    }

    public function today()
    {
        $today = now()->toDateString();

        $scrum = Scrum::firstOrCreate(
            ['date_scrum' => $today],
            ['day' => now()->translatedFormat('l')]
        );

        $members = Members::all();
        $tasks = Task::all();
        $statuses = StatusMembers::all();

        return view(
            'scrums.show',
            compact('scrum', 'members', 'tasks', 'statuses')
        );
    }

    public function startFromLogin(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'id_responsible' => 'required'
        ]);

        if ($request->password !== env('SCRUM_PASSWORD')) {
            return back()->with('scrum_error', 'Password Scrum Salah');
        }

        $today = now()->toDateString();

        // CEK: Cari apakah scrum untuk hari ini sudah ada
        $scrum = Scrum::whereDate('date_scrum', $today)->first();

        // Jika belum ada, maka buat scrum baru
        if (!$scrum) {
            $scrum = Scrum::create([
                'day'            => now()->translatedFormat('l'),
                'date_scrum'     => now(),
                'id_responsible' => $request->id_responsible,
                'is_locked'      => false
            ]);
        } 
        // Jika sudah ada, abaikan id_responsible yang baru dan gunakan scrum yang sudah ada
        
        return redirect()->route('scrum.guest.show', $scrum);
    }

    public function showGuest(Scrum $scrum)
    {
        $members = Members::all();
        $statuses = StatusMembers::all();
        
        $tasks = Task::with('assignees')
            ->orderBy('nama_task')
            ->get();

        return view(
            'scrums.show_guest',
            compact('scrum', 'members', 'statuses', 'tasks')
        );
    }
}