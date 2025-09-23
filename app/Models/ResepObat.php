<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepObat extends Model
{
    use HasFactory;
    protected $fillable = ['konsultasi_id', 'obat_id', 'qty', 'keterangan_konsumsi'];

    /**
     * Mendapatkan data obat (dari tabel obats) yang terkait dengan item resep ini.
     */
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}