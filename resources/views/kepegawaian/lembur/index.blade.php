@extends('layouts.app')
@section('titlepage', 'SPK Lembur')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">SPK Lembur</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Surat Perintah Kerja (SPK) Lembur</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($lemburs->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Manajemen lembur, kepatuhan PP 35/2021, formula pengali Depnaker, dan persetujuan.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        @can('overtime_policy.index')
            <a href="{{ route('overtime_policy.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 500; padding: 0 14px;">
                <i class="ti ti-settings"></i>
                <span>Kebijakan Lembur</span>
            </a>
        @endcan
        @can('overtime.create')
            <a href="{{ route('overtime.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Buat SPK Lembur</span>
            </a>
        @endcan
    </div>
</div>

{{-- Alerts --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle fs-5 me-2"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card h-100 p-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.05em;">Total SPK Bulan Ini</span>
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                    <i class="ti ti-file-text"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold font-mono text-dark">
                {{ number_format($stats['total_spk_month']) }}
            </div>
            <span class="text-muted mt-1" style="font-size: 11.5px;">Semua status pengajuan</span>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100 p-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.05em;">Menunggu Approval</span>
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(217, 119, 6, 0.08); color: #D97706;">
                    <i class="ti ti-clock-pause"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold font-mono text-warning">
                {{ number_format($stats['pending_approval']) }}
            </div>
            <span class="text-muted mt-1" style="font-size: 11.5px;">Butuh persetujuan</span>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100 p-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.05em;">Disetujui Bulan Ini</span>
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(21, 128, 61, 0.08); color: #15803D;">
                    <i class="ti ti-circle-check"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold font-mono text-success">
                {{ number_format($stats['approved_month']) }}
            </div>
            <span class="text-muted mt-1" style="font-size: 11.5px;">Siap dihitung payroll</span>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card h-100 p-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-uppercase text-muted" style="font-size: 11px; font-weight: 600; letter-spacing: 0.05em;">Jam Bayar Ekuivalen</span>
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(2, 132, 199, 0.08); color: #0284C7;">
                    <i class="ti ti-calculator"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold font-mono text-dark">
                {{ number_format($stats['total_rate_hours_month'], 1) }} <span style="font-size: 13px; font-weight: normal; color: #755841;">jam</span>
            </div>
            <span class="text-muted mt-1" style="font-size: 11.5px;">Formula Depnaker</span>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form method="GET" action="{{ route('overtime.index') }}" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 200px;">
                <select name="nik" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Karyawan</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->nik }}" {{ request('nik') == $emp->nik ? 'selected' : '' }}>
                            {{ $emp->nama_karyawan }} ({{ $emp->nik }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-shrink-0" style="min-width: 140px;">
                <select name="status" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Status</option>
                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Menunggu (Pending)</option>
                    <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Disetujui (Approved)</option>
                    <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                    <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div class="flex-shrink-0" style="min-width: 140px;">
                <select name="day_type" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Tipe Hari</option>
                    <option value="WORKDAY" {{ request('day_type') == 'WORKDAY' ? 'selected' : '' }}>Hari Kerja</option>
                    <option value="OFFDAY_5DAYS" {{ request('day_type') == 'OFFDAY_5DAYS' ? 'selected' : '' }}>Libur (5 Hari)</option>
                    <option value="OFFDAY_6DAYS" {{ request('day_type') == 'OFFDAY_6DAYS' ? 'selected' : '' }}>Libur (6 Hari)</option>
                    <option value="PUBLIC_HOLIDAY" {{ request('day_type') == 'PUBLIC_HOLIDAY' ? 'selected' : '' }}>Libur Nasional</option>
                </select>
            </div>

            <div class="flex-shrink-0" style="min-width: 130px;">
                <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="{{ request('tanggal_dari') }}" style="border-radius: 8px;" placeholder="Dari">
            </div>

            <div class="flex-shrink-0" style="min-width: 130px;">
                <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="{{ request('tanggal_sampai') }}" style="border-radius: 8px;" placeholder="Sampai">
            </div>

            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3" title="Cari">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari</span>
                </button>
                @if (request()->hasAny(['nik', 'status', 'day_type', 'tanggal_dari', 'tanggal_sampai']))
                    <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Overtime Records Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
                <thead style="background: #f8fafc; border-bottom: 1px solid var(--theme-border, rgba(15, 23, 42, 0.08));">
                    <tr>
                        <th style="color: #475569; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">NO. SPK</th>
                        <th style="color: #475569; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">KARYAWAN</th>
                        <th style="color: #475569; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">TANGGAL & WAKTU</th>
                        <th style="color: #475569; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">TIPE HARI</th>
                        <th style="color: #475569; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">DURASI</th>
                        <th style="color: #475569; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">JAM BAYAR (DEPNAKER)</th>
                        <th style="color: #475569; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">STATUS</th>
                        <th class="text-end" style="color: #475569; font-weight: 600; font-size: 11px; letter-spacing: 0.04em;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lemburs as $lembur)
                        <tr>
                            <td>
                                <a href="{{ route('overtime.show', $lembur->id) }}" class="fw-bold text-decoration-none" style="font-family: 'JetBrains Mono', monospace; font-size: 12.5px; color: var(--theme-text-primary, #0F172A);">
                                    {{ $lembur->no_spk }}
                                </a>
                                <div class="text-muted" style="font-size: 11px;">
                                    {{ $lembur->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                                        style="width: 32px; height: 32px; background: #f1f5f9; color: var(--theme-text-primary, #0F172A); font-weight: 600; font-size: 12px; border: 1px solid #e2e8f0;">
                                        {{ strtoupper(substr($lembur->karyawan->nama_karyawan ?? 'K', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-truncate" style="max-width: 180px; color: var(--theme-text-primary, #0F172A);">
                                            {{ $lembur->karyawan->nama_karyawan ?? $lembur->nik }}
                                        </div>
                                        <div class="text-muted" style="font-size: 11.5px; font-family: 'JetBrains Mono', monospace;">
                                            {{ $lembur->nik }} &bull; {{ $lembur->karyawan->departemen->nama_dept ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">
                                    {{ $lembur->tanggal->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-muted" style="font-size: 11.5px; font-family: 'JetBrains Mono', monospace;">
                                    {{ $lembur->lembur_mulai->format('H:i') }} - {{ $lembur->lembur_selesai->format('H:i') }}
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background: #f4f3f2; color: #4f4540; font-weight: 500; font-size: 11px; border: 1px solid #d3c3bd;">
                                    {{ $lembur->day_type_label }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">
                                    {{ $lembur->formatted_duration }}
                                </span>
                                @if ($lembur->duration_hours >= 4.0)
                                    <div class="text-success" style="font-size: 10.5px;">
                                        <i class="ti ti-check" style="font-size: 11px;"></i> Ekstra Makan (≥4j)
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background: #fdfcfb; border: 1px solid rgba(15, 23, 42, 0.12); color: var(--theme-text-primary, #0F172A); font-family: 'JetBrains Mono', monospace; font-size: 12px; font-weight: 600;">
                                    {{ number_format($lembur->calculated_rate_hours, 2) }} jam
                                </span>
                            </td>
                            <td>
                                {!! $lembur->status_badge_html !!}
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    {{-- Detail/Print --}}
                                    <a href="{{ route('overtime.show', $lembur->id) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Detail SPK" style="border-radius: 6px;">
                                        <i class="ti ti-file-description"></i>
                                    </a>

                                    {{-- Approval buttons for PENDING --}}
                                    @if ($lembur->status === 'PENDING')
                                        @can('overtime.approve')
                                            <button type="button" class="btn btn-sm btn-icon text-success" title="Setujui SPK"
                                                style="border-radius: 6px; background: #f0fdf4; border: 1px solid #bbf7d0;"
                                                data-bs-toggle="modal" data-bs-target="#modalApprove{{ $lembur->id }}">
                                                <i class="ti ti-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-icon text-danger" title="Tolak SPK"
                                                style="border-radius: 6px; background: #fef2f2; border: 1px solid #fecdd3;"
                                                data-bs-toggle="modal" data-bs-target="#modalReject{{ $lembur->id }}">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        @endcan
                                    @endif

                                    {{-- Edit / Delete --}}
                                    @if (in_array($lembur->status, ['PENDING', 'DRAFT']))
                                        @can('overtime.edit')
                                            <a href="{{ route('overtime.edit', $lembur->id) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Edit" style="border-radius: 6px;">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                        @endcan
                                        @can('overtime.delete')
                                            <form action="{{ route('overtime.delete', $lembur->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger delete-confirm" data-label="SPK Lembur {{ $lembur->no_spk }}" title="Hapus" style="border-radius: 6px;">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>

                                {{-- Modal Approve --}}
                                <div class="modal fade" id="modalApprove{{ $lembur->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <form action="{{ route('overtime.approve', $lembur->id) }}" method="POST" class="modal-content" style="border-radius: 12px; border: 1px solid rgba(15, 23, 42, 0.1);">
                                            @csrf
                                            <div class="modal-header border-bottom py-3">
                                                <h5 class="modal-title fw-bold" style="font-family: 'Outfit', sans-serif; color: var(--theme-text-primary, #0F172A); font-size: 16px;">
                                                    Setujui SPK Lembur {{ $lembur->no_spk }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                <div class="p-3 mb-3" style="background: #faf9f8; border-radius: 8px; border: 1px solid rgba(15, 23, 42, 0.08); font-size: 13px;">
                                                    <div><strong>Karyawan:</strong> {{ $lembur->karyawan->nama_karyawan ?? $lembur->nik }}</div>
                                                    <div><strong>Waktu Rencana:</strong> {{ $lembur->lembur_mulai->format('d/m/Y H:i') }} - {{ $lembur->lembur_selesai->format('H:i') }} ({{ $lembur->formatted_duration }})</div>
                                                    <div><strong>Tugas:</strong> {{ $lembur->keterangan }}</div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4f4540;">Durasi Disetujui (Menit)</label>
                                                    <input type="number" name="approved_duration_minutes" class="form-control"
                                                        value="{{ $lembur->approved_duration_minutes ?? $lembur->planned_duration_minutes }}"
                                                        style="border-radius: 8px; font-family: 'JetBrains Mono', monospace;" min="1" required>
                                                    <span class="text-muted" style="font-size: 11px;">Default sesuai durasi rencana ({{ $lembur->planned_duration_minutes }} menit).</span>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4f4540;">Catatan Persetujuan (Opsional)</label>
                                                    <textarea name="notes" rows="2" class="form-control" style="border-radius: 8px; font-size: 13px;" placeholder="Catatan approval..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top py-2.5">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                                                <button type="submit" class="btn text-white" style="background: #15803d; border-radius: 8px; font-weight: 500;">
                                                    <i class="ti ti-check me-1"></i> Konfirmasi Setujui
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- Modal Reject --}}
                                <div class="modal fade" id="modalReject{{ $lembur->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <form action="{{ route('overtime.reject', $lembur->id) }}" method="POST" class="modal-content" style="border-radius: 12px; border: 1px solid rgba(15, 23, 42, 0.1);">
                                            @csrf
                                            <div class="modal-header border-bottom py-3">
                                                <h5 class="modal-title fw-bold" style="font-family: 'Outfit', sans-serif; color: #ba1a1a; font-size: 16px;">
                                                    Tolak SPK Lembur {{ $lembur->no_spk }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                <div class="mb-3">
                                                    <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4f4540;">Alasan Penolakan <span class="text-danger">*</span></label>
                                                    <textarea name="notes" rows="3" class="form-control" style="border-radius: 8px; font-size: 13px;" placeholder="Sebutkan alasan penolakan..." required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top py-2.5">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                                                <button type="submit" class="btn text-white" style="background: #ba1a1a; border-radius: 8px; font-weight: 500;">
                                                    <i class="ti ti-x me-1"></i> Konfirmasi Tolak
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted" style="font-size: 14px;">
                                    <i class="ti ti-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada data pengajuan SPK Lembur.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
</div>

<!-- Pagination Footer Card -->
@if ($lemburs->hasPages())
    <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
        <div class="card-body py-2.5 px-3">
            {{ $lemburs->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
@endsection
