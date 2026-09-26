<form action="{{ route('divisi.store') }}" method="POST" id="formDivisi">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-input-with-icon-label label="Kode Divisi *" name="kode_divisi" icon="ti ti-barcode" placeholder="Contoh: DIV-OPS, IT-DEV" required="true" />
        </div>
        <div class="col-12">
            <x-input-with-icon-label label="Nama Divisi / Tim *" name="nama_divisi" icon="ti ti-users-group" placeholder="Contoh: Operasional Lapangan, Tim Backend" required="true" />
        </div>
        <div class="col-12 mb-3">
            <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Departemen Induk (Opsional)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ti ti-building"></i></span>
                <select name="kode_dept" class="form-select">
                    <option value="">-- Tanpa Departemen Induk --</option>
                    @foreach ($departemen as $d)
                        <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }} ({{ $d->kode_dept }})</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12 text-end mt-2">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Simpan Divisi</button>
        </div>
    </div>
</form>
