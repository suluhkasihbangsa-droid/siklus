	{{-- resources/views/sasaran/partials/_print-styles.blade.php --}}

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