<form action="{{ route('karyawan.update', Crypt::encrypt($karyawan->nik)) }}" id="formcreateKaryawan" class="no-ajax-modal" data-custom-ajax="true" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

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
            <x-input-with-icon-label icon="ti ti-user" label="Nama Karyawan" name="nama_karyawan" value="{{ $karyawan->nama_karyawan }}" placeholder="Masukkan nama lengkap karyawan" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-barcode" label="NIK / ID Karyawan" name="nik_show" value="{{ $karyawan->nik_show ?? $karyawan->nik }}" placeholder="ID / NIK Karyawan" optional="true" />
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
                        <option value="L" {{ $karyawan->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki - Laki</option>
                        <option value="P" {{ $karyawan->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-phone" label="No. Handphone / WhatsApp" name="no_hp" value="{{ $karyawan->no_hp }}" placeholder="Contoh: 08123456789" optional="true" />
        </div>
    </div>

    <!-- SECTION 2: PENUGASAN & JABATAN -->
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
            <x-select-label label="Kantor Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang" selected="{{ $karyawan->kode_cabang }}" placeholder="Pilih Cabang" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Departemen" name="kode_dept" :data="$departemen" selected="{{ $karyawan->kode_dept }}" key="kode_dept" textShow="nama_dept" placeholder="Pilih Departemen" upperCase="true" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Jabatan" name="kode_jabatan" :data="$jabatan" selected="{{ $karyawan->kode_jabatan }}" key="kode_jabatan" textShow="nama_jabatan" placeholder="Pilih Jabatan" upperCase="true" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Shift Kerja" name="kode_jam_kerja" :data="$jamkerja" key="kode_jam_kerja" textShow="nama_jam_kerja" selected="{{ $karyawan->kode_jam_kerja }}" placeholder="Pilih Shift Kerja" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-calendar" datepicker="flatpickr-date" label="Tanggal Masuk" name="tanggal_masuk" value="{{ $karyawan->tanggal_masuk }}" placeholder="Pilih tanggal bergabung" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group mb-3">
                <label style="font-weight: 600" class="form-label d-flex align-items-center justify-content-between mb-1.5" for="status_aktif_karyawan">
                    <span>Status Keaktifan <span class="text-danger fw-bold">*</span></span>
                </label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti ti-activity"></i></span>
                    <select name="status_aktif_karyawan" id="status_aktif_karyawan" class="form-select" required>
                        <option value="1" {{ $karyawan->status_aktif_karyawan == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ $karyawan->status_aktif_karyawan === '0' ? 'selected' : '' }}>Non Aktif</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 3: AKUN & KEAMANAN -->
    <div class="form-section-header mt-2">
        <div class="section-icon bg-label-warning text-warning">
            <i class="ti ti-lock"></i>
        </div>
        <div>
            <h6 class="section-title">Akun Login & Keamanan</h6>
            <span class="section-subtitle">Kredensial login aplikasi presensi karyawan</span>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="form-group mb-2">
                <label style="font-weight: 600" class="form-label d-flex align-items-center justify-content-between mb-1.5" for="karyawan_password_edit">
                    <span>Password Baru</span>
                    <span class="badge bg-label-secondary text-muted fw-normal" style="font-size: 11px;">Opsional</span>
                </label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti ti-key"></i></span>
                    <input type="password" class="form-control" id="karyawan_password_edit" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" autocomplete="new-password">
                    <span class="input-group-text cursor-pointer" id="togglePasswordEditBtn" style="cursor: pointer;" title="Lihat / Sembunyikan Password">
                        <i class="ti ti-eye" id="togglePasswordEditIcon"></i>
                    </span>
                </div>
                <div class="form-text d-flex align-items-center gap-1 mt-1 text-muted" style="font-size: 11.5px;">
                    <i class="ti ti-info-circle text-primary"></i>
                    <span>Username login: <strong>{{ $karyawan->nik }}</strong>. Password hanya diubah jika kolom di atas diisi.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 4: FOTO PROFIL -->
    <div class="form-section-header mt-2">
        <div class="section-icon bg-label-info text-info">
            <i class="ti ti-camera"></i>
        </div>
        <div>
            <h6 class="section-title">Foto Profil Karyawan</h6>
            <span class="section-subtitle">Foto resmi identitas karyawan (Opsional)</span>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <x-input-file name="foto" label="Foto Profil" :value="$karyawan->foto" helper="Format: JPG, PNG, WEBP (Maks. 2MB) • Kosongkan jika tidak diubah" />
        </div>
    </div>

    <!-- BUTTON ACTIONS -->
    <div class="modal-footer-standard d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
            Batal
        </button>
        <button class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4" type="submit" id="btnSimpanKaryawanEdit">
            <i class="ti ti-device-floppy"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

<script>
    $(function() {
        $(".flatpickr-date").flatpickr({
            allowInput: true,
            dateFormat: "Y-m-d"
        });

        // Toggle password edit visibility
        $('#togglePasswordEditBtn').on('click', function() {
            const input = $('#karyawan_password_edit');
            const icon = $('#togglePasswordEditIcon');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('ti-eye').addClass('ti-eye-off');
            } else {
                input.attr('type', 'password');
                icon.removeClass('ti-eye-off').addClass('ti-eye');
            }
        });

        // Robust Client-Side Validation + AJAX Submit (Never close modal on validation failure!)
        var isSubmittingKaryawanEdit = false;
        $('#formcreateKaryawan').off('submit').on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            if (isSubmittingKaryawanEdit) {
                return false;
            }

            const form = this;
            const $form = $(form);
            const $btnSubmit = $('#btnSimpanKaryawanEdit');

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
                { name: 'tanggal_masuk', label: 'Tanggal Masuk' },
                { name: 'status_aktif_karyawan', label: 'Status Keaktifan' }
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
            isSubmittingKaryawanEdit = true;
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
                    var msg = response.message || 'Data Karyawan Berhasil Disimpan';
                    try {
                        sessionStorage.setItem('flash_success', msg);
                    } catch(e) {}
                    window.location.reload();
                },
                error: function(xhr) {
                    isSubmittingKaryawanEdit = false;
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
