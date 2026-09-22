<table>
    <thead>
        <tr>
            <th colspan="14" style="font-family: Arial, sans-serif; font-weight: bold; font-size: 15px; color: #1E4D3E; text-align: center;">
                {{ strtoupper($generalsetting->nama_perusahaan ?? 'PERUSAHAAN') }}
            </th>
        </tr>
        <tr>
            <th colspan="14" style="font-family: Arial, sans-serif; font-weight: bold; font-size: 12px; color: #0F172A; text-align: center;">
                REKAPITULASI PRESENSI DAN DISIPLIN KERJA KARYAWAN
            </th>
        </tr>
        <tr>
            <th colspan="14" style="font-family: Arial, sans-serif; font-size: 10px; color: #64748B; text-align: center;">
                Periode: {{ date('d/m/Y', strtotime($periode_dari)) }} s.d. {{ date('d/m/Y', strtotime($periode_sampai)) }} | Dicetak: {{ date('d/m/Y H:i') }} WIB oleh {{ auth()->user()->name ?? 'Administrator' }}
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">No</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">NIK</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: left; vertical-align: middle;">Nama Karyawan</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: left; vertical-align: middle;">Outlet / Cabang</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: left; vertical-align: middle;">Shift Penugasan</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Hadir Tepat</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Dispensasi</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Terlambat</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Izin</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Sakit</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Cuti</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Alfa</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #163B2F; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Total Hadir</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #163B2F; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">% Hadir</th>
        </tr>
    </thead>
    <tbody>
        @php
            $today = date('Y-m-d');
            $effectiveEnd = ($periode_sampai > $today) ? $today : $periode_sampai;

            $totalWorkDays = 0;
            $curD = $periode_dari;
            $sistemHariKerja = (string)($generalsetting->sistem_hari_kerja ?? '6');
            while (strtotime($curD) <= strtotime($effectiveEnd)) {
                $w = (int)date('w', strtotime($curD));
                $isWeekend = ($w === 0) || ($sistemHariKerja === '5' && $w === 6);
                if (!isset($hariLibur[$curD]) && !$isWeekend) $totalWorkDays++;
                $curD = date('Y-m-d', strtotime('+1 day', strtotime($curD)));
            }
            if ($totalWorkDays == 0) $totalWorkDays = 1;

            $sumHadir = 0; $sumDispen = 0; $sumTelat = 0;
            $sumIzin = 0; $sumSakit = 0; $sumCuti = 0; $sumAlfa = 0; $sumTotalHadir = 0;
            $grandExpected = 0;
        @endphp

        @foreach ($karyawanList as $k)
            @php
                $totHadirNormal = 0; $totDispensasi = 0; $totTelat = 0;
                $totIzin = 0; $totSakit = 0; $totCuti = 0; $totAlfa = 0;
                $empWorkDays = 0;
                $defaultJk = $jamkerja_map[$k->kode_jam_kerja ?? 'JK01'] ?? $jamkerja_map->first();

                $curr = $periode_dari;
                while (strtotime($curr) <= strtotime($periode_sampai)) {
                    $key = $k->nik . '|' . $curr;
                    $pres = $presensiMap[$key] ?? null;
                    $disp = $dispensasiMap[$key] ?? null;

                    $eff = $effectiveSchedules[$k->nik][$curr] ?? null;
                    $isOff = $eff ? $eff['is_off'] : false;
                    $effCabang = $eff['kode_cabang'] ?? $k->kode_cabang;
                    $isPastOrToday = ($curr <= $today);

                    if (!empty($kode_cabang) && $effCabang !== $kode_cabang && !$pres) {
                        $curr = date('Y-m-d', strtotime('+1 day', strtotime($curr)));
                        continue;
                    }

                    if (!$isOff && $curr <= $effectiveEnd) {
                        $empWorkDays++;
                    }

                    if ($pres) {
                        if ($pres->status === 'h') {
                            $jk = $jamkerja_map[$pres->kode_jam_kerja] ?? ($eff['jam_kerja'] ?? $defaultJk);
                            $batas = $jk && $jk->batas_toleransi ? date('H:i:s', strtotime($jk->batas_toleransi)) : '07:05:00';
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
                        } elseif ($pres->status === 'a') {
                            $totAlfa++;
                        }
                    } else {
                        if (!$isOff && $isPastOrToday) {
                            $totAlfa++;
                        }
                    }
                    $curr = date('Y-m-d', strtotime('+1 day', strtotime($curr)));
                }

                $effectiveWorkDays = $empWorkDays > 0 ? $empWorkDays : 1;
                $rowTotalHadir = $totHadirNormal + $totDispensasi;
                $empRate = min(100, round(($rowTotalHadir / $effectiveWorkDays) * 100));

                $sumHadir += $totHadirNormal;
                $sumDispen += $totDispensasi;
                $sumTelat += $totTelat;
                $sumIzin += $totIzin;
                $sumSakit += $totSakit;
                $sumCuti += $totCuti;
                $sumAlfa += $totAlfa;
                $sumTotalHadir += $rowTotalHadir;
                $grandExpected += $effectiveWorkDays;

                $bg = ($loop->iteration % 2 == 0) ? '#F8FAF8' : '#FFFFFF';
            @endphp
            <tr style="background-color: {{ $bg }};">
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">'{{ $k->nik }}</td>
                <td style="border: 1px solid #CBD5E1; vertical-align: middle; font-weight: bold;">{{ $k->nama_karyawan }}</td>
                <td style="border: 1px solid #CBD5E1; vertical-align: middle;">{{ $k->cabang->nama_cabang ?? '-' }}</td>
                <td style="border: 1px solid #CBD5E1; vertical-align: middle;">{{ $k->jamkerja->nama_jam_kerja ?? 'Shift Pagi' }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle; color: #059669; font-weight: bold;">{{ $totHadirNormal }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle; color: #0284C7; font-weight: bold;">{{ $totDispensasi }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle; @if($totTelat > 0) color: #D97706; font-weight: bold; @endif">{{ $totTelat }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">{{ $totIzin }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">{{ $totSakit }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">{{ $totCuti }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle; @if($totAlfa > 0) color: #DC2626; font-weight: bold; @endif">{{ $totAlfa }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle; font-weight: bold; background-color: #F1F5F9;">{{ $rowTotalHadir }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">{{ $empRate }}%</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        @php
            $grandRate = $grandExpected > 0 ? round(($sumTotalHadir / $grandExpected) * 100, 1) : 0;
        @endphp
        <tr>
            <td colspan="5" style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9; text-align: right; vertical-align: middle;">TOTAL AKUMULASI:</td>
            <td style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9; text-align: center; vertical-align: middle; color: #059669;">{{ $sumHadir }}</td>
            <td style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9; text-align: center; vertical-align: middle; color: #0284C7;">{{ $sumDispen }}</td>
            <td style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9; text-align: center; vertical-align: middle; color: #D97706;">{{ $sumTelat }}</td>
            <td style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9; text-align: center; vertical-align: middle;">{{ $sumIzin }}</td>
            <td style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9; text-align: center; vertical-align: middle;">{{ $sumSakit }}</td>
            <td style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9; text-align: center; vertical-align: middle;">{{ $sumCuti }}</td>
            <td style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9; text-align: center; vertical-align: middle; color: #DC2626;">{{ $sumAlfa }}</td>
            <td style="border: 1px solid #94A3B8; font-weight: bold; background-color: #E2E8F0; text-align: center; vertical-align: middle; color: #1E4D3E;">{{ $sumTotalHadir }}</td>
            <td style="border: 1px solid #94A3B8; font-weight: bold; background-color: #E2E8F0; text-align: center; vertical-align: middle;">{{ $grandRate }}%</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="4" style="text-align: center; font-weight: bold;">Dibuat Oleh:</td>
            <td colspan="5" style="text-align: center; font-weight: bold;">Diperiksa Oleh:</td>
            <td colspan="5" style="text-align: center; font-weight: bold;">Disetujui Oleh:</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center; height: 50px;"></td>
            <td colspan="5" style="text-align: center; height: 50px;"></td>
            <td colspan="5" style="text-align: center; height: 50px;"></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ auth()->user()->name ?? 'Staff Operasional' }}</td>
            <td colspan="5" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $generalsetting->nama_hrd ?? 'HRD Manager' }}</td>
            <td colspan="5" style="text-align: center; font-weight: bold; text-decoration: underline;">Direktur Operasional</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: center; font-size: 9px; color: #64748B;">Staff HR &amp; Payroll</td>
            <td colspan="5" style="text-align: center; font-size: 9px; color: #64748B;">People &amp; Culture Head</td>
            <td colspan="5" style="text-align: center; font-size: 9px; color: #64748B;">Executive Director</td>
        </tr>
    </tfoot>
</table>
