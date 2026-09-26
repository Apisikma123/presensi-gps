@extends('layouts.app')
@section('titlepage', 'Rekrutmen & Pelamar')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Rekrutmen & Lowongan</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Rekrutmen & Lowongan Kerja</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($vacancies->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Manajemen pembukaan lowongan kerja, kuota posisi, dan pelamar rekrutmen.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('recruitment.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-plus" style="font-size: 16px;"></i>
            <span>Buat Lowongan Baru</span>
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Total Lowongan</div>
                <div class="fs-4 fw-bold font-mono text-dark">{{ $stats['total_vacancies'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Lowongan Dibuka</div>
                <div class="fs-4 fw-bold font-mono text-primary">{{ $stats['open_vacancies'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Total Pelamar</div>
                <div class="fs-4 fw-bold font-mono text-warning">{{ $stats['total_candidates'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.05em;">Kandidat Diterima (Hired)</div>
                <div class="fs-4 fw-bold font-mono text-success">{{ $stats['hired_candidates'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('recruitment.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari posisi lowongan atau kode..." hideLabel="true" />
            </div>
            <div class="flex-shrink-0" style="min-width: 180px;">
                <select name="status" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Status</option>
                    <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>Dibuka (OPEN)</option>
                    <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>Draft</option>
                    <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>Ditutup (CLOSED)</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('recruitment.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Vacancies Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th>KODE & POSISI</th>
                    <th>DEPARTEMEN & CABANG</th>
                    <th>JENIS KERJA</th>
                    <th>KUOTA</th>
                    <th>RENTANG GAJI</th>
                    <th>PELAMAR</th>
                    <th>STATUS</th>
                    <th class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vacancies as $v)
                <tr>
                    <td>
                        <div class="font-mono text-muted small">{{ $v->vacancy_code }}</div>
                        <a href="{{ route('recruitment.show', $v->id) }}" class="fw-bold text-dark text-decoration-none">
                            {{ $v->title }}
                        </a>
                    </td>
                    <td>
                        <div>{{ $v->department->nama_dept ?? 'Semua Dept' }}</div>
                        <div class="text-muted small">{{ $v->branch->nama_cabang ?? 'Pusat' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $v->employment_type }}</span>
                    </td>
                    <td class="font-mono">
                        {{ $v->quota }} Orang
                    </td>
                    <td class="font-mono small">
                        @if($v->salary_min || $v->salary_max)
                            Rp {{ number_format($v->salary_min, 0, ',', '.') }} - {{ number_format($v->salary_max, 0, ',', '.') }}
                        @else
                            <span class="text-muted">Kompetitif</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-label-primary font-mono fw-bold">
                            {{ $v->candidates->count() }} Pelamar
                        </span>
                    </td>
                    <td>
                        {!! $v->status_badge_html !!}
                    </td>
                    <td class="text-end">
                        <a href="{{ route('recruitment.show', $v->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                            <i class="ti ti-layout-kanban"></i> Pipeline
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        Belum ada data lowongan kerja. Klik "Buat Lowongan Baru" untuk memulai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Footer Card -->
@if($vacancies->hasPages())
    <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
        <div class="card-body py-2.5 px-3">
            {{ $vacancies->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
@endsection
