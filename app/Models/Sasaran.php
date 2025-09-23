<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sasaran extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nomor_registrasi',        
        'nik',
        'nama_lengkap',
        'tgl_lahir',
        'gender',
        'no_hp',
        'provinsi_id',
        'kota_id',
        'kecamatan_id',
        'kelurahan_id',
        'alamat_detail',
        'organisasi_id',
    ];
    
    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class);
    }
    
    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($sasaran) {
            // Generate nomor registrasi otomatis saat membuat record baru
            if (empty($sasaran->nomor_registrasi)) {
                $sasaran->nomor_registrasi = self::generateNomorRegistrasi($sasaran->organisasi_id);
            }
        });
    }
    
    /**
     * Generate nomor registrasi untuk organisasi tertentu
     */
    public static function generateNomorRegistrasi($organisasiId)
    {
        // Langsung mencari nilai numerik TERTINGGI dari nomor registrasi
        // yang sudah ada untuk organisasi_id yang spesifik.
        $maxNumber = self::where('organisasi_id', $organisasiId)
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(nomor_registrasi, "-", -1) AS UNSIGNED)) as max_seq')
            ->value('max_seq');

        // Jika tidak ada record sebelumnya (hasilnya NULL), kita mulai dari 1.
        // Jika ada, kita tambahkan 1 pada nomor tertinggi yang ditemukan.
        $sequence = ($maxNumber) ? $maxNumber + 1 : 1;
        
        // Format kembali dengan padding nol di depan
        $paddedSequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return $organisasiId . '-' . $paddedSequence;
    }
    
    public function pemeriksaans()
    {
        return $this->hasMany(Pemeriksaan::class);
    }
    
    public function pemeriksaanTerakhir()
    {
        return $this->hasOne(Pemeriksaan::class)->latest('tanggal_pemeriksaan');
    }

    /**
     * Mendefinisikan relasi 'hasManyThrough'.
     * Satu Sasaran memiliki banyak Konsultasi MELALUI Pemeriksaan.
     */
    public function konsultasis()
    {
        return $this->hasManyThrough(Konsultasi::class, Pemeriksaan::class);
    }

    /**
     * Mendefinisikan relasi 'hasOneThrough' untuk mendapatkan HANYA konsultasi terakhir.
     * Ini adalah cara yang sangat efisien untuk mengambil data yang kita butuhkan.
     */
    public function konsultasiTerakhir()
    {
        //return $this->hasOneThrough(
        //    Konsultasi::class,
        //    Pemeriksaan::class,
        //    'sasaran_id', // Foreign key di tabel pemeriksaans
        //    'pemeriksaan_id', // Foreign key di tabel konsultasis
        //    'id', // Local key di tabel sasarans
        //    'id' // Local key di tabel pemeriksaans
        //)->latest('konsultasis.created_at');

        return $this->hasOneThrough(Konsultasi::class, Pemeriksaan::class)
                    ->latest('konsultasis.created_at');
    }

    /**
     * Mendapatkan data user (dokter) yang sedang melakukan konsultasi pada sasaran ini.
     */
    public function dokterYangSedangKonsultasi()
    {
        return $this->belongsTo(User::class, 'konsultasi_oleh_id');
    }    

}