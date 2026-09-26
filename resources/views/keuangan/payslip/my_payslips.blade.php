@extends('layouts.app')
@section('titlepage', 'Slip Gaji Saya')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item active">Slip Gaji Saya</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Slip Gaji Saya</h4>
        <p class="page-subtitle text-muted mb-0">Riwayat slip gaji bulanan resmi Anda yang telah diterbitkan oleh departemen keuangan.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('payslip.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

<!-- Table Card -->
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
            <thead>
                <tr>
                    <th>PERIODE GAJI</th>
                    <th>BULAN & TAHUN</th>
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
                                <span class="fw-bold text-dark d-block">{{ $ps->period?->period_name ?? '-' }}</span>
                                <small class="text-muted font-mono" style="font-size: 11px;">
                                    {{ $ps->period?->period_code ?? '-' }}
                                </small>
                            </div>
                        </td>
                        <td>
                            {{ date('F', mktime(0, 0, 0, $ps->period?->month ?? 1, 10)) }} {{ $ps->period?->year }}
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
                            @else
                                <span class="badge bg-label-info">Terverifikasi</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1.5">
                                <a href="{{ route('payslip.show', $ps->id) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Rincian">
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
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="ti ti-file-certificate fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Belum Ada Slip Gaji</h6>
                            <small class="text-muted">Belum ada slip gaji yang diterbitkan untuk akun Anda.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($payslips->isNotEmpty() && $payslips->hasPages())
    <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
        <div class="card-body py-2.5 px-3">
            {{ $payslips->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
@endsection
