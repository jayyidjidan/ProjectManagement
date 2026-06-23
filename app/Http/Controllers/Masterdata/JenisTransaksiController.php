<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\JenisTransaksi;
use Illuminate\Http\Request;

class JenisTransaksiController extends Controller
{
    public function index()
    {
        $jenis_transaksis = JenisTransaksi::latest()->paginate(10);

        return view('master-data.jenis-transaksis.index', compact('jenis_transaksis'));
    }

    public function create()
    {
        return view('master-data.jenis-transaksis.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis' => 'required|max:50'
        ]);

        JenisTransaksi::create([
            'nama_jenis' => $request->nama_jenis
        ]);

        return redirect()
            ->route('jenis-transaksis.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(JenisTransaksi $jenis_transaksi)
    {
        return view('master-data.jenis-transaksis.show', compact('jenis_transaksi'));
    }

    public function edit(JenisTransaksi $jenis_transaksi)
    {
        return view('master-data.jenis-transaksis.edit', compact('jenis_transaksi'));
    }

    public function update(Request $request, JenisTransaksi $jenis_transaksi)
    {
        $request->validate([
            'nama_jenis' => 'required|max:50'
        ]);

        $jenis_transaksi->update([
            'nama_jenis' => $request->nama_jenis
        ]);

        return redirect()
            ->route('jenis-transaksis.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(JenisTransaksi $jenis_transaksi)
    {
        $jenis_transaksi->delete();

        return redirect()
            ->route('jenis-transaksis.index')
            ->with('success', 'Data berhasil dihapus');
    }
}