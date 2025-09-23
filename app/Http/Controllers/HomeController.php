<?php

namespace App\Http\Controllers;

use App\Models\Sasaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemeriksaan;
use App\Models\Konsultasi;
use Illuminate\Support\Facades\DB;
use App\Models\User; 

class HomeController extends Controller
{

    /*
     * Dashboard Pages Routs
     */
    public function index(Request $request)
    {
        // 1. Aset asli Anda tetap dipertahankan
        $assets = ['chart', 'animation'];

        // 2. Ambil data user yang login
        $user = Auth::user();

        // ================================================================
        // LOGIKA UNTUK KARTU 1: DIREGISTER
        // ================================================================
        $totalSasaran = Sasaran::count();
        $jumlahSasaranDiregister = 0;
        $persentaseSasaranDiregister = 0;
        $accessibleOrganisasiIds = [];
        $totalSasaranOrganisasi = 0;

        if ($user->hasRole('admin')) {
            $jumlahSasaranDiregister = $totalSasaran;
            $persentaseSasaranDiregister = 100;
        } else {
            $accessibleOrganisasiIds = $user->getAccessibleOrganisasiIds();
            $totalSasaranOrganisasi = Sasaran::whereIn('organisasi_id', $accessibleOrganisasiIds)->count();
            $jumlahSasaranDiregister = $totalSasaranOrganisasi;

            if ($totalSasaran > 0) {
                $persentaseSasaranDiregister = ($jumlahSasaranDiregister / $totalSasaran) * 100;
            }
        }

        // ================================================================
        // LOGIKA UNTUK KARTU 2: DIPERIKSA
        // ================================================================
        $jumlahSasaranDiperiksa = 0;
        $persentaseSasaranDiperiksa = 0;

        if ($user->hasRole('admin')) {
            $jumlahSasaranDiperiksa = Pemeriksaan::distinct('sasaran_id')->count('sasaran_id');
            if ($totalSasaran > 0) {
                $persentaseSasaranDiperiksa = ($jumlahSasaranDiperiksa / $totalSasaran) * 100;
            }
        } else {
            $jumlahSasaranDiperiksa = Pemeriksaan::whereHas('sasaran', function ($query) use ($accessibleOrganisasiIds) {
                $query->whereIn('organisasi_id', $accessibleOrganisasiIds);
            })->distinct('sasaran_id')->count('sasaran_id');

            if ($totalSasaranOrganisasi > 0) {
                $persentaseSasaranDiperiksa = ($jumlahSasaranDiperiksa / $totalSasaranOrganisasi) * 100;
            }
        }

        // ================================================================
        // LOGIKA UNTUK KARTU 3: KONSULTASI
        // ================================================================
        $jumlahSasaranKonsultasi = 0;
        $persentaseSasaranKonsultasi = 0;

        if ($user->hasRole('admin')) {
            // Admin: Hitung sasaran unik yang memiliki konsultasi
            // FIX: Mengubah 'konsultasi' menjadi 'konsultasis' (plural)
            $jumlahSasaranKonsultasi = Sasaran::whereHas('pemeriksaans.konsultasis')->count();
            if ($totalSasaran > 0) {
                $persentaseSasaranKonsultasi = ($jumlahSasaranKonsultasi / $totalSasaran) * 100;
            }
        } else {
            // Non-Admin: Hitung sasaran unik yang memiliki konsultasi dari organisasi yang bisa diakses
            // FIX: Mengubah 'konsultasi' menjadi 'konsultasis' (plural)
            $jumlahSasaranKonsultasi = Sasaran::whereIn('organisasi_id', $accessibleOrganisasiIds)
                                              ->whereHas('pemeriksaans.konsultasis')
                                              ->count();
            if ($totalSasaranOrganisasi > 0) {
                $persentaseSasaranKonsultasi = ($jumlahSasaranKonsultasi / $totalSasaranOrganisasi) * 100;
            }
        }

        // ================================================================
        // FUNGSI BANTU UNTUK MENGAMBIL DATA CHART (AGAR TIDAK REPETITIF)
        // ================================================================
        $getChartData = function ($column) use ($user, $accessibleOrganisasiIds) {
            $query = Pemeriksaan::select($column, DB::raw('count(*) as total'))
                ->whereNotNull($column)
                ->where($column, '!=', '-')
                ->groupBy($column);

            if (!$user->hasRole('admin')) {
                $query->whereHas('sasaran', function ($q) use ($accessibleOrganisasiIds) {
                    $q->whereIn('organisasi_id', $accessibleOrganisasiIds);
                });
            }
            return $query->pluck('total', $column);
        };

        // ================================================================
        // AMBIL DATA UNTUK SEMUA CHART
        // ================================================================
        // 1. IMT
        $imtData = $getChartData('int_imt');
        $imtChartLabels = $imtData->keys();
        $imtChartData = $imtData->values();

        // 2. Tensi
        $tensiData = $getChartData('int_tensi');
        $tensiChartLabels = $tensiData->keys();
        $tensiChartData = $tensiData->values();

        // 3. Kolesterol
        $kolesterolData = $getChartData('int_koles');
        $kolesterolChartLabels = $kolesterolData->keys();
        $kolesterolChartData = $kolesterolData->values();

        // 4. Asam Urat
        $asamUratData = $getChartData('int_asut');
        $asamUratChartLabels = $asamUratData->keys();
        $asamUratChartData = $asamUratData->values();

        // Siapkan warna untuk chart
        $chartColors = ['#3a57e8', '#4bc7d2', '#fd7e14', '#dc3545', '#6f42c1'];

        // ================================================================
        // LOGIKA UNTUK KARTU RINGKASAN (PENGGUNA & DOKTER)
        // ================================================================
        $cardKiriJudul = '';
        $cardKiriNilai = 0;
        $cardKananJudul = '';
        $cardKananNilai = 0;

        // Logika untuk Admin, User, dan KoorUser
        if ($user->hasRole('admin')) {
            $cardKiriJudul = 'Total Pengguna';
            // Menggunakan Spatie/Permission untuk menghitung user berdasarkan peran
            $cardKiriNilai = User::role(['user', 'koorUser'])->count();
            
            $cardKananJudul = 'Total Dokter';
            $cardKananNilai = User::role('dokter')->count();

        } elseif ($user->hasRole(['user', 'koorUser'])) {
            $cardKiriJudul = 'Pengguna di Organisasi';
            // Menghitung user/koorUser yang terhubung dengan organisasi yang bisa diakses
            $cardKiriNilai = User::role(['user', 'koorUser'])
                ->whereHas('organisasis', function ($query) use ($accessibleOrganisasiIds) {
                    $query->whereIn('organisasi_id', $accessibleOrganisasiIds);
                })->count();

            $cardKananJudul = 'Dokter di Organisasi';
            // Menghitung dokter yang terhubung dengan organisasi yang bisa diakses
            $cardKananNilai = User::role('dokter')
                ->whereHas('organisasis', function ($query) use ($accessibleOrganisasiIds) {
                    $query->whereIn('organisasi_id', $accessibleOrganisasiIds);
                })->count();

        } elseif ($user->hasRole('dokter')) {
            $cardKiriJudul = 'Siap Konsultasi';
            // FIX: Menghitung sasaran yang SUDAH diperiksa TAPI BELUM dikonsultasi
            $cardKiriNilai = Sasaran::whereIn('organisasi_id', $accessibleOrganisasiIds)
                ->whereHas('pemeriksaans') // <-- Kondisi 1: Sudah punya data pemeriksaan
                ->whereDoesntHave('pemeriksaans.konsultasis') // <-- Kondisi 2: Tapi belum punya data konsultasi
                ->count();

            // SARAN: Judul diubah agar lebih sesuai dengan data
            $cardKananJudul = 'Sudah Konsultasi'; 
            // FIX: Menghitung sasaran yang SUDAH mempunyai konsultasi
            $cardKananNilai = Sasaran::whereIn('organisasi_id', $accessibleOrganisasiIds)
                ->whereHas('pemeriksaans.konsultasis')
                ->count();
        }

        // ================================================================
        // LOGIKA UNTUK FEED AKTIVITAS TERBARU
        // ================================================================
        // Query 1: Mengambil 5 sasaran terbaru
        $sasaranTerbaru = DB::table('sasarans')
            ->join('organisasis', 'sasarans.organisasi_id', '=', 'organisasis.id')
            ->select(
                DB::raw("CONCAT('Sasaran baru: ', sasarans.nama_lengkap) as teks"),
                'sasarans.created_at as tanggal',
                DB::raw("'sasaran' as tipe")
            );

        // Query 2: Mengambil 5 pemeriksaan terbaru
        $pemeriksaanTerbaru = DB::table('pemeriksaans')
            ->join('sasarans', 'pemeriksaans.sasaran_id', '=', 'sasarans.id')
            ->select(
                DB::raw("CONCAT('Pemeriksaan untuk ', sasarans.nama_lengkap) as teks"),
                'pemeriksaans.created_at as tanggal',
                DB::raw("'pemeriksaan' as tipe")
            );

        // Query 3: Mengambil 5 konsultasi terbaru
        $konsultasiTerbaru = DB::table('konsultasis')
            ->join('pemeriksaans', 'konsultasis.pemeriksaan_id', '=', 'pemeriksaans.id')
            ->join('sasarans', 'pemeriksaans.sasaran_id', '=', 'sasarans.id')
            ->select(
                DB::raw("CONCAT('Konsultasi untuk ', sasarans.nama_lengkap) as teks"),
                'konsultasis.created_at as tanggal',
                DB::raw("'konsultasi' as tipe")
            );
            
        // Terapkan filter organisasi untuk non-admin
        if (!$user->hasRole('admin')) {
            $sasaranTerbaru->whereIn('sasarans.organisasi_id', $accessibleOrganisasiIds);
            $pemeriksaanTerbaru->whereIn('sasarans.organisasi_id', $accessibleOrganisasiIds);
            $konsultasiTerbaru->whereIn('sasarans.organisasi_id', $accessibleOrganisasiIds);
        }

        // Gabungkan ketiga query, urutkan, dan ambil 5 teratas
        $aktivitasTerbaru = $sasaranTerbaru
            ->unionAll($pemeriksaanTerbaru)
            ->unionAll($konsultasiTerbaru)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        // ================================================================
        // KIRIM SEMUA DATA KE VIEW
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
            'chartColors' => $chartColors,
            'cardKiriJudul' => $cardKiriJudul, 'cardKiriNilai' => $cardKiriNilai,
            'cardKananJudul' => $cardKananJudul, 'cardKananNilai' => $cardKananNilai,
            'aktivitasTerbaru' => $aktivitasTerbaru
        ]);
    }

    /*
     * Menu Style Routs
     */
    public function horizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.horizontal',compact('assets'));
    }
    public function dualhorizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-horizontal',compact('assets'));
    }
    public function dualcompact(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-compact',compact('assets'));
    }
    public function boxed(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed',compact('assets'));
    }
    public function boxedfancy(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed-fancy',compact('assets'));
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
        return view('special-pages.calender',compact('assets'));
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
