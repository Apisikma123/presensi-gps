@forelse ($karyawan as $d)
    <tr>
        <td class="text-center font-mono text-muted" style="font-size: 12px;">{{ $loop->iteration }}</td>
        <td>
            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                {{ $d->nik_show ?? $d->nik }}
            </span>
        </td>
        @php
            $empName = formatName2($d->nama_karyawan);
            $words = explode(' ', trim($empName));
            $initials = '';
            foreach ($words as $wrd) {
                if (isset($wrd[0])) $initials .= $wrd[0];
            }
            $initials = strtoupper(substr($initials, 0, 2)) ?: 'KR';
        @endphp
        <td>
            <div class="d-flex align-items-center gap-2.5">
                @if (!empty($d->foto))
                    <img src="{{ getfotoKaryawan($d->foto) }}" alt="Avatar" class="rounded-circle flex-shrink-0"
                        style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #E2E8F0;"
                        onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                    <div class="rounded-circle flex-shrink-0 align-items-center justify-content-center fw-bold"
                        style="display: none; width: 32px; height: 32px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08)); color: var(--color-primary, #3C2A21); font-size: 11px; border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb, 60, 42, 33), 0.15));">
                        {{ $initials }}
                    </div>
                @else
                    <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center fw-bold"
                        style="width: 32px; height: 32px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08)); color: var(--color-primary, #3C2A21); font-size: 11px; border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb, 60, 42, 33), 0.15));">
                        {{ $initials }}
                    </div>
                @endif
                <span class="fw-bold text-dark text-truncate" style="font-size: 13px; max-width: 200px;">{{ $empName }}</span>
            </div>
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
