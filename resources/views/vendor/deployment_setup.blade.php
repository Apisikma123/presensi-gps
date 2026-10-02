@extends('layouts.app')
@section('titlepage', 'Developer Deployment Setup (Vendor Only)')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-11">
        <!-- HEADER -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h4 class="page-title mb-0 fw-bold text-dark">Developer Deployment Setup</h4>
                    <span class="badge bg-dark font-mono px-2.5 py-1 text-white" style="font-size: 11px;">VENDOR CONSOLE</span>
                    @if($isLocked)
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11px;">
                            <i class="ti ti-lock me-1"></i> Deployment Terkunci
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1" style="font-size: 11px;">
                            <i class="ti ti-lock-open me-1"></i> Deployment Terbuka
                        </span>
                    @endif
                </div>
                <p class="text-muted small mb-0">
                    Konsol operasional deployment vendor untuk inisialisasi paket klien pada lingkungan shared-hosting tanpa akses SSH.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('settings.hub') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1.5" style="border-radius: 8px;">
                    <i class="ti ti-arrow-left"></i> Kembali ke Sistem
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 shadow-xs" role="alert" style="border-radius: 10px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-circle-check fs-4"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-xs" role="alert" style="border-radius: 10px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-alert-triangle fs-4"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- LEFT COLUMN: STATUS & AUDIT HISTORY -->
            <div class="col-lg-4">
                <!-- CURRENT DEPLOYMENT STATUS -->
                <div class="card shadow-xs border-0 mb-4" style="border: 1px solid #E2E8F0 !important; border-radius: 14px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="ti ti-shield-check text-primary"></i> Status Deployment Klien
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-0" style="font-size: 13px;">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Paket Aktif:</span>
                                <span class="fw-bold font-mono text-dark">{{ $currentPackage['code'] }}</span>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Nama Lisensi:</span>
                                <span class="fw-bold text-dark">{{ $currentPackage['name'] }}</span>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Preset Bawaan:</span>
                                <span class="badge bg-light text-dark border font-mono">Preset {{ $activePreset }}</span>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Status Proteksi:</span>
                                <span class="badge {{ $isLocked ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} border">
                                    {{ $isLocked ? 'Terkunci (Aman)' : 'Terbuka' }}
                                </span>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span class="text-muted">Arsitektur:</span>
                                <span class="text-dark fw-medium">Dedicated Non-SaaS</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- AUDIT TRAIL PREVIEW -->
                <div class="card shadow-xs border-0" style="border: 1px solid #E2E8F0 !important; border-radius: 14px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="ti ti-history text-secondary"></i> Log Deployment Terakhir
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        @if($history->count() > 0)
                            <div class="timeline-feed">
                                @foreach($history as $h)
                                    <div class="mb-3 pb-2 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-primary-subtle text-primary font-mono" style="font-size: 10px;">{{ $h->new_package_code }}</span>
                                            <span class="text-muted" style="font-size: 11px;">{{ \Carbon\Carbon::parse($h->created_at)->diffForHumans() }}</span>
                                        </div>
                                        <div class="text-dark small fw-medium">{{ $h->notes ?: 'Perubahan paket lisensi' }}</div>
                                        <div class="text-muted" style="font-size: 11px;">Oleh: {{ $h->actor_name }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small mb-0 text-center py-3">Belum ada riwayat perubahan deployment.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: PACKAGE ASSIGNMENT FORM & LIVE PREVIEW -->
            <div class="col-lg-8">
                <div class="card shadow-xs border-0" style="border: 1px solid #E2E8F0 !important; border-radius: 14px;">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="ti ti-adjustments-horizontal text-primary"></i> Penugasan Paket Lisensi Klien
                        </h6>
                        <span class="text-muted small">Pilih paket lisensi. Sistem akan otomatis menetapkan entitlements, menerapkan preset bisnis, dan mengunci deployment dalam 1 langkah.</span>
                    </div>
                    <div class="card-body p-4">
                        <form id="formDeploymentPackage" action="{{ route('vendor.deployment.apply') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark" style="font-size: 13.5px;">
                                    Pilih Paket Deployment Lisensi:
                                </label>
                                <select id="selectTargetPackage" name="target_package" class="form-select form-select-lg" style="border-radius: 10px; font-size: 14.5px;">
                                    @foreach($allPackages as $code => $pkg)
                                        <option value="{{ $code }}" {{ $currentPackage['code'] === $code ? 'selected' : '' }}>
                                            {{ $pkg['name'] }} ({{ $code }}) — Preset {{ $packagePresetMap[$code] ?? 'Auto' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text mt-1 text-muted" style="font-size: 12px;">
                                    Setiap paket memiliki batas modul resmi (entitlement) dan template alur kerja bawaan.
                                </div>
                            </div>

                            <!-- LIVE PREVIEW CONTAINER -->
                            <div id="previewCard" class="card p-3 mb-4" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;">
                                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                    <div class="fw-bold text-dark small d-flex align-items-center gap-1.5">
                                        <i class="ti ti-eye text-primary"></i>
                                        <span>Preview Hasil Perubahan:</span>
                                    </div>
                                    <span id="previewActionBadge" class="badge bg-secondary-subtle text-secondary font-mono" style="font-size: 11px;">
                                        MEMUAT...
                                    </span>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <div class="small text-muted mb-0.5">Preset Bawaan Otomatis:</div>
                                        <div id="previewDefaultPreset" class="fw-bold font-mono text-dark">-</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="small text-muted mb-0.5">Jaminan Data Historis:</div>
                                        <div class="fw-bold text-success small d-flex align-items-center gap-1">
                                            <i class="ti ti-shield-check"></i>
                                            <span>100% Utuh (Zero Data Loss)</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <div class="small text-muted mb-1">Modul Tambahan Baru ([+]):</div>
                                    <div id="previewAddedModules" class="small text-success font-mono bg-white p-2 rounded border">-</div>
                                </div>

                                <div>
                                    <div class="small text-muted mb-1">Akses Dinonaktifkan ([-] Data Tetap Utuh):</div>
                                    <div id="previewRemovedModules" class="small text-muted font-mono bg-white p-2 rounded border">-</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small mb-1">Catatan Audit Deployment (Opsional):</label>
                                <input type="text" name="notes" class="form-control form-control-sm" placeholder="Contoh: Inisialisasi awal lisensi klien cPanel" style="border-radius: 8px;">
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2">
                                <div class="text-muted small">
                                    <i class="ti ti-lock me-1"></i> Deployment akan otomatis dikunci setelah proses selesai.
                                </div>
                                <button type="button" id="btnApplyPackage" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2" style="border-radius: 10px; font-weight: 600;">
                                    <i class="ti ti-check"></i>
                                    <span>Terapkan Paket & Kunci Deployment</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectPackage = document.getElementById('selectTargetPackage');
    const badgeAction = document.getElementById('previewActionBadge');
    const txtPreset = document.getElementById('previewDefaultPreset');
    const boxAdded = document.getElementById('previewAddedModules');
    const boxRemoved = document.getElementById('previewRemovedModules');
    const btnApply = document.getElementById('btnApplyPackage');
    const formApply = document.getElementById('formDeploymentPackage');

    function fetchPreview(packageCode) {
        badgeAction.textContent = 'Memuat...';
        badgeAction.className = 'badge bg-secondary-subtle text-secondary font-mono';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route('vendor.deployment.preview') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                target_package: packageCode
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.preview) {
                const p = data.preview;
                badgeAction.textContent = p.action;
                badgeAction.className = p.action === 'UPGRADE' 
                    ? 'badge bg-success-subtle text-success border border-success-subtle font-mono' 
                    : (p.action === 'DOWNGRADE' ? 'badge bg-warning-subtle text-warning border border-warning-subtle font-mono' : 'badge bg-secondary-subtle text-secondary border font-mono');

                txtPreset.textContent = 'Preset ' + p.default_preset;

                if (p.added_entitlements && p.added_entitlements.length > 0) {
                    boxAdded.textContent = '+ ' + p.added_entitlements.join(', ');
                    boxAdded.className = 'small text-success font-mono bg-white p-2 rounded border';
                } else {
                    boxAdded.textContent = 'Tidak ada penambahan modul baru.';
                    boxAdded.className = 'small text-muted font-mono bg-white p-2 rounded border';
                }

                if (p.removed_entitlements && p.removed_entitlements.length > 0) {
                    boxRemoved.textContent = '- ' + p.removed_entitlements.join(', ') + ' (Data historis tetap aman)';
                    boxRemoved.className = 'small text-danger font-mono bg-white p-2 rounded border';
                } else {
                    boxRemoved.textContent = 'Tidak ada modul yang dikurangi.';
                    boxRemoved.className = 'small text-muted font-mono bg-white p-2 rounded border';
                }
            }
        })
        .catch(err => {
            badgeAction.textContent = 'ERROR';
        });
    }

    if (selectPackage) {
        selectPackage.addEventListener('change', function () {
            fetchPreview(this.value);
        });
        // Initial preview load
        fetchPreview(selectPackage.value);
    }

    if (btnApply) {
        btnApply.addEventListener('click', function () {
            const targetPkg = selectPackage.value;
            Swal.fire({
                title: 'Terapkan Paket Deployment?',
                text: `Sistem akan otomatis mengatur hak akses modul paket '${targetPkg}', menerapkan konfigurasi bisnis preset, dan mengunci deployment. Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: (getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '#3C2A21'),
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Ya, Terapkan Sekarang',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menerapkan Paket...',
                        text: 'Menyinkronkan modul, preset, dan mengunci deployment...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    formApply.submit();
                }
            });
        });
    }
});
</script>
@endpush
