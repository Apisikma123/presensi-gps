@extends('layouts.app')
@section('titlepage', 'Template Checklist Onboarding')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('onboarding.index') }}">Onboarding</a></li>
    <li class="breadcrumb-item active">Template Checklist</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('onboarding.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Onboarding
        </a>
        <h4 class="page-title mb-1">Template Checklist Onboarding</h4>
        <p class="page-subtitle text-muted mb-0">Master template alur kerja dan daftar tugas orientasi kerja karyawan baru.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('onboarding.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <a href="{{ route('onboarding.templates.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-plus"></i>
            <span>Buat Template Baru</span>
        </a>
    </div>
</div>

<div class="row g-3">
    @forelse($templates as $tmpl)
    <div class="col-md-6">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title fw-bold text-dark mb-0">{{ $tmpl->name }}</h5>
                    <div class="text-muted small">{{ $tmpl->department->nama_dept ?? 'Berlaku untuk Semua Departemen' }}</div>
                </div>
                <span class="badge bg-label-primary font-mono fw-bold">{{ $tmpl->tasks->count() }} Tugas</span>
            </div>
            <div class="card-body p-4">
                @if($tmpl->description)
                    <p class="text-muted small mb-3">{{ $tmpl->description }}</p>
                @endif

                <div class="text-muted small fw-bold mb-2 text-uppercase" style="font-size: 11px; letter-spacing: 0.05em;">Daftar Tugas & Orientasi:</div>
                <div class="list-group list-group-flush border rounded-2" style="border-radius: 8px;">
                    @foreach($tmpl->tasks as $idx => $task)
                    <div class="list-group-item py-2 px-3 d-flex align-items-center justify-content-between small">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-muted font-mono">{{ $idx + 1 }}</span>
                            <span class="text-dark">{{ $task->task_name }}</span>
                        </div>
                        <span class="badge bg-light text-muted font-mono">H+{{ $task->day_offset }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card p-4 text-center text-muted" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
            Belum ada template checklist yang dibuat. Klik "Buat Template Baru" untuk menambahkan.
        </div>
    </div>
    @endforelse
</div>
@endsection
