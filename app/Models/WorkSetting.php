<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkSetting extends Model
{
    use HasFactory;

    protected $table =
        'work_settings';

    protected $fillable = [

        'checkin_start',

        'checkin_end',

        'checkout_start',

        'checkout_end'

    ];
}