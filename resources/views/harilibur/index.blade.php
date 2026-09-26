@extends('layouts.app')
@section('titlepage', 'Hari Libur & Tanggal Merah')

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
    <li class="breadcrumb-item active">Hari Libur</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Hari Libur & Tanggal Merah</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($harilibur->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Manajemen hari libur nasional, cuti bersama, dan hari operasional khusus outlet cabang.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        @can('harilibur.create')
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreate" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Tambah Hari Libur</span>
            </a>
        @endcan
    </div>
</div>

<!-- Search & Filter Bar (Standardized Compact Admin Filter Toolbar) -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('harilibur.index') }}" method="GET" class="m-0">
        <div class="row g-2 align-items-center">
            <div class="col-xl col-lg col-md col-sm-12">
                <x-input-with-icon icon="ti ti-calendar" label="Dari" name="dari" datepicker="flatpickr-date"
                    :value="Request('dari')" placeholder="Dari Tanggal" hideLabel />
            </div>
            <div class="col-xl col-lg col-md col-sm-12">
                <x-input-with-icon icon="ti ti-calendar" label="Sampai" name="sampai" datepicker="flatpickr-date"
                    :value="Request('sampai')" placeholder="Sampai Tanggal" hideLabel />
            </div>
            @if ($user->hasRole(['super admin', 'gm administrasi']) || !$cabang->isEmpty())
                <div class="col-xl col-lg col-md col-sm-12">
                    <select name="kode_cabang" id="kode_cabang" class="form-select select2Kodecabangsearch">
                        <option value="">Semua Cabang / Outlet</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}"
                                {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ textUpperCase($c->nama_cabang) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-auto">
                <div class="d-flex align-items-center gap-1.5">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5" id="btnSearch">
                        <i class="ti ti-search" style="font-size: 14px;"></i>
                        <span>Cari</span>
                    </button>
                    @if (Request('dari') || Request('sampai') || Request('kode_cabang'))
                        <a href="{{ route('harilibur.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" title="Reset Filter">
                            <i class="ti ti-refresh" style="font-size: 14px;"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
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
                    <th style="width: 130px;">KODE LIBUR</th>
                    <th>TANGGAL & HARI</th>
                    <th>CABANG / OUTLET</th>
                    <th>KETERANGAN LIBUR</th>
                    <th class="text-end" style="width: 140px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($harilibur as $d)
                    <tr>
                        <td class="text-center font-mono text-muted" style="font-size: 12px;">
                            {{ $loop->iteration + ($harilibur->currentPage() - 1) * $harilibur->perPage() }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11.5px; font-weight: 600;">
                                {{ $d->kode_libur }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 34px; height: 34px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                                    <i class="ti ti-calendar-event fs-5"></i>
                                </div>
                                <div>
                                    <span class="fw-bold text-dark d-block font-mono" style="font-size: 13px;">{{ formatIndo($d->tanggal) }}</span>
                                    <span class="text-muted" style="font-size: 11.5px;">{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('l') }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($d->kode_cabang === 'ALL')
                                <span class="badge font-mono" style="background: rgba(60, 42, 33, 0.1); color: #3C2A21; border: 1px solid rgba(60, 42, 33, 0.2); font-size: 11px;">
                                    <i class="ti ti-world me-1"></i> SEMUA CABANG (NASIONAL)
                                </span>
                            @else
                                <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                    <i class="ti ti-building-store me-1 text-muted"></i> {{ textUpperCase($d->nama_cabang) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold text-dark" style="font-size: 13px;">{{ $d->keterangan }}</span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                @can('harilibur.edit')
                                    <button type="button" class="btnEdit btn-action-tbl btn-action-edit"
                                        kode_libur="{{ Crypt::encrypt($d->kode_libur) }}" title="Edit Hari Libur">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                @endcan

                                @can('harilibur.setharilibur')
                                    <a href="{{ route('harilibur.aturharilibur', Crypt::encrypt($d->kode_libur)) }}" 
                                        class="btn-action-tbl btn-action-settings" title="Atur Karyawan Libur">
                                        <i class="ti ti-settings"></i>
                                    </a>
                                @endcan

                                @can('harilibur.delete')
                                    <form method="POST" name="deleteform" class="deleteform d-inline m-0"
                                        action="{{ route('harilibur.delete', Crypt::encrypt($d->kode_libur)) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-confirm btn-action-tbl btn-action-delete"
                                            title="Hapus Hari Libur">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="ti ti-calendar-off text-muted fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data Hari Libur</h6>
                            <small class="text-muted">Klik tombol "Tambah Hari Libur" di atas untuk menambahkan data libur baru.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-2">
    {{ $harilibur->links() }}
</div>

<x-modal-form id="modal" show="loadmodal" />
@endsection

@push('myscript')
<script>
    $(function() {
        const select2Kodecabangsearch = $(".select2Kodecabangsearch");
        if (select2Kodecabangsearch.length > 0) {
            select2Kodecabangsearch.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative w-100"></div>').select2({
                    placeholder: 'Semua Cabang / Outlet',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $this.parent()
                });
            });
        }

        $("#btnCreate").click(function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Hari Libur");
            $("#loadmodal").load(`/harilibur/create`);
            $("#modal").find(".modal-dialog").removeClass("modal-lg");
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            const kode_libur = $(this).attr("kode_libur");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Hari Libur");
            $("#loadmodal").load(`/harilibur/${kode_libur}/edit`);
            $("#modal").find(".modal-dialog").removeClass("modal-lg");
        });
    });
</script>
@endpush
