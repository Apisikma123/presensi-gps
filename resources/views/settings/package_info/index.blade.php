@extends('layouts.app')
@section('titlepage', 'Informasi Paket & Lisensi Deployment')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.hub') }}">Pengaturan Sistem</a></li>
    <li class="breadcrumb-item active">Informasi Paket</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Informasi Paket & Lisensi Deployment</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 4px 12px;">
                {{ $package['category'] }}
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Rincian hak akses modul, profil lisensi instalasi, dan status tata kelola deployment Presence.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalUpgradeRequest" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-arrow-up-circle" style="font-size: 16px;"></i>
            <span>Minta Upgrade Paket</span>
        </button>
        <a href="{{ route('module_features.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-arrow-left" style="font-size: 16px;"></i>
            <span>Kontrol Modul</span>
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- LEFT: PACKAGE CARD & METADATA -->
    <div class="col-lg-4">
        <div class="card shadow-xs border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 14px; background: #FFFFFF;">
            <div class="card-body p-4 text-center border-bottom">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                    style="width: 64px; height: 64px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                    <i class="ti ti-certificate fs-1"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $package['name'] }}</h5>
                <span class="badge bg-light text-muted border font-mono px-3 py-1 mb-2">{{ $package['code'] }}</span>
                <p class="text-muted small mb-0">{{ $package['description'] }}</p>
            </div>
            <div class="card-body p-3 bg-light" style="border-radius: 0 0 14px 14px;">
                <ul class="list-unstyled mb-0" style="font-size: 13px;">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Kategori Paket:</span>
                        <span class="fw-bold text-dark">{{ $package['category'] }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Deployment ID:</span>
                        <span class="font-mono text-dark fw-bold">{{ $deploymentId }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Status Profil:</span>
                        @if($isLocked)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="ti ti-lock me-1"></i> Terkunci (Stabil)
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                <i class="ti ti-lock-open me-1"></i> Terbuka
                            </span>
                        @endif
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Model Lisensi:</span>
                        <span class="text-dark fw-medium">Dedicated On-Premise / Shared</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- ADD-ONS CARD -->
        <div class="card shadow-xs border-0" style="border: 1px solid #E2E8F0 !important; border-radius: 14px; background: #FFFFFF;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-puzzle text-primary"></i> Add-on Tambahan
                </h6>
                <span class="badge bg-light text-dark border">{{ count($addons) }} Aktif</span>
            </div>
            <div class="card-body p-3">
                @if(count($addons) > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($addons as $addon)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 font-mono">
                                + {{ ucfirst(str_replace('_', ' ', $addon)) }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small mb-0 text-center py-2">Tidak ada add-on kustom yang dibeli di luar paket dasar.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- RIGHT: DETAILED ENTITLED MODULES BREAKDOWN -->
    <div class="col-lg-8">
        <div class="card shadow-xs border-0" style="border: 1px solid #E2E8F0 !important; border-radius: 14px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="ti ti-list-check text-success"></i> Rincian Hak Akses Modul (Entitlements)
                    </h6>
                    <span class="text-muted small">Daftar fitur yang secara resmi diizinkan beroperasi pada instalasi ini.</span>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 fw-bold">
                    {{ count($coreList) + count($purchasedList) }} Modul Berhak
                </span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748B;">
                            <th class="ps-3 py-2.5">Modul Fitur</th>
                            <th class="py-2.5">Tipe Hak Akses</th>
                            <th class="py-2.5">Status Berjalan</th>
                            <th class="text-end pe-3 py-2.5">Kontrol Klien</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Core Modules -->
                        @foreach($coreList as $core)
                        <tr class="bg-light/40">
                            <td class="ps-3 py-2.5">
                                <div class="fw-bold text-dark">{{ $core['name'] }}</div>
                                <div class="text-muted small font-mono">{{ $core['code'] }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border">Core System</span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="ti ti-check me-1"></i> Aktif Permanen
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <span class="text-muted small">Terkunci Sistem</span>
                            </td>
                        </tr>
                        @endforeach

                        <!-- Purchased Modules -->
                        @foreach($purchasedList as $p)
                        <tr>
                            <td class="ps-3 py-2.5">
                                <div class="fw-bold text-dark">
                                    {{ $p['name'] }}
                                    @if($p['is_addon'])
                                        <span class="badge bg-info-subtle text-info border border-info-subtle ms-1" style="font-size: 10px;">Add-on</span>
                                    @endif
                                </div>
                                <div class="text-muted small font-mono">{{ $p['code'] }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Termasuk Paket</span>
                            </td>
                            <td>
                                @if($p['is_enabled'])
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="ti ti-check me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">
                                        <i class="ti ti-x me-1"></i> Dinonaktifkan
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                @if($p['is_toggleable'])
                                    <span class="badge bg-light text-dark border">
                                        <i class="ti ti-toggle-right me-1 text-primary"></i> Dapat Diubah
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">
                                        <i class="ti ti-lock me-1"></i> Terkunci Kontrak
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- UPGRADE REQUEST MODAL -->
<div class="modal fade" id="modalUpgradeRequest" tabindex="-1" aria-labelledby="modalUpgradeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalUpgradeLabel">
                    <i class="ti ti-arrow-up-circle text-primary"></i> Permintaan Peningkatan Paket
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-3 mb-3" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                    <div class="small text-muted mb-1">Paket Berjalan Saat Ini:</div>
                    <div class="fw-bold text-dark font-mono fs-6">{{ $package['name'] }} ({{ $package['code'] }})</div>
                </div>

                <p class="text-muted small">
                    Presence menganut arsitektur deployment terisolasi mandiri (non-SaaS). Perubahan paket lisensi atau penambahan modul add-on (seperti Penggajian Statutori, PPh 21, BPJS, atau Face Biometrics) dilakukan melalui prosedur teknis resmi deployment vendor.
                </p>

                <div class="card p-3 bg-light border-0 mb-3" style="border-radius: 10px;">
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 13.5px;">Saluran Resmi Layanan Klien:</h6>
                    <div class="d-flex align-items-center gap-2 mb-2 text-dark small">
                        <i class="ti ti-mail text-primary fs-5"></i>
                        <span>Email: <a href="mailto:sales@presence-hr.id" class="fw-bold text-primary">sales@presence-hr.id</a></span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-dark small">
                        <i class="ti ti-brand-whatsapp text-success fs-5"></i>
                        <span>WhatsApp Deployment: <span class="fw-bold text-success">+62 812-3456-7890</span></span>
                    </div>
                </div>

                <div class="text-muted small">
                    <i class="ti ti-info-circle me-1"></i> Data historis absensi dan profil perusahaan Anda akan 100% dipertahankan selama proses peningkatan paket berlangsung.
                </div>
            </div>
            <div class="modal-footer border-top py-2.5">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Tutup</button>
                <a href="https://wa.me/6281234567890?text=Halo%20Tim%20Presence,%20kami%20ingin%20berkonsultasi%20upgrade%20paket%20lisensi%20Presence%20(Deployment%20ID:%20{{ $deploymentId }})"
                   target="_blank"
                   class="btn btn-success d-inline-flex align-items-center gap-1.5 px-3">
                    <i class="ti ti-brand-whatsapp"></i> Hubungi via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
