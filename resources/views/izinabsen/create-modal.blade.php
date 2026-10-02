<form action="{{ route('izinabsen.store') }}" method="POST" id="formIzin">
    @csrf
    <x-input-with-icon icon="ti ti-barcode" label="Auto" name="kode_izin" disabled="true" />
    <div class="form-group mb-3">
        <label for="nik" class="form-label fw-semibold" style="font-size: 13px;">Pilih Karyawan <span class="text-danger fw-bold ms-0.5">*</span></label>
        <select name="nik" id="nik" class="form-select select2Nik" required>
            <option value="">Pilih Karyawan</option>
            @foreach ($karyawan as $d)
                <option value="{{ $d->nik }}">{{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group mb-3">
        <label for="tipe_izin_modal" class="form-label fw-semibold" style="font-size: 13px;">Tipe Permisi / Izin</label>
        <select name="tipe_izin" id="tipe_izin_modal" class="form-select" onchange="toggleTipeIzinModal(this.value)">
            <option value="harian" selected>Izin Seharian (Full Day)</option>
            <option value="jam">Izin Jam Kerja (Hourly / Keluar Kantor)</option>
            <option value="pulang_cepat">Izin Pulang Lebih Cepat</option>
        </select>
    </div>

    {{-- Rentang Tanggal (Harian) --}}
    <div id="rowHarianModal" class="row">
        <div class="col-lg-6 col-sm-12 col-md-12">
            <x-input-with-icon icon="ti ti-calendar" label="Dari" name="dari" datepicker="flatpickr-date" />
        </div>
        <div class="col-lg-6 col-sm-12 col-md-12">
            <x-input-with-icon icon="ti ti-calendar" label="Sampai" name="sampai" datepicker="flatpickr-date" />
        </div>
    </div>
    <div id="rowJmlHariModal">
        <x-input-with-icon icon="ti ti-sun" label="Jumlah Hari Kerja" name="jml_hari" disabled="true" />
    </div>

    {{-- Tanggal & Jam (Untuk Izin Jam / Pulang Cepat) --}}
    <div id="rowJamModal" style="display:none;" class="row mb-3">
        <div class="col-lg-12 mb-3">
            <x-input-with-icon icon="ti ti-calendar" label="Tanggal Izin" name="tanggal_izin" datepicker="flatpickr-date" />
        </div>
        <div class="col-lg-6 col-sm-6" id="colJamMulaiModal">
            <label class="form-label fw-semibold" style="font-size: 13px;">Dari Jam</label>
            <input type="time" name="jam_mulai" class="form-control" value="10:00">
        </div>
        <div class="col-lg-6 col-sm-6" id="colJamSelesaiModal">
            <label class="form-label fw-semibold" style="font-size: 13px;">Sampai Jam</label>
            <input type="time" name="jam_selesai" class="form-control" value="12:00">
        </div>
        <div class="col-lg-12" id="colJamPulangModal" style="display:none;">
            <label class="form-label fw-semibold" style="font-size: 13px;">Jam Rencana Pulang</label>
            <input type="time" name="jam_pulang_cepat" class="form-control" value="15:00">
        </div>
    </div>

    <div class="form-group mb-3">
        <label for="keperluan_modal" class="form-label fw-semibold" style="font-size: 13px;">Keperluan / Acara Permisi <span class="text-danger fw-bold ms-0.5">*</span></label>
        <input type="text" name="keperluan" id="keperluan_modal" class="form-control" placeholder="Misal: Acara sekolah anak, urusan bank, fisioterapi, dll." required>
    </div>

    <x-textarea-label label="Keterangan Detail (Opsional)" name="keterangan_detail" />
    <div class="modal-footer-standard d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
        @if (request()->ajax())
            <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Batal</button>
        @else
            <a href="{{ route('izinabsen.index') }}" class="btn btn-outline-secondary px-3">Kembali</a>
        @endif
        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4" id="btnSimpan">
            <i class="ti ti-device-floppy"></i>
            <span>Simpan Pengajuan Izin</span>
        </button>
    </div>
</form>
<script>
    window.toggleTipeIzinModal = function(val) {
        if (val === 'jam') {
            $('#rowHarianModal, #rowJmlHariModal').hide();
            $('#rowJamModal').show();
            $('#colJamMulaiModal, #colJamSelesaiModal').show();
            $('#colJamPulangModal').hide();
        } else if (val === 'pulang_cepat') {
            $('#rowHarianModal, #rowJmlHariModal').hide();
            $('#rowJamModal').show();
            $('#colJamMulaiModal, #colJamSelesaiModal').hide();
            $('#colJamPulangModal').show();
        } else {
            $('#rowHarianModal, #rowJmlHariModal').show();
            $('#rowJamModal').hide();
        }
    };

    $(function() {
        const form = $('#formIzin');
        const batasi_hari_izin = "{{ $general_setting->batasi_hari_izin ?? 0 }}";
        const jml_hari_izin_max = "{{ $general_setting->jml_hari_izin_max ?? 0 }}";
        const sistem_hari_kerja = "{{ $general_setting->sistem_hari_kerja ?? '6' }}";

        $(".flatpickr-date").flatpickr({
            dateFormat: "Y-m-d"
        });

        const select2Nik = $('.select2Nik');
        if (select2Nik.length) {
            select2Nik.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Pilih Karyawan',
                    allowClear: true,
                    dropdownParent: $this.parent()
                });
            });
        }

        function hitungHari(startDate, endDate) {
            if (!startDate || !endDate) return 0;
            var start = new Date(startDate + 'T00:00:00');
            var end = new Date(endDate + 'T00:00:00');
            if (end < start) return 0;

            var count = 0;
            var cur = new Date(start);
            while (cur <= end) {
                var dayOfWeek = cur.getDay(); // 0 = Minggu, 6 = Sabtu
                var isOff = (dayOfWeek === 0) || (sistem_hari_kerja === '5' && dayOfWeek === 6);
                if (!isOff) {
                    count++;
                }
                cur.setDate(cur.getDate() + 1);
            }
            return count;
        }

        $("#dari,#sampai").on("change", function() {
            const dari = form.find("#dari").val();
            const sampai = form.find("#sampai").val();
            if (dari && sampai) {
                form.find("#jml_hari").val(hitungHari(dari, sampai));
            }
        });

        function buttonDisabled() {
            $("#btnSimpan").prop('disabled', true);
            $("#btnSimpan").html(`
            <div class="spinner-border spinner-border-sm text-white me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Loading..`);
        }

        form.submit(function(e) {
            const nik = form.find("#nik").val();
            const dari = form.find("#dari").val();
            const sampai = form.find("#sampai").val();
            const keterangan = form.find("#keterangan").val();
            const jml_hari = hitungHari(dari, sampai);

            if (nik == '') {
                Swal.fire({
                    title: "Oops!",
                    text: "Karyawan harus diisi !",
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => { form.find("#nik").focus(); },
                });
                return false;
            } else if (dari == '' || sampai == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Periode Izin Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => { form.find("#dari").focus(); }
                });
                return false;
            } else if (sampai < dari) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Periode Izin Tidak Valid !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => { form.find("#sampai").focus(); }
                });
                return false;
            } else if (jml_hari > parseInt(jml_hari_izin_max) && batasi_hari_izin == 1) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Periode Izin Tidak Boleh Lebih Dari ' + jml_hari_izin_max + ' Hari Kerja !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => { form.find("#sampai").focus(); }
                });
                return false;
            } else if (keterangan == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Keterangan Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => { form.find("#keterangan").focus(); }
                });
                return false;
            } else {
                buttonDisabled();
            }
        });
    });
</script>
