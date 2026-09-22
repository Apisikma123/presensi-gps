<form action="{{ route('dispensasi.store') }}" method="POST" id="formDispensasi">
    @csrf
    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label fw-semibold">Karyawan <span class="text-danger fw-bold ms-0.5">*</span></label>
            <select name="nik" id="nik" class="form-select select2" required>
                <option value="">Pilih Karyawan</option>
                @foreach ($karyawan as $k)
                    <option value="{{ $k->nik }}">{{ $k->nik }} - {{ $k->nama_karyawan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 mb-3">
            <x-input-with-icon icon="ti ti-calendar" label="Tanggal Dispensasi" name="tanggal" id="tanggal" datepicker="flatpickr-date" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d') }}" required="true" />
        </div>
        <div class="col-12 mb-3">
            <x-input-with-icon icon="ti ti-clock" label="Batas Jam Dispensasi" name="batas_dispensasi" id="batas_dispensasi" datepicker="flatpickr-time" placeholder="Contoh: 07:30" value="07:30" readonly="true" required="true" />
            <small class="text-muted">Karyawan yang absen masuk sebelum batas jam ini akan dianggap HADIR (Dispensasi).</small>
        </div>
        <div class="col-12 mb-3">
            <label class="form-label fw-semibold">Alasan Dispensasi <span class="text-danger fw-bold ms-0.5">*</span></label>
            <textarea name="alasan" id="alasan" class="form-control" rows="3" placeholder="Contoh: Kendala operasional jalan raya, instruksi darurat outlet..." required></textarea>
        </div>
        <div class="col-12 text-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="btnSubmitDispensasi">Simpan</button>
        </div>
    </div>
</form>

<script>
    $(function() {
        if (typeof $(".select2") !== "undefined") {
            $(".select2").select2({
                dropdownParent: $('#modal').length ? $('#modal') : $('body')
            });
        }

        $('#formDispensasi').submit(function(e) {
            const nik = $('#nik').val();
            const tanggal = $('#tanggal').val();
            const batas = $('#batas_dispensasi').val();
            const alasan = $('#alasan').val();

            if (!nik || !tanggal || !batas || !alasan) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Semua field wajib diisi!'
                });
            }
        });
    });
</script>
