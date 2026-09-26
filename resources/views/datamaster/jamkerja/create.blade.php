<form action="{{ route('jamkerja.store') }}" id="formcreateJamKerja" method="POST">
    @csrf
    <x-input-with-icon icon="ti ti-barcode" label="Kode Jam Kerja" name="kode_jam_kerja" maxlength="4" placeholder="Contoh: JK01 (Maksimal 4 karakter)"
        required />
    <x-input-with-icon icon="ti ti-file-text" label="Nama Jam Kerja" name="nama_jam_kerja" maxlength="50"
        placeholder="Contoh: Jam Kerja Pagi (Maksimal 50 karakter)" required />
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-input-with-icon icon="ti ti-clock" label="Jam Masuk" name="jam_masuk" datepicker="flatpickr-time" placeholder="Contoh: 08:00" required />
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-input-with-icon icon="ti ti-clock" label="Jam Pulang" name="jam_pulang" datepicker="flatpickr-time" placeholder="Contoh: 17:00" required />
        </div>
        <div class="col-12 mb-2" style="margin-top: -8px;">
            <small class="text-muted" style="font-size: 11.5px;">
                <i class="ti ti-info-circle me-1 text-primary"></i>Format 24 Jam: <strong>00:00</strong> = Jam 12 Malam, <strong>12:00</strong> = Jam 12 Siang
            </small>
        </div>
    </div>
    <input type="hidden" name="istirahat" id="istirahat" value="0">
    <x-input-with-icon icon="ti ti-clock" label="Total Jam" name="total_jam" id="total_jam" type="number" placeholder="Contoh: 8 (Minimal 1, Maksimal 24 jam)"
        min="1" max="24" required />
    <x-input-with-icon icon="ti ti-file-text" label="Keterangan" name="keterangan" maxlength="255"
        placeholder="Contoh: Jam kerja untuk shift pagi (Opsional, maksimal 255 karakter)" />
    <x-input-with-icon icon="ti ti-palette" label="Warna (Untuk Laporan)" name="color" type="color" value="#3C2A21" placeholder="Pilih Warna" />
    <div class="form-group mb-3">
        <label for="lintashari" class="form-label" style="font-weight: 600;">
            Lintas Hari <span class="text-danger">*</span>
        </label>
        <select name="lintashari" id="lintashari" class="form-select" required>
            <option value="">Pilih Lintas Hari</option>
            <option value="1">Ya</option>
            <option value="0" selected>Tidak</option>
        </select>
    </div>
    <div id="sectionLintasHari" style="display: none;">
        <x-input-with-icon icon="ti ti-clock-pause" label="Batas Jam Pulang Lintas Hari" name="batas_presensi_pulang" datepicker="flatpickr-time"
            placeholder="Contoh: 10:00 (Opsional, jika kosong menggunakan General Setting)" />
        <small class="text-muted d-block mb-3" style="margin-top: -10px;">
            <i class="ti ti-info-circle me-1"></i>Jika dikosongkan, sistem akan menggunakan batas dari General Setting.
        </small>
    </div>
    <div class="modal-footer-standard d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4" id="btnSimpan">
            <i class="ti ti-device-floppy"></i>
            <span>Simpan Shift</span>
        </button>
    </div>
</form>
<script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/jamkerja.js') }}?v={{ time() }}"></script>
<script>
    $(document).ready(function() {
        function toggleLintasHari() {
            if ($('#lintashari').val() == '1') {
                $('#sectionLintasHari').slideDown();
            } else {
                $('#sectionLintasHari').slideUp();
                $('#batas_presensi_pulang').val('');
            }
        }
        toggleLintasHari();

        $('#lintashari').on('change', function() {
            toggleLintasHari();
        });

        $("#jam_masuk,#jam_pulang,#batas_presensi_pulang").mask("00:00");
    });
</script>
