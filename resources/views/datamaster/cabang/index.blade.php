@extends('layouts.app')
@section('titlepage', 'Cabang / Outlet')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Cabang & Outlet</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Cabang / Outlet Coffee</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($cabang->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Manajemen data multi-outlet cabang, radius presensi, dan titik koordinat GPS.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        @can('cabang.create')
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-1.5" id="btncreateCabang" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Tambah Cabang</span>
            </a>
        @endcan
    </div>
</div>

<!-- Search & Filter Bar (Standardized Compact Admin Filter Toolbar) -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('cabang.index') }}" method="GET" class="m-0">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ Request('nama_cabang') }}" name="nama_cabang"
                    icon="ti ti-search" placeholder="Cari nama atau kode cabang..." hideLabel="true" />
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari</span>
                </button>
                @if (Request('nama_cabang'))
                    <a href="{{ route('cabang.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" title="Reset Filter">
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
                    <th style="width: 50px;" class="text-center">NO</th>
                    <th>OUTLET CABANG</th>
                    <th>KODE</th>
                    <th>ALAMAT OUTLET</th>
                    <th>RADIUS & ZONA WAKTU</th>
                    <th>TITIK GPS (KOORDINAT)</th>
                    <th class="text-end" style="width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cabang as $d)
                    <tr>
                        <td class="text-center font-mono text-muted" style="font-size: 12px;">
                            {{ $loop->iteration + ($cabang->currentPage() - 1) * $cabang->perPage() }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 32px; height: 32px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                                    <i class="ti ti-building-community fs-6"></i>
                                </div>
                                <div>
                                    <span class="fw-bold text-dark d-block" style="font-size: 13px;">{{ textUpperCase($d->nama_cabang) }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                {{ $d->kode_cabang }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted text-truncate d-inline-block" style="max-width: 250px; font-size: 12px;" title="{{ $d->alamat_cabang }}">
                                {{ $d->alamat_cabang ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2" style="font-size: 11.5px;">
                                <span class="badge bg-label-primary font-mono">
                                    <i class="ti ti-radar me-0.5"></i> {{ $d->radius_cabang }}m
                                </span>
                                <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0;">
                                    {{ $d->timezone }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="font-mono text-primary d-inline-flex align-items-center gap-1" style="font-size: 11.5px;" title="{{ $d->lokasi_cabang }}">
                                <i class="ti ti-map-pin" style="font-size: 13px;"></i>
                                <span>{{ $d->lokasi_cabang }}</span>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                @can('cabang.edit')
                                    <button type="button" class="btnEdit editCabang" kode_cabang="{{ Crypt::encrypt($d->kode_cabang) }}" title="Edit Cabang">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                @endcan

                                @can('cabang.delete')
                                    <form method="POST" name="deleteform" class="deleteform d-inline m-0"
                                        action="{{ route('cabang.delete', Crypt::encrypt($d->kode_cabang)) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-confirm" title="Hapus Cabang">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="ti ti-building-community text-muted fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data Cabang</h6>
                            <small class="text-muted">Coba ubah kata kunci pencarian.</small>
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
        {{ $cabang->links('pagination::bootstrap-5') }}
    </div>
</div>

<x-modal-form id="mdlcreateCabang" size="" show="loadcreateCabang" title="Tambah Cabang" />
<x-modal-form id="mdleditCabang" size="" show="loadeditCabang" title="Edit Cabang" />

@endsection

@push('mystyle')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/leaflet/leaflet.css') }}" />
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
    #map {
        height: 380px !important;
        width: 100% !important;
        border-radius: 8px;
        border: 1px solid #E2E8F0;
        position: relative;
    }
    .leaflet-pane {
        z-index: 400 !important;
    }
    .leaflet-top, .leaflet-bottom {
        z-index: 500 !important;
    }
</style>
@endpush

@push('myscript')
<script src="{{ asset('assets/vendor/libs/leaflet/leaflet.js') }}"></script>
<script>
    $(function() {
        $(document).on('click', '#btncreateCabang', function(e) {
            e.preventDefault();
            $('#mdlcreateCabang').modal("show");
            $("#loadcreateCabang").load('/cabang/create');
        });

        $(document).on('click', '.editCabang', function(e) {
            e.preventDefault();
            var kode_cabang = $(this).attr("kode_cabang");
            $('#mdleditCabang').modal("show");
            $("#loadeditCabang").load('/cabang/' + kode_cabang);
        });

        // Ensure Leaflet recalculates size when modal opens
        $('#mdlcreateCabang, #mdleditCabang').on('shown.bs.modal', function() {
            setTimeout(function() {
                if (window._cabangEditMap) window._cabangEditMap.invalidateSize();
                if (window._cabangCreateMap) window._cabangCreateMap.invalidateSize();
            }, 100);
        });
    });
</script>
@endpush
