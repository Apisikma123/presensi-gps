<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi - {{ $karyawan->nama_karyawan }} ({{ $karyawan->nik }})</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap');

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 11px;
            color: #0F172A;
            background: #F8FAF8;
            margin: 0;
            padding: 20px;
        }

        .page-sheet {
            background: #FFFFFF;
            max-width: 860px;
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
            max-width: 860px;
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
            color: #1E4D3E;
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
            background: #1E4D3E;
            color: #FFFFFF;
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
            background: #32745E;
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
            max-height: 50px;
            max-width: 120px;
            object-fit: contain;
        }
        .kop-logo-monogram {
            width: 44px;
            height: 44px;
            background: #1E4D3E;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
            border-radius: 4px;
        }
        .kop-company-title {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .kop-company-sub {
            margin: 2px 0 0 0;
            font-size: 10px;
            font-weight: 600;
            color: #1E4D3E;
        }
        .kop-company-meta {
            margin: 3px 0 0 0;
            font-size: 9px;
            color: #64748B;
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
        }
        .kop-doc-badge .doc-ref {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            font-weight: 700;
            color: #0F172A;
            margin: 2px 0;
        }
        .kop-doc-badge .doc-date {
            font-size: 9px;
            color: #64748B;
        }

        .kop-divider-primary {
            height: 2px;
            background: #1E4D3E;
            margin-bottom: 2px;
        }
        .kop-divider-secondary {
            height: 1px;
            background: #E2E8F0;
            margin-bottom: 12px;
        }

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

        /* Employee Identity Card */
        .employee-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 14px;
            background: #F8FAF8;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .employee-avatar-frame {
            width: 48px;
            height: 48px;
            border-radius: 4px;
            overflow: hidden;
            background: #E2E8F0;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #CBD5E1;
        }
        .employee-avatar-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .employee-avatar-fallback {
            font-size: 16px;
            font-weight: 700;
            color: #64748B;
        }
        .employee-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4px 18px;
            width: 100%;
            font-size: 10.5px;
        }
        .emp-item {
            display: flex;
            align-items: baseline;
        }
        .emp-item .emp-label {
            width: 100px;
            color: #64748B;
            font-weight: 500;
            flex-shrink: 0;
        }
        .emp-item .emp-colon {
            width: 10px;
            color: #94A3B8;
        }
        .emp-item .emp-val {
            color: #0F172A;
            font-weight: 600;
        }

        /* Pure Typographic Audit Summary Ledger (Anti-Slop) */
        .audit-summary-ledger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #F8FAF8;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            padding: 8px 14px;
            margin-bottom: 12px;
        }
        .ledger-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .ledger-label {
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: #64748B;
        }
        .ledger-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12.5px;
            font-weight: 700;
            color: #0F172A;
        }
        .ledger-val small {
            font-size: 9.5px;
            font-weight: 500;
            color: #64748B;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .ledger-sep {
            width: 1px;
            height: 20px;
            background: #E2E8F0;
        }

        /* Data Grid */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.grid th {
            background-color: #1E4D3E;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 9px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            padding: 7px 5px;
            border: 1px solid #163B2F;
            text-align: center;
        }
        table.grid td {
            padding: 5px 5px;
            border: 1px solid #E2E8F0;
            vertical-align: middle;
            color: #1E293B;
        }
        table.grid tbody tr:nth-child(even) {
            background-color: #F8FAF8;
        }
        .num-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9.5px;
        }

        /* Status Text */
        .status-badge {
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
        }
        .status-hadir { color: #059669; }
        .status-dispen { color: #0284C7; }
        .status-telat { color: #D97706; }
        .status-izin { color: #475569; }
        .status-alfa { color: #DC2626; }
        .status-libur { color: #64748B; }
        .status-future { color: #94A3B8; font-weight: normal; }

        /* Sign-off */
        .sign-wrapper {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .sign-meta-date {
            text-align: right;
            font-size: 10.5px;
            color: #334155;
            margin-bottom: 10px;
        }
        .sign-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
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
            margin-bottom: 45px;
        }
        .sign-name {
            font-size: 10.5px;
            font-weight: 700;
            color: #0F172A;
            border-bottom: 1px solid #0F172A;
            padding-bottom: 2px;
            display: inline-block;
            min-width: 120px;
        }
        .sign-title {
            font-size: 9px;
            color: #64748B;
            margin-top: 2px;
        }

        .doc-security {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8.5px;
            color: #94A3B8;
            border-top: 1px solid #E2E8F0;
            padding-top: 5px;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: #FFFFFF !important; padding: 0 !important; margin: 0 !important; }
            .page-sheet { box-shadow: none !important; border: none !important; padding: 0 !important; margin: 0 !important; border-radius: 0 !important; max-width: 100% !important; }
            @page { size: A4 portrait; margin: 8mm 10mm 10mm 10mm; }
            table.grid { page-break-inside: auto; }
            table.grid tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            .sign-wrapper { page-break-inside: avoid !important; }
        }
    </style>
</head>
<body>

    <!-- Browser Action Toolbar (Hidden in Print) -->
    <div class="preview-toolbar no-print">
        <div class="toolbar-info">
            <span class="badge-mode">A4 Portrait</span>
            <span>Laporan Rekapitulasi Presensi Individual: {{ $karyawan->nama_karyawan }}</span>
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
                    <div class="kop-company-sub">SISTEM INFORMASI MANAJEMEN PRESENSI & OPERASIONAL OUTLET</div>
                    <div class="kop-company-meta">
                        {{ $generalsetting->alamat ?? 'Alamat Kantor Pusat' }}
                        @if(!empty($generalsetting->telepon)) &bull; Telp: {{ $generalsetting->telepon }} @endif
                    </div>
                </div>
            </div>

            <div class="kop-doc-badge">
                <div class="doc-title">Dokumen Audit Presensi</div>
                <div class="doc-ref">IND/{{ $karyawan->nik }}/{{ date('Ym', strtotime($periode_dari)) }}</div>
                <div class="doc-date">Dicetak: {{ date('d/m/Y H:i') }} WIB</div>
            </div>
        </div>

        <div class="kop-divider-primary"></div>
        <div class="kop-divider-secondary"></div>

        <!-- Headline -->
        <div class="report-headline">
            <h2>Laporan Presensi & Kehadiran Individual</h2>
            <div class="period-badge">
                Periode: {{ date('d F Y', strtotime($periode_dari)) }} s.d. {{ date('d F Y', strtotime($periode_sampai)) }}
            </div>
        </div>

        <!-- Employee Info Bio -->
        <div class="employee-card">
            <div class="employee-avatar-frame">
                @php
                    $fotoKaryawan = null;
                    if (!empty($karyawan->foto)) {
                        if (file_exists(public_path('storage/karyawan/' . $karyawan->foto))) {
                            $fotoKaryawan = asset('storage/karyawan/' . $karyawan->foto);
                        } elseif (file_exists(storage_path('app/public/karyawan/' . $karyawan->foto))) {
                            $fotoKaryawan = asset('storage/karyawan/' . $karyawan->foto);
                        }
                    }
                @endphp
                @if ($fotoKaryawan)
                    <img src="{{ $fotoKaryawan }}" alt="{{ $karyawan->nama_karyawan }}">
                @else
                    <div class="employee-avatar-fallback">
                        {{ substr($karyawan->nama_karyawan, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="employee-grid">
                <div class="emp-item">
                    <span class="emp-label">Nomor Induk (NIK)</span>
                    <span class="emp-colon">:</span>
                    <span class="emp-val num-code">{{ $karyawan->nik }}</span>
                </div>
                <div class="emp-item">
                    <span class="emp-label">Outlet / Cabang</span>
                    <span class="emp-colon">:</span>
                    <span class="emp-val">{{ $karyawan->cabang->nama_cabang ?? '-' }}</span>
                </div>
                <div class="emp-item">
                    <span class="emp-label">Nama Lengkap</span>
                    <span class="emp-colon">:</span>
                    <span class="emp-val">{{ $karyawan->nama_karyawan }}</span>
                </div>
                <div class="emp-item">
                    <span class="emp-label">Departemen / Divisi</span>
                    <span class="emp-colon">:</span>
                    <span class="emp-val">{{ $karyawan->departemen->nama_dept ?? '-' }}</span>
                </div>
                <div class="emp-item">
                    <span class="emp-label">Jabatan</span>
                    <span class="emp-colon">:</span>
                    <span class="emp-val">{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</span>
                </div>
                <div class="emp-item">
                    <span class="emp-label">Shift Penugasan</span>
                    <span class="emp-colon">:</span>
                    <span class="emp-val">{{ $karyawan->jamkerja->nama_jam_kerja ?? 'Shift Pagi' }}</span>
                </div>
            </div>
        </div>

        @php
            $today = date('Y-m-d');
            $effectiveEnd = ($periode_sampai > $today) ? $today : $periode_sampai;

            $curr = $periode_dari;
            $totHadirNormal = 0; $totDispensasi = 0; $totTelat = 0;
            $totIzin = 0; $totSakit = 0; $totCuti = 0; $totAlfa = 0; $totLibur = 0;
            $workDaysCount = 0;
            $defaultJk = $jamkerja_map[$karyawan->kode_jam_kerja ?? 'JK01'] ?? $jamkerja_map->first();
            $sistemHariKerja = (string)($generalsetting->sistem_hari_kerja ?? '6');

            while (strtotime($curr) <= strtotime($effectiveEnd)) {
                $eff = $effectiveSchedules[$karyawan->nik][$curr] ?? null;
                $isOff = $eff ? $eff['is_off'] : false;
                if (!$isOff) {
                    $workDaysCount++;
                } else {
                    $totLibur++;
                }

                $key = $karyawan->nik . '|' . $curr;
                $pres = $presensiMap[$key] ?? null;
                $disp = $dispensasiMap[$key] ?? null;

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
                    if (!$isOff) {
                        $totAlfa++;
                    }
                }
                $curr = date('Y-m-d', strtotime('+1 day', strtotime($curr)));
            }

            if ($workDaysCount == 0) $workDaysCount = 1;
            $totalHadirActual = $totHadirNormal + $totDispensasi;
            $persenKehadiran = round(($totalHadirActual / $workDaysCount) * 100, 1);
        @endphp

        <!-- Pure Typographic Audit Summary Ledger (Anti-Slop) -->
        <div class="audit-summary-ledger">
            <div class="ledger-item">
                <span class="ledger-label">Hari Kerja Berjalan</span>
                <span class="ledger-val">{{ $workDaysCount }} <small>Hari</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Tepat Waktu</span>
                <span class="ledger-val">{{ $totHadirNormal }} <small>Hari</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Dispensasi</span>
                <span class="ledger-val">{{ $totDispensasi }} <small>Hari</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Terlambat</span>
                <span class="ledger-val">{{ $totTelat }} <small>Kejadian</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Izin/Sakit/Cuti</span>
                <span class="ledger-val">{{ $totIzin + $totSakit + $totCuti }} <small>Hari</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Rasio Kehadiran</span>
                <span class="ledger-val">{{ $persenKehadiran }}%</span>
            </div>
        </div>

        <!-- Detail Table -->
        <table class="grid">
            <thead>
                <tr>
                    <th style="width: 28px;">No</th>
                    <th style="width: 75px;">Tanggal</th>
                    <th style="width: 58px;">Hari</th>
                    <th style="text-align: left;">Shift</th>
                    <th style="width: 65px;">Masuk</th>
                    <th style="width: 65px;">Pulang</th>
                    <th style="width: 95px;">Status</th>
                    <th style="text-align: left;">Keterangan / Audit Presensi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $curr = $periode_dari;
                    $no = 1;
                @endphp
                @while (strtotime($curr) <= strtotime($periode_sampai))
                    @php
                        $key = $karyawan->nik . '|' . $curr;
                        $pres = $presensiMap[$key] ?? null;
                        $disp = $dispensasiMap[$key] ?? null;
                        $eff = $effectiveSchedules[$karyawan->nik][$curr] ?? null;
                        $isOff = $eff ? $eff['is_off'] : false;
                        $isPastOrToday = ($curr <= $today);

                        $statusText = 'ALFA';
                        $statusClass = 'status-alfa';
                        $keterangan = 'Tidak hadir tanpa konfirmasi';
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
                                    $statusText = 'HADIR';
                                    $statusClass = 'status-hadir';
                                    $keterangan = 'Hadir di Luar Jadwal (Off Day)';
                                } elseif ($disp && $actualIn <= $disp->batas_dispensasi) {
                                    $statusText = 'DISPENSASI';
                                    $statusClass = 'status-dispen';
                                    $keterangan = 'Dispen s/d ' . date('H:i', strtotime($disp->batas_dispensasi)) . ' (' . ($disp->alasan ?? 'Disetujui') . ')';
                                } elseif ($actualIn <= $batas) {
                                    $statusText = 'HADIR';
                                    $statusClass = 'status-hadir';
                                    $keterangan = 'Tepat waktu';
                                } else {
                                    $statusText = 'TELAT';
                                    $statusClass = 'status-telat';
                                    $keterangan = 'Masuk lewat toleransi ' . date('H:i', strtotime($batas));
                                }

                                if ($pres->is_early_out) {
                                    $keterangan .= ' • Pulang Cepat: ' . $pres->early_out_minutes . ' mnt';
                                    if (!empty($pres->early_out_reason)) {
                                        $keterangan .= ' (' . $pres->early_out_reason . ')';
                                    }
                                }
                            } elseif ($pres->status === 'i') {
                                $statusText = 'IZIN';
                                $statusClass = 'status-izin';
                                $keterangan = $pres->keterangan_izin ?? ($pres->keterangan ?? 'Izin resmi');
                            } elseif ($pres->status === 's') {
                                $statusText = 'SAKIT';
                                $statusClass = 'status-izin';
                                $keterangan = $pres->keterangan_sakit ?? ($pres->keterangan ?? 'Surat dokter / sakit');
                            } elseif ($pres->status === 'c') {
                                $statusText = 'CUTI';
                                $statusClass = 'status-izin';
                                $keterangan = $pres->keterangan_cuti ?? ($pres->keterangan ?? 'Hak cuti terpakai');
                            } elseif ($pres->status === 'a') {
                                $statusText = 'ALFA';
                                $statusClass = 'status-alfa';
                                $keterangan = $pres->keterangan ?? 'Tanpa keterangan (Alpha)';
                            }
                        } else {
                            if ($isOff) {
                                $statusText = 'LIBUR';
                                $statusClass = 'status-libur';
                                $keterangan = $eff['keterangan'] ?? 'Libur Rutin (Off Day)';
                            } elseif (!$isPastOrToday) {
                                $statusText = '-';
                                $statusClass = 'status-future';
                                $keterangan = 'Jadwal mendatang';
                            } else {
                                $statusText = 'ALFA';
                                $statusClass = 'status-alfa';
                                $keterangan = 'Tidak hadir tanpa konfirmasi';
                            }
                        }
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td style="text-align: center;" class="num-code">{{ date('d/m/Y', strtotime($curr)) }}</td>
                        <td style="text-align: center; color: #64748B;">{{ \Carbon\Carbon::parse($curr)->translatedFormat('l') }}</td>
                        <td>{{ $namaShift }}</td>
                        <td style="text-align: center; font-weight: 600;" class="num-code">{{ $jamIn }}</td>
                        <td style="text-align: center;" class="num-code">{{ $jamOut }}</td>
                        <td style="text-align: center;"><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                        <td style="color: #475569; font-size: 9.5px;">{{ $keterangan }}</td>
                    </tr>
                    @php $curr = date('Y-m-d', strtotime('+1 day', strtotime($curr))); @endphp
                @endwhile
            </tbody>
        </table>

        <!-- Sign-Off Matrix -->
        <div class="sign-wrapper">
            <div class="sign-meta-date">
                {{ $generalsetting->alamat ? explode(',', $generalsetting->alamat)[count(explode(',', $generalsetting->alamat))-1] : 'Surabaya' }},
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
            <div class="sign-grid">
                <div class="sign-col">
                    <div class="sign-role">Pegawai Yang Dievaluasi</div>
                    <div class="sign-name">{{ $karyawan->nama_karyawan }}</div>
                    <div class="sign-title">NIK: {{ $karyawan->nik }}</div>
                </div>
                <div class="sign-col">
                    <div class="sign-role">Atasan Langsung / SPV</div>
                    <div class="sign-name">Supervisor / Branch Lead</div>
                    <div class="sign-title">Outlet {{ $karyawan->cabang->nama_cabang ?? 'Cabang' }}</div>
                </div>
                <div class="sign-col">
                    <div class="sign-role">Mengetahui (HRD)</div>
                    <div class="sign-name">{{ $generalsetting->nama_hrd ?? 'HRD Manager' }}</div>
                    <div class="sign-title">People & Culture Management</div>
                </div>
            </div>
        </div>

        <!-- Security & Audit Footer -->
        <div class="doc-security">
            <span>Sistem Presensi Biometrik & Geofencing GPS Enterprise &bull; Laporan Individual Terverifikasi</span>
            <span>Dokumen Rahasia &bull; Dicetak oleh: {{ auth()->user()->name ?? 'Administrator' }}</span>
        </div>

    </div>

</body>
</html>
