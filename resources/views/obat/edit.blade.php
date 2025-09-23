<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Edit Data Obat</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('obat.update', $obat->id) }}" method="POST">
                        @method('PUT')
                        
                        {{-- Memanggil file partial form yang sama --}}
                        {{-- Variabel $obat dan $tombol akan otomatis terkirim ke partial --}}
                        @include('obat.partials._form', ['tombol' => 'Perbarui'])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>