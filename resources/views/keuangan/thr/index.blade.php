@extends('layouts.app')
@section('titlepage', 'THR Keagamaan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item active">THR Keagamaan</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Tunjangan Hari Raya (THR) Keagamaan</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($payments->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Manajemen pembagian THR sesuai regulasi PP 36/2021 & Permenaker 6/2016 secara otomatis.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('compliance.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 500; padding: 0 14px;">
            <i class="ti ti-shield-check"></i>
            <span>Statutory Compliance</span>
        </a>
        @can('thr.create')
            <a href="{{ route('thr.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Buat Event THR</span>
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

{{-- Event List Card --}}
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead>
                <tr>
                    <th class="ps-4">EVENT THR</th>
                    <th>TAHUN</th>
                    <th>TANGGAL DISTRIBUSI</th>
                    <th>STATUS</th>
                    <th class="text-center">PENERIMA</th>
                    <th class="text-end">TOTAL ANGGARAN</th>
                    <th class="text-end pe-4" style="width: 120px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark d-block" style="font-size: 13.5px;">{{ $payment->event_name }}</span>
                            @if($payment->notes)
                                <small class="text-muted d-block" style="font-size: 11px;">{{ Str::limit($payment->notes, 40) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                {{ $payment->year }}
                            </span>
                        </td>
                        <td>
                            <span class="text-dark font-mono" style="font-size: 12px;">
                                {{ $payment->distribution_date ? $payment->distribution_date->format('d/m/Y') : '-' }}
                            </span>
                        </td>
                        <td>
                            {!! $payment->status_badge_html !!}
                        </td>
                        <td class="text-center font-mono" style="font-size: 12.5px;">
                            {{ number_format($payment->employee_count) }} Pegawai
                        </td>
                        <td class="text-end font-mono fw-bold text-success" style="font-size: 13.5px;">
                            Rp {{ number_format($payment->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1.5">
                                <a href="{{ route('thr.show', $payment->id) }}" class="btn btn-sm btn-outline-secondary" title="Lihat Rincian" style="padding: 4px 8px;">
                                    <i class="ti ti-eye fs-6"></i>
                                </a>
                                <a href="{{ route('thr.export', $payment->id) }}" class="btn btn-sm btn-outline-secondary" title="Export CSV" style="padding: 4px 8px;">
                                    <i class="ti ti-download fs-6"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="ti ti-gift-off fs-1 d-block mb-2 text-secondary"></i>
                            <span style="font-size: 14px;">Belum ada event pembagian THR yang dibuat.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Footer Card -->
@if ($payments->hasPages())
    <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
        <div class="card-body py-2.5 px-3">
            {{ $payments->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
@endsection
