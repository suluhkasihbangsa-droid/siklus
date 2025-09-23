<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;
use App\Imports\ObatImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ObatController extends Controller
{
    /**
     * Menampilkan halaman daftar obat.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        $obats = Obat::query()
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->where('nama_obat', 'like', "%{$searchTerm}%")
                             ->orWhere('kategori', 'like', "%{$searchTerm}%");
            })
            ->orderBy('nama_obat', 'asc')
            ->get();

        return view('obat.index', compact('obats'));
    }

    /**
     * Menampilkan form untuk menambah obat baru.
     */
    public function create()
    {
        return view('obat.create');
    }

    /**
     * Menyimpan obat baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_obat' => 'required|string|max:255|unique:obats,nama_obat',
            'kategori' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
        ]);

        Obat::create($validatedData);

        return redirect()->route('obat.index')->with('success', 'Obat baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit obat.
     */
    public function edit(Obat $obat)
    {
        return view('obat.edit', compact('obat'));
    }

    /**
     * Memperbarui data obat di database.
     */
    public function update(Request $request, Obat $obat)
    {
        $validatedData = $request->validate([
            // Rule 'unique' diubah agar mengabaikan data obat yang sedang diedit
            'nama_obat' => 'required|string|max:255|unique:obats,nama_obat,' . $obat->id,
            'kategori' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
        ]);

        $obat->update($validatedData);

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil diperbarui.');
    }

    /**
     * Menghapus data obat dari database.
     */
    public function destroy(Obat $obat)
    {
        try {
            $obat->delete();
            return redirect()->route('obat.index')->with('success', 'Data obat berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('obat.index')->with('error', 'Gagal menghapus obat karena masih terhubung dengan data lain.');
        }
    }

    public function downloadTemplate()
    {
        // Pastikan file template Anda ada di public/templates/template_import_obat.xlsx
        $filePath = public_path('templates/template_import_obat.xlsx');

        if (!file_exists($filePath)) {
            return redirect()->route('obat.index')->with('error', 'File template tidak ditemukan.');
        }

        return response()->download($filePath);
    }

    /**
     * Mengelola proses import data obat dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');
        $obatImport = new ObatImport();

        try {
            Excel::import($obatImport, $file);
        } catch (ValidationException $e) {
            // Tangkap error validasi jika ada
            $failures = $e->failures();
            // Lanjutkan proses, karena kita ingin menampilkan laporan, bukan menghentikan semuanya
        }

        // Siapkan pesan laporan
        $failures = $obatImport->failures();
        $errors = $obatImport->errors();

        $importedCount = $obatImport->getRowCount() - count($failures) - count($errors);

        $feedback = [
            'success' => "Import selesai! {$importedCount} data berhasil ditambahkan."
        ];

        if (count($failures) > 0) {
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            $feedback['failures'] = $errorMessages;
        }
        
        return redirect()->route('obat.index')->with($feedback);
    }

}