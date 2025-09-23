{{-- resources/views/sasaran/partials/_form.blade.php --}}
<div class="card-body">
    <p>
        @if(isset($sasaran))
            Anda sedang mengubah data untuk: <strong>{{ $sasaran->nama_lengkap }}</strong>
        @else
            Silakan isi data diri dan alamat sasaran (pasien) dengan lengkap pada form di bawah ini.
        @endif
    </p>

    <!-- Notifikasi Sukses -->
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {!! session('success') !!}
        </div>
    @endif

    <!-- Menampilkan Error Validasi -->
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

    <form action="{{ isset($sasaran) ? route('sasaran.update', $sasaran->id) : route('sasaran.store') }}" method="POST" id="sasaranForm">
        @csrf
        @if(isset($sasaran))
            @method('PUT')
        @endif
        
        {{-- DATA DIRI --}}
        <h5 class="mb-3">Data Diri</h5>
        
        {{-- NIK Section with Toggle --}}
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
                <small class="form-text text-muted">Kursor akan berpindah otomatis. Klik tombol di atas untuk menyembunyikan.</small>
            </div>
            <input type="hidden" name="nik" id="nik_hidden">
        </div>

        <div class="form-group">
            <label class="form-label" for="nama_lengkap">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                   value="{{ old('nama_lengkap', isset($sasaran) ? $sasaran->nama_lengkap : '') }}" 
                   required placeholder="Masukkan nama lengkap sesuai KTP" 
                   oninput="this.value = this.value.toUpperCase()">
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
                        <input class="form-check-input" type="radio" name="gender" id="gender_l" value="L" 
                               {{ old('gender', isset($sasaran) ? $sasaran->gender : '') == 'L' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="gender_l">Laki-laki</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender_p" value="P" 
                               {{ old('gender', isset($sasaran) ? $sasaran->gender : '') == 'P' ? 'checked' : '' }}>
                        <label class="form-check-label" for="gender_p">Perempuan</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="no_hp">No. Handphone</label>
            <input type="tel" class="form-control" id="no_hp" name="no_hp" 
                   value="{{ old('no_hp', isset($sasaran) ? $sasaran->no_hp : '') }}" 
                   placeholder="Contoh: 0812 3456 7890" 
                   minlength="10" maxlength="15">
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
                    <option value="{{ $provinsi->id }}" 
                            {{ old('provinsi_id', isset($sasaran) ? $sasaran->provinsi_id : '') == $provinsi->id ? 'selected' : '' }}>
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
            <textarea class="form-control" id="alamat_detail" name="alamat_detail" rows="3" 
                      placeholder="Contoh: Jl. Merdeka No. 1, RT 01 RW 05">{{ old('alamat_detail', isset($sasaran) ? $sasaran->alamat_detail : '') }}</textarea>
        </div>

        <hr>
        {{-- PENDAFTARAN ORGANISASI --}}
        <h5 class="mb-3">Pendaftaran Organisasi</h5>
        <div class="form-group">
            <label class="form-label" for="organisasi_induk_id">Organisasi Induk</label>
            <div class="input-group">
                <select class="form-select" id="organisasi_induk_id" name="organisasi_induk_id" required>
                    <option value="">Pilih Organisasi Induk</option>
                    @foreach ($organisasi_induk as $induk)
                        <option value="{{ $induk->id }}">{{ $induk->nama_organisasi }}</option>
                    @endforeach
                </select>
                {{-- FIX: Tombol ini hanya akan muncul untuk admin --}}
                @role('admin')
                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#addOrganisasiModal">+</button>
                @endrole
            </div>
        </div>
        <div class="form-group" id="sub_organisasi_container" style="display: none;">
            <label class="form-label" for="sub_organisasi_id">Sub Organisasi (Opsional)</label>
            <select class="form-select" id="sub_organisasi_id">
                <option value="">Pilih Sub Organisasi</option>
            </select>
        </div>
        <input type="hidden" name="organisasi_id" id="organisasi_id_hidden">

        <button type="submit" class="btn btn-primary mt-3" id="submitBtn">
            {{ isset($sasaran) ? 'Simpan Perubahan' : 'Simpan Data' }}
        </button>
        <a href="{{ route('sasaran.index') }}" class="btn btn-danger mt-3">Batal</a>
    </form>
</div>

{{-- FIX: Modal ini hanya akan muncul untuk admin --}}
@role('admin')
<!-- Modal Tambah Organisasi -->
<div class="modal fade" id="addOrganisasiModal" tabindex="-1" aria-labelledby="addOrganisasiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrganisasiModalLabel">Tambah Organisasi Cepat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-error" class="alert alert-danger" style="display: none;"></div>
                <form id="quick-add-organisasi-form">
                    <div class="form-group">
                        <label class="form-label" for="modal_nama_organisasi_induk">Nama Organisasi Induk</label>
                        <input type="text" class="form-control" name="nama_organisasi_induk" id="modal_nama_organisasi_induk" required>
                    </div>
                    <hr>
                    <div id="modal-sub-organisasi-wrapper">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Sub Organisasi (Opsional)</h6>
                            <button type="button" id="modal-add-sub" class="btn btn-sm btn-success">+</button>
                        </div>
                        <div id="modal-sub-fields"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="save-quick-organisasi">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endrole

@include('sasaran.partials._form-scripts')