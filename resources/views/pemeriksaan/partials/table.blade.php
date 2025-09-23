<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                {{-- Data Utama --}}
                <th scope="col">No</th>
                <th scope="col">Pasien</th>
                <th scope="col">Organisasi</th>
                <th scope="col">Tgl Periksa</th>
                <th scope="col">Usia</th>
                
                {{-- Pemeriksaan Fisik --}}
                <th scope="col" class="text-center">BB (kg)</th>
                <th scope="col" class="text-center">TB (cm)</th>
                <th scope="col" class="text-center">IMT</th>
                <th scope="col" class="text-center">LP (cm)</th>
                <th scope="col" class="text-center">LiLA (cm)</th>
                <th scope="col" class="text-center">Tensi</th>

                {{-- Hasil Lab --}}
                <th scope="col" class="text-center">Gula Darah</th>
                <th scope="col" class="text-center">Asam Urat</th>
                <th scope="col" class="text-center">Kolesterol</th>

                {{-- Status / Interpretasi --}}
                <th scope="col">Status IMT</th>
                <th scope="col">Status Tensi</th>
                <th scope="col">Status Gula</th>
                <th scope="col">Status LP</th>
                <th scope="col">Status LiLA</th>
                <th scope="col">Status As. Urat</th>
                <th scope="col">Status Kolesterol</th>

                {{-- Aksi --}}
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemeriksaans as $pemeriksaan)
                <tr>
                    <td>{{ $loop->iteration + $pemeriksaans->firstItem() - 1 }}</td>
                    <td>
                        @if ($pemeriksaan->sasaran)
                            <strong>{{ $pemeriksaan->sasaran->nama_lengkap }}</strong><br>
                            <small class="text-muted">{{ $pemeriksaan->sasaran->nomor_registrasi }}</small>
                        @else
                            <strong class="text-danger">DATA SASARAN DIHAPUS</strong>
                        @endif
                    </td>
                    <td>
                        @if ($pemeriksaan->sasaran && $pemeriksaan->sasaran->organisasi)
                            @if ($pemeriksaan->sasaran->organisasi->parent)
                                <strong>{{ $pemeriksaan->sasaran->organisasi->parent->nama_organisasi }}</strong>
                                <br>
                                <small class="text-muted">({{ $pemeriksaan->sasaran->organisasi->nama_organisasi }})</small>
                            @else
                                <strong>{{ $pemeriksaan->sasaran->organisasi->nama_organisasi }}</strong>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($pemeriksaan->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                    <td>{{ $pemeriksaan->usia_saat_pemeriksaan ?? '-' }}</td>
                    <td class="text-center">{{ $pemeriksaan->bb ?? '-' }}</td>
                    <td class="text-center">{{ $pemeriksaan->tb ?? '-' }}</td>
                    <td class="text-center">{{ $pemeriksaan->imt ?? '-' }}</td>
                    <td class="text-center">{{ $pemeriksaan->lp ?? '-' }}</td>
                    <td class="text-center">{{ $pemeriksaan->lila ?? '-' }}</td>
                    <td class="text-center">{{ $pemeriksaan->tensi_sistolik ?? '-' }}/{{ $pemeriksaan->tensi_diastolik ?? '-' }}</td>
                    <td class="text-center">{{ $pemeriksaan->gd ?? '-' }} {{ $pemeriksaan->mgd ? "($pemeriksaan->mgd)" : '' }}</td>
                    <td class="text-center">{{ $pemeriksaan->asut ?? '-' }}</td>
                    <td class="text-center">{{ $pemeriksaan->koles ?? '-' }}</td>
                    
                    {{-- Menggunakan "Peta Warna" dari Controller --}}
                    <td><span class="badge bg-{{ $warnaAturan['IMT'][$pemeriksaan->int_imt] ?? 'secondary' }}">{{ $pemeriksaan->int_imt ?? '-' }}</span></td>
                    <td><span class="badge bg-{{ $warnaAturan['TENSI'][$pemeriksaan->int_tensi] ?? 'secondary' }}">{{ $pemeriksaan->int_tensi ?? '-' }}</span></td>
                    <td><span class="badge bg-{{ $warnaAturan['GULA_DARAH'][$pemeriksaan->int_gd] ?? 'secondary' }}">{{ $pemeriksaan->int_gd ?? '-' }}</span></td>
                    <td><span class="badge bg-{{ $warnaAturan['LP'][$pemeriksaan->int_lp] ?? 'secondary' }}">{{ $pemeriksaan->int_lp ?? '-' }}</span></td>
                    <td><span class="badge bg-{{ $warnaAturan['LILA_DEWASA'][$pemeriksaan->int_lila] ?? $warnaAturan['LILA_BALITA'][$pemeriksaan->int_lila] ?? 'secondary' }}">{{ $pemeriksaan->int_lila ?? '-' }}</span></td>
                    <td><span class="badge bg-{{ $warnaAturan['ASAM_URAT'][$pemeriksaan->int_asut] ?? 'secondary' }}">{{ $pemeriksaan->int_asut ?? '-' }}</span></td>
                    <td><span class="badge bg-{{ $warnaAturan['KOLESTEROL'][$pemeriksaan->int_koles] ?? 'secondary' }}">{{ $pemeriksaan->int_koles ?? '-' }}</span></td>

                    <td>
                        <div class="d-flex align-items-center gap-1">
                            {{-- Tombol Edit --}}
                            <a class="btn btn-sm btn-icon btn-warning" href="{{ route('pemeriksaan.edit', $pemeriksaan->id) }}" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg>
                            </a>
                            {{-- Tombol Cetak --}}
                            <a class="btn btn-sm btn-icon btn-info" href="#" title="Cetak">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zM1 7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1zm3 4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v1H4z"/></svg>
                            </a>
                            {{-- Tombol Delete --}}
                            <form method="POST" action="{{ route('pemeriksaan.destroy', $pemeriksaan->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pemeriksaan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="22" class="text-center py-4">
                        @if(isset($isFiltered) && $isFiltered)
                            <p class="text-muted">Data yang dicari, tidak ditemukan.</p>
                        @else
                            @if(auth()->user()->hasRole('admin'))
                                <h5 class="mt-2">Silakan Pilih Filter</h5>
                                <p class="text-muted">Untuk memulai, pilih filter di atas untuk menampilkan data pemeriksaan.</p>
                            @else
                                <p class="text-muted">Tidak ada data pemeriksaan yang ditemukan.</p>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>