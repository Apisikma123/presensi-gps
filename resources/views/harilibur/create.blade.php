<form action="{{ route('harilibur.store') }}" method="POST" id="formHariLibur">
    @csrf
    <x-input-with-icon icon="ti ti-calendar" label="Tanggal Libur" name="tanggal" datepicker="flatpickr-date" />

    @if ($user->hasRole(['super admin', 'admin pusat']) || !$cabang->isEmpty())
        <div class="form-group mb-3">
            <label for="kode_cabang" class="form-label" style="font-weight: 600; font-size: 13px;">Cabang / Outlet</label>
            <select name="kode_cabang" id="kode_cabang" class="form-select select2Kodecabang">
                <option value="">Pilih Cabang / Cakupan</option>
                <option value="ALL" style="font-weight: 700; color: #1E4D3E;">SEMUA CABANG (LIBUR NASIONAL / BERSAMA)</option>
                @foreach ($cabang as $c)
                    <option value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-check mb-3 p-2.5 rounded-2" style="background: rgba(30, 77, 62, 0.05); border: 1px solid rgba(30, 77, 62, 0.15);">
            <input class="form-check-input ms-0 me-2" type="checkbox" name="auto_assign_karyawan" id="auto_assign_karyawan" value="1" checked>
            <label class="form-check-label text-dark fw-semibold" for="auto_assign_karyawan" style="font-size: 13px;">
                Otomatis daftarkan seluruh karyawan aktif
            </label>
            <div class="text-muted mt-1" style="font-size: 11px; line-height: 1.4;">
                Karyawan langsung tercatat libur pada tanggal ini tanpa perlu dipilih satu per satu secara manual.
            </div>
        </div>
    @endif

    <x-textarea label="Keterangan Libur (contoh: HUT RI, Idul Fitri)" name="keterangan" />

    <div class="form-group mb-2 pt-2">
        <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-1.5" id="btnSimpan" style="padding: 10px 16px; font-weight: 600;">
            <i class="ti ti-device-floppy fs-5"></i>
            <span>Simpan Hari Libur</span>
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        const form = $('#formHariLibur');
        $(".flatpickr-date").flatpickr();
        const select2Kodecabang = $(".select2Kodecabang");

        if (select2Kodecabang.length) {
            select2Kodecabang.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Pilih Cabang / Cakupan',
                    dropdownParent: $this.parent()
                });
            });
        }

        function buttonDisable() {
            $("#btnSimpan").prop('disabled', true);
            $("#btnSimpan").html(`
            <div class="spinner-border spinner-border-sm text-white me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Menyimpan...`);
        }

        form.submit(function(e) {
            const tanggal = form.find("#tanggal").val();
            const kode_cabang = form.find("#kode_cabang").val();
            const keterangan = form.find("#keterangan").val();

            if (tanggal == "") {
                Swal.fire({
                    title: "Peringatan",
                    text: "Tanggal libur wajib diisi!",
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => form.find("#tanggal").focus(),
                });
                return false;
            } else if (kode_cabang == "") {
                Swal.fire({
                    title: "Peringatan",
                    text: "Silakan pilih cabang atau semua cabang!",
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => form.find("#kode_cabang").focus(),
                });
                return false;
            } else if (keterangan == "") {
                Swal.fire({
                    title: "Peringatan",
                    text: "Keterangan hari libur wajib diisi!",
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => form.find("#keterangan").focus(),
                });
                return false;
            } else {
                buttonDisable();
            }
        });
    });
</script>
