@extends('layouts.app')
@section('titlepage', 'Rincian Penilaian Kinerja')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('performance.index') }}">Penilaian Kinerja</a></li>
    <li class="breadcrumb-item active">Rincian Skor</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('performance.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Evaluasi
        </a>
        <h4 class="page-title mb-1">
            {{ $review->period_title }}
            <span class="font-mono text-muted fs-5 ms-2">[{{ $review->review_code }}]</span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Scorecard evaluasi performa, pencapaian KPI terbobot, dan catatan kualitatif karyawan.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
        @if($review->status !== 'APPROVED')
            <form action="{{ route('performance.approve', $review->id) }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                    <i class="ti ti-check"></i>
                    <span>Setujui Evaluasi (Approve)</span>
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Employee & Score Header -->
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-body p-4">
        <div class="row g-4 align-items-center">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="avatar avatar-md rounded bg-label-primary d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 18px;">
                        {{ strtoupper(substr($review->karyawan->nama_karyawan ?? 'K', 0, 2)) }}
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">{{ $review->karyawan->nama_karyawan ?? 'Karyawan' }}</h5>
                        <div class="text-muted small">
                            NIK: <span class="font-mono text-dark fw-bold">{{ $review->nik }}</span> &bull; 
                            {{ $review->karyawan->departemen->nama_dept ?? '-' }} &bull; 
                            {{ $review->karyawan->cabang->nama_cabang ?? '-' }}
                        </div>
                        <div class="text-muted small mt-1">
                            Penilai (Reviewer): <span class="fw-bold text-dark">{{ $review->reviewer->nama_karyawan ?? 'Atasan Langsung' }}</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 small text-muted">
                    <div class="col-sm-6">
                        <div class="text-uppercase" style="font-size: 11px;">Periode Evaluasi:</div>
                        <div class="fw-bold text-dark"><i class="ti ti-calendar me-1"></i>{{ $review->start_date->format('d M Y') }} s.d {{ $review->end_date->format('d M Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-uppercase" style="font-size: 11px;">Status Review:</div>
                        <div>{!! $review->status_badge_html !!}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 border-start-md ps-md-4 text-center text-md-end">
                <div class="text-muted small fw-bold text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Skor Kinerja Kumulatif</div>
                <div class="fs-1 fw-bold font-mono text-dark mb-1">
                    {{ number_format($review->overall_score, 1) }}
                    <span class="fs-4 text-muted">/ 100</span>
                </div>
                <div>
                    {!! $review->grade_badge_html !!}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Indicators Breakdown Table -->
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="card-title fw-bold text-dark mb-0">Rincian Indikator Kinerja Utama (KPI)</h6>
        <span class="badge bg-light text-muted font-mono">{{ $review->kpis->count() }} Indikator</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 35%;">INDIKATOR KPI</th>
                    <th class="text-center" style="width: 15%;">BOBOT</th>
                    <th style="width: 20%;">TARGET / REALISASI</th>
                    <th class="text-center" style="width: 10%;">NILAI (0-100)</th>
                    <th class="text-end" style="width: 15%;">NILAI TERBOBOT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($review->kpis as $idx => $kpi)
                <tr>
                    <td class="text-muted font-mono">{{ $idx + 1 }}</td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $kpi->kpi_name }}</div>
                        @if($kpi->notes)
                            <div class="text-muted small mt-1">{{ $kpi->notes }}</div>
                        @endif
                    </td>
                    <td class="text-center font-mono">
                        {{ number_format($kpi->weight, 0) }}%
                    </td>
                    <td class="small">
                        <div>Target: <span class="fw-bold">{{ $kpi->target_value ?: '-' }}</span></div>
                        <div>Realisasi: <span class="text-success fw-bold">{{ $kpi->actual_value ?: '-' }}</span></div>
                    </td>
                    <td class="text-center font-mono fw-bold">
                        {{ number_format($kpi->score, 1) }}
                    </td>
                    <td class="text-end font-mono fw-bold text-primary fs-5">
                        {{ number_format($kpi->weighted_score, 1) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="2" class="text-end">Total Bobot:</td>
                    <td class="text-center font-mono">{{ number_format($review->kpis->sum('weight'), 0) }}%</td>
                    <td colspan="2" class="text-end">Total Skor Akhir:</td>
                    <td class="text-end font-mono fs-4 text-success">{{ number_format($review->overall_score, 1) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Qualitative Feedback Cards -->
<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-label-success p-1 rounded"><i class="ti ti-thumb-up"></i></span>
                    <h6 class="fw-bold text-dark mb-0">Kelebihan & Prestasi</h6>
                </div>
                <p class="text-muted small mb-0" style="font-size: 13px;">
                    {{ $review->strengths ?: 'Tidak ada catatan khusus.' }}
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-label-warning p-1 rounded"><i class="ti ti-trending-up"></i></span>
                    <h6 class="fw-bold text-dark mb-0">Area Peningkatan</h6>
                </div>
                <p class="text-muted small mb-0" style="font-size: 13px;">
                    {{ $review->areas_for_improvement ?: 'Tidak ada catatan khusus.' }}
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-label-primary p-1 rounded"><i class="ti ti-target"></i></span>
                    <h6 class="fw-bold text-dark mb-0">Sasaran Periode Depan</h6>
                </div>
                <p class="text-muted small mb-0" style="font-size: 13px;">
                    {{ $review->goals_next_period ?: 'Tidak ada catatan khusus.' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
