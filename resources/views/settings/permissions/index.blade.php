@extends('layouts.app')
@section('titlepage', 'Roles')

@section('content')
@section('navigasi')
    <span>Permissions</span>
@endsection
<div class="row">
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <a href="#" class="btn btn-primary" id="btncreatePermission"><i class="fa fa-plus me-2"></i> Tambah
                    Permission</a>
            </div>
            <div class="card-body p-3">
                <div class="admin-filter-toolbar mb-3">
                    <form action="{{ route('permissions.index') }}" method="GET" class="m-0">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <x-select name="id_permission_group" label="" :data="$permission_groups" key="id"
                                    textShow="name" selected="{{ Request('id_permission_group') }}" placeholder="Semua Permission Group" hideLabel="true" />
                            </div>
                            <div class="col-auto">
                                <div class="d-flex align-items-center gap-1.5">
                                    <button class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1" type="submit">
                                        <i class="ti ti-search" style="font-size: 14px;"></i>
                                        <span>Cari</span>
                                    </button>
                                    @if(Request('id_permission_group'))
                                        <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" title="Reset Filter">
                                            <i class="ti ti-refresh" style="font-size: 14px;"></i>
                                            <span>Reset</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive mb-2">
                            <table class="table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Permission Name</th>
                                        <th>Group</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ strtolower($d->name) }}</td>
                                            <td>{{ $d->group_name }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <div>
                                                        <a href="#" class="me-2 editPermission"
                                                            id="{{ Crypt::encrypt($d->id) }}">
                                                            <i class="fa fa-edit text-success"></i>
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <form method="POST" name="deleteform" class="deleteform"
                                                            action="{{ route('permissions.delete', Crypt::encrypt($d->id)) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a href="#" class="delete-confirm ml-1">
                                                                <i class="fa fa-trash-alt text-danger"></i>
                                                            </a>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div style="float: right;">
                            {{ $permissions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="mdlcreatePermission" size="" show="loadcreatePermission" title="Tambah Permission" />
<x-modal-form id="mdleditPermission" size="" show="loadeditPermission" title="Edit Permission" />
@endsection
@push('myscript')
{{-- <script src="{{ asset('assets/js/pages/roles/create.js') }}"></script> --}}
<script>
    $(function() {
        $("#btncreatePermission").click(function(e) {
            $('#mdlcreatePermission').modal("show");
            $("#loadcreatePermission").load('/permissions/create');
        });

        $(".editPermission").click(function(e) {
            var id = $(this).attr("id");
            e.preventDefault();
            $('#mdleditPermission').modal("show");
            $("#loadeditPermission").load('/permissions/' + id + '/edit');
        });
    });
</script>
@endpush
