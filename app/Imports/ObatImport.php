<?php

namespace App\Imports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ObatImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
{
    // Gunakan trait ini agar kita bisa mengumpulkan pesan error/kegagalan
    use SkipsFailures, SkipsErrors;
    private $rowCount = 0;

    /**
     * Fungsi ini akan mengubah setiap baris di Excel menjadi Model Obat.
     * Laravel Excel akan secara otomatis memanggil fungsi ini untuk setiap baris.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        
        $this->rowCount++;

        // Pengecekan sederhana untuk mengabaikan baris contoh (No. 0)
        // Kita asumsikan header kolomnya 'no' (sesuaikan jika berbeda di file Anda)
        if (isset($row['no']) && $row['no'] == '0') {
            return null; // Mengabaikan baris ini
        }
        
        return new Obat([
            // 'nama_obat' adalah nama kolom di database.
            // 'nama_obat' di sebelah kanan adalah nama header di file Excel (setelah di-lowercase).
            'nama_obat' => $row['nama_obat'],
            'kategori'  => $row['kategori'],
            'satuan'    => $row['satuan'],
        ]);
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * Aturan validasi untuk setiap baris.
     * Jika sebuah baris gagal validasi, ia akan dilewati dan ditandai sebagai 'failure'.
     */
    public function rules(): array
    {
        return [
            // 'nama_obat' harus unik di tabel 'obats'
            'nama_obat' => 'required|string|max:255|unique:obats,nama_obat',
            'kategori' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
        ];
    }

    /**
     * Pesan kustom untuk validasi.
     */
    public function customValidationMessages()
    {
        return [
            'nama_obat.unique' => 'Nama obat ini sudah ada di database.',
            'nama_obat.required' => 'Kolom Nama Obat tidak boleh kosong.',
            'kategori.required' => 'Kolom Kategori tidak boleh kosong.',
            'satuan.required' => 'Kolom Satuan tidak boleh kosong.',
        ];
    }
}