@extends('layouts.app')
@section('titlepage', 'Publikasikan Lowongan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('recruitment.index') }}">Rekrutmen</a></li>
    <li class="breadcrumb-item active">Buat Lowongan</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('recruitment.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Lowongan
        </a>
        <h4 class="page-title mb-1">Publikasikan Lowongan Baru</h4>
        <p class="page-subtitle text-muted mb-0">Input formasi rekrutmen, penempatan kerja, kuota, dan kualifikasi calon karyawan.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('recruitment.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Batal</span>
        </a>
    </div>
</div>

<div class="card mx-auto shadow-sm" style="max-width: 900px; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF;">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title fw-bold mb-0" style="color: #1e293b;">Informasi Formasi Lowongan</h5>
    </div>
    <form action="{{ route('recruitment.store') }}" method="POST">
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
                <div class="col-md-4">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Kode Lowongan</label>
                    <input type="text" name="vacancy_code" class="form-control font-mono" value="{{ old('vacancy_code', $newCode) }}" required readonly style="border-radius: 8px;">
                </div>
                <div class="col-md-8">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Posisi / Judul Lowongan</label>
                    <input type="text" name="title" class="form-control" placeholder="Contoh: Barista & Store Crew, HR Generalist" value="{{ old('title') }}" required style="border-radius: 8px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Departemen</label>
                    <select name="kode_dept" class="form-select" style="border-radius: 8px;">
                        <option value="">-- Semua / Fleksibel --</option>
                        @foreach($departemens as $d)
                            <option value="{{ $d->kode_dept }}" {{ old('kode_dept') === $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Penempatan Cabang</label>
                    <select name="kode_cabang" class="form-select" style="border-radius: 8px;">
                        <option value="">-- Kantor Pusat / Fleksibel --</option>
                        @foreach($cabangs as $c)
                            <option value="{{ $c->kode_cabang }}" {{ old('kode_cabang') === $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Jenis Hubungan Kerja</label>
                    <select name="employment_type" class="form-select" required style="border-radius: 8px;">
                        <option value="PKWT" {{ old('employment_type') === 'PKWT' ? 'selected' : '' }}>PKWT (Kontrak Tertentu)</option>
                        <option value="PKWTT" {{ old('employment_type') === 'PKWTT' ? 'selected' : '' }}>PKWTT (Tetap / Permanen)</option>
                        <option value="PROBATION" {{ old('employment_type') === 'PROBATION' ? 'selected' : '' }}>Probation (Masa Percobaan)</option>
                        <option value="INTERNSHIP" {{ old('employment_type') === 'INTERNSHIP' ? 'selected' : '' }}>Internship (Magang)</option>
                        <option value="FREELANCE" {{ old('employment_type') === 'FREELANCE' ? 'selected' : '' }}>Freelance / Harian</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Jumlah Formasi (Kuota)</label>
                    <input type="number" name="quota" class="form-control font-mono" min="1" value="{{ old('quota', 1) }}" required style="border-radius: 8px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Batas Lamaran (Deadline)</label>
                    <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}" style="border-radius: 8px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Gaji Minimum (Rp)</label>
                    <input type="number" name="salary_min" class="form-control font-mono" placeholder="4000000" value="{{ old('salary_min') }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Gaji Maksimum (Rp)</label>
                    <input type="number" name="salary_max" class="form-control font-mono" placeholder="6000000" value="{{ old('salary_max') }}" style="border-radius: 8px;">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Deskripsi Pekerjaan & Tanggung Jawab</label>
                    <textarea name="description" rows="4" class="form-control" placeholder="Uraikan tugas, wewenang, dan tanggung jawab posisi ini..." style="border-radius: 8px;">{{ old('description') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Kualifikasi & Persyaratan</label>
                    <textarea name="requirements" rows="4" class="form-control" placeholder="Contoh: Pendidikan minimal S1, pengalaman 2 tahun, bersedia bekerja shift..." style="border-radius: 8px;">{{ old('requirements') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-light d-flex justify-content-end gap-2 py-3 px-4 border-top">
            <a href="{{ route('recruitment.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">Batal</a>
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4">
                <i class="ti ti-check"></i>
                <span>Publikasikan Lowongan</span>
            </button>
        </div>
    </form>
</div>
@endsection
