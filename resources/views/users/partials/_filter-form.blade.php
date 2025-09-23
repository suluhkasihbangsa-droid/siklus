{{-- resources/views/users/partials/_filter-form.blade.php --}}

<div class="px-3 pb-4">
    <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label for="role" class="form-label">Filter berdasarkan Role</label>
            <select name="role" id="role" class="form-select">
                <option value="">Semua Role</option>
                @foreach($roles_for_filter as $role)
                    <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <label for="organisasi_id" class="form-label">Filter berdasarkan Organisasi</label>
            <select name="organisasi_id" id="organisasi_id" class="form-select">
                <option value="">Semua Organisasi</option>
                @foreach($organisasis_for_filter as $id => $nama)
                    <option value="{{ $id }}" {{ request('organisasi_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <div class="d-flex">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
    <hr class="mt-4">
</div>