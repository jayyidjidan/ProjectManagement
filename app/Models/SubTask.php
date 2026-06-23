<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SubTaskActivity;

class Subtask extends Model
{
    use HasFactory;

    protected $table = 'subtasks';

    protected $primaryKey = 'id_subtask';

    protected $fillable = [
        'id_task',
        'subtask_name',
        'subtask_deadline',
        'id_priority',
        'id_status',
        'note',
    ];

    /**
     * Relasi ke task induk
     */
    public function task()
    {
        return $this->belongsTo(
            Task::class,
            'id_task',
            'id_task'
        );
    }

    /**
     * Relasi ke priority
     */
    public function priority()
    {
        return $this->belongsTo(
            Priority::class,
            'id_priority',
            'id_priority'
        );
    }

    /**
     * Relasi ke status subtask
     */
    public function status()
    {
        return $this->belongsTo(
            StatusTasks::class,
            'id_status',
            'id_status'
        );
    }

    public function assignees()
    {
        return $this->belongsToMany(
            Members::class,
            'asignee_subtasks',
            'id_subtask',
            'id_member'
        )->withTimestamps();
    }

    public function activities()
    {
        return $this->hasMany(
            SubTaskActivity::class, 
            'id_subtask', // Foreign key di tabel subtask_activities
            'id_subtask'  // Local key di tabel subtasks
        );
    }
}