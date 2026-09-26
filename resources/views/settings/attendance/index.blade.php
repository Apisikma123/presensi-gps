@extends('layouts.app')
@section('titlepage', 'Kebijakan Presensi & Kehadiran')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.hub') }}">Pengaturan Sistem</a></li>
    <li class="breadcrumb-item active">Kebijakan Presensi</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Kebijakan Presensi & Validasi Kehadiran</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                Kebijakan Aktif
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Konfigurasi sentral aturan absensi, toleransi keterlambatan, geofencing GPS, dan biometrik wajah.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('settings.hub') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-arrow-left" style="font-size: 16px;"></i>
            <span>Direktori</span>
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); overflow: hidden;">
            <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center" style="background: #FAF9F8; border-bottom: 1px solid #F1F5F9;">
                <h5 class="card-title fw-bold text-dark mb-0">Parameter & Validasi Absensi</h5>
                <span class="text-muted small">Aturan Universal</span>
            </div>

            <form action="{{ route('attendance_policy.update') }}" method="POST" class="card-body p-4">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark required">Nama Kebijakan</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $policy->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Security & Biometric Toggles -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark mb-2">Validasi & Keamanan Presensi</label>
                        <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                            <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <div class="fw-semibold text-dark">Validasi Geofencing GPS Radius</div>
                                    <div class="text-muted small">Karyawan wajib berada dalam koordinat cabang/titik lokasi yang ditentukan.</div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="require_gps" value="1" {{ old('require_gps', $policy->require_gps) ? 'checked' : '' }}>
                                </div>
                            </label>

                            <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <div class="fw-semibold text-dark">Face Recognition AI (Biometrik Wajah)</div>
                                    <div class="text-muted small">Mencocokkan wajah karyawan dengan foto referensi untuk mencegah titip absen.</div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="require_face_recognition" value="1" {{ old('require_face_recognition', $policy->require_face_recognition) ? 'checked' : '' }}>
                                </div>
                            </label>

                            <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <div class="fw-semibold text-dark">Wajib Foto Bukti Presensi (Selfie)</div>
                                    <div class="text-muted small">Mengambil jepretan kamera saat karyawan melakukan clock-in / clock-out.</div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="require_photo" value="1" {{ old('require_photo', $policy->require_photo) ? 'checked' : '' }}>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Time & Radius Rules -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark required">Toleransi Keterlambatan (Menit)</label>
                        <div class="input-group">
                            <input type="number" name="allow_late_tolerance_minutes" class="form-control font-mono" value="{{ old('allow_late_tolerance_minutes', $policy->allow_late_tolerance_minutes) }}" min="0" max="120" required>
                            <span class="input-group-text">Menit</span>
                        </div>
                        <div class="form-text text-muted">Batas menit keterlambatan tanpa ditandai terlambat/potongan.</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark required">Radius Presensi Maksimal (Meter)</label>
                        <div class="input-group">
                            <input type="number" name="max_out_of_radius_meters" class="form-control font-mono" value="{{ old('max_out_of_radius_meters', $policy->max_out_of_radius_meters) }}" min="10" max="1000" required>
                            <span class="input-group-text">Meter</span>
                        </div>
                        <div class="form-text text-muted">Jarak radius geofencing default dari koordinat kantor.</div>
                    </div>

                    <!-- Additional Policy Rules -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark mb-2">Opsi Fleksibilitas Tambahan</label>
                        <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                            <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <div class="fw-semibold text-dark">Izinkan Presensi Di Luar Radius</div>
                                    <div class="text-muted small">Mengizinkan presensi tetap tersimpan dengan label penanda "Luar Radius" (cocok untuk sales/remote).</div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="allow_out_of_radius" value="1" {{ old('allow_out_of_radius', $policy->allow_out_of_radius) ? 'checked' : '' }}>
                                </div>
                            </label>

                            <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <div class="fw-semibold text-dark">Dukung Shift Malam Lintas Hari (Overnight)</div>
                                    <div class="text-muted small">Mendukung clock-in malam dan clock-out pagi keesokan harinya dalam satu sesi presensi.</div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="allow_overnight" value="1" {{ old('allow_overnight', $policy->allow_overnight) ? 'checked' : '' }}>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Keterangan / Panduan Kebijakan untuk Karyawan</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Uraikan ringkasan ketentuan kehadiran...">{{ old('description', $policy->description) }}</textarea>
                    </div>

                    <div class="col-12 text-end pt-3 border-top">
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                            <i class="ti ti-device-floppy"></i>
                            <span>Simpan Kebijakan Presensi</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
