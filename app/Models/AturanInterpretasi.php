<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturanInterpretasi extends Model
{
    use HasFactory;
    protected $table = 'aturan_interpretasi';
    protected $fillable = [
    'nama_interpretasi',
    'kode_interpretasi',
    'warna_badge',
    'batas_bawah',
    'batas_atas',
    'batas_sistolik',
    'batas_diastolik',
    'kategori' 
    ];
}
