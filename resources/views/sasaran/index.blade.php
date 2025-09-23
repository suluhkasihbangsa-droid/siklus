<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Sasaran (Pasien)</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
        }
        .btn-modern {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
        }
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .header-title h4 {
            font-weight: 600;
            color: #333;
        }
        .alert-modern {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        @media (max-width: 576px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .action-buttons {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <x-app-layout>
        {{-- 1. Sertakan CSS khusus untuk print --}}
        @include('sasaran.partials._print-styles')
        
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-white">
                                        <div class="header-title">
                                            <h4 class="card-title mb-0"><i class="fas fa-users me-2"></i>Data Sasaran (Pasien)</h4>
                                        </div>
                                        @if(auth()->user()->hasRole('admin') || !isset($showWarning))
                                        <div class="action-buttons">
                                            <!-- Tombol untuk Scan QR -->
                                            <a href="{{ route('sasaran.createFromQr') }}" class="btn btn-modern btn-success">
                                                <i class="fas fa-qrcode me-1"></i> Scan QR
                                            </a>

                                            <!-- Tombol untuk Tambah Manual -->
                                            <a href="{{ route('sasaran.create') }}" class="btn btn-modern btn-primary">
                                                <i class="fas fa-plus-circle me-1"></i> Tambah
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        @if(isset($showWarning) && $showWarning)
                                            <div class="alert alert-warning alert-modern text-center" role="alert">
                                                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Akses Terbatas</h5>
                                                <p class="mb-0">Akun Anda belum ditautkan dengan organisasi manapun. Silakan hubungi Admin atau Koordinator Anda untuk mendapatkan penugasan.</p>
                                            </div>
                                        @else
                                            @if (session('success'))
                                                <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
                                                    <i class="fas fa-check-circle me-2"></i>
                                                    {!! session('success') !!}
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            @endif
                                            
                                            {{-- 2. Sertakan formulir filter --}}
                                            @include('sasaran.partials._filter-form')

                                            {{-- 3. Sertakan tabel data --}}
                                            @include('sasaran.partials._table')
                                            
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Sertakan semua modal --}}
        @include('sasaran.partials._modal-detail')
        @include('sasaran.partials._modal-hasil')
        @include('sasaran.partials._modal-success')

        {{-- Memuat library eksternal tetap di sini atau di layout utama --}}
        @push('scripts')
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
            <!-- Bootstrap & Popper.js -->
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
        @endpush
    </x-app-layout>
</body>
</html>