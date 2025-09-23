<x-guest-layout>
    <section class="login-content">
        <div class="row m-0 align-items-center bg-white vh-100">
            {{-- Bagian gambar di sebelah kiri, kita pertahankan --}}
            <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                <img src="{{asset('images/auth/05.png')}}" class="img-fluid gradient-main animated-scaleX" alt="images">
            </div>

            {{-- Bagian konten di sebelah kanan --}}
            <div class="col-md-6">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card card-transparent auth-card shadow-none d-flex justify-content-center mb-0">
                            {{-- Di sini kita masukkan konten dari show_qr yang lama --}}
                            <div class="card-body text-center">
                                <a href="{{ url('/') }}" class="navbar-brand d-flex justify-content-center align-items-center mb-3">
                                    <svg width="30" class="text-primary" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2" transform="rotate(-45 -0.757324 19.2427)" fill="currentColor"/>
                                        <rect x="7.72803" y="27.728" width="28" height="4" rx="2" transform="rotate(-45 7.72803 27.728)" fill="currentColor"/>
                                        <rect x="10.5366" y="16.3945" width="16" height="4" rx="2" transform="rotate(45 10.5366 16.3945)" fill="currentColor"/>
                                        <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2" transform="rotate(45 10.5562 -0.556152)" fill="currentColor"/>
                                    </svg>
                                    <h4 class="logo-title ms-3">{{env('APP_NAME')}}</h4>
                                </a>
                                <h2 class="mb-2">Pendaftaran Berhasil!</h2>

                                <p class="mt-3">
                                    Halo, <strong>{{ $validatedData['nama_lengkap'] }}</strong>!
                                    <br>
                                    Silakan <strong>Screenshot</strong> QR Code di bawah ini dan tunjukkan kepada petugas kami di lokasi.
                                </p>

                                <div class="my-4">
                                    {{-- Ini akan menampilkan gambar QR Code SVG --}}
                                    {!! $qrCode !!}
                                </div>

                                <p class="text-muted">QR Code ini berisi data pendaftaran Anda.</p>

                                <div class="d-grid">
                                    <a href="{{ route('public.register.create') }}" class="btn btn-secondary mt-3">Daftar Lagi</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>