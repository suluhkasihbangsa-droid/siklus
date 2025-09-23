<?php

namespace App\Http\Controllers;

use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrganisasiController extends Controller
{
    /**
     * Constructor - Terapkan middleware untuk semua method
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->hasRole('admin')) {
                abort(403, 'Unauthorized action. Hanya admin yang dapat mengakses ini.');
            }
            return $next($request);
        });
    }

    /**
     * Menampilkan daftar organisasi.
     */
    public function index()
    {
        // Ambil hanya organisasi induk (yang tidak punya parent)
        // dan langsung muat relasi "children" mereka untuk efisiensi query
        $organisasis = Organisasi::whereNull('parent_id')
                                ->with('children')
                                ->orderBy('nama_organisasi', 'asc')
                                ->get();

        return view('organisasi.index', compact('organisasis'));
    }

    /**
     * Menampilkan detail organisasi.
     */
    public function show(Organisasi $organisasi)
    {
        // Memastikan kita memuat semua sub-organisasi (children)
        $organisasi->load('children');

        return view('organisasi.show', compact('organisasi'));
    }

    /**
     * Menampilkan form untuk membuat organisasi baru.
     */
    public function create()
    {
        return view('organisasi.create');
    }

    /**
     * Menyimpan organisasi baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_organisasi_induk' => 'required|string|max:255',
            'sub_organisasi.*' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat Organisasi Induk
            $induk = Organisasi::create([
                'nama_organisasi' => $request->nama_organisasi_induk
            ]);

            // 2. Jika ada Sub Organisasi, buat dan hubungkan
            if ($request->has('sub_organisasi')) {
                foreach ($request->sub_organisasi as $nama_sub) {
                    if (!empty($nama_sub)) {
                        Organisasi::create([
                            'nama_organisasi' => $nama_sub,
                            'parent_id' => $induk->id
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('organisasi.index')->with('success', 'Organisasi berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form untuk edit organisasi.
     */
    public function edit(Organisasi $organisasi)
    {
        return view('organisasi.edit', compact('organisasi'));
    }

    /**
     * Update organisasi di database.
     */
    public function update(Request $request, Organisasi $organisasi)
    {
        $request->validate([
            'nama_organisasi' => 'required|string|max:255',
        ]);

        try {
            $organisasi->update([
                'nama_organisasi' => $request->nama_organisasi
            ]);

            return redirect()->route('organisasi.index')->with('success', 'Organisasi berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus organisasi dari database.
     */
    public function destroy(Organisasi $organisasi)
    {
        try {
            // Hapus juga semua sub-organisasi jika ada
            if ($organisasi->children()->exists()) {
                $organisasi->children()->delete();
            }

            $organisasi->delete();

            return redirect()->route('organisasi.index')->with('success', 'Organisasi berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Quick store untuk AJAX requests.
     */
    public function quickStore(Request $request)
    {
        $request->validate([
            'nama_organisasi_induk' => 'required|string|max:255|unique:organisasis,nama_organisasi',
            'sub_organisasi.*' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            $induk = Organisasi::create([
                'nama_organisasi' => $request->nama_organisasi_induk
            ]);

            if ($request->has('sub_organisasi')) {
                foreach ($request->sub_organisasi as $nama_sub) {
                    if (!empty($nama_sub)) {
                        Organisasi::create([
                            'nama_organisasi' => $nama_sub,
                            'parent_id' => $induk->id
                        ]);
                    }
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Organisasi berhasil dibuat!',
                'data' => $induk
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}   