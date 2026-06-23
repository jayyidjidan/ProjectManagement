<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusMembers extends Model
{
    use HasFactory;

    protected $table = 'status_members';

    protected $primaryKey = 'id_status';

    protected $fillable = [
        'status_name',
    ];
}
