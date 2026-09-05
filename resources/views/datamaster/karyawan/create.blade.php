<form action="{{ route('karyawan.store') }}" id="formcreateKaryawan" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- SECTION 1: DATA POKOK KARYAWAN -->
    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-label-primary" style="width: 28px; height: 28px;">
            <i class="ti ti-user-check" style="font-size: 16px;"></i>
        </div>
        <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">Data Pokok Karyawan</h6>
    </div>

    <div class="row g-2">
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-user" label="Nama Karyawan" name="nama_karyawan" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-barcode" label="NIK / ID Karyawan" name="nik_show" placeholder="Kosongkan jika ingin dibuat otomatis" />
        </div>

        <div class="col-md-6 col-12">
            <div class="form-group mb-3">
                <label style="font-weight: 600" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti ti-gender-intergender"></i></span>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L">Laki - Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-phone" label="No. HP / WhatsApp" name="no_hp" placeholder="Contoh: 08123456789" />
        </div>
    </div>

    <!-- SECTION 2: PENUGASAN & JABATAN -->
    <div class="d-flex align-items-center gap-2 mb-3 mt-2 pb-2 border-bottom">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-label-success" style="width: 28px; height: 28px;">
            <i class="ti ti-briefcase" style="font-size: 16px;"></i>
        </div>
        <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">Penugasan & Posisi Kerja</h6>
    </div>

    <div class="row g-2">
        <div class="col-md-6 col-12">
            <x-select-label label="Kantor Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept" upperCase="true" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Jabatan" name="kode_jabatan" :data="$jabatan" key="kode_jabatan" textShow="nama_jabatan" upperCase="true" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Shift Kerja (Pagi / Siang)" name="kode_jam_kerja" :data="$jamkerja" key="kode_jam_kerja" textShow="nama_jam_kerja" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-calendar" datepicker="flatpickr-date" label="Tanggal Masuk" name="tanggal_masuk" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-file name="foto" label="Foto Karyawan (Opsional)" />
        </div>
    </div>

    <!-- SECTION 3: AKUN LOGIN -->
    <div class="d-flex align-items-center gap-2 mb-3 mt-2 pb-2 border-bottom">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-label-warning" style="width: 28px; height: 28px;">
            <i class="ti ti-lock" style="font-size: 16px;"></i>
        </div>
        <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">Akun Login Aplikasi Presensi</h6>
    </div>

    <div class="row g-2">
        <div class="col-12">
            <div class="form-group mb-2">
                <label style="font-weight: 600" class="form-label">Password Login Karyawan</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti ti-key"></i></span>
                    <input type="password" class="form-control" id="karyawan_password" name="password" placeholder="Kosongkan jika ingin password default: 12345" autocomplete="new-password">
                    <span class="input-group-text cursor-pointer" id="togglePasswordBtn" style="cursor: pointer;" title="Lihat / Sembunyikan Password">
                        <i class="ti ti-eye" id="togglePasswordIcon"></i>
                    </span>
                </div>
                <div class="form-text d-flex align-items-center gap-1 mt-1 text-muted" style="font-size: 11.5px;">
                    <i class="ti ti-info-circle text-primary"></i>
                    <span>Akun login akan <strong>langsung aktif otomatis</strong>. Username menggunakan NIK, password default: <strong>12345</strong>.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- BUTTON ACTIONS -->
    <div class="modal-footer-standard d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
            Batal
        </button>
        <button class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4" type="submit">
            <i class="ti ti-device-floppy"></i>
            <span>Simpan Data Karyawan</span>
        </button>
    </div>
</form>

<script src="{{ asset('assets/js/pages/karyawan.js') }}"></script>
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
    });
</script>