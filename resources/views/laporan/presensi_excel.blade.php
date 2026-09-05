<table>
    <thead>
        <tr>
            <th colspan="13" style="font-weight: bold; font-size: 14px; text-align: center;">
                {{ $generalsetting->nama_perusahaan ?? 'PRESENSI KARYAWAN' }}
            </th>
        </tr>
        <tr>
            <th colspan="13" style="font-weight: bold; font-size: 12px; text-align: center;">
                REKAPITULASI PRESENSI KARYAWAN
            </th>
        </tr>
        <tr>
            <th colspan="13" style="text-align: center;">
                Periode: {{ date('d/m/Y', strtotime($periode_dari)) }} s.d. {{ date('d/m/Y', strtotime($periode_sampai)) }}
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">No</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">NIK</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000;">Nama Karyawan</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000;">Outlet</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000;">Shift</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Hadir Normal</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Dispensasi</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Telat</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Izin</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Sakit</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Cuti</th>
            <th style="font-weight: bold; background-color: #dbeafe; border: 1px solid #000000; text-align: center;">Alfa</th>
            <th style="font-weight: bold; background-color: #bfdbfe; border: 1px solid #000000; text-align: center;">Total Hadir</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($karyawanList as $k)
            @php
                $totHadirNormal = 0; $totDispensasi = 0; $totTelat = 0;
                $totIzin = 0; $totSakit = 0; $totCuti = 0; $totAlfa = 0;
                $defaultJk = $jamkerja_map[$k->kode_jam_kerja ?? 'JK01'] ?? $jamkerja_map->first();

                $curr = $periode_dari;
                while (strtotime($curr) <= strtotime($periode_sampai)) {
                    $key = $k->nik . '|' . $curr;
                    $pres = $presensiMap[$key] ?? null;
                    $disp = $dispensasiMap[$key] ?? null;
                    $isLibur = isset($hariLibur[$curr]);

                    if ($pres) {
                        if ($pres->status === 'h') {
                            $jk = $jamkerja_map[$pres->kode_jam_kerja] ?? $defaultJk;
                            $batas = $jk->batas_toleransi ? date('H:i:s', strtotime($jk->batas_toleransi)) : '07:05:00';
                            $actualIn = date('H:i:s', strtotime($pres->jam_in));

                            if ($disp && $actualIn <= $disp->batas_dispensasi) {
                                $totDispensasi++;
                            } elseif ($actualIn <= $batas) {
                                $totHadirNormal++;
                            } else {
                                $totTelat++;
                            }
                        } elseif ($pres->status === 'i') {
                            $totIzin++;
                        } elseif ($pres->status === 's') {
                            $totSakit++;
                        } elseif ($pres->status === 'c') {
                            $totCuti++;
                        }
                    } else {
                        if (!$isLibur) {
                            $totAlfa++;
                        }
                    }
                    $curr = date('Y-m-d', strtotime('+1 day', strtotime($curr)));
                }
            @endphp
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid #000000; text-align: center;">'{{ $k->nik }}</td>
                <td style="border: 1px solid #000000;">{{ $k->nama_karyawan }}</td>
                <td style="border: 1px solid #000000;">{{ $k->cabang->nama_cabang ?? '-' }}</td>
                <td style="border: 1px solid #000000;">{{ $k->jamkerja->nama_jam_kerja ?? 'Shift Pagi' }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $totHadirNormal }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $totDispensasi }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $totTelat }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $totIzin }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $totSakit }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $totCuti }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $totAlfa }}</td>
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $totHadirNormal + $totDispensasi }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
