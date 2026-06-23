<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Tipes;
use Illuminate\Http\Request;

class TipeController extends Controller
{
    public function index()
    {
        $tipes = Tipes::latest()->paginate(10);

        return view('master-data.tipes.index', compact('tipes'));
    }

    public function create()
    {
        return view('master-data.tipes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tipe' => 'required|max:50'
        ]);

        Tipes::create([
            'nama_tipe' => $request->nama_tipe
        ]);

        return redirect()
            ->route('tipes.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(Tipes $tipe)
    {
        return view('master-data.tipes.show', compact('tipe'));
    }

    public function edit(Tipes $tipe)
    {
        return view('master-data.tipes.edit', compact('tipe'));
    }

    public function update(Request $request, Tipes $tipe)
    {
        $request->validate([
            'nama_tipe' => 'required|max:50'
        ]);

        $tipe->update([
            'nama_tipe' => $request->nama_tipe
        ]);

        return redirect()
            ->route('tipes.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(Tipes $tipe)
    {
        $tipe->delete();

        return redirect()
            ->route('tipes.index')
            ->with('success', 'Data berhasil dihapus');
    }
}