@extends('layouts.app')
@section('titlepage', 'Pusat Pengaturan Sistem')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pusat Pengaturan Sistem</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Pusat Direktori Pengaturan</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                Universal Hub
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Konfigurasi sentral identitas perusahaan, modul fungsional, kepatuhan undang-undang, dan keamanan sistem.</p>
    </div>
</div>

{{-- Overview Stats Card --}}
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-label-primary font-mono mb-2" style="font-size: 11px;">{{ company_setting('app_name', 'PRESENCE') }} v2.0</span>
                <h4 class="fw-bold text-dark mb-1">{{ company_setting('company_name', 'Perusahaan') }}</h4>
                <div class="text-muted small">Zona Waktu Operasional: <span class="font-mono text-dark fw-medium">{{ company_setting('timezone', 'Asia/Jakarta') }}</span></div>
            </div>
            <div class="d-flex gap-4">
                <div class="text-center">
                    <div class="fs-3 fw-bold font-mono text-primary">{{ $enabledModules }} / {{ $totalModules }}</div>
                    <div class="text-muted small">Modul Aktif</div>
                </div>
                <div class="text-center">
                    <div class="fs-3 fw-bold font-mono text-success">{{ $activeRulesCount }}</div>
                    <div class="text-muted small">Aturan Kepatuhan</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Settings Cards Grid --}}
<div class="row g-3">
    {{-- 1. Identitas Perusahaan --}}
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                        <i class="ti ti-building fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Profil & Branding</h6>
                        <span class="badge bg-label-success" style="font-size: 10px;">Terkonfigurasi</span>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1 mb-3">
                    Nama instansi, logo resmi, alamat kantor pusat, email korespondensi, dan zona waktu operasional.
                </p>
                <div class="pt-3 border-top d-flex justify-content-end">
                    <a href="{{ route('company_settings.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-settings" style="font-size: 14px;"></i>
                        <span>Buka Pengaturan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Modul & Fitur (Feature Flags) --}}
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(74, 103, 65, 0.1); color: #4A6741;">
                        <i class="ti ti-toggle-right fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Modul & Fitur HR</h6>
                        <span class="badge bg-label-primary font-mono" style="font-size: 10px;">{{ $enabledModules }} Aktif</span>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1 mb-3">
                    Saklar (toggle) on/off modul sistem: Rekrutmen, Payroll, Kasbon, Lembur, Aset, Pelatihan, dan Disiplin.
                </p>
                <div class="pt-3 border-top d-flex justify-content-end">
                    <a href="{{ route('module_features.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-apps" style="font-size: 14px;"></i>
                        <span>Kelola Modul</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Kebijakan Presensi & GPS --}}
    @if(module_enabled('attendance'))
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(234, 84, 85, 0.1); color: #ea5455;">
                        <i class="ti ti-map-pin fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Kebijakan Presensi & GPS</h6>
                        <span class="badge bg-label-info font-mono" style="font-size: 10px;">Radius {{ $attendancePolicy->default_radius_meters ?? 100 }}m</span>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1 mb-3">
                    Radius toleransi geofencing kantor, deteksi biometrik wajah, auto-alpha harian, dan kebijakan keterlambatan.
                </p>
                <div class="pt-3 border-top d-flex justify-content-end">
                    <a href="{{ route('attendance_policy.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-radar" style="font-size: 14px;"></i>
                        <span>Atur Kebijakan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 4. Kebijakan Lembur Depnaker --}}
    @if(module_enabled('overtime'))
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(255, 159, 67, 0.12); color: #ff9f43;">
                        <i class="ti ti-clock-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Kebijakan Lembur & Upah</h6>
                        <span class="badge bg-label-success" style="font-size: 10px;">PP 35/2021 Aktif</span>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1 mb-3">
                    Pengali upah lembur resmi Permenaker & PP 35/2021, dasar pembagi 1/173, dan aturan pembulatan menit lembur.
                </p>
                <div class="pt-3 border-top d-flex justify-content-end">
                    <a href="{{ route('overtime_policy.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-calculator" style="font-size: 14px;"></i>
                        <span>Atur Lembur</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 5. Kepatuhan PPh 21 TER & BPJS --}}
    @if(module_enabled('payroll'))
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(115, 103, 240, 0.1); color: #7367f0;">
                        <i class="ti ti-shield-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Kepatuhan Pajak & BPJS</h6>
                        <span class="badge bg-label-primary font-mono" style="font-size: 10px;">TER 2024</span>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1 mb-3">
                    Tarif efektif rata-rata (TER) PPh 21 PP 58/2023, iuran BPJS Ketenagakerjaan (JKK, JKM, JHT, JP), dan BPJS Kesehatan.
                </p>
                <div class="pt-3 border-top d-flex justify-content-end">
                    <a href="{{ route('compliance.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-receipt-tax" style="font-size: 14px;"></i>
                        <span>Buka Kepatuhan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 6. Pengguna & Hak Akses --}}
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(30, 41, 59, 0.08); color: #1e293b;">
                        <i class="ti ti-lock-access fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Pengguna & Hak Akses</h6>
                        <span class="badge bg-label-secondary font-mono" style="font-size: 10px;">Spatie RBAC</span>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1 mb-3">
                    Manajemen akun user administrator, role Spatie, dan matriks hak akses fungsional per divisi kerja.
                </p>
                <div class="pt-3 border-top d-flex justify-content-end">
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-user-check" style="font-size: 14px;"></i>
                        <span>Kelola User</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 7. Log Jejak Audit (Audit Trail) --}}
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                        <i class="ti ti-history fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Log Jejak Audit</h6>
                        <span class="badge bg-label-success" style="font-size: 10px;">Audit Trail Aktif</span>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1 mb-3">
                    Pencatatan riwayat setiap aksi administratif, perubahan data karyawan, persetujuan payroll, dan aktivitas sistem.
                </p>
                <div class="pt-3 border-top d-flex justify-content-end">
                    <a href="{{ route('settings.audit_logs.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-file-text" style="font-size: 14px;"></i>
                        <span>Buka Audit Trail</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 8. Informasi Paket & Lisensi (Jika Terkunci) atau Matriks Preset Klien (Jika Onboarding) --}}
    @php
        $isDeploymentLocked = app(\App\Services\ModuleEntitlementService::class)->isDeploymentLocked();
    @endphp
    @if($isDeploymentLocked)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                        <i class="ti ti-box fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Informasi Paket Lisensi</h6>
                        <span class="badge bg-label-info font-mono" style="font-size: 10px;">Deployment Terkunci</span>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1 mb-3">
                    Rincian paket Presence perusahaan, modul inti aktif, modul opsional, serta status hak akses sistem.
                </p>
                <div class="pt-3 border-top d-flex justify-content-end">
                    <a href="{{ route('settings.package_info.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-info-circle" style="font-size: 14px;"></i>
                        <span>Lihat Paket Lisensi</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: var(--bs-primary-bg-subtle, rgba(var(--bs-primary-rgb, 60, 42, 33), 0.1)); color: var(--color-primary, #3C2A21);">
                        <i class="ti ti-layout-grid fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Matriks Preset Klien</h6>
                        <span class="badge bg-label-primary font-mono" style="font-size: 10px;">Preset A — E</span>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1 mb-3">
                    Pengalihan profil bisnis instan: Cafe/F&B, Kantor Korporasi, Retail Multi-Cabang, Jasa/Konsultan, atau Remote HR.
                </p>
                <div class="pt-3 border-top d-flex justify-content-end">
                    <a href="{{ route('settings.presets.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5">
                        <i class="ti ti-adjustments" style="font-size: 14px;"></i>
                        <span>Buka Preset Matrix</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
