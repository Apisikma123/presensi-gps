@extends('layouts.app')
@section('titlepage', 'Kebijakan & Pengali Lembur')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.hub') }}">Pengaturan Sistem</a></li>
    <li class="breadcrumb-item active">Kebijakan Lembur</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Kebijakan & Pengali Lembur</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                PP 35/2021
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Parameter batas regulasi jam kerja lembur, tunjangan makan, dan formula resmi pengali upah.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-arrow-left" style="font-size: 16px;"></i>
            <span>Kembali ke SPK Lembur</span>
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert" style="border-radius: 8px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #15803d;">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <span style="font-size: 13.5px;">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    {{-- Policy Settings Form --}}
    <div class="col-12 col-lg-6">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); overflow: hidden;">
            <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center" style="background: #FAF9F8; border-bottom: 1px solid #F1F5F9;">
                <h5 class="card-title fw-bold text-dark mb-0">Parameter Batas Regulasi</h5>
                <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                    {{ $policy->code }}
                </span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('overtime_policy.update', $policy->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase;">
                            Nama Skema Kebijakan
                        </label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $policy->name) }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase;">
                                Maks. Lembur / Hari (Jam)
                            </label>
                            <input type="number" step="0.5" name="max_hours_per_day" class="form-control font-mono"
                                value="{{ old('max_hours_per_day', $policy->max_hours_per_day) }}" required>
                            <span class="text-muted" style="font-size: 11px;">Maksimal PP 35/2021: 4 jam/hari</span>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase;">
                                Maks. Lembur / Pekan (Jam)
                            </label>
                            <input type="number" step="0.5" name="max_hours_per_week" class="form-control font-mono"
                                value="{{ old('max_hours_per_week', $policy->max_hours_per_week) }}" required>
                            <span class="text-muted" style="font-size: 11px;">Maksimal PP 35/2021: 18 jam/pekan</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase;">
                            Hak Makanan/Minuman (&ge;1.400 kkal)
                        </label>
                        <select name="requires_meal_allowance_after_4h" class="form-select">
                            <option value="1" {{ $policy->requires_meal_allowance_after_4h ? 'selected' : '' }}>Wajib untuk lembur 4 jam atau lebih (Standar Depnaker)</option>
                            <option value="0" {{ !$policy->requires_meal_allowance_after_4h ? 'selected' : '' }}>Nonaktifkan pengecekan</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark" style="font-size: 12px; text-transform: uppercase;">
                            Status Kebijakan
                        </label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ $policy->is_active ? 'selected' : '' }}>Aktif (Default)</option>
                            <option value="0" {{ !$policy->is_active ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    @can('overtime_policy.edit')
                        <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1.5">
                            <i class="ti ti-device-floppy"></i>
                            <span>Simpan Kebijakan</span>
                        </button>
                    @endcan
                </form>
            </div>
        </div>
    </div>

    {{-- Indonesian Statutory Rules Reference --}}
    <div class="col-12 col-lg-6">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02); overflow: hidden;">
            <div class="card-header py-3 px-4" style="background: #FAF9F8; border-bottom: 1px solid #F1F5F9;">
                <h5 class="card-title fw-bold text-dark mb-0">Tabel Formula Pengali Resmi (PP 35/2021)</h5>
            </div>
            <div class="card-body p-4">
                {{-- 1. Hari Kerja --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark" style="font-size: 13px;">A. Lembur Pada Hari Kerja Biasa</span>
                        <span class="badge bg-label-primary font-mono">WORKDAY</span>
                    </div>
                    <div class="border rounded-2 overflow-hidden" style="border-color: #F1F5F9 !important;">
                        <table class="table table-sm table-striped mb-0" style="font-size: 12px;">
                            <thead>
                                <tr style="background: #F8FAFC;">
                                    <th class="ps-3 py-2 text-muted">JAM KERJA LEMBUR</th>
                                    <th class="pe-3 py-2 text-end text-muted">FAKTOR PENGALI UPAH</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-3 py-2">Jam pertama (ke-1)</td>
                                    <td class="pe-3 py-2 text-end fw-bold font-mono text-dark">1.5x upah per jam</td>
                                </tr>
                                <tr>
                                    <td class="ps-3 py-2">Jam kedua dan seterusnya</td>
                                    <td class="pe-3 py-2 text-end fw-bold font-mono text-dark">2.0x upah per jam</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 2. Hari Libur Mingguan --}}
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark" style="font-size: 13px;">B. Lembur Hari Istirahat Mingguan / Hari Libur Resmi</span>
                        <span class="badge bg-label-warning font-mono">OFFDAY / HOLIDAY</span>
                    </div>
                    <div class="border rounded-2 overflow-hidden mb-2" style="border-color: #F1F5F9 !important;">
                        <table class="table table-sm table-striped mb-0" style="font-size: 12px;">
                            <thead>
                                <tr style="background: #F8FAFC;">
                                    <th class="ps-3 py-2 text-muted">JAM KERJA LEMBUR (5 HARI KERJA)</th>
                                    <th class="pe-3 py-2 text-end text-muted">FAKTOR PENGALI UPAH</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-3 py-2">Jam ke-1 s/d jam ke-8</td>
                                    <td class="pe-3 py-2 text-end fw-bold font-mono text-dark">2.0x upah per jam</td>
                                </tr>
                                <tr>
                                    <td class="ps-3 py-2">Jam ke-9</td>
                                    <td class="pe-3 py-2 text-end fw-bold font-mono text-dark">3.0x upah per jam</td>
                                </tr>
                                <tr>
                                    <td class="ps-3 py-2">Jam ke-10 s/d jam ke-12</td>
                                    <td class="pe-3 py-2 text-end fw-bold font-mono text-dark">4.0x upah per jam</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-3 rounded-2" style="background: #FAF9F8; border: 1px solid #F1F5F9; font-size: 12px; color: #475569; line-height: 1.5;">
                    <i class="ti ti-info-circle me-1 text-primary"></i> Rumus dasar upah lembur per jam (PP 35/2021 Pasal 32):<br>
                    <strong class="text-dark">Upah per Jam = 1 / 173 &times; Upah Bulanan (Gaji Pokok + Tunjangan Tetap)</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
