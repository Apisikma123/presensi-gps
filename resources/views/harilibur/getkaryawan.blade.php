@forelse ($karyawan as $d)
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
                {{ $d->nama_dept }}
            </span>
        </td>
        <td class="text-end">
            @if (empty($d->ceklibur))
                <button type="button" nik="{{ $d->nik }}" class="updateLibur btn-action-tbl btn-action-approve" title="Tambahkan ke Daftar Libur">
                    <i class="ti ti-plus"></i>
                </button>
            @else
                <button type="button" nik="{{ $d->nik }}" class="updateLibur btn-action-tbl btn-action-delete" title="Batalkan Libur (Wajib Masuk)">
                    <i class="ti ti-circle-minus"></i>
                </button>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-4 text-muted">
            Tidak ada data karyawan yang cocok dengan filter.
        </td>
    </tr>
@endforelse
