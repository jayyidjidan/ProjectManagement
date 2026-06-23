<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SumberKlien extends Model
{
    use HasFactory;

    protected $table = 'sumber_kliens';

    protected $primaryKey = 'id_sumberklien';

    protected $fillable = [
        'nama_sumber',
    ];
}
