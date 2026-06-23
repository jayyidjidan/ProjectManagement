<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\SumberKlien;
use Illuminate\Http\Request;

class SumberKlienController extends Controller
{
    public function index()
    {
        $sumber_kliens = SumberKlien::latest()->paginate(10);

        return view('master-data.sumber-kliens.index', compact('sumber_kliens'));
    }

    public function create()
    {
        return view('master-data.sumber-kliens.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sumber' => 'required|max:50'
        ]);

        SumberKlien::create([
            'nama_sumber' => $request->nama_sumber
        ]);

        return redirect()
            ->route('sumber-kliens.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(SumberKlien $sumber_kliens)
    {
        return view('master-data.sumber-kliens.show', compact('sumber_kliens'));
    }

    public function edit(SumberKlien $sumber_kliens)
    {
        return view('master-data.sumber-kliens.edit', compact('sumber_kliens'));
    }

    public function update(Request $request, SumberKlien $sumber_kliens)
    {
        $request->validate([
            'nama_sumber' => 'required|max:50'
        ]);

        $sumber_kliens->update([
            'nama_sumber' => $request->nama_sumber
        ]);

        return redirect()
            ->route('sumber-kliens.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(SumberKlien $sumber_kliens)
    {
        $sumber_kliens->delete();

        return redirect()
            ->route('sumber-kliens.index')
            ->with('success', 'Data berhasil dihapus');
    }
}