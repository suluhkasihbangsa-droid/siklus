{{-- resources/views/sasaran/partials/_modal-success.blade.php --}}

@if(session('popup_data') && session('popup_data.show'))
    <div class="modal fade" id="successPopup" tabindex="-1" aria-labelledby="successPopupLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successPopupLabel">
                        <i class="fas fa-check-circle"></i> {{ session('popup_data.title') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success bg-light border-0">
                        {!! session('popup_data.message') !!}
                    </div>
                    <div class="text-center mt-3">
                        <p class="text-muted">Nomor Registrasi: <strong>{{ session('popup_data.nomor_registrasi') }}</strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                        <i class="fas fa-check"></i> Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    // Fungsi global untuk copy dari popup bisa diletakkan di file JS utama atau di sini
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Nomor registrasi berhasil disalin!');
        }, function(err) {
            alert('Gagal menyalin nomor registrasi.');
        });
    }
    
    $(document).ready(function() {
        $('#successPopup').modal('show');
    });
</script>
@endpush
@endif