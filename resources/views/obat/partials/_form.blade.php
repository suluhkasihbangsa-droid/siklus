@csrf
<div class="mb-3">
    <label for="nama_obat" class="form-label">Nama Obat</label>
    {{-- Mengecek apakah ada data $obat (untuk edit) atau tidak (untuk create) --}}
    <input type="text" class="form-control @error('nama_obat') is-invalid @enderror" id="nama_obat" name="nama_obat" value="{{ old('nama_obat', $obat->nama_obat ?? '') }}" required>
    @error('nama_obat')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="kategori" class="form-label">Kategori</label>
    <input type="text" class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori" value="{{ old('kategori', $obat->kategori ?? '') }}" required>
    @error('kategori')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="satuan" class="form-label">Satuan</label>
    <input type="text" class="form-control @error('satuan') is-invalid @enderror" id="satuan" name="satuan" value="{{ old('satuan', $obat->satuan ?? '') }}" required>
    @error('satuan')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Tombol akan berubah tergantung halaman --}}
<button type="submit" class="btn btn-primary">{{ $tombol ?? 'Simpan' }}</button>
<a href="{{ route('obat.index') }}" class="btn btn-secondary">Batal</a>