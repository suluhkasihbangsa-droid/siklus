<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak ID Pasien - {{ $sasaran->nama_lengkap }}</title>
    <style>
        /* Pengaturan halaman tetap sama untuk kertas thermal 58mm */
        @page {
            size: 58mm auto;
            margin: 0.2cm;
        }

        body {
            font-family: 'Arial Narrow', Arial, monospace;
            font-size: 11pt;
            color: black;
            /* PERUBAHAN UTAMA: Semua teks dibuat rata tengah */
            text-align: center;
        }

        .container {
            width: 100%;
        }
        
        /* GAYA BARU UNTUK LABEL DAN VALUE */
        .label {
            display: block; /* Pastikan setiap elemen ada di baris baru */
            font-size: 10pt;
            margin-top: 10px; /* Jarak antar bagian */
        }

        .value {
            display: block;
            font-size: 12pt;
            font-weight: bold;
        }

        /* Kelas khusus untuk membuat nama pasien menjadi huruf kapital */
        .patient-name {
            text-transform: uppercase;
        }

    </style>
</head>
<body>
    <div class="container">
        
        <div class="label">Nomor Registrasi</div>
        <div class="value">{{ $sasaran->nomor_registrasi }}</div>

        <div class="label">NAMA PASIEN</div>
        <div class="value patient-name">{{ $sasaran->nama_lengkap }}</div>
        
        <div class="label">Tgl Register</div>
        <div class="value">{{ $tanggalCetak }}</div>

    </div>

    <script>
        // Skrip tidak berubah, tetap simpel
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>