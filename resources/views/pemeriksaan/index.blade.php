<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Riwayat Pemeriksaan</h4>
                </div>
                <div class="card-body">
                    {{-- Form Filter --}}
                    <form action="{{ route('pemeriksaan.filter') }}" method="POST" id="filterForm" class="mb-4">
                        @csrf
                        <div class="row gy-3 align-items-end">

                            {{-- Input Pencarian --}}
                            <div class="col-md-4 col-12">
                                <label for="search_term" class="form-label">Cari Nama / No. Registrasi</label>
                                <input type="text" name="search_term" id="search_term" class="form-control" placeholder="Ketik di sini...">
                            </div>

                            {{-- Filter Organisasi --}}
                            <div class="col-md-4 col-12">
                                <label for="organisasi_induk_id" class="form-label">Filter Organisasi</label>
                                <select name="organisasi_induk_id" id="organisasi_induk_id" class="form-select">
                                    <option value="">-- Semua Organisasi --</option>
                                    @foreach($organisasi_induk_list as $induk)
                                        <option value="{{ $induk->id }}">{{ $induk->nama_organisasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Filter Sub-Organisasi --}}
                            <div class="col-md-4 col-12" id="sub_organisasi_container" style="display: none;">
                                <label for="sub_organisasi_id" class="form-label">Filter Sub-Organisasi</label>
                                <select name="sub_organisasi_id" id="sub_organisasi_id" class="form-select">
                                    {{-- Opsi akan diisi oleh JavaScript --}}
                                </select>
                            </div>

                            {{-- Filter Rentang Tanggal --}}
                            <div class="col-md-3 col-sm-6 col-12">
                                <label for="tanggal_mulai" class="form-label">Dari Tanggal</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control">
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <label for="tanggal_selesai" class="form-label">Sampai Tanggal</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control">
                            </div>

                            {{-- Filter Lanjutan --}}
                            <div class="col-12 mt-2">
                                <a class="text-primary fw-semibold" data-bs-toggle="collapse" href="#filterLanjutan" role="button" aria-expanded="false">
                                    + Filter Lanjutan (IMT, Tensi, Gula Darah, dll.)
                                </a>
                            </div>

                            <div class="collapse col-12" id="filterLanjutan">
                                <div class="card card-body mt-2">
                                    <div class="row gy-3">
                                        <div class="col-md-4 col-sm-6 col-12">
                                            <label class="form-label">Status IMT</label>
                                            <select name="int_imt" class="form-select">
                                                <option value="">Semua</option>
                                                @foreach($filter_options['imt'] as $opt)
                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-sm-6 col-12">
                                            <label class="form-label">Status Tensi</label>
                                            <select name="int_tensi" class="form-select">
                                                <option value="">Semua</option>
                                                @foreach($filter_options['tensi'] as $opt)
                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-sm-6 col-12">
                                            <label class="form-label">Status Gula Darah</label>
                                            <select name="int_gd" class="form-select">
                                                <option value="">Semua</option>
                                                @foreach($filter_options['gula'] as $opt)
                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-sm-6 col-12">
                                            <label class="form-label">Status Kolesterol</label>
                                            <select name="int_koles" class="form-select">
                                                <option value="">Semua</option>
                                                @foreach($filter_options['kolesterol'] as $opt)
                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-sm-6 col-12">
                                            <label class="form-label">Status Asam Urat</label>
                                            <select name="int_asut" class="form-select">
                                                <option value="">Semua</option>
                                                @foreach($filter_options['asut'] as $opt)
                                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Aksi - Diperbaiki untuk tampilan mobile --}}
                            <div class="col-12 mt-3">
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <a href="{{ route('pemeriksaan.index') }}" class="btn btn-danger flex-grow-1 flex-md-grow-0">
                                        <i class="bi bi-arrow-repeat me-1"></i> Reset
                                    </a>
                                    
                                    <a href="#" id="exportBtn" class="btn btn-success flex-grow-1 flex-md-grow-0">
                                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                                    </a>

                                    <button type="submit" class="btn btn-primary flex-grow-1 flex-md-grow-0">
                                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>

                    {{-- Tabel Data --}}
                    <div class="table-responsive mt-4" id="pemeriksaanTable"> 
                        @include('pemeriksaan.partials.table', ['pemeriksaans' => $pemeriksaans, 'warnaAturan' => $warnaAturan])
                    </div>
                    <div class="mt-4 d-flex justify-content-center" id="paginationContainer">
                        {{ $pemeriksaans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('styles')
    <style>
        /* Styling untuk tampilan mobile */
        @media (max-width: 768px) {
            .btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .pagination {
                flex-wrap: wrap;
            }
        }
        
        /* Styling untuk tombol agar lebih rapi */
        .btn {
            min-width: 120px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    @endpush
    
    @push('scripts')
    <script>
    $(document).ready(function(){
        // --- PENYIMPANAN VARIABEL (SELECTOR) ---
        const filterForm = $('#filterForm');
        const tableContainer = $('#pemeriksaanTable');
        const paginationContainer = $('#paginationContainer');
        const orgIndukSelect = $('#organisasi_induk_id');
        const subOrgContainer = $('#sub_organisasi_container');
        const subOrgSelect = $('#sub_organisasi_id');

        
        // --- LOGIKA UNTUK FILTER SUB-ORGANISASI YANG DINAMIS ---
        orgIndukSelect.on('change', function() {
            const parentId = $(this).val();

            // Kosongkan dan sembunyikan dropdown anak jika tidak ada induk yang dipilih
            if (!parentId) {
                subOrgContainer.hide();
                subOrgSelect.html('');
                return;
            }

            // Ambil data anak via AJAX
            $.ajax({
                url: `/ajax/get-sub-organisasi/${parentId}`, // Pastikan route ini ada di web.php
                method: 'GET',
                success: function(data) {
                    if (data && data.length > 0) {
                        subOrgSelect.html('<option value="">-- Semua Sub-Organisasi --</option>');
                        $.each(data, function(index, sub) {
                            subOrgSelect.append(`<option value="${sub.id}">${sub.nama_organisasi}</option>`);
                        });
                        subOrgContainer.show();
                    } else {
                        subOrgContainer.hide();
                        subOrgSelect.html('');
                    }
                }
            });
        });
        

        // --- LOGIKA UNTUK MENGIRIM FORM FILTER VIA AJAX ---
        filterForm.on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {'X-CSRF-TOKEN': csrfToken},
                beforeSend: function() {
                    tableContainer.html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                    paginationContainer.html('');
                },
                success: function(response) {
                    if (response.success) {
                        tableContainer.html(response.html);
                        paginationContainer.html(response.pagination);
                    } else {
                         tableContainer.html('<div class="alert alert-danger">Terjadi kesalahan</div>');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Terjadi kesalahan saat memuat data.';
                    if (xhr.status === 419) {
                        errorMsg = 'Sesi Anda telah berakhir, silakan muat ulang halaman.';
                    }
                    tableContainer.html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            });
        });

        
        // --- LOGIKA BARU UNTUK TOMBOL EXPORT EXCEL ---
        $('#exportBtn').on('click', function(e) {
            e.preventDefault();

            // Ambil semua data filter yang sedang aktif dari form
            var filterData = filterForm.serialize(); 
            
            // Buat URL untuk export dengan menyertakan data filter sebagai query string
            var exportUrl = "{{ route('pemeriksaan.export') }}?" + filterData;

            // Buka URL di window saat ini untuk memulai proses download file
            window.location.href = exportUrl;
        });

    });
    </script>
    @endpush

</x-app-layout>