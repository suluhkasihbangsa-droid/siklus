<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Manajemen Pengguna</h4>
                    </div>
                    @role('admin')
                        {{-- Panggil partial tombol aksi & modal --}}
                        @include('users.partials._action-buttons')
                    @endrole
                </div>
                <div class="card-body">
                    
                    @role('admin')
                        {{-- Panggil partial form filter --}}
                        @include('users.partials._filter-form')
                    @endrole

                    @if(isset($showWarning) && $showWarning)
                        <div class="alert alert-warning text-center" role="alert">
                            <h5 class="alert-heading">Akses Terbatas</h5>
                            <p class="mb-0">Akun Anda belum ditautkan dengan organisasi manapun. Silakan hubungi Admin atau Koordinator Anda untuk mendapatkan penugasan.</p>
                        </div>
                    @else
                        @if (session('success'))
                            <div class="alert alert-success mx-4" role="alert">
                                {!! session('success') !!}  {{-- Menggunakan {!! !!} --}}
                            </div>
                        @elseif(session('error'))
                             <div class="alert alert-danger mx-4" role="alert">
                                {!! session('error') !!}  {{-- Menggunakan {!! !!} --}}
                            </div>
                        @endif

                        {{-- Panggil partial tabel --}}
                        @include('users.partials._table', [
                            'users' => $users, 
                            'sortBy' => $sortBy, 
                            'sortDirection' => $sortDirection
                        ])
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>