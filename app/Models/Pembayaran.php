<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';

    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'total_pembayaran',
        'total_dibayarkan',
        'sisa_pembayaran',
        'jumlah_pembayaran',
    ];

    public function transaksis()
{
    return $this->hasMany(
        Transaksi::class,
        'id_pembayaran',
        'id_pembayaran'
    );
}

    public function project()
    {
        return $this->hasOne(
            Proyeks::class,
            'id_pembayaran',
            'id_pembayaran'
        );
    }
}   