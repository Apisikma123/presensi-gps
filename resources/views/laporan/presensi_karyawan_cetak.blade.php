<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi Karyawan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
        }
        .header h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 12px;
            color: #64748b;
        }
        .info-karyawan {
            margin-bottom: 20px;
            width: 100%;
        }
        .info-karyawan td {
            padding: 3px 6px;
            font-size: 12px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data th, table.data td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 11px;
        }
        table.data th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .bg-success { background-color: #dcfce7; color: #15803d; }
        .bg-warning { background-color: #fef9c3; color: #a16207; }
        .bg-danger { background-color: #fee2e2; color: #b91c1c; }
        .bg-info { background-color: #e0f2fe; color: #0369a1; }
        .bg-secondary { background-color: #f1f5f9; color: #475569; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 6px 12px; background: #0f172a; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            Cetak / Print PDF
        </button>
    </div>

    <div class="header">
        <h3>{{ $generalsetting->nama_perusahaan ?? 'PRESENSI KARYAWAN' }}</h3>
        <p>LAPORAN PRESENSI INDIVIDUAL</p>
        <p>Periode: {{ date('d/m/Y', strtotime($periode_dari)) }} s.d. {{ date('d/m/Y', strtotime($periode_sampai)) }}</p>
    </div>

    <table class="info-karyawan">
        <tr>
            <td style="width: 120px; font-weight: bold;">NIK</td>
            <td style="width: 10px;">:</td>
            <td>{{ $karyawan->nik }}</td>
            <td style="width: 120px; font-weight: bold;">Outlet / Cabang</td>
            <td style="width: 10px;">:</td>
            <td>{{ $karyawan->cabang->nama_cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nama Karyawan</td>
            <td>:</td>
            <td>{{ $karyawan->nama_karyawan }}</td>
            <td style="font-weight: bold;">Shift Utama</td>
            <td>:</td>
            <td>{{ $karyawan->jamkerja->nama_jam_kerja ?? 'Shift Pagi (JK01)' }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 65px;">Hari</th>
                <th>Shift</th>
                <th style="width: 70px;">Jam Masuk</th>
                <th style="width: 70px;">Jam Pulang</th>
                <th style="width: 90px;">Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $curr = $periode_dari;
                $no = 1;
                $totHadir = 0; $totTelat = 0; $totDispensasi = 0;
                $totIzin = 0; $totSakit = 0; $totCuti = 0; $totAlfa = 0;
                $defaultJk = $jamkerja_map[$karyawan->kode_jam_kerja ?? 'JK01'] ?? $jamkerja_map->first();
            @endphp
            @while (strtotime($curr) <= strtotime($periode_sampai))
                @php
                    $key = $karyawan->nik . '|' . $curr;
                    $pres = $presensiMap[$key] ?? null;
                    $disp = $dispensasiMap[$key] ?? null;
                    $isLibur = isset($hariLibur[$curr]);

                    $status = 'TIDAK HADIR';
                    $badge = 'bg-danger';
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
                                $badge = 'bg-info';
                                $keterangan = 'Dispensasi s/d ' . $disp->batas_dispensasi . ' (' . $disp->alasan . ')';
                                $totDispensasi++;
                                $totHadir++;
                            } elseif ($actualIn <= $batas) {
                                $status = 'HADIR';
                                $badge = 'bg-success';
                                $totHadir++;
                            } else {
                                $status = 'TELAT';
                                $badge = 'bg-warning';
                                $keterangan = 'Masuk lewat dari toleransi ' . $batas;
                                $totTelat++;
                            }
                        } elseif ($pres->status === 'i') {
                            $status = 'IZIN';
                            $badge = 'bg-secondary';
                            $keterangan = $pres->keterangan_izin ?? 'Izin Absen';
                            $totIzin++;
                        } elseif ($pres->status === 's') {
                            $status = 'SAKIT';
                            $badge = 'bg-secondary';
                            $keterangan = $pres->keterangan_sakit ?? 'Izin Sakit';
                            $totSakit++;
                        } elseif ($pres->status === 'c') {
                            $status = 'CUTI';
                            $badge = 'bg-secondary';
                            $keterangan = $pres->keterangan_cuti ?? 'Izin Cuti';
                            $totCuti++;
                        }
                    } else {
                        if ($isLibur) {
                            $status = 'LIBUR';
                            $badge = 'bg-secondary';
                            $keterangan = $hariLibur[$curr]->keterangan ?? 'Libur Nasional';
                        } else {
                            $totAlfa++;
                        }
                    }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td style="text-align: center;">{{ date('d/m/Y', strtotime($curr)) }}</td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($curr)->translatedFormat('l') }}</td>
                    <td>{{ $namaShift }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $jamIn }}</td>
                    <td style="text-align: center;">{{ $jamOut }}</td>
                    <td style="text-align: center;"><span class="badge {{ $badge }}">{{ $status }}</span></td>
                    <td>{{ $keterangan }}</td>
                </tr>
                @php $curr = date('Y-m-d', strtotime('+1 day', strtotime($curr))); @endphp
            @endwhile
        </tbody>
    </table>

    <div style="margin-top: 20px; display: flex; justify-content: space-between;">
        <table style="border: 1px solid #cbd5e1; border-collapse: collapse; font-size: 11px;">
            <tr>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; font-weight: bold;">Hadir Tepat Waktu</td>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; text-align: right;">{{ $totHadir - $totDispensasi }}</td>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; font-weight: bold;">Dispensasi</td>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; text-align: right;">{{ $totDispensasi }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; font-weight: bold;">Terlambat</td>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; text-align: right;">{{ $totTelat }}</td>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; font-weight: bold;">Izin / Sakit / Cuti</td>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; text-align: right;">{{ $totIzin + $totSakit + $totCuti }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; font-weight: bold;">Tidak Hadir (Alfa)</td>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; text-align: right;">{{ $totAlfa }}</td>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; font-weight: bold;">Total Kehadiran</td>
                <td style="padding: 4px 8px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold;">{{ $totHadir }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
