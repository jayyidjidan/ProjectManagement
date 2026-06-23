<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusProyeks extends Model
{
    use HasFactory;

    protected $table = 'status_proyeks';

    protected $primaryKey = 'id_status';

    protected $fillable = [
        'nama_status',
    ];
}
