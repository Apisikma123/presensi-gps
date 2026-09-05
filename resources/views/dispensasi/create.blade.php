<form action="{{ route('dispensasi.store') }}" method="POST" id="formDispensasi">
    @csrf
    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label fw-semibold">Karyawan</label>
            <select name="nik" id="nik" class="form-select select2" required>
                <option value="">Pilih Karyawan</option>
                @foreach ($karyawan as $k)
                    <option value="{{ $k->nik }}">{{ $k->nik }} - {{ $k->nama_karyawan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 mb-3">
            <label class="form-label fw-semibold">Tanggal Dispensasi</label>
            <input type="text" name="tanggal" id="tanggal" class="form-control flatpickr-date" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="col-12 mb-3">
            <label class="form-label fw-semibold">Batas Jam Dispensasi</label>
            <input type="time" name="batas_dispensasi" id="batas_dispensasi" class="form-control" value="07:30" step="1" required>
            <small class="text-muted">Karyawan yang absen masuk sebelum batas jam ini akan dianggap HADIR (Dispensasi).</small>
        </div>
        <div class="col-12 mb-3">
            <label class="form-label fw-semibold">Alasan Dispensasi</label>
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
                dropdownParent: $('#modalDispensasi')
            });
        }
        if (typeof flatpickr !== "undefined") {
            flatpickr("#tanggal", {
                dateFormat: "Y-m-d"
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
