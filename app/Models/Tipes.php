<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tipes extends Model
{
    use HasFactory;

    protected $table = 'tipes';

    protected $primaryKey = 'id_tipe';

    protected $fillable = [
        'nama_tipe',
    ];
}
