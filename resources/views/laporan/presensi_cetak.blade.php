<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi Karyawan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
        }
        .header h3 {
            margin: 0 0 5px 0;
            font-size: 15px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            color: #64748b;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data th, table.data td {
            border: 1px solid #cbd5e1;
            padding: 6px 6px;
            font-size: 11px;
        }
        table.data th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
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
        <p>REKAPITULASI PRESENSI KARYAWAN</p>
        <p>Periode: {{ date('d/m/Y', strtotime($periode_dari)) }} s.d. {{ date('d/m/Y', strtotime($periode_sampai)) }}</p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 80px;">NIK</th>
                <th>Nama Karyawan</th>
                <th>Outlet</th>
                <th>Shift</th>
                <th style="width: 50px;">Hadir Normal</th>
                <th style="width: 50px;">Dispensasi</th>
                <th style="width: 50px;">Telat</th>
                <th style="width: 45px;">Izin</th>
                <th style="width: 45px;">Sakit</th>
                <th style="width: 45px;">Cuti</th>
                <th style="width: 50px;">Alfa</th>
                <th style="width: 55px; background: #e2e8f0;">Total Hadir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($karyawanList as $k)
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
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td style="text-align: center; font-family: monospace;">{{ $k->nik }}</td>
                    <td><b>{{ $k->nama_karyawan }}</b></td>
                    <td>{{ $k->cabang->nama_cabang ?? '-' }}</td>
                    <td>{{ $k->jamkerja->nama_jam_kerja ?? 'Shift Pagi' }}</td>
                    <td style="text-align: center; color: #166534;">{{ $totHadirNormal }}</td>
                    <td style="text-align: center; color: #0369a1;">{{ $totDispensasi }}</td>
                    <td style="text-align: center; color: #b45309; font-weight: bold;">{{ $totTelat }}</td>
                    <td style="text-align: center;">{{ $totIzin }}</td>
                    <td style="text-align: center;">{{ $totSakit }}</td>
                    <td style="text-align: center;">{{ $totCuti }}</td>
                    <td style="text-align: center; color: #b91c1c; font-weight: bold;">{{ $totAlfa }}</td>
                    <td style="text-align: center; font-weight: bold; background: #f8fafc;">{{ $totHadirNormal + $totDispensasi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" style="text-align: center; padding: 20px;">Tidak ada data karyawan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
