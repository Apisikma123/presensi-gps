@php
    $theme = \App\Services\ThemeResolver::resolve();
    $primaryColor = $theme['primary'] ?? '#3C2A21';
    $secondaryColor = $theme['secondary'] ?? '#634832';
    $primaryContrast = $theme['primary_contrast'] ?? '#FFFFFF';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Exit Clearance & Hak Akhir - {{ $resignation->nik }}</title>
    <style>
        :root {
            --theme-color-1: {{ $primaryColor }};
            --theme-color-2: {{ $secondaryColor }};
            --theme-primary-contrast: {{ $primaryContrast }};
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #0F172A;
            background-color: #fff;
            padding: 30px;
            font-size: 11pt;
            line-height: 1.4;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            border-bottom: 2px solid var(--theme-color-1, #3C2A21);
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .company-title {
            font-size: 16pt;
            font-weight: 800;
            color: var(--theme-color-1, #3C2A21);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .company-subtitle {
            font-size: 9pt;
            color: #555;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title h2 {
            font-size: 14pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: underline;
        }
        .doc-title p {
            font-size: 9pt;
            color: #666;
            margin-top: 3px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 6px;
            font-size: 10pt;
            vertical-align: top;
        }
        .section-heading {
            font-size: 11pt;
            font-weight: 700;
            text-transform: uppercase;
            background-color: #f7f6f5;
            padding: 6px 10px;
            border-left: 3px solid var(--theme-color-1, #3C2A21);
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #dcd7d2;
            padding: 6px 8px;
            font-size: 9.5pt;
        }
        .data-table th {
            background-color: #faf9f8;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 8.5pt;
        }
        .signatures {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 22%;
            text-align: center;
            font-size: 9pt;
        }
        .signature-space {
            height: 65px;
        }
        .signature-name {
            font-weight: 700;
            border-bottom: 1px solid #333;
            padding-bottom: 2px;
            margin-bottom: 3px;
        }
        .print-actions {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-print {
            background-color: var(--theme-color-1, #3C2A21);
            color: var(--theme-primary-contrast, #FFFFFF);
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
        }
        @media print {
            .print-actions {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="print-actions">
            <button onclick="window.print()" class="btn-print">Cetak Dokumen Clearance</button>
        </div>

        <div class="header">
            <div>
                <div class="company-title">{{ $company->company_name ?? 'PRESENCE UNIVERSAL HR' }}</div>
                <div class="company-subtitle">{{ $company->address ?? 'Human Resource & Operations Department' }}</div>
            </div>
            <div style="text-align: right; font-size: 9pt; color: #666;">
                Tanggal Cetak: {{ date('d/m/Y') }}
            </div>
        </div>

        <div class="doc-title">
            <h2>SURAT EXIT CLEARANCE & PENETAPAN HAK AKHIR</h2>
            <p>Nomor Dokumen: CLR/{{ $resignation->tanggal_keluar ? \Carbon\Carbon::parse($resignation->tanggal_keluar)->format('Ym') : date('Ym') }}/{{ str_pad($resignation->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 20%; font-weight: 600;">Nama Karyawan</td>
                <td style="width: 30%;">: {{ $resignation->karyawan->nama_karyawan ?? '-' }}</td>
                <td style="width: 20%; font-weight: 600;">Departemen</td>
                <td style="width: 30%;">: {{ $resignation->karyawan->departemen->nama_dept ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Nomor Induk (NIK)</td>
                <td>: {{ $resignation->nik }}</td>
                <td style="font-weight: 600;">Cabang / Penempatan</td>
                <td>: {{ $resignation->karyawan->cabang->nama_cabang ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Jabatan Terakhir</td>
                <td>: {{ $resignation->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                <td style="font-weight: 600;">Kategori Keluar</td>
                <td>: {{ $resignation->kategori_keluar_label }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Tanggal Masuk</td>
                <td>: {{ $resignation->karyawan && $resignation->karyawan->tanggal_masuk ? \Carbon\Carbon::parse($resignation->karyawan->tanggal_masuk)->format('d M Y') : '-' }}</td>
                <td style="font-weight: 600;">Tanggal Efektif Keluar</td>
                <td>: {{ $resignation->tanggal_keluar ? \Carbon\Carbon::parse($resignation->tanggal_keluar)->format('d M Y') : '-' }}</td>
            </tr>
        </table>

        {{-- Section 1: Checklist Clearance Antar Departemen --}}
        <div class="section-heading">I. Verifikasi Clearance Serah Terima Aset & Kewajiban</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Departemen</th>
                    <th style="width: 45%;">Item Pemeriksaan / Serah Terima</th>
                    <th style="width: 25%;">Status Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resignation->clearances as $idx => $c)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $c->department_label }}</strong></td>
                    <td>{{ $c->item_name }}</td>
                    <td>
                        @if($c->is_cleared)
                            <span style="color: #2b8a3e; font-weight: 700;">[LUNAS / CLEARED]</span>
                            <div style="font-size: 8pt; color: #666;">
                                Oleh: {{ $c->clearedByUser->name ?? 'Admin' }} ({{ $c->cleared_at ? $c->cleared_at->format('d/m/y') : '' }})
                            </div>
                        @else
                            <span style="color: #c92a2a; font-weight: 600;">[PENDING]</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Section 2: Rincian Hak Akhir & Kompensasi --}}
        <div class="section-heading">II. Rincian Hak Akhir & Kompensasi (PP 35/2021)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Komponen Hak Akhir</th>
                    <th style="width: 30%; text-align: right;">Jumlah (Rupiah)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Uang Pesangon (Severance Pay)</td>
                    <td style="text-align: right; font-family: monospace;">Rp {{ number_format($resignation->severance_pay, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Uang Penghargaan Masa Kerja (UPMK)</td>
                    <td style="text-align: right; font-family: monospace;">Rp {{ number_format($resignation->service_pay, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Uang Penggantian Hak (UPH)</td>
                    <td style="text-align: right; font-family: monospace;">Rp {{ number_format($resignation->compensation_pay, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Gaji Terakhir / Prorata Hari Kerja</td>
                    <td style="text-align: right; font-family: monospace;">Rp {{ number_format($resignation->final_salary_pay, 0, ',', '.') }}</td>
                </tr>
                <tr style="color: #c92a2a;">
                    <td>Potongan Saldo Pinjaman / Kasbon / Lainnya</td>
                    <td style="text-align: right; font-family: monospace;">- Rp {{ number_format($resignation->deductions_pay, 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #faf9f8; font-weight: 700; font-size: 11pt;">
                    <td>TOTAL HAK AKHIR YANG DIBAYARKAN (NET)</td>
                    <td style="text-align: right; font-family: monospace; color: #2b8a3e;">
                        Rp {{ number_format($resignation->total_settlement, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="font-size: 8.5pt; color: #555; margin-top: 15px; line-height: 1.4;">
            Dengan ditandatanganinya dokumen ini, kedua belah pihak menyatakan bahwa seluruh hak dan kewajiban ketenagakerjaan telah dipenuhi dengan baik tanpa adanya tuntutan hukum di kemudian hari.
        </div>

        {{-- Signatures --}}
        <div class="signatures">
            <div class="signature-box">
                <div>Karyawan Bersangkutan,</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $resignation->karyawan->nama_karyawan ?? 'Karyawan' }}</div>
                <div>Tanggal: {{ $resignation->tanggal_keluar ? \Carbon\Carbon::parse($resignation->tanggal_keluar)->format('d/m/Y') : date('d/m/Y') }}</div>
            </div>

            <div class="signature-box">
                <div>IT & Aset Officer,</div>
                <div class="signature-space"></div>
                <div class="signature-name">(..............................)</div>
                <div>Departemen IT</div>
            </div>

            <div class="signature-box">
                <div>Finance & Payroll,</div>
                <div class="signature-space"></div>
                <div class="signature-name">(..............................)</div>
                <div>Departemen Keuangan</div>
            </div>

            <div class="signature-box">
                <div>HR & Operations Head,</div>
                <div class="signature-space"></div>
                <div class="signature-name">(..............................)</div>
                <div>Human Resources</div>
            </div>
        </div>
    </div>
</body>
</html>
