@extends('layouts.app')
@section('titlepage', 'Tugaskan Onboarding')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('onboarding.index') }}">Onboarding</a></li>
    <li class="breadcrumb-item active">Tugaskan Onboarding</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('onboarding.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Onboarding
        </a>
        <h4 class="page-title mb-1">Tugaskan Program Onboarding</h4>
        <p class="page-subtitle text-muted mb-0">Inisiasi program orientasi kerja, pemilihan template tugas, dan penetapan mentor pembimbing.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('onboarding.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Batal</span>
        </a>
    </div>
</div>

<div class="card mx-auto shadow-sm" style="max-width: 750px; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF;">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title fw-bold mb-0" style="color: #1e293b;">Formulir Penugasan Onboarding</h5>
    </div>
    <form action="{{ route('onboarding.store') }}" method="POST">
        @csrf
        <div class="card-body p-4">
            @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Pilih Karyawan</label>
                    <select name="nik" class="form-select" required style="border-radius: 8px;">
                        <option value="">-- Cari Nama Karyawan atau NIK --</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->nik }}" {{ old('nik') === $k->nik ? 'selected' : '' }}>
                                {{ $k->nik }} - {{ $k->nama_karyawan }} ({{ $k->departemen->nama_dept ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Template Checklist Tugas</label>
                    <select name="onboarding_template_id" class="form-select" style="border-radius: 8px;">
                        <option value="">-- Gunakan Template Standar Perusahaan --</option>
                        @foreach($templates as $t)
                            <option value="{{ $t->id }}" {{ old('onboarding_template_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }} ({{ $t->tasks->count() }} Tugas)
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted" style="font-size: 11px;">Daftar tugas akan otomatis digenerate ke dalam akun karyawan berdasarkan template ini.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Tanggal Mulai Orientasi</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" required style="border-radius: 8px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Mentor / Pembimbing (Opsional)</label>
                    <select name="mentor_nik" class="form-select" style="border-radius: 8px;">
                        <option value="">-- Tanpa Mentor Khusus --</option>
                        @foreach($karyawans as $m)
                            <option value="{{ $m->nik }}" {{ old('mentor_nik') === $m->nik ? 'selected' : '' }}>
                                {{ $m->nama_karyawan }} ({{ $m->jabatan->nama_jabatan ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer bg-light d-flex justify-content-end gap-2 py-3 px-4 border-top">
            <a href="{{ route('onboarding.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">Batal</a>
            <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1.5">
                <i class="ti ti-check"></i>
                <span>Tugaskan Checklist</span>
            </button>
        </div>
    </form>
</div>
@endsection
