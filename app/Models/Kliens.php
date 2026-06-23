<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kliens extends Model
{
    use HasFactory;

    protected $table = 'kliens';

    protected $primaryKey = 'id_klien';

    protected $fillable = [
        'nama_klien',
        'no_telp',
        'email',
        'id_sumber_klien',
        'asal_negara',
    ];

    /**
     * Relasi ke sumber klien
     */
    public function sumberKlien()
    {
        return $this->belongsTo(
            SumberKlien::class,
            'id_sumber_klien',
            'id_sumberklien'
        );
    }
}
