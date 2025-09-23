{{-- resources/views/sasaran/partials/_table.blade.php --}}

<div class="table-responsive">
    <table class="table table-striped" role="grid">
        <thead>
            <tr class="ligth">
                <th scope="col">#</th>
                
                {{-- Header untuk No. Reg --}}
                <th scope="col">
                    @php
                        // Logika untuk menentukan arah sorting berikutnya
                        $direction = ($sortBy == 'nomor_registrasi' && $sortDirection == 'asc') ? 'desc' : 'asc';
                    @endphp
                    <a href="{{ route('sasaran.index', array_merge(request()->query(), ['sort_by' => 'nomor_registrasi', 'sort_direction' => $direction])) }}" class="text-decoration-none text-dark">
                        No. Reg
                        {{-- Tampilkan ikon panah jika kolom ini yang aktif --}}
                        @if ($sortBy == 'nomor_registrasi')
                            @if ($sortDirection == 'asc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/></svg>
                            @endif
                        @endif
                    </a>
                </th>

                {{-- Header untuk Nama Pasien --}}
                <th scope="col">
                    @php
                        $direction = ($sortBy == 'nama_lengkap' && $sortDirection == 'asc') ? 'desc' : 'asc';
                    @endphp
                    <a href="{{ route('sasaran.index', array_merge(request()->query(), ['sort_by' => 'nama_lengkap', 'sort_direction' => $direction])) }}" class="text-decoration-none text-dark">
                        Nama Pasien
                        @if ($sortBy == 'nama_lengkap')
                            @if ($sortDirection == 'asc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/></svg>
                            @endif
                        @endif
                    </a>
                </th>

                {{-- Header untuk Gender --}}
                <th scope="col">
                    @php
                        $direction = ($sortBy == 'gender' && $sortDirection == 'asc') ? 'desc' : 'asc';
                    @endphp
                    <a href="{{ route('sasaran.index', array_merge(request()->query(), ['sort_by' => 'gender', 'sort_direction' => $direction])) }}" class="text-decoration-none text-dark">
                        Gender
                        @if ($sortBy == 'gender')
                            @if ($sortDirection == 'asc')
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/></svg>
                            @endif
                        @endif
                    </a>
                </th>

                @role('admin')
                    {{-- Kolom Organisasi tidak dibuat sortable --}}
                    <th scope="col">Organisasi</th>
                @endrole
                <th scope="col">Riwayat Periksa</th>
                <th scope="col">Konsultasi Terakhir</th>
                <th scope="col" style="width: 25%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sasarans as $sasaran)
                <tr>
                    <th scope="row">{{ $loop->iteration + $sasarans->firstItem() - 1 }}</th>
                    <td><strong>{{ $sasaran->nomor_registrasi ?? '-' }}</strong></td>
                    <td>{{ $sasaran->nama_lengkap }}</td>
                    <td>{{ $sasaran->gender == 'L' ? 'L' : 'P' }}</td>
                @role('admin')
                    <td>
                        @if($sasaran->organisasi)
                            @if($sasaran->organisasi->parent)
                                <small class="text-muted">{{ $sasaran->organisasi->parent->nama_organisasi }} /</small><br>
                                {{ $sasaran->organisasi->nama_organisasi }}
                            @else
                                {{ $sasaran->organisasi->nama_organisasi }}
                            @endif
                        @else
                            -
                        @endif
                    </td>
                @endrole
                    <td>
                        @if ($sasaran->pemeriksaanTerakhir)
                            {{ \Carbon\Carbon::parse($sasaran->pemeriksaanTerakhir->tanggal_pemeriksaan)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($sasaran->konsultasiTerakhir)
                            Diperiksa oleh: <strong>dr. {{ $sasaran->konsultasiTerakhir->dokter->first_name ?? '' }}</strong><br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($sasaran->konsultasiTerakhir->created_at)->isoFormat('D MMM YYYY') }}</small>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            @if($sasaran->pemeriksaanTerakhir)
                                <button type="button" class="btn btn-sm btn-icon btn-info lihat-hasil-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#hasilKonsultasiModal"
                                    data-pemeriksaan-id="{{ $sasaran->pemeriksaanTerakhir->id }}"
                                    title="Lihat Hasil Pemeriksaan/Konsultasi">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text-fill" viewBox="0 0 16 16"><path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M4.5 9a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1zM4 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1z"/></svg>
                                </button>
                            @endif

                            {{-- Tombol Lihat Detail (Bisa diakses semua role) --}}
                            <button type="button" class="btn btn-sm btn-icon btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#sasaranDetailModal"
                                    data-sasaran-json="{{ json_encode($sasaran) }}"
                                    title="Lihat Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>
                            </button>

                            {{-- Tombol Periksa (Bisa diakses semua role) --}}
                            <a href="{{ route('pemeriksaan.create', $sasaran->id) }}" class="btn btn-sm btn-icon btn-success" title="Periksa / Skrining">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard2-plus-fill" viewBox="0 0 16 16"><path d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5"/><path d="M4.085 1H3.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1h-.585c.055.156.085.325.085.5V2a1.5 1.5 0 0 1-1.5 1.5h-5A1.5 1.5 0 0 1 4 2v-.5c0-.175.03-.344.085-.5M8.5 6.5V8H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V9H6a.5.5 0 0 1 0-1h1.5V6.5a.5.5 0 0 1 1 0"/></svg>
                            </a>
                            
                            {{-- Tombol Cetak ID & Edit (Hanya untuk admin, koorUser, dan user) --}}
                            @hasanyrole('admin|koorUser|user')
                                <a href="{{ route('sasaran.cetakId', $sasaran->id) }}" target="_blank" class="btn btn-sm btn-icon btn-secondary" title="Cetak ID">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16"><path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1"/><path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/></svg>
                                </a>
                                <a class="btn btn-sm btn-icon btn-warning" href="{{ route('sasaran.edit', $sasaran->id) }}" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg>
                                </a>
                            @endhasanyrole

                            {{-- Tombol Hapus (Hanya untuk admin dan koorUser) --}}
                            @hasanyrole('admin|koorUser')
                                <form action="{{ route('sasaran.destroy', $sasaran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16"><path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/></svg>
                                    </button>
                                </form>
                            @endhasanyrole
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ auth()->user()->hasRole('admin') ? '7' : '6' }}" class="text-center">
                        Tidak ada data yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 px-4">
    {{ $sasarans->links() }}
</div>