@extends('layouts.app')
@section('titlepage', 'Pelatihan & Sertifikasi')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Pelatihan & Sertifikasi</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Pelatihan & Sertifikasi Karyawan</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($trainings->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Pencatatan riwayat kursus, sertifikasi profesi, dan evaluasi hasil training karyawan.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalAddTraining" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-plus" style="font-size: 16px;"></i>
            <span>Catat Pelatihan Baru</span>
        </button>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('training.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari nama pelatihan, nama karyawan, atau NIK..." hideLabel="true" />
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari Data</span>
                </button>
                @if(request('search'))
                    <a href="{{ route('training.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Trainings Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th>KODE & NAMA PELATIHAN</th>
                    <th>KARYAWAN</th>
                    <th>LEMBAGA / PROVIDER</th>
                    <th>TANGGAL PELAKSANAAN</th>
                    <th class="text-center">DURASI</th>
                    <th class="text-center">SKOR / NILAI</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trainings as $t)
                <tr>
                    <td>
                        <div class="font-mono text-muted small">{{ $t->training_code }}</div>
                        <div class="fw-semibold text-dark">{{ $t->title }}</div>
                        @if($t->certificate_number)
                            <div class="text-muted small">No. Sertifikat: <span class="font-mono">{{ $t->certificate_number }}</span></div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $t->karyawan->nama_karyawan ?? 'Karyawan' }}</div>
                        <div class="text-muted small font-mono">NIK: {{ $t->nik }}</div>
                    </td>
                    <td>{{ $t->provider ?: 'Internal HR' }}</td>
                    <td>
                        <div>{{ $t->start_date->format('d M Y') }}</div>
                        <div class="text-muted small">s.d {{ $t->end_date->format('d M Y') }}</div>
                    </td>
                    <td class="text-center font-mono">{{ $t->duration_hours }} Jam</td>
                    <td class="text-center font-mono fw-bold {{ $t->score >= 80 ? 'text-success' : 'text-dark' }}">
                        {{ $t->score ? number_format($t->score, 1) : '-' }}
                    </td>
                    <td>{!! $t->status_badge_html !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat pelatihan tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($trainings->hasPages())
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $trainings->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

<!-- Modal Add Training -->
<div class="modal fade" id="modalAddTraining" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="{{ route('training.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark mb-0">Catat Pelatihan Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Pilih Karyawan</label>
                        <select name="nik" class="form-select" required style="border-radius: 8px;">
                            @foreach($karyawans as $k)
                                <option value="{{ $k->nik }}">{{ $k->nik }} - {{ $k->nama_karyawan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Nama Kursus / Pelatihan</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Food Safety & Hygiene HACCP" required style="border-radius: 8px;">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Lembaga Penyelenggara</label>
                            <input type="text" name="provider" class="form-control" placeholder="Internal HR / LSP" style="border-radius: 8px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Durasi (Jam)</label>
                            <input type="number" name="duration_hours" class="form-control font-mono" value="8" min="1" required style="border-radius: 8px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Mulai Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Selesai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Nomor Sertifikat</label>
                            <input type="text" name="certificate_number" class="form-control font-mono" style="border-radius: 8px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Skor / Nilai Evaluasi</label>
                            <input type="number" name="score" class="form-control font-mono" placeholder="85" min="0" max="100" style="border-radius: 8px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Simpan Pelatihan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
