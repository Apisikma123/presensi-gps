<table>
    <thead>
        <tr>
            <th colspan="8" style="font-family: Arial, sans-serif; font-weight: bold; font-size: 15px; color: #1E4D3E; text-align: center;">
                {{ strtoupper($generalsetting->nama_perusahaan ?? 'PERUSAHAAN') }}
            </th>
        </tr>
        <tr>
            <th colspan="8" style="font-family: Arial, sans-serif; font-weight: bold; font-size: 12px; color: #0F172A; text-align: center;">
                LAPORAN PRESENSI INDIVIDUAL KARYAWAN
            </th>
        </tr>
        <tr>
            <th colspan="8" style="font-family: Arial, sans-serif; font-size: 10px; color: #64748B; text-align: center;">
                Periode: {{ date('d/m/Y', strtotime($periode_dari)) }} s.d. {{ date('d/m/Y', strtotime($periode_sampai)) }} | Dicetak: {{ date('d/m/Y H:i') }} WIB
            </th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; background-color: #F1F5F9; border: 1px solid #CBD5E1;">Nomor Induk (NIK)</th>
            <th style="border: 1px solid #CBD5E1; text-align: left;">'{{ $karyawan->nik }}</th>
            <th style="font-weight: bold; background-color: #F1F5F9; border: 1px solid #CBD5E1;">Outlet / Cabang</th>
            <th colspan="2" style="border: 1px solid #CBD5E1; text-align: left;">{{ $karyawan->cabang->nama_cabang ?? '-' }}</th>
            <th style="font-weight: bold; background-color: #F1F5F9; border: 1px solid #CBD5E1;">Departemen</th>
            <th colspan="2" style="border: 1px solid #CBD5E1; text-align: left;">{{ $karyawan->departemen->nama_dept ?? '-' }}</th>
        </tr>
        <tr>
            <th style="font-weight: bold; background-color: #F1F5F9; border: 1px solid #CBD5E1;">Nama Lengkap</th>
            <th style="border: 1px solid #CBD5E1; font-weight: bold; text-align: left;">{{ $karyawan->nama_karyawan }}</th>
            <th style="font-weight: bold; background-color: #F1F5F9; border: 1px solid #CBD5E1;">Shift Penugasan</th>
            <th colspan="2" style="border: 1px solid #CBD5E1; text-align: left;">{{ $karyawan->jamkerja->nama_jam_kerja ?? 'Shift Pagi' }}</th>
            <th style="font-weight: bold; background-color: #F1F5F9; border: 1px solid #CBD5E1;">Jabatan</th>
            <th colspan="2" style="border: 1px solid #CBD5E1; text-align: left;">{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">No</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Tanggal</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Hari</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: left; vertical-align: middle;">Shift</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Jam Masuk</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Jam Pulang</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: center; vertical-align: middle;">Status Kehadiran</th>
            <th style="font-family: Arial, sans-serif; font-weight: bold; background-color: #1E4D3E; color: #FFFFFF; border: 1px solid #94A3B8; text-align: left; vertical-align: middle;">Keterangan / Audit Presensi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $today = date('Y-m-d');
            $curr = $periode_dari;
            $no = 1;
            $totHadir = 0; $totTelat = 0; $totDispensasi = 0;
            $totIzin = 0; $totSakit = 0; $totCuti = 0; $totAlfa = 0; $totLibur = 0;
            $defaultJk = $jamkerja_map[$karyawan->kode_jam_kerja ?? 'JK01'] ?? $jamkerja_map->first();
            $sistemHariKerja = (string)($generalsetting->sistem_hari_kerja ?? '6');
        @endphp
        @while (strtotime($curr) <= strtotime($periode_sampai))
            @php
                $key = $karyawan->nik . '|' . $curr;
                $pres = $presensiMap[$key] ?? null;
                $disp = $dispensasiMap[$key] ?? null;
                $eff = $effectiveSchedules[$karyawan->nik][$curr] ?? null;
                $isOff = $eff ? $eff['is_off'] : false;
                $isPastOrToday = ($curr <= $today);

                $status = 'ALFA';
                $statusColor = '#DC2626';
                $keterangan = 'Tidak hadir tanpa keterangan';
                $jamIn = '-';
                $jamOut = '-';
                $namaShift = $eff && $eff['jam_kerja'] ? $eff['jam_kerja']->nama_jam_kerja : ($isOff ? 'Libur (OFF)' : ($defaultJk->nama_jam_kerja ?? 'Shift Pagi'));

                if ($pres) {
                    $jamIn = $pres->jam_in ? date('H:i:s', strtotime($pres->jam_in)) : '-';
                    $jamOut = $pres->jam_out ? date('H:i:s', strtotime($pres->jam_out)) : '-';

                    if ($pres->status === 'h') {
                        $jk = $jamkerja_map[$pres->kode_jam_kerja] ?? ($eff['jam_kerja'] ?? $defaultJk);
                        $namaShift = $jk->nama_jam_kerja ?? $namaShift;
                        $batas = $jk && $jk->batas_toleransi ? date('H:i:s', strtotime($jk->batas_toleransi)) : '07:05:00';
                        $actualIn = date('H:i:s', strtotime($pres->jam_in));

                        if ($isOff) {
                            $status = 'HADIR';
                            $statusColor = '#059669';
                            $keterangan = 'Hadir di Luar Jadwal (Off Day)';
                            $totHadir++;
                        } elseif ($disp && $actualIn <= $disp->batas_dispensasi) {
                            $status = 'DISPENSASI';
                            $statusColor = '#0284C7';
                            $keterangan = 'Dispensasi s/d ' . date('H:i', strtotime($disp->batas_dispensasi)) . ' (' . ($disp->alasan ?? 'Disetujui') . ')';
                            $totDispensasi++;
                            $totHadir++;
                        } elseif ($actualIn <= $batas) {
                            $status = 'HADIR';
                            $statusColor = '#059669';
                            $keterangan = 'Tepat waktu';
                            $totHadir++;
                        } else {
                            $status = 'TELAT';
                            $statusColor = '#D97706';
                            $keterangan = 'Masuk lewat toleransi ' . date('H:i', strtotime($batas));
                            $totTelat++;
                        }
                    } elseif ($pres->status === 'i') {
                        $status = 'IZIN';
                        $statusColor = '#475569';
                        $keterangan = $pres->keterangan_izin ?? ($pres->keterangan ?? 'Izin resmi');
                        $totIzin++;
                    } elseif ($pres->status === 's') {
                        $status = 'SAKIT';
                        $statusColor = '#475569';
                        $keterangan = $pres->keterangan_sakit ?? ($pres->keterangan ?? 'Surat dokter / sakit');
                        $totSakit++;
                    } elseif ($pres->status === 'c') {
                        $status = 'CUTI';
                        $statusColor = '#475569';
                        $keterangan = $pres->keterangan_cuti ?? ($pres->keterangan ?? 'Hak cuti terpakai');
                        $totCuti++;
                    } elseif ($pres->status === 'a') {
                        $status = 'ALFA';
                        $statusColor = '#DC2626';
                        $keterangan = $pres->keterangan ?? 'Tanpa keterangan (Alpha)';
                        $totAlfa++;
                    }
                } else {
                    if ($isOff) {
                        $status = 'LIBUR';
                        $statusColor = '#64748B';
                        $keterangan = $eff['keterangan'] ?? 'Libur Rutin (Off Day)';
                        $totLibur++;
                    } elseif (!$isPastOrToday) {
                        $status = '-';
                        $statusColor = '#94A3B8';
                        $keterangan = 'Jadwal mendatang';
                    } else {
                        $status = 'ALFA';
                        $statusColor = '#DC2626';
                        $keterangan = 'Tidak hadir tanpa keterangan';
                        $totAlfa++;
                    }
                }

                $bg = ($no % 2 == 0) ? '#F8FAF8' : '#FFFFFF';
            @endphp
            <tr style="background-color: {{ $bg }};">
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">{{ $no++ }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">{{ date('d/m/Y', strtotime($curr)) }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">{{ \Carbon\Carbon::parse($curr)->translatedFormat('l') }}</td>
                <td style="border: 1px solid #CBD5E1; vertical-align: middle;">{{ $namaShift }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle; font-weight: bold;">{{ $jamIn }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle;">{{ $jamOut }}</td>
                <td style="border: 1px solid #CBD5E1; text-align: center; vertical-align: middle; font-weight: bold; color: {{ $statusColor }};">{{ $status }}</td>
                <td style="border: 1px solid #CBD5E1; vertical-align: middle; font-size: 10px;">{{ $keterangan }}</td>
            </tr>
            @php $curr = date('Y-m-d', strtotime('+1 day', strtotime($curr))); @endphp
        @endwhile
    </tbody>
    <tfoot>
        <tr></tr>
        <tr>
            <td colspan="4" style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9;">RINGKASAN KEHADIRAN:</td>
            <td colspan="4" style="border: 1px solid #94A3B8; font-weight: bold; background-color: #F1F5F9;">STATISTIK DISIPLIN:</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #CBD5E1;">Hadir Tepat Waktu:</td>
            <td colspan="2" style="border: 1px solid #CBD5E1; font-weight: bold; color: #059669; text-align: center;">{{ $totHadir }} Hari</td>
            <td colspan="2" style="border: 1px solid #CBD5E1;">Terlambat Masuk:</td>
            <td colspan="2" style="border: 1px solid #CBD5E1; font-weight: bold; color: #D97706; text-align: center;">{{ $totTelat }} Kejadian</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #CBD5E1;">Hadir Dispensasi:</td>
            <td colspan="2" style="border: 1px solid #CBD5E1; font-weight: bold; color: #0284C7; text-align: center;">{{ $totDispensasi }} Hari</td>
            <td colspan="2" style="border: 1px solid #CBD5E1;">Izin, Sakit dan Cuti:</td>
            <td colspan="2" style="border: 1px solid #CBD5E1; font-weight: bold; text-align: center;">{{ $totIzin + $totSakit + $totCuti }} Hari</td>
        </tr>
        <tr>
            <td colspan="2" style="border: 1px solid #CBD5E1; font-weight: bold;">Total Hadir Efektif:</td>
            <td colspan="2" style="border: 1px solid #CBD5E1; font-weight: bold; background-color: #E2E8F0; text-align: center;">{{ $totHadir + $totDispensasi }} Hari</td>
            <td colspan="2" style="border: 1px solid #CBD5E1; font-weight: bold;">Tanpa Keterangan (Alfa):</td>
            <td colspan="2" style="border: 1px solid #CBD5E1; font-weight: bold; color: #DC2626; text-align: center;">{{ $totAlfa }} Hari</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold;">Pegawai:</td>
            <td colspan="3" style="text-align: center; font-weight: bold;">Atasan Langsung:</td>
            <td colspan="3" style="text-align: center; font-weight: bold;">Manager HRD:</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; height: 50px;"></td>
            <td colspan="3" style="text-align: center; height: 50px;"></td>
            <td colspan="3" style="text-align: center; height: 50px;"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $karyawan->nama_karyawan }}</td>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">Supervisor / SPV</td>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $generalsetting->nama_hrd ?? 'HRD Manager' }}</td>
        </tr>
    </tfoot>
</table>
