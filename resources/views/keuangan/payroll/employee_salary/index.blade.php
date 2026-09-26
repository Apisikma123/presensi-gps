@extends('layouts.app')
@section('titlepage', 'Struktur Gaji Karyawan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item active">Struktur Gaji Karyawan</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Penugasan Struktur Gaji Karyawan</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($employees->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Alokasi upah pokok dan tunjangan tetap per individu karyawan.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 500; padding: 0 14px;">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali ke Payroll</span>
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form method="GET" action="{{ route('employee_salary.index') }}" class="m-0">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari nama atau NIK karyawan..." hideLabel="true" />
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari</span>
                </button>
                @if (request()->filled('search'))
                    <a href="{{ route('employee_salary.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1" title="Reset">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0" style="font-size: 13px;">
            <thead>
                <tr>
                    <th>KARYAWAN</th>
                    <th>GAJI POKOK</th>
                    <th>TUNJANGAN AKTIF</th>
                    <th>TOTAL UPAH TETAP</th>
                    <th class="text-end" style="width: 130px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $emp)
                    @php
                        $basic = (float) optional($emp->salaryAssignments->firstWhere('component.code', 'BASIC_SALARY'))->amount ?? 0;
                        $allowances = (float) $emp->salaryAssignments->filter(function($a) {
                            return $a->component && $a->component->type === 'EARNING' && $a->component->code !== 'BASIC_SALARY';
                        })->sum('amount');
                        $totalFixed = $basic + $allowances;
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-2 d-flex align-items-center justify-content-center me-2 flex-shrink-0"
                                    style="width: 32px; height: 32px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); font-weight: 600; font-size: 12px;">
                                    {{ strtoupper(substr($emp->nama_karyawan, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;">
                                        {{ $emp->nama_karyawan }}
                                    </div>
                                    <div class="text-muted font-mono" style="font-size: 11.5px;">
                                        {{ $emp->nik }} &bull; {{ $emp->departemen->nama_dept ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-semibold font-mono text-dark" style="font-size: 13px;">
                                Rp {{ number_format($basic, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            <span class="font-mono text-muted" style="font-size: 12.5px;">
                                Rp {{ number_format($allowances, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold font-mono text-success" style="font-size: 13px;">
                                Rp {{ number_format($totalFixed, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="text-end">
                            @can('employee_salary.edit')
                                <a href="{{ route('employee_salary.edit', $emp->nik) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                                    <i class="ti ti-edit"></i>
                                    <span>Kelola</span>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="ti ti-users-minus fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data Karyawan</h6>
                            <small class="text-muted">Coba ubah kata kunci pencarian.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($employees->hasPages())
    <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
        <div class="card-body py-2.5 px-3">
            {{ $employees->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
@endsection
