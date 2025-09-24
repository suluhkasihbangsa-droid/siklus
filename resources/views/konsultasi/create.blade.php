<x-app-layout>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Formulir Konsultasi</h4>
                </div>
                <div class="card-body">

                    {{-- BAGIAN 1: DATA PASIEN & PEMERIKSAAN (READ-ONLY) --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0">Data Pasien & Hasil Skrining Terakhir</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Kolom Data Pasien --}}
                                <div class="col-md-6 border-end">
                                    <h6 class="mb-3 text-primary">Informasi Pasien</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th style="width: 40%;">Nama Lengkap</th>
                                            <td>: {{ $pemeriksaan->sasaran->nama_lengkap }}</td>
                                        </tr>
                                        <tr>
                                            <th>No. Registrasi</th>
                                            <td>: {{ $pemeriksaan->sasaran->nomor_registrasi }}</td>
                                        </tr>
                                        <tr>
                                            <th>Usia Saat Ini</th>
                                            <td>: {{ \Carbon\Carbon::parse($pemeriksaan->sasaran->tgl_lahir)->age }} tahun</td>
                                        </tr>
                                        <tr>
                                            <th>Gender</th>
                                            <td>: {{ $pemeriksaan->sasaran->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Organisasi</th>
                                            <td>: {{ $pemeriksaan->sasaran->organisasi->nama_organisasi ?? '-'}}</td>
                                        </tr>
                                        <tr>
                                            <th>No. HP</th>
                                            <td>
                                                : {{ $pemeriksaan->sasaran->no_hp ?? '-' }}
                                                @if($pemeriksaan->sasaran->no_hp)
                                                    {{-- Shortcut WhatsApp --}}
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pemeriksaan->sasaran->no_hp) }}" target="_blank" class="ms-2 btn btn-success btn-sm py-0 px-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/></svg>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                {{-- Kolom Hasil Skrining --}}
                                <div class="col-md-6">
                                    <h6 class="mb-3 text-primary">Hasil Skrining Terakhir ({{ \Carbon\Carbon::parse($pemeriksaan->tanggal_pemeriksaan)->format('d/m/Y') }})</h6>
                                    <table class="table table-sm table-borderless">
                                        <tr><th style="width: 40%;">IMT</th><td>: {{ $pemeriksaan->imt ?? '-' }} ({{ $pemeriksaan->int_imt ?? '-' }})</td></tr>
                                        <tr><th>Tensi</th><td>: {{ $pemeriksaan->tensi_sistolik ?? '-' }}/{{ $pemeriksaan->tensi_diastolik ?? '-' }} ({{ $pemeriksaan->int_tensi ?? '-' }})</td></tr>
                                        <tr><th>Gula Darah</th><td>: {{ $pemeriksaan->gd ?? '-' }} mg/dL ({{ $pemeriksaan->int_gd ?? '-' }})</td></tr>
                                        <tr><th>Asam Urat</th><td>: {{ $pemeriksaan->asut ?? '-' }} mg/dL ({{ $pemeriksaan->int_asut ?? '-' }})</td></tr>
                                        <tr><th>Kolesterol</th><td>: {{ $pemeriksaan->koles ?? '-' }} mg/dL ({{ $pemeriksaan->int_koles ?? '-' }})</td></tr>
                                        <tr><th>Keluhan</th><td>: {{ $pemeriksaan->keluhan_awal ?? '-' }} </td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BAGIAN 2: FORM INPUT KONSULTASI --}}
                    <form action="{{ route('konsultasi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="pemeriksaan_id" value="{{ $pemeriksaan->id }}">

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group"><label for="keluhan" class="form-label fw-semibold">Keluhan Tambahan</label><textarea name="keluhan" id="keluhan" class="form-control" rows="3" placeholder="Tuliskan keluhan...">{{ old('keluhan') }}</textarea></div>
                                <div class="form-group"><label for="diagnosa" class="form-label fw-semibold">Diagnosa</label><textarea name="diagnosa" id="diagnosa" class="form-control" rows="4" placeholder="Tuliskan diagnosa Anda..." required>{{ old('diagnosa') }}</textarea></div>
                                <div class="form-group"><label for="rekomendasi" class="form-label fw-semibold">Rekomendasi & Anjuran</label><textarea name="rekomendasi" id="rekomendasi" class="form-control" rows="4" placeholder="Tuliskan rekomendasi..." required>{{ old('rekomendasi') }}</textarea></div>
                                
                                {{-- BAGIAN BARU: RESEP OBAT DINAMIS --}}
                                <hr class="my-4">
                                <div class="form-group">
                                    <label class="form-label fw-semibold d-block mb-3">Rekomendasikan Resep Obat?</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rekomendasi_obat" id="resep_tidak" value="tidak" checked>
                                        <label class="form-check-label" for="resep_tidak">Tidak</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rekomendasi_obat" id="resep_ya" value="ya">
                                        <label class="form-check-label" for="resep_ya">Ya</label>
                                    </div>
                                </div>

                                {{-- Kontainer untuk baris resep obat (awalya tersembunyi) --}}
                                <div id="resep-obat-container" style="display: none;">
                                    <div id="resep-obat-list">
                                        {{-- Baris resep akan ditambahkan oleh JavaScript di sini --}}
                                    </div>
                                    <button type="button" id="add-resep-row" class="btn btn-success btn-sm mt-2">
                                        + Tambah Obat
                                    </button>
                                </div>
                                {{-- AKHIR BAGIAN RESEP OBAT DINAMIS --}}

                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="{{ route('konsultasi.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Hasil Konsultasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- TEMPLATE UNTUK BARIS RESEP OBAT (TERSEMBUNYI) --}}
    <div id="resep-row-template" style="display: none;">
        <div class="row gx-2 gy-2 align-items-center resep-obat-row mb-2">
            <div class="col-md-5">
                <input type="hidden" name="obats[__INDEX__][obat_id]" class="resep-obat-id">
                <select class="form-select select2-obat" name="obats[__INDEX__][nama_obat_select]" required>
                    <option value="">Cari nama obat...</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control resep-satuan bg-light" placeholder="Satuan" readonly>
            </div>
            <div class="col-md-1">
                <input type="number" name="obats[__INDEX__][qty]" class="form-control resep-qty" placeholder="Qty" required min="1">
            </div>
            <div class="col-md-3">
                <input type="text" name="obats[__INDEX__][keterangan_konsumsi]" class="form-control resep-keterangan" placeholder="cth: 3x1 setelah makan" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-icon remove-resep-row">X</button>
            </div>
        </div>
    </div>

@push('scripts')
<script>
$(document).ready(function(){
    // === INISIALISASI & VARIABEL ===
    const resepContainer = $('#resep-obat-container');
    const resepList = $('#resep-obat-list');
    const template = $('#resep-row-template');
    let resepIndex = 0; // Counter untuk memastikan setiap baris punya nama unik

    // === FUNGSI-FUNGSI UTAMA ===

    /**
     * Fungsi untuk menginisialisasi Select2 pada sebuah elemen.
     * Select2 akan diubah menjadi input autocomplete yang canggih.
     */
    function initSelect2(element) {
        element.select2({
            placeholder: 'Cari nama obat...',
            ajax: {
                url: "{{ route('ajax.cariObat') }}", // Route ke endpoint pencarian kita
                dataType: 'json',
                delay: 250, // Jeda sebelum request dikirim
                data: function (params) {
                    return {
                        term: params.term // Kirim kata kunci ketikan user
                    };
                },
                processResults: function (data) {
                    // Ubah format data dari server agar sesuai dengan Select2
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.nama_obat,
                                id: item.id,
                                satuan: item.satuan // Kirim data 'satuan' tambahan
                            }
                        })
                    };
                }
            }
        }).on('select2:select', function (e) {
            // Event ini berjalan saat dokter MEMILIH obat dari daftar
            let data = e.params.data;
            let row = $(this).closest('.resep-obat-row');
            
            // Isi input 'obat_id' (hidden) dan 'satuan' (readonly) secara otomatis
            row.find('.resep-obat-id').val(data.id);
            row.find('.resep-satuan').val(data.satuan);
        });
    }

    /**
     * Fungsi untuk menambah baris resep obat baru.
     */
    function addRow() {
        // Ambil HTML dari template, ganti placeholder __INDEX__ dengan counter saat ini
        let newRowHtml = template.html().replace(/__INDEX__/g, resepIndex);
        
        // Tambahkan baris baru ke dalam daftar
        resepList.append(newRowHtml);
        
        // Cari elemen <select> yang baru saja ditambahkan
        let newSelect = resepList.find('.resep-obat-row').last().find('.select2-obat');
        
        // Inisialisasi Select2 pada elemen <select> yang baru tersebut
        initSelect2(newSelect);
        
        resepIndex++; // Naikkan counter untuk baris berikutnya
    }


    // === EVENT LISTENERS ===

    // 1. Saat radio button "Rekomendasikan obat?" diubah
    $('input[name="rekomendasi_obat"]').on('change', function() {
        if (this.value === 'ya') {
            resepContainer.slideDown(); // Tampilkan kontainer resep
            // Jika belum ada baris resep sama sekali, otomatis tambahkan satu
            if (resepList.children().length === 0) {
                addRow();
            }
        } else {
            resepContainer.slideUp(); // Sembunyikan kontainer resep
            resepList.empty(); // Hapus semua baris resep jika "Tidak" dipilih
        }
    });

    // 2. Saat tombol "+ Tambah Obat" di-klik
    $('#add-resep-row').on('click', addRow);

    // 3. Saat tombol "X" (hapus baris) di-klik
    // Menggunakan event delegation karena tombol ini dibuat secara dinamis
    resepList.on('click', '.remove-resep-row', function() {
        $(this).closest('.resep-obat-row').remove();

        // Jika semua baris sudah dihapus, otomatis set radio button ke "Tidak"
        if (resepList.children().length === 0) {
            $('#resep_tidak').prop('checked', true).trigger('change');
        }
    });

});
</script>
@endpush

</x-app-layout>