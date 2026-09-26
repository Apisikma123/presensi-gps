<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Eksekutif Ketenagakerjaan & HR — {{ date('Y') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #0F172A;
            background: #fff;
            margin: 0;
            padding: 24px;
            font-size: 13px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid {{ $theme['primary'] ?? '#0F172A' }};
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .subtitle {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #e8e4df;
            padding-bottom: 4px;
            margin-top: 24px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th, td {
            border: 1px solid #e8e4df;
            padding: 6px 10px;
            text-align: left;
        }
        th {
            background-color: #faf9f8;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        .text-end { text-align: right; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid {{ $theme['primary'] ?? '#0F172A' }};
            margin-top: 60px;
            margin-bottom: 4px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 16px; cursor: pointer;">Cetak Laporan</button>
        <button onclick="window.close()" style="padding: 6px 16px; cursor: pointer;">Tutup</button>
    </div>

    <div class="header">
        <div class="title">{{ company_setting('legal_name') ?: company_setting('company_name', 'PRESENCE HR SYSTEM') }}</div>
        @if(company_setting('legal_name') && company_setting('company_name') && company_setting('legal_name') !== company_setting('company_name'))
            <div style="font-size: 13px; font-weight: 600; color: #555;">{{ company_setting('company_name') }}</div>
        @endif
        @php
            $subDetails = array_filter([
                company_setting('npwp') ? 'NPWP: ' . company_setting('npwp') : null,
                company_setting('nib') ? 'NIB: ' . company_setting('nib') : null,
                company_setting('phone') ? 'Telp: ' . company_setting('phone') : null,
                company_setting('email') ? 'Email: ' . company_setting('email') : null,
            ]);
        @endphp
        @if(!empty($subDetails) || company_setting('address'))
            <div style="font-size: 11px; color: #666; margin-top: 2px;">
                @if(company_setting('address')) {{ company_setting('address') }} @if(!empty($subDetails)) &bull; @endif @endif
                {{ implode(' &bull; ', $subDetails) }}
            </div>
        @endif
        <div class="subtitle" style="margin-top: 6px; font-weight: 500;">Laporan Eksekutif Ketenagakerjaan, Kompensasi & Tata Kelola SDM</div>
    </div>

    <div class="meta-info">
        <div><strong>Periode Pelaporan:</strong> Tahun {{ $year }} (Bulan {{ $month }})</div>
        <div><strong>Dicetak Pada:</strong> {{ date('d F Y, H:i') }} WIB</div>
    </div>

    <div class="section-title">1. Ringkasan Ketenagakerjaan & Turnover</div>
    <table>
        <tr>
            <th>Total Karyawan Terdaftar</th>
            <td class="font-mono text-end">{{ number_format($headcount['total_registered']) }}</td>
            <th>Karyawan Aktif</th>
            <td class="font-mono text-end">{{ number_format($headcount['active_headcount']) }}</td>
        </tr>
        <tr>
            <th>Karyawan Masuk ({{ $year }})</th>
            <td class="font-mono text-end">+{{ number_format($turnover['new_hires']) }}</td>
            <th>Karyawan Keluar ({{ $year }})</th>
            <td class="font-mono text-end">-{{ number_format($turnover['exits']) }}</td>
        </tr>
        <tr>
            <th>Pertumbuhan Bersih (Net Growth)</th>
            <td class="font-mono text-end">{{ number_format($turnover['net_growth']) }}</td>
            <th>Turnover Rate</th>
            <td class="font-mono text-end">{{ $turnover['turnover_rate_percent'] }}%</td>
        </tr>
    </table>

    <div class="grid">
        <div>
            <div class="section-title">2. Distribusi per Departemen</div>
            <table>
                <thead>
                    <tr>
                        <th>Departemen</th>
                        <th class="text-end">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($headcount['by_department'] as $d)
                    <tr>
                        <td>{{ $d['label'] }}</td>
                        <td class="text-end font-mono">{{ $d['count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <div class="section-title">3. Distribusi per Cabang / Lokasi</div>
            <table>
                <thead>
                    <tr>
                        <th>Cabang</th>
                        <th class="text-end">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($headcount['by_branch'] as $b)
                    <tr>
                        <td>{{ $b['label'] }}</td>
                        <td class="text-end font-mono">{{ $b['count'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-title">4. Kompensasi & Tata Kelola Operasional</div>
    <table>
        <tr>
            <th>Karyawan Diproses Payroll</th>
            <td class="font-mono text-end">{{ $compensation['employees_processed'] }} Orang</td>
            <th>Total Gaji Bruto (Gross)</th>
            <td class="font-mono text-end">Rp {{ number_format($compensation['total_gross'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Potongan (PPh 21 / BPJS / Kasbon)</th>
            <td class="font-mono text-end">Rp {{ number_format($compensation['total_deductions'], 0, ',', '.') }}</td>
            <th>Total Take Home Pay (THP)</th>
            <td class="font-mono text-end"><strong>Rp {{ number_format($compensation['total_net_thp'], 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <th>Jam Pelatihan Terselesaikan</th>
            <td class="font-mono text-end">{{ $governance['total_training_hours'] }} Jam</td>
            <th>Surat Peringatan (SP) Aktif</th>
            <td class="font-mono text-end">{{ $governance['active_warnings'] }} SP</td>
        </tr>
    </table>

    <div class="signatures">
        <div class="signature-box">
            <div>Dibuat Oleh,</div>
            <div class="signature-line"></div>
            <div><strong>HR & GA Specialist</strong></div>
        </div>
        <div class="signature-box">
            <div>Diperiksa Oleh,</div>
            <div class="signature-line"></div>
            <div><strong>HR Manager</strong></div>
        </div>
        <div class="signature-box">
            <div>Disetujui Oleh,</div>
            <div class="signature-line"></div>
            <div><strong>Direktur Operasional</strong></div>
        </div>
    </div>
</body>
</html>
