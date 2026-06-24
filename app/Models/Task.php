<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $primaryKey = 'id_task';

    protected $fillable = [
        'id_proyek',
        'nama_task',
        'deadline_task',
        'id_priority',
        'id_status',
        'note',
    ];

    /**
     * Relasi ke proyek
     */
    public function project()
    {
        return $this->belongsTo(
            Proyeks::class,
            'id_proyek',
            'id_proyek'
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
     * Relasi ke status task
     */
    public function status()
    {
        return $this->belongsTo(
            StatusTasks::class,
            'id_status',
            'id_status'
        );
    }

    /**
     * Relasi assignee task
     */
    public function assignees()
    {
        return $this->belongsToMany(
            Members::class,
            'asignee_tasks',
            'id_task',
            'id_member'
        )->withTimestamps();
    }
    
    /**
 * Relasi ke subtasks
 */
public function subtasks()
{
    return $this->hasMany(
        Subtask::class,
        'id_task',
        'id_task'
    );
}

    public function activities()
    {
        return $this->hasMany(
            TaskActivity::class,
            'id_task',
            'id_task'
        )
        ->latest(
            'created_at'
        );
    }

    public function getHasUnreadCommentsAttribute()
    {
        $memberId = auth()->user()->member->id_member ?? null;
        if (!$memberId) return false;

        // Ambil kapan terakhir kali member ini membuka detail task ini
        $lastRead = \DB::table('task_reads')
            ->where('id_task', $this->id_task)
            ->where('id_member', $memberId)
            ->value('last_read_at');

        // Cek apakah ada activity baru bertipe "Comment" (id_type = 1)
        return \DB::table('task_activities')
            ->where('id_task', $this->id_task)
            ->where('id_type', 1) // 1 = Comment sesuai isi tabel activity_types kamu
            ->where('id_member', '!=', $memberId) // Kecualikan komen dari diri sendiri
            ->when($lastRead, function($query) use ($lastRead) {
                $query->where('created_at', '>', $lastRead);
            })
            ->exists();
    }
}