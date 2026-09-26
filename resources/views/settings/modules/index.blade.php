@extends('layouts.app')
@section('titlepage', 'Kontrol Modul & Paket Lisensi')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.hub') }}">Pengaturan Sistem</a></li>
    <li class="breadcrumb-item active">Kontrol Modul</li>
@endsection

@section('content')
@php
    $currentPackage = $currentPackage ?? app(\App\Services\ModuleEntitlementService::class)->getCurrentPackage();
    $isLocked = $isLocked ?? app(\App\Services\ModuleEntitlementService::class)->isDeploymentLocked();
    $totalEntitled = $totalEntitled ?? ($totalCount ?? 0);
    $totalActive = $totalActive ?? ($enabledCount ?? 0);
    $totalUnpurchased = $totalUnpurchased ?? 0;
    $coreModules = $coreModules ?? collect([]);
    $activeToggleable = $activeToggleable ?? ($modules ?? collect([]))->filter(fn($m) => $m->is_enabled);
    $disabledToggleable = $disabledToggleable ?? ($modules ?? collect([]))->filter(fn($m) => !$m->is_enabled);
    $vendorLockedModules = $vendorLockedModules ?? collect([]);
@endphp
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Kontrol Modul & Fitur Paket</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 4px 12px;">
                Paket: {{ $currentPackage['name'] }}
            </span>
            @if($isLocked)
                <span class="badge bg-light text-secondary border" title="Profil deployment telah dikunci untuk integritas sistem">
                    <i class="ti ti-lock me-1"></i> Terkunci
                </span>
            @endif
        </div>
        <p class="page-subtitle text-muted mb-0">Kelola aktivasi modul yang termasuk dalam paket lisensi resmi Presence untuk perusahaan Anda.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('settings.package_info.index') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-certificate" style="font-size: 16px;"></i>
            <span>Info Lisensi & Paket</span>
        </a>
        <a href="{{ route('settings.hub') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-arrow-left" style="font-size: 16px;"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

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

<!-- SECTION 1: CORE ARCHITECTURAL MODULES (CORE_LOCKED) -->
<div class="card shadow-xs border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 12px;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div>
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="ti ti-cpu text-primary"></i> Modul Inti Sistem (Core Architectural)
            </h6>
            <span class="text-muted small">Modul pondasi dasar Presence. Selalu aktif demi integritas struktural aplikasi.</span>
        </div>
        <span class="badge bg-light text-dark border px-2 py-1">
            <i class="ti ti-lock me-1"></i> Wajib & Permanen
        </span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748B;">
                    <th class="ps-3 py-2.5">Modul</th>
                    <th class="py-2.5">Kategori</th>
                    <th class="py-2.5">Ketergantungan (Dependency)</th>
                    <th class="py-2.5">Status</th>
                    <th class="text-end pe-3 py-2.5">Akses Kontrol</th>
                </tr>
            </thead>
            <tbody>
                @foreach($coreModules as $mod)
                <tr>
                    <td class="ps-3 py-3">
                        <div class="fw-bold text-dark">{{ $mod->module_name }}</div>
                        <div class="text-muted small font-mono">{{ $mod->module_code }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-secondary border">{{ $mod->category }}</span>
                    </td>
                    <td>
                        <span class="text-muted small"><i class="ti ti-minus me-1"></i>Sistem Mandiri</span>
                    </td>
                    <td>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                            <i class="ti ti-check me-1"></i> Aktif Permanen
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <span class="text-muted small fw-medium" title="Modul inti tidak memiliki tombol nonaktif">
                            <i class="ti ti-lock text-muted me-1"></i> Dikelola Sistem
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- SECTION 2: CLIENT-TOGGLEABLE PURCHASED MODULES -->
<div class="card shadow-xs border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 12px;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div>
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="ti ti-toggle-right text-success"></i> Modul Opsional Terbeli (Client-Toggleable)
            </h6>
            <span class="text-muted small">Modul yang termasuk dalam paket pembelian Anda dan dapat Anda aktifkan/nonaktifkan sesuai alur kerja.</span>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
            Super Admin Dapat Mengubah
        </span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748B;">
                    <th class="ps-3 py-2.5">Nama Fitur</th>
                    <th class="py-2.5">Kategori</th>
                    <th class="py-2.5">Ketergantungan (Prasyarat)</th>
                    <th class="py-2.5">Status Saat Ini</th>
                    <th class="text-end pe-3 py-2.5">Aksi Sakelar</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $allToggleable = $activeToggleable->merge($disabledToggleable);
                @endphp
                @forelse($allToggleable as $mod)
                @php
                    $dep = \App\Services\ModuleEntitlementService::$dependencies[$mod->module_code] ?? null;
                    $depMet = $dep ? is_module_enabled($dep) : true;
                @endphp
                <tr>
                    <td class="ps-3 py-3">
                        <div class="fw-bold text-dark">{{ $mod->module_name }}</div>
                        <div class="text-muted small">{{ $mod->description ?: 'Modul fitur terintegrasi Presence.' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-secondary border">{{ $mod->category }}</span>
                    </td>
                    <td>
                        @if($dep)
                            @if($depMet)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="ti ti-check me-1"></i> Membutuhkan {{ ucfirst($dep) }} (Tersedia)
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="ti ti-alert-triangle me-1"></i> Wajib Aktifkan {{ ucfirst($dep) }}
                                </span>
                            @endif
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        @if($mod->is_enabled)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="ti ti-circle-check me-1"></i> Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                <i class="ti ti-circle-x me-1"></i> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        <button type="button"
                            class="btn btn-sm {{ $mod->is_enabled ? 'btn-outline-danger' : 'btn-outline-success' }} btn-toggle-module"
                            data-id="{{ $mod->id }}"
                            data-code="{{ $mod->module_code }}"
                            data-name="{{ $mod->module_name }}"
                            data-current="{{ $mod->is_enabled ? '1' : '0' }}"
                            data-dep="{{ $dep ?? '' }}"
                            style="border-radius: 8px; font-weight: 600; font-size: 12px; padding: 5px 12px;">
                            <i class="ti {{ $mod->is_enabled ? 'ti-power' : 'ti-player-play' }} me-1"></i>
                            {{ $mod->is_enabled ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted small">
                        Tidak ada modul opsional tambahan dalam paket ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- SECTION 3: VENDOR-LOCKED PURCHASED MODULES -->
@if($vendorLockedModules->count() > 0)
<div class="card shadow-xs border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 12px;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div>
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="ti ti-shield-lock text-info"></i> Modul Terbeli Terkunci Kebijakan (Vendor/Deployment Locked)
            </h6>
            <span class="text-muted small">Modul ini termasuk dalam paket Anda namun dikunci aktif demi kesinambungan kalkulasi operasional/hukum.</span>
        </div>
        <span class="badge bg-light text-secondary border">
            Dikelola Kontrak Paket
        </span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748B;">
                    <th class="ps-3 py-2.5">Modul</th>
                    <th class="py-2.5">Kategori</th>
                    <th class="py-2.5">Status</th>
                    <th class="text-end pe-3 py-2.5">Kebijakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vendorLockedModules as $mod)
                <tr>
                    <td class="ps-3 py-3">
                        <div class="fw-bold text-dark">{{ $mod->module_name }}</div>
                        <div class="text-muted small font-mono">{{ $mod->module_code }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-secondary border">{{ $mod->category }}</span>
                    </td>
                    <td>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            <i class="ti ti-check me-1"></i> Aktif Operasional
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <span class="badge bg-light text-muted border">
                            <i class="ti ti-lock me-1"></i> Locked By Deployment
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- SECTION 4: UNPURCHASED SUMMARY BANNER -->
@if($totalUnpurchased > 0)
<div class="card shadow-xs border-0 p-3" style="border: 1px dashed #CBD5E1 !important; border-radius: 12px; background: #F8FAFC;">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-white border shadow-xs" style="width: 44px; height: 44px; color: #64748B;">
                <i class="ti ti-sparkles fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0">Terdapat {{ $totalUnpurchased }} Modul Enterprise Tambahan di Luar Paket Saat Ini</h6>
                <p class="text-muted small mb-0">Fitur seperti Penggajian Statutori, PPh 21 TER, BPJS, Rekrutmen, dan Talent Governance tersedia jika perusahaan membutuhkan peningkatan kapasitas.</p>
            </div>
        </div>
        <a href="{{ route('settings.package_info.index') }}" class="btn btn-sm btn-outline-primary text-nowrap" style="border-radius: 8px; font-weight: 600; padding: 6px 14px;">
            <i class="ti ti-info-circle me-1"></i> Lihat Informasi Paket
        </a>
    </div>
</div>
@endif

<!-- CSRF FORM FOR TOGGLE -->
<form id="form-toggle-module" method="POST" style="display: none;">
    @csrf
</form>
@endsection

@push('myscript')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '#3C2A21';

    document.querySelectorAll('.btn-toggle-module').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const code = this.getAttribute('data-code');
            const name = this.getAttribute('data-name');
            const isCurrentlyEnabled = this.getAttribute('data-current') === '1';
            const dep = this.getAttribute('data-dep');

            let title = isCurrentlyEnabled ? `Nonaktifkan Modul ${name}?` : `Aktifkan Modul ${name}?`;
            let text = isCurrentlyEnabled 
                ? `Fitur ini akan dinonaktifkan sementara dari sistem. Seluruh data historis tetap tersimpan utuh dan TIDAK akan dihapus.`
                : `Modul akan diaktifkan dan dapat langsung diakses oleh pengguna yang memiliki hak akses berwenang.`;

            if (dep && !isCurrentlyEnabled) {
                text += `\n(Catatan: Pastikan modul prasyarat '${dep}' telah beroperasi normal).`;
            }

            Swal.fire({
                title: title,
                text: text,
                icon: isCurrentlyEnabled ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: primaryColor,
                cancelButtonColor: '#64748B',
                confirmButtonText: isCurrentlyEnabled ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-primary px-4 py-2',
                    cancelButton: 'btn btn-secondary px-4 py-2 me-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Memperbarui status modul sistem...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    const url = `{{ url('/settings/modules/toggle') }}/${id}`;
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonColor: primaryColor
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Gagal mengubah status modul.',
                                confirmButtonColor: primaryColor
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menghubungi server.',
                            confirmButtonColor: primaryColor
                        });
                    });
                }
            });
        });
    });
});
</script>
@endpush
