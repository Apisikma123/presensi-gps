@extends('layouts.app')
@section('titlepage', 'Pusat Pengaturan Sistem')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pengaturan Sistem</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Pengaturan Sistem</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                Unified Panel
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Kelola profil instansi, kebijakan absensi & lembur, modul fungsional, dan konfigurasi server dalam satu tempat.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        @can('audit_logs.index')
        <a href="{{ route('settings.audit_logs.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-history" style="font-size: 16px;"></i>
            <span>Log Jejak Audit</span>
        </a>
        @endcan
        @can('users.index')
        <a href="{{ route('users.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-user-check" style="font-size: 16px;"></i>
            <span>Akun Pengguna</span>
        </a>
        @endcan
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible mb-4 d-flex align-items-center" role="alert" style="border-radius: 10px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #15803d;">
        <i class="ti ti-circle-check fs-5 me-2"></i>
        <div class="flex-grow-1" style="font-size: 13.5px; font-weight: 500;">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible mb-4 d-flex align-items-center" role="alert" style="border-radius: 10px; border: 1px solid #fecaca; background: #fef2f2; color: #b91c1c;">
        <i class="ti ti-alert-triangle fs-5 me-2"></i>
        <div class="flex-grow-1" style="font-size: 13.5px; font-weight: 500;">{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Unified Navigation Tabs --}}
<style>
    #settingsTab .nav-link {
        color: #475569 !important;
        background: transparent !important;
        border: 1px solid transparent !important;
        transition: all 0.15s ease-in-out;
    }
    #settingsTab .nav-link i {
        color: #64748B !important;
        transition: color 0.15s ease-in-out;
    }
    #settingsTab .nav-link:hover {
        color: var(--color-primary, #1A5276) !important;
        background: rgba(var(--bs-primary-rgb, 26, 82, 118), 0.06) !important;
    }
    #settingsTab .nav-link:hover i {
        color: var(--color-primary, #1A5276) !important;
    }
    #settingsTab .nav-link.active {
        color: #FFFFFF !important;
        background: var(--color-primary, #1A5276) !important;
        border-color: var(--color-primary, #1A5276) !important;
        box-shadow: 0 2px 6px rgba(var(--bs-primary-rgb, 26, 82, 118), 0.25) !important;
    }
    #settingsTab .nav-link.active i {
        color: #FFFFFF !important;
    }
</style>
<div class="card mb-4" style="border: 1px solid #E2E8F0; border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-body p-2">
        <ul class="nav nav-pills nav-fill gap-1" id="settingsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link text-start text-md-center py-2.5 px-3 {{ $activeTab === 'company' ? 'active' : '' }}" 
                    id="tab-company-btn" data-bs-toggle="pill" data-bs-target="#tab-company" type="button" role="tab" aria-controls="tab-company" aria-selected="{{ $activeTab === 'company' ? 'true' : 'false' }}"
                    style="border-radius: 8px; font-weight: 600; font-size: 13px;">
                    <i class="ti ti-building me-1 fs-5"></i>
                    <span>Profil Perusahaan</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-start text-md-center py-2.5 px-3 {{ $activeTab === 'attendance' ? 'active' : '' }}" 
                    id="tab-attendance-btn" data-bs-toggle="pill" data-bs-target="#tab-attendance" type="button" role="tab" aria-controls="tab-attendance" aria-selected="{{ $activeTab === 'attendance' ? 'true' : 'false' }}"
                    style="border-radius: 8px; font-weight: 600; font-size: 13px;">
                    <i class="ti ti-map-pin me-1 fs-5"></i>
                    <span>Kebijakan Presensi & GPS</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-start text-md-center py-2.5 px-3 {{ $activeTab === 'overtime' ? 'active' : '' }}" 
                    id="tab-overtime-btn" data-bs-toggle="pill" data-bs-target="#tab-overtime" type="button" role="tab" aria-controls="tab-overtime" aria-selected="{{ $activeTab === 'overtime' ? 'true' : 'false' }}"
                    style="border-radius: 8px; font-weight: 600; font-size: 13px;">
                    <i class="ti ti-clock-check me-1 fs-5"></i>
                    <span>Kebijakan Lembur</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-start text-md-center py-2.5 px-3 {{ $activeTab === 'modules' ? 'active' : '' }}" 
                    id="tab-modules-btn" data-bs-toggle="pill" data-bs-target="#tab-modules" type="button" role="tab" aria-controls="tab-modules" aria-selected="{{ $activeTab === 'modules' ? 'true' : 'false' }}"
                    style="border-radius: 8px; font-weight: 600; font-size: 13px;">
                    <i class="ti ti-toggle-right me-1 fs-5"></i>
                    <span>Modul & Fitur HR</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-start text-md-center py-2.5 px-3 {{ $activeTab === 'server' ? 'active' : '' }}" 
                    id="tab-server-btn" data-bs-toggle="pill" data-bs-target="#tab-server" type="button" role="tab" aria-controls="tab-server" aria-selected="{{ $activeTab === 'server' ? 'true' : 'false' }}"
                    style="border-radius: 8px; font-weight: 600; font-size: 13px;">
                    <i class="ti ti-server me-1 fs-5"></i>
                    <span>Server & Sistem</span>
                </button>
            </li>
        </ul>
    </div>
</div>

{{-- Tab Panes Content --}}
<div class="tab-content p-0" id="settingsTabContent">

    {{-- ========================================================================= --}}
    {{-- TAB 1: PROFIL PERUSAHAAN & BRANDING                                       --}}
    {{-- ========================================================================= --}}
    <div class="tab-pane fade {{ $activeTab === 'company' ? 'show active' : '' }}" id="tab-company" role="tabpanel" aria-labelledby="tab-company-btn">
        <form action="{{ route('company_settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- KOLOM 1: IDENTITAS & LEGALITAS PERUSAHAAN -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                        <div class="card-header bg-transparent pb-2 pt-3 border-bottom d-flex align-items-center">
                            <i class="ti ti-id-badge text-primary me-2 fs-5"></i>
                            <h6 class="mb-0 fw-semibold text-dark">Identitas & Legalitas</h6>
                        </div>
                        <div class="card-body pt-3">
                            <x-input-with-icon-label label="Nama Brand / Dagang *" name="company_name" icon="ti ti-building" :value="$companySetting->company_name ?? ''" required="true" />
                            
                            <x-input-with-icon-label label="Nama Badan Hukum (PT/CV/Yayasan)" name="legal_name" icon="ti ti-certificate" :value="$companySetting->legal_name ?? ''" helper="Contoh: PT Sumber Makmur Sentosa" />

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Jenis Industri / Model Usaha *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-category"></i></span>
                                    <select name="business_type" class="form-select" required>
                                        @foreach($businessTypes as $bType)
                                            <option value="{{ $bType }}" {{ ($companySetting->business_type ?? '') == $bType ? 'selected' : '' }}>{{ $bType }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <x-input-with-icon-label label="Nomor Pokok Wajib Pajak (NPWP)" name="npwp" icon="ti ti-receipt-tax" :value="$companySetting->npwp ?? ''" helper="15 atau 16 digit NPWP Badan" />

                            <x-input-with-icon-label label="Nomor Induk Berusaha (NIB)" name="nib" icon="ti ti-file-text" :value="$companySetting->nib ?? ''" />

                            <x-input-file name="logo" label="Logo Perusahaan / Instansi" :value="$companySetting->logo ?? null" helper="Format: PNG, JPG, WEBP (Maks. 2MB)" :crop="true" cropRatio="free" cropShape="rect" />
                        </div>
                    </div>
                </div>

                <!-- KOLOM 2: KONTAK & ALAMAT KANTOR PUSAT -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                        <div class="card-header bg-transparent pb-2 pt-3 border-bottom d-flex align-items-center">
                            <i class="ti ti-map-pin text-primary me-2 fs-5"></i>
                            <h6 class="mb-0 fw-semibold text-dark">Kontak & Kantor Pusat</h6>
                        </div>
                        <div class="card-body pt-3">
                            <x-textarea-label label="Alamat Kantor Pusat" name="address" icon="ti ti-home" :value="$companySetting->address ?? ''" rows="3" />

                            <div class="row">
                                <div class="col-6">
                                    <x-input-with-icon-label label="Provinsi" name="province" icon="ti ti-map" :value="$companySetting->province ?? ''" />
                                </div>
                                <div class="col-6">
                                    <x-input-with-icon-label label="Kota / Kab." name="city" icon="ti ti-building-community" :value="$companySetting->city ?? ''" />
                                </div>
                            </div>

                            <x-input-with-icon-label label="Kode Pos" name="postal_code" icon="ti ti-mailbox" :value="$companySetting->postal_code ?? ''" />

                            <x-input-with-icon-label label="Nomor Telepon / WhatsApp" name="phone" icon="ti ti-phone" :value="$companySetting->phone ?? ''" />

                            <x-input-with-icon-label label="Email Resmi HR / Perusahaan" name="email" icon="ti ti-mail" :value="$companySetting->email ?? ''" type="email" />

                            <x-input-with-icon-label label="Website Perusahaan" name="website" icon="ti ti-world" :value="$companySetting->website ?? ''" helper="Contoh: https://perusahaan.co.id" />
                        </div>
                    </div>
                </div>

                <!-- KOLOM 3: REGIONAL, PAYROLL, & UNIVERSAL BRANDING -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="card h-100 shadow-sm border-0" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                        <div class="card-header bg-transparent pb-2 pt-3 border-bottom d-flex align-items-center">
                            <i class="ti ti-palette text-primary me-2 fs-5"></i>
                            <h6 class="mb-0 fw-semibold text-dark">Regional, Payroll & Branding</h6>
                        </div>
                        <div class="card-body pt-3">
                            <!-- Pengaturan Regional -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Zona Waktu (Timezone) *</label>
                                <select name="timezone" class="form-select" required>
                                    @foreach($timezones as $tzKey => $tzLabel)
                                        <option value="{{ $tzKey }}" {{ ($companySetting->timezone ?? 'Asia/Jakarta') == $tzKey ? 'selected' : '' }}>{{ $tzLabel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Mata Uang *</label>
                                    <select name="currency" class="form-select" required>
                                        @foreach($currencies as $cKey => $cLabel)
                                            <option value="{{ $cKey }}" {{ ($companySetting->currency ?? 'IDR') == $cKey ? 'selected' : '' }}>{{ $cKey }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Format Tanggal *</label>
                                    <select name="date_format" class="form-select" required>
                                        @foreach($dateFormats as $dfKey => $dfLabel)
                                            <option value="{{ $dfKey }}" {{ ($companySetting->date_format ?? 'd-m-Y') == $dfKey ? 'selected' : '' }}>{{ $dfKey }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" name="locale" value="{{ $companySetting->locale ?? 'id' }}">

                            <!-- Siklus Penggajian -->
                            <div class="border rounded p-2.5 mb-3 bg-light">
                                <span class="fw-semibold text-dark d-block mb-2" style="font-size: 12.5px;"><i class="ti ti-calendar-stats me-1 text-primary"></i>Siklus Cutoff & Pembayaran Payroll</span>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label text-muted mb-1" style="font-size: 11.5px;">Tgl Cutoff (1-31)</label>
                                        <input type="number" name="payroll_cutoff_date" min="1" max="31" class="form-control form-control-sm" value="{{ $companySetting->payroll_cutoff_date ?? 20 }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-muted mb-1" style="font-size: 11.5px;">Tgl Gajian (1-31)</label>
                                        <input type="number" name="payroll_payment_date" min="1" max="31" class="form-control form-control-sm" value="{{ $companySetting->payroll_payment_date ?? 25 }}" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Universal Branding -->
                            <x-input-with-icon-label label="Nama Aplikasi (App Display Name) *" name="app_name" icon="ti ti-brand-appgallery" :value="$companySetting->app_name ?? 'Presence'" required="true" />

                            <x-input-with-icon-label label="Tagline Aplikasi" name="app_tagline" icon="ti ti-badge" :value="$companySetting->app_tagline ?? 'Sistem HRIS'" />

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Warna Utama (Primary)</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color w-100" name="theme_color_primary" value="{{ $companySetting->theme_color_primary ?? '#3C2A21' }}" title="Pilih warna utama">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Warna Aksen (Secondary)</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color w-100" name="theme_color_secondary" value="{{ $companySetting->theme_color_secondary ?? '#634832' }}" title="Pilih warna aksen">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="card shadow-sm border-0 mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                <div class="card-body d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 py-3">
                    <span class="text-muted small"><i class="ti ti-info-circle me-1"></i>Perubahan profil perusahaan langsung disinkronkan ke seluruh kop cetak laporan dan antarmuka karyawan.</span>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-device-floppy"></i>
                        <span>Simpan Profil Perusahaan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 2: KEBIJAKAN PRESENSI & GPS                                           --}}
    {{-- ========================================================================= --}}
    <div class="tab-pane fade {{ $activeTab === 'attendance' ? 'show active' : '' }}" id="tab-attendance" role="tabpanel" aria-labelledby="tab-attendance-btn">
        <form action="{{ route('attendance_policy.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0 h-100" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                        <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold text-dark"><i class="ti ti-radar text-primary me-2 fs-5"></i>Validasi & Keamanan Geofencing Presensi</h6>
                            <span class="badge bg-label-info font-mono" style="font-size: 11px;">Radius {{ $attendancePolicy->max_out_of_radius_meters ?? 100 }}m</span>
                        </div>
                        <div class="card-body pt-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark required">Nama Kebijakan Presensi</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $attendancePolicy->name ?? 'Kebijakan Presensi Standar Perusahaan') }}" required>
                            </div>

                            <div class="list-group list-group-flush border rounded-3 overflow-hidden mb-3">
                                <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="fw-semibold text-dark">Validasi Geofencing GPS Radius Cabang</div>
                                        <div class="text-muted small">Karyawan wajib berada dalam jangkauan titik koordinat kantor/outlet yang ditentukan.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="require_gps" value="1" {{ old('require_gps', $attendancePolicy->require_gps ?? true) ? 'checked' : '' }}>
                                    </div>
                                </label>

                                <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="fw-semibold text-dark">Face Recognition AI (Biometrik Wajah)</div>
                                        <div class="text-muted small">Mencocokkan biometrik wajah karyawan secara real-time untuk mencegah kecurangan titip absen.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="require_face_recognition" value="1" {{ old('require_face_recognition', $attendancePolicy->require_face_recognition ?? true) ? 'checked' : '' }}>
                                    </div>
                                </label>

                                <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="fw-semibold text-dark">Wajib Foto Bukti Presensi (Selfie Langsung)</div>
                                        <div class="text-muted small">Kamera depan aktif otomatis dan mengambil bukti visual saat absensi masuk/pulang.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="require_photo" value="1" {{ old('require_photo', $attendancePolicy->require_photo ?? true) ? 'checked' : '' }}>
                                    </div>
                                </label>

                                <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="fw-semibold text-dark">Izinkan Presensi Di Luar Radius (Toleransi Dinas Luar)</div>
                                        <div class="text-muted small">Memperbolehkan clock-in di luar radius dengan status khusus tercatat di sistem.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="allow_out_of_radius" value="1" {{ old('allow_out_of_radius', $attendancePolicy->allow_out_of_radius ?? false) ? 'checked' : '' }}>
                                    </div>
                                </label>

                                <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="fw-semibold text-dark">Dukungan Shift Lintas Hari (Overnight Roster)</div>
                                        <div class="text-muted small">Mendukung shift malam melewati pergantian hari (pukul 00:00) tanpa dianggap alpa.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="allow_overnight" value="1" {{ old('allow_overnight', $attendancePolicy->allow_overnight ?? true) ? 'checked' : '' }}>
                                    </div>
                                </label>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-dark required">Toleransi Keterlambatan (Menit)</label>
                                    <div class="input-group">
                                        <input type="number" name="allow_late_tolerance_minutes" class="form-control font-mono" value="{{ old('allow_late_tolerance_minutes', $attendancePolicy->allow_late_tolerance_minutes ?? 15) }}" min="0" max="120" required>
                                        <span class="input-group-text">Menit</span>
                                    </div>
                                    <div class="form-text text-muted">Batas menit kedatangan setelah jam masuk tanpa potongan kedisiplinan.</div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold text-dark required">Radius Toleransi GPS Maksimal (Meter)</label>
                                    <div class="input-group">
                                        <input type="number" name="max_out_of_radius_meters" class="form-control font-mono" value="{{ old('max_out_of_radius_meters', $attendancePolicy->max_out_of_radius_meters ?? 100) }}" min="10" max="1000" required>
                                        <span class="input-group-text">Meter</span>
                                    </div>
                                    <div class="form-text text-muted">Jarak radius maksimal dari titik GPS cabang (standar: 50 - 150 meter).</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                        <div class="card-header bg-transparent py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold text-dark"><i class="ti ti-notes text-primary me-2 fs-5"></i>Catatan & Kebijakan Khusus</h6>
                        </div>
                        <div class="card-body pt-3 d-flex flex-column">
                            <div class="mb-3 flex-grow-1">
                                <label class="form-label fw-bold text-dark">Keterangan / Panduan Internal</label>
                                <textarea name="description" class="form-control h-100" rows="8" placeholder="Masukkan instruksi tata tertib absensi bagi karyawan...">{{ old('description', $attendancePolicy->description ?? '') }}</textarea>
                            </div>
                            <div class="p-3 bg-light rounded-2 border">
                                <span class="d-block fw-semibold text-dark mb-1" style="font-size: 12.5px;"><i class="ti ti-shield-lock me-1 text-success"></i>Integritas GPS Terlindungi</span>
                                <span class="text-muted small">Sistem otomatis menolak fake GPS / mock location provider dari perangkat seluler karyawan.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="card shadow-sm border-0 mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                <div class="card-body d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 py-3">
                    <span class="text-muted small"><i class="ti ti-info-circle me-1"></i>Kebijakan baru akan langsung diaktifkan untuk seluruh validasi presensi di mesin mobile & web scanner.</span>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-device-floppy"></i>
                        <span>Simpan Kebijakan Presensi</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 3: KEBIJAKAN LEMBUR                                                   --}}
    {{-- ========================================================================= --}}
    <div class="tab-pane fade {{ $activeTab === 'overtime' ? 'show active' : '' }}" id="tab-overtime" role="tabpanel" aria-labelledby="tab-overtime-btn">
        <div class="row g-4">
            <div class="col-12 col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                    <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="ti ti-calculator text-primary me-2 fs-5"></i>Parameter Regulasi Lembur (PP 35/2021)</h6>
                        <span class="badge bg-light text-dark font-mono border" style="font-size: 11px;">
                            {{ $overtimePolicy->code ?? 'PP-35-2021' }}
                        </span>
                    </div>
                    <div class="card-body pt-3">
                        <form action="{{ route('overtime_policy.update', $overtimePolicy->id ?? 1) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase;">Nama Skema Kebijakan</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $overtimePolicy->name ?? 'Standar Depnaker (PP 35/2021)') }}" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase;">Maks. Lembur / Hari (Jam)</label>
                                    <input type="number" step="0.5" name="max_hours_per_day" class="form-control font-mono" value="{{ old('max_hours_per_day', $overtimePolicy->max_hours_per_day ?? 4.0) }}" required>
                                    <span class="text-muted" style="font-size: 11px;">Maksimal PP 35/2021: 4 jam/hari</span>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase;">Maks. Lembur / Pekan (Jam)</label>
                                    <input type="number" step="0.5" name="max_hours_per_week" class="form-control font-mono" value="{{ old('max_hours_per_week', $overtimePolicy->max_hours_per_week ?? 18.0) }}" required>
                                    <span class="text-muted" style="font-size: 11px;">Maksimal PP 35/2021: 18 jam/pekan</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase;">Hak Makanan/Minuman (&ge;1.400 kkal)</label>
                                <select name="requires_meal_allowance_after_4h" class="form-select">
                                    <option value="1" {{ ($overtimePolicy->requires_meal_allowance_after_4h ?? true) ? 'selected' : '' }}>Wajib untuk lembur 4 jam atau lebih (Standar Depnaker)</option>
                                    <option value="0" {{ !($overtimePolicy->requires_meal_allowance_after_4h ?? true) ? 'selected' : '' }}>Nonaktifkan pengecekan</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase;">Status Kebijakan</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ ($overtimePolicy->is_active ?? true) ? 'selected' : '' }}>Aktif (Default Sistem)</option>
                                    <option value="0" {{ !($overtimePolicy->is_active ?? true) ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4 fw-semibold d-inline-flex align-items-center gap-1.5">
                                    <i class="ti ti-device-floppy"></i>
                                    <span>Simpan Kebijakan Lembur</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h6 class="mb-0 fw-semibold text-dark"><i class="ti ti-scale text-primary me-2 fs-5"></i>Formula Resmi Pengali Upah Lembur</h6>
                    </div>
                    <div class="card-body pt-3">
                        <div class="alert alert-info py-2 px-3 mb-3 border-0" style="border-radius: 8px; background: rgba(115, 103, 240, 0.08); color: #7367f0; font-size: 12.5px;">
                            <i class="ti ti-info-circle me-1"></i> Formula upah per jam resmi Depnaker = <strong>Upah Sebulan / 173</strong>.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" style="font-size: 12.5px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis Hari</th>
                                        <th>Jam Ke-</th>
                                        <th>Pengali (Multiplier)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td rowspan="2" class="fw-semibold align-middle">Hari Kerja Biasa</td>
                                        <td>Jam ke-1</td>
                                        <td><span class="badge bg-label-primary font-mono">1.5 &times; Upah/Jam</span></td>
                                    </tr>
                                    <tr>
                                        <td>Jam ke-2 s/d selesai</td>
                                        <td><span class="badge bg-label-primary font-mono">2.0 &times; Upah/Jam</span></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="3" class="fw-semibold align-middle">Hari Libur / Akhir Pekan</td>
                                        <td>Jam ke-1 s/d ke-7 atau ke-8</td>
                                        <td><span class="badge bg-label-warning font-mono">2.0 &times; Upah/Jam</span></td>
                                    </tr>
                                    <tr>
                                        <td>Jam ke-8 (atau ke-9)</td>
                                        <td><span class="badge bg-label-warning font-mono">3.0 &times; Upah/Jam</span></td>
                                    </tr>
                                    <tr>
                                        <td>Jam ke-9+ s/d selesai</td>
                                        <td><span class="badge bg-label-danger font-mono">4.0 &times; Upah/Jam</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 4: MODUL & FITUR HR                                                   --}}
    {{-- ========================================================================= --}}
    <div class="tab-pane fade {{ $activeTab === 'modules' ? 'show active' : '' }}" id="tab-modules" role="tabpanel" aria-labelledby="tab-modules-btn">
        <!-- STATS SUMMARY CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-xs border-0" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-medium">Modul Termasuk (Entitled)</span>
                            <h3 class="fw-bold mb-0 font-mono text-dark">{{ $totalEntitled }}</h3>
                        </div>
                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 42px; height: 42px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                            <i class="ti ti-package fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-xs border-0" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-medium">Modul Aktif Beroperasi</span>
                            <h3 class="fw-bold mb-0 font-mono text-success">{{ $totalActive }}</h3>
                        </div>
                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 42px; height: 42px; background: rgba(74, 103, 65, 0.1); color: #4A6741;">
                            <i class="ti ti-circle-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-xs border-0" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-medium">Modul Opsional (Dapat Diubah)</span>
                            <h3 class="fw-bold mb-0 font-mono text-primary">{{ $activeToggleable->count() + $disabledToggleable->count() }}</h3>
                        </div>
                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 42px; height: 42px; background: rgba(14, 165, 233, 0.1); color: #0284C7;">
                            <i class="ti ti-adjustments-horizontal fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 1: MODUL OPSIONAL AKTIF -->
        <div class="card shadow-xs border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 12px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="ti ti-toggle-right text-success fs-5"></i>
                        Modul Fungsional Aktif Beroperasi
                    </h6>
                    <small class="text-muted">Modul opsional yang saat ini dapat diakses oleh admin dan karyawan.</small>
                </div>
                <span class="badge bg-label-success font-mono">{{ $activeToggleable->count() }} Modul</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr style="font-size: 12px; text-transform: uppercase;">
                                <th class="ps-3 py-2.5">Modul & Fungsi</th>
                                <th class="py-2.5">Kategori</th>
                                <th class="py-2.5 text-center" style="width: 140px;">Status</th>
                                <th class="py-2.5 text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeToggleable as $m)
                            <tr>
                                <td class="ps-3 py-3">
                                    <div class="fw-semibold text-dark">{{ $m->module_name }}</div>
                                    <div class="text-muted small">{{ $m->description ?? 'Modul fungsional operasional HR.' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary font-mono" style="font-size: 11px;">{{ $m->category }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success" style="font-size: 11px; padding: 4px 10px; border-radius: 20px;">Aktif</span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('module_features.toggle', $m->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1" onclick="return confirm('Nonaktifkan modul {{ $m->module_name }}?')">
                                            <i class="ti ti-power"></i>
                                            <span>Nonaktifkan</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Tidak ada modul opsional aktif.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECTION 2: MODUL NONAKTIF -->
        @if($disabledToggleable->isNotEmpty())
        <div class="card shadow-xs border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 12px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="ti ti-toggle-left text-muted fs-5"></i>
                        Modul Tersedia Tetapi Dinonaktifkan
                    </h6>
                    <small class="text-muted">Aktifkan kembali modul berikut jika bisnis Anda membutuhkannya.</small>
                </div>
                <span class="badge bg-label-secondary font-mono">{{ $disabledToggleable->count() }} Modul</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr style="font-size: 12px; text-transform: uppercase;">
                                <th class="ps-3 py-2.5">Modul & Fungsi</th>
                                <th class="py-2.5">Kategori</th>
                                <th class="py-2.5 text-center" style="width: 140px;">Status</th>
                                <th class="py-2.5 text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($disabledToggleable as $m)
                            <tr class="bg-light opacity-75">
                                <td class="ps-3 py-3">
                                    <div class="fw-semibold text-dark">{{ $m->module_name }}</div>
                                    <div class="text-muted small">{{ $m->description ?? 'Modul fungsional operasional HR.' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary font-mono" style="font-size: 11px;">{{ $m->category }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary" style="font-size: 11px; padding: 4px 10px; border-radius: 20px;">Nonaktif</span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('module_features.toggle', $m->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1">
                                            <i class="ti ti-power"></i>
                                            <span>Aktifkan</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- SECTION 3: CORE ARCHITECTURAL MODULES (LOCKED) -->
        <div class="card shadow-xs border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 12px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="ti ti-shield-lock text-primary fs-5"></i>
                        Modul Arsitektur Inti (Core Engine)
                    </h6>
                    <small class="text-muted">Fondasi vital sistem yang senantiasa aktif demi stabilitas database dan otentikasi.</small>
                </div>
                <span class="badge bg-label-primary font-mono">{{ $coreModules->count() }} Modul Inti</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    @foreach($coreModules as $m)
                    <div class="col-md-6 col-lg-4">
                        <div class="p-2.5 rounded-2 border d-flex align-items-center justify-content-between bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-lock text-muted fs-6"></i>
                                <span class="fw-semibold text-dark" style="font-size: 12.5px;">{{ $m->module_name }}</span>
                            </div>
                            <span class="badge bg-label-success" style="font-size: 10px;">Inti Wajib</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 5: SERVER & SISTEM                                                    --}}
    {{-- ========================================================================= --}}
    <div class="tab-pane fade {{ $activeTab === 'server' ? 'show active' : '' }}" id="tab-server" role="tabpanel" aria-labelledby="tab-server-btn">
        <form action="{{ route('generalsetting.update', Crypt::encrypt($generalSetting->id ?? 1)) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Kolom 1: Autentikasi, Email & Jadwal Kerja -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                        <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center">
                            <i class="ti ti-shield-lock text-primary me-2 fs-5"></i>
                            <h6 class="mb-0 fw-semibold text-dark">Autentikasi, Sesi & Domain Email</h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Sistem Hari Kerja</label>
                                    <select name="sistem_hari_kerja" class="form-select" required>
                                        <option value="5" {{ ($generalSetting->sistem_hari_kerja ?? 5) == 5 ? 'selected' : '' }}>5 Hari Kerja (Senin - Jumat)</option>
                                        <option value="6" {{ ($generalSetting->sistem_hari_kerja ?? 5) == 6 ? 'selected' : '' }}>6 Hari Kerja (Senin - Sabtu)</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Session Timeout (Hari)</label>
                                    <input type="number" name="session_time" class="form-control" value="{{ $generalSetting->session_time ?? 7 }}" min="1" max="90" required>
                                    <div class="form-text text-muted">Durasi aktif login admin & karyawan.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Filter Domain Email Karyawan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-at"></i></span>
                                    <input type="text" name="domain_email" class="form-control" value="{{ $generalSetting->domain_email ?? 'gmail.com' }}" placeholder="Contoh: perusahaan.com">
                                </div>
                                <div class="form-text text-muted">Membatasi pendaftaran akun karyawan hanya dengan domain email resmi perusahaan ini.</div>
                            </div>

                            <div class="p-3 bg-light rounded-2 border mt-3">
                                <span class="d-block fw-semibold text-dark mb-1" style="font-size: 12.5px;"><i class="ti ti-info-circle me-1 text-primary"></i>Informasi Keamanan Sesi</span>
                                <span class="text-muted small">Session timeout mengontrol berapa lama sesi autentikasi tetap tersimpan sebelum pengguna diwajibkan login ulang demi keamanan data.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom 2: Periode Laporan & Pemeliharaan Server -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                        <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center">
                            <i class="ti ti-server-cog text-primary me-2 fs-5"></i>
                            <h6 class="mb-0 fw-semibold text-dark">Periode Laporan & Utilitas Server</h6>
                        </div>
                        <div class="card-body pt-3">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <x-input-with-icon-label label="Periode Laporan Dari" icon="ti ti-calendar" name="periode_laporan_dari" :value="$generalSetting->periode_laporan_dari ?? '21'" required="true" />
                                </div>
                                <div class="col-6">
                                    <x-input-with-icon-label label="Periode Laporan Sampai" icon="ti ti-calendar" name="periode_laporan_sampai" :value="$generalSetting->periode_laporan_sampai ?? '20'" required="true" />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Metode Periode Lintas Bulan</label>
                                <select name="periode_laporan_next_bulan" class="form-select">
                                    <option value="0" {{ ($generalSetting->periode_laporan_next_bulan ?? 0) == 0 ? 'selected' : '' }}>Satu Bulan Kalender Sama (1 - 31)</option>
                                    <option value="1" {{ ($generalSetting->periode_laporan_next_bulan ?? 0) == 1 ? 'selected' : '' }}>Bulan Sebelumnya ke Bulan Berjalan (Contoh: 21 Apr - 20 Mei)</option>
                                    <option value="2" {{ ($generalSetting->periode_laporan_next_bulan ?? 0) == 2 ? 'selected' : '' }}>Bulan Berjalan ke Bulan Berikutnya (Contoh: 2 Mei - 1 Juni)</option>
                                </select>
                            </div>

                            <input type="hidden" name="nama_aplikasi" value="{{ $generalSetting->nama_aplikasi ?? 'Presence' }}">
                            <input type="hidden" name="nama_perusahaan" value="{{ $generalSetting->nama_perusahaan ?? 'Perusahaan' }}">
                            <input type="hidden" name="alamat" value="{{ $generalSetting->alamat ?? '-' }}">
                            <input type="hidden" name="telepon" value="{{ $generalSetting->telepon ?? '-' }}">
                            <input type="hidden" name="total_jam_bulan" value="{{ $generalSetting->total_jam_bulan ?? 173 }}">
                            <input type="hidden" name="timezone" value="{{ $generalSetting->timezone ?? 'Asia/Jakarta' }}">

                            <!-- Server Maintenance Tools -->
                            <div class="p-3 bg-light rounded-2 border mt-4">
                                <span class="d-block fw-semibold text-dark mb-2" style="font-size: 12.5px;"><i class="ti ti-tools me-1 text-primary"></i>Server Diagnostics & Hosting Tools</span>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('generalsetting.hosting-readiness') }}" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1">
                                        <i class="ti ti-device-heart-monitor"></i>
                                        <span>Cek Hosting Readiness</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="card shadow-sm border-0 mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08) !important; border-radius: 12px;">
                <div class="card-body d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 py-3">
                    <span class="text-muted small"><i class="ti ti-info-circle me-1"></i>Pengaturan sesi login, domain email, dan periode laporan akan langsung diterapkan ke sistem.</span>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-device-floppy"></i>
                        <span>Simpan Konfigurasi Server</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sync active tab with browser URL query string ?tab=...
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam) {
            const targetBtn = document.querySelector('#tab-' + tabParam + '-btn');
            if (targetBtn) {
                const tabInstance = new bootstrap.Tab(targetBtn);
                tabInstance.show();
            }
        }

        // On tab switch, smoothly update URL query string without reloading page
        document.querySelectorAll('#settingsTab button[data-bs-toggle="pill"]').forEach(function(button) {
            button.addEventListener('shown.bs.tab', function(e) {
                const tabName = e.target.id.replace('tab-', '').replace('-btn', '');
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('tab', tabName);
                window.history.replaceState({}, '', currentUrl);
            });
        });
    });
</script>
@endsection
