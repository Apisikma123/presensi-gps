<table>
    <thead>
        <tr>
            <th colspan="8" style="font-weight: bold; font-size: 14px; text-align: center;">
                {{ $generalsetting->nama_perusahaan ?? 'PRESENSI KARYAWAN' }}
            </th>
        </tr>
        <tr>
            <th colspan="8" style="font-weight: bold; font-size: 12px; text-align: center;">
                LAPORAN PRESENSI INDIVIDUAL
            </th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">
                Periode: {{ date('d/m/Y', strtotime($periode_dari)) }} s.d. {{ date('d/m/Y', strtotime($periode_sampai)) }}
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">NIK:</th>
            <th colspan="2">'{{ $karyawan->nik }}</th>
            <th colspan="2" style="font-weight: bold;">Outlet:</th>
            <th colspan="2">{{ $karyawan->cabang->nama_cabang ?? '-' }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Nama:</th>
            <th colspan="2">{{ $karyawan->nama_karyawan }}</th>
            <th colspan="2" style="font-weight: bold;">Shift Utama:</th>
            <th colspan="2">{{ $karyawan->jamkerja->nama_jam_kerja ?? 'Shift Pagi' }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">No</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Tanggal</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Hari</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000;">Shift</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Jam Masuk</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Jam Pulang</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Status</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $curr = $periode_dari;
            $no = 1;
            $defaultJk = $jamkerja_map[$karyawan->kode_jam_kerja ?? 'JK01'] ?? $jamkerja_map->first();
        @endphp
        @while (strtotime($curr) <= strtotime($periode_sampai))
            @php
                $key = $karyawan->nik . '|' . $curr;
                $pres = $presensiMap[$key] ?? null;
                $disp = $dispensasiMap[$key] ?? null;
                $isLibur = isset($hariLibur[$curr]);

                $status = 'TIDAK HADIR';
                $keterangan = '-';
                $jamIn = '-';
                $jamOut = '-';
                $namaShift = $defaultJk->nama_jam_kerja ?? 'Shift Pagi';

                if ($pres) {
                    $jamIn = $pres->jam_in ? date('H:i', strtotime($pres->jam_in)) : '-';
                    $jamOut = $pres->jam_out ? date('H:i', strtotime($pres->jam_out)) : '-';
                    
                    if ($pres->status === 'h') {
                        $jk = $jamkerja_map[$pres->kode_jam_kerja] ?? $defaultJk;
                        $namaShift = $jk->nama_jam_kerja ?? $namaShift;
                        $batas = $jk->batas_toleransi ? date('H:i:s', strtotime($jk->batas_toleransi)) : '07:05:00';
                        $actualIn = date('H:i:s', strtotime($pres->jam_in));

                        if ($disp && $actualIn <= $disp->batas_dispensasi) {
                            $status = 'HADIR (DISPENSASI)';
                            $keterangan = 'Dispensasi s/d ' . $disp->batas_dispensasi . ' (' . $disp->alasan . ')';
                        } elseif ($actualIn <= $batas) {
                            $status = 'HADIR';
                        } else {
                            $status = 'TELAT';
                            $keterangan = 'Masuk lewat toleransi ' . $batas;
                        }
                    } elseif ($pres->status === 'i') {
                        $status = 'IZIN';
                        $keterangan = $pres->keterangan_izin ?? 'Izin Absen';
                    } elseif ($pres->status === 's') {
                        $status = 'SAKIT';
                        $keterangan = $pres->keterangan_sakit ?? 'Izin Sakit';
                    } elseif ($pres->status === 'c') {
                        $status = 'CUTI';
                        $keterangan = $pres->keterangan_cuti ?? 'Izin Cuti';
                    }
                } else {
                    if ($isLibur) {
                        $status = 'LIBUR';
                        $keterangan = $hariLibur[$curr]->keterangan ?? 'Libur Nasional';
                    }
                }
            @endphp
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $no++ }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ date('d/m/Y', strtotime($curr)) }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ \Carbon\Carbon::parse($curr)->translatedFormat('l') }}</td>
                <td style="border: 1px solid #000000;">{{ $namaShift }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $jamIn }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $jamOut }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $status }}</td>
                <td style="border: 1px solid #000000;">{{ $keterangan }}</td>
            </tr>
            @php $curr = date('Y-m-d', strtotime('+1 day', strtotime($curr))); @endphp
        @endwhile
    </tbody>
</table>
