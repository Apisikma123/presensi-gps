<form action="{{ route('dispensasi.storeApprove', $dispensasi->id) }}" method="POST" id="formApproveDispensasi">
    @csrf
    <div class="row">
        <div class="col-12 mb-3">
            <div class="p-3 bg-light rounded border">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Karyawan:</span>
                    <span class="fw-bold text-dark">{{ $dispensasi->karyawan->nama_karyawan ?? $dispensasi->nik }} ({{ $dispensasi->nik }})</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Tanggal:</span>
                    <span class="fw-bold font-mono text-dark">{{ date('d/m/Y', strtotime($dispensasi->tanggal)) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Batas Dispensasi:</span>
                    <span class="fw-bold font-mono text-primary">{{ $dispensasi->batas_dispensasi }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Status Saat Ini:</span>
                    <span class="badge {{ $dispensasi->status == 'APPROVED' ? 'bg-success' : ($dispensasi->status == 'REJECTED' ? 'bg-danger' : 'bg-warning') }}">
                        {{ $dispensasi->status }}
                    </span>
                </div>
                <div class="mt-2 pt-2 border-top">
                    <span class="text-muted d-block small">Alasan:</span>
                    <div class="text-dark small">{{ $dispensasi->alasan }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 mb-3">
            <label class="form-label fw-semibold">Keputusan Persetujuan</label>
            <select name="status" id="status_approval" class="form-select" required>
                <option value="APPROVED" {{ $dispensasi->status == 'APPROVED' ? 'selected' : '' }}>Setujui (APPROVED)</option>
                <option value="REJECTED" {{ $dispensasi->status == 'REJECTED' ? 'selected' : '' }}>Tolak (REJECTED)</option>
                <option value="PENDING" {{ $dispensasi->status == 'PENDING' ? 'selected' : '' }}>Kembalikan ke PENDING</option>
            </select>
        </div>

        <div class="col-12 text-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan Keputusan</button>
        </div>
    </div>
</form>
