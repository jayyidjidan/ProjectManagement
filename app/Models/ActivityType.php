<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityType extends Model
{
    use HasFactory;

    protected $table = 'activity_types';

    protected $primaryKey = 'id_type';

    protected $fillable = [
        'type_name',
    ];
}
