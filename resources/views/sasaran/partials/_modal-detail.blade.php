{{-- resources/views/sasaran/partials/_modal-detail.blade.php --}}

<div class="modal fade" id="sasaranDetailModal" tabindex="-1" aria-labelledby="sasaranDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sasaranDetailModalLabel">Detail Sasaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr><th style="width: 30%;">No. Registrasi</th><td id="modal-noreg"></td></tr>
                        <tr><th>NIK</th><td id="modal-nik"></td></tr>
                        <tr><th>Nama Lengkap</th><td id="modal-nama"></td></tr>
                        <tr><th>Tanggal Lahir</th><td id="modal-tgl-lahir"></td></tr>
                        <tr><th>Usia Saat Ini</th><td id="modal-usia"></td></tr>
                        <tr><th>Gender</th><td id="modal-gender"></td></tr>
                        <tr><th>No. HP</th><td id="modal-no-hp"></td></tr>
                        <tr><th>Organisasi</th><td id="modal-organisasi"></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // SCRIPT UNTUK MODAL DETAIL SASARAN
    const sasaranDetailModal = document.getElementById('sasaranDetailModal');
    if (sasaranDetailModal) {
        sasaranDetailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const sasaran = JSON.parse(button.getAttribute('data-sasaran-json'));
            
            function calculateAge(dateString) {
                if (!dateString) return '-';
                const birthDate = new Date(dateString);
                const today = new Date();
                let years = today.getFullYear() - birthDate.getFullYear();
                let months = today.getMonth() - birthDate.getMonth();
                
                if (today.getDate() < birthDate.getDate()) { months--; }
                if (months < 0) { years--; months += 12; }
                
                return `${years} tahun, ${months} bulan`;
            }
            
            const formattedTglLahir = sasaran.tgl_lahir ? new Date(sasaran.tgl_lahir).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-';
            
            let organisasiText = 'Tidak Terdaftar';
            if (sasaran.organisasi) {
                organisasiText = sasaran.organisasi.nama_organisasi;
                if (sasaran.organisasi.parent) {
                    organisasiText = `${sasaran.organisasi.parent.nama_organisasi} > ${sasaran.organisasi.nama_organisasi}`;
                }
            }

            document.getElementById('modal-noreg').textContent = sasaran.nomor_registrasi || '-';
            document.getElementById('modal-nik').textContent = sasaran.nik || '-';
            document.getElementById('modal-nama').textContent = sasaran.nama_lengkap || '-';
            document.getElementById('modal-tgl-lahir').textContent = formattedTglLahir;
            document.getElementById('modal-usia').textContent = calculateAge(sasaran.tgl_lahir);
            document.getElementById('modal-gender').textContent = sasaran.gender === 'L' ? 'Laki-laki' : 'Perempuan';
            document.getElementById('modal-no-hp').textContent = sasaran.no_hp || '-';
            document.getElementById('modal-organisasi').textContent = organisasiText;
        });
    }
});
</script>
@endpush