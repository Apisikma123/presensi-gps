@extends('layouts.app')
@section('titlepage', 'Onboarding Karyawan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Onboarding</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Onboarding Karyawan Baru</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($onboardings->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Manajemen orientasi kerja, penugasan mentor, dan checklist tugas karyawan baru.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('onboarding.templates') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 500; padding: 0 14px;">
            <i class="ti ti-checklist"></i>
            <span>Kelola Template</span>
        </a>
        <a href="{{ route('onboarding.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-plus" style="font-size: 16px;"></i>
            <span>Tugaskan Onboarding</span>
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Total Program Onboarding</div>
                <div class="fs-4 fw-bold font-mono text-dark">{{ $stats['total_onboardings'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Sedang Berjalan (Active)</div>
                <div class="fs-4 fw-bold font-mono text-primary">{{ $stats['in_progress'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Tuntas Selesai (Completed)</div>
                <div class="fs-4 fw-bold font-mono text-success">{{ $stats['completed'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Template Checklist</div>
                <div class="fs-4 fw-bold font-mono text-warning">{{ $stats['templates_count'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('onboarding.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari nama karyawan atau NIK..." hideLabel="true" />
            </div>
            <div class="flex-shrink-0" style="min-width: 180px;">
                <select name="status" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Status</option>
                    <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>Sedang Berjalan</option>
                    <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Selesai</option>
                    <option value="OVERDUE" {{ request('status') === 'OVERDUE' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('onboarding.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Onboardings Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th>KARYAWAN</th>
                    <th>TEMPLATE CHECKLIST</th>
                    <th>PERIODE ORIENTASI</th>
                    <th style="width: 25%;">PROGRESS CHECKLIST</th>
                    <th>STATUS</th>
                    <th class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($onboardings as $ob)
                @php
                    $progress = $ob->progress_percentage;
                    $completedCount = $ob->tasks->where('is_completed', true)->count();
                    $totalCount = $ob->tasks->count();
                @endphp
                <tr>
                    <td>
                        <div class="fw-bold text-dark">{{ $ob->karyawan->nama_karyawan ?? 'Karyawan' }}</div>
                        <div class="text-muted small font-mono">
                            NIK: {{ $ob->nik }} &bull; {{ $ob->karyawan->departemen->nama_dept ?? '-' }}
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $ob->template->name ?? 'Checklist Standar' }}</div>
                        <div class="text-muted small">{{ $totalCount }} Tugas Terdaftar</div>
                    </td>
                    <td>
                        <div><i class="ti ti-calendar me-1"></i>{{ $ob->start_date->format('d M Y') }}</div>
                        @if($ob->target_completion_date)
                            <div class="text-muted small">Target: {{ $ob->target_completion_date->format('d M Y') }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="small text-muted" style="font-size: 11px;">{{ $completedCount }} dari {{ $totalCount }} Tugas</span>
                            <span class="small font-mono fw-bold">{{ $progress }}%</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 4px;">
                            <div class="progress-bar {{ $progress === 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $progress }}%"></div>
                        </div>
                    </td>
                    <td>
                        {!! $ob->status_badge_html !!}
                    </td>
                    <td class="text-end">
                        <a href="{{ route('onboarding.show', $ob->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                            <i class="ti ti-checklist"></i> Buka Checklist
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        Belum ada program onboarding yang terdaftar. Klik "Tugaskan Onboarding" untuk memulai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Footer Card -->
@if($onboardings->hasPages())
    <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
        <div class="card-body py-2.5 px-3">
            {{ $onboardings->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
@endsection
