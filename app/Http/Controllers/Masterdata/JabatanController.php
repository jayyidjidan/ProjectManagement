<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatans = Jabatan::latest()->paginate(10);

        return view('master-data.jabatans.index', compact('jabatans'));
    }

    public function create()
    {
        return view('master-data.jabatans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|max:100'
        ]);

        Jabatan::create([
            'position_name' => $request->position_name
        ]);

        return redirect()
            ->route('jabatans.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(Jabatan $jabatan)
    {
        return view('master-data.jabatans.show', compact('jabatan'));
    }

    public function edit(Jabatan $jabatan)
    {
        return view('master-data.jabatans.edit', compact('jabatan'));
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $request->validate([
            'position_name' => 'required|max:100'
        ]);

        $jabatan->update([
            'position_name' => $request->position_name
        ]);

        return redirect()
            ->route('jabatans.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(Jabatan $jabatan)
    {
        $jabatan->delete();

        return redirect()
            ->route('jabatans.index')
            ->with('success', 'Data berhasil dihapus');
    }
}