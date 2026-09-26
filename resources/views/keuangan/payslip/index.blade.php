@extends('layouts.app')
@section('titlepage', 'Slip Gaji Karyawan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item active">Slip Gaji</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Slip Gaji Karyawan (Payslips)</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($payslips->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Daftar arsip slip gaji resmi seluruh karyawan berdasarkan snapshot periode payroll.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-calendar-stats" style="font-size: 16px;"></i>
            <span>Periode Payroll</span>
        </a>
        <a href="{{ route('payslip.my_payslips') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-file-certificate" style="font-size: 16px;"></i>
            <span>Slip Gaji Saya</span>
        </a>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body p-3">
        <form action="{{ route('payslip.index') }}" method="GET" class="m-0">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                        icon="ti ti-search" placeholder="Cari nama karyawan / NIK..." hideLabel="true" />
                </div>
                <div class="col-12 col-md-3">
                    <select name="payroll_period_id" class="form-select" style="height: 38px; border-radius: 8px;">
                        <option value="">Semua Periode Payroll</option>
                        @foreach ($periods as $p)
                            <option value="{{ $p->id }}" {{ request('payroll_period_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->period_name }} ({{ $p->period_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select name="kode_dept" class="form-select" style="height: 38px; border-radius: 8px;">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1" style="height: 38px; border-radius: 8px; font-weight: 600;">
                        <i class="ti ti-search" style="font-size: 15px;"></i>
                        <span>Cari Data</span>
                    </button>
                    @if(request()->hasAny(['search', 'payroll_period_id', 'kode_dept']))
                        <a href="{{ route('payslip.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" style="height: 38px; width: 38px; border-radius: 8px; font-weight: 600;" title="Reset">
                            <i class="ti ti-refresh" style="font-size: 15px;"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th>PEGAWAI</th>
                    <th>DEPARTEMEN / CABANG</th>
                    <th>PERIODE</th>
                    <th class="text-end">GAJI BRUTO</th>
                    <th class="text-end">TOTAL POTONGAN</th>
                    <th class="text-end">TAKE HOME PAY</th>
                    <th class="text-center">STATUS</th>
                    <th class="text-end" style="width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payslips as $ps)
                    <tr>
                        <td>
                            <div>
                                <span class="fw-bold text-dark d-block">{{ $ps->karyawan?->nama_karyawan ?? '-' }}</span>
                                <small class="text-muted font-mono" style="font-size: 11px;">{{ $ps->nik }}</small>
                            </div>
                        </td>
                        <td>
                            <div>{{ $ps->karyawan?->dpt?->nama_dept ?? '-' }}</div>
                            <small class="text-muted">{{ $ps->karyawan?->cabang?->nama_cabang ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                {{ $ps->period?->period_code ?? '-' }}
                            </span>
                        </td>
                        <td class="text-end font-mono">
                            Rp {{ number_format($ps->gross_salary, 0, ',', '.') }}
                        </td>
                        <td class="text-end font-mono text-danger">
                            - Rp {{ number_format($ps->total_deductions, 0, ',', '.') }}
                        </td>
                        <td class="text-end font-mono fw-bold text-success">
                            Rp {{ number_format($ps->net_salary, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($ps->status === 'PAID')
                                <span class="badge bg-label-success">Terbayar</span>
                            @elseif($ps->status === 'VERIFIED')
                                <span class="badge bg-label-info">Terverifikasi</span>
                            @else
                                <span class="badge bg-label-warning">Terkalkulasi</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1.5">
                                <a href="{{ route('payslip.show', $ps->id) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Lihat Slip">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('payslip.print', $ps->id) }}" target="_blank" class="btn btn-sm btn-icon btn-outline-secondary" title="Cetak Slip">
                                    <i class="ti ti-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="ti ti-file-off fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Belum Ada Data Slip Gaji</h6>
                            <small class="text-muted">Slip gaji akan terbit setelah periode payroll difinalisasi.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($payslips->hasPages())
    <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
        <div class="card-body py-2.5 px-3">
            {{ $payslips->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
@endsection
