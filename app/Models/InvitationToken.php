<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvitationToken extends Model
{
    use HasFactory;

    protected $table = 'invitation_tokens';

    protected $primaryKey = 'id';

    protected $fillable = [
        'email',
        'id_role',
        'token',
        'is_used',
        'expired_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'expired_at' => 'datetime',
    ];

    /**
     * Relasi ke role yang diundang
     */
    public function role()
    {
        return $this->belongsTo(
            Roles::class,
            'id_role',
            'id_role'
        );
    }
}