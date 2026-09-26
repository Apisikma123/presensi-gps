@extends('layouts.app')
@section('titlepage', 'Laporan & Analitik Universal HR')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Laporan & Analitik Universal HR</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Laporan & Analitik Universal HR</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                Executive Report
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Ringkasan eksekutif ketenagakerjaan, demografi, penggajian, dan kepatuhan tata kelola SDM.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('reports.export', request()->all()) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-download" style="font-size: 16px;"></i>
            <span>Ekspor CSV</span>
        </a>
        <a href="{{ route('reports.print', request()->all()) }}" target="_blank" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-printer" style="font-size: 16px;"></i>
            <span>Cetak Laporan</span>
        </a>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('reports.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 200px;">
                <select name="kode_cabang" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Kantor / Cabang</option>
                    @foreach($cabangs as $c)
                        <option value="{{ $c->kode_cabang }}" {{ $cabang == $c->kode_cabang ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow-1" style="min-width: 200px;">
                <select name="kode_dept" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Departemen</option>
                    @foreach($departemens as $d)
                        <option value="{{ $d->kode_dept }}" {{ $dept == $d->kode_dept ? 'selected' : '' }}>{{ $d->nama_dept }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-shrink-0" style="min-width: 140px;">
                <select name="year" class="form-select form-select-sm font-mono" style="border-radius: 8px;">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari Data</span>
                </button>
                @if($cabang || $dept || request('year'))
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- 1. KETENAGAKERJAAN & DEMOGRAFI METRICS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                        <i class="ti ti-users fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Karyawan Aktif</div>
                        <h4 class="mb-0 fw-bold font-mono text-dark">{{ number_format($headcount['active_headcount']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(74, 103, 65, 0.1); color: #4A6741;">
                        <i class="ti ti-user-plus fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Masuk ({{ $year }})</div>
                        <h4 class="mb-0 fw-bold font-mono text-success">+{{ number_format($turnover['new_hires']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(234, 84, 85, 0.1); color: #ea5455;">
                        <i class="ti ti-user-minus fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Keluar ({{ $year }})</div>
                        <h4 class="mb-0 fw-bold font-mono text-danger">-{{ number_format($turnover['exits']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(255, 159, 67, 0.12); color: #ff9f43;">
                        <i class="ti ti-chart-arrows fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Turnover Tahunan</div>
                        <h4 class="mb-0 fw-bold font-mono text-dark">{{ $turnover['turnover_rate_percent'] }}%</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. DISTRIBUSI DEPARTEMEN & CABANG --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); overflow: hidden;">
            <div class="card-header py-3 px-4 bg-transparent border-bottom">
                <h5 class="card-title fw-bold text-dark mb-0">Distribusi Karyawan per Departemen</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>DEPARTEMEN</th>
                            <th class="text-end" style="width: 100px;">JUMLAH</th>
                            <th class="text-end" style="width: 120px;">PERSENTASE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $tot = max(1, $headcount['active_headcount']); @endphp
                        @forelse($headcount['by_department'] as $deptRow)
                        <tr>
                            <td><span class="fw-bold text-dark" style="font-size: 13px;">{{ $deptRow['label'] }}</span></td>
                            <td class="text-end font-mono" style="font-size: 13px;">{{ $deptRow['count'] }}</td>
                            <td class="text-end font-mono text-muted" style="font-size: 12px;">{{ round(($deptRow['count'] / $tot) * 100, 1) }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); overflow: hidden;">
            <div class="card-header py-3 px-4 bg-transparent border-bottom">
                <h5 class="card-title fw-bold text-dark mb-0">Distribusi Karyawan per Outlet / Cabang</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>OUTLET / CABANG</th>
                            <th class="text-end" style="width: 100px;">JUMLAH</th>
                            <th class="text-end" style="width: 120px;">PERSENTASE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($headcount['by_branch'] as $branchRow)
                        <tr>
                            <td><span class="fw-bold text-dark" style="font-size: 13px;">{{ $branchRow['label'] }}</span></td>
                            <td class="text-end font-mono" style="font-size: 13px;">{{ $branchRow['count'] }}</td>
                            <td class="text-end font-mono text-muted" style="font-size: 12px;">{{ round(($branchRow['count'] / $tot) * 100, 1) }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 3. KOMPENSASI & REKAP OPERASIONAL --}}
@php
    $hasPayrollReport = module_enabled('payroll');
    $hasGovReport = module_enabled('training') || module_enabled('discipline') || module_enabled('asset') || module_enabled('loan');
@endphp
@if($hasPayrollReport || $hasGovReport)
<div class="row g-3 mb-4">
    @if($hasPayrollReport)
    <div class="{{ $hasGovReport ? 'col-md-6' : 'col-12' }}">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); overflow: hidden;">
            <div class="card-header py-3 px-4 bg-transparent border-bottom">
                <h5 class="card-title fw-bold text-dark mb-0">Kompensasi & Payroll (Periode {{ $month }}/{{ $year }})</h5>
            </div>
            <div class="card-body p-4">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Status Siklus Payroll</span>
                        <span class="badge bg-label-info font-mono">{{ $compensation['payroll_status'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Karyawan Diproses</span>
                        <span class="font-mono fw-bold text-dark">{{ $compensation['employees_processed'] }} Orang</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Total Gaji Bruto (Gross)</span>
                        <span class="font-mono fw-bold text-dark">Rp {{ number_format($compensation['total_gross'], 0, ',', '.') }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Total Potongan (PPh 21, BPJS, Kasbon)</span>
                        <span class="font-mono text-danger">- Rp {{ number_format($compensation['total_deductions'], 0, ',', '.') }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 fw-bold border-top">
                        <span class="text-dark">Total Gaji Bersih (THP Ditransfer)</span>
                        <span class="font-mono fs-4 text-success">Rp {{ number_format($compensation['total_net_thp'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($hasGovReport)
    <div class="{{ $hasPayrollReport ? 'col-md-6' : 'col-12' }}">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); overflow: hidden;">
            <div class="card-header py-3 px-4 bg-transparent border-bottom">
                <h5 class="card-title fw-bold text-dark mb-0">Ringkasan Tata Kelola & Fasilitas SDM</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @if(module_enabled('training'))
                    <div class="col-6">
                        <div class="p-3 rounded-2 text-center" style="background: #FAF9F8; border: 1px solid #F1F5F9;">
                            <div class="text-muted small fw-medium mb-1">Total Jam Pelatihan</div>
                            <div class="fs-4 fw-bold font-mono text-primary">{{ $governance['total_training_hours'] }} Jam</div>
                            <div class="small text-muted font-mono" style="font-size: 11px;">{{ $governance['total_trainings_held'] }} Sesi Terlaksana</div>
                        </div>
                    </div>
                    @endif

                    @if(module_enabled('discipline'))
                    <div class="col-6">
                        <div class="p-3 rounded-2 text-center" style="background: #FAF9F8; border: 1px solid #F1F5F9;">
                            <div class="text-muted small fw-medium mb-1">Surat Peringatan Aktif</div>
                            <div class="fs-4 fw-bold font-mono text-warning">{{ $governance['active_warnings'] }} SP</div>
                            <div class="small text-muted font-mono" style="font-size: 11px;">Masa Berlaku 6 Bulan</div>
                        </div>
                    </div>
                    @endif

                    @if(module_enabled('asset'))
                    <div class="col-6">
                        <div class="p-3 rounded-2 text-center" style="background: #FAF9F8; border: 1px solid #F1F5F9;">
                            <div class="text-muted small fw-medium mb-1">Aset Dipinjamkan</div>
                            <div class="fs-4 fw-bold font-mono text-dark">{{ $governance['assigned_assets'] }} Unit</div>
                            <div class="small text-muted font-mono" style="font-size: 11px;">Inventaris Terdaftar</div>
                        </div>
                    </div>
                    @endif

                    @if(module_enabled('loan'))
                    <div class="col-6">
                        <div class="p-3 rounded-2 text-center" style="background: #FAF9F8; border: 1px solid #F1F5F9;">
                            <div class="text-muted small fw-medium mb-1">Piutang Kasbon Karyawan</div>
                            <div class="fs-4 fw-bold font-mono text-dark">Rp {{ number_format($compensation['loan_receivable_balance'], 0, ',', '.') }}</div>
                            <div class="small text-muted font-mono" style="font-size: 11px;">Cicilan Aktif Berjalan</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endif
@endsection
