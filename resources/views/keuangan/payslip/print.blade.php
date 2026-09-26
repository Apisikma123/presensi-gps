<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip_Gaji_{{ $payslip['employee']['nik'] }}_{{ $payslip['period']['code'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #25160e;
            background: #fff;
            padding: 24px;
            font-size: 12px;
            line-height: 1.5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #d6cebe;
            padding: 28px;
            border-radius: 8px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #25160e;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .company-title {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #25160e;
        }
        .badge-payslip {
            background: #25160e;
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            background: #faf9f8;
            border: 1px solid #e5ded4;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 18px;
        }
        .meta-row {
            display: flex;
            margin-bottom: 4px;
        }
        .meta-label {
            width: 120px;
            color: #78716c;
        }
        .meta-value {
            font-weight: 600;
            color: #25160e;
        }
        .breakdown-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .section-box {
            border: 1px solid #e5ded4;
            border-radius: 6px;
            padding: 14px;
            background: #faf9f8;
        }
        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 12px;
            padding-bottom: 6px;
        }
        .title-earn {
            color: #15803d;
            border-bottom: 1px solid #bbf7d0;
        }
        .title-ded {
            color: #ba1a1a;
            border-bottom: 1px solid #fecdd3;
        }
        .row-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 12px;
        }
        .row-total {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #d6cebe;
            font-weight: 700;
        }
        .mono {
            font-family: 'JetBrains Mono', monospace;
        }
        .thp-banner {
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            padding: 14px 18px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .thp-title {
            font-size: 11px;
            font-weight: 700;
            color: #166534;
            letter-spacing: 0.5px;
        }
        .thp-words {
            font-style: italic;
            font-size: 11.5px;
            color: #15803d;
            margin-top: 2px;
        }
        .thp-amount {
            font-family: 'JetBrains Mono', monospace;
            font-size: 20px;
            font-weight: 700;
            color: #15803d;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid #e5ded4;
        }
        .sig-box {
            width: 200px;
        }
        .sig-space {
            height: 50px;
        }
        .no-print {
            margin-bottom: 16px;
            text-align: right;
        }
        .btn-print {
            background: #25160e;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        @media print {
            body {
                padding: 0;
            }
            .container {
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Cetak Slip Gaji</button>
    </div>

    <div class="container">
        <div class="header">
            <div>
                <div class="company-title">{{ $payslip['company']['name'] }}</div>
                <div style="color: #78716c; font-size: 11px;">{{ $payslip['company']['address'] }}</div>
                <div style="color: #78716c; font-size: 11px;">Telp: {{ $payslip['company']['phone'] }}</div>
            </div>
            <div style="text-align: right;">
                <span class="badge-payslip">SLIP GAJI KARYAWAN</span>
                <div class="mono" style="font-size: 11px; margin-top: 6px; color: #78716c;">
                    Periode: {{ $payslip['period']['code'] }}
                </div>
            </div>
        </div>

        <div class="meta-grid">
            <div>
                <div class="meta-row">
                    <span class="meta-label">NIK:</span>
                    <span class="meta-value mono">{{ $payslip['employee']['nik'] }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Nama:</span>
                    <span class="meta-value">{{ $payslip['employee']['name'] }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Departemen:</span>
                    <span class="meta-value">{{ $payslip['employee']['department'] }}</span>
                </div>
            </div>
            <div>
                <div class="meta-row">
                    <span class="meta-label">Jabatan:</span>
                    <span class="meta-value">{{ $payslip['employee']['job_title'] }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Status PTKP:</span>
                    <span class="meta-value mono">{{ $payslip['employee']['ptkp_status'] }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Rekening:</span>
                    <span class="meta-value mono">{{ $payslip['employee']['bank_name'] }} - {{ $payslip['employee']['bank_account'] }}</span>
                </div>
            </div>
        </div>

        <div class="breakdown-grid">
            <div class="section-box">
                <div class="section-title title-earn">A. PENERIMAAN</div>
                <div class="row-item">
                    <span>Gaji Pokok</span>
                    <span class="mono">Rp {{ number_format($payslip['basic_salary'], 0, ',', '.') }}</span>
                </div>
                @foreach($payslip['earnings'] as $earn)
                    <div class="row-item">
                        <span>{{ $earn['name'] ?? $earn['component_name'] ?? 'Tunjangan' }}</span>
                        <span class="mono">Rp {{ number_format($earn['amount'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                @endforeach
                @if(($payslip['attendance']['overtime_pay'] ?? 0) > 0)
                    <div class="row-item">
                        <span>Upah Lembur</span>
                        <span class="mono">Rp {{ number_format($payslip['attendance']['overtime_pay'], 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="row-total" style="color: #25160e;">
                    <span>Total Penerimaan Bruto</span>
                    <span class="mono">Rp {{ number_format($payslip['gross_salary'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="section-box">
                <div class="section-title title-ded">B. POTONGAN</div>
                @forelse($payslip['deductions'] as $ded)
                    <div class="row-item">
                        <span>{{ $ded['name'] ?? $ded['component_name'] ?? 'Potongan' }}</span>
                        <span class="mono" style="color: #ba1a1a;">- Rp {{ number_format($ded['amount'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <div style="color: #78716c; text-align: center; padding: 12px 0;">Tidak ada potongan</div>
                @endforelse
                <div class="row-total" style="color: #ba1a1a;">
                    <span>Total Potongan</span>
                    <span class="mono">- Rp {{ number_format($payslip['total_deductions'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="thp-banner">
            <div>
                <div class="thp-title">GAJI BERSIH DITERIMA (TAKE HOME PAY)</div>
                <div class="thp-words">"{{ $payslip['terbilang'] }}"</div>
            </div>
            <div class="thp-amount">
                Rp {{ number_format($payslip['net_salary'], 0, ',', '.') }}
            </div>
        </div>

        <div class="signatures">
            <div class="sig-box">
                <div style="color: #78716c;">Tanda Tangan Penerima,</div>
                <div class="sig-space"></div>
                <div style="font-weight: 700;">{{ $payslip['employee']['name'] }}</div>
            </div>
            <div class="sig-box" style="text-align: right;">
                <div style="color: #78716c;">Departemen Keuangan & HRD,</div>
                <div class="sig-space"></div>
                <div style="font-weight: 700;">{{ $payslip['company']['name'] }}</div>
                <div class="mono" style="font-size: 9px; color: #78716c; margin-top: 4px;">VERIF: {{ $payslip['verification_code'] }}</div>
            </div>
        </div>
    </div>
</body>
</html>
