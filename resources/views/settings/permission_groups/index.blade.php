@extends('layouts.app')
@section('titlepage', 'Permission Groups')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.hub') }}">Pengaturan Sistem</a></li>
    <li class="breadcrumb-item active">Permission Groups</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Permission Groups</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($permission_groups->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Pengelompokan kategori permission dan modul otoritas pengguna.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-1.5" id="btncreateGroup" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-plus" style="font-size: 16px;"></i>
            <span>Tambah Group</span>
        </a>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('permissiongroups.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ Request('name') }}" name="name"
                    icon="ti ti-search" placeholder="Cari nama group..." hideLabel="true" />
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3" type="submit">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari Data</span>
                </button>
                @if(Request('name'))
                    <a href="{{ route('permissiongroups.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
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
                    <th style="width: 50px;">NO</th>
                    <th>NAMA GROUP</th>
                    <th class="text-end" style="width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($permission_groups as $d)
                    <tr>
                        <td class="text-muted font-mono">{{ $loop->iteration + ($permission_groups->currentPage() - 1) * $permission_groups->perPage() }}</td>
                        <td>
                            <span class="fw-bold text-dark">{{ $d->name }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="#" class="btn btn-outline-primary editGroup" id="{{ Crypt::encrypt($d->id) }}" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form method="POST" name="deleteform" class="deleteform d-inline" action="{{ route('permissiongroups.delete', Crypt::encrypt($d->id)) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger delete-confirm" title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">
                            Belum ada permission group yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($permission_groups->hasPages())
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $permission_groups->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

<x-modal-form id="mdlcreateGroup" size="" show="loadcreateGroup" title="Tambah Group" />
<x-modal-form id="mdleditGroup" size="" show="loadeditGroup" title="Edit Group" />
@endsection

@push('myscript')
<script>
    $(function() {
        $("#btncreateGroup").click(function(e) {
            $('#mdlcreateGroup').modal("show");
            $("#loadcreateGroup").load('/permissiongroups/create');
        });

        $(".editGroup").click(function(e) {
            var id = $(this).attr("id");
            e.preventDefault();
            $('#mdleditGroup').modal("show");
            $("#loadeditGroup").load('/permissiongroups/' + id + '/edit');
        });
    });
</script>
@endpush
