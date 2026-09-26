@php
    $theme = \App\Services\ThemeResolver::resolve();
    $primaryColor = $theme['primary'] ?? '#3C2A21';
    $secondaryColor = $theme['secondary'] ?? '#634832';
    $primaryHover = $theme['primary_hover'] ?? '#2A1D17';
    $primaryContrast = $theme['primary_contrast'] ?? '#FFFFFF';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Presensi Karyawan - {{ $generalsetting->nama_perusahaan ?? 'HRIS Enterprise' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap');

        :root {
            --theme-color-1: {{ $primaryColor }};
            --theme-color-2: {{ $secondaryColor }};
            --color-primary-hover: {{ $primaryHover }};
            --theme-primary-contrast: {{ $primaryContrast }};
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 11px;
            color: #0F172A;
            background: #FAF9F8;
            margin: 0;
            padding: 20px;
        }

        .page-sheet {
            background: #FFFFFF;
            max-width: 1140px;
            margin: 0 auto;
            padding: 24px 28px;
            border-radius: 4px;
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        /* Top Action Bar for Browser View */
        .preview-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1140px;
            margin: 0 auto 12px auto;
            padding: 10px 16px;
            background: #FFFFFF;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 4px;
        }
        .toolbar-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--theme-color-1, #3C2A21);
        }
        .toolbar-info .badge-mode {
            padding: 2px 7px;
            background: #F1F5F9;
            color: #334155;
            border: 1px solid #CBD5E1;
            border-radius: 3px;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print {
            padding: 7px 16px;
            background: var(--theme-color-1, #3C2A21);
            color: var(--theme-primary-contrast, #FFFFFF);
            border: none;
            border-radius: 4px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover {
            background: var(--color-primary-hover, var(--theme-color-2, #634832));
        }
        .btn-close-view {
            padding: 7px 14px;
            background: #FFFFFF;
            color: #475569;
            border: 1px solid #CBD5E1;
            border-radius: 4px;
            font-size: 11.5px;
            font-weight: 500;
            cursor: pointer;
        }
        .btn-close-view:hover {
            background: #F1F5F9;
        }

        /* Corporate KOP Header */
        .kop-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
        }
        .kop-identity {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .kop-logo {
            max-height: 52px;
            max-width: 130px;
            object-fit: contain;
        }
        .kop-logo-monogram {
            width: 48px;
            height: 48px;
            background: var(--theme-color-1, #3C2A21);
            color: var(--theme-primary-contrast, #FFFFFF);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            border-radius: 4px;
        }
        .kop-company-title {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .kop-company-sub {
            margin: 2px 0 0 0;
            font-size: 10.5px;
            font-weight: 600;
            color: var(--theme-color-1, #3C2A21);
        }
        .kop-company-meta {
            margin: 3px 0 0 0;
            font-size: 9.5px;
            color: #64748B;
            line-height: 1.4;
        }
        .kop-doc-badge {
            text-align: right;
            border-left: 1px solid #E2E8F0;
            padding-left: 14px;
        }
        .kop-doc-badge .doc-title {
            font-size: 8.5px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-doc-badge .doc-ref {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10.5px;
            font-weight: 700;
            color: #0F172A;
            margin: 2px 0;
        }
        .kop-doc-badge .doc-date {
            font-size: 9.5px;
            color: #64748B;
        }

        /* Divider */
        .kop-divider-primary {
            height: 2px;
            background: var(--theme-color-1, #3C2A21);
            margin-bottom: 2px;
        }
        .kop-divider-secondary {
            height: 1px;
            background: #E2E8F0;
            margin-bottom: 12px;
        }

        /* Document Headline */
        .report-headline {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .report-headline h2 {
            margin: 0;
            font-size: 13px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .report-headline .period-badge {
            font-size: 10.5px;
            font-weight: 600;
            color: #475569;
        }

        /* Pure Typographic Audit Summary Ledger (Anti-Slop) */
        .audit-summary-ledger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #F4F3F2;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            padding: 8px 16px;
            margin-bottom: 12px;
        }
        .ledger-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .ledger-label {
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: #64748B;
        }
        .ledger-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: #0F172A;
        }
        .ledger-val small {
            font-size: 10px;
            font-weight: 500;
            color: #64748B;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .ledger-sep {
            width: 1px;
            height: 22px;
            background: #E2E8F0;
        }

        /* Data Grid */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.grid th {
            background-color: var(--theme-color-1, #3C2A21);
            color: var(--theme-primary-contrast, #FFFFFF);
            font-weight: 700;
            font-size: 9.5px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            padding: 7px 5px;
            border: 1px solid var(--theme-color-1, #3C2A21);
            vertical-align: middle;
            text-align: center;
        }
        table.grid td {
            padding: 5px 5px;
            border: 1px solid #E2E8F0;
            vertical-align: middle;
            color: #1E293B;
        }
        table.grid tbody tr:nth-child(even) {
            background-color: #F4F3F2;
        }
        table.grid tfoot td {
            background-color: #F1F5F9;
            font-weight: 700;
            border-top: 1.5px solid var(--theme-color-1, #3C2A21);
            border-bottom: 1.5px solid var(--theme-color-1, #3C2A21);
            padding: 7px 5px;
        }
        .num-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9.5px;
        }

        /* Sign-off */
        .sign-wrapper {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .sign-meta-date {
            text-align: right;
            font-size: 10.5px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 10px;
        }
        .sign-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
        }
        .sign-col {
            padding: 10px;
            background: #FAFAFA;
            border: 1px dashed #CBD5E1;
            border-radius: 4px;
        }
        .sign-role {
            font-size: 9px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 50px;
        }
        .sign-name {
            font-size: 10.5px;
            font-weight: 700;
            color: #0F172A;
            border-bottom: 1px solid #0F172A;
            padding-bottom: 2px;
            display: inline-block;
            min-width: 130px;
        }
        .sign-title {
            font-size: 9px;
            color: #64748B;
            margin-top: 2px;
        }

        /* Document Security Notice */
        .doc-security {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8.5px;
            color: #94A3B8;
            border-top: 1px solid #E2E8F0;
            padding-top: 6px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .page-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                border-radius: 0 !important;
                max-width: 100% !important;
            }
            @page {
                size: A4 landscape;
                margin: 8mm 10mm 10mm 10mm;
            }
            table.grid {
                page-break-inside: auto;
            }
            table.grid tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
            .sign-wrapper {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- Browser Action Toolbar (Hidden in Print) -->
    <div class="preview-toolbar no-print">
        <div class="toolbar-info">
            <span class="badge-mode">A4 Landscape</span>
            <span>Rekapitulasi Presensi Karyawan &bull; {{ $generalsetting->nama_perusahaan ?? 'HRIS Enterprise' }}</span>
        </div>
        <div class="toolbar-actions">
            <button type="button" class="btn-print" onclick="window.print()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Cetak / Simpan PDF
            </button>
            <button type="button" class="btn-close-view" onclick="window.close()">Tutup</button>
        </div>
    </div>

    <!-- Main Printable Sheet -->
    <div class="page-sheet">

        <!-- KOP Surat Resmi -->
        <div class="kop-container">
            <div class="kop-identity">
                @php
                    $logoPath = null;
                    if (!empty($generalsetting->logo)) {
                        if (file_exists(public_path('storage/logo/' . $generalsetting->logo))) {
                            $logoPath = asset('storage/logo/' . $generalsetting->logo);
                        } elseif (file_exists(storage_path('app/public/logo/' . $generalsetting->logo))) {
                            $logoPath = asset('storage/logo/' . $generalsetting->logo);
                        }
                    }
                @endphp

                @if ($logoPath)
                    <img src="{{ $logoPath }}" alt="Logo Perusahaan" class="kop-logo">
                @else
                    <div class="kop-logo-monogram">
                        {{ substr($generalsetting->nama_perusahaan ?? 'P', 0, 1) }}
                    </div>
                @endif

                <div>
                    <h1 class="kop-company-title">{{ $generalsetting->nama_perusahaan ?? 'PERUSAHAAN' }}</h1>
                    <div class="kop-company-sub">{{ strtoupper(company_setting('app_tagline') ?: 'Sistem Informasi Manajemen SDM & Presensi') }}</div>
                    <div class="kop-company-meta">
                        {{ $generalsetting->alamat ?? 'Alamat Kantor Pusat' }}
                        @if(!empty($generalsetting->telepon)) &bull; Telp: {{ $generalsetting->telepon }} @endif
                    </div>
                </div>
            </div>

            <div class="kop-doc-badge">
                <div class="doc-title">Dokumen Audit Presensi</div>
                <div class="doc-ref">REF/PRS/{{ date('Ym', strtotime($periode_dari)) }}-{{ str_pad(count($karyawanList), 3, '0', STR_PAD_LEFT) }}</div>
                <div class="doc-date">Dicetak: {{ date('d/m/Y H:i') }} WIB</div>
            </div>
        </div>

        <div class="kop-divider-primary"></div>
        <div class="kop-divider-secondary"></div>

        <!-- Headline -->
        <div class="report-headline">
            <h2>Rekapitulasi Presensi & Disiplin Karyawan</h2>
            <div class="period-badge">
                Periode: {{ date('d F Y', strtotime($periode_dari)) }} s.d. {{ date('d F Y', strtotime($periode_sampai)) }}
            </div>
        </div>

        @php
            $today = date('Y-m-d');
            $effectiveEnd = ($periode_sampai > $today) ? $today : $periode_sampai;

            // Calculate total working days elapsed up to effective date
            $totalWorkDays = 0;
            $curD = $periode_dari;
            $sistemHariKerja = (string)($generalsetting->sistem_hari_kerja ?? '6');
            while (strtotime($curD) <= strtotime($effectiveEnd)) {
                $w = (int)date('w', strtotime($curD));
                $isWeekend = ($w === 0) || ($sistemHariKerja === '5' && $w === 6);
                if (!isset($hariLibur[$curD]) && !$isWeekend) {
                    $totalWorkDays++;
                }
                $curD = date('Y-m-d', strtotime('+1 day', strtotime($curD)));
            }
            if ($totalWorkDays == 0) $totalWorkDays = 1;

            $totalEmployees = count($karyawanList);

            // Single pass calculation of all employee statistics using effective schedule
            $employeeStats = [];
            $grandHadirNormal = 0;
            $grandDispensasi = 0;
            $grandTelat = 0;
            $grandIzin = 0;
            $grandSakit = 0;
            $grandCuti = 0;
            $grandAlfa = 0;
            $grandExpected = 0;

            foreach ($karyawanList as $k) {
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
                    $isResigned = ($k->status_aktif_karyawan == 0 || $k->status_aktif_karyawan === '0')
                        && !empty($k->tanggal_nonaktif)
                        && $curr > $k->tanggal_nonaktif;

                    if (!empty($kode_cabang) && $effCabang !== $kode_cabang && !$pres) {
                        $curr = date('Y-m-d', strtotime('+1 day', strtotime($curr)));
                        continue;
                    }

                    if (!$isOff && $curr <= $effectiveEnd && !$isResigned) {
                        $empWorkDays++;
                    }

                    if ($pres) {
                        if ($pres->status === 'h') {
                            $actualIn = date('H:i:s', strtotime($pres->jam_in));

                            if ($disp && $actualIn <= $disp->batas_dispensasi) {
                                $totDispensasi++;
                            } elseif (isset($pres->is_terlambat) && $pres->is_terlambat !== null) {
                                // P1-4: Snapshot immutability from attendance record
                                if ($pres->is_terlambat == 1) {
                                    $totTelat++;
                                } else {
                                    $totHadirNormal++;
                                }
                            } else {
                                // Legacy fallback
                                $jk = $jamkerja_map[$pres->kode_jam_kerja] ?? ($eff['jam_kerja'] ?? $defaultJk);
                                $batas = $jk && $jk->batas_toleransi ? date('H:i:s', strtotime($jk->batas_toleransi)) : ($jk && $jk->jam_masuk ? date('H:i:s', strtotime($jk->jam_masuk)) : '08:00:00');
                                if ($actualIn <= $batas) {
                                    $totHadirNormal++;
                                } else {
                                    $totTelat++;
                                }
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
                        // Only count as Alfa if date is scheduled work day, date has already elapsed, and employee had not resigned
                        if (!$isOff && $isPastOrToday && !$isResigned) {
                            $totAlfa++;
                        }
                    }
                    $curr = date('Y-m-d', strtotime('+1 day', strtotime($curr)));
                }

                $effectiveWorkDays = $empWorkDays > 0 ? $empWorkDays : 1;
                $rowTotalHadir = $totHadirNormal + $totDispensasi;
                $empRate = min(100, round(($rowTotalHadir / $effectiveWorkDays) * 100));

                $grandHadirNormal += $totHadirNormal;
                $grandDispensasi += $totDispensasi;
                $grandTelat += $totTelat;
                $grandIzin += $totIzin;
                $grandSakit += $totSakit;
                $grandCuti += $totCuti;
                $grandAlfa += $totAlfa;
                $grandExpected += $effectiveWorkDays;

                $employeeStats[$k->nik] = [
                    'hadir_normal' => $totHadirNormal,
                    'dispensasi' => $totDispensasi,
                    'telat' => $totTelat,
                    'izin' => $totIzin,
                    'sakit' => $totSakit,
                    'cuti' => $totCuti,
                    'alfa' => $totAlfa,
                    'total_hadir' => $rowTotalHadir,
                    'work_days' => $empWorkDays,
                    'rate' => $empRate
                ];
            }

            $grandTotalHadir = $grandHadirNormal + $grandDispensasi;
            $attendanceRate = $grandExpected > 0 ? round(($grandTotalHadir / $grandExpected) * 100, 1) : 0;
        @endphp

        <!-- Pure Typographic Audit Summary Ledger (Anti-Slop) -->
        <div class="audit-summary-ledger">
            <div class="ledger-item">
                <span class="ledger-label">Total Personel</span>
                <span class="ledger-val">{{ number_format($totalEmployees, 0, ',', '.') }} <small>Pegawai</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Hari Kerja Berjalan</span>
                <span class="ledger-val">{{ $totalWorkDays }} <small>Hari</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Tingkat Kehadiran</span>
                <span class="ledger-val">{{ $attendanceRate }}% <small>({{ number_format($grandTotalHadir, 0, ',', '.') }} Sesi)</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Keterlambatan</span>
                <span class="ledger-val">{{ number_format($grandTelat, 0, ',', '.') }} <small>Kejadian</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Izin, Sakit & Cuti</span>
                <span class="ledger-val">{{ number_format($grandIzin + $grandSakit + $grandCuti, 0, ',', '.') }} <small>Hari</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Tanpa Keterangan (Alfa)</span>
                <span class="ledger-val">{{ number_format($grandAlfa, 0, ',', '.') }} <small>Hari</small></span>
            </div>
        </div>

        <!-- Data Grid -->
        <table class="grid">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 80px;">NIK</th>
                    <th style="text-align: left; min-width: 140px;">Nama Karyawan</th>
                    <th style="text-align: left;">Outlet / Cabang</th>
                    <th style="text-align: left;">Shift Default</th>
                    <th style="width: 50px;">Hadir Tepat</th>
                    <th style="width: 50px;">Dispen</th>
                    <th style="width: 50px;">Telat</th>
                    <th style="width: 40px;">Izin</th>
                    <th style="width: 40px;">Sakit</th>
                    <th style="width: 40px;">Cuti</th>
                    <th style="width: 42px;">Alfa</th>
                    <th style="width: 55px; background-color: var(--color-primary-hover, var(--theme-color-2, #25160E));">Total Hadir</th>
                    <th style="width: 48px; background-color: var(--color-primary-hover, var(--theme-color-2, #25160E));">% Hadir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawanList as $k)
                    @php $s = $employeeStats[$k->nik]; @endphp
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td style="text-align: center;" class="num-code">{{ $k->nik }}</td>
                        <td><strong>{{ $k->nama_karyawan }}</strong></td>
                        <td>{{ $k->cabang->nama_cabang ?? '-' }}</td>
                        <td>{{ $k->jamkerja->nama_jam_kerja ?? 'Shift Pagi' }}</td>
                        <td style="text-align: center; color: #059669; font-weight: 600;">{{ $s['hadir_normal'] }}</td>
                        <td style="text-align: center; color: #0284C7; font-weight: 600;">{{ $s['dispensasi'] }}</td>
                        <td style="text-align: center; @if($s['telat'] > 0) color: #D97706; font-weight: 700; @endif">{{ $s['telat'] }}</td>
                        <td style="text-align: center;">{{ $s['izin'] }}</td>
                        <td style="text-align: center;">{{ $s['sakit'] }}</td>
                        <td style="text-align: center;">{{ $s['cuti'] }}</td>
                        <td style="text-align: center; @if($s['alfa'] > 0) color: #DC2626; font-weight: 700; @endif">{{ $s['alfa'] }}</td>
                        <td style="text-align: center; font-weight: 700; background-color: #F8FAF8;" class="num-code">{{ $s['total_hadir'] }}</td>
                        <td style="text-align: center; font-weight: 600; font-size: 9.5px;" class="num-code">{{ $s['rate'] }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" style="text-align: center; padding: 20px; color: #64748B;">
                            Tidak ada catatan presensi dalam parameter filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right; text-transform: uppercase; letter-spacing: 0.5px;">TOTAL AKUMULASI:</td>
                    <td style="text-align: center; color: #059669;" class="num-code">{{ number_format($grandHadirNormal, 0, ',', '.') }}</td>
                    <td style="text-align: center; color: #0284C7;" class="num-code">{{ number_format($grandDispensasi, 0, ',', '.') }}</td>
                    <td style="text-align: center; color: #D97706;" class="num-code">{{ number_format($grandTelat, 0, ',', '.') }}</td>
                    <td style="text-align: center;" class="num-code">{{ number_format($grandIzin, 0, ',', '.') }}</td>
                    <td style="text-align: center;" class="num-code">{{ number_format($grandSakit, 0, ',', '.') }}</td>
                    <td style="text-align: center;" class="num-code">{{ number_format($grandCuti, 0, ',', '.') }}</td>
                    <td style="text-align: center; color: #DC2626;" class="num-code">{{ number_format($grandAlfa, 0, ',', '.') }}</td>
                    <td style="text-align: center; font-size: 10.5px; color: var(--theme-color-1, #3C2A21);" class="num-code">{{ number_format($grandTotalHadir, 0, ',', '.') }}</td>
                    <td style="text-align: center;" class="num-code">{{ $attendanceRate }}%</td>
                </tr>
            </tfoot>
        </table>

        <!-- Formal Sign-Off Matrix -->
        <div class="sign-wrapper">
            <div class="sign-meta-date">
                {{ $generalsetting->alamat ? explode(',', $generalsetting->alamat)[count(explode(',', $generalsetting->alamat))-1] : 'Surabaya' }},
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
            <div class="sign-grid">
                <div class="sign-col">
                    <div class="sign-role">Dibuat Oleh (Staff HRD)</div>
                    <div class="sign-name">{{ auth()->user()->name ?? 'Staff Operasional' }}</div>
                    <div class="sign-title">Human Resources Officer</div>
                </div>
                <div class="sign-col">
                    <div class="sign-role">Diperiksa Oleh (Manager HRD)</div>
                    <div class="sign-name">{{ $generalsetting->nama_hrd ?? 'HRD Manager' }}</div>
                    <div class="sign-title">Head of People & Culture</div>
                </div>
                <div class="sign-col">
                    <div class="sign-role">Disetujui Oleh (Pimpinan)</div>
                    <div class="sign-name">General Manager / Direktur</div>
                    <div class="sign-title">Operational Director</div>
                </div>
            </div>
        </div>

        <!-- Security & Audit Footer -->
        <div class="doc-security">
            <span>Sistem Presensi Biometrik & Geofencing GPS Enterprise &bull; Rekap Resmi Terkunci</span>
            <span>Dokumen Rahasia &bull; ID Audit: PRS-{{ date('Ym', strtotime($periode_dari)) }}-{{ substr(sha1($periode_dari . $periode_sampai . $totalEmployees), 0, 8) }}</span>
        </div>

    </div>

</body>
</html>
