@extends('layouts.app')
@section('titlepage', 'Rincian Kasbon: ' . $loan->loan_number)

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item"><a href="{{ route('loan.index') }}">Kasbon & Pinjaman</a></li>
    <li class="breadcrumb-item active">Detail Kasbon</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('loan.index') }}" class="text-muted"><i class="ti ti-arrow-left fs-4"></i></a>
            <h4 class="page-title mb-0">Kasbon &bull; {{ $loan->loan_number }}</h4>
            {!! $loan->status_badge_html !!}
        </div>
        <p class="page-subtitle text-muted mb-0">
            Peminjam: <strong class="text-dark">{{ $loan->karyawan?->nama_karyawan }}</strong> ({{ $loan->nik }}) &bull; Mulai Cicilan: {{ $loan->start_date ? $loan->start_date->translatedFormat('d F Y') : '-' }}
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if($loan->status === 'PENDING')
            @can('loan.approve')
                <form action="{{ route('loan.approve', $loan->id) }}" method="POST" class="d-inline form-confirm" data-title="Setujui Pinjaman" data-message="Setujui pinjaman ini dan terbitkan jadwal cicilan?">
                    @csrf
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-check"></i>
                        <span>Setujui & Terbitkan Jadwal</span>
                    </button>
                </form>
            @endcan
        @endif
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

{{-- Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <span class="text-muted small fw-medium text-uppercase">Pokok Pinjaman</span>
                <div class="fs-4 fw-bold font-mono text-dark mb-0 mt-1">
                    Rp {{ number_format($loan->loan_amount, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <span class="text-muted small fw-medium text-uppercase">Tenor & Cicilan</span>
                <div class="fs-4 fw-bold font-mono text-dark mb-0 mt-1">
                    {{ $loan->installment_months }} Bln <span class="fs-6 fw-normal text-muted">(Rp {{ number_format($loan->monthly_installment, 0, ',', '.') }}/bln)</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <span class="text-muted small fw-medium text-uppercase">Telah Terbayar</span>
                <div class="fs-4 fw-bold font-mono text-success mb-0 mt-1">
                    Rp {{ number_format($loan->total_repaid, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <span class="text-muted small fw-medium text-uppercase">Sisa Kewajiban</span>
                <div class="fs-4 fw-bold font-mono mb-0 mt-1" style="color: {{ $loan->remaining_amount > 0 ? '#b45309' : '#15803d' }};">
                    Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Installment Schedule Table Card --}}
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-header py-3 px-4 bg-transparent border-bottom">
        <h6 class="fw-bold mb-0 text-dark">Jadwal Angsuran & Rekapitulasi Pelunasan</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">ANGSURAN KE-</th>
                    <th>JATUH TEMPO</th>
                    <th class="text-end">TAGIHAN</th>
                    <th class="text-end">DIBAYAR</th>
                    <th>TANGGAL BAYAR</th>
                    <th class="text-center">STATUS</th>
                    <th class="text-end pe-4" style="width: 120px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($loan->installments as $inst)
                    <tr>
                        <td class="ps-4 font-mono fw-bold text-dark" style="font-size: 13px;">
                            Angsuran #{{ $inst->installment_number }}
                        </td>
                        <td class="font-mono text-muted" style="font-size: 12px;">
                            {{ $inst->due_date ? $inst->due_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-end font-mono" style="font-size: 12.5px;">
                            Rp {{ number_format($inst->amount, 0, ',', '.') }}
                        </td>
                        <td class="text-end font-mono" style="font-size: 12.5px; color: {{ $inst->paid_amount > 0 ? '#15803d' : '#64748b' }};">
                            Rp {{ number_format($inst->paid_amount, 0, ',', '.') }}
                        </td>
                        <td class="font-mono text-muted" style="font-size: 12px;">
                            {{ $inst->paid_at ? $inst->paid_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="text-center">
                            {!! $inst->status_badge_html !!}
                        </td>
                        <td class="text-end pe-4">
                            @if($inst->status === 'UNPAID')
                                @can('loan.approve')
                                    <form action="{{ route('loan.repay', $inst->id) }}" method="POST" class="d-inline form-confirm" data-title="Konfirmasi Pembayaran" data-message="Tandai angsuran ini sebagai telah dibayar/lunas?">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" style="padding: 4px 8px;">
                                            <i class="ti ti-check fs-6 me-0.5"></i> Bayar
                                        </button>
                                    </form>
                                @endcan
                            @else
                                <span class="badge bg-label-success font-mono" style="font-size: 10.5px;"><i class="ti ti-circle-check me-0.5"></i> Terbayar</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="ti ti-calendar-off fs-1 d-block mb-2 text-secondary"></i>
                            <span style="font-size: 14px;">Jadwal angsuran akan terbentuk secara otomatis setelah pengajuan pinjaman disetujui.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
