@forelse ($detailharilibur as $d)
    <tr>
        <td class="text-center font-mono text-muted" style="font-size: 12px;">{{ $loop->iteration }}</td>
        <td>
            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                {{ $d->nik_show ?? $d->nik }}
            </span>
        </td>
        <td>
            <span class="fw-bold text-dark d-block" style="font-size: 13px;">{{ formatName2($d->nama_karyawan) }}</span>
        </td>
        <td>
            <span class="badge bg-label-info text-uppercase font-mono" style="font-size: 10.5px;">
                {{ $d->nama_dept ?? $d->kode_dept }}
            </span>
        </td>
        <td class="text-end">
            <button type="button" class="delete btn-action-tbl btn-action-delete" nik="{{ $d->nik }}" title="Batalkan Libur (Wajib Masuk)">
                <i class="ti ti-circle-minus"></i>
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-5 text-muted">
            <i class="ti ti-users-minus fs-1 d-block mb-2 text-muted" style="opacity: 0.4;"></i>
            <div class="fw-semibold text-dark">Belum ada karyawan yang didaftarkan libur</div>
            <small>Klik tombol "Kelola / Tambah Karyawan" di atas untuk menambahkan karyawan.</small>
        </td>
    </tr>
@endforelse
