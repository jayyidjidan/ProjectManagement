<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksis';

    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
         'id_pembayaran',
        'tanggal_transaksi',
        'jumlah_transaksi',
        'bukti_transaksi',
        'metode_pembayaran',
        'id_jenis',
        'note',
    ];

    /**
     * Relasi ke jenis transaksi
     */
    public function jenis()
    {
        return $this->belongsTo(
            JenisTransaksi::class,
            'id_jenis',
            'id_jenis'
        );
    }

    public function pembayaran()
    {
        return $this->belongsTo(
            Pembayaran::class,
            'id_pembayaran',
            'id_pembayaran'
        );
    }
}