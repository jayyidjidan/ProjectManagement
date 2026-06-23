<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScrumUpdate extends Model
{
    use HasFactory;
    
    protected $table = 'scrum_updates';

    protected $primaryKey =
        'id_scrum_update';

    protected $fillable = [
        'id_scrum',
        'id_member',

        'id_task_1',
        'target_1',

        'id_task_2',
        'target_2',
    ];

    public function scrum()
    {
        return $this->belongsTo(
            Scrum::class,
            'id_scrum',
            'id_scrum'
        );
    }

    public function member()
    {
        return $this->belongsTo(
            Members::class,
            'id_member',
            'id_member'
        );
    }

    public function task1()
    {
        return $this->belongsTo(
            Task::class,
            'id_task_1',
            'id_task'
        );
    }

    public function task2()
    {
        return $this->belongsTo(
            Task::class,
            'id_task_2',
            'id_task'
        );
    }
}