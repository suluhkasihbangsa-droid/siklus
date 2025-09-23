@php
    $id = $data->id ?? null;
    
    // FIX: Gunakan 01.png sebagai default, ini akan mengatasi error 404 placeholder.png
    $profileImage = asset('images/avatars/01.png');

    if ($id) {
        $gender = $data->userProfile->gender ?? 'L'; // Default ke Laki-laki jika null

        if ($data->hasRole('admin')) {
            $profileImage = asset('images/avatars/01.png');
        } elseif ($data->hasRole('dokter')) {
            $profileImage = ($gender == 'P') ? asset('images/avatars/4.jpg') : asset('images/avatars/3.jpg');
        } elseif ($data->hasRole(['user', 'koorUser'])) {
            $profileImage = ($gender == 'P') ? asset('images/avatars/2.jpg') : asset('images/avatars/1.jpg');
        }
    }
@endphp

@if(isset($id))
    {!! Form::model($data, ['route' => ['users.update', $id], 'method' => 'patch' , 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::open(['route' => ['users.store'], 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
@endif

<div class="row">
    {{-- KOLOM KIRI: FOTO PROFIL & INFO DASAR --}}
    <div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title"><h4 class="card-title">{{$id ? 'Edit' : 'Tambah' }} Pengguna</h4></div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="profile-img-edit position-relative">
                        <img src="{{ $profileImage }}" alt="User-Profile" class="profile-pic rounded avatar-100">
                        <div class="upload-icone bg-primary">
                            <svg class="upload-button" width="14" height="14" viewBox="0 0 24 24"><path fill="#ffffff" d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" /></svg>
                            <input class="file-upload" type="file" accept="image/*" name="profile_image">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Role Pengguna: <span class="text-danger">*</span></label>
                    {{ Form::select('role', $roles, $data->roles->first()->name ?? null, ['class' => 'form-select' . ($errors->has('role') ? ' is-invalid' : ''), 'id' => 'role-select', 'required']) }}
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                 <div class="form-group">
                    <label class="form-label">Organisasi: <span class="text-danger">*</span></label>
                    {{ Form::select('organisasi_ids[]', $organisasis, $data->organisasis->pluck('id')->toArray() ?? [], ['class' => 'form-select', 'id' => 'organisasi-select', 'multiple' => 'multiple']) }}
                    @error('organisasi_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
                 </div>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: DETAIL INFORMASI PENGGUNA --}}
    <div class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title"><h4 class="card-title">Informasi Pengguna</h4></div>
                <div class="card-action"><a href="{{route('users.index')}}" class="btn btn-sm btn-primary" role="button">Kembali</a></div>
            </div>
            <div class="card-body">
                <div class="new-user-info">
                    <div class="row">
                        <div class="form-group col-md-6 col-sm-12">
                            <label class="form-label">Nama Depan: <span class="text-danger">*</span></label>
                            {{ Form::text('first_name', null, ['class' => 'form-control' . ($errors->has('first_name') ? ' is-invalid' : ''), 'placeholder' => 'Nama Depan', 'required']) }}
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label class="form-label">Nama Belakang: <span class="text-danger">*</span></label>
                            {{ Form::text('last_name', null, ['class' => 'form-control' . ($errors->has('last_name') ? ' is-invalid' : ''), 'placeholder' => 'Nama Belakang' ,'required']) }}
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="form-group col-md-6 col-sm-12">
                            <label class="form-label">Jenis Kelamin: <span class="text-danger">*</span></label>
                            {{ Form::select('userProfile[gender]', ['L' => 'Laki-laki', 'P' => 'Perempuan'], null, ['class' => 'form-select' . ($errors->has('userProfile.gender') ? ' is-invalid' : ''), 'placeholder' => 'Pilih Jenis Kelamin', 'required']) }}
                            @error('userProfile.gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label class="form-label">Username: <span class="text-danger">*</span></label>
                            {{ Form::text('username', null, ['class' => 'form-control' . ($errors->has('username') ? ' is-invalid' : ''), 'required', 'placeholder' => 'Username']) }}
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-12">
                            <label class="form-label">Email: <span class="text-danger">*</span></label>
                            {{ Form::email('email', null, ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'placeholder' => 'Email', 'required']) }}
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="form-group col-md-6 col-sm-12">
                            <label class="form-label">Nomor HP:</label>
                            {{ Form::text('userProfile[phone_number]', null, ['class' => 'form-control' . ($errors->has('userProfile.phone_number') ? ' is-invalid' : ''), 'placeholder' => 'Contoh: 081234567890']) }}
                            @error('userProfile.phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <hr>
                    <h5 class="mb-3">Keamanan</h5>
                    <div class="row">
                        <div class="form-group col-md-6 col-sm-12">
                            <label class="form-label">Password: @if($id)<small class="text-muted">(Kosongkan jika tidak diubah)</small>@endif</label>
                            {{ Form::password('password', ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'placeholder' => 'Password']) }}
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-6 col-sm-12">
                            <label class="form-label">Konfirmasi Password:</label>
                            {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Konfirmasi Password']) }}
                        </div>
                    </div>
                     <hr id="dokter-separator" style="display: none;">
                    <h5 class="mb-3" id="dokter-header" style="display: none;">Informasi Dokter</h5>
                    <div class="row" id="dokter-fields" style="display: none;">
                         <div class="form-group col-md-6 col-sm-12">
                            <label class="form-label">Nomor STR:</label>
                            {{ Form::text('nomor_str', null, ['class' => 'form-control' . ($errors->has('nomor_str') ? ' is-invalid' : ''), 'placeholder' => 'Nomor STR']) }}
                            @error('nomor_str') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                         <div class="form-group col-md-6 col-sm-12">
                            <label class="form-label">Nomor SIP:</label>
                            {{ Form::text('nomor_sip', null, ['class' => 'form-control' . ($errors->has('nomor_sip') ? ' is-invalid' : ''), 'placeholder' => 'Nomor SIP']) }}
                            @error('nomor_sip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">{{$id ? 'Update' : 'Tambah' }} Pengguna</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi Choices.js untuk select organisasi
        if (document.getElementById('organisasi-select')) {
            new Choices('#organisasi-select', { removeItemButton: true });
        }
        
        const roleSelect = document.getElementById('role-select');
        const dokterFields = document.getElementById('dokter-fields');
        const dokterHeader = document.getElementById('dokter-header');
        const dokterSeparator = document.getElementById('dokter-separator');
        
        function toggleDokterFields() {
            if(roleSelect.value === 'dokter') {
                dokterFields.style.display = 'flex'; // 'flex' agar row berfungsi
                dokterHeader.style.display = 'block';
                dokterSeparator.style.display = 'block';
            } else {
                dokterFields.style.display = 'none';
                dokterHeader.style.display = 'none';
                dokterSeparator.style.display = 'none';
            }
        }

        // Jalankan saat halaman dimuat & saat role diganti
        toggleDokterFields();
        roleSelect.addEventListener('change', toggleDokterFields);
    });
</script>
@endpush