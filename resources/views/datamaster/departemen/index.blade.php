@extends('layouts.app')
@section('titlepage', 'Divisi & Departemen')

@section('content')
@section('navigasi')
    <span>Departemen</span>
@endsection

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Divisi & Departemen</h4>
        <p class="page-subtitle text-muted mb-0">Manajemen unit kerja, divisi operasional, kitchen, bar & service.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('departemen.create')
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreate">
                <i class="ti ti-plus"></i>
                <span>Tambah Departemen</span>
            </a>
        @endcan
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <div class="card-body p-3">
        <form action="{{ route('departemen.index') }}" method="GET">
            <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">
                <div class="flex-grow-1" style="min-width: 240px;">
                    <x-input-with-icon label="" value="{{ Request('nama_dept') }}" name="nama_dept"
                        icon="ti ti-search" placeholder="Cari nama atau kode departemen..." hideLabel="true" />
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5"
                        style="height: 38px; min-width: 90px;">
                        <i class="ti ti-search"></i>
                        <span>Cari</span>
                    </button>
                    @if (Request('nama_dept'))
                        <a href="{{ route('departemen.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1"
                            style="height: 38px; padding: 0 12px;" title="Reset Filter">
                            <i class="ti ti-refresh" style="font-size: 14px;"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;" class="text-center">NO</th>
                    <th style="width: 120px;">KODE</th>
                    <th>NAMA DEPARTEMEN</th>
                    <th class="text-end" style="width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($departemen as $d)
                    <tr>
                        <td class="text-center font-mono text-muted" style="font-size: 12px;">
                            {{ $loop->iteration + ($departemen->currentPage() - 1) * $departemen->perPage() }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                {{ $d->kode_dept }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 32px; height: 32px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E;">
                                    <i class="ti ti-building fs-6"></i>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 13px;">{{ $d->nama_dept }}</span>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                @can('departemen.edit')
                                    <button type="button" class="btnEdit" kode_dept="{{ Crypt::encrypt($d->kode_dept) }}" title="Edit Departemen">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                @endcan

                                @can('departemen.delete')
                                    <form method="POST" name="deleteform" class="deleteform d-inline m-0"
                                        action="{{ route('departemen.delete', Crypt::encrypt($d->kode_dept)) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-confirm" title="Hapus Departemen">
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
                            <i class="ti ti-building-off text-muted fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data Departemen</h6>
                            <small class="text-muted">Klik Tambah Departemen untuk menambahkan data baru.</small>
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
        {{ $departemen->links('pagination::bootstrap-5') }}
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />

@endsection

@push('myscript')
<script>
    $(function() {
        function loading() {
            $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
            </div>`);
        };

        $(document).on('click', '#btnCreate', function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Data Departemen");
            loading();
            $("#loadmodal").load("{{ route('departemen.create') }}");
        });

        $(document).on('click', '.btnEdit', function(e) {
            e.preventDefault();
            const kode_dept = $(this).attr("kode_dept");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Departemen");
            loading();
            $("#loadmodal").load(`/departemen/${kode_dept}/edit`);
        });
    });
</script>
@endpush
