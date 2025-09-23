<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Hasil Konsultasi - {{ $pemeriksaan->sasaran->nama_lengkap }}</title>
    <style>
        @page {
            size: 58mm auto; /* Lebar kertas thermal */
            margin: 0.01cm;
        }
        body {
            font-family: 'Arial Narrow', Arial, monospace; /* Font yang lebih cocok untuk thermal */
            font-size: 10pt;
            color: black;
            line-height: 1.4;
        }
       .container {
            width: 100%;
            /*padding: 2mm;*/
        }
        .header, .footer {
            text-align: center;
        }
        .section-title {
            text-align: center;
            font-weight: bold;
            margin: 10px 0 5px 0;
            font-size: 10pt; /* Menyamakan ukuran font dengan body */
        }
        .info-item {
            margin-bottom: 4px;
        }
        .info-item .label {
            display: block;
            font-weight: bold; /* Label hanya dibedakan dengan bold */
            /* Ukuran font sama dengan value */
        }
        .resep-item {
            margin-bottom: 5px;
        }
        .resep-item .instruction {
            padding-left: 5px;
        }
        .footer {
            margin-top: 15px;
            font-size: 9pt;
            text-align: left; /* Perataan kiri untuk info dokter */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">-- Catatan Periksa Dokter --</div>

        {{-- BAGIAN DATA PASIEN --}}
        <div class="info-item">
            <span class="label">Tanggal:</span>
            {{-- Jika ada konsultasi, pakai tanggalnya. Jika tidak, pakai tanggal pemeriksaan. --}}
            <span class="value">{{ \Carbon\Carbon::parse($konsultasi->created_at ?? $pemeriksaan->tanggal_pemeriksaan)->format('d/m/Y') }}</span>
        </div>
        <div class="info-item">
            <span class="label">ID Pasien:</span>
            <span class="value">{{ $pemeriksaan->sasaran->nomor_registrasi }}</span>
        </div>
        <div class="info-item">
            <span class="label">Nama:</span>
            <span class="value">{{ $pemeriksaan->sasaran->nama_lengkap }}</span>
        </div>
        <div class="info-item">
            <span class="label">Usia:</span>
            <span class="value">{{ $pemeriksaan->usia_saat_pemeriksaan ?? \Carbon\Carbon::parse($pemeriksaan->sasaran->tgl_lahir)->age . ' tahun' }}</span>
        </div>

        {{-- BAGIAN HASIL SKRINING (PEMERIKSAAN) --}}
        <div class="section-title">-- Hasil Skrining --</div>
        <div class="info-item">
            <span class="label">BB / TB:</span>
            <span class="value">{{ $pemeriksaan->bb ?? '-' }} kg / {{ $pemeriksaan->tb ?? '-' }} cm</span>
        </div>
        <div class="info-item">
            <span class="label">IMT:</span>
            <span class="value">{{ $pemeriksaan->imt ?? '-' }} ({{ $pemeriksaan->int_imt ?? '-' }})</span>
        </div>
        <div class="info-item">
            <span class="label">Tensi:</span>
            <span class="value">{{ $pemeriksaan->tensi_sistolik ?? '-' }}/{{ $pemeriksaan->tensi_diastolik ?? '-' }} ({{ $pemeriksaan->int_tensi ?? '-' }})</span>
        </div>
        @if($pemeriksaan->lp)
        <div class="info-item">
            <span class="label">Lingkar Perut:</span>
            <span class="value">{{ $pemeriksaan->lp }} cm ({{ $pemeriksaan->int_lp ?? '-' }})</span>
        </div>
        @endif
        @if($pemeriksaan->lila)
        <div class="info-item">
            <span class="label">LiLA:</span>
            <span class="value">{{ $pemeriksaan->lila }} cm ({{ $pemeriksaan->int_lila ?? '-' }})</span>
        </div>
        @endif
        
        {{-- BAGIAN LAB JIKA ADA ISINYA --}}
        @if($pemeriksaan->gd || $pemeriksaan->asut || $pemeriksaan->koles)
        <div class="section-title">-- Hasil Laboratorium --</div>
        @endif
        @if($pemeriksaan->gd)
        <div class="info-item">
            <span class="label">Gula Darah:</span>
            <span class="value">{{ $pemeriksaan->gd }} mg/dL ({{ $pemeriksaan->int_gd ?? '-' }})</span>
        </div>
        @endif
        @if($pemeriksaan->asut)
        <div class="info-item">
            <span class="label">Asam Urat:</span>
            <span class="value">{{ $pemeriksaan->asut }} mg/dL ({{ $pemeriksaan->int_asut ?? '-' }})</span>
        </div>
        @endif
        @if($pemeriksaan->koles)
        <div class="info-item">
            <span class="label">Kolesterol:</span>
            <span class="value">{{ $pemeriksaan->koles }} mg/dL ({{ $pemeriksaan->int_koles ?? '-' }})</span>
        </div>
        @endif

        {{-- BAGIAN KONSULTASI (HANYA JIKA ADA) --}}
        @if($konsultasi)
            <div class="section-title">-- Konsultasi Dokter --</div>
            <div class="info-item">
                <span class="label">Keluhan:</span>
                <span class="value">{{ $konsultasi->keluhan ?? $pemeriksaan->keluhan_awal ?? 't.a.k' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Diagnosa:</span>
                <span class="value">{{ $konsultasi->diagnosa }}</span>
            </div>
            <div class="info-item">
                <span class="label">Rekomendasi:</span>
                <span class="value">{{ $konsultasi->rekomendasi }}</span>
            </div>

            {{-- Resep Obat (Hanya jika ada) --}}
            @if($konsultasi->resepObats->isNotEmpty())
                <div class="section-title">-- Resep Obat --</div>
                @foreach($konsultasi->resepObats as $resep)
                    <div class="resep-item">
                        <strong>{{ $resep->obat->nama_obat }}</strong> ({{ $resep->qty }} {{ $resep->obat->satuan }})
                        <span class="instruction">> {{ $resep->keterangan_konsumsi }}</span>
                    </div>
                @endforeach
            @endif

            {{-- Info Dokter (Hanya jika ada) --}}
            <div class="footer">
                <div class="info-item">
                    <span class="label">Pemeriksa:</span>
                    <span class="value">dr. {{ $konsultasi->dokter->first_name ?? '' }} {{ $konsultasi->dokter->last_name ?? '' }}</span>
                </div>
                <div class="info-item">
                    <span class="label">STR:</span>
                    <span class="value">{{ $konsultasi->dokter->nomor_str ?? '(Belum ada data)' }}</span>
                </div>
                <div class="info-item">
                    <span class="label">SIP:</span>
                    <span class="value">{{ $konsultasi->dokter->nomor_sip ?? '(Belum ada data)' }}</span>
                </div>                
            </div>
        @else
            <div class="section-title">-- Belum Ada Konsultasi --</div>
        @endif
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>