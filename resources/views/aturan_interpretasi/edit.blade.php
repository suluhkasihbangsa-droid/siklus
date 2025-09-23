<x-app-layout>
    <div class="row">
        <div class="col-sm-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Edit Standar Interpretasi</h4>
                    </div>
                </div>
                <div class="card-body">
                    <p>Anda sedang mengubah aturan untuk kategori <strong>{{ str_replace('_', ' ', $aturan->kategori) }}</strong>.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
                        </div>
                    @endif

                    <form action="{{ route('aturan-interpretasi.update', $aturan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="form-label" for="nama_interpretasi">Nama Interpretasi</label>
                            <input type="text" class="form-control" name="nama_interpretasi" id="nama_interpretasi" value="{{ old('nama_interpretasi', $aturan->nama_interpretasi) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="kode_interpretasi">Kode/Singkatan</label>
                            <input type="text" class="form-control" name="kode_interpretasi" id="kode_interpretasi" value="{{ old('kode_interpretasi', $aturan->kode_interpretasi) }}">
                        </div>

                        {{-- Field Warna Badge --}}
                        <div class="form-group">
                            <label class="form-label" for="warna_badge">Warna Badge</label>
                            <select class="form-select" id="warna_badge" name="warna_badge" required>
                                <option value="primary" {{ old('warna_badge', $aturan->warna_badge) == 'primary' ? 'selected' : '' }}>Primary (Biru)</option>
                                <option value="secondary" {{ old('warna_badge', $aturan->warna_badge) == 'secondary' ? 'selected' : '' }}>Secondary (Abu-abu)</option>
                                <option value="success" {{ old('warna_badge', $aturan->warna_badge) == 'success' ? 'selected' : '' }}>Success (Hijau)</option>
                                <option value="danger" {{ old('warna_badge', $aturan->warna_badge) == 'danger' ? 'selected' : '' }}>Danger (Merah)</option>
                                <option value="warning" {{ old('warna_badge', $aturan->warna_badge) == 'warning' ? 'selected' : '' }}>Warning (Kuning)</option>
                                <option value="info" {{ old('warna_badge', $aturan->warna_badge) == 'info' ? 'selected' : '' }}>Info (Biru Muda)</option>
                                <option value="light" {{ old('warna_badge', $aturan->warna_badge) == 'light' ? 'selected' : '' }}>Light (Putih)</option>
                                <option value="dark" {{ old('warna_badge', $aturan->warna_badge) == 'dark' ? 'selected' : '' }}>Dark (Hitam)</option>
                            </select>
                            <div class="mt-2">
                                <span class="badge bg-{{ old('warna_badge', $aturan->warna_badge ?? 'secondary') }}" id="badgePreview">
                                    Contoh Tampilan
                                </span>
                            </div>
                        </div>

                        {{-- Tampilkan field yang relevan berdasarkan kategori --}}
                        @if ($aturan->kategori === 'TENSI')
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label" for="batas_sistolik">Batas Atas Sistolik (mmHg)</label>
                                    <input type="number" class="form-control" name="batas_sistolik" id="batas_sistolik" value="{{ old('batas_sistolik', $aturan->batas_sistolik) }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label" for="batas_diastolik">Batas Atas Diastolik (mmHg)</label>
                                    <input type="number" class="form-control" name="batas_diastolik" id="batas_diastolik" value="{{ old('batas_diastolik', $aturan->batas_diastolik) }}">
                                </div>
                            </div>
                        @else
                             <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label" for="batas_bawah">Batas Bawah Nilai</label>
                                    <input type="number" step="0.01" class="form-control" name="batas_bawah" id="batas_bawah" value="{{ old('batas_bawah', $aturan->batas_bawah) }}">
                                    <small class="form-text text-muted">Kosongkan jika tidak ada batas bawah (misal: < 18.5).</small>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label" for="batas_atas">Batas Atas Nilai</label>
                                    <input type="number" step="0.01" class="form-control" name="batas_atas" id="batas_atas" value="{{ old('batas_atas', $aturan->batas_atas) }}">
                                    <small class="form-text text-muted">Kosongkan jika tidak ada batas atas (misal: >= 25).</small>
                                </div>
                            </div>
                        @endif

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('aturan-interpretasi.index') }}" class="btn btn-danger">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const warnaBadgeSelect = document.getElementById('warna_badge');
            const badgePreview = document.getElementById('badgePreview');
            
            // Fungsi untuk update preview badge
            function updateBadgePreview() {
                const selectedColor = warnaBadgeSelect.value;
                badgePreview.className = 'badge bg-' + selectedColor;
                badgePreview.textContent = 'Contoh Tampilan (' + selectedColor + ')';
            }
            
            // Update preview saat halaman dimuat
            updateBadgePreview();
            
            // Update preview saat pilihan berubah
            warnaBadgeSelect.addEventListener('change', updateBadgePreview);
        });
    </script>
    @endpush
</x-app-layout>