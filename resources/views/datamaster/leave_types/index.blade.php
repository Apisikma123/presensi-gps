@extends('layouts.app')
@section('titlepage', 'Master Jenis Cuti Universal')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Jenis Cuti</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Master Jenis Cuti & Izin</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($leaveTypes->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Pengaturan kategori cuti tahunan, cuti normatif undang-undang, serta izin khusus perusahaan.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('leave_quotas.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-wallet" style="font-size: 16px;"></i>
            <span>Saldo Kuota Cuti</span>
        </a>
        @can('leave_types.create')
            <a href="{{ route('leave_types.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Tambah Jenis Cuti</span>
            </a>
        @endcan
    </div>
</div>

<!-- Search Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('leave_types.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari nama atau kode jenis cuti..." hideLabel="true" />
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari Data</span>
                </button>
                @if(request('search'))
                    <a href="{{ route('leave_types.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode</th>
                    <th>Nama Jenis Cuti</th>
                    <th>Sifat Upah</th>
                    <th>Ketentuan Kuota</th>
                    <th>Syarat & Dokumen</th>
                    <th>Restriksi Gender</th>
                    <th>Status</th>
                    <th class="text-end" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaveTypes as $lt)
                    <tr>
                        <td class="text-muted font-mono">{{ $loop->iteration + ($leaveTypes->currentPage() - 1) * $leaveTypes->perPage() }}</td>
                        <td>
                            <span class="badge bg-label-primary font-mono fw-bold">{{ $lt->code }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">{{ $lt->name }}</span>
                            @if($lt->max_consecutive_days)
                                <div class="text-muted small">Maks. <span class="font-mono">{{ $lt->max_consecutive_days }}</span> hari berturut-turut</div>
                            @endif
                        </td>
                        <td>
                            @if($lt->is_paid)
                                <span class="badge bg-label-success fw-bold"><i class="ti ti-check me-0.5"></i>Berbayar</span>
                            @else
                                <span class="badge bg-label-secondary fw-bold"><i class="ti ti-x me-0.5"></i>Unpaid (Potong Gaji)</span>
                            @endif
                        </td>
                        <td>
                            @if($lt->uses_quota)
                                <div>
                                    <span class="fw-bold text-dark font-mono">{{ (float)$lt->default_quota }} Hari</span>
                                    <span class="text-muted small">/ {{ $lt->quota_type }}</span>
                                </div>
                            @else
                                <span class="text-muted small">Tanpa Batas Kuota</span>
                            @endif
                        </td>
                        <td>
                            <div class="small">
                                @if($lt->requires_attachment)
                                    <div><i class="ti ti-paperclip text-primary me-0.5"></i>Wajib Lampiran Surat</div>
                                @endif
                                @if($lt->min_notice_days > 0)
                                    <div class="text-muted">Min. pengajuan H-<span class="font-mono">{{ $lt->min_notice_days }}</span></div>
                                @endif
                                @if(!$lt->requires_attachment && $lt->min_notice_days == 0)
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($lt->gender_restriction === 'P')
                                <span class="badge bg-label-danger fw-bold"><i class="ti ti-gender-female me-0.5"></i>Perempuan</span>
                            @elseif($lt->gender_restriction === 'M')
                                <span class="badge bg-label-info fw-bold"><i class="ti ti-gender-male me-0.5"></i>Laki-laki</span>
                            @else
                                <span class="text-muted small">Semua Gender</span>
                            @endif
                        </td>
                        <td>
                            @if($lt->is_active)
                                <span class="badge bg-label-success">Aktif</span>
                            @else
                                <span class="badge bg-label-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end" style="white-space: nowrap;">
                            <div class="d-inline-flex align-items-center justify-content-end gap-1.5">
                                @can('leave_types.edit')
                                    <a href="{{ route('leave_types.edit', Crypt::encrypt($lt->id)) }}" class="btn-action-tbl btn-action-edit btnEdit" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                @endcan
                                @can('leave_types.delete')
                                    <form action="{{ route('leave_types.delete', Crypt::encrypt($lt->id)) }}" method="POST" class="d-inline m-0 form-delete deleteform">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-tbl btn-action-delete delete-confirm" title="Hapus">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="empty">
                                <div class="empty-icon text-muted mb-2"><i class="ti ti-calendar-off fs-1"></i></div>
                                <p class="empty-title fw-bold">Belum Ada Jenis Cuti</p>
                                <p class="empty-subtitle text-muted">Tambahkan jenis cuti untuk mengatur hak libur dan izin cuti karyawan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($leaveTypes->hasPages())
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $leaveTypes->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif
@endsection

@push('myscript')
<script>
    $('.form-delete').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Hapus Jenis Cuti?',
            text: 'Data jenis cuti akan dihapus dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
