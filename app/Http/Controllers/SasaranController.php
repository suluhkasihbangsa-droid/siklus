<?php

namespace App\Http\Controllers;

use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Sasaran;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SasaranController extends Controller
{
    /**
     * Menampilkan daftar data sasaran.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Cek jika user BUKAN admin DAN BELUM punya organisasi sama sekali.
        if (!$user->hasRole('admin') && $user->organisasis()->count() === 0) {
            // Kirim ke view dengan flag untuk menampilkan pesan peringatan.
            return view('sasaran.index', ['showWarning' => true]);
        }

        $sortBy = $request->input('sort_by', 'nama_lengkap');
        $sortDirection = $request->input('sort_direction', 'asc');

        $sortableColumns = ['nomor_registrasi', 'nama_lengkap', 'gender'];
        if (!in_array($sortBy, $sortableColumns)) {
            $sortBy = 'nama_lengkap';
        }

        $sasaransQuery = Sasaran::with(['organisasi.parent', 'pemeriksaanTerakhir', 'konsultasiTerakhir.dokter']);

        // Terapkan filter berdasarkan pencarian (KODE ANDA, TIDAK DIUBAH)
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $sasaransQuery->where(function ($query) use ($searchTerm) {
                $query->where('nama_lengkap', 'like', $searchTerm)
                        ->orWhere('nik', 'like', $searchTerm)
                        ->orWhere('nomor_registrasi', 'like', $searchTerm)
                        ->orWhere('id', 'like', $searchTerm)
                        ->orWhereHas('organisasi', function ($q) use ($searchTerm) {
                            $q->where('nama_organisasi', 'like', $searchTerm);
                        });
            });
        }

        // Terapkan filter berdasarkan organisasi (KODE ANDA, TIDAK DIUBAH)
        if ($user->hasRole('admin')) {
            if ($request->filled('organisasi_id')) {
                $sasaransQuery->where('organisasi_id', $request->organisasi_id);
            } elseif ($request->filled('organisasi_induk_id')) {
                $indukId = $request->organisasi_induk_id;
                $sasaransQuery->where(function ($query) use ($indukId) {
                    $query->where('organisasi_id', $indukId)
                            ->orWhereHas('organisasi', function ($q) use ($indukId) {
                                $q->where('parent_id', $indukId);
                            });
                });
            }
        } else {
            $accessibleOrganisasiIds = $user->getAccessibleOrganisasiIds();
            $sasaransQuery->whereIn('organisasi_id', $accessibleOrganisasiIds);
        }

        // Terapkan sorting ke query SEBELUM pagination.
        $sasaransQuery->orderBy($sortBy, $sortDirection);

        // Ambil data dengan pagination dan pastikan filter terbawa (KODE ANDA, TIDAK DIUBAH)
        $sasarans = $sasaransQuery->paginate(10)->withQueryString();

        // Ambil data untuk dropdown filter (KODE ANDA, TIDAK DIUBAH)
        $organisasi_induk_list = [];
        if ($user->hasRole('admin')) {
            $organisasi_induk_list = Organisasi::whereNull('parent_id')->orderBy('nama_organisasi', 'asc')->get();
        }

        // Tambahkan variabel sorting ke data yang dikirim ke view.
        return view('sasaran.index', compact(
            'sasarans', 
            'organisasi_induk_list', 
            'sortBy',               
            'sortDirection'         
        ));
    }

    /**
     * Menampilkan form untuk mengedit data sasaran.
     */
    public function edit(Sasaran $sasaran)
    {
        $user = Auth::user();
        
        // Cek otorisasi: hanya admin atau user yang dapat mengakses organisasi sasaran
        if (!$user->hasRole('admin') && !$user->canAccessOrganisasi($sasaran->organisasi_id)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data sasaran ini.');
        }

        // PENTING: Muat relasi organisasi beserta induknya (parent)
        $sasaran->load('organisasi.parent');

        $provinsis = Provinsi::all();
        
        // Ambil organisasi yang dapat diakses user
        if ($user->hasRole('admin')) {
            // Admin bisa melihat semua organisasi
            $organisasi_induk = Organisasi::whereNull('parent_id')->orderBy('nama_organisasi', 'asc')->get();
        } else {
            // User non-admin hanya bisa melihat organisasi parent yang ditugaskan
            $organisasi_induk = $user->organisasis()->whereNull('parent_id')->orderBy('nama_organisasi', 'asc')->get();
        }
        
        return view('sasaran.edit', compact('sasaran', 'provinsis', 'organisasi_induk'));
    }

    /**
     * Custom validation untuk nomor HP
     */
    private function getPhoneValidationRules()
    {
        return [
            'nullable',
            'string',
            // Validasi custom untuk memeriksa panjang digit setelah membersihkan spasi
            function ($attribute, $value, $fail) {
                if (empty($value)) {
                    return; // Lewati jika kosong
                }

                $cleanValue = preg_replace('/\s+/', '', $value);

                if (!ctype_digit($cleanValue)) {
                    $fail('Nomor HP hanya boleh berisi angka.');
                    return;
                }

                $digitLength = strlen($cleanValue);

                if ($digitLength < 10) {
                    $fail('Nomor HP minimal 10 digit.');
                } elseif ($digitLength > 13) {
                    $fail('Nomor HP maksimal 13 digit.');
                }
            },
        ];
    }

    /**
     * Membersihkan dan memformat nomor HP sebelum disimpan ke database.
     */
    private function formatPhoneNumber($phoneNumber)
    {
        if (empty($phoneNumber)) {
            return null;
        }
        
        // Bersihkan dari semua karakter non-digit dan batasi 13 digit
        $cleanPhone = substr(preg_replace('/\D/', '', $phoneNumber), 0, 13);
        
        // Kembalikan nomor bersih tanpa spasi untuk disimpan di DB
        return $cleanPhone;
    }

    /**
     * Memperbarui data sasaran di database.
     */
    public function update(Request $request, Sasaran $sasaran)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->canAccessOrganisasi($sasaran->organisasi_id)) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data sasaran ini.');
        }

        $organisasiValidation = 'required|exists:organisasis,id';
        if (!$user->hasRole('admin')) {
            $accessibleIds = $user->getAccessibleOrganisasiIds();
            $organisasiValidation .= '|in:' . implode(',', $accessibleIds);
        }

        // Validasi data input
        $validatedData = $request->validate([
            'nik'           => ['nullable', 'digits:16', Rule::unique('sasarans')->ignore($sasaran->id)], // DIUBAH jadi nullable
            'nama_lengkap'  => 'required|string|max:255',
            'tgl_lahir'     => 'required|date',
            'gender'        => 'required|in:L,P',
            'no_hp'         => $this->getPhoneValidationRules(), // MENGGUNAKAN FUNGSI BARU
            'provinsi_id'   => 'required|exists:provinsis,id',
            'kota_id'       => 'required|exists:kotas,id',
            'kecamatan_id'  => 'required|exists:kecamatans,id',
            'kelurahan_id'  => 'required|exists:kelurahans,id',
            'alamat_detail' => 'nullable|string',
            'organisasi_id' => $organisasiValidation,
        ]);
        
        // Proses data sebelum disimpan
        $validatedData['nama_lengkap'] = strtoupper($validatedData['nama_lengkap']);
        $validatedData['no_hp'] = $this->formatPhoneNumber($validatedData['no_hp']); // MENGGUNAKAN FUNGSI BARU

        // Update data di database
        $sasaran->update($validatedData);

        return redirect()->route('sasaran.index')->with('success', 'Data sasaran berhasil diperbarui!');
    }

    /**
     * Menampilkan form untuk membuat sasaran baru.
     */
    public function create()
    {
        $user = Auth::user();
        // Blokir akses jika user non-admin tidak punya organisasi
        if (!$user->hasRole('admin') && $user->organisasis()->count() === 0) {
            return redirect()->route('sasaran.index')->with('error', 'Anda tidak bisa menambah sasaran karena belum ditugaskan ke organisasi manapun.');
        }
        
        $provinsis = Provinsi::all();
        
        // Ambil organisasi berdasarkan role user
        if ($user->hasRole('admin')) {
            // Admin bisa melihat semua organisasi induk
            $organisasi_induk = Organisasi::whereNull('parent_id')->orderBy('nama_organisasi', 'asc')->get();
        } else {
            // User non-admin hanya bisa melihat organisasi parent yang ditugaskan kepadanya
            $organisasi_induk = $user->organisasis()->whereNull('parent_id')->orderBy('nama_organisasi', 'asc')->get();
        }
        
        return view('sasaran.create', [
            'provinsis' => $provinsis,
            'organisasi_induk' => $organisasi_induk
        ]);
    }

    /**
     * Menyimpan data sasaran baru ke database.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi tambahan untuk organisasi_id berdasarkan role
        $organisasiValidation = 'required|exists:organisasis,id';
        if (!$user->hasRole('admin')) {
            $accessibleIds = $user->getAccessibleOrganisasiIds();
            $organisasiValidation .= '|in:' . implode(',', $accessibleIds);
        }

        // 1. Validasi data input
        $validatedData = $request->validate([
            'nik'           => 'nullable|digits:16|unique:sasarans,nik',
            'nama_lengkap'  => 'required|string|max:255',
            'tgl_lahir'     => 'required|date',
            'gender'        => 'required|in:L,P',
            'no_hp'         => $this->getPhoneValidationRules(),
            'provinsi_id'   => 'required|exists:provinsis,id',
            'kota_id'       => 'required|exists:kotas,id',
            'kecamatan_id'  => 'required|exists:kecamatans,id',
            'kelurahan_id'  => 'required|exists:kelurahans,id',
            'alamat_detail' => 'nullable|string',
            'organisasi_id' => $organisasiValidation,
        ]);

        $sasaran = null; // Inisialisasi variabel di luar transaction

        // --- MULAI PERUBAHAN ---
        // Bungkus logika pembuatan data dalam sebuah transaction
        DB::transaction(function () use ($validatedData, &$sasaran) {
            // 2. Proses nama menjadi UPPERCASE
            $validatedData['nama_lengkap'] = strtoupper($validatedData['nama_lengkap']);

            // 2.5. Format nomor HP
            $validatedData['no_hp'] = $this->formatPhoneNumber($validatedData['no_hp']);

            // 3. Simpan data ke database
            // Pembuatan nomor registrasi di model akan berjalan di dalam 'safe zone' ini
            $sasaran = Sasaran::create($validatedData);
        });
        // --- SELESAI PERUBAHAN ---

        // Jika $sasaran masih null, berarti terjadi error di dalam transaction
        if (!$sasaran) {
            return redirect()->back()->with('error', 'Gagal menyimpan data. Silakan coba lagi.')->withInput();
        }

        $successMessage = "Data sasaran <strong>{$sasaran->nama_lengkap}</strong> berhasil ditambahkan dengan <strong>Nomor Registrasi: {$sasaran->nomor_registrasi}</strong>. Harap catat nomor registrasi ini untuk mempermudahkan pemeriksaan selanjutnya.";

        return redirect()->route('sasaran.index')
            ->with('success', 'Data berhasil disimpan!')
            ->with('popup_data', [
                'show' => true,
                'title' => 'Berhasil!',
                'message' => $successMessage,
                'nomor_registrasi' => $sasaran->nomor_registrasi
            ]);
    }

    /**
     * Menghapus data sasaran dari database.
     */
    public function destroy(Sasaran $sasaran)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if (!$user->hasRole('admin') && !$user->canAccessOrganisasi($sasaran->organisasi_id)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data sasaran ini.');
        }

        $sasaran->delete();
        return redirect()->route('sasaran.index')->with('success', 'Data sasaran berhasil dihapus.');
    }

    /**
     * Menampilkan halaman khusus untuk mencetak ID Card sasaran.
     */
    public function cetakId(Sasaran $sasaran)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if (!$user->hasRole('admin') && !$user->canAccessOrganisasi($sasaran->organisasi_id)) {
            abort(403, 'Anda tidak memiliki akses untuk mencetak ID sasaran ini.');
        }

        // Atur timezone ke Asia/Jakarta
        Carbon::setLocale('id');
        $tz = 'Asia/Jakarta';

        // Hitung usia saat ini
        $usia = Carbon::parse($sasaran->tgl_lahir, $tz)->diff(Carbon::now($tz))->format('%y tahun, %m bulan');

        // Ambil tanggal cetak hari ini
        $tanggalCetak = Carbon::now($tz)->translatedFormat('d F Y');

        return view('sasaran.cetak-id', compact('sasaran', 'usia', 'tanggalCetak'));
    }

    // Method API tetap sama
    public function getKota($provinsi_id)
    {
        $kotas = Kota::where('provinsi_id', $provinsi_id)->orderBy('nama_kota', 'asc')->get();
        return response()->json($kotas);
    }

    public function getKecamatan($kota_id)
    {
        $kecamatans = Kecamatan::where('kota_id', $kota_id)->orderBy('nama_kecamatan', 'asc')->get();
        return response()->json($kecamatans);
    }

    public function getKelurahan($kecamatan_id)
    {
        $kelurahans = Kelurahan::where('kecamatan_id', $kecamatan_id)->orderBy('nama_kelurahan', 'asc')->get();
        return response()->json($kelurahans);
    }

    public function getSubOrganisasi($parent_id)
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            // Admin bisa melihat semua sub organisasi
            $sub_organisasi = Organisasi::where('parent_id', $parent_id)
                                        ->orderBy('nama_organisasi', 'asc')
                                        ->get();
        } else {
            // User non-admin hanya bisa melihat sub organisasi yang dapat diakses
            $accessibleIds = $user->getAccessibleOrganisasiIds();
            $sub_organisasi = Organisasi::where('parent_id', $parent_id)
                                        ->whereIn('id', $accessibleIds)
                                        ->orderBy('nama_organisasi', 'asc')
                                        ->get();
        }
        
        return response()->json($sub_organisasi);
    }

    public function createFromQr()
    {
        $user = auth()->user();
        $provinsis = Provinsi::all();
        
        if ($user->hasRole('admin')) {
            $organisasi_induk = \App\Models\Organisasi::whereNull('parent_id')->orderBy('nama_organisasi', 'asc')->get();
        } else {
            $organisasi_induk = $user->organisasis()->whereNull('parent_id')->orderBy('nama_organisasi', 'asc')->get();
        }

        return view('sasaran.create_from_qr', compact('provinsis', 'organisasi_induk'));
    }
    
}