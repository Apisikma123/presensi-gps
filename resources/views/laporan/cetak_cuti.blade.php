<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Cuti Karyawan - {{ $tahun }}</title>
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
            max-height: 52px;
            max-width: 130px;
            object-fit: contain;
        }
        .kop-logo-monogram {
            width: 48px;
            height: 48px;
            background: #1E4D3E;
            color: #FFFFFF;
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
            color: #1E4D3E;
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

        /* Filter Meta Line */
        .filter-meta-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 12px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 10.5px;
        }
        .filter-item {
            display: flex;
            gap: 6px;
        }
        .filter-item .label {
            color: #64748B;
            font-weight: 500;
        }
        .filter-item .val {
            color: #0F172A;
            font-weight: 700;
        }

        /* Pure Typographic Audit Summary Ledger (Anti-Slop) */
        .audit-summary-ledger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #F8FAF8;
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
            background-color: #1E4D3E;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 9.5px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            padding: 6px 4px;
            border: 1px solid #163B2F;
            text-align: center;
            vertical-align: middle;
        }
        table.grid td {
            padding: 5px 4px;
            border: 1px solid #E2E8F0;
            vertical-align: middle;
            color: #1E293B;
        }
        table.grid tbody tr:nth-child(even) {
            background-color: #F8FAF8;
        }
        table.grid tfoot td {
            background-color: #F1F5F9;
            font-weight: 700;
            border-top: 1.5px solid #1E4D3E;
            border-bottom: 1.5px solid #1E4D3E;
            padding: 7px 4px;
        }
        .num-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9.5px;
        }
        .cell-cuti-active {
            font-weight: 700;
            color: #0F766E !important;
        }

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
            margin-bottom: 45px;
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
            .no-print { display: none !important; }
            body { background: #FFFFFF !important; padding: 0 !important; margin: 0 !important; }
            .page-sheet { box-shadow: none !important; border: none !important; padding: 0 !important; margin: 0 !important; border-radius: 0 !important; max-width: 100% !important; }
            @page { size: A4 landscape; margin: 8mm 10mm 10mm 10mm; }
            table.grid { page-break-inside: auto; }
            table.grid tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            .sign-wrapper { page-break-inside: avoid !important; }
        }
    </style>
</head>
<body>

    <!-- Browser Action Toolbar (Hidden in Print) -->
    <div class="preview-toolbar no-print">
        <div class="toolbar-info">
            <span class="badge-mode">A4 Landscape</span>
            <span>Rekapitulasi Cuti Tahunan Karyawan &bull; Tahun {{ $tahun }}</span>
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
                    <div class="kop-company-sub">SISTEM INFORMASI MANAJEMEN CUTI & PERIZINAN KERJA</div>
                    <div class="kop-company-meta">
                        {{ $generalsetting->alamat ?? 'Alamat Kantor Pusat' }}
                        @if(!empty($generalsetting->telepon)) &bull; Telp: {{ $generalsetting->telepon }} @endif
                    </div>
                </div>
            </div>

            <div class="kop-doc-badge">
                <div class="doc-title">Dokumen Rekap Cuti Resmi</div>
                <div class="doc-ref">CUTI/{{ $tahun }}/{{ str_pad(count($karyawan), 3, '0', STR_PAD_LEFT) }}</div>
                <div class="doc-date">Dicetak: {{ date('d/m/Y H:i') }} WIB</div>
            </div>
        </div>

        <div class="kop-divider-primary"></div>
        <div class="kop-divider-secondary"></div>

        <!-- Headline -->
        <div class="report-headline">
            <h2>Rekapitulasi Pengambilan Cuti Karyawan</h2>
            <div class="period-badge">
                Tahun Kalender: {{ $tahun }}
            </div>
        </div>

        <!-- Filter Meta Bar -->
        <div class="filter-meta-bar">
            <div class="filter-item">
                <span class="label">Cabang / Outlet:</span>
                <span class="val">{{ $namacabang }}</span>
            </div>
            <div class="filter-item">
                <span class="label">Departemen:</span>
                <span class="val">{{ $namadept }}</span>
            </div>
            <div class="filter-item">
                <span class="label">Klasifikasi Cuti:</span>
                <span class="val">{{ $jenis_cuti }}</span>
            </div>
            <div class="filter-item">
                <span class="label">Kuota Tahunan:</span>
                <span class="val">{{ $master_cuti->jumlah_hari ?? 12 }} Hari / Tahun</span>
            </div>
        </div>

        @php
            $grandTotalAmbil = 0;
            $monthTotals = array_fill(1, 12, 0);
            $totalPegawai = count($rekap_cuti);
            $pegawaiMengambilCuti = 0;

            foreach ($rekap_cuti as $nik => $d) {
                $grandTotalAmbil += $d['total_ambil'];
                if ($d['total_ambil'] > 0) $pegawaiMengambilCuti++;
                for ($m = 1; $m <= 12; $m++) {
                    $monthTotals[$m] += $d['bulan'][$m];
                }
            }

            $rataRataCuti = $totalPegawai > 0 ? round($grandTotalAmbil / $totalPegawai, 1) : 0;
        @endphp

        <!-- Pure Typographic Audit Summary Ledger (Anti-Slop) -->
        <div class="audit-summary-ledger">
            <div class="ledger-item">
                <span class="ledger-label">Total Personel</span>
                <span class="ledger-val">{{ number_format($totalPegawai, 0, ',', '.') }} <small>Karyawan</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Total Cuti Terpakai</span>
                <span class="ledger-val">{{ number_format($grandTotalAmbil, 0, ',', '.') }} <small>Hari Kerja</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Karyawan Ambil Cuti</span>
                <span class="ledger-val">{{ number_format($pegawaiMengambilCuti, 0, ',', '.') }} <small>Orang</small></span>
            </div>
            <div class="ledger-sep"></div>
            <div class="ledger-item">
                <span class="ledger-label">Rata-Rata Cuti</span>
                <span class="ledger-val">{{ $rataRataCuti }} <small>Hari / Pegawai</small></span>
            </div>
        </div>

        <!-- Data Grid -->
        <table class="grid">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 28px;">No</th>
                    <th rowspan="2" style="width: 75px;">NIK</th>
                    <th rowspan="2" style="text-align: left; min-width: 130px;">Nama Karyawan</th>
                    <th rowspan="2" style="text-align: left; width: 100px;">Cabang</th>
                    <th colspan="12" style="border-bottom: 1px solid #163B2F;">Distribusi Bulanan (Tahun {{ $tahun }})</th>
                    <th rowspan="2" style="width: 50px; background-color: #163B2F;">Total Ambil</th>
                    <th rowspan="2" style="width: 45px;">Jatah</th>
                    <th rowspan="2" style="width: 45px; background-color: #163B2F;">Sisa</th>
                </tr>
                <tr>
                    <th style="width: 28px;">Jan</th>
                    <th style="width: 28px;">Feb</th>
                    <th style="width: 28px;">Mar</th>
                    <th style="width: 28px;">Apr</th>
                    <th style="width: 28px;">Mei</th>
                    <th style="width: 28px;">Jun</th>
                    <th style="width: 28px;">Jul</th>
                    <th style="width: 28px;">Agu</th>
                    <th style="width: 28px;">Sep</th>
                    <th style="width: 28px;">Okt</th>
                    <th style="width: 28px;">Nov</th>
                    <th style="width: 28px;">Des</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekap_cuti as $nik => $d)
                    @php
                        $jatah = $master_cuti->jumlah_hari ?? 12;
                        $sisa = max(0, $jatah - $d['total_ambil']);
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td style="text-align: center;" class="num-code">{{ $d['nik_show'] }}</td>
                        <td><strong>{{ $d['nama'] }}</strong></td>
                        <td>{{ $d['cabang'] }}</td>
                        @for ($i = 1; $i <= 12; $i++)
                            @php $val = $d['bulan'][$i]; @endphp
                            <td style="text-align: center;" class="num-code @if($val > 0) cell-cuti-active @endif">
                                {{ $val > 0 ? $val : '-' }}
                            </td>
                        @endfor
                        <td style="text-align: center; font-weight: 700; color: #0F766E; background-color: #F0FDFA;" class="num-code">{{ $d['total_ambil'] }}</td>
                        <td style="text-align: center;" class="num-code">{{ $jatah }}</td>
                        <td style="text-align: center; font-weight: 700; @if($sisa <= 2) color: #D97706; @else color: #059669; @endif background-color: #F8FAF8;" class="num-code">{{ $sisa }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="19" style="text-align: center; padding: 20px; color: #64748B;">
                            Tidak ada data permohonan cuti untuk kriteria yang dipilih pada tahun {{ $tahun }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; text-transform: uppercase; letter-spacing: 0.5px;">TOTAL BULANAN:</td>
                    @for ($i = 1; $i <= 12; $i++)
                        <td style="text-align: center;" class="num-code">{{ $monthTotals[$i] > 0 ? $monthTotals[$i] : '-' }}</td>
                    @endfor
                    <td style="text-align: center; font-size: 10.5px; color: #0F766E;" class="num-code">{{ number_format($grandTotalAmbil, 0, ',', '.') }}</td>
                    <td colspan="2" style="text-align: center; font-size: 9.5px; color: #64748B;">Hari Kerja</td>
                </tr>
            </tfoot>
        </table>

        <!-- Sign-Off Matrix -->
        <div class="sign-wrapper">
            <div class="sign-meta-date">
                {{ $generalsetting->alamat ? explode(',', $generalsetting->alamat)[count(explode(',', $generalsetting->alamat))-1] : 'Surabaya' }},
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
            <div class="sign-grid">
                <div class="sign-col">
                    <div class="sign-role">Dibuat Oleh (Staff HRD)</div>
                    <div class="sign-name">{{ auth()->user()->name ?? 'HR Officer' }}</div>
                    <div class="sign-title">People Administration</div>
                </div>
                <div class="sign-col">
                    <div class="sign-role">Diperiksa Oleh (Manager HRD)</div>
                    <div class="sign-name">{{ $generalsetting->nama_hrd ?? 'HRD Manager' }}</div>
                    <div class="sign-title">Head of HR & General Affair</div>
                </div>
                <div class="sign-col">
                    <div class="sign-role">Disetujui Oleh (Direktur)</div>
                    <div class="sign-name">Direktur Operasional</div>
                    <div class="sign-title">Chief Operating Officer</div>
                </div>
            </div>
        </div>

        <!-- Security & Audit Footer -->
        <div class="doc-security">
            <span>Sistem Presensi Biometrik & Geofencing GPS Enterprise &bull; Rekap Hak Cuti Tahunan Terkunci</span>
            <span>Dokumen Rahasia &bull; ID Audit: CUTI-{{ $tahun }}-{{ substr(sha1($tahun . $totalPegawai), 0, 8) }}</span>
        </div>

    </div>

</body>
</html>
