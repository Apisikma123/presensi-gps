<form action="{{ route('karyawan.store') }}" id="formcreateKaryawan" class="no-ajax-modal" data-custom-ajax="true" method="POST" enctype="multipart/form-data" novalidate>
    @csrf

    <!-- SECTION 1: DATA POKOK KARYAWAN -->
    <div class="form-section-header">
        <div class="section-icon bg-label-primary text-primary">
            <i class="ti ti-user-check"></i>
        </div>
        <div>
            <h6 class="section-title">Data Identitas Karyawan</h6>
            <span class="section-subtitle">Informasi identitas pribadi tenaga kerja</span>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-user" label="Nama Karyawan" name="nama_karyawan" placeholder="Masukkan nama lengkap karyawan" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-barcode" label="NIK / ID Karyawan" name="nik_show" placeholder="Kosongkan untuk NIK otomatis" optional="true" />
        </div>

        <div class="col-md-6 col-12">
            <div class="form-group mb-3">
                <label style="font-weight: 600" class="form-label d-flex align-items-center justify-content-between mb-1.5" for="jenis_kelamin">
                    <span>Jenis Kelamin <span class="text-danger fw-bold">*</span></span>
                </label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti ti-gender-intergender"></i></span>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L">Laki - Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-phone" label="No. Handphone / WhatsApp" name="no_hp" placeholder="Contoh: 08123456789" optional="true" />
        </div>
    </div>

    <!-- SECTION 2: PENUGASAN & POSISI KERJA -->
    <div class="form-section-header mt-2">
        <div class="section-icon bg-label-success text-success">
            <i class="ti ti-briefcase"></i>
        </div>
        <div>
            <h6 class="section-title">Penugasan & Posisi Kerja</h6>
            <span class="section-subtitle">Pengaturan divisi, kantor cabang, dan shift penugasan</span>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-12">
            <x-select-label label="Kantor Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang" placeholder="Pilih Cabang" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept" placeholder="Pilih Departemen" upperCase="true" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Jabatan" name="kode_jabatan" :data="$jabatan" key="kode_jabatan" textShow="nama_jabatan" placeholder="Pilih Jabatan" upperCase="true" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Shift Kerja" name="kode_jam_kerja" :data="$jamkerja" key="kode_jam_kerja" textShow="nama_jam_kerja" placeholder="Pilih Shift Kerja" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-calendar" datepicker="flatpickr-date" label="Tanggal Masuk" name="tanggal_masuk" placeholder="Pilih tanggal bergabung" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group mb-3">
                <label style="font-weight: 600" class="form-label d-flex align-items-center justify-content-between mb-1.5" for="karyawan_password">
                    <span>Password Akun Presensi</span>
                    <span class="badge bg-label-info text-info fw-semibold" style="font-size: 11px;">Otomatis Dibuat Acak</span>
                </label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti ti-lock"></i></span>
                    <input type="password" class="form-control" id="karyawan_password" name="password" placeholder="Kosongkan untuk generate password otomatis" autocomplete="new-password">
                    <span class="input-group-text cursor-pointer" id="togglePasswordBtn" style="cursor: pointer;" title="Lihat / Sembunyikan Password">
                        <i class="ti ti-eye" id="togglePasswordIcon"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 3: FOTO PROFIL -->
    <div class="form-section-header mt-2">
        <div class="section-icon bg-label-warning text-warning">
            <i class="ti ti-camera"></i>
        </div>
        <div>
            <h6 class="section-title">Foto Profil Karyawan</h6>
            <span class="section-subtitle">Foto resmi untuk identitas aplikasi presensi (Opsional)</span>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <x-input-file name="foto" label="Upload Foto Profil" helper="Format: JPG, PNG, WEBP (Maks. 2MB)" />
        </div>
    </div>

    <!-- BUTTON ACTIONS -->
    <div class="modal-footer-standard d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
            Batal
        </button>
        <button class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4" type="submit" id="btnSimpanKaryawan">
            <i class="ti ti-device-floppy"></i>
            <span>Simpan Data Karyawan</span>
        </button>
    </div>
</form>

<script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

<script>
    $(function () {
        $(".flatpickr-date").flatpickr({
            allowInput: true,
            dateFormat: "Y-m-d"
        });

        // Toggle password visibility
        $('#togglePasswordBtn').on('click', function() {
            const input = $('#karyawan_password');
            const icon = $('#togglePasswordIcon');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('ti-eye').addClass('ti-eye-off');
            } else {
                input.attr('type', 'password');
                icon.removeClass('ti-eye-off').addClass('ti-eye');
            }
        });

        // Robust Client-Side Validation + AJAX Submit (Never close modal on validation failure!)
        var isSubmittingKaryawan = false;
        $('#formcreateKaryawan').off('submit').on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            if (isSubmittingKaryawan) {
                return false;
            }

            const form = this;
            const $form = $(form);
            const $btnSubmit = $('#btnSimpanKaryawan');

            // Clear previous invalid states
            $form.find('.is-invalid').removeClass('is-invalid');

            // Required fields definition
            const requiredFields = [
                { name: 'nama_karyawan', label: 'Nama Karyawan' },
                { name: 'jenis_kelamin', label: 'Jenis Kelamin' },
                { name: 'kode_cabang', label: 'Kantor Cabang' },
                { name: 'kode_dept', label: 'Departemen' },
                { name: 'kode_jabatan', label: 'Jabatan' },
                { name: 'kode_jam_kerja', label: 'Shift Kerja' },
                { name: 'tanggal_masuk', label: 'Tanggal Masuk' }
            ];

            let missing = [];
            let $firstMissing = null;

            requiredFields.forEach(function(field) {
                const $input = $form.find(`[name="${field.name}"]`);
                const val = $input.val();
                if (!val || (typeof val === 'string' && val.trim() === '')) {
                    $input.addClass('is-invalid');
                    missing.push(field.label);
                    if (!$firstMissing) {
                        $firstMissing = $input;
                    }
                }
            });

            // If required fields missing, halt immediately & inform user without closing modal
            if (missing.length > 0) {
                if ($firstMissing) {
                    $firstMissing.focus();
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'Kolom Wajib Belum Diisi',
                    html: '<div class="text-start fs-6">Silakan lengkapi kolom yang bertanda bintang (<span class="text-danger">*</span>):' +
                          '<ul class="mt-2 mb-0 ps-3">' +
                          missing.map(m => `<li><strong>${m}</strong></li>`).join('') +
                          '</ul></div>',
                    confirmButtonColor: '#1E4D3E',
                    confirmButtonText: 'Lengkapi Sekarang'
                });
                return false;
            }

            // AJAX Submission: Modal stays open upon server validation error
            isSubmittingKaryawan = true;
            const formData = new FormData(form);
            const originalBtnHtml = $btnSubmit.html();
            $btnSubmit.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    $('#modal').modal('hide');
                    var tempPw = response.temp_password;
                    var username = response.username || '';
                    var nama = response.nama_karyawan || '';

                    if (!tempPw && response.message && response.message.indexOf('Password sementara:') !== -1) {
                        var match = response.message.match(/Password sementara:\s*([^\)]+)/i);
                        if (match) tempPw = match[1].trim();
                    }

                    if (tempPw) {
                        var credText = 'Nama: ' + (nama || '-') + '\nUsername (NIK): ' + username + '\nPassword Sementara: ' + tempPw;
                        Swal.fire({
                            icon: 'success',
                            title: 'Karyawan Berhasil Disimpan',
                            html: '<div class="text-start fs-6 mt-2">' +
                                  '<p class="text-muted mb-2" style="font-size: 13px;">Akun mobile untuk <strong>' + (nama || 'karyawan') + '</strong> berhasil dibuat dengan password sementara berikut:</p>' +
                                  '<div class="p-3 rounded-3 mb-3" style="background: #F8FAFC; border: 1px solid #E2E8F0;">' +
                                  '<div class="d-flex justify-content-between align-items-center mb-1.5">' +
                                  '<span class="text-muted small">Username (NIK):</span>' +
                                  '<span class="fw-bold font-mono text-dark" style="font-size: 14px;">' + (username || '-') + '</span>' +
                                  '</div>' +
                                  '<div class="d-flex justify-content-between align-items-center">' +
                                  '<span class="text-muted small">Password Sementara:</span>' +
                                  '<span class="fw-bold font-mono text-primary" style="font-size: 16px; letter-spacing: 0.5px;">' + tempPw + '</span>' +
                                  '</div>' +
                                  '</div>' +
                                  '<button type="button" id="btnCopyCredPopup" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1.5" style="border-radius: 8px; font-weight: 600;">' +
                                  '<i class="ti ti-copy" style="font-size: 16px;"></i> <span id="btnCopyCredPopupText">Salin Akun & Password</span>' +
                                  '</button>' +
                                  '</div>',
                            confirmButtonColor: '#1E4D3E',
                            confirmButtonText: 'Selesai',
                            allowOutsideClick: false,
                            didOpen: function() {
                                var copyBtn = document.getElementById('btnCopyCredPopup');
                                if (copyBtn) {
                                    copyBtn.addEventListener('click', function() {
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(credText).then(function() {
                                                document.getElementById('btnCopyCredPopupText').innerText = 'Berhasil Disalin!';
                                                copyBtn.className = 'btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1.5';
                                            });
                                        } else {
                                            var tempInput = document.createElement('textarea');
                                            tempInput.value = credText;
                                            document.body.appendChild(tempInput);
                                            tempInput.select();
                                            document.execCommand('copy');
                                            document.body.removeChild(tempInput);
                                            document.getElementById('btnCopyCredPopupText').innerText = 'Berhasil Disalin!';
                                            copyBtn.className = 'btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1.5';
                                        }
                                    });
                                }
                            }
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        var msg = response.message || 'Data Karyawan Berhasil Disimpan';
                        try {
                            sessionStorage.setItem('flash_success', msg);
                        } catch(e) {}
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    isSubmittingKaryawan = false;
                    $btnSubmit.prop('disabled', false).html(originalBtnHtml);

                    let errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.status === 422 && xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorList = Object.keys(errors).map(key => {
                                $form.find(`[name="${key}"]`).addClass('is-invalid');
                                return `<li>${errors[key][0]}</li>`;
                            }).join('');
                            errorMsg = `<ul class="text-start mt-2 ps-3">${errorList}</ul>`;
                        } else if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        html: errorMsg,
                        confirmButtonColor: '#1E4D3E'
                    });
                }
            });
        });
    });
</script>