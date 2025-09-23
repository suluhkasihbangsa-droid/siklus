<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pemeriksaan_id',
        'dokter_id',
        'keluhan',
        'diagnosa',
        'rekomendasi',
        'resep_obat',
    ];

    /**
     * Mendapatkan data pemeriksaan yang terkait dengan konsultasi ini.
     */
    public function pemeriksaan()
    {
        return $this->belongsTo(Pemeriksaan::class);
    }

    /**
     * Mendapatkan data dokter (user) yang melakukan konsultasi ini.
     */
    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    /**
     * Mendapatkan semua item resep obat untuk konsultasi ini.
     */
    public function resepObats()
    {
        return $this->hasMany(ResepObat::class);
    }


}