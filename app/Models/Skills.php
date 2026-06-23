<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skills extends Model
{
    use HasFactory;

    protected $table = 'skills';

    protected $primaryKey = 'id_skill';

    protected $fillable = [
        'skill_name',
    ];
}
