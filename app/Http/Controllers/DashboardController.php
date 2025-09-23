<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sasaran; // <-- Import model Sasaran
use Illuminate\Support\Facades\Auth; // <-- Import Fassad Auth

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Hitung total semua sasaran, ini dibutuhkan untuk semua role
        $totalSasaran = Sasaran::count();

        // Siapkan variabel yang akan dikirim ke view
        $jumlahSasaranDiregister = 0;
        $persentaseSasaranDiregister = 0;

        // Logika berdasarkan role user
        if ($user->role === 'admin') {
            $jumlahSasaranDiregister = $totalSasaran;
            $persentaseSasaranDiregister = 100; // Sesuai permintaan, untuk admin selalu 100%
        } else {
            // Untuk role user, koorUser, dokter, dll.
            if ($user->organisasi_id) {
                // Hitung jumlah sasaran berdasarkan organisasi_id milik user
                $jumlahSasaranOrganisasi = Sasaran::where('organisasi_id', $user->organisasi_id)->count();
                $jumlahSasaranDiregister = $jumlahSasaranOrganisasi;

                // Hitung persentase, hindari pembagian dengan nol
                if ($totalSasaran > 0) {
                    $persentaseSasaranDiregister = ($jumlahSasaranOrganisasi / $totalSasaran) * 100;
                }
            }
            // Jika user tidak punya organisasi_id, nilainya akan tetap 0
        }

        // Kirim data ke view dashboard
        return view('dashboard.dashboard', [
            'jumlahSasaranDiregister' => $jumlahSasaranDiregister,
            'persentaseSasaranDiregister' => round($persentaseSasaranDiregister) // Dibulatkan agar rapi
        ]);
    }
}