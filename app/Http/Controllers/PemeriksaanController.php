<?php

namespace App\Http\Controllers;

use App\Models\Sasaran;
use App\Models\Pemeriksaan;
use App\Models\Organisasi;
use App\Models\AturanInterpretasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\PemeriksaanExport;
use Maatwebsite\Excel\Facades\Excel;

class PemeriksaanController extends Controller
{
    /**
     * Menampilkan form untuk membuat data pemeriksaan baru.
     */
    public function create($sasaran_id = null)
    {
        $sasaran = null;
        if ($sasaran_id) {
            $sasaran = Sasaran::with('organisasi.parent')->findOrFail($sasaran_id);
        }
        
        $tanggalSekarang = Carbon::now()->format('Y-m-d');

        // FIX: Ambil daftar organisasi induk HANYA jika pengguna adalah admin
        $organisasi_induk_list = collect(); // Default ke koleksi kosong
        if (Auth::user()->hasRole('admin')) {
            $organisasi_induk_list = Organisasi::whereNull('parent_id')->orderBy('nama_organisasi', 'asc')->get();
        }

        // FIX: Kirim variabel baru ke view
        return view('pemeriksaan.create', compact('sasaran', 'tanggalSekarang', 'organisasi_induk_list'));
    }

    /**
     * Menyimpan data pemeriksaan baru ke database.
     */
    public function store(Request $request)
    {
        // FIX: Mengubah aturan validasi dari 'decimal' menjadi 'numeric' yang lebih umum
        $validatedData = $request->validate([
            'sasaran_id' => 'required|exists:sasarans,id',
            'tanggal_pemeriksaan' => 'required|date',
            'bb' => 'required|numeric|min:0', // Menggunakan numeric
            'tb' => 'required|numeric|min:0', // Menggunakan numeric
            'lp' => 'nullable|numeric|min:0', // Menggunakan numeric
            'lila' => 'nullable|numeric|min:0', // Menggunakan numeric
            'tensi_sistolik' => 'nullable|integer|min:0',
            'tensi_diastolik' => 'nullable|integer|min:0',
            'keluhan_awal' => 'nullable|string',
            'gd' => 'nullable|integer|min:0',
            'mgd' => 'nullable|required_with:gd|string|in:S,P',
            'asut' => 'nullable|numeric|min:0', // Menggunakan numeric
            'koles' => 'nullable|integer|min:0',
        ]);

        $dataToStore = $validatedData;

        // Normalisasi input koma menjadi titik sebelum disimpan
        $decimalFields = ['bb', 'tb', 'lp', 'lila', 'asut'];
        foreach($decimalFields as $field) {
            if(isset($dataToStore[$field])) {
                $dataToStore[$field] = str_replace(',', '.', $dataToStore[$field]);
            }
        }
        
        DB::beginTransaction();
        try {
            $sasaran = Sasaran::findOrFail($dataToStore['sasaran_id']);
            $tanggalPemeriksaan = Carbon::parse($dataToStore['tanggal_pemeriksaan']);
            $tanggalLahir = Carbon::parse($sasaran->tgl_lahir);
            $usiaBulan = $tanggalLahir->diffInMonths($tanggalPemeriksaan);

            $tbInMeter = $dataToStore['tb'] / 100;
            $imt = ($tbInMeter > 0) ? ($dataToStore['bb'] / ($tbInMeter * $tbInMeter)) : 0;

            $interpretasi = $this->getAllInterpretations(
                $imt, $dataToStore, $usiaBulan, $sasaran->gender
            );

            $dataToStore = array_merge($dataToStore, $interpretasi, [
                'imt' => round($imt, 2),
                'usia_saat_pemeriksaan' => $this->formatUsia($tanggalLahir, $tanggalPemeriksaan),
            ]);

            Pemeriksaan::create($dataToStore);
            DB::commit();

            return redirect()->route('sasaran.index')->with('success', 'Data pemeriksaan untuk ' . $sasaran->nama_lengkap . ' berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function ajaxGetInterpretasi(Request $request)
    {
        $sasaran = Sasaran::find($request->input('sasaran_id'));
        if (!$sasaran) {
            return response()->json(['error' => 'Sasaran tidak ditemukan'], 404);
        }

        $tanggalPemeriksaan = Carbon::parse($request->input('tanggal_pemeriksaan'));
        $usiaBulan = Carbon::parse($sasaran->tgl_lahir)->diffInMonths($tanggalPemeriksaan);
        $imt = $request->input('imt', 0);

        $interpretasi = $this->getAllInterpretations(
            $imt, $request->all(), $usiaBulan, $sasaran->gender
        );

        return response()->json($interpretasi);
    }
    
    /**
     * "Mesin" utama untuk mendapatkan semua interpretasi berdasarkan input.
     */
    private function getAllInterpretations($imt, $data, $usiaBulan, $gender)
    {
        $hasil = [];
        $hasil['int_imt'] = $this->getInterpretasi('IMT', $imt, $usiaBulan, $gender);
        $hasil['int_lp'] = $this->getInterpretasi('LP', $data['lp'] ?? null, $usiaBulan, $gender);
        $hasil['int_lila'] = $this->getInterpretasiLila($data['lila'] ?? null, $usiaBulan, $gender);
        $hasil['int_tensi'] = $this->getInterpretasiTensi($data['tensi_sistolik'] ?? null, $data['tensi_diastolik'] ?? null, $usiaBulan);
        $hasil['int_gd'] = $this->getInterpretasi('GULA_DARAH', $data['gd'] ?? null, $usiaBulan, null, $data['mgd'] ?? null);
        $hasil['int_asut'] = $this->getInterpretasi('ASAM_URAT', $data['asut'] ?? null, $usiaBulan, $gender);
        $hasil['int_koles'] = $this->getInterpretasi('KOLESTEROL', $data['koles'] ?? null, $usiaBulan, $gender);
        return $hasil;
    }

    /**
     * Fungsi generik untuk mencari aturan interpretasi.
     */
    private function getInterpretasi($kategori, $nilai, $usiaBulan, $gender = null, $metode = null)
    {
        if (is_null($nilai) || $nilai === '') return '-';

        $query = AturanInterpretasi::where('kategori', $kategori)
            ->where(function ($q) use ($usiaBulan) {
                $q->where('kondisi_usia_min_bulan', '<=', $usiaBulan)
                  ->orWhereNull('kondisi_usia_min_bulan');
            })
            ->where(function ($q) use ($usiaBulan) {
                $q->where('kondisi_usia_max_bulan', '>=', $usiaBulan)
                  ->orWhereNull('kondisi_usia_max_bulan');
            })
            ->where(function ($q) use ($gender) {
                $q->where('kondisi_gender', $gender)
                  ->orWhereNull('kondisi_gender');
            });
        
        if ($metode) {
            $query->where('kondisi_metode', $metode);
        }
        
        $aturan = $query->get();

        foreach ($aturan as $rule) {
            $cocok = false;
            if (!is_null($rule->batas_bawah) && !is_null($rule->batas_atas)) {
                if ($nilai >= $rule->batas_bawah && $nilai <= $rule->batas_atas) $cocok = true;
            } elseif (!is_null($rule->batas_bawah)) {
                if ($nilai >= $rule->batas_bawah) $cocok = true;
            } elseif (!is_null($rule->batas_atas)) {
                if ($nilai <= $rule->batas_atas) $cocok = true;
            }
            if ($cocok) return $rule->nama_interpretasi;
        }

        return 'Tidak Terdefinisi';
    }
    
    // Fungsi spesifik karena logikanya berbeda
    private function getInterpretasiLila($nilai, $usiaBulan, $gender)
    {
        if (is_null($nilai) || $nilai === '') return '-';
        if ($usiaBulan >= 6 && $usiaBulan <= 59) {
            return $this->getInterpretasi('LILA_BALITA', $nilai, $usiaBulan);
        }
        if ($gender == 'P' && $usiaBulan >= 180 && $usiaBulan <= 599) {
            return $this->getInterpretasi('LILA_DEWASA', $nilai, $usiaBulan, 'P');
        }
        return '-';
    }

    private function getInterpretasiTensi($sistolik, $diastolik, $usiaBulan)
    {
        if (is_null($sistolik) || is_null($diastolik) || $sistolik === '' || $diastolik === '') return '-';
        if ($usiaBulan < 180) return 'N/A';
        
        // 1. Ambil SEMUA aturan untuk kategori TENSI sekaligus (lebih efisien)
        $aturanTensi = AturanInterpretasi::where('kategori', 'TENSI')->get();

        // 2. Cari aturan spesifik untuk 'Normal' dan 'Hipertensi' dari koleksi
        $aturanNormal = $aturanTensi->firstWhere('kode_interpretasi', 'N');
        $aturanHipertensi = $aturanTensi->firstWhere('kode_interpretasi', 'H');

        // 3. Pastikan kedua aturan ada di database untuk menghindari error
        if (!$aturanNormal || !$aturanHipertensi) {
            return 'Aturan Tensi Tidak Lengkap'; // Pesan fallback jika salah satu aturan tidak ada
        }

        // 4. Jalankan logika perbandingan seperti biasa
        if ($sistolik <= $aturanNormal->batas_sistolik && $diastolik <= $aturanNormal->batas_diastolik) {
            // Kembalikan nama interpretasi "Normal" dari database
            return $aturanNormal->nama_interpretasi; 
        }
        
        // 5. Kembalikan nama interpretasi "Hipertensi" dari database jika tidak normal
        return $aturanHipertensi->nama_interpretasi; 
    } 

    private function formatUsia(Carbon $tglLahir, Carbon $tglPemeriksaan)
    {
        $usiaBulan = $tglLahir->diffInMonths($tglPemeriksaan);
        $years = $tglLahir->diffInYears($tglPemeriksaan);
        $months = $tglPemeriksaan->copy()->subYears($years)->diffInMonths($tglLahir);
        if ($usiaBulan < 60) return "{$usiaBulan} bulan";
        if ($usiaBulan < (19 * 12)) return "{$years} tahun {$months} bulan";
        return "{$years} tahun";
    }

    /**
     * Mencari data sasaran untuk fitur autocomplete di form pemeriksaan.
     */
    public function searchSasaran(Request $request)
    {
        $user = Auth::user();
        $searchTerm = $request->get('q');
        $organisasiIndukId = $request->get('org_id'); // Terima ID organisasi dari filter

        if (!$searchTerm) {
            return response()->json([]);
        }

        $query = Sasaran::query()
            ->select('id', 'nomor_registrasi', 'nama_lengkap', 'tgl_lahir', 'gender')
            ->where(function ($q) use ($searchTerm) {
                $q->where('nama_lengkap', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('nomor_registrasi', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('nik', 'LIKE', "%{$searchTerm}%");
            });

        // Terapkan filter organisasi berdasarkan peran
        if ($user->hasRole('admin')) {
            // Jika admin memilih filter organisasi
            if ($organisasiIndukId) {
                $organisasi = Organisasi::with('children')->find($organisasiIndukId);
                if ($organisasi) {
                    $idsToFilter = $organisasi->children->pluck('id')->push($organisasi->id);
                    $query->whereIn('organisasi_id', $idsToFilter);
                }
            }
        } else {
            // Untuk non-admin, filter berdasarkan organisasi yang bisa mereka akses
            $accessibleOrgIds = $user->getAccessibleOrganisasiIds();
            $query->whereIn('organisasi_id', $accessibleOrgIds);
        }

        $sasarans = $query->limit(10)->get();

        return response()->json($sasarans);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isFiltered = false;
        
        // Logika untuk mengambil daftar organisasi (tidak berubah)
        if ($user->hasRole('admin')) {
            $organisasi_induk_list = Organisasi::whereNull('parent_id')
                ->orderBy('nama_organisasi', 'asc')
                ->get();
            
            $pemeriksaansQuery = Pemeriksaan::whereRaw('0 = 1'); 

        } else {
            // ... (Logika untuk non-admin tidak berubah)
            $assignedOrgs = $user->organisasis;
            $parentOrgIds = [];
            foreach ($assignedOrgs as $org) {
                if (is_null($org->parent_id)) {
                    $parentOrgIds[] = $org->id;
                } else {
                    $parentOrgIds[] = $org->parent_id;
                }
            }
            $organisasi_induk_list = Organisasi::whereIn('id', array_unique($parentOrgIds))
                ->orderBy('nama_organisasi', 'asc')
                ->get();

            $pemeriksaansQuery = Pemeriksaan::with('sasaran.organisasi.parent');
            $accessibleOrgIds = $user->getAccessibleOrganisasiIds();
            
            if (!empty($accessibleOrgIds)) {
                $pemeriksaansQuery->whereHas('sasaran', function ($q) use ($accessibleOrgIds) {
                    $q->whereIn('organisasi_id', $accessibleOrgIds);
                });
            } else {
                $pemeriksaansQuery->whereRaw('0 = 1');
            }
        }

        $pemeriksaans = $pemeriksaansQuery->latest('tanggal_pemeriksaan')->paginate(15);

        // Data untuk dropdown filter lanjutan (tidak berubah)
        $filter_options = [
            'imt' => AturanInterpretasi::where('kategori', 'IMT')->distinct()->pluck('nama_interpretasi'),
            'tensi' => ['Normal', 'Hipertensi'],
            'gula' => AturanInterpretasi::where('kategori', 'GULA_DARAH')->distinct()->pluck('nama_interpretasi'),
            'kolesterol' => AturanInterpretasi::where('kategori', 'KOLESTEROL')->distinct()->pluck('nama_interpretasi'),
            'asut' => AturanInterpretasi::where('kategori', 'ASAM_URAT')->distinct()->pluck('nama_interpretasi'),
        ];

        // Mengambil "Peta Warna" (tidak berubah)
        $semuaAturan = AturanInterpretasi::all();
        $warnaAturan = $semuaAturan->groupBy('kategori')->map(function ($group) {
            return $group->pluck('warna_badge', 'nama_interpretasi');
        });

        // ===============================================
        // PERBAIKAN: Kirim SEMUA variabel yang dibutuhkan ke view
        // ===============================================
        return view('pemeriksaan.index', compact(
            'pemeriksaans', 
            'organisasi_induk_list', 
            'filter_options', 
            'isFiltered', 
            'warnaAturan' // <-- Variabel ini sebelumnya tidak terkirim
        ));
    }

    public function filter(Request $request)
    {
        $user = Auth::user();
        $pemeriksaansQuery = Pemeriksaan::with('sasaran.organisasi.parent');

        // Sebelum menerapkan filter apa pun, batasi dulu data berdasarkan hak akses
        if (!$user->hasRole('admin')) {
            $accessibleOrgIds = $user->getAccessibleOrganisasiIds();

            // Jika user tidak punya akses ke organisasi mana pun, pastikan query tidak mengembalikan apa-apa
            if (empty($accessibleOrgIds)) {
                $pemeriksaansQuery->whereRaw('0 = 1'); 
            } else {
                $pemeriksaansQuery->whereHas('sasaran', function ($q) use ($accessibleOrgIds) {
                    $q->whereIn('organisasi_id', $accessibleOrgIds);
                });
            }
        }    
        
        if ($request->filled('search_term')) {
            $searchTerm = $request->input('search_term');
            
            $pemeriksaansQuery->whereHas('sasaran', function ($q) use ($searchTerm) {
                $q->where(function ($subQuery) use ($searchTerm) {
                    // Cari berdasarkan nama lengkap
                    $subQuery->where('nama_lengkap', 'LIKE', "%{$searchTerm}%")
                             // ATAU cari berdasarkan nomor registrasi (mengabaikan awalan)
                             ->orWhere('nomor_registrasi', 'LIKE', "%{$searchTerm}%");
                });
            });
        }    

        // Hirarki Filter Organisasi: Prioritaskan Sub-Organisasi jika dipilih
        if ($request->filled('sub_organisasi_id')) {
            // Jika anak dipilih, hanya filter berdasarkan anak
            $pemeriksaansQuery->whereHas('sasaran', function ($q) use ($request) {
                $q->where('organisasi_id', $request->input('sub_organisasi_id'));
            });
        } elseif ($request->filled('organisasi_induk_id')) {
            // Jika hanya induk yang dipilih, filter berdasarkan induk dan semua anaknya
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
            $pemeriksaansQuery->whereBetween('tanggal_pemeriksaan', [
                $request->input('tanggal_mulai'), 
                $request->input('tanggal_selesai')
            ]);
        }    

        // Filter Lanjutan
        if ($request->filled('int_imt')) {
            $pemeriksaansQuery->where('int_imt', $request->input('int_imt'));
        }
        if ($request->filled('int_tensi')) {
            $pemeriksaansQuery->where('int_tensi', $request->input('int_tensi'));
        }
        if ($request->filled('int_gd')) {
            $pemeriksaansQuery->where('int_gd', $request->input('int_gd'));
        }
        if ($request->filled('int_koles')) {
            $pemeriksaansQuery->where('int_koles', $request->input('int_koles'));
        }
        if ($request->filled('int_asut')) {
            $pemeriksaansQuery->where('int_asut', $request->input('int_asut'));
        }

        $semuaAturan = AturanInterpretasi::all();
        $warnaAturan = $semuaAturan->groupBy('kategori')->map(function ($group) {
            return $group->pluck('warna_badge', 'nama_interpretasi');
        });

        $pemeriksaans = $pemeriksaansQuery->latest('tanggal_pemeriksaan')
            ->paginate(15)
            ->appends($request->all());
        
        $isFiltered = true; 

        return response()->json([
            'success' => true,
            // Kirim variabel '$warnaAturan' yang baru ke partial
            'html' => view('pemeriksaan.partials.table', compact('pemeriksaans', 'isFiltered', 'warnaAturan'))->render(),
            'pagination' => $pemeriksaans->links()->render()
        ]);
    }

    private function getAllChildOrgIds($parentId)
    {
        $ids = collect([$parentId]);
        $children = Organisasi::where('parent_id', $parentId)->pluck('id');

        foreach ($children as $childId) {
            $ids = $ids->merge($this->getAllChildOrgIds($childId));
        }

        return $ids;
    }

    public function exportExcel(Request $request)
    {
        $tanggal = now()->format('Y-m-d');
        $namaFile = 'laporan-pemeriksaan-' . $tanggal . '.xlsx';

        return Excel::download(new PemeriksaanExport($request), $namaFile);
    }

    public function edit(Pemeriksaan $pemeriksaan)
    {
        // Kita akan gunakan view 'create' yang sama, karena form-nya identik
        // Kita hanya perlu mengirim data pemeriksaan yang akan diedit
        $pemeriksaan->load('sasaran.organisasi'); // Eager load data sasaran

        // Kita buat variabel $sasaran agar kompatibel dengan view create
        $sasaran = $pemeriksaan->sasaran;

        return view('pemeriksaan.create', compact('pemeriksaan', 'sasaran'));
    }

    /**
     * Memperbarui data pemeriksaan di database.
     */
    public function update(Request $request, Pemeriksaan $pemeriksaan)
    {
        // Validasi bisa disamakan dengan method store, namun sasaran_id tidak wajib
        $validatedData = $request->validate([
            'tanggal_pemeriksaan' => 'required|date',
            'bb' => 'required|numeric|min:0', // Menggunakan numeric
            'tb' => 'required|numeric|min:0', // Menggunakan numeric
            'lp' => 'nullable|numeric|min:0', // Menggunakan numeric
            'lila' => 'nullable|numeric|min:0', // Menggunakan numeric
            'tensi_sistolik' => 'nullable|integer|min:0',
            'tensi_diastolik' => 'nullable|integer|min:0',
            'keluhan_awal' => 'nullable|string',
            'gd' => 'nullable|integer|min:0',
            'mgd' => 'nullable|required_with:gd|string|in:S,P',
            'asut' => 'nullable|numeric|min:0', // Menggunakan numeric
            'koles' => 'nullable|integer|min:0',            
        ]);

        // Karena sasaran tidak berubah, kita ambil dari data yang sudah ada
        $validatedData['sasaran_id'] = $pemeriksaan->sasaran_id;

        DB::beginTransaction();
        try {
            // Logika kalkulasi IMT, Usia, dan Interpretasi di-copy dari method store
            $sasaran = $pemeriksaan->sasaran;
            $tanggalPemeriksaan = Carbon::parse($validatedData['tanggal_pemeriksaan']);
            $tanggalLahir = Carbon::parse($sasaran->tgl_lahir);
            $usiaBulan = $tanggalLahir->diffInMonths($tanggalPemeriksaan);

            $tbInMeter = $validatedData['tb'] / 100;
            $imt = ($tbInMeter > 0) ? ($validatedData['bb'] / ($tbInMeter * $tbInMeter)) : 0;

            $interpretasi = $this->getAllInterpretations(
                $imt, $validatedData, $usiaBulan, $sasaran->gender
            );
            
            $dataToUpdate = array_merge($validatedData, $interpretasi, [
                'imt' => round($imt, 2),
                'usia_saat_pemeriksaan' => $this->formatUsia($tanggalLahir, $tanggalPemeriksaan),
            ]);

            $pemeriksaan->update($dataToUpdate);
            DB::commit();

            return redirect()->route('pemeriksaan.index')->with('success', 'Data pemeriksaan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data pemeriksaan dari database.
     */
    public function destroy(Pemeriksaan $pemeriksaan)
    {
        try {
            $pemeriksaan->delete();
            return redirect()->route('pemeriksaan.index')->with('success', 'Data pemeriksaan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('pemeriksaan.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

}