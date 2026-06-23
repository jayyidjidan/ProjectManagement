<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskActivity extends Model
{
    use HasFactory;

    protected $table = 'task_activities';

    protected $primaryKey = 'id_activity';

    const UPDATED_AT = null;

    protected $fillable = [
        'id_task',
        'id_member',
        'id_type',
        'message',
        'old_value',
        'new_value',
        'created_at'
    ];

    public function task()
    {
        return $this->belongsTo(
            Task::class,
            'id_task',
            'id_task'
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

    public function type()
    {
        return $this->belongsTo(
            ActivityType::class,
            'id_type',
            'id_type'
        );
    }
}