<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provinsi;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Import library QR Code

class PublicRegistrationController extends Controller
{
    // Menampilkan form pendaftaran
    public function create()
    {
        $provinsis = Provinsi::all();
        // Kita tidak butuh data organisasi di sini
        return view('public.register_form', compact('provinsis'));
    }

    // Memproses data dan menghasilkan QR Code
    public function generateQr(Request $request)
    {
        // 1. Validasi data input
        $validatedData = $request->validate([
            'nik'           => 'nullable|digits:16',
            'nama_lengkap'  => 'required|string|max:255',
            'tgl_lahir'     => 'required|date',
            'gender'        => 'required|in:L,P',
            'no_hp'         => 'nullable|string|min:10|max:15',
            'provinsi_id'   => 'required|exists:provinsis,id',
            'kota_id'       => 'required|exists:kotas,id',
            'kecamatan_id'  => 'required|exists:kecamatans,id',
            'kelurahan_id'  => 'required|exists:kelurahans,id',
            'alamat_detail' => 'nullable|string',
        ]);

        // 2. Format data menjadi JSON
        $jsonData = json_encode($validatedData);

        // 3. Generate QR Code dari string JSON
        $qrCode = QrCode::size(300)->margin(10)->generate($jsonData);

        // 4. Tampilkan halaman yang berisi QR Code
        return view('public.show_qr', compact('qrCode', 'validatedData'));
    }
}