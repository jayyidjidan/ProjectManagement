<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusTasks extends Model
{
    use HasFactory;

    protected $table = 'status_tasks';

    protected $primaryKey = 'id_status';

    protected $fillable = [
        'status_name',
    ];
}
