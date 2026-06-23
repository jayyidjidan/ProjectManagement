<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubTaskActivity extends Model
{
    use HasFactory;

    protected $table = 'subtask_activities';

    protected $primaryKey = 'id_activity';

    /**
     * Karena tabel hanya punya created_at
     * dan tidak memiliki updated_at
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'id_subtask',
        'id_member',
        'id_type',
        'message',
        'old_value',
        'new_value',
    ];

    /**
     * Relasi ke subtask
     */
    public function subtask()
    {
        return $this->belongsTo(
            SubTask::class,
            'id_subtask',
            'id_subtask'
        );
    }

    /**
     * Relasi ke member
     */
    public function member()
    {
        return $this->belongsTo(
            Members::class,
            'id_member',
            'id_member'
        );
    }

    /**
     * Relasi ke activity type
     */
    public function type()
    {
        return $this->belongsTo(
            ActivityType::class,
            'id_type',
            'id_type'
        );
    }
}
