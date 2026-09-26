@extends('layouts.app')
@section('titlepage', 'Catat Mutasi & Perubahan Karir')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Data Karyawan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('movement.index') }}">Mutasi & Karir</a></li>
    <li class="breadcrumb-item active">Catat Baru</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('movement.index') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Mutasi
        </a>
        <h4 class="page-title mb-1">Pencatatan Mutasi, Rotasi & Promosi Karyawan</h4>
        <p class="page-subtitle text-muted mb-0">Dokumentasikan perpindahan penempatan cabang, promosi jabatan, pergantian divisi, atau atasan langsung.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('movement.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Batal</span>
        </a>
    </div>
</div>

<div class="card mx-auto shadow-sm" style="max-width: 900px; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF;">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title fw-bold text-dark mb-0">Formulir Mutasi & Perubahan Karir</h5>
    </div>
    <form action="{{ route('movement.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body p-4">
            <div class="row g-3">
                <!-- Employee Picker -->
                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Pilih Karyawan</label>
                    <select name="nik" id="selectKaryawan" class="form-select @error('nik') is-invalid @enderror" required style="border-radius: 8px;">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->nik }}" 
                                data-cabang="{{ $k->kode_cabang }}"
                                data-dept="{{ $k->kode_dept }}"
                                data-divisi="{{ $k->kode_divisi }}"
                                data-jabatan="{{ $k->kode_jabatan }}"
                                data-grade="{{ $k->grade_level }}"
                                data-spv="{{ $k->direct_supervisor_nik }}"
                                data-type="{{ $k->employment_type }}"
                                {{ old('nik', $selectedNik) == $k->nik ? 'selected' : '' }}>
                                {{ $k->nik }} - {{ $k->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Movement Type -->
                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Jenis Perubahan / Mutasi</label>
                    <select name="movement_type" id="selectMovementType" class="form-select @error('movement_type') is-invalid @enderror" required style="border-radius: 8px;">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($types as $code => $label)
                            <option value="{{ $code }}" {{ old('movement_type') == $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('movement_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- SK and Date -->
                <div class="col-12 col-md-6">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Tanggal Efektif Berlaku</label>
                    <input type="date" name="effective_date" class="form-control @error('effective_date') is-invalid @enderror" value="{{ old('effective_date', date('Y-m-d')) }}" required style="border-radius: 8px;">
                    <div class="form-text text-muted" style="font-size: 11px;">Jika tanggal efektif adalah hari ini atau masa lampau, data karyawan akan langsung diperbarui otomatis.</div>
                    @error('effective_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Nomor SK / Surat Tugas (Opsional)</label>
                    <input type="text" name="no_sk" class="form-control font-mono" value="{{ old('no_sk') }}" placeholder="Contoh: SK/045/DIR-HRD/VI/2026" style="border-radius: 8px;">
                </div>

                <!-- Current vs New Values section -->
                <div class="col-12">
                    <div class="card border-0 p-3" style="background-color: #f8fafc; border-radius: 8px;">
                        <h6 class="text-primary fw-bold mb-3"><i class="ti ti-adjustments me-1"></i>Rincian Penugasan Baru (Isi Bidang yang Berubah)</h6>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size: 12px;">Cabang Baru</label>
                                <select name="new_kode_cabang" class="form-select" style="border-radius: 8px;">
                                    <option value="">-- Tidak Berubah --</option>
                                    @foreach($cabangs as $c)
                                        <option value="{{ $c->kode_cabang }}" {{ old('new_kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                            {{ $c->nama_cabang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size: 12px;">Departemen Baru</label>
                                <select name="new_kode_dept" class="form-select" style="border-radius: 8px;">
                                    <option value="">-- Tidak Berubah --</option>
                                    @foreach($departemens as $d)
                                        <option value="{{ $d->kode_dept }}" {{ old('new_kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                            {{ $d->nama_dept }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size: 12px;">Divisi Baru</label>
                                <select name="new_kode_divisi" class="form-select" style="border-radius: 8px;">
                                    <option value="">-- Tidak Berubah --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->kode_divisi }}" {{ old('new_kode_divisi') == $div->kode_divisi ? 'selected' : '' }}>
                                            {{ $div->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size: 12px;">Jabatan Baru</label>
                                <select name="new_kode_jabatan" class="form-select" style="border-radius: 8px;">
                                    <option value="">-- Tidak Berubah --</option>
                                    @foreach($jabatans as $j)
                                        <option value="{{ $j->kode_jabatan }}" {{ old('new_kode_jabatan') == $j->kode_jabatan ? 'selected' : '' }}>
                                            {{ $j->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size: 12px;">Grade / Level Baru</label>
                                <input type="text" name="new_grade_level" class="form-control" value="{{ old('new_grade_level') }}" placeholder="Contoh: Senior Officer / L3" style="border-radius: 8px;">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size: 12px;">Atasan Langsung Baru</label>
                                <select name="new_supervisor_nik" class="form-select" style="border-radius: 8px;">
                                    <option value="">-- Tidak Berubah --</option>
                                    @foreach($karyawans as $spv)
                                        <option value="{{ $spv->nik }}" {{ old('new_supervisor_nik') == $spv->nik ? 'selected' : '' }}>
                                            {{ $spv->nik }} - {{ $spv->nama_karyawan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size: 12px;">Status Kepegawaian Baru</label>
                                <select name="new_employment_type" class="form-select" style="border-radius: 8px;">
                                    <option value="">-- Tidak Berubah --</option>
                                    <option value="PKWT" {{ old('new_employment_type') == 'PKWT' ? 'selected' : '' }}>PKWT</option>
                                    <option value="PKWTT" {{ old('new_employment_type') == 'PKWTT' ? 'selected' : '' }}>PKWTT (Tetap)</option>
                                    <option value="PROBATION" {{ old('new_employment_type') == 'PROBATION' ? 'selected' : '' }}>Probation</option>
                                    <option value="INTERNSHIP" {{ old('new_employment_type') == 'INTERNSHIP' ? 'selected' : '' }}>Magang</option>
                                    <option value="FREELANCE" {{ old('new_employment_type') == 'FREELANCE' ? 'selected' : '' }}>Freelance</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size: 12px;">Berkas Dokumen SK / Surat Penugasan</label>
                                <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Alasan / Dasar Keputusan Mutasi</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Kebutuhan operasional cabang baru dan apresiasi performa kinerja..." style="border-radius: 8px;">{{ old('reason') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light d-flex justify-content-end gap-2 py-3 px-4 border-top">
            <a href="{{ route('movement.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">Batal</a>
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4">
                <i class="ti ti-device-floppy"></i>
                <span>Simpan & Terapkan Perubahan</span>
            </button>
        </div>
    </form>
</div>
@endsection
