@extends('layouts.app')
@section('titlepage', 'Checklist Onboarding')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('onboarding.index') }}">Onboarding</a></li>
    <li class="breadcrumb-item active">Detail Checklist</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('onboarding.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Onboarding
        </a>
        <h4 class="page-title mb-1">
            {{ $onboarding->karyawan->nama_karyawan ?? 'Karyawan' }}
            <span class="font-mono text-muted fs-5 ms-2">[{{ $onboarding->nik }}]</span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Workspace orientasi dan checklist penyelesaian tugas karyawan baru.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('onboarding.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

<!-- Overview Card & Progress -->
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-body p-4">
        <div class="row g-4 align-items-center">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="avatar avatar-md rounded bg-label-primary d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 18px;">
                        {{ strtoupper(substr($onboarding->karyawan->nama_karyawan ?? 'K', 0, 2)) }}
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">{{ $onboarding->karyawan->nama_karyawan ?? 'Karyawan' }}</h5>
                        <div class="text-muted small">
                            {{ $onboarding->karyawan->departemen->nama_dept ?? '-' }} &bull; {{ $onboarding->karyawan->cabang->nama_cabang ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="row g-3 small text-muted">
                    <div class="col-sm-4">
                        <div class="text-uppercase" style="font-size: 11px;">Mulai Orientasi:</div>
                        <div class="fw-bold text-dark"><i class="ti ti-calendar me-1"></i>{{ $onboarding->start_date->format('d M Y') }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-uppercase" style="font-size: 11px;">Target Selesai:</div>
                        <div class="fw-bold text-dark"><i class="ti ti-calendar-check me-1"></i>{{ $onboarding->target_completion_date ? $onboarding->target_completion_date->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-uppercase" style="font-size: 11px;">Mentor / Buddy:</div>
                        <div class="fw-bold text-dark">{{ $onboarding->mentor->nama_karyawan ?? 'Tidak Ditentukan' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 border-start-md ps-md-4">
                @php
                    $progress = $onboarding->progress_percentage;
                    $completedCount = $onboarding->tasks->where('is_completed', true)->count();
                    $totalCount = $onboarding->tasks->count();
                @endphp
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.05em;">Kemajuan Onboarding</span>
                    <span class="font-mono fs-3 fw-bold text-dark">{{ $progress }}%</span>
                </div>
                <div class="progress mb-2" style="height: 8px; border-radius: 4px;">
                    <div class="progress-bar {{ $progress === 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $progress }}%"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small">
                    <span style="font-size: 12px;">{{ $completedCount }} dari {{ $totalCount }} tugas diselesaikan</span>
                    <span>{!! $onboarding->status_badge_html !!}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Checklist Grouped by Category -->
@php
    $categoryIcons = [
        'DOCUMENT' => ['icon' => 'ti-file-text', 'name' => 'Dokumen & Legalitas Karyawan'],
        'IT_ACCESS' => ['icon' => 'ti-device-laptop', 'name' => 'Akses IT, Email & Sistem'],
        'HR_BRIEFING' => ['icon' => 'ti-users', 'name' => 'Orientasi & Briefing Perusahaan'],
        'ASSET' => ['icon' => 'ti-box', 'name' => 'Serah Terima Fasilitas & Aset Kerja'],
        'TRAINING' => ['icon' => 'ti-school', 'name' => 'Pelatihan SOP & Evaluasi Berkala'],
    ];
@endphp

<div class="row g-3">
    @foreach($categoryIcons as $catKey => $catMeta)
    @php
        $tasks = $tasksByCategory->get($catKey, collect());
    @endphp
    @if($tasks->isNotEmpty())
    <div class="col-12">
        <div class="card mb-2" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                <i class="ti {{ $catMeta['icon'] }} fs-4 text-primary"></i>
                <h6 class="card-title fw-bold text-dark mb-0">{{ $catMeta['name'] }}</h6>
                <span class="badge bg-light text-muted ms-auto font-mono">
                    {{ $tasks->where('is_completed', true)->count() }} / {{ $tasks->count() }} Selesai
                </span>
            </div>

            <div class="list-group list-group-flush">
                @foreach($tasks as $t)
                <div class="list-group-item p-3 d-flex align-items-center gap-3 {{ $t->is_completed ? 'bg-light' : '' }}">
                    <form action="{{ route('onboarding.task.toggle', $t->id) }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="is_completed" value="{{ $t->is_completed ? '0' : '1' }}">
                        <button type="submit" class="btn btn-sm {{ $t->is_completed ? 'btn-success' : 'btn-outline-secondary' }} d-flex align-items-center justify-content-center p-0" style="border-radius: 6px; width: 28px; height: 28px;">
                            <i class="ti ti-check fs-5"></i>
                        </button>
                    </form>

                    <div class="flex-grow-1">
                        <div class="fw-semibold {{ $t->is_completed ? 'text-decoration-line-through text-muted' : 'text-dark' }}" style="font-size: 13.5px;">
                            {{ $t->task_name }}
                        </div>
                        <div class="text-muted small mt-1 d-flex flex-wrap gap-3" style="font-size: 12px;">
                            @if($t->due_date)
                                @php
                                    $isOverdue = !$t->is_completed && $t->due_date->isPast();
                                @endphp
                                <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                    <i class="ti ti-clock me-1"></i>
                                    Tenggat: {{ $t->due_date->format('d M Y') }}
                                    @if($isOverdue) (Terlambat) @endif
                                </span>
                            @endif

                            @if($t->is_completed)
                                <span class="text-success">
                                    <i class="ti ti-check-double me-1"></i>
                                    Selesai pada {{ $t->completed_at ? $t->completed_at->format('d M Y H:i') : '-' }}
                                    @if($t->completedBy) (oleh {{ $t->completedBy->name }}) @endif
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(!$t->is_completed)
                        <span class="badge bg-label-warning fw-semibold">Pending</span>
                    @else
                        <span class="badge bg-label-success fw-semibold">Selesai</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>
@endsection
