@extends('layouts.app')
@section('titlepage', 'Matriks Preset Klien')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.hub') }}">Pengaturan Sistem</a></li>
    <li class="breadcrumb-item active">Matriks Preset Klien</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0" style="font-family: 'Outfit', sans-serif; font-weight: 700; color: var(--theme-text-primary, #1A1C1C);">
                Matriks Preset Klien (A — E)
            </h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(60, 42, 33, 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                5 Preset Standar
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0" style="font-size: 13.5px;">Konfigurasi cepat arsitektur modular adaptif untuk berbagai lanskap bisnis dan sektor industri.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('settings.hub') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-arrow-left" style="font-size: 16px;"></i>
            <span>Kembali ke Direktori</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert" style="border-radius: 8px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #15803d;">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <span style="font-size: 13.5px; font-weight: 500;">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible mb-4" role="alert" style="border-radius: 8px; border: 1px solid #fecdd3; background: #fef2f2; color: #ba1a1a;">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle fs-5 me-2"></i>
            <span style="font-size: 13.5px; font-weight: 500;">{{ session('error') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@push('mystyle')
<style>
    .preset-card {
        border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.08)) !important;
        border-radius: 14px !important;
        background: #FFFFFF !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease !important;
        overflow: hidden;
        position: relative;
    }
    .preset-card:hover {
        transform: translateY(-2px);
        border-color: var(--color-primary) !important;
        box-shadow: 0 8px 24px rgba(var(--bs-primary-rgb), 0.08) !important;
    }
    .preset-card-active {
        border: 2px solid var(--color-primary) !important;
        box-shadow: 0 4px 16px rgba(var(--bs-primary-rgb), 0.12) !important;
    }
    .preset-btn-apply {
        background: var(--color-primary) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        height: 42px !important;
        border-radius: 9px !important;
        border: none !important;
        box-shadow: 0 2px 4px rgba(var(--bs-primary-rgb), 0.15) !important;
        transition: all 0.15s ease !important;
    }
    .preset-btn-apply:hover {
        background: var(--color-primary-hover, var(--theme-color-2)) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(var(--bs-primary-rgb), 0.25) !important;
    }
    .preset-btn-apply:active {
        transform: translateY(1px);
    }
    .badge-preset-code {
        background: var(--color-primary) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0.04em !important;
        padding: 4px 9px !important;
        border-radius: 6px !important;
    }
    .badge-preset-category {
        background: var(--color-primary-soft, rgba(60, 42, 33, 0.08)) !important;
        color: var(--theme-text-secondary, #755841) !important;
        border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.15)) !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        padding: 3.5px 8px !important;
        border-radius: 6px !important;
    }
    .badge-preset-universal {
        background: rgba(74, 103, 65, 0.08) !important;
        color: #2E4D26 !important;
        border: 1px solid rgba(74, 103, 65, 0.2) !important;
        font-size: 10.5px !important;
        font-weight: 600 !important;
        padding: 3px 8px !important;
        border-radius: 6px !important;
        font-family: 'JetBrains Mono', monospace !important;
    }
    .badge-mod-active {
        background: rgba(74, 103, 65, 0.08) !important;
        color: #2E4D26 !important;
        border: 1px solid rgba(74, 103, 65, 0.2) !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        padding: 3px 8px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
    }
    .badge-mod-inactive {
        background: #F8FAFC !important;
        color: #94A3B8 !important;
        border: 1px solid #E2E8F0 !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        padding: 3px 8px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
    }
</style>
@endpush

@php
    $activePresetCode = $activePresetCode ?? (app(\App\Services\ClientPresetService::class)->getActivePresetCode());
    $isDeploymentLocked = $isLocked ?? (app(\App\Services\ModuleEntitlementService::class)->isDeploymentLocked());
@endphp

@if($isDeploymentLocked)
<div class="alert d-flex align-items-center mb-4" style="background: rgba(234, 84, 85, 0.08); border: 1px solid rgba(234, 84, 85, 0.25); border-radius: 12px; color: #b91c1c;">
    <i class="ti ti-lock fs-4 me-3 flex-shrink-0"></i>
    <div>
        <strong class="d-block mb-1">Profil Deployment Terkunci (Production Safe)</strong>
        <span style="font-size: 13px;">Matriks preset di bawah ditampilkan hanya untuk keperluan audit dan referensi tata kelola. Pergantian preset dinonaktifkan untuk melindungi integritas sistem klien. Perubahan paket hanya dapat dilakukan oleh vendor melalui mode pemeliharaan deployment.</span>
    </div>
</div>
@endif

{{-- Intro Banner Card --}}
<div class="card mb-4" style="border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.08)); border-radius: 14px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-body p-4">
        <div class="row align-items-center gy-3">
            <div class="col-md-9">
                <h5 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif; color: var(--theme-text-primary, #1A1C1C);">Profil Konfigurasi Adaptif Otomatis</h5>
                <p class="text-muted mb-0" style="font-size: 13.5px; line-height: 1.6;">
                    PRESENCE dirancang secara universal untuk beroperasi di berbagai lanskap bisnis Indonesia tanpa perubahan kode (zero code modifications). Pilih salah satu preset di bawah untuk mengaktifkan fitur dan alur kerja yang relevan secara instan.
                </p>
            </div>
            <div class="col-md-3 text-md-end">
                <span class="badge" style="background: var(--color-primary-soft, rgba(60, 42, 33, 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.16)); font-size: 11.5px; font-weight: 600; padding: 6px 12px; border-radius: 20px;">
                    <i class="ti ti-layers-subtract me-1"></i> Preset Aktif: <strong>Preset {{ $activePresetCode ?? 'A' }}</strong>
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Preset Cards Grid --}}
<div class="row g-3">
    @foreach($presets as $code => $preset)
        @php
            $isCurrentActive = ($activePresetCode === $code);
        @endphp
        <div class="col-lg-6 col-xl-4">
            <div class="card h-100 d-flex flex-column preset-card {{ $isCurrentActive ? 'preset-card-active' : '' }}">
                <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center" style="background: #FFFFFF; border-bottom: 1px solid var(--theme-border, rgba(60, 42, 33, 0.07));">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge-preset-code">
                            PRESET {{ $code }}
                        </span>
                        <span class="badge badge-preset-category font-mono">
                            {{ $preset['badge'] }}
                        </span>
                    </div>
                    @if($isCurrentActive)
                        <span class="badge bg-success text-white d-inline-flex align-items-center gap-1.5" style="font-size: 10.5px; font-weight: 700; padding: 4px 9px; border-radius: 6px;">
                            <span class="d-inline-block rounded-circle bg-white" style="width: 6px; height: 6px;"></span>
                            Aktif Sekarang
                        </span>
                    @else
                        <span class="badge badge-preset-universal">
                            <i class="ti ti-check me-1" style="font-size: 10px;"></i>Universal HR
                        </span>
                    @endif
                </div>
                <div class="card-body flex-grow-1 p-4">
                    <h5 class="card-title fw-bold mb-1.5" style="font-family: 'Outfit', sans-serif; font-size: 17px; color: var(--theme-text-primary, #1A1C1C);">
                        {{ $preset['name'] }}
                    </h5>
                    <p class="text-muted mb-3" style="font-size: 12.5px; line-height: 1.55; min-height: 40px;">
                        {{ $preset['subtitle'] }}
                    </p>

                    <div class="mb-3">
                        <div class="fw-bold mb-2" style="font-size: 11px; letter-spacing: 0.05em; text-transform: uppercase; color: #475569;">
                            Modul Aktif
                        </div>
                        <div class="d-flex flex-wrap gap-1.5">
                            @foreach($preset['enabled_modules'] as $mod)
                                <span class="badge badge-mod-active">
                                    <i class="ti ti-plus" style="font-size: 9.5px; stroke-width: 3;"></i>
                                    <span>{{ ucfirst(str_replace('_', ' ', $mod)) }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>

                    @if(!empty($preset['disabled_modules']))
                        <div class="mb-3">
                            <div class="fw-bold mb-2" style="font-size: 11px; letter-spacing: 0.05em; text-transform: uppercase; color: #94A3B8;">
                                Modul Nonaktif
                            </div>
                            <div class="d-flex flex-wrap gap-1.5">
                                @foreach($preset['disabled_modules'] as $mod)
                                    <span class="badge badge-mod-inactive">
                                        <i class="ti ti-minus" style="font-size: 9.5px;"></i>
                                        <span>{{ ucfirst(str_replace('_', ' ', $mod)) }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="p-3 mt-3" style="background: #FDFCFB; border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.08)); border-radius: 10px;">
                        <div class="d-flex align-items-center gap-1.5 mb-2">
                            <i class="ti ti-shield-check" style="color: #4A6741; font-size: 14px;"></i>
                            <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: var(--theme-text-secondary, #634832);">
                                Kriteria Pengujian (Must Work)
                            </span>
                        </div>
                        <div class="d-flex flex-column gap-1.5">
                            @foreach($preset['must_work'] as $mw)
                                <div class="d-flex align-items-start gap-2" style="font-size: 12px; color: #334155; line-height: 1.45;">
                                    <i class="ti ti-check" style="color: #4A6741; font-size: 12.5px; stroke-width: 2.5; margin-top: 2px; flex-shrink: 0;"></i>
                                    <span>{{ $mw }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card-footer p-3 bg-white" style="border-top: 1px solid var(--theme-border, rgba(60, 42, 33, 0.06));">
                    @if($isCurrentActive)
                        <button type="button" class="btn w-100 d-inline-flex align-items-center justify-content-center gap-2" disabled style="height: 42px; border-radius: 9px; font-weight: 700; background: rgba(74, 103, 65, 0.1); color: #2E4D26; border: 1px solid rgba(74, 103, 65, 0.25); cursor: default;">
                            <i class="ti ti-circle-check fs-5"></i>
                            <span>Preset Sedang Aktif</span>
                        </button>
                    @elseif($isDeploymentLocked)
                        <button type="button" class="btn w-100 d-inline-flex align-items-center justify-content-center gap-2" disabled style="height: 42px; border-radius: 9px; font-weight: 600; background: #F8FAFC; color: #94A3B8; border: 1px solid #E2E8F0; cursor: not-allowed;">
                            <i class="ti ti-lock" style="font-size: 15px;"></i>
                            <span>Terkunci (Mode Produksi)</span>
                        </button>
                    @else
                        <form action="{{ route('settings.presets.apply') }}" method="POST" class="form-preset-apply">
                            @csrf
                            <input type="hidden" name="preset_code" value="{{ $code }}">
                            <button type="button" class="btn w-100 preset-btn-apply btn-apply-preset d-inline-flex align-items-center justify-content-center gap-2" data-preset-name="{{ $preset['name'] }}">
                                <i class="ti ti-check" style="font-size: 16px;"></i>
                                <span>Terapkan Preset {{ $code }}</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('myscript')
<script>
    $(document).on('click', '.btn-apply-preset', function(e) {
        e.preventDefault();
        var btn = $(this);
        var form = btn.closest('form');
        var presetName = btn.data('preset-name') || 'Preset';
        var themePrimary = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '#3C2A21';

        Swal.fire({
            title: "Terapkan " + presetName + "?",
            text: "Tindakan ini akan menyesuaikan modul dan pengaturan sistem.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: themePrimary,
            cancelButtonColor: "#64748B",
            confirmButtonText: "Ya, Terapkan",
            cancelButtonText: "Batal",
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status"></span> Menerapkan...');
                form.submit();
            }
        });
    });
</script>
@endpush
