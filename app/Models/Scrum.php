<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Scrum extends Model
{
    use HasFactory;

    protected $table = 'scrums';

    protected $primaryKey = 'id_scrum';

    protected $fillable = [
        'day',
        'date_scrum',
        'id_responsible',
        'scrum_password',
        'is_locked',
    ];

    public function responsible()
    {
        return $this->belongsTo(
            Members::class,
            'id_responsible',
            'id_member'
        );
    }

    public function attendances()
    {
        return $this->hasMany(
            Attendance::class,
            'id_scrum',
            'id_scrum'
        );
    }

    public function updates()
    {
        return $this->hasMany(
            ScrumUpdate::class,
            'id_scrum',
            'id_scrum'
        );
    }

    public function members()
    {
        return $this->belongsToMany(
            Members::class,
            'scrum_members',
            'id_scrum',
            'id_member'
        )->withPivot('id_status')
         ->withTimestamps();
    }
}