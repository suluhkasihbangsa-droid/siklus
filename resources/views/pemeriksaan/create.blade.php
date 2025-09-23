<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <div class="header-title">
                        <h4 class="card-title mb-0">{{ isset($pemeriksaan) ? 'Edit Data Pemeriksaan' : 'Formulir Pemeriksaan (Skrining)' }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
                        </div>
                    @endif

                    <form action="{{ isset($pemeriksaan) ? route('pemeriksaan.update', $pemeriksaan->id) : route('pemeriksaan.store') }}" method="POST" autocomplete="off">
                        @csrf
                        <input type="hidden" name="sasaran_id" id="sasaran_id" value="{{ $sasaran->id ?? '' }}">

                        {{-- ==================== BAGIAN 1: DATA SASARAN ==================== --}}
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header py-3 bg-light">
                                <h5 class="mb-0 d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill me-3" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 14px;">1</span>
                                    Data Sasaran
                                </h5>
                            </div>
                            <div class="card-body">
                                @if(isset($sasaran))
                                <div class="row g-3">
                                    <div class="col-lg-8">
                                        <label class="form-label fw-semibold">Nama Sasaran</label>
                                        <div class="form-control bg-light border-0 shadow-sm">
                                            <strong>{{ $sasaran->nama_lengkap }}</strong> 
                                            <span class="text-muted">({{ $sasaran->nomor_registrasi }})</span>
                                        </div>
                                        <div class="form-text">Sasaran dipilih dari halaman sebelumnya</div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <label for="tanggal_pemeriksaan" class="form-label fw-semibold">Tanggal Pemeriksaan</label>
                                        <input type="date" class="form-control shadow-sm" id="tanggal_pemeriksaan" name="tanggal_pemeriksaan" value="{{ old('tanggal_pemeriksaan', $pemeriksaan->tanggal_pemeriksaan ?? $tanggalSekarang) }}">
                                    </div>
                                </div>
                                
                                <div id="sasaran-info" class="info-box mt-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <span class="info-label">Nama:</span>
                                                <span class="info-value fw-medium" id="info-nama">{{ $sasaran->nama_lengkap }}</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">No. Registrasi:</span>
                                                <span class="info-value" id="info-noreg">{{ $sasaran->nomor_registrasi }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <span class="info-label">Usia Saat Periksa:</span>
                                                <span class="info-value">
                                                    <span id="info-usia" class="badge bg-info text-dark rounded-pill px-3 py-1">-</span>
                                                </span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Gender:</span>
                                                <span class="info-value">
                                                    <span class="badge bg-{{ $sasaran->gender == 'L' ? 'primary' : 'danger' }} rounded-pill px-3 py-1">
                                                        {{ $sasaran->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="text-center py-5">
                                    <div class="alert alert-warning d-inline-block">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Tidak ada sasaran yang dipilih. Silakan pilih sasaran dari halaman data sasaran terlebih dahulu.
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('sasaran.index') }}" class="btn btn-primary">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Data Sasaran
                                        </a>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- ==================== BAGIAN 2: SKRINING UMUM ==================== --}}
                        <fieldset id="skrining-umum-fieldset" {{ !isset($sasaran) ? 'disabled' : '' }}>
                            <div class="card mb-4 shadow-sm {{ !isset($sasaran) ? 'section-disabled' : '' }}">
                                <div class="card-header py-3 bg-light">
                                    <h5 class="mb-0 d-flex align-items-center">
                                        <span class="badge bg-primary rounded-pill me-3" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 14px;">2</span>
                                        Skrining Umum
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Antropometri Dasar -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-sm-6 col-lg-3">
                                            <label for="bb" class="form-label fw-semibold">Berat Badan</label>
                                            <div class="input-group shadow-sm">
                                                <input type="text" class="form-control decimal-input" name="bb" id="bb" value="{{ old('bb', $pemeriksaan->bb ?? '') }}" placeholder="0,0" style="max-width: 100px;" required>
                                                <span class="input-group-text bg-light">kg</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <label for="tb" class="form-label fw-semibold">Tinggi Badan</label>
                                            <div class="input-group shadow-sm">
                                                <input type="text" class="form-control decimal-input" name="tb" id="tb" value="{{ old('tb', $pemeriksaan->tb ?? '') }}" placeholder="0,0" style="max-width: 100px;" required>
                                                <span class="input-group-text bg-light">cm</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <label for="imt" class="form-label fw-semibold">IMT</label>
                                            <input type="text" class="form-control bg-light border-0 shadow-sm fw-medium" id="imt" placeholder="-" style="max-width: 100px;" readonly>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <label class="form-label fw-semibold">Status</label>
                                            <div class="status-display bg-light border rounded p-2 text-center shadow-sm" style="min-height: 38px; display: flex; align-items: center; justify-content: center;">
                                                <span id="status-imt" class="text-muted">-</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Pengukuran Tambahan -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4">
                                            <label for="lp" class="form-label fw-semibold">Lingkar Perut</label>
                                            <div class="input-group shadow-sm">
                                                <input type="text" class="form-control decimal-input" name="lp" id="lp" value="{{ old('lp', $pemeriksaan->lp ?? '') }}" placeholder="0,0" style="max-width: 100px;">
                                                <span class="input-group-text bg-light">cm</span>
                                            </div>
                                            <div class="form-text small" id="lp-info">Untuk usia 15 tahun ke atas</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="lila" class="form-label fw-semibold">Lingkar Lengan Atas</label>
                                            <div class="input-group shadow-sm">
                                                <input type="text" class="form-control decimal-input" name="lila" id="lila" value="{{ old('lila', $pemeriksaan->lila ?? '') }}" placeholder="0,0" style="max-width: 100px;">
                                                <span class="input-group-text bg-light">cm</span>
                                            </div>
                                            <div class="form-text small" id="lila-info">Untuk wanita usia 15+ tahun</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Tekanan Darah</label>
                                            <div class="input-group shadow-sm">
                                                <input type="number" class="form-control" name="tensi_sistolik" id="tensi_sistolik" value="{{ old('tensi_sistolik', $pemeriksaan->tensi_sistolik ?? '') }}" placeholder="120" style="max-width: 80px;">
                                                <span class="input-group-text bg-light">/</span>
                                                <input type="number" class="form-control" name="tensi_diastolik" id="tensi_diastolik" value="{{ old('tensi_diastolik', $pemeriksaan->tensi_diastolik ?? '') }}" placeholder="80" style="max-width: 80px;">
                                                <span class="input-group-text bg-light">mmHg</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Keluhan -->
                                    <div class="border-top pt-4">
                                        <label class="form-label fw-semibold mb-3">Ada Keluhan Awal?</label>
                                        <div class="d-flex gap-4 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="ada_keluhan" id="keluhan_tidak" value="tidak" checked>
                                                <label class="form-check-label fw-medium" for="keluhan_tidak">Tidak</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="ada_keluhan" id="keluhan_ya" value="ya">
                                                <label class="form-check-label fw-medium" for="keluhan_ya">Ya</label>
                                            </div>
                                        </div>
                                        <textarea name="keluhan_awal" id="keluhan_awal" class="form-control shadow-sm" rows="3" placeholder="Jelaskan keluhan awal yang dirasakan sasaran..." style="display: none;">{{ old('keluhan_awal', $pemeriksaan->keluhan_awal ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        {{-- ==================== BAGIAN 3: SKRINING LANJUTAN ==================== --}}
                        <fieldset id="skrining-lanjutan-fieldset" {{ !isset($sasaran) ? 'disabled' : '' }}>
                            <div class="card mb-4 shadow-sm {{ !isset($sasaran) ? 'section-disabled' : '' }}">
                                <div class="card-header py-3 bg-light skrining-lanjutan-toggle" data-bs-toggle="collapse" href="#collapseLanjutan" role="button" aria-expanded="false" aria-controls="collapseLanjutan" style="cursor: pointer;">
                                    <h5 class="mb-0 d-flex align-items-center justify-content-between">
                                        <span class="d-flex align-items-center">
                                            <span class="badge bg-primary rounded-pill me-3" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 14px;">3</span>
                                            Skrining Lanjutan <small class="text-muted ms-2">(Opsional)</small>
                                        </span>
                                        <i class="fas fa-chevron-down transition-all"></i>
                                    </h5>
                                </div>
                                <div class="collapse" id="collapseLanjutan">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6 col-lg-3">
                                                <label for="gd" class="form-label fw-semibold">Gula Darah</label>
                                                <div class="input-group shadow-sm">
                                                    <input type="number" class="form-control" name="gd" id="gd" value="{{ old('gd', $pemeriksaan->gd ?? '') }}" placeholder="100" style="max-width: 100px;">
                                                    <span class="input-group-text bg-light">mg/dL</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3">
                                                <label class="form-label fw-semibold">Metode Cek</label>
                                                <div class="d-flex gap-3 pt-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="mgd" id="mgd_s" value="S" {{ old('mgd', $pemeriksaan->mgd ?? 'S') == 'S' ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-medium" for="mgd_s">Sewaktu</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="mgd" id="mgd_p" value="P" {{ old('mgd', $pemeriksaan->mgd ?? '') == 'P' ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-medium" for="mgd_p">Puasa</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3">
                                                <label for="asut" class="form-label fw-semibold">Asam Urat</label>
                                                <div class="input-group shadow-sm">
                                                    <input type="text" class="form-control decimal-input" name="asut" id="asut" value="{{ old('asut', $pemeriksaan->asut ?? '') }}" placeholder="0,0" style="max-width: 100px;">
                                                    <span class="input-group-text bg-light">mg/dL</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3">
                                                <label for="koles" class="form-label fw-semibold">Kolesterol</label>
                                                <div class="input-group shadow-sm">
                                                    <input type="number" class="form-control" name="koles" id="koles" value="{{ old('koles', $pemeriksaan->koles ?? '') }}" placeholder="200" style="max-width: 100px;">
                                                    <span class="input-group-text bg-light">mg/dL</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        {{-- ==================== BAGIAN 4: REKAP HASIL ==================== --}}
                        <fieldset id="rekap-hasil-fieldset" {{ !isset($sasaran) ? 'disabled' : '' }}>
                            <div class="card mb-4 shadow-sm border-success {{ !isset($sasaran) ? 'section-disabled' : '' }}">
                                <div class="card-header py-3 bg-light-success">
                                    <h5 class="mb-0 d-flex align-items-center">
                                        <span class="badge bg-success rounded-pill me-3" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 14px;">4</span>
                                        Rekap Hasil Interpretasi
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="h6 text-primary border-bottom border-primary pb-2 mb-3">
                                                <i class="fas fa-ruler-combined me-2"></i>Pengukuran Antropometri
                                            </div>
                                            <div class="d-grid gap-3">
                                                <div id="hasil-imt" class="result-card" style="display: none;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-semibold text-dark">Status IMT</div>
                                                            <span class="interpretasi-badge" id="int-imt">-</span>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="small text-muted">IMT</div>
                                                            <div class="fw-bold" id="nilai-imt">-</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="hasil-lp" class="result-card" style="display: none;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-semibold text-dark">Lingkar Perut</div>
                                                            <span class="interpretasi-badge" id="int-lp">-</span>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="small text-muted">LP</div>
                                                            <div class="fw-bold"><span id="nilai-lp">-</span> cm</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="hasil-lila" class="result-card" style="display: none;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-semibold text-dark">LiLA</div>
                                                            <span class="interpretasi-badge" id="int-lila">-</span>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="small text-muted">LiLA</div>
                                                            <div class="fw-bold"><span id="nilai-lila">-</span> cm</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="hasil-tensi" class="result-card" style="display: none;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-semibold text-dark">Tekanan Darah</div>
                                                            <span class="interpretasi-badge" id="int-tensi">-</span>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="small text-muted">TD</div>
                                                            <div class="fw-bold"><span id="nilai-tensi">-</span> mmHg</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="h6 text-danger border-bottom border-danger pb-2 mb-3">
                                                <i class="fas fa-vial me-2"></i>Pemeriksaan Laboratorium
                                            </div>
                                            <div class="d-grid gap-3">
                                                <div id="hasil-gd" class="result-card" style="display: none;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-semibold text-dark">Gula Darah</div>
                                                            <span class="interpretasi-badge" id="int-gd">-</span>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="small text-muted">GD (<span id="metode-gd">-</span>)</div>
                                                            <div class="fw-bold"><span id="nilai-gd">-</span> mg/dL</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="hasil-asut" class="result-card" style="display: none;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-semibold text-dark">Asam Urat</div>
                                                            <span class="interpretasi-badge" id="int-asut">-</span>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="small text-muted">Asam Urat</div>
                                                            <div class="fw-bold"><span id="nilai-asut">-</span> mg/dL</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="hasil-koles" class="result-card" style="display: none;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-semibold text-dark">Kolesterol</div>
                                                            <span class="interpretasi-badge" id="int-koles">-</span>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="small text-muted">Kolesterol</div>
                                                            <div class="fw-bold"><span id="nilai-koles">-</span> mg/dL</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="no-results" class="text-center text-muted py-5">
                                        <div class="mb-3">
                                            <i class="fas fa-clipboard-list fa-3x text-muted opacity-50"></i>
                                        </div>
                                        <h6 class="text-muted">Hasil Interpretasi</h6>
                                        <p class="mb-0">Hasil interpretasi akan muncul setelah Anda mengisi data pemeriksaan</p>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        @if(isset($sasaran))
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm" id="submit-pemeriksaan">
                                <i class="fas fa-save me-2"></i>{{ isset($pemeriksaan) ? 'Update Data' : 'Simpan Pemeriksaan' }}
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
    /* Modern Form Styling */
    .card {
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .card-header {
        border-bottom: 2px solid #e9ecef;
        border-radius: 12px 12px 0 0 !important;
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.08) !important;
    }

    /* Input Field Improvements */
    .form-control, .input-group-text {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .input-group .form-control {
        border-right: none;
    }

    .input-group-text {
        border-left: none;
        background-color: #f8f9fa;
        color: #6c757d;
        font-weight: 500;
    }

    /* Badge dan Status */
    .badge {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .interpretasi-badge {
        font-weight: 600;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }

    /* Warna untuk interpretasi */
    .interpretasi-normal {
        background: linear-gradient(135deg, #d1e7dd, #a3d9a4);
        color: #0f5132;
        border: 2px solid #badbcc;
    }

    .interpretasi-warning {
        background: linear-gradient(135deg, #fff3cd, #ffecb5);
        color: #664d03;
        border: 2px solid #ffdf7e;
    }

    .interpretasi-danger {
        background: linear-gradient(135deg, #f8d7da, #f1aeb5);
        color: #842029;
        border: 2px solid #f5c2c7;
    }

    /* Result Cards */
    .result-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1.2rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .result-card:hover {
        border-color: #0d6efd;
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    /* Info Box */
    .info-box {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 1.5rem;
        border-radius: 12px;
        border: 2px solid #dee2e6;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .info-item {
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        margin-right: 1rem;
        min-width: 130px;
    }

    .info-value {
        color: #212529;
        flex: 1;
    }

    /* Section Headers */
    .bg-light-success {
        background-color: #d1e7dd !important;
    }

    /* Disabled State */
    .section-disabled {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Form Labels */
    .form-label {
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .fw-semibold {
        font-weight: 600 !important;
    }

    /* Status Display */
    .status-display {
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Transitions */
    .transition-all {
        transition: all 0.3s ease;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        
        .info-box {
            padding: 1rem;
        }
        
        .result-card {
            padding: 1rem;
        }
        
        .info-label {
            min-width: 100px;
            font-size: 0.9rem;
        }
        
        .interpretasi-badge {
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
            min-width: 70px;
        }
        
        .badge {
            width: 24px !important;
            height: 24px !important;
            font-size: 12px !important;
        }
    }

    @media (max-width: 576px) {
        .info-item {
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }
        
        .info-label {
            min-width: auto;
            margin-bottom: 0.25rem;
            margin-right: 0;
        }
        
        .result-card .d-flex {
            flex-direction: column;
            gap: 1rem;
        }
        
        .result-card .text-end {
            text-align: left !important;
        }
        
        .input-group {
            max-width: 200px;
        }
    }
    </style>
    @endpush

    @push('scripts')
    <script>
        function calculateAgeInMonths(birthDate, checkDate) {
            if (!birthDate) return 0;
            
            const birth = new Date(birthDate);
            const check = new Date(checkDate);
            let years = check.getFullYear() - birth.getFullYear();
            let months = check.getMonth() - birth.getMonth();
            
            if (check.getDate() < birth.getDate()) { 
                months--; 
            }
            if (months < 0) { 
                years--; 
                months += 12; 
            }
            
            return years * 12 + months;
        }        

        // Fungsi untuk memperbarui status field berdasarkan usia
        function updateFieldsByAge() {
            if (!selectedSasaran) return;

            const tglPemeriksaan = $('#tanggal_pemeriksaan').val();
            if (!tglPemeriksaan) {
                // Jika tanggal tidak ada, disable semua field
                disableAllAgeBasedFields();
                return;
            }
            
            const ageInMonths = calculateAgeInMonths(selectedSasaran.tgl_lahir, tglPemeriksaan);
            const ageInYears = Math.floor(ageInMonths / 12);
            const gender = selectedSasaran.gender;
            
            console.log('Usia dalam bulan:', ageInMonths, 'Usia dalam tahun:', ageInYears, 'Gender:', gender);
            
            // Update LP - minimal 15 tahun
            const lpCondition = ageInYears >= 15;
            updateFieldStatus('#lp', lpCondition, 'Lingkar Perut', 'Untuk usia minimal 15 tahun');
            
            // Update LiLA - usia 6-59 bulan ATAU wanita 15-49 tahun
            const lilaCondition = (ageInMonths >= 6 && ageInMonths <= 59) || 
                                 (gender === 'P' && ageInYears >= 15 && ageInYears <= 49);
            let lilaInfoText = '';
            if (ageInMonths >= 6 && ageInMonths <= 59) {
                lilaInfoText = 'Untuk usia 6-59 bulan';
            } else if (gender === 'P' && ageInYears >= 15 && ageInYears <= 49) {
                lilaInfoText = 'Untuk wanita usia 15-49 tahun';
            } else {
                lilaInfoText = 'Untuk usia 6-59 bulan atau wanita 15-49 tahun';
            }
            updateFieldStatus('#lila', lilaCondition, 'LiLA', lilaInfoText);
            
            // Update Tensi - minimal 15 tahun
            const tensiCondition = ageInYears >= 15;
            updateFieldStatus(['#tensi_sistolik', '#tensi_diastolik'], tensiCondition, 'Tekanan Darah', 'Untuk usia minimal 15 tahun');
            
            // Update Gula Darah - minimal 15 tahun
            const gdCondition = ageInYears >= 15;
            updateFieldStatus('#gd', gdCondition, 'Gula Darah', 'Untuk usia minimal 15 tahun');
            updateFieldStatus(['input[name="mgd"]'], gdCondition, '', '');
            
            // Update Kolesterol - minimal 15 tahun
            const kolesCondition = ageInYears >= 15;
            updateFieldStatus('#koles', kolesCondition, 'Kolesterol', 'Untuk usia minimal 15 tahun');
        }

        // Fungsi untuk mengupdate status field individual
        function updateFieldStatus(selector, condition, fieldName, infoText) {
            const selectors = Array.isArray(selector) ? selector : [selector];
            
            selectors.forEach(sel => {
                const field = $(sel);
                const infoElement = field.closest('.col-md-4, .col-md-6, .col-lg-3').find('.form-text, .small');
                
                if (condition) {
                    field.prop('disabled', false);
                    field.removeClass('bg-light text-muted');
                    if (infoElement.length) {
                        infoElement.text(infoText).removeClass('text-danger text-warning').addClass('text-muted');
                    }
                } else {
                    field.prop('disabled', true).val('');
                    field.addClass('bg-light text-muted');
                    if (infoElement.length && fieldName) {
                        infoElement.text(`${fieldName} tidak tersedia untuk usia ini`).removeClass('text-muted').addClass('text-warning');
                    }
                }
            });
        }

        // Fungsi untuk disable semua field yang bergantung pada usia
        function disableAllAgeBasedFields() {
            const fields = ['#lp', '#lila', '#tensi_sistolik', '#tensi_diastolik', '#gd', 'input[name="mgd"]', '#koles'];
            
            fields.forEach(selector => {
                updateFieldStatus(selector, false, '', 'Pilih tanggal pemeriksaan terlebih dahulu');
            });
        }                

        $(document).ready(function() {
            // Variabel global
            let selectedSasaran = @json($sasaran ?? null);
            let interpretasiTimeout = null;

            // Fungsi untuk menangani input desimal dengan koma
            function handleDecimalInput(input) {
                let value = input.val();
                // Ganti koma dengan titik untuk proses internal
                let normalizedValue = value.replace(',', '.');
                
                // Simpan posisi kursor
                let cursorPos = input[0].selectionStart;
                
                // Update nilai di input (tampilkan dengan koma untuk user)
                let displayValue = normalizedValue.replace('.', ',');
                if (displayValue !== value) {
                    input.val(displayValue);
                    // Restore posisi kursor
                    input[0].setSelectionRange(cursorPos, cursorPos);
                }
                
                return parseFloat(normalizedValue) || 0;
            }

            // Event handler untuk semua input decimal
            $('.decimal-input').on('input', function() {
                handleDecimalInput($(this));
            });

            // Fungsi untuk menormalkan nilai sebelum mengirim ke server
            function normalizeDecimalValue(value) {
                if (typeof value === 'string') {
                    return value.replace(',', '.');
                }
                return value;
            }

            // Fungsi untuk menghitung usia dalam teks
            function calculateAgeText(birthDate, checkDate) {
                if (!birthDate) return '-';
                
                const birth = new Date(birthDate);
                const check = new Date(checkDate);
                let years = check.getFullYear() - birth.getFullYear();
                let months = check.getMonth() - birth.getMonth();
                
                if (check.getDate() < birth.getDate()) { 
                    months--; 
                }
                if (months < 0) { 
                    years--; 
                    months += 12; 
                }
                
                const totalMonths = years * 12 + months;
                if (totalMonths < 60) return `${totalMonths} bulan`;
                if (totalMonths < 228) return `${years} tahun, ${months} bulan`;
                return `${years} tahun`;
            }

            // Fungsi untuk memperbarui info sasaran
            function updateSasaranInfo() {
                if (!selectedSasaran) return;

                const tglPemeriksaan = $('#tanggal_pemeriksaan').val();
                if (!tglPemeriksaan) return;
                
                const usia = calculateAgeText(selectedSasaran.tgl_lahir, tglPemeriksaan);
                $('#info-usia').text(usia);
                
                const tglLahir = new Date(selectedSasaran.tgl_lahir);
                const tglPeriksa = new Date(tglPemeriksaan);
                const totalYears = Math.floor((tglPeriksa - tglLahir) / (1000 * 60 * 60 * 24 * 365.25));
                
                $('#lp').prop('disabled', totalYears < 15);
                $('#lp-info').text(totalYears < 15 ? 'Tidak tersedia (usia < 15 thn)' : 'Untuk usia 15 tahun ke atas').toggleClass('text-warning', totalYears < 15);

                const lilaDisabled = !(selectedSasaran.gender === 'P' && totalYears >= 15);
                $('#lila').prop('disabled', lilaDisabled);
                $('#lila-info').text(lilaDisabled ? 'Hanya untuk wanita (usia >= 15 thn)' : 'Untuk wanita usia 15+ tahun').toggleClass('text-warning', lilaDisabled);
            }

            // Event listener untuk tanggal pemeriksaan
            $('#tanggal_pemeriksaan').on('change', function() {
                updateSasaranInfo();
                calculateIMT();
            });
            
            // Event listener untuk keluhan
            $('input[name="ada_keluhan"]').on('change', function() {
                $('#keluhan_awal').toggle(this.value === 'ya');
            });

            // Fungsi untuk menghitung IMT
            function calculateIMT() {
                const bb = handleDecimalInput($('#bb'));
                const tb = handleDecimalInput($('#tb'));
                
                if (bb > 0 && tb > 0) {
                    const tbMeter = tb / 100;
                    const imt = bb / (tbMeter * tbMeter);
                    $('#imt').val(imt.toFixed(2).replace('.', ','));
                    $('#status-imt').text('Menghitung...');
                    getInterpretasi();
                } else {
                    $('#imt').val('');
                    $('#status-imt').text('-');
                    updateRekapHasil();
                }
            }

            // Event listener untuk input BB dan TB
            $('#bb, #tb').on('input', function() {
                calculateIMT();
                clearTimeout(interpretasiTimeout);
                interpretasiTimeout = setTimeout(getInterpretasi, 500);
            });

            // Event listener untuk input yang memerlukan interpretasi
            $('#lp, #lila, #tensi_sistolik, #tensi_diastolik, #gd, #asut, #koles').on('input', function() {
                clearTimeout(interpretasiTimeout);
                interpretasiTimeout = setTimeout(getInterpretasi, 500);
            });

            // Event listener untuk radio button metode gula darah
            $('input[name="mgd"]').on('change', function() {
                getInterpretasi();
            });

            // Fungsi untuk mendapatkan interpretasi dari server
            function getInterpretasi() {
                if (!selectedSasaran) {
                    console.log('Tidak ada sasaran yang dipilih');
                    return;
                }
                
                const formData = {
                    sasaran_id: selectedSasaran.id,
                    tanggal_pemeriksaan: $('#tanggal_pemeriksaan').val(),
                    bb: normalizeDecimalValue($('#bb').val()),
                    tb: normalizeDecimalValue($('#tb').val()),
                    imt: normalizeDecimalValue($('#imt').val()),
                    lp: normalizeDecimalValue($('#lp').val()),
                    lila: normalizeDecimalValue($('#lila').val()),
                    tensi_sistolik: $('#tensi_sistolik').val(),
                    tensi_diastolik: $('#tensi_diastolik').val(),
                    gd: $('#gd').val(),
                    mgd: $('input[name="mgd"]:checked').val(),
                    asut: normalizeDecimalValue($('#asut').val()),
                    koles: $('#koles').val()
                };

                console.log('Mengirim data interpretasi:', formData);

                $.ajax({
                    url: "{{ route('ajax.getInterpretasi') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Response interpretasi:', response);
                        updateRekapHasil(response);
                        
                        // Update status IMT di bagian skrining umum
                        if (response.int_imt && response.int_imt !== '-') {
                            $('#status-imt').text(response.int_imt);
                        } else {
                            $('#status-imt').text('-');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error mendapatkan interpretasi:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                        $('#status-imt').text('Error');
                    }
                });
            }

            // Fungsi untuk memperbarui bagian Rekap Hasil
            function updateRekapHasil(interpretasi = null) {
                let hasResults = false;

                // Update IMT
                const imtVal = $('#imt').val();
                if (imtVal && imtVal !== '' && interpretasi && interpretasi.int_imt && interpretasi.int_imt !== '-') {
                    $('#hasil-imt').show();
                    $('#nilai-imt').text(imtVal);
                    updateInterpretasiDisplay('int-imt', interpretasi.int_imt);
                    hasResults = true;
                } else {
                    $('#hasil-imt').hide();
                }

                // Update LP
                const lpVal = $('#lp').val();
                if (lpVal && lpVal !== '' && interpretasi && interpretasi.int_lp && interpretasi.int_lp !== '-') {
                    $('#hasil-lp').show();
                    $('#nilai-lp').text(lpVal.replace('.', ','));
                    updateInterpretasiDisplay('int-lp', interpretasi.int_lp);
                    hasResults = true;
                } else {
                    $('#hasil-lp').hide();
                }

                // Update LiLA
                const lilaVal = $('#lila').val();
                if (lilaVal && lilaVal !== '' && interpretasi && interpretasi.int_lila && interpretasi.int_lila !== '-') {
                    $('#hasil-lila').show();
                    $('#nilai-lila').text(lilaVal.replace('.', ','));
                    updateInterpretasiDisplay('int-lila', interpretasi.int_lila);
                    hasResults = true;
                } else {
                    $('#hasil-lila').hide();
                }

                // Update Tensi
                const sistolik = $('#tensi_sistolik').val();
                const diastolik = $('#tensi_diastolik').val();
                if (sistolik && diastolik && interpretasi && interpretasi.int_tensi && interpretasi.int_tensi !== '-') {
                    $('#hasil-tensi').show();
                    $('#nilai-tensi').text(sistolik + '/' + diastolik);
                    updateInterpretasiDisplay('int-tensi', interpretasi.int_tensi);
                    hasResults = true;
                } else {
                    $('#hasil-tensi').hide();
                }

                // Update Gula Darah
                const gdVal = $('#gd').val();
                const mgdVal = $('input[name="mgd"]:checked').val();
                if (gdVal && gdVal !== '' && interpretasi && interpretasi.int_gd && interpretasi.int_gd !== '-') {
                    $('#hasil-gd').show();
                    $('#nilai-gd').text(gdVal);
                    $('#metode-gd').text(mgdVal === 'S' ? 'Sewaktu' : 'Puasa');
                    updateInterpretasiDisplay('int-gd', interpretasi.int_gd);
                    hasResults = true;
                } else {
                    $('#hasil-gd').hide();
                }

                // Update Asam Urat
                const asutVal = $('#asut').val();
                if (asutVal && asutVal !== '' && interpretasi && interpretasi.int_asut && interpretasi.int_asut !== '-') {
                    $('#hasil-asut').show();
                    $('#nilai-asut').text(asutVal.replace('.', ','));
                    updateInterpretasiDisplay('int-asut', interpretasi.int_asut);
                    hasResults = true;
                } else {
                    $('#hasil-asut').hide();
                }

                // Update Kolesterol
                const kolesVal = $('#koles').val();
                if (kolesVal && kolesVal !== '' && interpretasi && interpretasi.int_koles && interpretasi.int_koles !== '-') {
                    $('#hasil-koles').show();
                    $('#nilai-koles').text(kolesVal);
                    updateInterpretasiDisplay('int-koles', interpretasi.int_koles);
                    hasResults = true;
                } else {
                    $('#hasil-koles').hide();
                }

                // Show/hide no results message
                if (hasResults) {
                    $('#no-results').hide();
                } else {
                    $('#no-results').show();
                }
            }

            // Fungsi untuk memperbarui tampilan interpretasi dengan styling yang sesuai
            function updateInterpretasiDisplay(elementId, interpretasi) {
                const element = $(`#${elementId}`);
                if (!interpretasi || interpretasi === '-' || interpretasi === 'Tidak Terdefinisi') {
                    element.text('-').removeClass('interpretasi-normal interpretasi-warning interpretasi-danger');
                    return;
                }
                
                element.text(interpretasi);
                
                // Hapus semua kelas styling sebelumnya
                element.removeClass('interpretasi-normal interpretasi-warning interpretasi-danger');
                
                // Tambahkan kelas styling berdasarkan jenis interpretasi
                const interpretasiLower = interpretasi.toLowerCase();
                if (interpretasiLower.includes('normal')) {
                    element.addClass('interpretasi-normal');
                } else if (interpretasiLower.includes('kurang') || interpretasiLower.includes('prediabetes') || interpretasiLower.includes('gemuk')) {
                    element.addClass('interpretasi-warning');
                } else if (interpretasiLower.includes('obesitas') || interpretasiLower.includes('diabetes') || interpretasiLower.includes('tinggi') || interpretasiLower.includes('hipertensi') || interpretasiLower.includes('buruk')) {
                    element.addClass('interpretasi-danger');
                }
            }

            // Normalisasi nilai decimal sebelum submit form
            $('form').on('submit', function() {
                $('.decimal-input').each(function() {
                    const normalizedValue = normalizeDecimalValue($(this).val());
                    $(this).val(normalizedValue);
                });
            });

            // === INISIALISASI AWAL ===
            @if(isset($sasaran))
                // Update info sasaran saat halaman dimuat
                updateSasaranInfo();
                
                // Hitung IMT jika ada nilai awal dari old input
                const bbValue = "{{ old('bb', '') }}";
                const tbValue = "{{ old('tb', '') }}";
                
                if (bbValue !== '' && tbValue !== '') {
                    $('#bb').val(bbValue.replace('.', ','));
                    $('#tb').val(tbValue.replace('.', ','));
                    calculateIMT();
                }
                
                // Set nilai lain dari old input dengan format koma
                const lpValue = "{{ old('lp', '') }}";
                const lilaValue = "{{ old('lila', '') }}";
                const asutValue = "{{ old('asut', '') }}";
                
                if (lpValue !== '') $('#lp').val(lpValue.replace('.', ','));
                if (lilaValue !== '') $('#lila').val(lilaValue.replace('.', ','));
                if (asutValue !== '') $('#asut').val(asutValue.replace('.', ','));
                
                // Jika ada nilai dari old input, trigger interpretasi
                @if(old('bb') || old('tb') || old('lp') || old('lila') || old('tensi_sistolik') || old('gd') || old('asut') || old('koles'))
                    setTimeout(getInterpretasi, 1000);
                @endif
            @endif

            // Inisialisasi tampilan rekap hasil
            updateRekapHasil();
        });
    </script>
    @endpush
</x-app-layout>