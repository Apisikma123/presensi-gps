<form action="{{ route('harilibur.store') }}" method="POST" id="formHariLibur">
    @csrf
    <x-input-with-icon icon="ti ti-calendar" label="Tanggal" name="tanggal" datepicker="flatpickr-date" />
    @if ($user->hasRole(['super admin', 'admin pusat']) || !$cabang->isEmpty())
        <div class="form-group mb-3">
            <label for="kode_cabang" class="form-label" style="font-weight: 600;">Cabang</label>
            <select name="kode_cabang" id="kode_cabang" class="form-select select2Kodecabang">
                <option value="">Pilih Cabang</option>
                <option value="ALL" style="font-weight: 700; color: #1E4D3E;">⭐ SEMUA CABANG (LIBUR NASIONAL / BERSAMA)</option>
                @foreach ($cabang as $c)
                    <option value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-check mb-3 p-2 rounded-2" style="background: rgba(30, 77, 62, 0.05); border: 1px solid rgba(30, 77, 62, 0.12);">
            <input class="form-check-input ms-1 me-2" type="checkbox" name="auto_assign_karyawan" id="auto_assign_karyawan" value="1" checked>
            <label class="form-check-label text-dark fw-bold" for="auto_assign_karyawan" style="font-size: 13px;">
                Otomatis daftarkan seluruh karyawan aktif
            </label>
            <div class="form-text text-muted ms-1" style="font-size: 11px;">
                Karyawan langsung terdaftar otomatis ke hari libur ini tanpa perlu checklist satu per satu di menu "Atur Karyawan".
            </div>
        </div>
    @endif
    <x-textarea label="Keterangan" name="keterangan" />
    <div class="form-group mb-3">
        <button class="btn btn-primary w-100" id="btnSimpan"><i class="ti ti-send me-1"></i>Submit</button>
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
                    placeholder: 'Pilih Cabang',
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
            Loading..`);
        }
        form.submit(function(e) {
            //e.preventDefault();
            const tanggal = form.find("#tanggal").val();
            const kode_cabang = form.find("#kode_cabang").val();
            const keterangan = form.find("#keterangan").val();

            if (tanggal == "") {
                Swal.fire({
                    title: "Oops!",
                    text: "Tanggal Harus Diisi !",
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: (e) => {
                        form.find("#tanggal").focus();
                    },
                });
                return false;
            } else if (kode_cabang == "") {
                Swal.fire({
                    title: "Oops!",
                    text: "Cabang Harus Diisi !",
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: (e) => {
                        form.find("#kode_cabang").focus();
                    },
                });
                return false;
            } else if (keterangan == "") {
                Swal.fire({
                    title: "Oops!",
                    text: "Keterangan Harus Diisi !",
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: (e) => {
                        form.find("#keterangan").focus();
                    },
                });
                return false;
            } else {
                buttonDisable();
            }
        });

    });
</script>
