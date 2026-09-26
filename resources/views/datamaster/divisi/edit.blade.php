<form action="{{ route('divisi.update', Crypt::encrypt($divisi->id)) }}" method="POST" id="formEditDivisi">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-12">
            <x-input-with-icon-label label="Kode Divisi" name="kode_divisi" icon="ti ti-barcode" :value="$divisi->kode_divisi" readonly="true" />
        </div>
        <div class="col-12">
            <x-input-with-icon-label label="Nama Divisi / Tim *" name="nama_divisi" icon="ti ti-users-group" :value="$divisi->nama_divisi" required="true" />
        </div>
        <div class="col-12 mb-3">
            <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Departemen Induk (Opsional)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="ti ti-building"></i></span>
                <select name="kode_dept" class="form-select">
                    <option value="">-- Tanpa Departemen Induk --</option>
                    @foreach ($departemen as $d)
                        <option value="{{ $d->kode_dept }}" {{ $divisi->kode_dept == $d->kode_dept ? 'selected' : '' }}>
                            {{ $d->nama_dept }} ({{ $d->kode_dept }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12 mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $divisi->is_active ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold text-dark" for="is_active">Status Divisi Aktif</label>
            </div>
        </div>
        <div class="col-12 text-end mt-2">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Simpan Perubahan</button>
        </div>
    </div>
</form>
