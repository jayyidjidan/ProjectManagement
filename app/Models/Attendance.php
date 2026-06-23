<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $primaryKey = 'id_attendance';

    protected $fillable = [
        'id_member',
        'id_scrum',
        'id_status',
        'start_hour',
        'leave_hour',
        'tanggal',
        'work_hours'
    ];

    public function member()
    {
        return $this->belongsTo(
            Members::class,
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

    public function status()
    {
        return $this->belongsTo(
            StatusMembers::class,
            'id_status',
            'id_status'
        );
    }

    public function attendanceTasks()
    {
        return $this->hasMany(
            AttendanceTask::class,
            'id_attendance',
            'id_attendance'
        );
    }

    public function overtime()
    {
        return $this->hasOne(
            RiwayatOvertime::class,
            'id_attendance',
            'id_attendance'
        );
    }
}