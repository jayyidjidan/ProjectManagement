<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\StatusProyeks;
use Illuminate\Http\Request;

class StatusProyekController extends Controller
{
    public function index()
    {
        $status_proyeks = StatusProyeks::latest()->paginate(10);

        return view('master-data.status-proyeks.index', compact('status_proyeks'));
    }

    public function create()
    {
        return view('master-data.status-proyeks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_status' => 'required|max:50'
        ]);

        StatusProyeks::create([
            'nama_status' => $request->nama_status
        ]);

        return redirect()
            ->route('status-proyeks.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(StatusProyeks $status_proyek)
    {
        return view('master-data.status-proyeks.show', compact('status_proyek'));
    }

    public function edit(StatusProyeks $status_proyek)
    {
        return view('master-data.status-proyeks.edit', compact('status_proyek'));
    }

    public function update(Request $request, StatusProyeks $status_proyek)
    {
        $request->validate([
            'nama_status' => 'required|max:50'
        ]);

        $status_proyek->update([
            'nama_status' => $request->nama_status
        ]);

        return redirect()
            ->route('status-proyeks.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(StatusProyeks $status_proyek)
    {
        $status_proyek->delete();

        return redirect()
            ->route('status-proyeks.index')
            ->with('success', 'Data berhasil dihapus');
    }
}