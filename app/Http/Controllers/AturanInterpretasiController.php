<?php

namespace App\Http\Controllers;

use App\Models\AturanInterpretasi;
use Illuminate\Http\Request;

class AturanInterpretasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Ambil semua aturan, urutkan berdasarkan kategori agar rapi
        $semuaAturan = AturanInterpretasi::orderBy('kategori')->orderBy('batas_bawah')->get();
        
        // Kelompokkan aturan berdasarkan kolom 'kategori'
        $groupedAturan = $semuaAturan->groupBy('kategori');

        return view('aturan_interpretasi.index', compact('groupedAturan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(AturanInterpretasi $aturan_interpretasi)
    {
        return view('aturan_interpretasi.edit', ['aturan' => $aturan_interpretasi]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AturanInterpretasi $aturan_interpretasi)
    {
        // 1. Definisikan aturan-aturan dasar
        $rules = [
            'nama_interpretasi' => 'required|string|max:255',
            'kode_interpretasi' => 'nullable|string|max:10',
            'warna_badge' => 'required|string|in:primary,secondary,success,danger,warning,info,light,dark',
            'batas_bawah' => 'nullable|numeric',
            'batas_atas' => 'nullable|numeric', // <-- Aturan dasar
            'batas_sistolik' => 'nullable|integer',
            'batas_diastolik' => 'nullable|integer',
        ];

        // 2. Tambahkan aturan 'gte' secara KONDISIONAL
        // Aturan ini hanya ditambahkan JIKA KEDUA field (bawah dan atas) diisi oleh pengguna
        if ($request->filled('batas_bawah') && $request->filled('batas_atas')) {
            $rules['batas_atas'] .= '|gte:batas_bawah';
        }

        // 3. Jalankan validasi dengan aturan yang sudah dinamis
        $validatedData = $request->validate($rules);

        // 4. Update data (tidak berubah)
        $aturan_interpretasi->update($validatedData);

        return redirect()->route('aturan-interpretasi.index')->with('success', 'Aturan interpretasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
