@extends('layouts.app')
@section('titlepage', 'Profil Perusahaan & Universal Branding')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.hub') }}">Pengaturan Sistem</a></li>
    <li class="breadcrumb-item active">Profil Perusahaan</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Profil Perusahaan & Universal Branding</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                Identitas Resmi
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Konfigurasi identitas perusahaan, legalitas, regional, dan branding universal untuk sistem HR.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('module_features.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-toggle-left" style="font-size: 16px;"></i>
            <span>Fitur & Modul</span>
        </a>
        <a href="{{ route('generalsetting.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-settings" style="font-size: 16px;"></i>
            <span>Pengaturan GPS</span>
        </a>
    </div>
</div>

<form action="{{ route('company_settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- KOLOM 1: IDENTITAS & LEGALITAS PERUSAHAAN -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-transparent pb-1 pt-3 border-bottom d-flex align-items-center">
                    <i class="ti ti-id-badge text-primary me-2 fs-5"></i>
                    <h6 class="mb-0 fw-semibold">Identitas & Legalitas</h6>
                </div>
                <div class="card-body pt-3">
                    <x-input-with-icon-label label="Nama Brand / Dagang *" name="company_name" icon="ti ti-building" :value="$setting->company_name ?? ''" required="true" />
                    
                    <x-input-with-icon-label label="Nama Badan Hukum (PT/CV/Yayasan)" name="legal_name" icon="ti ti-certificate" :value="$setting->legal_name ?? ''" helper="Contoh: PT Sumber Makmur Sentosa" />

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Jenis Industri / Model Usaha *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-category"></i></span>
                            <select name="business_type" class="form-select" required>
                                @foreach($businessTypes as $bType)
                                    <option value="{{ $bType }}" {{ ($setting->business_type ?? '') == $bType ? 'selected' : '' }}>{{ $bType }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <x-input-with-icon-label label="Nomor Pokok Wajib Pajak (NPWP)" name="npwp" icon="ti ti-receipt-tax" :value="$setting->npwp ?? ''" helper="15 atau 16 digit NPWP Badan" />

                    <x-input-with-icon-label label="Nomor Induk Berusaha (NIB)" name="nib" icon="ti ti-file-text" :value="$setting->nib ?? ''" />

                    <x-input-file name="logo" label="Logo Perusahaan / Instansi" :value="$setting->logo" helper="Format: PNG, JPG, WEBP (Maks. 2MB)" :crop="true" cropRatio="free" cropShape="rect" />
                </div>
            </div>
        </div>

        <!-- KOLOM 2: KONTAK & ALAMAT KANTOR PUSAT -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-transparent pb-1 pt-3 border-bottom d-flex align-items-center">
                    <i class="ti ti-map-pin text-primary me-2 fs-5"></i>
                    <h6 class="mb-0 fw-semibold">Kontak & Kantor Pusat</h6>
                </div>
                <div class="card-body pt-3">
                    <x-textarea-label label="Alamat Kantor Pusat" name="address" icon="ti ti-home" :value="$setting->address ?? ''" rows="3" />

                    <div class="row">
                        <div class="col-6">
                            <x-input-with-icon-label label="Provinsi" name="province" icon="ti ti-map" :value="$setting->province ?? ''" />
                        </div>
                        <div class="col-6">
                            <x-input-with-icon-label label="Kota / Kab." name="city" icon="ti ti-building-community" :value="$setting->city ?? ''" />
                        </div>
                    </div>

                    <x-input-with-icon-label label="Kode Pos" name="postal_code" icon="ti ti-mailbox" :value="$setting->postal_code ?? ''" />

                    <x-input-with-icon-label label="Nomor Telepon / WhatsApp" name="phone" icon="ti ti-phone" :value="$setting->phone ?? ''" />

                    <x-input-with-icon-label label="Email Resmi HR / Perusahaan" name="email" icon="ti ti-mail" :value="$setting->email ?? ''" type="email" />

                    <x-input-with-icon-label label="Website Perusahaan" name="website" icon="ti ti-world" :value="$setting->website ?? ''" helper="Contoh: https://perusahaan.co.id" />
                </div>
            </div>
        </div>

        <!-- KOLOM 3: REGIONAL, PAYROLL, & UNIVERSAL BRANDING -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-transparent pb-1 pt-3 border-bottom d-flex align-items-center">
                    <i class="ti ti-palette text-primary me-2 fs-5"></i>
                    <h6 class="mb-0 fw-semibold">Regional, Payroll & Branding</h6>
                </div>
                <div class="card-body pt-3">
                    <!-- Pengaturan Regional -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Zona Waktu (Timezone) *</label>
                        <select name="timezone" class="form-select" required>
                            @foreach($timezones as $tzKey => $tzLabel)
                                <option value="{{ $tzKey }}" {{ ($setting->timezone ?? 'Asia/Jakarta') == $tzKey ? 'selected' : '' }}>{{ $tzLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Mata Uang *</label>
                            <select name="currency" class="form-select" required>
                                @foreach($currencies as $cKey => $cLabel)
                                    <option value="{{ $cKey }}" {{ ($setting->currency ?? 'IDR') == $cKey ? 'selected' : '' }}>{{ $cKey }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Format Tanggal *</label>
                            <select name="date_format" class="form-select" required>
                                @foreach($dateFormats as $dfKey => $dfLabel)
                                    <option value="{{ $dfKey }}" {{ ($setting->date_format ?? 'd-m-Y') == $dfKey ? 'selected' : '' }}>{{ $dfKey }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="locale" value="{{ $setting->locale ?? 'id' }}">

                    <!-- Siklus Penggajian -->
                    <div class="border rounded p-2.5 mb-3 bg-light">
                        <span class="fw-semibold text-dark d-block mb-2" style="font-size: 12.5px;"><i class="ti ti-calendar-stats me-1 text-primary"></i>Siklus Cutoff & Pembayaran Payroll</span>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label text-muted mb-1" style="font-size: 11.5px;">Tgl Cutoff (1-31)</label>
                                <input type="number" name="payroll_cutoff_date" min="1" max="31" class="form-control form-control-sm" value="{{ $setting->payroll_cutoff_date ?? 20 }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted mb-1" style="font-size: 11.5px;">Tgl Gajian (1-31)</label>
                                <input type="number" name="payroll_payment_date" min="1" max="31" class="form-control form-control-sm" value="{{ $setting->payroll_payment_date ?? 25 }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Universal Branding -->
                    <x-input-with-icon-label label="Nama Aplikasi (App Display Name) *" name="app_name" icon="ti ti-brand-appgallery" :value="$setting->app_name ?? 'Presence'" required="true" />

                    <x-input-with-icon-label label="Tagline Aplikasi" name="app_tagline" icon="ti ti-badge" :value="$setting->app_tagline ?? 'Universal HR Management System'" />

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Warna Utama (Primary)</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color w-100" name="theme_color_primary" value="{{ $setting->theme_color_primary ?? '#3C2A21' }}" title="Pilih warna utama">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-muted" style="font-size: 12.5px;">Warna Aksen (Secondary)</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color w-100" name="theme_color_secondary" value="{{ $setting->theme_color_secondary ?? '#634832' }}" title="Pilih warna aksen">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Simpan -->
    <div class="row">
        <div class="col-12 mb-5">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center py-2.5">
                    <span class="text-muted" style="font-size: 13px;"><i class="ti ti-info-circle me-1"></i>Perubahan akan langsung diperbarui ke seluruh kop laporan, aplikasi mobile ESS, dan sistem navigasi.</span>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy me-1"></i>Simpan Perubahan Profil Perusahaan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
