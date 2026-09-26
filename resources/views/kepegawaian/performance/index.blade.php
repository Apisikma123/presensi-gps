@extends('layouts.app')
@section('titlepage', 'Penilaian Kinerja & KPI')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Penilaian Kinerja</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Penilaian Kinerja & KPI Karyawan</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($reviews->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Manajemen evaluasi kerja berkala, penilaian KPI objektif, dan penetapan predikat performa.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('performance.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-plus" style="font-size: 16px;"></i>
            <span>Buat Evaluasi Baru</span>
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Total Evaluasi</div>
                <div class="fs-4 fw-bold font-mono text-dark">{{ $stats['total_reviews'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Disetujui (Approved)</div>
                <div class="fs-4 fw-bold font-mono text-success">{{ $stats['approved_reviews'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Menunggu Review</div>
                <div class="fs-4 fw-bold font-mono text-warning">{{ $stats['pending_reviews'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Rata-rata Skor Kinerja</div>
                <div class="fs-4 fw-bold font-mono text-primary">{{ $stats['avg_score'] ?? '0' }} / 100</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('performance.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari nama karyawan, NIK, atau kode..." hideLabel="true" />
            </div>
            <div class="flex-shrink-0" style="min-width: 180px;">
                <select name="rating_grade" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Grade Nilai</option>
                    <option value="EXCEEDS" {{ request('rating_grade') === 'EXCEEDS' ? 'selected' : '' }}>Sangat Memuaskan</option>
                    <option value="MEETS" {{ request('rating_grade') === 'MEETS' ? 'selected' : '' }}>Memenuhi Standar</option>
                    <option value="NEEDS_IMPROVEMENT" {{ request('rating_grade') === 'NEEDS_IMPROVEMENT' ? 'selected' : '' }}>Perlu Perbaikan</option>
                    <option value="POOR" {{ request('rating_grade') === 'POOR' ? 'selected' : '' }}>Di Bawah Standar</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari Data</span>
                </button>
                @if(request()->hasAny(['search', 'rating_grade', 'status']))
                    <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Reviews Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th>KODE & PERIODE</th>
                    <th>KARYAWAN</th>
                    <th>PENILAI (REVIEWER)</th>
                    <th class="text-center">SKOR AKHIR</th>
                    <th>GRADE PREDIKAT</th>
                    <th>STATUS</th>
                    <th class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $r)
                <tr>
                    <td>
                        <div class="font-mono text-muted small">{{ $r->review_code }}</div>
                        <a href="{{ route('performance.show', $r->id) }}" class="fw-bold text-dark text-decoration-none">
                            {{ $r->period_title }}
                        </a>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $r->karyawan->nama_karyawan ?? 'Karyawan' }}</div>
                        <div class="text-muted small font-mono">
                            NIK: {{ $r->nik }} &bull; {{ $r->karyawan->departemen->nama_dept ?? '-' }}
                        </div>
                    </td>
                    <td>
                        @if($r->reviewer)
                            <div>{{ $r->reviewer->nama_karyawan }}</div>
                            <div class="text-muted small">Atasan Langsung</div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center font-mono fs-5 fw-bold {{ $r->overall_score >= 75 ? 'text-success' : 'text-warning' }}">
                        {{ number_format($r->overall_score, 1) }}
                    </td>
                    <td>
                        {!! $r->grade_badge_html !!}
                    </td>
                    <td>
                        {!! $r->status_badge_html !!}
                    </td>
                    <td class="text-end">
                        <a href="{{ route('performance.show', $r->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" style="border-radius: 6px;">
                            <i class="ti ti-file-analytics"></i> Rincian Skor
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Belum ada catatan penilaian kinerja. Klik "Buat Evaluasi Baru" untuk memulai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($reviews->hasPages())
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $reviews->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif
@endsection
