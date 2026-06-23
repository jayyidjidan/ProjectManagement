<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proyeks extends Model
{
    use HasFactory;

    protected $table = 'proyeks';

    protected $primaryKey = 'id_proyek';

    protected $fillable = [
        'nama_proyek',
        'deadline',
        'id_status',
        'id_klien',
        'id_pembayaran',
        'id_tipe',
        'id_project_manager',
    ];

    /**
     * Status proyek
     */
    public function status()
    {
        return $this->belongsTo(
            StatusProyeks::class,
            'id_status',
            'id_status'
        );
    }

    /**
     * Klien proyek
     */
    public function klien()
    {
        return $this->belongsTo(
            Kliens::class,
            'id_klien',
            'id_klien'
        );
    }

    /**
     * Pembayaran proyek
     */
    public function pembayaran()
    {
        return $this->belongsTo(
            Pembayaran::class,
            'id_pembayaran',
            'id_pembayaran'
        );
    }

    /**
     * Tipe proyek
     */
    public function tipe()
    {
        return $this->belongsTo(
            Tipes::class,
            'id_tipe',
            'id_tipe'
        );
    }

    /**
     * Project Manager
     */
    public function projectManager()
    {
        return $this->belongsTo(
            Members::class,
            'id_project_manager',
            'id_member'
        );
    }

    public function kategoris()
{
    return $this->belongsToMany(
        Kategoris::class,
        'kategori_proyeks',
        'id_proyek',
        'id_kategori'
    )->withTimestamps();
}

public function tasks()
{
    return $this->hasMany(
        Task::class,
        'id_proyek',
        'id_proyek'
    );
}
}