{{-- resources/views/sasaran/edit.blade.php --}}
<x-app-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Edit Data Sasaran</h4>
                        </div>
                    </div>
                    
                    @include('sasaran.partials._form')
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>