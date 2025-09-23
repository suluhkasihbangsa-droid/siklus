<x-guest-layout>
    <section class="login-content">
        <div class="row m-0 bg-white">
            <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 overflow-hidden">
                <img src="{{asset('images/auth/05.png')}}" class="img-fluid gradient-main" alt="images" style="object-fit: cover; height: 100%; min-height: 1024px;">
            </div>
            <div class="col-md-6 py-5">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                            <div class="card-body">
                                <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center mb-3">
                                    <svg width="30" class="text-primary" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2" transform="rotate(-45 -0.757324 19.2427)" fill="currentColor"/>
                                        <rect x="7.72803" y="27.728" width="28" height="4" rx="2" transform="rotate(-45 7.72803 27.728)" fill="currentColor"/>
                                        <rect x="10.5366" y="16.3945" width="16" height="4" rx="2" transform="rotate(45 10.5366 16.3945)" fill="currentColor"/>
                                        <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2" transform="rotate(45 10.5562 -0.556152)" fill="currentColor"/>
                                    </svg>
                                    <h4 class="logo-title ms-3">{{env('APP_NAME')}}</h4>
                                </a>
                                <h2 class="mb-2 text-center">Pendaftaran Mandiri</h2>
                                <p class="text-center">Silakan isi data Anda untuk mendapatkan QR Code pendaftaran.</p>

                                <p>Silakan isi data diri dan alamat sasaran (pasien) dengan lengkap pada form di bawah ini.</p>

                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        <p class="mb-0"><strong>Terdapat kesalahan pada input Anda:</strong></p>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- 1. UBAH ACTION FORM --}}
                                <form action="{{ route('public.register.generateQr') }}" method="POST" id="sasaranForm">
                                    @csrf
                                    
                                    {{-- DATA DIRI --}}
                                    <h5 class="mb-3">Data Diri</h5>
                                    
                                    <div class="form-group">
                                        <label class="form-label">NIK</label>
                                        <div id="nik-toggle-section">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="toggle-nik-input">
                                                <i class="fas fa-id-card me-2"></i>Input NIK - Klik disini
                                            </button>
                                        </div>
                                        <div id="nik-input-section" style="display: none;">
                                            <div id="nik-input-container" class="d-flex flex-wrap mt-2" style="gap: 5px; max-width: 560px;">
                                                @for ($i = 1; $i <= 16; $i++)
                                                    <input type="tel" class="form-control nik-digit-input text-center p-0" id="nik-input-{{$i}}" maxlength="1" pattern="[0-9]">
                                                @endfor
                                            </div>
                                            <small class="form-text text-muted">Kursor akan berpindah otomatis.</small>
                                        </div>
                                        <input type="hidden" name="nik" id="nik_hidden">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label" for="nama_lengkap">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', '') }}" required placeholder="Masukkan nama lengkap sesuai KTP" oninput="this.value = this.value.toUpperCase()">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="tgl_lahir_dd">Tanggal Lahir</label>
                                                <div class="d-flex align-items-center">
                                                    <input type="tel" class="form-control text-center" id="tgl_lahir_dd" placeholder="DD" maxlength="2">
                                                    <span class="mx-2">/</span>
                                                    <input type="tel" class="form-control text-center" id="tgl_lahir_mm" placeholder="MM" maxlength="2">
                                                    <span class="mx-2">/</span>
                                                    <input type="tel" class="form-control text-center" id="tgl_lahir_yyyy" placeholder="YYYY" maxlength="4">
                                                </div>
                                                <input type="hidden" name="tgl_lahir" id="tgl_lahir_hidden">
                                                <div id="tgl_lahir_error" class="text-danger mt-1" style="font-size: 0.875em;"></div>
                                                <div id="display_usia" class="form-text text-primary mt-1 fw-bold"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label d-block">Gender</label>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender" id="gender_l" value="L" {{ old('gender') == 'L' ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="gender_l">Laki-laki</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender" id="gender_p" value="P" {{ old('gender') == 'P' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="gender_p">Perempuan</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label" for="no_hp">No. Handphone</label>
                                        <input type="tel" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp', '') }}" placeholder="Contoh: 0812 3456 7890" minlength="10" maxlength="15">
                                        <small class="form-text text-muted">Minimal 10 digit, maksimal 13 digit</small>
                                        <div id="no_hp_error" class="text-danger mt-1" style="font-size: 0.875em;"></div>
                                    </div>

                                    <hr>
                                    {{-- ALAMAT DOMISILI --}}
                                    <h5 class="mb-3">Alamat Domisili</h5>
                                    <div class="form-group">
                                        <label class="form-label" for="provinsi_id">Provinsi</label>
                                        <select class="form-select" id="provinsi_id" name="provinsi_id" required>
                                            <option value="">Pilih Provinsi</option>
                                            @foreach ($provinsis as $provinsi)
                                                <option value="{{ $provinsi->id }}" {{ old('provinsi_id') == $provinsi->id ? 'selected' : '' }}>
                                                    {{ $provinsi->nama_provinsi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="kota_id">Kota/Kabupaten</label>
                                        <select class="form-select" id="kota_id" name="kota_id" required>
                                            <option value="">Pilih Provinsi Terlebih Dahulu</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="kecamatan_id">Kecamatan</label>
                                        <select class="form-select" id="kecamatan_id" name="kecamatan_id" required>
                                            <option value="">Pilih Kota Terlebih Dahulu</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="kelurahan_id">Kelurahan/Desa</label>
                                        <select class="form-select" id="kelurahan_id" name="kelurahan_id" required>
                                            <option value="">Pilih Kecamatan Terlebih Dahulu</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="alamat_detail">Detail Alamat (RT/RW/Jalan)</label>
                                        <textarea class="form-control" id="alamat_detail" name="alamat_detail" rows="3" placeholder="Contoh: Jl. Merdeka No. 1, RT 01 RW 05">{{ old('alamat_detail', '') }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary mt-3" id="submitBtn">
                                        Dapatkan QR Code Saya
                                    </button>
                                </form>
                                @include('sasaran.partials._form-scripts')

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>

