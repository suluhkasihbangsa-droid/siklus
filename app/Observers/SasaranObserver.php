<?php

namespace App\Observers;

use App\Models\Sasaran;

class SasaranObserver
{
    /**
     * Handle the Sasaran "creating" event.
     *
     * This method is triggered before a new sasaran is saved to the database.
     *
     * @param  \App\Models\Sasaran  $sasaran
     * @return void
     */
    public function creating(Sasaran $sasaran)
    {
        // Pastikan ada organisasi_id sebelum membuat nomor registrasi
        if ($sasaran->organisasi_id) {
            // 1. Hitung jumlah sasaran yang sudah ada di organisasi yang sama.
            //    Ini adalah cara paling sederhana dan andal untuk mendapatkan nomor urut.
            $latestCount = Sasaran::where('organisasi_id', $sasaran->organisasi_id)->count();
            
            // 2. Buat nomor urut berikutnya
            $nextSequence = $latestCount + 1;
            
            // 3. Format nomor urut dengan padding nol (total 4 digit)
            $paddedSequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
            
            // 4. Gabungkan menjadi nomor registrasi final dan tempelkan ke model
            $sasaran->nomor_registrasi = $sasaran->organisasi_id . '-' . $paddedSequence;
        }
    }
}
