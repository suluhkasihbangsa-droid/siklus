{{-- resources/views/sasaran/partials/_modal-hasil.blade.php --}}

{{-- MODAL UNTUK MENAMPILKAN HASIL KONSULTASI --}}
<div class="modal fade" id="hasilKonsultasiModal" tabindex="-1" aria-labelledby="hasilKonsultasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hasilKonsultasiModalLabel">Memuat Hasil Rekam Medis...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body-content">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="copyBtn" class="btn btn-info" data-clipboard-target="#print-content">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard me-1" viewBox="0 0 16 16"><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/><path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/></svg>
                    Salin Teks
                </button>
                <a href="#" id="cetakBtn" target="_blank" class="btn btn-primary disabled" role="button" aria-disabled="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer me-1" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1h10v-1a2 2 0 0 0-2-2z"/></svg>
                    Cetak
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
<script>
$(document).ready(function() {
    let lastFocusedElement;

    // --- FIX: INISIALISASI CLIPBOARDJS DENGAN TARGET MODAL ---
    // Kita inisialisasi ClipboardJS untuk bekerja di dalam modal.
    // Ini lebih andal daripada menggunakan fungsi `text`.
    let clipboard = new ClipboardJS('#copyBtn', {
        container: document.getElementById('hasilKonsultasiModal')
    });

    clipboard.on('success', function(e) {
        showCopySuccess();
        e.clearSelection(); // Membersihkan seleksi setelah copy berhasil
    });

    clipboard.on('error', function(e) {
        alert('Gagal menyalin. Silakan salin manual.');
        console.error('Clipboard Error:', e);
    });

    // ✅ Event klik tombol "Lihat Hasil"
    $('body').on('click', '.lihat-hasil-btn', function() {
        lastFocusedElement = this;
        
        const pemeriksaanId = $(this).data('pemeriksaan-id');
        const modalTitle = $('#hasilKonsultasiModalLabel');
        const modalBody = $('#modal-body-content');

        const ajaxUrl = "{{ route('ajax.getHasilKonsultasi', ['pemeriksaan' => ':id']) }}".replace(':id', pemeriksaanId);
        
        modalTitle.text('Memuat Hasil Rekam Medis...');
        modalBody.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        $('#cetakBtn').addClass('disabled').attr('href', '#');

        $.ajax({
            url: ajaxUrl,
            method: 'GET',
            success: function(data) {
                if (!data || !data.sasaran) {
                    modalTitle.text('Data Tidak Ditemukan');
                    modalBody.html('<div id="print-content"><p class="text-danger text-center">Data pemeriksaan tidak ditemukan.</p></div>');
                    return;
                }
                
                const cetakUrl = `{{ url('/konsultasi/pemeriksaan') }}/${pemeriksaanId}/cetak`;
                $('#cetakBtn').removeClass('disabled').attr('href', cetakUrl);
                
                const sasaran = data.sasaran;
                const konsultasi = data.konsultasi_terakhir;
                const pemeriksaan = data;

                modalTitle.text(`Hasil Rekam Medis`);

                const contentHtml = `
                <div class="rekam-medis-print" id="print-content">
                    
                    <div class="mb-4">
                        <h4 class="mb-0">${sasaran.nama_lengkap}</h4>
                        <small class="text-muted">
                            ${sasaran.gender === 'L' ? 'Laki-laki' : 'Perempuan'} &bull; 
                            Usia ${pemeriksaan.usia_saat_pemeriksaan ?? (moment().diff(moment(sasaran.tgl_lahir), 'years') + ' tahun')} &bull; 
                            ID: ${sasaran.nomor_registrasi}
                        </small>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-notes-medical me-2"></i>Hasil Skrining</h6>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr><td width="120px"><strong>Tgl. Periksa</strong></td><td>: ${moment(pemeriksaan.tanggal_pemeriksaan).format('D MMMM YYYY')}</td></tr>
                                    <tr><td><strong>Tensi</strong></td><td>: ${pemeriksaan.tensi_sistolik ?? '-'}/${pemeriksaan.tensi_diastolik ?? '-'} <span class="badge bg-secondary">${pemeriksaan.int_tensi ?? '-'}</span></td></tr>
                                    <tr><td><strong>BB / TB</strong></td><td>: ${pemeriksaan.bb ?? '-'} kg / ${pemeriksaan.tb ?? '-'} cm</td></tr>
                                    <tr><td><strong>IMT</strong></td><td>: ${pemeriksaan.imt ?? '-'} <span class="badge bg-secondary">${pemeriksaan.int_imt ?? '-'}</span></td></tr>
                                    ${pemeriksaan.lp ? `<tr><td><strong>Lingkar Perut</strong></td><td>: ${pemeriksaan.lp} cm <span class="badge bg-secondary">${pemeriksaan.int_lp ?? '-'}</span></td></tr>` : ''}
                                    ${pemeriksaan.lila ? `<tr><td><strong>LiLA</strong></td><td>: ${pemeriksaan.lila} cm <span class="badge bg-secondary">${pemeriksaan.int_lila ?? '-'}</span></td></tr>` : ''}
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-vial me-2"></i>Hasil Laboratorium</h6>
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <td width="120px"><strong>Gula Darah</strong></td>
                                        <td>: ${pemeriksaan.gd ?? '-'} mg/dL <span class="badge bg-secondary">${pemeriksaan.int_gd ?? '-'}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Asam Urat</strong></td>
                                        <td>: ${pemeriksaan.asut ?? '-'} mg/dL <span class="badge bg-secondary">${pemeriksaan.int_asut ?? '-'}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kolesterol</strong></td>
                                        <td>: ${pemeriksaan.koles ?? '-'} mg/dL <span class="badge bg-secondary">${pemeriksaan.int_koles ?? '-'}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                    ${konsultasi ? `
                    <h6><i class="fas fa-user-md me-2"></i>Catatan Konsultasi</h6>
                    <p class="mb-2">
                        <strong>Keluhan:</strong><br>
                        <span class="text-muted">${konsultasi.keluhan || pemeriksaan.keluhan_awal || 'Tidak ada keluhan tercatat.'}</span>
                    </p>
                    <p class="mb-2">
                        <strong>Diagnosa:</strong><br>
                        <span class="text-muted">${konsultasi.diagnosa || '-'}</span>
                    </p>
                    <p class="mb-3">
                        <strong>Rekomendasi / Anjuran:</strong><br>
                        <span class="text-muted">${konsultasi.rekomendasi || '-'}</span>
                    </p>

                    ${konsultasi.resep_obats && konsultasi.resep_obats.length > 0 ? `
                    <h6><i class="fas fa-pills me-2"></i>Resep Obat</h6>
                    <ul class="list-group list-group-flush">
                        ${konsultasi.resep_obats.map(resep => `
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">${resep.obat.nama_obat}</div>
                                <small class="text-muted">${resep.keterangan_konsumsi}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">${resep.qty} ${resep.obat.satuan}</span>
                        </li>
                        `).join('')}
                    </ul>
                    ` : ''}

                    <div class="mt-4 text-end">
                        <small class="text-muted">Diperiksa oleh,</small><br>
                        <strong>dr. ${konsultasi.dokter ? `${konsultasi.dokter.first_name || ''} ${konsultasi.dokter.last_name || ''}`.trim() : 'N/A'}</strong><br>
                        <small class="text-muted">SIP: ${konsultasi.dokter ? konsultasi.dokter.nomor_sip || '(Belum ada data)' : '-'}</small>
                    </div>
                    ` : '<div class="alert alert-warning text-center">Belum ada data konsultasi dokter untuk pemeriksaan ini.</div>'}
                </div>`;

                modalBody.html(contentHtml);
                
            },
            error: function(jqXHR) {
                console.error("AJAX Error:", jqXHR.status, jqXHR.responseText);
                modalTitle.text('Gagal Memuat Data');
                modalBody.html('<p class="text-danger text-center">Terjadi kesalahan saat mengambil data.</p>');
            }
        });
    });

    function showCopySuccess() {
        const button = $('#copyBtn');
        const originalHtml = button.html();
        
        button
            .prop('disabled', true)
            .html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle me-1" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/></svg>Tersalin!')
            .removeClass('btn-info')
            .addClass('btn-success');
        
        setTimeout(() => {
            button
                .html(originalHtml)
                .prop('disabled', false)
                .removeClass('btn-success')
                .addClass('btn-info');
        }, 2000);
    }

    // ✅ Balikin fokus ke tombol terakhir setelah modal ditutup
    const hasilModal = document.getElementById('hasilKonsultasiModal');
    if (hasilModal) {
        // Event ini akan berjalan setiap kali modal selesai ditutup
        hasilModal.addEventListener('hidden.bs.modal', function () {
            // Jika kita tahu tombol mana yang membuka modal...
            if (lastFocusedElement) {
                // ...kembalikan fokus ke tombol itu.
                lastFocusedElement.focus();
            }
        });
    }
});
</script>
@endpush