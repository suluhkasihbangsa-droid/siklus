<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title mb-2">Daftar Obat</h4>
                    </div>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#importObatModal">
                            Import Obat
                        </button>
                        <a href="{{ route('obat.create') }}" class="btn btn-primary">
                            Tambah Obat Manual
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Tampilan Laporan Sukses/Error/Gagal --}}
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('failures'))
                        <div class="alert alert-warning" role="alert">
                            <strong>Beberapa data gagal diimport:</strong>
                            <ul>
                                @foreach(session('failures') as $failure)
                                    <li>{{ $failure }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped" data-toggle="data-table">
                            <thead>
                                <tr>
                                    {{-- PERUBAHAN 1: Tambah kolom No --}}
                                    <th style="width: 5%;">No</th> 
                                    <th>Nama Obat & Aksi</th>
                                    <th>Kategori</th>
                                    <th>Satuan</th>
                                    {{-- PERUBAHAN 2: Kolom Aksi di sini dihapus --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($obats as $obat)
                                    <tr>
                                        {{-- PERUBAHAN 3: Isi kolom No dengan nomor urut --}}
                                        <td>{{ $loop->iteration }}</td> 

                                        {{-- PERUBAHAN 4: Gabungkan Nama Obat dan Tombol Aksi --}}
                                        <td>
                                            <span class="fw-bold">{{ $obat->nama_obat }}</span>
                                            <div class="mt-2">
                                                <a href="{{ route('obat.edit', $obat->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('obat.destroy', $obat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus obat ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </td>

                                        <td>{{ $obat->kategori }}</td>
                                        <td>{{ $obat->satuan }}</td>
                                        {{-- PERUBAHAN 5: Kolom Aksi di sini dihapus --}}
                                    </tr>
                                @empty
                                    <tr>
                                        {{-- Colspan tetap 4 karena kita menambah 'No' dan menghapus 'Aksi' --}}
                                        <td colspan="4" class="text-center">Tidak ada data obat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- KODE POPUP/MODAL UNTUK IMPORT (TETAP SAMA) --}}
<div class="modal fade" id="importObatModal" tabindex="-1" aria-labelledby="importObatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importObatModalLabel">Import Data Obat dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('obat.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">1. Unduh dan isi template</label><br>
                        <a href="{{ route('obat.download_template') }}" class="btn btn-sm btn-info">Unduh Template Import</a>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label for="file" class="form-label">2. Upload file yang sudah diisi</label>
                        <input class="form-control" type="file" name="file" required>
                    </div>
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Mulai Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT UNTUK MENGAKTIFKAN DATATABLES (TETAP SAMA) --}}
@push('scripts')
<script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data yang tersedia",
                "infoFiltered": "(disaring dari total _MAX_ data)",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    });
</script>
@endpush