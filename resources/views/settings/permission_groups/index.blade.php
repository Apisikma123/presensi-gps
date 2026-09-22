@extends('layouts.app')
@section('titlepage', 'Permission Groups')

@section('content')
@section('navigasi')
    <span>Permission Groups</span>
@endsection
<div class="row">
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <a href="#" class="btn btn-primary" id="btncreateGroup"><i class="fa fa-plus me-2"></i> Tambah
                    Group</a>
            </div>
            <div class="card-body p-3">
                <div class="admin-filter-toolbar mb-3">
                    <form action="{{ route('permissiongroups.index') }}" method="GET" class="m-0">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <x-input-with-icon label="" value="{{ Request('name') }}" name="name"
                                    icon="ti ti-search" placeholder="Cari nama group..." hideLabel="true" />
                            </div>
                            <div class="col-auto">
                                <div class="d-flex align-items-center gap-1.5">
                                    <button class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1" type="submit">
                                        <i class="ti ti-search" style="font-size: 14px;"></i>
                                        <span>Cari</span>
                                    </button>
                                    @if(Request('name'))
                                        <a href="{{ route('permissiongroups.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" title="Reset Filter">
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
                                        <th>Group</th>
                                        <th>#</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($permission_groups as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->name }}</td>
                                            <td>
                                                <div class="d-flex">

                                                    <div>
                                                        <a href="#" class="me-2 editGroup"
                                                            id="{{ Crypt::encrypt($d->id) }}">
                                                            <i class="fa fa-edit text-success"></i>
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <form method="POST" name="deleteform" class="deleteform"
                                                            action="{{ route('permissiongroups.delete', Crypt::encrypt($d->id)) }}">
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
                            {{ $permission_groups->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="mdlcreateGroup" size="" show="loadcreateGroup" title="Tambah Group" />
<x-modal-form id="mdleditGroup" size="" show="loadeditGroup" title="Edit Group" />

@endsection



@push('myscript')
{{-- <script src="{{ asset('assets/js/pages/permission_groups/create.js') }}"></script> --}}
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
