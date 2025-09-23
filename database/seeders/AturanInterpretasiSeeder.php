<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AturanInterpretasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Kosongkan tabel sebelum diisi untuk menghindari duplikasi
        DB::table('aturan_interpretasi')->truncate();

        $aturan = [
            // ==================== IMT (Indeks Massa Tubuh) - Usia >= 19 tahun ====================
            [
                'kategori' => 'IMT',
                'nama_interpretasi' => 'Berat Badan Kurang',
                'kode_interpretasi' => 'K',
                'kondisi_usia_min_bulan' => 228, // 19 tahun * 12 bulan
                'batas_atas' => 18.49,
            ],
            [
                'kategori' => 'IMT',
                'nama_interpretasi' => 'Berat Badan Normal',
                'kode_interpretasi' => 'N',
                'kondisi_usia_min_bulan' => 228,
                'batas_bawah' => 18.5,
                'batas_atas' => 22.9,
            ],
            [
                'kategori' => 'IMT',
                'nama_interpretasi' => 'Gemuk',
                'kode_interpretasi' => 'G',
                'kondisi_usia_min_bulan' => 228,
                'batas_bawah' => 23,
                'batas_atas' => 24.9,
            ],
            [
                'kategori' => 'IMT',
                'nama_interpretasi' => 'Obesitas',
                'kode_interpretasi' => 'O',
                'kondisi_usia_min_bulan' => 228,
                'batas_bawah' => 25,
            ],

            // ==================== Lingkar Perut (LP) - Usia >= 15 tahun ====================
            [
                'kategori' => 'LP',
                'nama_interpretasi' => 'Normal',
                'kode_interpretasi' => 'N',
                'kondisi_gender' => 'L', // Pria
                'kondisi_usia_min_bulan' => 180, // 15 tahun
                'batas_atas' => 90,
            ],
            [
                'kategori' => 'LP',
                'nama_interpretasi' => 'Obesitas Sentral',
                'kode_interpretasi' => 'ObS',
                'kondisi_gender' => 'L', // Pria
                'kondisi_usia_min_bulan' => 180,
                'batas_bawah' => 90.01,
            ],
            [
                'kategori' => 'LP',
                'nama_interpretasi' => 'Normal',
                'kode_interpretasi' => 'N',
                'kondisi_gender' => 'P', // Wanita
                'kondisi_usia_min_bulan' => 180,
                'batas_atas' => 80,
            ],
            [
                'kategori' => 'LP',
                'nama_interpretasi' => 'Obesitas Sentral',
                'kode_interpretasi' => 'ObS',
                'kondisi_gender' => 'P', // Wanita
                'kondisi_usia_min_bulan' => 180,
                'batas_bawah' => 80.01,
            ],

            // ==================== Lingkar Lengan Atas (LiLA) - Wanita Dewasa (15-49 tahun) ====================
            [
                'kategori' => 'LILA_DEWASA',
                'nama_interpretasi' => 'Kurang Energi Kronis',
                'kode_interpretasi' => 'KEK',
                'kondisi_gender' => 'P',
                'kondisi_usia_min_bulan' => 180, // 15 tahun
                'kondisi_usia_max_bulan' => 599, // 49 tahun 11 bulan
                'batas_atas' => 23.49,
            ],
            [
                'kategori' => 'LILA_DEWASA',
                'nama_interpretasi' => 'Normal',
                'kode_interpretasi' => 'N',
                'kondisi_gender' => 'P',
                'kondisi_usia_min_bulan' => 180,
                'kondisi_usia_max_bulan' => 599,
                'batas_bawah' => 23.5,
            ],

            // ==================== Lingkar Lengan Atas (LiLA) - Balita (6-59 bulan) ====================
            [
                'kategori' => 'LILA_BALITA',
                'nama_interpretasi' => 'Gizi Buruk',
                'kode_interpretasi' => 'GB',
                'kondisi_usia_min_bulan' => 6,
                'kondisi_usia_max_bulan' => 59,
                'batas_atas' => 11.49,
            ],
            [
                'kategori' => 'LILA_BALITA',
                'nama_interpretasi' => 'Gizi Kurang',
                'kode_interpretasi' => 'GK',
                'kondisi_usia_min_bulan' => 6,
                'kondisi_usia_max_bulan' => 59,
                'batas_bawah' => 11.5,
                'batas_atas' => 12.4,
            ],
            [
                'kategori' => 'LILA_BALITA',
                'nama_interpretasi' => 'Normal',
                'kode_interpretasi' => 'N',
                'kondisi_usia_min_bulan' => 6,
                'kondisi_usia_max_bulan' => 59,
                'batas_bawah' => 12.5,
            ],

            // ==================== Tensi - Usia >= 15 tahun ====================
            [
                'kategori' => 'TENSI',
                'nama_interpretasi' => 'Normal',
                'kode_interpretasi' => 'N',
                'kondisi_usia_min_bulan' => 180,
                'batas_sistolik' => 139, // Aturan: <= 139
                'batas_diastolik' => 89, // Aturan: <= 89
            ],
            // Note: Hipertensi adalah "selain itu", jadi akan dihandle oleh logika di controller

            // ==================== Gula Darah Puasa - Usia >= 15 tahun ====================
            [
                'kategori' => 'GULA_DARAH', 'kondisi_metode' => 'P', 'nama_interpretasi' => 'Normal', 'kondisi_usia_min_bulan' => 180, 'batas_atas' => 99.99,
            ],
            [
                'kategori' => 'GULA_DARAH', 'kondisi_metode' => 'P', 'nama_interpretasi' => 'Prediabetes', 'kondisi_usia_min_bulan' => 180, 'batas_bawah' => 100, 'batas_atas' => 125,
            ],
            [
                'kategori' => 'GULA_DARAH', 'kondisi_metode' => 'P', 'nama_interpretasi' => 'Diabetes', 'kondisi_usia_min_bulan' => 180, 'batas_bawah' => 126,
            ],

            // ==================== Gula Darah Sewaktu - Usia >= 15 tahun ====================
             [
                'kategori' => 'GULA_DARAH', 'kondisi_metode' => 'S', 'nama_interpretasi' => 'Normal', 'kondisi_usia_min_bulan' => 180, 'batas_atas' => 139.99,
            ],
            [
                'kategori' => 'GULA_DARAH', 'kondisi_metode' => 'S', 'nama_interpretasi' => 'Prediabetes', 'kondisi_usia_min_bulan' => 180, 'batas_bawah' => 140, 'batas_atas' => 199.99,
            ],
            [
                'kategori' => 'GULA_DARAH', 'kondisi_metode' => 'S', 'nama_interpretasi' => 'Diabetes', 'kondisi_usia_min_bulan' => 180, 'batas_bawah' => 200,
            ],

            // ==================== Asam Urat - Usia >= 15 tahun ====================
            [
                'kategori' => 'ASAM_URAT', 'kondisi_gender' => 'L', 'nama_interpretasi' => 'Normal', 'kondisi_usia_min_bulan' => 180, 'batas_atas' => 6.99,
            ],
            [
                'kategori' => 'ASAM_URAT', 'kondisi_gender' => 'L', 'nama_interpretasi' => 'Tinggi', 'kondisi_usia_min_bulan' => 180, 'batas_bawah' => 7.0,
            ],
            [
                'kategori' => 'ASAM_URAT', 'kondisi_gender' => 'P', 'nama_interpretasi' => 'Normal', 'kondisi_usia_min_bulan' => 180, 'batas_atas' => 5.99,
            ],
            [
                'kategori' => 'ASAM_URAT', 'kondisi_gender' => 'P', 'nama_interpretasi' => 'Tinggi', 'kondisi_usia_min_bulan' => 180, 'batas_bawah' => 6.0,
            ],

            // ==================== Kolesterol - Usia >= 15 tahun ====================
            [
                'kategori' => 'KOLESTEROL', 'nama_interpretasi' => 'Normal', 'kondisi_usia_min_bulan' => 180, 'batas_atas' => 199.99,
            ],
            [
                'kategori' => 'KOLESTEROL', 'nama_interpretasi' => 'Tinggi', 'kondisi_usia_min_bulan' => 180, 'batas_bawah' => 200,
            ],
        ];

        // Masukkan data ke dalam database
        foreach ($aturan as $rule) {
            DB::table('aturan_interpretasi')->insert($rule);
        }
    }
}
