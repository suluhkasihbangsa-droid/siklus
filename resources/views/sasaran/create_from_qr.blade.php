<x-app-layout>
    
    <div class="container">
        {{-- 1. Area untuk Scanner --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Pindai QR Code Sasaran</h4>
                </div>
                <div class="header-action">
                    <a href="{{ route('sasaran.index') }}" class="btn btn-danger">Batal</a>
                </div>
            </div>
            <div class="card-body text-center">
                <div id="qr-reader" style="width: 100%; max-width: 450px; margin: auto;"></div>
                <div id="qr-reader-results" class="mt-3"></div>
            </div>
        </div>

        {{-- 2. Form Tujuan (Awalnya tersembunyi) --}}
        <div class="card" id="form-container" style="display: none;">
            <div class="card-header">
                <h4 class="card-title">Verifikasi Data Sasaran & Pilih Organisasi</h4>
            </div>
            {{-- Menggunakan partial form yang sudah ada --}}
            @include('sasaran.partials._form', [
                'provinsis' => $provinsis,
                'organisasi_induk' => $organisasi_induk,
                'sasaran' => null
            ])
        </div>
    </div>

    @push('scripts')
    {{-- Library untuk QR Scanner --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            function onScanSuccess(decodedText, decodedResult) {
                console.log('Teks mentah dari QR Code:', decodedText);
                html5QrcodeScanner.clear();
                $('#qr-reader').slideUp();
                $('#qr-reader-results').html(`<div class="alert alert-success">Berhasil! Memuat data...</div>`);

                try {
                    // =============================================================
                    // PERUBAHAN DI SINI: Bersihkan teks sebelum parsing
                    // =============================================================
                    const cleanedText = decodedText.trim();
                    const data = JSON.parse(cleanedText);
                    // =============================================================

                    // --- MENGISI FORM (biarkan sisanya sama) ---
                    $('#nama_lengkap').val(data.nama_lengkap);
                    $(`input[name=gender][value=${data.gender}]`).prop('checked', true);
                    $('#no_hp').val(data.no_hp).trigger('input');

                    if (data.tgl_lahir) {
                        const parts = data.tgl_lahir.split('-');
                        $('#tgl_lahir_yyyy').val(parts[0]);
                        $('#tgl_lahir_mm').val(parts[1]);
                        $('#tgl_lahir_dd').val(parts[2]);
                        validateAndSetDate(); 
                    }

                    if (data.nik) {
                        $('#toggle-nik-input').click(); 
                        data.nik.split('').forEach((digit, i) => {
                            $(`#nik-input-${i+1}`).val(digit);
                        });
                        updateHiddenNik();
                    }

                    $('#alamat_detail').val(data.alamat_detail);
                    
                    $('#provinsi_id').val(data.provinsi_id);
                    loadDropdown('{{ url('/ajax/get-kota/') }}/', data.provinsi_id, '#kota_id', 'Pilih Kota/Kabupaten', data.kota_id, function() {
                        loadDropdown('{{ url('/ajax/get-kecamatan/') }}/', data.kota_id, '#kecamatan_id', 'Pilih Kecamatan', data.kecamatan_id, function() {
                            loadDropdown('{{ url('/ajax/get-kelurahan/') }}/', data.kecamatan_id, '#kelurahan_id', 'Pilih Kelurahan/Desa', data.kelurahan_id);
                        });
                    });

                    $('#form-container').slideDown();
                    $('html, body').animate({
                        scrollTop: $("#form-container").offset().top
                    }, 1000);

                } catch (e) {
                    console.error("Gagal mem-parse JSON:", e);
                    $('#qr-reader-results').html(`<div class="alert alert-danger">QR Code tidak valid. Cek Console (F12) untuk detail.</div>`);
                    setTimeout(() => html5QrcodeScanner.render(onScanSuccess, onScanFailure), 2000);
                }
            }

            function onScanFailure(error) { /* Biarkan kosong */ }

            let html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", 
                { fps: 10, qrbox: { width: 250, height: 250 } },
                false
            );
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        });
    </script>
    @endpush

</x-app-layout>