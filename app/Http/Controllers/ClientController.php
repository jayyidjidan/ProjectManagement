<?php

namespace App\Http\Controllers;

use App\Models\Kliens;
use App\Models\SumberKlien;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $sources = SumberKlien::orderBy('nama_sumber')->get();
        
        // 1. Tangkap keyword pencarian global
        $keyword = request('q');

        $clients = Kliens::with('sumberKlien')
            ->when(request('source'), function ($query) {
                $query->where(
                    'id_sumber_klien',
                    request('source')
                );
            })
            // 2. TAMBAHKAN LOGIKA SEARCH DI SINI
            ->when($keyword, function ($query, $keyword) {
                // Sesuaikan 'nama_klien' dengan nama kolom nama klien di databasemu
                return $query->where('nama_klien', 'like', "%{$keyword}%");
                
                // Opsional: Jika ada kolom perusahaan atau email dan ingin bisa dicari juga, gunakan ini:
                // return $query->where('nama_klien', 'like', "%{$keyword}%")
                //              ->orWhere('nama_perusahaan', 'like', "%{$keyword}%")
                //              ->orWhere('email_klien', 'like', "%{$keyword}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Memastikan filter source & keyword pencarian tidak hilang saat ganti halaman

        return view(
            'clients.index',
            compact('clients', 'sources')
        );
    }

    public function create()
    {
        $sources = SumberKlien::orderBy('nama_sumber')->get();

        return view('clients.create', compact('sources'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_klien'      => 'required|max:100',
            'no_telp'         => 'required|max:30',
            'email'           => 'nullable|email|max:100',
            'id_sumber_klien' => 'required',
            'asal_negara'     => 'required|max:100',
        ]);

        Kliens::create($request->all());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client berhasil ditambahkan');
    }

    public function show(Kliens $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Kliens $client)
    {
        $sources = SumberKlien::orderBy('nama_sumber')->get();

        return view(
            'clients.edit',
            compact('client', 'sources')
        );
    }

    public function update(Request $request, Kliens $client)
    {
        $request->validate([
            'nama_klien'      => 'required|max:100',
            'no_telp'         => 'required|max:30',
            'email'           => 'nullable|email|max:100',
            'id_sumber_klien' => 'required',
            'asal_negara'     => 'required|max:100',
        ]);

        $client->update($request->all());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client berhasil diperbarui');
    }

    public function destroy(Kliens $client)
    {
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client berhasil dihapus');
    }
}