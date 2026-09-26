@extends('layouts.app')
@section('titlepage', 'Pengangkatan Karyawan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('recruitment.index') }}">Rekrutmen</a></li>
    <li class="breadcrumb-item"><a href="{{ route('recruitment.show', $candidate->recruitment_vacancy_id) }}">Detail Pipeline</a></li>
    <li class="breadcrumb-item active">Pengangkatan</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('recruitment.show', $candidate->recruitment_vacancy_id) }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Pipeline
        </a>
        <h4 class="page-title mb-1">Pengangkatan Karyawan Baru</h4>
        <p class="page-subtitle text-muted mb-0">Konversi kandidat diterima ke master karyawan aktif dan inisiasi alur orientasi onboarding.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('recruitment.show', $candidate->recruitment_vacancy_id) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali ke Pipeline</span>
        </a>
    </div>
</div>

<div class="card mx-auto shadow-sm" style="max-width: 850px; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF;">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-md rounded bg-label-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="ti ti-user-check fs-3"></i>
            </div>
            <div>
                <h5 class="card-title fw-bold text-dark mb-0">{{ $candidate->name }}</h5>
                <div class="text-muted small">
                    Kandidat untuk posisi: <span class="fw-bold">{{ $candidate->vacancy->title ?? '-' }}</span> 
                    (<span class="font-mono">{{ $candidate->candidate_code }}</span>)
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('recruitment.hire', $candidate->id) }}" method="POST">
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

            <div class="alert alert-primary border-0 d-flex align-items-center gap-2 mb-4" style="background-color: rgba(105, 108, 255, 0.08); color: #696cff; border-radius: 8px;">
                <i class="ti ti-info-circle fs-4 flex-shrink-0"></i>
                <div style="font-size: 13px;">
                    Proses ini akan mendaftarkan kandidat ke master karyawan (tabel <code>karyawan</code>), membuat akun login pengguna, dan otomatis menginisiasi alur kerja <strong>Onboarding Karyawan Baru</strong>.
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Nomor Induk Karyawan (NIK)</label>
                    <input type="text" name="nik" class="form-control font-mono" placeholder="Kosongkan untuk auto-generate NIK" value="{{ old('nik') }}" style="border-radius: 8px;">
                    <small class="text-muted" style="font-size: 11px;">Maksimal 9 karakter. Sistem akan membuat NIK urut otomatis jika dikosongkan.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Tanggal Mulai Masuk (Hire Date)</label>
                    <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required style="border-radius: 8px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Departemen</label>
                    <select name="kode_dept" class="form-select" required style="border-radius: 8px;">
                        @foreach($departemens as $d)
                            <option value="{{ $d->kode_dept }}" {{ (old('kode_dept', $candidate->vacancy->kode_dept) === $d->kode_dept) ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Penempatan Cabang</label>
                    <select name="kode_cabang" class="form-select" required style="border-radius: 8px;">
                        @foreach($cabangs as $c)
                            <option value="{{ $c->kode_cabang }}" {{ (old('kode_cabang', $candidate->vacancy->kode_cabang) === $c->kode_cabang) ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Jabatan</label>
                    <select name="kode_jabatan" class="form-select" required style="border-radius: 8px;">
                        @foreach($jabatans as $j)
                            <option value="{{ $j->kode_jabatan }}" {{ old('kode_jabatan') === $j->kode_jabatan ? 'selected' : '' }}>
                                {{ $j->nama_jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Status Hubungan Kerja</label>
                    <select name="employment_type" class="form-select" required style="border-radius: 8px;">
                        <option value="PKWT" {{ old('employment_type', $candidate->vacancy->employment_type) === 'PKWT' ? 'selected' : '' }}>PKWT (Kontrak Tertentu)</option>
                        <option value="PKWTT" {{ old('employment_type', $candidate->vacancy->employment_type) === 'PKWTT' ? 'selected' : '' }}>PKWTT (Tetap / Permanen)</option>
                        <option value="PROBATION" {{ old('employment_type', $candidate->vacancy->employment_type) === 'PROBATION' ? 'selected' : '' }}>Probation (Masa Percobaan)</option>
                        <option value="INTERNSHIP" {{ old('employment_type', $candidate->vacancy->employment_type) === 'INTERNSHIP' ? 'selected' : '' }}>Internship (Magang)</option>
                    </select>
                </div>

                <div class="col-12 mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-dark mb-1">Penugasan Checklist Onboarding</h6>
                    <p class="text-muted small mb-3">Pilih template daftar tugas yang harus diselesaikan selama masa orientasi 30 hari pertama.</p>
                    <select name="onboarding_template_id" class="form-select" style="border-radius: 8px;">
                        <option value="">-- Gunakan Checklist Standar (6 Tugas Dasar) --</option>
                        @foreach($templates as $tmpl)
                            <option value="{{ $tmpl->id }}" {{ old('onboarding_template_id') == $tmpl->id ? 'selected' : '' }}>
                                {{ $tmpl->name }} ({{ $tmpl->tasks->count() }} Tugas)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 mt-3 pt-3 border-top">
                    <h6 class="fw-bold text-dark mb-1">Akun Sistem & Kredensial</h6>
                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Email Akun</label>
                            <input type="text" class="form-control" value="{{ $candidate->email }}" readonly disabled style="border-radius: 8px; background-color: #f8fafc;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Password Awal</label>
                            <input type="text" name="password" class="form-control font-mono" value="password123" style="border-radius: 8px;">
                            <small class="text-muted" style="font-size: 11px;">Default: <code>password123</code> (karyawan dapat mengganti saat pertama login).</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light d-flex justify-content-end gap-2 py-3 px-4 border-top">
            <a href="{{ route('recruitment.show', $candidate->recruitment_vacancy_id) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">Batal</a>
            <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1.5">
                <i class="ti ti-check"></i>
                <span>Konfirmasi Pengangkatan & Buat Onboarding</span>
            </button>
        </div>
    </form>
</div>
@endsection
