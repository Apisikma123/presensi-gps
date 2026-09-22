<form action="{{ route('dispensasi.storeApprove', $dispensasi->id) }}" method="POST" id="formApproveDispensasi">
    @csrf
    <div class="row">
        <div class="col">
            <table class="table">
                <tr>
                    <th>No. Pengajuan</th>
                    <td class="text-end font-mono">#DSP-{{ str_pad($dispensasi->id, 5, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <th>Tanggal Presensi</th>
                    <td class="text-end">{{ DateToIndo($dispensasi->tanggal) }}</td>
                </tr>
                <tr>
                    <th>NIK</th>
                    <td class="text-end font-mono">{{ $dispensasi->nik }}</td>
                </tr>
                <tr>
                    <th>Nama Karyawan</th>
                    <td class="text-end fw-semibold text-dark">{{ $dispensasi->karyawan->nama_karyawan ?? $dispensasi->nik }}</td>
                </tr>
                <tr>
                    <th>Jabatan</th>
                    <td class="text-end">{{ $dispensasi->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Dept</th>
                    <td class="text-end">{{ $dispensasi->karyawan->departemen->nama_dept ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Cabang</th>
                    <td class="text-end">{{ $dispensasi->karyawan->cabang->nama_cabang ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Batas Toleransi</th>
                    <td class="text-end font-mono fw-bold" style="color: #1E4D3E;">{{ $dispensasi->batas_dispensasi }}</td>
                </tr>
                <tr>
                    <th>Alasan</th>
                    <td class="text-end">{{ $dispensasi->alasan }}</td>
                </tr>
                <tr>
                    <th>Status Saat Ini</th>
                    <td class="text-end">
                        @if ($dispensasi->status === 'APPROVED')
                            <span class="badge bg-success">Disetujui</span>
                        @elseif ($dispensasi->status === 'REJECTED')
                            <span class="badge bg-danger">Ditolak</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <button class="btn w-100 text-white fw-bold shadow-sm" name="approve" type="submit" value="approve" style="background-color: #1E4D3E; border: 1px solid #163C30; border-radius: 10px; height: 42px;">
                <i class="ti ti-thumb-up me-1"></i> Approve
            </button>
        </div>
        <div class="col">
            <button class="btn w-100 text-white fw-bold shadow-sm" name="tolak" type="submit" value="tolak" style="background-color: #DC2626; border: 1px solid #B91C1C; border-radius: 10px; height: 42px;">
                <i class="ti ti-thumb-down me-1"></i> Tolak
            </button>
        </div>
    </div>
</form>

<script>
    $(document).on('click', '#formApproveDispensasi [name="approve"]', function() {
        $('#formApproveDispensasi').append('<input type="hidden" name="status" value="APPROVED">');
        $('#formApproveDispensasi').submit();
        $(this).prop('readonly', true);
        $('#formApproveDispensasi button[name="tolak"]').prop('disabled', true);
        $(this).html("<i class='fa fa-spin fa-spinner me-1'></i> Processing...");
    });

    $(document).on('click', '#formApproveDispensasi [name="tolak"]', function() {
        $('#formApproveDispensasi').append('<input type="hidden" name="status" value="REJECTED">');
        $('#formApproveDispensasi').submit();
        $(this).prop('readonly', true);
        $('#formApproveDispensasi button[name="approve"]').prop('disabled', true);
        $(this).html("<i class='fa fa-spin fa-spinner me-1'></i> Processing...");
    });
</script>
