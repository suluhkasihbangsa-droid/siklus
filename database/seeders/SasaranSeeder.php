<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SasaranSeeder extends Seeder
{
    private $currentNik = 1111111111111111;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $maleNames = [
            'Budi Santoso', 'Agus Setiawan', 'Rizky Pratama', 'Andi Wijaya', 'Fajar Nugroho',
            'Doni Firmansyah', 'Hendra Maulana', 'Riko Aditya', 'Eko Prasetyo', 'Yudi Hartono'
        ];

        $femaleNames = [
            'Siti Aminah', 'Dewi Lestari', 'Anita Wulandari', 'Maya Sari', 'Rina Permata',
            'Lina Marlina', 'Putri Ayu', 'Nadia Fitri', 'Citra Kirana', 'Dina Puspita'
        ];

        // Hanya menggunakan ID: 2, 3, 5, 6, 7
        $organisasiIds = [2, 3, 5, 6, 7];

        for ($i = 0; $i < 10; $i++) {
            $isMale = $i < 5; // 5 laki-laki, 5 perempuan
            $name = $isMale ? $maleNames[$i] : $femaleNames[$i - 5];
            $gender = $isMale ? 'L' : 'P';

            DB::table('sasarans')->insert([
                'nik' => $this->generateSequentialNik(),
                'nama_lengkap' => $name,
                'tgl_lahir' => Carbon::now()->subYears(rand(18, 45))->toDateString(),
                'gender' => $gender,
                'no_hp' => '08' . rand(1000000000, 9999999999),
                'provinsi_id' => 13,
                'kota_id' => 193,
                'kecamatan_id' => 2720,
                'kelurahan_id' => 33062,
                'alamat_detail' => 'RT 0' . rand(1, 9) . ' RW 0' . rand(1, 9) . ' Jl. ' . Str::random(6) . ' No. ' . rand(1, 50),
                'organisasi_id' => $organisasiIds[array_rand($organisasiIds)], // Random dari [2,3,5,6,7]
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Generate NIK secara urut: 1111111111111111, 1111111111111112, dst.
     */
    private function generateSequentialNik()
    {
        return str_pad($this->currentNik++, 16, '0', STR_PAD_LEFT);
    }
}