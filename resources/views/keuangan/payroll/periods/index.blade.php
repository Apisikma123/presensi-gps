@extends('layouts.app')
@section('titlepage', 'Penggajian & Periode Payroll')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item active">Periode Penggajian</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Penggajian & Periode Payroll</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($stats['total_periods']) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Manajemen siklus bulanan, perhitungan upah, lembur terintegrasi, dan snapshot THP.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        @can('salary_component.index')
            <a href="{{ route('salary_components.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 500; padding: 0 14px;">
                <i class="ti ti-adjustments-horizontal"></i>
                <span>Komponen Gaji</span>
            </a>
        @endcan
        @can('employee_salary.index')
            <a href="{{ route('employee_salary.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 500; padding: 0 14px;">
                <i class="ti ti-users"></i>
                <span>Struktur Gaji</span>
            </a>
        @endcan
        @can('payroll.create')
            <a href="{{ route('payroll.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Buka Periode Baru</span>
            </a>
        @endcan
    </div>
</div>

{{-- Alerts --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert" style="border-radius: 8px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #15803d;">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <span style="font-size: 13.5px;">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible mb-4" role="alert" style="border-radius: 8px; border: 1px solid #fecdd3; background: #fef2f2; color: #ba1a1a;">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle fs-5 me-2"></i>
            <span style="font-size: 13.5px;">{{ session('error') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Metric Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium text-uppercase">Total Periode {{ $year }}</span>
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                        style="width: 32px; height: 32px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                        <i class="ti ti-calendar fs-6"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold font-mono text-dark">
                    {{ number_format($stats['total_periods']) }}
                </div>
                <span class="text-muted small">Siklus penggajian dibuka</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium text-uppercase">Total THP Final</span>
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                        style="width: 32px; height: 32px; background: rgba(74, 103, 65, 0.1); color: #4A6741;">
                        <i class="ti ti-cash fs-6"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold font-mono text-success">
                    Rp {{ number_format($stats['total_thp_year'], 0, ',', '.') }}
                </div>
                <span class="text-muted small">Gaji bersih tahun {{ $year }}</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium text-uppercase">Karyawan Terbayar</span>
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                        style="width: 32px; height: 32px; background: rgba(115, 103, 240, 0.1); color: #7367f0;">
                        <i class="ti ti-user-check fs-6"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold font-mono text-dark">
                    {{ number_format($stats['total_employees_paid']) }}
                </div>
                <span class="text-muted small">Akumulasi penerima THP</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium text-uppercase">Menunggu Review</span>
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                        style="width: 32px; height: 32px; background: rgba(255, 159, 67, 0.12); color: #ff9f43;">
                        <i class="ti ti-clock-pause fs-6"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold font-mono text-warning">
                    {{ number_format($stats['pending_review']) }}
                </div>
                <span class="text-muted small">Draft / kalkulasi berjalan</span>
            </div>
        </div>
    </div>
</div>

{{-- Year Filter Toolbar --}}
<div class="card admin-filter-toolbar mb-3">
    <form method="GET" action="{{ route('payroll.index') }}" class="m-0">
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small fw-medium">Tahun Periode:</span>
            <div style="width: 140px;">
                <select name="year" class="form-select form-select-sm font-mono" onchange="this.form.submit()">
                    @for ($y = date('Y') + 1; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </form>
</div>

{{-- Payroll Periods Table --}}
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead>
                <tr>
                    <th>PERIODE BULAN</th>
                    <th>RENTANG CUTOFF PRESENSI</th>
                    <th>TANGGAL BAYAR</th>
                    <th>KARYAWAN</th>
                    <th>TOTAL THP (NETTO)</th>
                    <th>STATUS</th>
                    <th class="text-end" style="width: 160px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($periods as $period)
                    <tr>
                        <td>
                            <a href="{{ route('payroll.show', $period->id) }}" class="fw-bold text-dark d-block" style="font-size: 13.5px;">
                                {{ $period->formatted_period }}
                            </a>
                            <div class="text-muted font-mono" style="font-size: 11px;">
                                Tahun {{ $period->period_year }} &bull; Bulan {{ str_pad($period->period_month, 2, '0', STR_PAD_LEFT) }}
                            </div>
                        </td>
                        <td>
                            <span class="font-mono text-muted" style="font-size: 12px;">
                                {{ $period->cutoff_start->format('d/m/Y') }} &ndash; {{ $period->cutoff_end->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-medium text-dark" style="font-size: 12.5px;">
                                {{ $period->payment_date->translatedFormat('d F Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                {{ $period->employee_count }} orang
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold font-mono text-success" style="font-size: 13.5px;">
                                Rp {{ number_format($period->total_net, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            {!! $period->status_badge_html !!}
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                <a href="{{ route('payroll.show', $period->id) }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                                    <i class="ti ti-calculator"></i>
                                    <span>Proses</span>
                                </a>

                                @if ($period->status !== 'FINALIZED')
                                    @can('payroll.delete')
                                        <form action="{{ route('payroll.delete', $period->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm" data-label="Periode Payroll {{ $period->formatted_period }}" title="Hapus" style="padding: 4px 8px;">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ti ti-inbox fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada periode payroll yang dibuka untuk tahun {{ $year }}.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
