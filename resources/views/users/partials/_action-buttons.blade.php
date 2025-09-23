{{-- resources/views/users/partials/_action-buttons.blade.php --}}

{{-- Tombol-Tombol Aksi --}}
<div class="d-flex">
    <a href="{{ route('users.download_template') }}" class="btn btn-sm btn-info me-2">Download Template</a>
    <button type="button" class="btn btn-sm btn-dark me-2" data-bs-toggle="modal" data-bs-target="#importModal">Import Pengguna</button>
    <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary d-flex align-items-center">
        <svg class="me-1" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.87651 15.2063C6.03251 15.2063 2.74951 15.7873 2.74951 18.1153C2.74951 20.4433 6.01251 21.0453 9.87651 21.0453C13.7215 21.0453 17.0035 20.4633 17.0035 18.1363C17.0035 15.8093 13.7415 15.2063 9.87651 15.2063Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M9.8766 11.886C12.3996 11.886 14.4446 9.841 14.4446 7.318C14.4446 4.795 12.3996 2.75 9.8766 2.75C7.3546 2.75 5.3096 4.795 5.3096 7.318C5.3006 9.832 7.3306 11.877 9.8456 11.886H9.8766Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M19.2036 8.66919V12.6792" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M21.2497 10.6741H17.1597" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
        Tambah User
    </a>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importModalLabel">Import Pengguna dari Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <p>Unduh template, isi data sesuai format, lalu unggah file di sini. Pastikan nama *role* dan organisasi sudah ada di sistem.</p>
            <div class="mb-3">
                <label for="file" class="form-label">Pilih File Excel (.xlsx, .xls, .csv)</label>
                <input class="form-control" type="file" name="file" id="file" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Unggah dan Proses</button>
          </div>
      </form>
    </div>
  </div>
</div>