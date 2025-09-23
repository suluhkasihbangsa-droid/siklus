<style>
    .nik-digit-input { 
        width: calc(6.25% - 5px); 
        min-width: 30px; 
        height: 38px; 
        font-size: 1.1rem; 
        font-weight: bold; 
    }
    @media (max-width: 768px) { 
        #nik-input-container { justify-content: flex-start; } 
        .nik-digit-input { width: calc(12.5% - 5px); margin-bottom: 5px; } 
    }
    #toggle-nik-input {
        transition: all 0.3s ease;
    }
    #toggle-nik-input:hover {
        transform: translateY(-1px);
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// =======================================================================
// DEFINISI FUNGSI GLOBAL
// Fungsi-fungsi ini ditempatkan di luar agar bisa diakses oleh script lain,
// seperti script scanner QR.
// =======================================================================

function updateHiddenNik() {
    let nikValue = '';
    $('#nik-input-container .nik-digit-input').each(function() { 
        nikValue += $(this).val(); 
    });
    $('#nik_hidden').val(nikValue);
}

function calculateAndDisplayAge(dateString) {
    const displayUsia = $('#display_usia');
    if (!dateString) {
        displayUsia.text('');
        return;
    }
    
    const birthDate = new Date(dateString);
    const today = new Date();
    
    if (isNaN(birthDate.getTime())) {
        displayUsia.text('');
        return;
    }
    
    let years = today.getFullYear() - birthDate.getFullYear();
    let months = today.getMonth() - birthDate.getMonth();
    
    if (today.getDate() < birthDate.getDate()) months--;
    if (months < 0) {
        years--;
        months += 12;
    }
    
    const totalMonths = (years * 12) + months;
    let ageString = totalMonths < 60 ? `${totalMonths} bulan` : 
                    totalMonths < 228 ? `${years} tahun ${months} bulan` : 
                    `${years} tahun`;
                    
    displayUsia.text('Perkiraan Usia: ' + ageString);
}

function validateAndSetDate() {
    const dd = $('#tgl_lahir_dd'), mm = $('#tgl_lahir_mm'), yyyy = $('#tgl_lahir_yyyy');
    const hiddenDate = $('#tgl_lahir_hidden'), errorDiv = $('#tgl_lahir_error');
    
    const day = parseInt(dd.val()), month = parseInt(mm.val()), year = parseInt(yyyy.val());
    errorDiv.text('');
    hiddenDate.val('');
    calculateAndDisplayAge(null);
    
    if (dd.val().length === 2 && mm.val().length === 2 && yyyy.val().length === 4) {
        if (isNaN(day) || isNaN(month) || isNaN(year) || month < 1 || month > 12 || year < 1900 || year > new Date().getFullYear()) {
            errorDiv.text('Format tanggal tidak wajar.');
            return;
        }
        
        const date = new Date(year, month - 1, day);
        if (date.getFullYear() !== year || date.getMonth() !== month - 1 || date.getDate() !== day) {
            errorDiv.text('Tanggal tidak valid untuk bulan ini.');
            return;
        }
        
        const fullDateString = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        hiddenDate.val(fullDateString);
        calculateAndDisplayAge(fullDateString);
    }
}

function validatePhoneNumber() {
    const phoneInput = $('#no_hp');
    const phoneError = $('#no_hp_error');
    const phoneValue = phoneInput.val().replace(/\s/g, '');
    
    phoneError.text('');
    
    if (phoneValue.length > 0 && phoneValue.length < 10) {
        phoneError.text('Nomor HP minimal 10 digit.');
        return false;
    }
    
    if (phoneValue.length > 13) {
        phoneError.text('Nomor HP maksimal 13 digit.');
        return false;
    }
    
    return true;
}

function loadDropdown(url, parentId, targetSelector, defaultText, oldValue, callback) {
    $(targetSelector).empty().append(`<option value="">Memuat...</option>`);
    
    if (!parentId) {
        $(targetSelector).empty().append(`<option value="">${defaultText}</option>`);
        if (typeof callback === 'function') callback();
        return;
    }

    $.ajax({
        url: url + parentId,
        type: "GET", 
        dataType: "json", 
        cache: false,
        success: function(data) {
            $(targetSelector).empty().append(`<option value="">${defaultText}</option>`);
            
            $.each(data, function(key, value) {
                $(targetSelector).append(`<option value="${value.id}">${value.nama_kota || value.nama_kecamatan || value.nama_kelurahan || value.nama_organisasi}</option>`);
            });
            
            if (oldValue) {
                $(targetSelector).val(oldValue);
            }
            
            if (typeof callback === 'function') {
                callback();
            }
        },
        error: function() {
            $(targetSelector).empty().append(`<option value="">Gagal memuat</option>`);
            if (typeof callback === 'function') {
                callback();
            }
        }
    });
}

function addSubOrganisasiField() {
    const newField = `<div class="input-group mb-2">
        <input type="text" class="form-control" name="sub_organisasi[]" placeholder="Nama Sub Organisasi">
        <button class="btn btn-outline-danger modal-remove-sub" type="button">-</button>
    </div>`;
    $('#modal-sub-fields').append(newField);
}

function showValidationErrors(xhr, errorContainer) {
    if (xhr.status === 422) {
        let errors = xhr.responseJSON.errors;
        let errorMsg = '<ul>';
        $.each(errors, function(key, value) { 
            errorMsg += '<li>' + value[0] + '</li>'; 
        });
        errorMsg += '</ul>';
        errorContainer.html(errorMsg).show();
    }
}


// =======================================================================
// EVENT LISTENERS & INISIALISASI
// Kode yang perlu dijalankan setelah halaman siap, tetap di dalam
// $(document).ready()
// =======================================================================
$(document).ready(function() {
    const isEditMode = {{ isset($sasaran) ? 'true' : 'false' }};
    const sasaranData = isEditMode ? @json($sasaran ?? null) : null;
    
    const oldProvinsiId = '{{ old('provinsi_id', isset($sasaran) ? $sasaran->provinsi_id : '') }}';
    const oldKotaId = '{{ old('kota_id', isset($sasaran) ? $sasaran->kota_id : '') }}';
    const oldKecamatanId = '{{ old('kecamatan_id', isset($sasaran) ? $sasaran->kecamatan_id : '') }}';
    const oldKelurahanId = '{{ old('kelurahan_id', isset($sasaran) ? $sasaran->kelurahan_id : '') }}';
    const oldOrganisasiIndukId = '{{ old('organisasi_induk_id', '') }}';
    const oldOrganisasiFinalId = '{{ old('organisasi_id', isset($sasaran) ? $sasaran->organisasi_id : '') }}';
    let oldSubOrgIdToSelect = null;

    // === EVENT HANDLERS ===
    
    $('#toggle-nik-input').on('click', function() {
        const nikSection = $('#nik-input-section');
        const toggleSection = $('#nik-toggle-section');
        
        if (nikSection.is(':visible')) {
            nikSection.slideUp(300);
            toggleSection.slideDown(300);
            $(this).html('<i class="fas fa-id-card me-2"></i>Input NIK - Klik disini');
        } else {
            toggleSection.slideUp(300);
            nikSection.slideDown(300, function() {
                $('#nik-input-1').focus();
            });
            $(this).html('<i class="fas fa-eye-slash me-2"></i>Sembunyikan NIK');
        }
    });
    
    $('#nik-input-container').on('input', '.nik-digit-input', function() {
        if (this.value.length === 1 && $(this).next('.nik-digit-input').length) {
            $(this).next('.nik-digit-input').focus();
        }
        updateHiddenNik();
    }).on('keydown', '.nik-digit-input', function(e) {
        if (e.key === 'Backspace' && this.value.length === 0 && $(this).prev('.nik-digit-input').length) {
            $(this).prev('.nik-digit-input').focus();
        }
        if (e.key === 'ArrowLeft' && $(this).prev('.nik-digit-input').length) {
            $(this).prev('.nik-digit-input').focus();
        }
        if (e.key === 'ArrowRight' && $(this).next('.nik-digit-input').length) {
            $(this).next('.nik-digit-input').focus();
        }
    });

    $('#nik-input-1').on('paste', function(e) {
        e.preventDefault();
        const pastedData = (e.originalEvent || e).clipboardData.getData('text').replace(/\D/g, '');
        if (pastedData.length >= 16) {
            $('#nik-input-container .nik-digit-input').each(function(i) {
                $(this).val(pastedData[i] || '');
            });
            updateHiddenNik();
            $('#nik-input-16').focus();
        }
    });

    $('#tgl_lahir_dd, #tgl_lahir_mm, #tgl_lahir_yyyy').on('input keyup change', validateAndSetDate);
    
    $('#tgl_lahir_dd').on('input', function() { 
        if ($(this).val().length === 2) $('#tgl_lahir_mm').focus(); 
    });
    
    $('#tgl_lahir_mm').on('input', function() { 
        if ($(this).val().length === 2) $('#tgl_lahir_yyyy').focus(); 
    });

    $('#no_hp').on('input', function() {
        let input = $(this).val().replace(/[^\d\s]/g, '');
        let cleanInput = input.replace(/\s/g, '');
        
        if (cleanInput.length > 13) {
            cleanInput = cleanInput.substring(0, 13);
        }
        
        let formattedInput = '';
        for(let i = 0; i < cleanInput.length; i++) {
            if (i > 0 && i % 4 == 0) formattedInput += ' ';
            formattedInput += cleanInput[i];
        }
        
        $(this).val(formattedInput);
        validatePhoneNumber();
    });

    $('#sasaranForm').on('submit', function(e) {
        if (!validatePhoneNumber()) {
            e.preventDefault();
            $('#no_hp').focus();
            return false;
        }
    });

    $('#provinsi_id').on('change', function() {
        loadDropdown('{{ url('/ajax/get-kota/') }}/', $(this).val(), '#kota_id', 'Pilih Kota/Kabupaten', oldKotaId);
    });

    $('#kota_id').on('change', function() {
        loadDropdown('{{ url('/ajax/get-kecamatan/') }}/', $(this).val(), '#kecamatan_id', 'Pilih Kecamatan', oldKecamatanId);
    });

    $('#kecamatan_id').on('change', function() {
        loadDropdown('{{ url('/ajax/get-kelurahan/') }}/', $(this).val(), '#kelurahan_id', 'Pilih Kelurahan/Desa', oldKelurahanId);
    });

    $('#organisasi_induk_id').on('change', function() {
        const indukId = $(this).val();
        $('#organisasi_id_hidden').val(indukId);
        
        $('#sub_organisasi_id').empty().append('<option value="">Pilih Sub Organisasi</option>');
        $('#sub_organisasi_container').hide();

        if (indukId) {
            loadDropdown('{{ url('/ajax/get-sub-organisasi/') }}/', indukId, '#sub_organisasi_id', '-- Opsional --', oldSubOrgIdToSelect, function() {
                $('#sub_organisasi_container').show();
            });
        }
    });

    $('#sub_organisasi_id').on('change', function() {
        $('#organisasi_id_hidden').val($(this).val() || $('#organisasi_induk_id').val());
    });

    $('#modal-add-sub').on('click', addSubOrganisasiField);
    $('#modal-sub-fields').on('click', '.modal-remove-sub', function() {
        $(this).closest('.input-group').remove();
    });

    $('#save-quick-organisasi').on('click', function() {
        const form = $('#quick-add-organisasi-form');
        const modalError = $('#modal-error');
        
        modalError.hide();
        
        $.ajax({
            url: "{{ route('organisasi.quickStore') }}", 
            type: "POST", 
            data: form.serialize() + '&_token=' + '{{ csrf_token() }}',
            success: function(response) {
                if (response.success) {
                    const newOption = new Option(response.data.nama_organisasi, response.data.id, true, true);
                    $('#organisasi_induk_id').append(newOption).trigger('change');
                    $('#addOrganisasiModal').modal('hide');
                    form[0].reset();
                    $('#modal-sub-fields').empty();
                }
            },
            error: function(xhr) {
                showValidationErrors(xhr, modalError);
            }
        });
    });

    // === INITIALIZATION ===
    function restoreOldInput() {
        const oldNik = '{{ old('nik', isset($sasaran) ? $sasaran->nik : '') }}';
        if (oldNik) {
            $('#nik-toggle-section').hide();
            $('#nik-input-section').show();
            $('#toggle-nik-input').html('<i class="fas fa-eye-slash me-2"></i>Sembunyikan NIK');
            
            $('#nik-input-container .nik-digit-input').each(function(i) {
                $(this).val(oldNik[i] || '');
            });
            updateHiddenNik();
        }

        const oldTglLahir = '{{ old('tgl_lahir', isset($sasaran) ? $sasaran->tgl_lahir : '') }}';
        if (oldTglLahir) {
            const parts = oldTglLahir.split('-');
            if (parts.length === 3) {
                $('#tgl_lahir_yyyy').val(parts[0]);
                $('#tgl_lahir_mm').val(parts[1]);
                $('#tgl_lahir_dd').val(parts[2]);
                validateAndSetDate();
            }
        }

        if (isEditMode && sasaranData) {
            const orgIndukIdToSelect = oldOrganisasiIndukId || (sasaranData.organisasi ? (sasaranData.organisasi.parent_id || sasaranData.organisasi.id) : '');
            const finalOrgId = oldOrganisasiFinalId;
            
            if (orgIndukIdToSelect) {
                if (finalOrgId && finalOrgId !== orgIndukIdToSelect) {
                    oldSubOrgIdToSelect = finalOrgId;
                }
                $('#organisasi_induk_id').val(orgIndukIdToSelect).trigger('change');
            }
        } else if (oldOrganisasiIndukId) {
            if (oldOrganisasiFinalId && oldOrganisasiFinalId !== oldOrganisasiIndukId) {
                oldSubOrgIdToSelect = oldOrganisasiFinalId;
            }
            $('#organisasi_induk_id').val(oldOrganisasiIndukId).trigger('change');
        }

        if (oldProvinsiId) {
            $('#provinsi_id').val(oldProvinsiId).trigger('change');
        }
    }
    restoreOldInput();
});
</script>