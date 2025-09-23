<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Daftar Sub Organisasi untuk: <strong>{{ $organisasi->nama_organisasi }}</strong></h4>
                    </div>
                    <div class="header-action">
                        <a href="{{ route('organisasi.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table class="table table-striped" role="grid">
                            <thead>
                                <tr class="ligth">
                                    <th scope="col" style="width: 5%;">#</th>
                                    <th scope="col">Nama Sub Organisasi</th>
                                    <th scope="col" style="width: 25%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($organisasi->children as $sub)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $sub->nama_organisasi }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a class="btn btn-sm btn-info me-2" href="#">User</a>
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
                                        <td colspan="3" class="text-center">Tidak ada data sub-organisasi.</td>
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
