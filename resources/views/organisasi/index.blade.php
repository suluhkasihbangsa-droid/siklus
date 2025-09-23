<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Daftar Organisasi</h4>
                    </div>
                    <div class="header-action">
                        <a href="{{ route('organisasi.create') }}" class="btn btn-primary">Tambah Organisasi</a>
                    </div>
                </div>
                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table class="table table-striped" role="grid">
                            <thead>
                                <tr class="ligth">
                                    <th scope="col" style="width: 5%;">#</th>
                                    <th scope="col">Nama Organisasi</th>
                                    <th scope="col" style="width: 25%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($organisasis as $organisasi)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td><strong>{{ $organisasi->nama_organisasi }}</strong></td>
                                        <td>
                                            <div class="d-flex">
                                                <a class="btn btn-sm btn-info me-2" href="#">User</a>
                                                {{-- Tombol Lihat sekarang mengarah ke method show --}}
                                                <a class="btn btn-sm btn-primary me-2" href="{{ route('organisasi.show', $organisasi->id) }}">Lihat</a>
                                                <a class="btn btn-sm btn-warning me-2" href="#">Edit</a>
                                                <form action="#" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada data organisasi.</td>
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
