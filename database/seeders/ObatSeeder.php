<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obat;
use Illuminate\Support\Facades\Schema;

class ObatSeeder extends Seeder
{
    public function run()
    {
        // 2. Nonaktifkan pengecekan foreign key
        Schema::disableForeignKeyConstraints();

        // 3. Kosongkan tabel seperti biasa
        Obat::truncate(); 

        // 4. Aktifkan kembali pengecekan foreign key
        Schema::enableForeignKeyConstraints();
        
        $obats = [
            ['nama_obat' => 'Panadol (Parasetamol 500 mg Tab)', 'kategori' => 'Analgesik', 'satuan' => 'Strip'],
            ['nama_obat' => 'Parasetamol Sir 120 mg/5 ml', 'kategori' => 'Analgesik', 'satuan' => 'Botol'],
            ['nama_obat' => 'Panadol Sirup (Parasetamol 160 mg/5 ml Sir)', 'kategori' => 'Analgesik', 'satuan' => 'Botol'],
            ['nama_obat' => 'Parasetamol Drops 100 mg/ml', 'kategori' => 'Analgesik', 'satuan' => 'Botol'],
            ['nama_obat' => 'Panadol Anak (Parasetamol 120 mg Tab kunyah)', 'kategori' => 'Analgesik', 'satuan' => 'Strip'],
            ['nama_obat' => 'Panadol Extra (Parasetamol 500 mg + Kafein 65 mg Tab)', 'kategori' => 'Analgesik', 'satuan' => 'Strip'],
        ];

        foreach ($obats as $obat) {
            Obat::create($obat);
        }
    }
}