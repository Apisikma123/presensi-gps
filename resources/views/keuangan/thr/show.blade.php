@extends('layouts.app')
@section('titlepage', 'Rincian Event THR: ' . $thr->event_name)

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item"><a href="{{ route('thr.index') }}">THR Keagamaan</a></li>
    <li class="breadcrumb-item active">Rincian Event THR</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="{{ route('thr.index') }}" class="text-muted"><i class="ti ti-arrow-left fs-4"></i></a>
            <h4 class="page-title mb-0">{{ $thr->event_name }}</h4>
            {!! $thr->status_badge_html !!}
        </div>
        <p class="page-subtitle text-muted mb-0">Tahun Buku {{ $thr->year }} &bull; Tanggal Distribusi: {{ $thr->distribution_date ? $thr->distribution_date->translatedFormat('d F Y') : '-' }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('thr.export', $thr->id) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-download"></i>
            <span>Export CSV</span>
        </a>
        @if ($thr->status !== 'FINALIZED' && $thr->status !== 'PAID')
            <form action="{{ route('thr.calculate', $thr->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
                    <i class="ti ti-refresh"></i>
                    <span>Hitung Ulang</span>
                </button>
            </form>
            <form action="{{ route('thr.finalize', $thr->id) }}" method="POST" class="d-inline form-confirm" data-title="Finalisasi Event THR" data-message="Apakah Anda yakin ingin mem-finalisasi event THR ini? Data tidak dapat diubah setelah finalisasi.">
                @csrf
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                    <i class="ti ti-lock"></i>
                    <span>Finalisasi</span>
                </button>
            </form>
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
                <span class="text-muted small fw-medium text-uppercase">Total Dana THR</span>
                <div class="fs-4 fw-bold font-mono text-success mb-0 mt-1">
                    Rp {{ number_format($thr->total_amount, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <span class="text-muted small fw-medium text-uppercase">Total Penerima</span>
                <div class="fs-4 fw-bold font-mono text-dark mb-0 mt-1">
                    {{ number_format($thr->employee_count) }} <span class="fs-6 fw-normal text-muted">Karyawan</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <span class="text-muted small fw-medium text-uppercase">Status Eksekusi</span>
                <div class="fs-5 fw-bold text-dark mb-0 mt-1">
                    {!! $thr->status_badge_html !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <span class="text-muted small fw-medium text-uppercase">Regulasi Rujukan</span>
                <div class="fs-6 fw-bold text-dark mb-0 mt-1">
                    PP 36/2021 & Permenaker 6/2016
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Detail Table Card --}}
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">KARYAWAN</th>
                    <th>OUTLET & JABATAN</th>
                    <th>TANGGAL MASUK</th>
                    <th class="text-center">MASA KERJA</th>
                    <th class="text-end">UPAH ACUAN</th>
                    <th class="text-center">FAKTOR</th>
                    <th class="text-end">NOMINAL THR</th>
                    <th class="text-center pe-4">KATEGORI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($details as $detail)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark d-block" style="font-size: 13px;">{{ $detail->karyawan->nama_karyawan ?? $detail->nik }}</span>
                            <span class="text-muted font-mono" style="font-size: 11px;">{{ $detail->nik }}</span>
                        </td>
                        <td>
                            <div class="text-dark" style="font-size: 12.5px;">{{ $detail->karyawan->cabang->nama_cabang ?? '-' }}</div>
                            <small class="text-muted" style="font-size: 11px;">{{ $detail->karyawan->jabatan->nama_jabatan ?? '-' }}</small>
                        </td>
                        <td class="font-mono text-muted" style="font-size: 12px;">
                            {{ $detail->karyawan && $detail->karyawan->tgl_masuk ? \Carbon\Carbon::parse($detail->karyawan->tgl_masuk)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-center font-mono" style="font-size: 12px;">
                            {{ $detail->tenure_months }} Bln ({{ number_format($detail->tenure_years, 1) }} Thn)
                        </td>
                        <td class="text-end font-mono" style="font-size: 12.5px;">
                            Rp {{ number_format($detail->base_salary, 0, ',', '.') }}
                        </td>
                        <td class="text-center font-mono" style="font-size: 12px;">
                            &times; {{ number_format($detail->multiplier, 3) }}
                        </td>
                        <td class="text-end font-mono fw-bold text-success" style="font-size: 13px;">
                            Rp {{ number_format($detail->thr_amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center pe-4">
                            @if($detail->category === 'FULL')
                                <span class="badge bg-label-success font-mono" style="font-size: 10.5px;">FULL</span>
                            @elseif($detail->category === 'PRORATA')
                                <span class="badge bg-label-info font-mono" style="font-size: 10.5px;">PRORATA</span>
                            @else
                                <span class="badge bg-label-danger font-mono" style="font-size: 10.5px;">EXCLUDED</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            Tidak ada data karyawan yang cocok dengan kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($details->hasPages())
        <div class="card-footer px-4 py-3 d-flex align-items-center justify-content-between border-top">
            {{ $details->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
