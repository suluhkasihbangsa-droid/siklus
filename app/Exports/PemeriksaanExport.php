<?php

namespace App\Exports;

use App\Models\Pemeriksaan;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting; // <-- 1. IMPORT CONCERN BARU
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;     // <-- 2. IMPORT FORMATTING HELPER

class PemeriksaanExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting // <-- 3. IMPLEMENT CONCERN BARU
{
    protected $request;
    private $rowNumber = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $pemeriksaansQuery = Pemeriksaan::query()
                            ->with('sasaran.organisasi.parent')
                            ->latest('tanggal_pemeriksaan');
        $request = $this->request;
        if ($request->filled('sub_organisasi_id')) {
            $pemeriksaansQuery->whereHas('sasaran', function ($q) use ($request) {
                $q->where('organisasi_id', $request->input('sub_organisasi_id'));
            });
        } elseif ($request->filled('organisasi_induk_id')) {
            $indukId = $request->organisasi_induk_id;
            $organisasiInduk = Organisasi::with('children')->find($indukId);
            if ($organisasiInduk) {
                $idsToFilter = $organisasiInduk->children->pluck('id')->push($organisasiInduk->id);
                $pemeriksaansQuery->whereHas('sasaran', function ($q) use ($idsToFilter) {
                    $q->whereIn('organisasi_id', $idsToFilter);
                });
            }
        }
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $pemeriksaansQuery->whereBetween('tanggal_pemeriksaan', [$request->input('tanggal_mulai'), $request->input('tanggal_selesai')]);
        }
        if ($request->filled('int_imt')) $pemeriksaansQuery->where('int_imt', $request->input('int_imt'));
        if ($request->filled('int_tensi')) $pemeriksaansQuery->where('int_tensi', $request->input('int_tensi'));
        if ($request->filled('int_gd')) $pemeriksaansQuery->where('int_gd', $request->input('int_gd'));
        if ($request->filled('int_koles')) $pemeriksaansQuery->where('int_koles', $request->input('int_koles'));
        if ($request->filled('int_asut')) $pemeriksaansQuery->where('int_asut', $request->input('int_asut'));
        return $pemeriksaansQuery;
    }

    // Method headings() tidak berubah
    public function headings(): array
    {
        return [
            'No', 'No. Registrasi', 'Nama Pasien', 'NIK', 'Tanggal Lahir', 'Gender', 'No. HP',
            'Organisasi Induk', 'Sub Organisasi', 'Tanggal Pemeriksaan', 'Usia Saat Periksa',
            'BB (kg)', 'TB (cm)', 'IMT', 'Status IMT', 'Lingkar Perut (cm)', 'Status LP',
            'Lingkar Lengan (cm)', 'Status LiLA', 'Tensi Sistolik', 'Tensi Diastolik', 'Status Tensi',
            'Gula Darah', 'Metode Gula Darah', 'Status Gula Darah', 'Asam Urat', 'Status Asam Urat',
            'Kolesterol', 'Status Kolesterol',
        ];
    }

    // Method map() tidak berubah
    public function map($pemeriksaan): array
    {
        // Dengan pendekatan ini, kita akan langsung mengakses relasi dari $pemeriksaan
        // dan menggunakan pengecekan untuk setiap data yang diambil.
        return [
            ++$this->rowNumber,

            // Data dari Sasaran (dengan pengecekan keamanan)
            $pemeriksaan->sasaran->nomor_registrasi ?? '-',
            $pemeriksaan->sasaran->nama_lengkap ?? 'DATA SASARAN DIHAPUS',
            "'" . ($pemeriksaan->sasaran ? str_replace(' ', '', $pemeriksaan->sasaran->nik) : '-'),
            ($pemeriksaan->sasaran && $pemeriksaan->sasaran->tgl_lahir) ? \Carbon\Carbon::parse($pemeriksaan->sasaran->tgl_lahir)->format('d-m-Y') : '-',
            $pemeriksaan->sasaran->gender ?? '-',
            "'" . ($pemeriksaan->sasaran ? str_replace(' ', '', $pemeriksaan->sasaran->no_hp) : '-'),
            
            // Data Organisasi (dengan pengecekan keamanan)
            ($pemeriksaan->sasaran && $pemeriksaan->sasaran->organisasi && $pemeriksaan->sasaran->organisasi->parent) ? $pemeriksaan->sasaran->organisasi->parent->nama_organisasi : ($pemeriksaan->sasaran->organisasi->nama_organisasi ?? '-'),
            ($pemeriksaan->sasaran && $pemeriksaan->sasaran->organisasi && $pemeriksaan->sasaran->organisasi->parent) ? $pemeriksaan->sasaran->organisasi->nama_organisasi : '-',
            
            // Data Pemeriksaan (langsung)
            \Carbon\Carbon::parse($pemeriksaan->tanggal_pemeriksaan)->format('d-m-Y'),
            $pemeriksaan->usia_saat_pemeriksaan,
            $pemeriksaan->bb,
            $pemeriksaan->tb,
            $pemeriksaan->imt,
            $pemeriksaan->int_imt,
            $pemeriksaan->lp,
            $pemeriksaan->int_lp,
            $pemeriksaan->lila,
            $pemeriksaan->int_lila,
            $pemeriksaan->tensi_sistolik,
            $pemeriksaan->tensi_diastolik,
            $pemeriksaan->int_tensi,
            $pemeriksaan->gd,
            $pemeriksaan->mgd,
            $pemeriksaan->int_gd,
            $pemeriksaan->asut,
            $pemeriksaan->int_asut,
            $pemeriksaan->koles,
            $pemeriksaan->int_koles,
        ];
    }

    /**
     * @return array
     */
    // --- 4. TAMBAHKAN METHOD BARU DI BAWAH INI ---
    public function columnFormats(): array
    {
        // Berdasarkan urutan di headings(), NIK adalah kolom ke-4 (D) dan No. HP adalah kolom ke-7 (G)
        return [
            'D' => NumberFormat::FORMAT_TEXT, // Kolom NIK
            'G' => NumberFormat::FORMAT_TEXT, // Kolom No. HP
        ];
    }
}