<?php

namespace App\Http\Controllers;

// Pastikan semua model yang Anda butuhkan sudah di-import
use App\Models\Sasaran;
use App\Models\Pemeriksaan;
use App\Models\Konsultasi;
use App\Models\User;
use App\Models\Organisasi; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Dashboard Pages Routs
     */
    public function index(Request $request)
    {
        // ================================================================
        // BAGIAN 1: PERSIAPAN FILTER & DATA AWAL
        // ================================================================
        $assets = ['chart', 'animation'];
        $user = Auth::user();

        $filterOrganisasiId = $request->input('organisasi_id');
        $filterTanggalMulai = $request->input('tanggal_mulai');
        $filterTanggalSelesai = $request->input('tanggal_selesai');

        // Ambil daftar organisasi HANYA jika admin, untuk mengisi dropdown filter
        $organisasiList = $user->hasRole('admin') ? Organisasi::orderBy('nama_organisasi')->get() : [];

        // ================================================================
        // BAGIAN 2: PENENTUAN ID ORGANISASI YANG AKAN DIAKSES (LOGIKA KUNCI)
        // ================================================================
        $accessibleOrganisasiIds = [];
        if ($user->hasRole('admin')) {
            if ($filterOrganisasiId) {
                // Jika admin memfilter, ambil ID induk dan SEMUA anak-anaknya.
                $accessibleOrganisasiIds = $this->getAllChildOrgIds($filterOrganisasiId);
                // Jangan lupa tambahkan ID induk itu sendiri ke dalam array.
                $accessibleOrganisasiIds[] = (int)$filterOrganisasiId;
            } else {
                // Jika admin tidak memfilter, maka dia bisa akses semua organisasi.
                $accessibleOrganisasiIds = Organisasi::pluck('id')->toArray();
            }
        } else {
            // Jika bukan admin, gunakan fungsi yang sudah ada di model User untuk mendapatkan organisasinya.
            $accessibleOrganisasiIds = $user->getAccessibleOrganisasiIds();
        }

        // ================================================================
        // BAGIAN 3: MEMBUAT QUERY DASAR YANG SUDAH TERFILTER
        // ================================================================

        // Query dasar untuk Sasaran (digunakan untuk data registrasi)
        $sasaranQuery = Sasaran::whereIn('organisasi_id', $accessibleOrganisasiIds)
            ->when($filterTanggalMulai && $filterTanggalSelesai, function ($q) use ($filterTanggalMulai, $filterTanggalSelesai) {
                return $q->whereBetween('created_at', [$filterTanggalMulai, $filterTanggalSelesai]);
            });

        // Query dasar untuk Pemeriksaan (digunakan untuk data medis dan chart)
        $pemeriksaanQuery = Pemeriksaan::whereHas('sasaran', function ($q) use ($accessibleOrganisasiIds) {
                $q->whereIn('organisasi_id', $accessibleOrganisasiIds);
            })
            ->when($filterTanggalMulai && $filterTanggalSelesai, function ($q) use ($filterTanggalMulai, $filterTanggalSelesai) {
                // Ganti 'tanggal_pemeriksaan' jika nama kolom tanggal di tabel pemeriksaans berbeda
                return $q->whereBetween('tanggal_pemeriksaan', [$filterTanggalMulai, $filterTanggalSelesai]);
            });

        // ================================================================
        // BAGIAN 4: EKSEKUSI QUERY & HITUNG SEMUA STATISTIK DARI HASILNYA
        // ================================================================
        $sasaransFiltered = $sasaranQuery->get();
        $pemeriksaansFiltered = $pemeriksaanQuery->with('konsultasis')->get(); // with('konsultasis') untuk efisiensi

        // --- KARTU-KARTU STATISTIK ---
        $totalSasaranGlobal = Sasaran::count();
        $jumlahSasaranDiregister = $sasaransFiltered->count();
        $persentaseSasaranDiregister = ($totalSasaranGlobal > 0) ? ($jumlahSasaranDiregister / $totalSasaranGlobal) * 100 : 0;
        
        $jumlahSasaranDiperiksa = $pemeriksaansFiltered->unique('sasaran_id')->count();
        $persentaseSasaranDiperiksa = ($jumlahSasaranDiregister > 0) ? ($jumlahSasaranDiperiksa / $jumlahSasaranDiregister) * 100 : 0;

        $jumlahSasaranKonsultasi = Sasaran::whereIn('id', $sasaransFiltered->pluck('id'))
                                           ->whereHas('pemeriksaans.konsultasis')
                                           ->count();
        $persentaseSasaranKonsultasi = ($jumlahSasaranDiregister > 0) ? ($jumlahSasaranKonsultasi / $jumlahSasaranDiregister) * 100 : 0;

        // --- DATA UNTUK SEMUA CHART ---
        $getChartData = function($column) use ($pemeriksaansFiltered) {
            return $pemeriksaansFiltered->whereNotNull($column)->where($column, '!=', '-')->groupBy($column)->map->count();
        };
        $imtData = $getChartData('int_imt');
        $tensiData = $getChartData('int_tensi');
        $kolesterolData = $getChartData('int_koles');
        $asamUratData = $getChartData('int_asut');
        $gulaDarahData = $getChartData('int_gd'); // <-- DATA BARU UNTUK GULA DARAH
        $chartColors = ['#3a57e8', '#4bc7d2', '#fd7e14', '#dc3545', '#6f42c1', '#ffc107'];

        // --- DATA BARU UNTUK CHART PERBANDINGAN PIE ---
        $comparisonChartLabels = ['Diregister', 'Diperiksa', 'Konsultasi'];
        $comparisonChartData = [$jumlahSasaranDiregister, $jumlahSasaranDiperiksa, $jumlahSasaranKonsultasi];

        // --- LOGIKA UNTUK KARTU RINGKASAN (PENGGUNA & DOKTER) ---
        // (Logika Anda sebelumnya)
        $cardKiriJudul = 'Total Pengguna';
        $cardKiriNilai = User::role(['user', 'koorUser'])->count();
        $cardKananJudul = 'Total Dokter';
        $cardKananNilai = User::role('dokter')->count();

        // --- LOGIKA UNTUK FEED AKTIVITAS TERBARU (Kini sudah terfilter) ---
        $aktivitasQueryIds = $user->hasRole('admin') && $filterOrganisasiId ? [$filterOrganisasiId] : $accessibleOrganisasiIds;
        
        $sasaranTerbaru = DB::table('sasarans')->select(DB::raw("CONCAT('Sasaran baru: ', nama_lengkap) as teks"), 'created_at as tanggal')->whereIn('organisasi_id', $aktivitasQueryIds);
        $pemeriksaanTerbaru = DB::table('pemeriksaans')->join('sasarans', 'pemeriksaans.sasaran_id', '=', 'sasarans.id')->select(DB::raw("CONCAT('Pemeriksaan untuk ', sasarans.nama_lengkap) as teks"), 'pemeriksaans.created_at as tanggal')->whereIn('sasarans.organisasi_id', $aktivitasQueryIds);
        $konsultasiTerbaru = DB::table('konsultasis')->join('pemeriksaans', 'konsultasis.pemeriksaan_id', '=', 'pemeriksaans.id')->join('sasarans', 'pemeriksaans.sasaran_id', '=', 'sasarans.id')->select(DB::raw("CONCAT('Konsultasi untuk ', sasarans.nama_lengkap) as teks"), 'konsultasis.created_at as tanggal')->whereIn('sasarans.organisasi_id', $aktivitasQueryIds);
        
        $aktivitasTerbaru = $sasaranTerbaru->unionAll($pemeriksaanTerbaru)->unionAll($konsultasiTerbaru)->orderBy('tanggal', 'desc')->limit(5)->get();

        // ================================================================
        // BAGIAN 5: KIRIM SEMUA DATA KE VIEW
        // ================================================================
        return view('dashboards.dashboard', [
            'assets' => $assets,
            'jumlahSasaranDiregister' => $jumlahSasaranDiregister, 'persentaseSasaranDiregister' => round($persentaseSasaranDiregister),
            'jumlahSasaranDiperiksa' => $jumlahSasaranDiperiksa, 'persentaseSasaranDiperiksa' => round($persentaseSasaranDiperiksa),
            'jumlahSasaranKonsultasi' => $jumlahSasaranKonsultasi, 'persentaseSasaranKonsultasi' => round($persentaseSasaranKonsultasi),
            'imtChartLabels' => $imtData->keys(), 'imtChartData' => $imtData->values(),
            'tensiChartLabels' => $tensiData->keys(), 'tensiChartData' => $tensiData->values(),
            'kolesterolChartLabels' => $kolesterolData->keys(), 'kolesterolChartData' => $kolesterolData->values(),
            'asamUratChartLabels' => $asamUratData->keys(), 'asamUratChartData' => $asamUratData->values(),
            'gulaDarahChartLabels' => $gulaDarahData->keys(), 'gulaDarahChartData' => $gulaDarahData->values(), // <-- KIRIM DATA GULA DARAH
            'chartColors' => $chartColors,
            'cardKiriJudul' => $cardKiriJudul, 'cardKiriNilai' => $cardKiriNilai,
            'cardKananJudul' => $cardKananJudul, 'cardKananNilai' => $cardKananNilai,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'comparisonChartLabels' => $comparisonChartLabels, // <-- KIRIM DATA PIE BARU
            'comparisonChartData' => $comparisonChartData,     // <-- KIRIM DATA PIE BARU

            // Variabel untuk filter
            'organisasiList' => $organisasiList,
            'filters' => $request->all()
        ]);
    }

    private function getAllChildOrgIds($parentId)
    {
        $allChildIds = [];
        // Ambil anak-anak langsung dari parentId
        $children = Organisasi::where('parent_id', $parentId)->pluck('id');

        // Untuk setiap anak, tambahkan ID-nya dan cari anak-anaknya lagi
        foreach ($children as $childId) {
            $allChildIds[] = $childId;
            $allChildIds = array_merge($allChildIds, $this->getAllChildOrgIds($childId));
        }
        return $allChildIds;
    }    

    /*
     * Menu Style Routs
     */
    public function horizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.horizontal', compact('assets'));
    }
    public function dualhorizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-horizontal', compact('assets'));
    }
    public function dualcompact(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-compact', compact('assets'));
    }
    public function boxed(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed', compact('assets'));
    }
    public function boxedfancy(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed-fancy', compact('assets'));
    }

    /*
     * Pages Routs
     */
    public function billing(Request $request)
    {
        return view('special-pages.billing');
    }

    public function calender(Request $request)
    {
        $assets = ['calender'];
        return view('special-pages.calender', compact('assets'));
    }

    public function kanban(Request $request)
    {
        return view('special-pages.kanban');
    }

    public function pricing(Request $request)
    {
        return view('special-pages.pricing');
    }

    public function rtlsupport(Request $request)
    {
        return view('special-pages.rtl-support');
    }

    public function timeline(Request $request)
    {
        return view('special-pages.timeline');
    }


    /*
     * Widget Routs
     */
    public function widgetbasic(Request $request)
    {
        return view('widget.widget-basic');
    }
    public function widgetchart(Request $request)
    {
        $assets = ['chart'];
        return view('widget.widget-chart', compact('assets'));
    }
    public function widgetcard(Request $request)
    {
        return view('widget.widget-card');
    }

    /*
     * Maps Routs
     */
    public function google(Request $request)
    {
        return view('maps.google');
    }
    public function vector(Request $request)
    {
        return view('maps.vector');
    }

    /*
     * Auth Routs
     */
    public function signin(Request $request)
    {
        return view('auth.login');
    }
    public function signup(Request $request)
    {
        return view('auth.register');
    }
    public function confirmmail(Request $request)
    {
        return view('auth.confirm-mail');
    }
    public function lockscreen(Request $request)
    {
        return view('auth.lockscreen');
    }
    public function recoverpw(Request $request)
    {
        return view('auth.recoverpw');
    }
    public function userprivacysetting(Request $request)
    {
        return view('auth.user-privacy-setting');
    }

    /*
     * Error Page Routs
     */

    public function error404(Request $request)
    {
        return view('errors.error404');
    }

    public function error500(Request $request)
    {
        return view('errors.error500');
    }
    public function maintenance(Request $request)
    {
        return view('errors.maintenance');
    }

    /*
     * uisheet Page Routs
     */
    public function uisheet(Request $request)
    {
        return view('uisheet');
    }

    /*
     * Form Page Routs
     */
    public function element(Request $request)
    {
        return view('forms.element');
    }

    public function wizard(Request $request)
    {
        return view('forms.wizard');
    }

    public function validation(Request $request)
    {
        return view('forms.validation');
    }

    /*
     * Table Page Routs
     */
    public function bootstraptable(Request $request)
    {
        return view('table.bootstraptable');
    }

    public function datatable(Request $request)
    {
        return view('table.datatable');
    }

    /*
     * Icons Page Routs
     */

    public function solid(Request $request)
    {
        return view('icons.solid');
    }

    public function outline(Request $request)
    {
        return view('icons.outline');
    }

    public function dualtone(Request $request)
    {
        return view('icons.dualtone');
    }

    public function colored(Request $request)
    {
        return view('icons.colored');
    }

    /*
     * Extra Page Routs
     */
    public function privacypolicy(Request $request)
    {
        return view('privacy-policy');
    }
    public function termsofuse(Request $request)
    {
        return view('terms-of-use');
    }
}