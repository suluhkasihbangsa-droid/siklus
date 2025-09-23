<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sasaran;
use Illuminate\Support\Facades\DB;

class GenerateNomorRegistrasi extends Command
{
    protected $signature = 'sasaran:generate-nomor-registrasi {--force : Force regenerate all numbers}';
    protected $description = 'Generate nomor registrasi untuk data sasaran';

    public function handle()
    {
        $this->info('Memulai generate nomor registrasi...');

        if ($this->option('force')) {
            // Regenerate semua nomor registrasi
            $sasarans = Sasaran::orderBy('organisasi_id')
                ->orderBy('created_at')
                ->get();
                
            DB::table('sasarans')->update(['nomor_registrasi' => null]);
        } else {
            // Hanya generate untuk yang belum punya
            $sasarans = Sasaran::whereNull('nomor_registrasi')
                ->orderBy('organisasi_id')
                ->orderBy('created_at')
                ->get();
        }

        $currentOrgId = null;
        $sequence = 1;
        $updatedCount = 0;

        foreach ($sasarans as $sasaran) {
            // Reset sequence jika organisasi berbeda
            if ($currentOrgId !== $sasaran->organisasi_id) {
                $currentOrgId = $sasaran->organisasi_id;
                
                // Cari sequence terakhir untuk organisasi ini
                $lastSasaran = Sasaran::where('organisasi_id', $currentOrgId)
                    ->whereNotNull('nomor_registrasi')
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                if ($lastSasaran) {
                    $parts = explode('-', $lastSasaran->nomor_registrasi);
                    $sequence = (int) end($parts) + 1;
                } else {
                    $sequence = 1;
                }
            }

            // Generate nomor registrasi
            $paddedSequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
            $nomorRegistrasi = $sasaran->organisasi_id . '-' . $paddedSequence;

            // Update tanpa trigger observer
            DB::table('sasarans')
                ->where('id', $sasaran->id)
                ->update(['nomor_registrasi' => $nomorRegistrasi]);

            $this->line("ID: {$sasaran->id} | Nama: {$sasaran->nama_lengkap} | Nomor: {$nomorRegistrasi}");

            $sequence++;
            $updatedCount++;
        }

        $this->info('Selesai! Total data yang diupdate: ' . $updatedCount);
    }
}