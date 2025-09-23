<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Manajemen Standar Interpretasi</h4>
                    </div>
                    <div class="header-action">
                        {{-- Tombol ini bisa kita fungsikan nanti --}}
                        <a href="#" class="btn btn-primary">Tambah Aturan Baru</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive mt-4">
                        <table id="basic-table" class="table table-striped mb-0" role="grid">
                            <thead>
                                <tr>
                                    <th>Nama Interpretasi</th>
                                    <th>Warna Badge</th>
                                    <th>Kondisi</th>
                                    <th>Rentang Nilai</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($groupedAturan as $kategori => $aturans)
                                    {{-- Baris untuk Header Kategori --}}
                                    <tr class="bg-light">
                                        <td colspan="5">
                                            <h6 class="mb-0">{{ str_replace('_', ' ', $kategori) }}</h6>
                                        </td>
                                    </tr>
                                    
                                    {{-- Baris untuk setiap aturan di dalam kategori --}}
                                    @foreach ($aturans as $aturan)
                                        <tr>
                                            <td>{{ $aturan->nama_interpretasi }}</td>
                                            <td>
                                                <span class="badge bg-{{ $aturan->warna_badge ?? 'secondary' }}">
                                                    {{ $aturan->warna_badge ?? 'Belum diatur' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($aturan->kondisi_gender)
                                                    <span class="badge bg-info">{{ $aturan->kondisi_gender == 'L' ? 'Pria' : 'Wanita' }}</span>
                                                @endif
                                                @if($aturan->kondisi_usia_min_bulan || $aturan->kondisi_usia_max_bulan)
                                                    <span class="badge bg-secondary">
                                                        @if($aturan->kondisi_usia_min_bulan && $aturan->kondisi_usia_max_bulan)
                                                            {{ $aturan->kondisi_usia_min_bulan }} - {{ $aturan->kondisi_usia_max_bulan }} bln
                                                        @elseif($aturan->kondisi_usia_min_bulan)
                                                            &ge; {{ $aturan->kondisi_usia_min_bulan }} bln
                                                        @else
                                                            &le; {{ $aturan->kondisi_usia_max_bulan }} bln
                                                        @endif
                                                    </span>
                                                @endif
                                                @if($aturan->kondisi_metode)
                                                    <span class="badge bg-warning text-dark">{{ $aturan->kondisi_metode == 'P' ? 'Puasa' : 'Sewaktu' }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($aturan->kategori == 'TENSI')
                                                    Sistolik &le; {{ $aturan->batas_sistolik }} & Diastolik &le; {{ $aturan->batas_diastolik }}
                                                @elseif(!is_null($aturan->batas_bawah) && !is_null($aturan->batas_atas))
                                                    {{ $aturan->batas_bawah }} - {{ $aturan->batas_atas }}
                                                @elseif(!is_null($aturan->batas_bawah))
                                                    &ge; {{ $aturan->batas_bawah }}
                                                @elseif(!is_null($aturan->batas_atas))
                                                    &le; {{ $aturan->batas_atas }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('aturan-interpretasi.edit', $aturan->id) }}" class="btn btn-sm btn-warning me-2">Edit</a>
                                                    <form action="#" method="POST" onsubmit="return confirm('Anda yakin?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada data standar interpretasi di dalam sistem.</td>
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