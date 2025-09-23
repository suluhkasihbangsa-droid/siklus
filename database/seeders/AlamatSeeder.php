<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlamatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Nonaktifkan pengecekan foreign key untuk sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->seedProvinsi();
        $this->seedKota();
        $this->seedKecamatan();
        $this->seedKelurahan();

        // Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function seedProvinsi()
    {
        DB::table('provinsis')->truncate();
        $csvFile = fopen(database_path('seeders/csv/Prov.csv'), 'r');
        fgetcsv($csvFile); // Lewati baris header

        while (($data = fgetcsv($csvFile, 2000, ';')) !== false) {
            DB::table('provinsis')->insert([
                'id' => $data[0],
                'nama_provinsi' => $data[1],
            ]);
        }
        fclose($csvFile);
        $this->command->info('Seeding tabel provinsi selesai.');
    }

    private function seedKota()
    {
        DB::table('kotas')->truncate();
        $csvFile = fopen(database_path('seeders/csv/Kota.csv'), 'r');
        fgetcsv($csvFile); // Lewati baris header

        while (($data = fgetcsv($csvFile, 2000, ';')) !== false) {
            DB::table('kotas')->insert([
                'id' => $data[0],
                'nama_kota' => $data[1],
                'provinsi_id' => $data[2],
            ]);
        }
        fclose($csvFile);
        $this->command->info('Seeding tabel kota selesai.');
    }

    private function seedKecamatan()
    {
        DB::table('kecamatans')->truncate();
        $csvFile = fopen(database_path('seeders/csv/Kec.csv'), 'r');
        fgetcsv($csvFile); // Lewati baris header

        while (($data = fgetcsv($csvFile, 2000, ';')) !== false) {
            DB::table('kecamatans')->insert([
                'id' => $data[0],
                'nama_kecamatan' => $data[1],
                'kota_id' => $data[2],
            ]);
        }
        fclose($csvFile);
        $this->command->info('Seeding tabel kecamatan selesai.');
    }

    private function seedKelurahan()
    {
        DB::table('kelurahans')->truncate();
        $csvFile = fopen(database_path('seeders/csv/Kel.csv'), 'r');
        fgetcsv($csvFile); // Lewati baris header

        while (($data = fgetcsv($csvFile, 2000, ';')) !== false) {
            DB::table('kelurahans')->insert([
                'id' => $data[0],
                'nama_kelurahan' => $data[1],
                'kecamatan_id' => $data[2],
            ]);
        }
        fclose($csvFile);
        $this->command->info('Seeding tabel kelurahan selesai.');
    }
}