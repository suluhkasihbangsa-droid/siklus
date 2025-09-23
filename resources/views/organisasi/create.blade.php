<x-app-layout>
    <div class="row">
        <div class="col-sm-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Buat Organisasi Baru</h4>
                    </div>
                </div>
                <div class="card-body">
                    <p>Masukkan nama organisasi induk. Jika ada, Anda bisa langsung menambahkan beberapa sub-organisasi di bawahnya.</p>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('organisasi.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="nama_organisasi_induk">Nama Organisasi Induk</label>
                            <input type="text" class="form-control" name="nama_organisasi_induk" id="nama_organisasi_induk" value="{{ old('nama_organisasi_induk') }}" required>
                        </div>

                        <hr>

                        <div id="sub-organisasi-wrapper">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Sub Organisasi (Opsional)</h5>
                                <button type="button" id="add-sub-organisasi" class="btn btn-sm btn-success">+</button>
                            </div>
                            
                            {{-- Container untuk field sub-organisasi dinamis --}}
                            <div id="sub-organisasi-fields">
                                {{-- Field akan ditambahkan di sini oleh JavaScript --}}
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Simpan Organisasi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#add-sub-organisasi').on('click', function() {
                const newField = `
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="sub_organisasi[]" placeholder="Nama Sub Organisasi">
                    <button class="btn btn-outline-danger remove-sub-organisasi" type="button">-</button>
                </div>
                `;
                $('#sub-organisasi-fields').append(newField);
            });

            // Event delegation untuk tombol remove
            $('#sub-organisasi-fields').on('click', '.remove-sub-organisasi', function() {
                $(this).closest('.input-group').remove();
            });
        });
    </script>
    @endpush
</x-app-layout>