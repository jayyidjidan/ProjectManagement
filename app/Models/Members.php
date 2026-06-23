<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Members extends Model
{
    use HasFactory;

    protected $table = 'members';

    protected $primaryKey = 'id_member';

    protected $fillable = [
        'id_user',
        'member_name',
        'id_position',
        'joined_date',
        'profile_photo',
        'total_cuti',
        'total_WFH',
        'total_overtime',
        'point',
        'id_status',
        'work_location',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(
            Users::class,
            'id_user',
            'id_user'
        );
    }

    /**
     * Relasi ke Jabatan
     */
    public function jabatan()
    {
        return $this->belongsTo(
            Jabatan::class,
            'id_position',
            'id_position'
        );
    }

    public function position()
{
    return $this->belongsTo(
        Jabatan::class,
        'id_position',
        'id_position'
    );
}

    /**
     * Relasi ke Status Member
     */
    public function status()
    {
        return $this->belongsTo(
            StatusMembers::class,
            'id_status',
            'id_status'
        );
    }

    /**
     * Relasi Many-to-Many Skill
     */
    public function skills()
    {
        return $this->belongsToMany(
            Skills::class,
            'member_skills',
            'id_member',
            'id_skill'
        )->withTimestamps();
    }

    public function subtasks()
{
    return $this->belongsToMany(
        Subtask::class,
        'asignee_subtasks',
        'id_member',
        'id_subtask'
    )->withTimestamps();
}

public function attendances()
{
    return $this->hasMany(
        Attendance::class,
        'id_member',
        'id_member'
    );
}

public function scrumUpdates()
{
    return $this->hasMany(
        ScrumUpdate::class,
        'id_member',
        'id_member'
    );
}

public function scrums()
{
    return $this->belongsToMany(
        Scrum::class,
        'scrum_members',
        'id_member',
        'id_scrum'
    )->withPivot('id_status')
     ->withTimestamps();
}

public function responsibleScrums()
{
    return $this->hasMany(
        Scrum::class,
        'id_responsible',
        'id_member'
    );
}

public function riwayatCutis()
{
    return $this->hasMany(
        RiwayatCuti::class,
        'id_member',
        'id_member'
    );
}

public function riwayatWFHs()
{
    return $this->hasMany(
        RiwayatWFH::class,
        'id_member',
        'id_member'
    );
}

public function overtimes()
{
    return $this->hasMany(
        RiwayatOvertime::class,
        'id_member',
        'id_member'
    );
}
}