@extends('layouts.app')
@section('titlepage', 'Slip Gaji - ' . $payslip['employee']['name'])

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payslip.index') }}">Slip Gaji</a></li>
    <li class="breadcrumb-item active">{{ $payslip['employee']['name'] }}</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Slip Gaji &bull; {{ $payslip['employee']['name'] }}</h4>
            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                {{ $payslip['period']['code'] }}
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0 font-mono" style="font-size: 12px;">
            NIK: {{ $payslip['employee']['nik'] }} &bull; {{ $payslip['employee']['department'] }}
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('payslip.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <a href="{{ route('payslip.print', $detail->id) }}" target="_blank" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-printer"></i>
            <span>Cetak / Simpan PDF</span>
        </a>
    </div>
</div>

<!-- Payslip Document Preview Card -->
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
                <div class="card-body p-4 p-md-5">
                    {{-- Header Company --}}
                    <div class="d-flex justify-content-between align-items-start pb-4 mb-4" style="border-bottom: 2px solid #25160e;">
                        <div>
                            <h4 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif; color: #25160e; letter-spacing: -0.02em;">
                                {{ $payslip['company']['name'] }}
                            </h4>
                            <p class="text-muted mb-0" style="font-size: 12.5px;">{{ $payslip['company']['address'] }}</p>
                            <small class="text-muted" style="font-size: 11.5px;">Telp: {{ $payslip['company']['phone'] }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge px-3 py-1.5" style="background: #25160e; color: #fff; font-size: 12px; letter-spacing: 0.5px;">
                                SLIP GAJI BULANAN
                            </span>
                            <div class="mt-2" style="font-family: 'JetBrains Mono'; font-size: 12.5px; color: #78716c;">
                                Periode: {{ $payslip['period']['start_date'] }} - {{ $payslip['period']['end_date'] }}
                            </div>
                        </div>
                    </div>

                    {{-- Employee & Position Metadata --}}
                    <div class="row g-3 mb-4 p-3 rounded" style="background: #faf9f8; border: 1px solid #e5ded4; font-size: 13px;">
                        <div class="col-sm-6">
                            <div class="row mb-1">
                                <span class="col-4 text-muted">Nomor Induk (NIK):</span>
                                <span class="col-8 fw-semibold" style="font-family: 'JetBrains Mono'; color: #0F172A;">{{ $payslip['employee']['nik'] }}</span>
                            </div>
                            <div class="row mb-1">
                                <span class="col-4 text-muted">Nama Lengkap:</span>
                                <span class="col-8 fw-bold" style="color: #0F172A;">{{ $payslip['employee']['name'] }}</span>
                            </div>
                            <div class="row">
                                <span class="col-4 text-muted">Departemen:</span>
                                <span class="col-8">{{ $payslip['employee']['department'] }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row mb-1">
                                <span class="col-4 text-muted">Jabatan:</span>
                                <span class="col-8 fw-semibold" style="color: #0F172A;">{{ $payslip['employee']['job_title'] }}</span>
                            </div>
                            <div class="row mb-1">
                                <span class="col-4 text-muted">Status PTKP:</span>
                                <span class="col-8" style="font-family: 'JetBrains Mono';">{{ $payslip['employee']['ptkp_status'] }}</span>
                            </div>
                            <div class="row">
                                <span class="col-4 text-muted">Rekening Transfer:</span>
                                <span class="col-8" style="font-family: 'JetBrains Mono';">{{ $payslip['employee']['bank_name'] }} - {{ $payslip['employee']['bank_account'] }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Attendance Recap Strip --}}
                    @if(!empty($payslip['attendance']))
                        <div class="d-flex flex-wrap gap-2 justify-content-between p-2.5 mb-4 rounded" style="background: #f8f6f0; border: 1px dashed #d6cebe; font-size: 12px;">
                            <span><strong>Hari Kerja:</strong> {{ $payslip['attendance']['work_days'] ?? 0 }} hari</span>
                            <span><strong>Kehadiran:</strong> {{ $payslip['attendance']['present_days'] ?? 0 }} hari</span>
                            <span><strong>Terlambat:</strong> {{ $payslip['attendance']['late_count'] ?? 0 }} kali</span>
                            <span><strong>Cuti/Izin:</strong> {{ ($payslip['attendance']['leave_days'] ?? 0) + ($payslip['attendance']['permit_days'] ?? 0) }} hari</span>
                            <span><strong>Jam Lembur:</strong> {{ number_format($payslip['attendance']['overtime_hours'] ?? 0, 1) }} jam</span>
                        </div>
                    @endif

                    {{-- 2-Column Earnings & Deductions --}}
                    <div class="row g-4 mb-4">
                        {{-- Left: Earnings --}}
                        <div class="col-md-6">
                            <div class="p-3 rounded h-100" style="border: 1px solid #e5ded4; background: #faf9f8;">
                                <h6 class="fw-bold pb-2 mb-3" style="font-family: 'Outfit', sans-serif; color: #15803d; border-bottom: 1px solid #bbf7d0;">
                                    A. PENERIMAAN (EARNINGS)
                                </h6>
                                <div class="d-flex justify-content-between mb-2" style="font-size: 13px;">
                                    <span>Gaji Pokok</span>
                                    <span style="font-family: 'JetBrains Mono';">Rp {{ number_format($payslip['basic_salary'], 0, ',', '.') }}</span>
                                </div>
                                @foreach($payslip['earnings'] as $earn)
                                    <div class="d-flex justify-content-between mb-2" style="font-size: 13px;">
                                        <span>{{ $earn['name'] ?? $earn['component_name'] ?? 'Tunjangan' }}</span>
                                        <span style="font-family: 'JetBrains Mono';">Rp {{ number_format($earn['amount'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                                @if(($payslip['attendance']['overtime_pay'] ?? 0) > 0)
                                    <div class="d-flex justify-content-between mb-2" style="font-size: 13px;">
                                        <span>Upah Lembur</span>
                                        <span style="font-family: 'JetBrains Mono';">Rp {{ number_format($payslip['attendance']['overtime_pay'], 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between pt-2 mt-3 fw-bold" style="border-top: 1px dashed #d6cebe; font-size: 13.5px; color: #0F172A;">
                                    <span>Total Penerimaan Bruto</span>
                                    <span style="font-family: 'JetBrains Mono';">Rp {{ number_format($payslip['gross_salary'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Right: Deductions --}}
                        <div class="col-md-6">
                            <div class="p-3 rounded h-100" style="border: 1px solid #e5ded4; background: #faf9f8;">
                                <h6 class="fw-bold pb-2 mb-3" style="font-family: 'Outfit', sans-serif; color: #ba1a1a; border-bottom: 1px solid #fecdd3;">
                                    B. POTONGAN (DEDUCTIONS)
                                </h6>
                                @forelse($payslip['deductions'] as $ded)
                                    <div class="d-flex justify-content-between mb-2" style="font-size: 13px;">
                                        <span>{{ $ded['name'] ?? $ded['component_name'] ?? 'Potongan' }}</span>
                                        <span style="font-family: 'JetBrains Mono'; color: #ba1a1a;">- Rp {{ number_format($ded['amount'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                @empty
                                    <div class="text-muted text-center py-3" style="font-size: 12.5px;">Tidak ada potongan</div>
                                @endforelse
                                <div class="d-flex justify-content-between pt-2 mt-3 fw-bold" style="border-top: 1px dashed #d6cebe; font-size: 13.5px; color: #ba1a1a;">
                                    <span>Total Potongan</span>
                                    <span style="font-family: 'JetBrains Mono';">- Rp {{ number_format($payslip['total_deductions'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Take Home Pay Banner --}}
                    <div class="p-3.5 rounded mb-4" style="background: #f0fdf4; border: 1.5px solid #86efac;">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                            <div>
                                <span class="text-uppercase fw-bold" style="font-size: 12px; color: #166534; letter-spacing: 0.5px;">
                                    GAJI BERSIH DITERIMA (TAKE HOME PAY)
                                </span>
                                <div class="fst-italic mt-0.5" style="font-size: 12.5px; color: #15803d;">
                                    "{{ $payslip['terbilang'] }}"
                                </div>
                            </div>
                            <div class="mt-2 mt-sm-0 text-sm-end">
                                <span class="h3 fw-bold mb-0" style="font-family: 'JetBrains Mono'; color: #15803d;">
                                    Rp {{ number_format($payslip['net_salary'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer & Verification Signatures --}}
                    <div class="row pt-4" style="border-top: 1px solid #e5ded4; font-size: 12px;">
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Tanda Tangan Penerima,</span>
                            <div style="height: 60px;"></div>
                            <span class="fw-bold" style="color: #0F172A;">{{ $payslip['employee']['name'] }}</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted d-block mb-1">Departemen Keuangan & HRD,</span>
                            <div style="height: 60px;"></div>
                            <span class="fw-bold" style="color: #0F172A;">{{ $payslip['company']['name'] }}</span>
                            <small class="d-block text-muted font-monospace mt-1" style="font-size: 10px;">Token: {{ $payslip['verification_code'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
