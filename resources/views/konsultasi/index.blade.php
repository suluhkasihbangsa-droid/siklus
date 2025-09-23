<x-app-layout>

    {{-- BAGIAN BARU: CSS KHUSUS UNTUK PRINT --}}
    @push('styles')
    <style>
        @media print {
            /* Sembunyikan semua elemen di halaman KECUALI konten modal */
            body * {
                visibility: hidden;
            }
            .modal-body, .modal-body * {
                visibility: visible;
            }
            /* Atur agar konten modal mengisi seluruh halaman cetak */
            .modal-dialog {
                max-width: 100% !important;
                margin: 0 !important;
            }
            .modal-content {
                border: none !important;
                box-shadow: none !important;
            }
            /* Sembunyikan header dan footer modal saat mencetak */
            .modal-header, .modal-footer {
                display: none !important;
            }
            /* Atur lebar kertas thermal (sekitar 58mm) dan font */
            @page {
                size: 58mm auto; /* Lebar 5.8cm, panjang otomatis */
                margin: 5mm;
            }
            .rekam-medis-print {
                font-family: 'Courier New', Courier, monospace;
                font-size: 8pt; /* Ukuran font kecil untuk kertas thermal */
                color: black;
            }
            .rekam-medis-print h4, .rekam-medis-print h6 {
                font-size: 9pt;
                font-weight: bold;
                text-align: center;
            }
            .rekam-medis-print p, .rekam-medis-print li, .rekam-medis-print td {
                margin-bottom: 2px;
                line-height: 1.2;
            }
            .rekam-medis-print hr {
                border-top: 1px dashed black;
            }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Daftar Pasien Konsultasi</h4>
                    </div>
                </div>
                <div class="card-body">
                    
                    {{-- Form Pencarian --}}
                    <form action="{{ route('konsultasi.index') }}" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan Nama atau No. Registrasi..." value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Cari</button>
                        </div>
                    </form>

                    {{-- Tabel Daftar Pasien --}}
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pasien</th>
                                    <th>Usia</th>
                                    <th>Organisasi</th>
                                    <th>Periksa Terakhir</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sasarans as $sasaran)
                                    <tr>
                                        <td>{{ $loop->iteration + $sasarans->firstItem() - 1 }}</td>
                                        <td>
                                            <strong>{{ $sasaran->nama_lengkap }}</strong><br>
                                            <small class="text-muted">{{ $sasaran->nomor_registrasi }}</small>
                                        </td>
                                        <td>
                                            {{-- Menghitung usia langsung di view --}}
                                            {{ \Carbon\Carbon::parse($sasaran->tgl_lahir)->age }} tahun
                                        </td>
                                        <td>{{ $sasaran->organisasi->nama_organisasi ?? '-' }}</td>
                                        <td>
                                            @if($sasaran->pemeriksaanTerakhir)
                                                {{ \Carbon\Carbon::parse($sasaran->pemeriksaanTerakhir->tanggal_pemeriksaan)->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">Belum Ada</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- Logika untuk menampilkan status konsultasi --}}
                                            @if($sasaran->status_konsultasi == 'Sedang Konsultasi' && $sasaran->konsultasi_dimulai_pada && \Carbon\Carbon::parse($sasaran->konsultasi_dimulai_pada)->diffInMinutes(now()) < 30)
                                                <span class="badge bg-warning text-dark">
                                                    Oleh: {{ $sasaran->dokterYangSedangKonsultasi->first_name ?? 'Dokter Lain' }}
                                                </span>
                                            @elseif($sasaran->konsultasiTerakhir)
                                                <span class="badge bg-success">
                                                    Oleh: {{ $sasaran->konsultasiTerakhir->dokter->first_name ?? 'Dokter' }}
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($sasaran->konsultasiTerakhir->created_at)->isoFormat('D MMM YYYY') }}</small>
                                            @else
                                                <span class="badge bg-primary">Tersedia</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                {{-- Tombol Lihat Hasil Konsultasi (BARU) --}}
                                                @if($sasaran->konsultasiTerakhir)
                                                    <button type="button" class="btn btn-sm btn-icon btn-info lihat-hasil-btn" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#hasilKonsultasiModal" 
                                                            data-pemeriksaan-id="{{ $sasaran->pemeriksaanTerakhir->id }}"
                                                            title="Lihat Hasil Konsultasi Terakhir">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16"><path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m3 2.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/></svg>
                                                    </button>
                                                @endif

                                                {{-- Tombol Lakukan Konsultasi --}}
                                                @if($sasaran->pemeriksaanTerakhir)
                                                    @if($sasaran->status_konsultasi == 'Tersedia' || ($sasaran->konsultasi_dimulai_pada && \Carbon\Carbon::parse($sasaran->konsultasi_dimulai_pada)->diffInMinutes(now()) >= 30) || $sasaran->konsultasi_oleh_id == auth()->id())
                                                        <a href="{{ route('konsultasi.create', $sasaran->pemeriksaanTerakhir->id) }}" class="btn btn-sm btn-icon btn-primary" title="Lakukan Konsultasi Baru">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg>
                                                        </a>
                                                    @else
                                                         <button class="btn btn-sm btn-icon btn-secondary" disabled title="Sedang dikonsultasikan oleh dokter lain">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/></svg>
                                                         </button>
                                                    @endif
                                                @else
                                                    <span class="text-muted small fst-italic">Belum ada data pemeriksaan</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-muted">Tidak ada data sasaran yang ditemukan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Link Paginasi --}}
                    <div class="mt-4">
                        {{ $sasarans->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

{{-- MODAL UNTUK MENAMPILKAN HASIL KONSULTASI --}}
<div class="modal fade" id="hasilKonsultasiModal" tabindex="-1" aria-labelledby="hasilKonsultasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hasilKonsultasiModalLabel">Memuat Hasil Rekam Medis...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body-content">
                {{-- Konten akan diisi oleh JavaScript --}}
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                {{-- Ubah menjadi tag <a> dengan ID dan target="_blank" --}}
                <a href="#" id="cetakBtn" target="_blank" class="btn btn-primary">
                    <i class="bi bi-printer me-2"></i>Cetak
                </a>
            </div>
        </div>
    </div>
</div>

    @push('scripts')
    <script>
    $(document).ready(function(){
        $('.table').on('click', '.lihat-hasil-btn', function() {
            const pemeriksaanId = $(this).data('pemeriksaan-id');
            const modalTitle = $('#hasilKonsultasiModalLabel');
            const modalBody = $('#modal-body-content');
    
            modalTitle.text('Memuat Hasil Rekam Medis...');
            modalBody.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    
            $.ajax({
                url: `/ajax/get-hasil-konsultasi/${pemeriksaanId}`,
                method: 'GET',
                success: function(data) {
                    const konsultasi = data.konsultasi_terakhir;
                    const sasaran = data.sasaran;
                    
                    if (!konsultasi || !sasaran) {
                        modalBody.html('<p class="text-danger">Data konsultasi atau sasaran tidak lengkap.</p>');
                        return;
                    }

                    modalTitle.text('Hasil Rekam Medis - ' + sasaran.nama_lengkap);
                    const cetakUrl = `{{ url('/konsultasi/pemeriksaan') }}/${pemeriksaanId}/cetak`;
                    $('#cetakBtn').attr('href', cetakUrl);
    
                    let resepHtml = 'Tidak ada resep obat.';
                    if(konsultasi.resep_obats && konsultasi.resep_obats.length > 0) {
                        resepHtml = '<ul class="list-unstyled">';
                        konsultasi.resep_obats.forEach(resep => {
                            resepHtml += `<li><strong>${resep.obat.nama_obat}</strong> (${resep.qty} ${resep.obat.satuan})<br><small>${resep.keterangan_konsumsi}</small></li>`;
                        });
                        resepHtml += '</ul>';
                    }
    
                    moment.locale('id');
                    const usiaText = moment(sasaran.tgl_lahir).fromNow(true);

                    let keluhanGabungan = data.keluhan_awal || '';
                    if (konsultasi.keluhan) {
                        if(keluhanGabungan) keluhanGabungan += '<br>';
                        keluhanGabungan += konsultasi.keluhan;
                    }
                    if(!keluhanGabungan) keluhanGabungan = '-';

                    const contentHtml = `
                        <div class="rekam-medis-print">
                            <h4 class="text-center mb-0">Hasil Rekam Medis</h4>
                            <p class="text-center small text-muted">Siklus</p>
                            <hr>
                            <p><strong>Tgl. Konsultasi:</strong> ${new Date(konsultasi.created_at).toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})}</p>
                            <h6>DATA PASIEN</h6>
                            <table class="table table-sm table-borderless">
                                <tr><td style="width: 30%;">Nama</td><td>: ${sasaran.nama_lengkap}</td></tr>
                                <tr><td>Jenis Kelamin</td><td>: ${sasaran.gender === 'L' ? 'Laki-laki' : 'Perempuan'}</td></tr>
                                <tr><td>Usia</td><td>: ${usiaText}</td></tr>
                            </table>
                            <h6>PEMERIKSAAN (Tgl: ${new Date(data.tanggal_pemeriksaan).toLocaleDateString('id-ID')})</h6>
                            <ul class="list-unstyled">
                                <li><strong>1. IMT:</strong> BB (${data.bb} kg) / TB (${data.tb} cm) = ${data.imt} (${data.int_imt})</li>
                                <li><strong>2. Lingkar Perut:</strong> ${data.lp || '-'} cm (${data.int_lp || '-'})</li>
                                <li><strong>3. LiLA:</strong> ${data.lila || '-'} cm (${data.int_lila || '-'})</li>
                                <li><strong>4. Cek Laboratorium:</strong>
                                    <ul class="list-unstyled ps-3">
                                        <li>Gula Darah: ${data.gd || '-'} mg/dL (${data.int_gd || '-'})</li>
                                        <li>Asam Urat: ${data.asut || '-'} mg/dL (${data.int_asut || '-'})</li>
                                        <li>Kolesterol: ${data.koles || '-'} mg/dL (${data.int_koles || '-'})</li>
                                    </ul>
                                </li>
                            </ul>
                            <h6>KONSULTASI DOKTER</h6>
                            <p><strong>Keluhan:</strong><br>${keluhanGabungan}</p>
                            <p><strong>Diagnosa:</strong><br>${konsultasi.diagnosa}</p>
                            <p><strong>Rekomendasi:</strong><br>${konsultasi.rekomendasi}</p>
                            <h6>REKOMENDASI TERAPI</h6>
                            ${resepHtml}
                            <hr>
                            <p class="mt-4">
                                Diperiksa oleh:<br>
                                <strong>Dokter ${konsultasi.dokter ? (konsultasi.dokter.first_name + ' ' + konsultasi.dokter.last_name) : 'N/A'}</strong><br>
                                <small>SIP: ${konsultasi.dokter ? (konsultasi.dokter.nomor_sip) : 'N/A'}</small><br>
                                <small>STR: ${konsultasi.dokter ? (konsultasi.dokter.nomor_str) : 'N/A'}</small>
                            </p>
                        </div>
                    `;
                    modalBody.html(contentHtml);
                },
                error: function() {
                    modalTitle.text('Gagal Memuat Data');
                    modalBody.html('<p class="text-danger">Tidak dapat mengambil data rekam medis. Silakan coba lagi.</p>');
                }
            });
        });
    });
    </script>
    @endpush
    
</x-app-layout>