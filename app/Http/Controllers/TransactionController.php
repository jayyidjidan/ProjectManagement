<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pembayaran;
use App\Models\JenisTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index()
    {
        $sort = request('sort', 'tanggal_transaksi');
        $direction = request('direction', 'desc');

        // 1. Tangkap keyword pencarian global
        $keyword = request('q');

        $transactions = Transaksi::with([
            'pembayaran.project',
            'jenis'
        ])
        ->when(
            request('payment_method'),
            function ($query) {
                $query->where(
                    'metode_pembayaran',
                    request('payment_method')
                );
            }
        )
        ->when(
            request('id_jenis'),
            function ($query) {
                $query->where(
                    'id_jenis',
                    request('id_jenis')
                );
            }
        )
        // 2. TAMBAHKAN LOGIKA SEARCH DI SINI (Menembus relasi ke Nama Proyek)
        ->when($keyword, function ($query, $keyword) {
            return $query->whereHas('pembayaran.project', function ($q) use ($keyword) {
                $q->where('nama_proyek', 'like', "%{$keyword}%");
            });
            
            // Opsional: Jika di tabel transaksi ada kolom 'keterangan' dan ingin bisa dicari juga, gunakan ini:
            // return $query->where('keterangan', 'like', "%{$keyword}%")
            //              ->orWhereHas('pembayaran.project', function ($q) use ($keyword) {
            //                  $q->where('nama_proyek', 'like', "%{$keyword}%");
            //              });
        })
        ->orderBy(
            $sort,
            $direction
        )
        ->paginate(10)
        ->withQueryString(); // Memastikan filter metode, jenis, dan keyword search tidak hilang saat ganti halaman

        $types = JenisTransaksi::all();

        return view(
            'transactions.index',
            compact(
                'transactions',
                'types'
            )
        );
    }

    public function create(Request $request)
    {
        $payment = Pembayaran::findOrFail(
            $request->payment
        );

        $types = JenisTransaksi::orderBy(
            'nama_jenis'
        )->get();

        return view(
            'transactions.create',
            compact(
                'payment',
                'types'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pembayaran' => 'required',
            'tanggal_transaksi' => 'required|date',
            'jumlah_transaksi' => 'required|numeric|min:1',
            'metode_pembayaran' => 'nullable|max:50',
            'id_jenis' => 'nullable',
            'note' => 'nullable',
            'bukti_transaksi' => 'nullable|image|max:2048'
        ]);

        $path = null;

        if ($request->hasFile('bukti_transaksi')) {

            $path = $request
                ->file('bukti_transaksi')
                ->store(
                    'transactions',
                    'public'
                );
        }

        $payment = Pembayaran::findOrFail(
            $request->id_pembayaran
        );

        if (
            $request->jumlah_transaksi >
            $payment->sisa_pembayaran
        ) {

            return back()
                ->withErrors([
                    'jumlah_transaksi' =>
                        'Jumlah transaksi melebihi sisa pembayaran.'
                ])
                ->withInput();
        }

        $transaction = Transaksi::create([
            'id_pembayaran' => $request->id_pembayaran,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'jumlah_transaksi' => $request->jumlah_transaksi,
            'bukti_transaksi' => $path,
            'metode_pembayaran' => $request->metode_pembayaran,
            'id_jenis' => $request->id_jenis,
            'note' => $request->note,
        ]);

        $this->updatePayment(
            $transaction->id_pembayaran
        );

        return redirect()
            ->route(
                'payments.show',
                $request->id_pembayaran
            )
            ->with(
                'success',
                'Transaction added successfully'
            );
    }

    public function edit(Transaksi $transaction)
    {
        $types = JenisTransaksi::orderBy(
            'nama_jenis'
        )->get();

        return view(
            'transactions.edit',
            compact(
                'transaction',
                'types'
            )
        );
    }

    public function update(
        Request $request,
        Transaksi $transaction
    ) {

        $request->validate([
            'tanggal_transaksi' => 'required|date',
            'jumlah_transaksi' => 'required|numeric|min:1',
            'metode_pembayaran' => 'nullable|max:50',
            'id_jenis' => 'nullable',
            'note' => 'nullable',
            'bukti_transaksi' => 'nullable|image|max:2048'
        ]);

        $path = $transaction->bukti_transaksi;

        if ($request->hasFile('bukti_transaksi')) {

            if ($path) {

                Storage::disk('public')
                    ->delete($path);
            }

            $path = $request
                ->file('bukti_transaksi')
                ->store(
                    'transactions',
                    'public'
                );
        }

        $payment = Pembayaran::findOrFail(
            $transaction->id_pembayaran
        );

        $allowedAmount =
            $payment->sisa_pembayaran
            +
            $transaction->jumlah_transaksi;

        if (
            $request->jumlah_transaksi >
            $allowedAmount
        ) {

            return back()
                ->withErrors([
                    'jumlah_transaksi' =>
                        'Jumlah transaksi melebihi sisa pembayaran.'
                ])
                ->withInput();
        }

        $transaction->update([
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'jumlah_transaksi' => $request->jumlah_transaksi,
            'bukti_transaksi' => $path,
            'metode_pembayaran' => $request->metode_pembayaran,
            'id_jenis' => $request->id_jenis,
            'note' => $request->note,
        ]);

        $this->updatePayment(
            $transaction->id_pembayaran
        );

        return redirect()
            ->route(
                'payments.show',
                $transaction->id_pembayaran
            )
            ->with(
                'success',
                'Transaction updated successfully'
            );
    }

    public function destroy(
        Transaksi $transaction
    ) {

        $paymentId =
            $transaction->id_pembayaran;

        if ($transaction->bukti_transaksi) {

            Storage::disk('public')
                ->delete(
                    $transaction->bukti_transaksi
                );
        }

        $transaction->delete();

        $this->updatePayment(
            $paymentId
        );

        return back()
            ->with(
                'success',
                'Transaction deleted successfully'
            );
    }

    private function updatePayment(
        $paymentId
    ) {

        $payment = Pembayaran::findOrFail(
            $paymentId
        );

        $totalPaid = $payment
            ->transaksis()
            ->sum('jumlah_transaksi');

        $count = $payment
            ->transaksis()
            ->count();

        $payment->update([
            'total_dibayarkan' => $totalPaid,

            'sisa_pembayaran' =>
                $payment->total_pembayaran
                - $totalPaid,

            'jumlah_pembayaran' => $count
        ]);
    }
}