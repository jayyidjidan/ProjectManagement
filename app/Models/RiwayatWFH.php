<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatWFH extends Model
{
    use HasFactory;

    protected $table = 'riwayat_wfhs';

    protected $primaryKey = 'id_riwayat';

    protected $fillable = [
        'id_member',
        'id_scrum',
        'tanggal',
        'keterangan',
    ];

    public function member()
    {
        return $this->belongsTo(
            Member::class,
            'id_member',
            'id_member'
        );
    }

    public function scrum()
    {
        return $this->belongsTo(
            Scrum::class,
            'id_scrum',
            'id_scrum'
        );
    }
}