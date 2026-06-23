<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $total = Pembayaran::count();

        $finished = Pembayaran::where(
            'sisa_pembayaran',
            '<=',
            0
        )->count();

        $remaining = Pembayaran::where(
            'sisa_pembayaran',
            '>',
            0
        )->count();

        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');

        // 1. Tangkap keyword pencarian global
        $keyword = request('q');

        $query = Pembayaran::with([
            'project'
        ]);

        if (request('status') == 'finished') {
            $query->where(
                'sisa_pembayaran',
                '<=',
                0
            );
        }

        if (request('status') == 'remaining') {
            $query->where(
                'sisa_pembayaran',
                '>',
                0
            );
        }

        // 2. TAMBAHKAN LOGIKA SEARCH DI SINI
        $query->when($keyword, function ($query, $keyword) {
            return $query->whereHas('project', function ($q) use ($keyword) {
                $q->where('nama_proyek', 'like', "%{$keyword}%");
            });
        });

        $payments = $query
            ->orderBy(
                $sort,
                $direction
            )
            ->paginate(10)
            ->withQueryString(); // Memastikan filter status & keyword tetap terbawa saat pindah halaman

        return view(
            'payments.index',
            compact(
                'payments',
                'total',
                'finished',
                'remaining'
            )
        );
    }

    public function show(Pembayaran $payment)
    {
        $payment->load([
            'project',
            'transaksis'
        ]);

        return view(
            'payments.show',
            compact('payment')
        );
    }
}