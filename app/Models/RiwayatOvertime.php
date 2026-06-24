<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatOvertime extends Model
{
    use HasFactory;

    protected $table = 'riwayat_overtimes';

    protected $primaryKey = 'id_riwayat';

    protected $fillable = [
         'id_member',

    'id_attendance',

    'id_task',

    'reason',

    'start_overtime',

    'end_overtime',

    'tanggal',

    'durasi_jam',

    'status_approval',

    'id_approved_by',

    'approved_at'
    ];

    public function member()
    {
        return $this->belongsTo(
            Members::class,
            'id_member',
            'id_member'
        );
    }

    public function attendance()
    {
        return $this->belongsTo(
            Attendance::class,
            'id_attendance',
            'id_attendance'
        );
    }

    public function approvedBy()
    {
        return $this->belongsTo(
            Members::class,
            'id_approved_by',
            'id_member'
        );
    }

    public function task()
    {
        return $this->belongsTo(
            Task::class,
            'id_task',
            'id_task'
        );
    }
}