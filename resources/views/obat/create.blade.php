<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Tambah Obat Baru</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('obat.store') }}" method="POST">
                        {{-- Memanggil file partial form --}}
                        @include('obat.partials._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>