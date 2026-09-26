@extends('layouts.app')
@section('titlepage', 'Tambah Jenis Cuti Baru')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('leave_types.index') }}">Jenis Cuti</a></li>
    <li class="breadcrumb-item active">Tambah Jenis Cuti</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1"><i class="ti ti-calendar-plus me-2 text-primary"></i>Tambah Jenis Cuti Baru</h4>
        <p class="page-subtitle text-muted mb-0">Konfigurasi jatah cuti, aturan pemotongan upah, restriksi gender, dan alokasi kuota.</p>
    </div>
    <div>
        <a href="{{ route('leave_types.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Informasi & Kebijakan Cuti</h5>
            </div>
            <form action="{{ route('leave_types.store') }}" method="POST" class="card-body p-4">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label required fw-medium">Kode Cuti</label>
                        <input type="text" name="code" class="form-control font-mono @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Contoh: CT, CM, CN" style="text-transform: uppercase;" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-8">
                        <label class="form-label required fw-medium">Nama Jenis Cuti</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Cuti Tahunan, Cuti Melahirkan" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label required fw-medium">Tipe Alokasi Kuota</label>
                        <select name="quota_type" class="form-select @error('quota_type') is-invalid @enderror" required>
                            <option value="ANNUAL" {{ old('quota_type') === 'ANNUAL' ? 'selected' : '' }}>Tahunan (Annual Quota)</option>
                            <option value="MONTHLY" {{ old('quota_type') === 'MONTHLY' ? 'selected' : '' }}>Bulanan (Monthly Quota)</option>
                            <option value="UNLIMITED" {{ old('quota_type') === 'UNLIMITED' ? 'selected' : '' }}>Tanpa Batas Kuota (Unlimited)</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label required fw-medium">Jatah Default Hari</label>
                        <input type="number" step="0.5" name="default_quota" class="form-control font-mono @error('default_quota') is-invalid @enderror" value="{{ old('default_quota', 12) }}" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-medium">Batasan Gender</label>
                        <select name="gender_restriction" class="form-select">
                            <option value="">Semua Gender</option>
                            <option value="P" {{ old('gender_restriction') === 'P' ? 'selected' : '' }}>Khusus Perempuan (cth: Melahirkan)</option>
                            <option value="M" {{ old('gender_restriction') === 'M' ? 'selected' : '' }}>Khusus Laki-laki</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-medium">Min. Pengajuan (H-x Hari)</label>
                        <input type="number" name="min_notice_days" class="form-control font-mono" value="{{ old('min_notice_days', 0) }}" min="0" max="90">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-medium">Maks. Hari Berturut-turut</label>
                        <input type="number" name="max_consecutive_days" class="form-control font-mono" value="{{ old('max_consecutive_days') }}" placeholder="Opsional">
                    </div>

                    <div class="col-12">
                        <div class="card bg-light border-0 p-3" style="border-radius: 8px;">
                            <h6 class="fw-bold text-dark mb-2">Ketentuan Tambahan</h6>
                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <label class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="is_paid" value="1" {{ old('is_paid', 1) ? 'checked' : '' }}>
                                        <span class="form-check-label fw-semibold">Cuti Berbayar (Gaji Tetap Diberikan)</span>
                                    </label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="requires_attachment" value="1" {{ old('requires_attachment') ? 'checked' : '' }}>
                                        <span class="form-check-label fw-semibold">Wajib Lampiran Surat / Bukti</span>
                                    </label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="requires_approval" value="1" {{ old('requires_approval', 1) ? 'checked' : '' }}>
                                        <span class="form-check-label fw-semibold">Memerlukan Persetujuan Atasan / HR</span>
                                    </label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="uses_quota" value="1" {{ old('uses_quota', 1) ? 'checked' : '' }}>
                                        <span class="form-check-label fw-semibold">Memotong Saldo Kuota Cuti Karyawan</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('leave_types.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                            <i class="ti ti-device-floppy"></i>Simpan Jenis Cuti
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
