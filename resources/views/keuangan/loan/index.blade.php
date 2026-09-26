@extends('layouts.app')
@section('titlepage', 'Pinjaman & Kasbon Karyawan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item active">Kasbon & Pinjaman</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Pinjaman & Kasbon Karyawan</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($loans->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Pengelolaan dana pinjaman tunai/kasbon karyawan, jadwal tenor angsuran, dan pemantauan pelunasan.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        @can('loan.create')
            <a href="{{ route('loan.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Ajukan Kasbon Baru</span>
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

{{-- Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <span class="text-muted small fw-medium text-uppercase">Total Pengajuan</span>
                <div class="fs-4 fw-bold font-mono text-dark mb-0 mt-1">
                    {{ number_format($stats['total_loans'] ?? 0) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <span class="text-muted small fw-medium text-uppercase">Total Dana Dicairkan</span>
                <div class="fs-4 fw-bold font-mono text-success mb-0 mt-1">
                    Rp {{ number_format($stats['total_disbursed'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <span class="text-muted small fw-medium text-uppercase">Sisa Piutang Berjalan</span>
                <div class="fs-4 fw-bold font-mono text-warning mb-0 mt-1">
                    Rp {{ number_format($stats['total_remaining'] ?? $stats['total_receivable'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <span class="text-muted small fw-medium text-uppercase">Pinjaman Aktif</span>
                <div class="fs-4 fw-bold font-mono text-primary mb-0 mt-1">
                    {{ number_format($stats['active_loans'] ?? 0) }} <span class="fs-6 fw-normal text-muted">Kasbon</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Toolbar --}}
<div class="card admin-filter-toolbar mb-3">
    <form method="GET" action="{{ route('loan.index') }}" class="m-0">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">
            <div style="min-width: 170px;">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status Pinjaman</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Aktif Berjalan</option>
                    <option value="PAID_OFF" {{ request('status') === 'PAID_OFF' ? 'selected' : '' }}>Lunas</option>
                    <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="flex-grow-1" style="min-width: 220px;">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nomor pinjaman / nama karyawan..." value="{{ request('search') }}">
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari</span>
                </button>
                @if(request('status') || request('search'))
                    <a href="{{ route('loan.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Loans Table Card --}}
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead>
                <tr>
                    <th class="ps-4">NO. PINJAMAN</th>
                    <th>KARYAWAN</th>
                    <th class="text-end">NOMINAL PINJAMAN</th>
                    <th class="text-center">TENOR</th>
                    <th class="text-end">CICILAN / BLN</th>
                    <th class="text-end">SISA PIUTANG</th>
                    <th class="text-center">STATUS</th>
                    <th class="text-end pe-4" style="width: 120px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($loans as $loan)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                {{ $loan->loan_number }}
                            </span>
                            <small class="text-muted d-block font-mono" style="font-size: 11px;">{{ $loan->created_at ? $loan->created_at->format('d/m/Y') : '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark" style="font-size: 13px;">{{ $loan->karyawan->nama_karyawan ?? $loan->nik }}</div>
                            <small class="text-muted font-mono" style="font-size: 11px;">{{ $loan->nik }}</small>
                        </td>
                        <td class="text-end font-mono fw-bold text-dark" style="font-size: 13px;">
                            Rp {{ number_format($loan->loan_amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center font-mono" style="font-size: 12px;">
                            {{ $loan->installment_months }} Bulan
                        </td>
                        <td class="text-end font-mono" style="font-size: 12.5px;">
                            Rp {{ number_format($loan->monthly_installment, 0, ',', '.') }}
                        </td>
                        <td class="text-end font-mono fw-bold" style="font-size: 13px; color: {{ $loan->remaining_amount > 0 ? '#b45309' : '#15803d' }};">
                            Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            {!! $loan->status_badge_html !!}
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1.5 align-items-center">
                                <a href="{{ route('loan.show', $loan->id) }}" class="btn btn-sm btn-outline-secondary" style="padding: 4px 8px;" title="Lihat Jadwal Angsuran">
                                    <i class="ti ti-eye fs-6"></i>
                                </a>
                                @if($loan->status === 'PENDING')
                                    @can('loan.approve')
                                        <form action="{{ route('loan.approve', $loan->id) }}" method="POST" class="d-inline form-confirm" data-title="Setujui Kasbon" data-message="Setujui pengajuan kasbon ini dan terbitkan jadwal angsuran?">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" style="padding: 4px 8px;" title="Setujui Pinjaman">
                                                <i class="ti ti-check fs-6"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                                @can('loan.delete')
                                    @if(in_array($loan->status, ['PENDING', 'REJECTED']))
                                        <form action="{{ route('loan.destroy', $loan->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-muted p-1 delete-confirm" data-label="Pengajuan Kasbon" title="Hapus">
                                                <i class="ti ti-trash fs-6"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="ti ti-cash-off fs-1 d-block mb-2 text-secondary"></i>
                            <span style="font-size: 14px;">Belum ada data pinjaman / kasbon karyawan.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Footer Card -->
@if ($loans->hasPages())
    <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
        <div class="card-body py-2.5 px-3">
            {{ $loans->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
@endsection
