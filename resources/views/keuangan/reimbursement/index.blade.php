@extends('layouts.app')
@section('titlepage', 'Reimbursement & Klaim Biaya')

@push('mystyle')
<style>
    /* Table Animations & Clean Design (Harmonized with Karyawan Page) */
    .table-karyawan-wrapper {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #E2E8F0;
        background: #FFFFFF !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .table-karyawan {
        margin-bottom: 0;
        background: #FFFFFF !important;
    }

    .table-karyawan thead th {
        background: #F8FAFC;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 16px;
        border-bottom: 1px solid #E2E8F0;
        white-space: nowrap;
    }

    .table-karyawan tbody tr {
        transition: all 0.15s ease;
        background: #FFFFFF !important;
    }

    .table-karyawan tbody tr:hover {
        background-color: #F8FAFC !important;
    }

    .table-karyawan tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
        font-size: 13px;
        background: #FFFFFF;
    }

    .table-karyawan tbody tr:hover td {
        background-color: #F8FAFC !important;
    }

    .btn-primary {
        background-color: var(--color-primary, #3C2A21) !important;
        border-color: var(--color-primary, #3C2A21) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
        box-shadow: 0 1px 2px rgba(var(--bs-primary-rgb, 15, 23, 42), 0.08) !important;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .btn-primary:hover {
        background-color: var(--color-primary-hover, var(--theme-color-2, #25160E)) !important;
        border-color: var(--color-primary-hover, var(--theme-color-2, #25160E)) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
        transform: translateY(-1px);
    }

    .btn-outline-secondary {
        border-color: #CBD5E1 !important;
        color: #334155 !important;
        background-color: #FFFFFF !important;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .btn-outline-secondary:hover {
        background-color: #F8FAFC !important;
        border-color: #94A3B8 !important;
        color: #0F172A !important;
        transform: translateY(-1px);
    }
</style>
@endpush

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item active">Reimbursement</li>
@endsection

@section('content')

<!-- Standard Page Header (Matching Karyawan Reference) -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Reimbursement & Klaim Biaya</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($reimbursements->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Manajemen pengajuan pengembalian dana operasional, medis, dinas, dan transportasi karyawan.</p>
    </div>

    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        @can('reimbursement.create')
            <a href="{{ route('reimbursement.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Ajukan Klaim Baru</span>
            </a>
        @endcan
    </div>
</div>

{{-- Alerts --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert" style="border-radius: 8px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #15803d;">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <span style="font-size: 13.5px;">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible mb-4" role="alert" style="border-radius: 8px; border: 1px solid #fecdd3; background: #fef2f2; color: #ba1a1a;">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle fs-5 me-2"></i>
            <span style="font-size: 13.5px;">{{ session('error') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-primary rounded p-2 me-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-receipt fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-uppercase" style="font-size: 11px;">Total Pengajuan</div>
                        <h4 class="mb-0 fw-bold font-mono text-dark">{{ number_format($stats['total_claims'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-success rounded p-2 me-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-check fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-uppercase" style="font-size: 11px;">Total Disetujui</div>
                        <h4 class="mb-0 fw-bold font-mono text-success">Rp {{ number_format($stats['approved_amount'] ?? $stats['total_amount'] ?? 0, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-warning rounded p-2 me-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-clock-pause fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-uppercase" style="font-size: 11px;">Menunggu Review</div>
                        <h4 class="mb-0 fw-bold font-mono text-warning">Rp {{ number_format($stats['pending_amount'] ?? 0, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-info rounded p-2 me-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-wallet fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium text-uppercase" style="font-size: 11px;">Telah Dibayar</div>
                        <h4 class="mb-0 fw-bold font-mono text-info">Rp {{ number_format($stats['paid_amount'] ?? 0, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Standard Filter Toolbar Card (Compact Single-Row Toolbar like Karyawan) --}}
<div class="card admin-filter-toolbar mb-3">
    <form method="GET" action="{{ route('reimbursement.index') }}" id="filterReimbursementForm" class="m-0">
        <div class="row g-2 align-items-center">
            <!-- 1. Search Input: Nomor Klaim / Nama Karyawan -->
            <div class="col-xl col-lg col-md-12 col-12">
                <div class="input-group admin-table-search">
                    <span class="input-group-text text-muted">
                        <i class="ti ti-search" style="font-size: 14px;"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nomor klaim atau nama karyawan..."
                        value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>

            <!-- 2. Dropdown: Status -->
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
                <select name="status" class="form-select" onchange="document.getElementById('filterReimbursementForm').submit();">
                    <option value="">Semua Status</option>
                    <option value="SUBMITTED" {{ request('status') === 'SUBMITTED' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Disetujui</option>
                    <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
                    <option value="PAID" {{ request('status') === 'PAID' ? 'selected' : '' }}>Dibayar</option>
                </select>
            </div>

            <!-- 3. Dropdown: Kategori Biaya -->
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12">
                <select name="type_id" class="form-select" onchange="document.getElementById('filterReimbursementForm').submit();">
                    <option value="">Semua Kategori Biaya</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Action Buttons -->
            <div class="col-auto">
                <div class="d-flex align-items-center gap-1.5">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5">
                        <i class="ti ti-search" style="font-size: 14px;"></i>
                        <span>Cari Data</span>
                    </button>
                    @if(request('status') || request('type_id') || request('search'))
                        <a href="{{ route('reimbursement.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" title="Reset Filter">
                            <i class="ti ti-refresh" style="font-size: 14px;"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Reimbursement Table Card --}}
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover table-karyawan align-middle w-100">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>No. Klaim</th>
                    <th>Karyawan</th>
                    <th>Kategori Biaya</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th class="text-end">Nominal Klaim</th>
                    <th class="text-center" style="width: 80px;">Bukti</th>
                    <th class="text-end" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reimbursements as $item)
                    @php
                        $nama = $item->karyawan->nama_karyawan ?? $item->nik;
                        $words = explode(' ', $nama);
                        $initials = '';
                        foreach ($words as $w) {
                            if (isset($w[0])) $initials .= $w[0];
                        }
                        $initials = strtoupper(substr($initials, 0, 2));
                        $no = ($reimbursements->currentPage() - 1) * $reimbursements->perPage() + $loop->iteration;
                    @endphp
                    <tr>
                        <td class="text-muted font-mono" style="font-size: 11px;">{{ $no }}</td>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                {{ $item->claim_number }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                @if (!empty($item->karyawan->foto))
                                    <img src="{{ getfotoKaryawan($item->karyawan->foto) }}" alt="Avatar" class="rounded-circle flex-shrink-0"
                                        style="width: 34px; height: 34px; object-fit: cover; border: 1px solid #E2E8F0;"
                                        onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                    <div class="rounded-circle flex-shrink-0 align-items-center justify-content-center fw-bold"
                                        style="display: none; width: 34px; height: 34px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); font-size: 11.5px; border: 1px solid rgba(60, 42, 33, 0.15);">
                                        {{ $initials }}
                                    </div>
                                @else
                                    <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center fw-bold"
                                        style="width: 34px; height: 34px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); font-size: 11.5px; border: 1px solid rgba(60, 42, 33, 0.15);">
                                        {{ $initials }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 13px;">{{ $nama }}</div>
                                    <span class="badge bg-light text-muted font-mono" style="font-size: 10px; border: 1px solid #E2E8F0;">
                                        {{ $item->nik }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-primary font-mono" style="font-size: 11px;">
                                {{ $item->type->name ?? '-' }}
                            </span>
                            @if($item->description)
                                <small class="text-muted d-block mt-0.5" style="font-size: 11px;">{{ Str::limit($item->description, 35) }}</small>
                            @endif
                        </td>
                        <td class="font-mono text-muted" style="font-size: 12px;">
                            {{ $item->claim_date ? $item->claim_date->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            {!! $item->status_badge_html !!}
                        </td>
                        <td class="text-end font-mono fw-bold text-dark" style="font-size: 13px;">
                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if ($item->receipt_attachment)
                                <a href="{{ Storage::url($item->receipt_attachment) }}" target="_blank" class="btn btn-sm btn-outline-secondary p-1" title="Lihat Bukti Kwitansi">
                                    <i class="ti ti-paperclip fs-6"></i>
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1.5 align-items-center justify-content-end">
                                @if ($item->status === 'SUBMITTED')
                                    @can('reimbursement.approve')
                                        <form action="{{ route('reimbursement.approve', $item->id) }}" method="POST" class="d-inline form-approve">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Setujui Klaim" style="padding: 4px 8px;">
                                                <i class="ti ti-check fs-6"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" style="padding: 4px 8px;"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}" title="Tolak Klaim">
                                                <i class="ti ti-x fs-6"></i>
                                        </button>
                                    @endcan
                                @endif
                                @can('reimbursement.delete')
                                    @if(in_array($item->status, ['SUBMITTED', 'REJECTED']))
                                        <form action="{{ route('reimbursement.destroy', $item->id) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Klaim" style="padding: 4px 8px;">
                                                <i class="ti ti-trash fs-6"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>

                            {{-- Reject Modal --}}
                            <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content" style="border-radius: 12px;">
                                        <form action="{{ route('reimbursement.reject', $item->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header py-3" style="border-bottom: 1px solid #F1F5F9;">
                                                <h6 class="modal-title fw-bold text-dark mb-0">Tolak Klaim</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-3 text-start">
                                                <label class="form-label small fw-bold text-dark">Alasan Penolakan <span class="text-danger">*</span></label>
                                                <textarea name="rejection_reason" rows="3" class="form-control form-control-sm" required placeholder="Contoh: Bukti kuitansi tidak jelas / tidak sesuai S&K"></textarea>
                                            </div>
                                            <div class="modal-footer py-2" style="border-top: 1px solid #F1F5F9;">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-danger">Tolak Klaim</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="ti ti-receipt-off fs-1 d-block mb-2 text-secondary" style="opacity: 0.5;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Belum Ada Pengajuan Reimbursement</h6>
                            <small class="text-muted">Klaim biaya operasional, perjalanan dinas, atau medis yang diajukan akan tampil di sini.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Standard Pagination Footer Card (Harmonized with Karyawan Page) --}}
<div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $reimbursements->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        $('.form-approve').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Setujui Reimbursement?',
                text: 'Klaim ini akan disetujui dan masuk dalam antrean pembayaran.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: (getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '#3C2A21'),
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $('.form-delete').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Hapus Klaim?',
                text: 'Data reimbursement ini akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
