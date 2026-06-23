<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Priority extends Model
{
    use HasFactory;

    protected $table = 'priorities';

    protected $primaryKey = 'id_priority';

    protected $fillable = [
        'priority_name',
    ];
}
