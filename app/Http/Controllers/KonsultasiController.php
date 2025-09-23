<?php

namespace App\Http\Controllers;

use App\Models\Sasaran;
use App\Models\Pemeriksaan;
use App\Models\Obat;
use App\Models\Konsultasi;
use App\Models\ResepObat;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KonsultasiController extends Controller
{
    /**
     * Menampilkan daftar sasaran (pasien) yang bisa dikonsultasikan oleh dokter.
     */
    public function index(Request $request)
    {
        $dokter = Auth::user();

        // Ambil ID semua organisasi yang bisa diakses oleh dokter ini
        $accessibleOrgIds = $dokter->getAccessibleOrganisasiIds();

        // Mulai query untuk mengambil data sasaran
        $sasaransQuery = Sasaran::query()
            // Hanya ambil sasaran dari organisasi yang bisa diakses
            ->whereIn('organisasi_id', $accessibleOrgIds)
            // Muat relasi yang dibutuhkan untuk ditampilkan di view agar efisien
            ->with(['organisasi', 'pemeriksaanTerakhir', 'konsultasiTerakhir.dokter', 'dokterYangSedangKonsultasi']);

        // Terapkan filter pencarian jika ada input 'search'
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $sasaransQuery->where(function ($query) use ($searchTerm) {
                $query->where('nama_lengkap', 'like', "%{$searchTerm}%")
                      ->orWhere('nomor_registrasi', 'like', "%{$searchTerm}%");
            });
        }

        // Ambil data dengan paginasi
        $sasarans = $sasaransQuery->orderBy('nama_lengkap', 'asc')->paginate(15);

        // Kirim data ke view
        return view('konsultasi.index', compact('sasarans'));
    }

    /**
     * Menampilkan form untuk membuat konsultasi baru.
     */
    public function create(Pemeriksaan $pemeriksaan)
    {
        // Eager load relasi yang dibutuhkan untuk ditampilkan di view
        $pemeriksaan->load('sasaran.organisasi');
        $sasaran = $pemeriksaan->sasaran;

        // --- Logika Penguncian Pasien ---
        $dokter = Auth::user();

        // Cek apakah pasien sudah dikunci oleh dokter lain dan kuncinya masih baru (kurang dari 30 menit)
        if (
            $sasaran->status_konsultasi == 'Sedang Konsultasi' &&
            $sasaran->konsultasi_oleh_id != $dokter->id &&
            Carbon::parse($sasaran->konsultasi_dimulai_pada)->diffInMinutes(now()) < 30
        ) {
            return redirect()->route('konsultasi.index')
                ->with('error', 'Pasien sedang dikonsultasikan oleh dokter lain.');
        }

        // Jika lolos pengecekan, kunci pasien untuk dokter ini
        $sasaran->update([
            'status_konsultasi' => 'Sedang Konsultasi',
            'konsultasi_oleh_id' => $dokter->id,
            'konsultasi_dimulai_pada' => now(),
        ]);

        return view('konsultasi.create', compact('pemeriksaan'));
    }

    /**
     * Menyimpan data konsultasi baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari form, termasuk validasi untuk array resep obat
        $validatedData = $request->validate([
            'pemeriksaan_id'    => 'required|exists:pemeriksaans,id',
            'keluhan'           => 'nullable|string',
            'diagnosa'          => 'required|string',
            'rekomendasi'       => 'required|string',
            'rekomendasi_obat'  => 'required|in:ya,tidak',
            
            // Aturan validasi untuk resep obat (hanya jika 'rekomendasi_obat' adalah 'ya')
            'obats'                     => 'required_if:rekomendasi_obat,ya|array|min:1',
            'obats.*.obat_id'           => 'required|exists:obats,id',
            'obats.*.qty'               => 'required|integer|min:1',
            'obats.*.keterangan_konsumsi' => 'required|string|max:255',
        ]);

        // Mulai transaksi database untuk memastikan semua data konsisten
        DB::beginTransaction();
        try {
            // 2. Simpan data konsultasi utama
            $konsultasi = Konsultasi::create([
                'pemeriksaan_id'    => $validatedData['pemeriksaan_id'],
                'dokter_id'         => Auth::id(),
                'keluhan'           => $validatedData['keluhan'],
                'diagnosa'          => $validatedData['diagnosa'],
                'rekomendasi'       => $validatedData['rekomendasi'],
                // Kita tidak lagi menyimpan resep obat sebagai teks tunggal di sini
            ]);

            // 3. Jika ada resep obat, simpan setiap item resep
            if ($request->rekomendasi_obat == 'ya' && isset($validatedData['obats'])) {
                foreach ($validatedData['obats'] as $resep) {
                    ResepObat::create([
                        'konsultasi_id'       => $konsultasi->id,
                        'obat_id'             => $resep['obat_id'],
                        'qty'                 => $resep['qty'],
                        'keterangan_konsumsi' => $resep['keterangan_konsumsi'],
                    ]);
                }
            }

            // 4. "Lepas Kunci" status pasien setelah semua proses berhasil
            $pemeriksaan = Pemeriksaan::find($validatedData['pemeriksaan_id']);
            if ($pemeriksaan && $pemeriksaan->sasaran) {
                $pemeriksaan->sasaran->update([
                    'status_konsultasi'     => 'Tersedia',
                    'konsultasi_oleh_id'    => null,
                    'konsultasi_dimulai_pada' => null,
                ]);
            }

            // Jika semua berhasil, konfirmasi transaksi
            DB::commit();

            // 5. Redirect kembali ke halaman daftar dengan pesan sukses
            return redirect()->route('konsultasi.index')
                ->with('success', 'Hasil konsultasi berhasil disimpan.');

        } catch (\Exception $e) {
            // Jika terjadi error di tengah jalan, batalkan semua query
            DB::rollBack();

            // Redirect kembali ke form dengan pesan error
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }

    /**
     * Mencari data obat untuk fitur autocomplete.
     */
    public function cariObat(Request $request)
    {
        // Ambil kata kunci pencarian dari request
        $searchTerm = $request->input('term');

        if (!$searchTerm) {
            return response()->json([]);
        }

        // Cari di database, di mana nama_obat mengandung kata kunci
        $obats = Obat::where('nama_obat', 'LIKE', "%{$searchTerm}%")
                    ->limit(10) // Batasi hasil agar tidak terlalu banyak
                    ->get(['id', 'nama_obat', 'satuan']); // Ambil kolom yang dibutuhkan

        return response()->json($obats);
    }    

    public function getHasilKonsultasi(Pemeriksaan $pemeriksaan)
    {
        // Eager load data dasar yang pasti ada
        $pemeriksaan->load('sasaran.organisasi.parent');

        // Ambil konsultasi terakhir yang terkait dengan pemeriksaan ini secara terpisah
        $konsultasiTerakhir = \App\Models\Konsultasi::where('pemeriksaan_id', $pemeriksaan->id)
                                    ->with(['dokter', 'resepObats.obat']) // Muat relasi dari konsultasi
                                    ->latest() // Urutkan berdasarkan yang terbaru
                                    ->first(); // Ambil hanya satu

        // Sekarang, kita akan gabungkan data secara manual ke dalam objek $pemeriksaan
        // Ini lebih aman daripada menggunakan sub-query yang kompleks di dalam ->load()
        $pemeriksaan->konsultasi_terakhir = $konsultasiTerakhir;
        
        // Kirim data sebagai JSON
        return response()->json($pemeriksaan);
    }

    public function cetak(Pemeriksaan $pemeriksaan)
    {
        // Muat semua relasi yang kita butuhkan
        $pemeriksaan->load('sasaran');

        $konsultasiTerakhir = \App\Models\Konsultasi::where('pemeriksaan_id', $pemeriksaan->id)
                                        ->with(['dokter', 'resepObats.obat'])
                                        ->latest()
                                        ->first();
        
        // Blok 'if abort(404)' sudah dihapus dari sini

        return view('konsultasi.cetak', [
            'pemeriksaan' => $pemeriksaan,
            'konsultasi' => $konsultasiTerakhir, // Variabel ini bisa bernilai null
        ]);
    }

}