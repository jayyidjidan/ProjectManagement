<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Users extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username',
        'email',
        'password',
        'id_role',
    ];

    public function getAuthIdentifierName()
    {
        return 'id_user';
    }

    public function getIdAttribute()
    {
        return $this->id_user;
    }

    public function role()
    {
        return $this->belongsTo(
            Roles::class,
            'id_role',
            'id_role'
        );
    }

    public function member()
    {
        return $this->hasOne(
            Members::class,
            'id_user',
            'id_user'
        );
    }

    public function isSuperAdmin()
    {
        return $this->id_role == 1;
    }

    public function isProjectManager()
    {
        return $this->id_role == 2;
    }

    public function isMember()
    {
        return $this->id_role == 3;
    }

    public function canManageSystem()
    {
        return in_array(
            $this->id_role,
            [1]
        );
    }

    public function canManageProjects()
    {
        return in_array(
            $this->id_role,
            [1,2]
        );
    }

    public function isEmployee()
    {
        return in_array(
            $this->id_role,
            [2,3]
        );
    }
}
