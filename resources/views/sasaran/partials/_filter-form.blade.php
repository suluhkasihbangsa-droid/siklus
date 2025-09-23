{{-- resources/views/sasaran/partials/_filter-form.blade.php --}}

<form action="{{ route('sasaran.index') }}" method="GET" class="mb-4">
    <div class="row align-items-end">
        <div class="col-md-4">
            <label for="search" class="form-label">Cari Sasaran</label>
            <input type="text" name="search" id="search" class="form-control" placeholder="Nama, NIK, ID..." value="{{ request('search') }}">
        </div>
        @role('admin')
        <div class="col-md-3">
            <label for="organisasi_induk_id_filter" class="form-label">Filter Organisasi</label>
            <select name="organisasi_induk_id" id="organisasi_induk_id_filter" class="form-select">
                <option value="">Semua Organisasi</option>
                @foreach($organisasi_induk_list as $induk)
                    <option value="{{ $induk->id }}" {{ request('organisasi_induk_id') == $induk->id ? 'selected' : '' }}>
                        {{ $induk->nama_organisasi }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="organisasi_id_filter" class="form-label">Sub Organisasi</label>
            <select name="organisasi_id" id="organisasi_id_filter" class="form-select">
                <option value="">Semua Sub</option>
            </select>
        </div>
        @endrole
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // SCRIPT UNTUK FILTER ORGANISASI
    const indukFilter = document.getElementById('organisasi_induk_id_filter');
    if(indukFilter) {
        const subFilter = document.getElementById('organisasi_id_filter');
        const oldSubId = "{{ request('organisasi_id') }}";

        async function fetchSubOrganisasi() {
            let indukId = indukFilter.value;
            subFilter.innerHTML = '<option value="">Semua Sub-Organisasi</option>';
            if (!indukId) return;

            try {
                const response = await fetch(`/ajax/get-sub-organisasi/${indukId}`);
                const data = await response.json();
                
                if (data && data.length > 0) {
                    data.forEach(sub => {
                        const option = new Option(sub.nama_organisasi, sub.id);
                        if (sub.id == oldSubId) {
                            option.selected = true;
                        }
                        subFilter.add(option);
                    });
                }
            } catch (error) {
                console.error('Error fetching sub organisasi:', error);
            }
        }
        
        indukFilter.addEventListener('change', fetchSubOrganisasi);
        if (indukFilter.value) {
            fetchSubOrganisasi();
        }
    }
});
</script>
@endpush