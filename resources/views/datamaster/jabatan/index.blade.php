@extends('layouts.app')
@section('titlepage', 'Jabatan & Posisi')

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
</style>
@endpush

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Jabatan & Posisi</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Jabatan & Posisi Karyawan</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($jabatan->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Manajemen level posisi, barista, kasir, kitchen, dan manager outlet coffee shop.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        @can('jabatan.create')
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreate" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Tambah Jabatan</span>
            </a>
        @endcan
    </div>
</div>

<!-- Search & Filter Bar (Standardized Compact Admin Filter Toolbar) -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('jabatan.index') }}" method="GET" class="m-0">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ Request('nama_jabatan') }}" name="nama_jabatan"
                    icon="ti ti-search" placeholder="Cari nama atau kode jabatan..." hideLabel="true" />
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari</span>
                </button>
                @if (Request('nama_jabatan'))
                    <a href="{{ route('jabatan.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover table-karyawan align-middle w-100 mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;" class="text-center">NO</th>
                    <th style="width: 120px;">KODE</th>
                    <th>NAMA JABATAN & POSISI</th>
                    <th class="text-end" style="width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jabatan as $j)
                    <tr>
                        <td class="text-center font-mono text-muted" style="font-size: 12px;">
                            {{ $loop->iteration + ($jabatan->currentPage() - 1) * $jabatan->perPage() }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                {{ $j->kode_jabatan }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 32px; height: 32px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                                    <i class="ti ti-briefcase fs-6"></i>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 13px;">{{ $j->nama_jabatan }}</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                @can('jabatan.edit')
                                    <button type="button" class="btnEdit" kode_jabatan="{{ Crypt::encrypt($j->kode_jabatan) }}" title="Edit Jabatan">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                @endcan

                                @can('jabatan.delete')
                                    <form method="POST" name="deleteform" class="deleteform d-inline m-0"
                                        action="{{ route('jabatan.delete', Crypt::encrypt($j->kode_jabatan)) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-confirm" title="Hapus Jabatan">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="ti ti-briefcase-off text-muted fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data Jabatan</h6>
                            <small class="text-muted">Klik Tambah Jabatan untuk menambahkan data baru.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Footer -->
<div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $jabatan->links('pagination::bootstrap-5') }}
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />

@endsection

@push('myscript')
<script>
    $(function() {
        $(document).on('click', '#btnCreate', function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Data Jabatan");
            $("#loadmodal").load("{{ route('jabatan.create') }}");
        });

        $(document).on('click', '.btnEdit', function(e) {
            e.preventDefault();
            const kode_jabatan = $(this).attr("kode_jabatan");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Jabatan");
            $("#loadmodal").load(`/jabatan/${kode_jabatan}`);
        });
    });
</script>
@endpush
