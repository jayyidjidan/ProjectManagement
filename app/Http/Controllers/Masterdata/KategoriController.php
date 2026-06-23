<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Kategoris;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategoris::latest()->paginate(10);

        return view('master-data.kategoris.index', compact('kategoris'));
    }

    public function create()
    {
        return view('master-data.kategoris.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|max:100'
        ]);

        Kategoris::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()
            ->route('kategoris.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(Kategoris $kategori)
    {
        return view('master-data.kategoris.show', compact('kategori'));
    }

    public function edit(Kategoris $kategori)
    {
        return view('master-data.kategoris.edit', compact('kategori'));
    }

    public function update(Request $request, Kategoris $kategori)
    {
        $request->validate([
            'nama_kategori' => 'required|max:100'
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()
            ->route('kategoris.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(Kategoris $kategori)
    {
        $kategori->delete();

        return redirect()
            ->route('kategoris.index')
            ->with('success', 'Data berhasil dihapus');
    }
}