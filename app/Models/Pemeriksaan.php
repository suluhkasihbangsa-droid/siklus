<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sasaran_id',
        'usia_saat_pemeriksaan',
        'bb',
        'tb',
        'imt',
        'lila',
        'lp',
        'tensi_sistolik',
        'tensi_diastolik',
        'gd',
        'mgd',
        'asut',
        'koles',
        'int_imt',
        'int_lila',
        'int_lp',
        'int_tensi',
        'int_gd',
        'int_asut',
        'int_koles',
        'keluhan_awal',
        'tanggal_pemeriksaan',
    ];

    /**
     * Relasi ke model Sasaran.
     * Setiap data pemeriksaan pasti milik satu sasaran.
     */
    public function sasaran()
    {
        return $this->belongsTo(Sasaran::class);
    }

    /**
     * Mendapatkan semua data konsultasi yang terkait dengan pemeriksaan ini.
     */
    public function konsultasis()
    {
        return $this->hasMany(Konsultasi::class);
    }    
}
