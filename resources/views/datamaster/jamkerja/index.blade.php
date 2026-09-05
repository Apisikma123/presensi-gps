@extends('layouts.app')
@section('titlepage', 'Master Shift Kerja')

@section('content')
@section('navigasi')
    <span>Shift Kerja</span>
@endsection

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Master Jadwal & Shift Kerja</h4>
        <p class="page-subtitle text-muted mb-0">Konfigurasi jam masuk, jam pulang, waktu istirahat & shift lintas hari outlet coffee.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('jamkerja.create')
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreate">
                <i class="ti ti-plus"></i>
                <span>Tambah Shift Kerja</span>
            </a>
        @endcan
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <div class="card-body p-3">
        <form action="{{ route('jamkerja.index') }}" method="GET">
            <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">
                <div class="flex-grow-1" style="min-width: 240px;">
                    <x-input-with-icon label="" value="{{ Request('nama_jam_kerja_search') }}" name="nama_jam_kerja_search"
                        icon="ti ti-search" placeholder="Cari nama atau kode shift kerja..." hideLabel="true" />
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5"
                        style="height: 38px; min-width: 90px;">
                        <i class="ti ti-search"></i>
                        <span>Cari</span>
                    </button>
                    @if (Request('nama_jam_kerja_search'))
                        <a href="{{ route('jamkerja.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1"
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
                    <th style="width: 50px;" class="text-center">NO</th>
                    <th>KODE & NAMA SHIFT</th>
                    <th>JAM KERJA (WIB)</th>
                    <th class="text-center">ISTIRAHAT</th>
                    <th>WAKTU ISTIRAHAT</th>
                    <th class="text-center">TIPE SHIFT</th>
                    <th class="text-center">TOTAL JAM</th>
                    <th class="text-center">WARNA</th>
                    <th class="text-end" style="width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jamkerja as $d)
                    <tr>
                        <td class="text-center font-mono text-muted" style="font-size: 12px;">
                            {{ $loop->iteration + ($jamkerja->currentPage() - 1) * $jamkerja->perPage() }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                    {{ $d->kode_jam_kerja }}
                                </span>
                                <div>
                                    <span class="fw-bold text-dark d-block" style="font-size: 13px;">{{ $d->nama_jam_kerja }}</span>
                                    @if($d->keterangan)
                                        <small class="text-muted" style="font-size: 11px;">{{ $d->keterangan }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-mono fw-semibold text-dark" style="font-size: 12px;">
                                {{ substr($d->jam_masuk, 0, 5) }} - {{ substr($d->jam_pulang, 0, 5) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if ($d->istirahat == 1)
                                <span class="badge bg-label-success">Ada</span>
                            @else
                                <span class="badge bg-label-secondary">Tidak</span>
                            @endif
                        </td>
                        <td>
                            @if ($d->jam_awal_istirahat != null)
                                <span class="font-mono text-muted" style="font-size: 11.5px;">
                                    {{ date('H:i', strtotime($d->jam_awal_istirahat)) }} - {{ date('H:i', strtotime($d->jam_akhir_istirahat)) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($d->lintashari == 1)
                                <span class="badge bg-label-warning"><i class="ti ti-moon me-0.5"></i> Lintas Hari</span>
                            @else
                                <span class="badge bg-label-secondary">Reguler</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-primary font-mono fw-bold">
                                {{ $d->total_jam }} Jam
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="mx-auto" style="width: 20px; height: 20px; background-color: {{ $d->color ?? '#1E4D3E' }}; border-radius: 6px; border: 1px solid rgba(0,0,0,0.12);"></div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                @can('jamkerja.edit')
                                    <button type="button" class="btnEdit" kode_jam_kerja="{{ Crypt::encrypt($d->kode_jam_kerja) }}" title="Edit Shift">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                @endcan

                                @can('jamkerja.delete')
                                    <form method="POST" name="deleteform" class="deleteform d-inline m-0"
                                        action="{{ route('jamkerja.delete', Crypt::encrypt($d->kode_jam_kerja)) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-confirm" title="Hapus Shift">
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
                            <i class="ti ti-clock-off text-muted fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data Shift Kerja</h6>
                            <small class="text-muted">Klik Tambah Shift Kerja untuk membuat shift baru.</small>
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
        {{ $jamkerja->links('pagination::bootstrap-5') }}
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

        $("#btnCreate").click(function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Shift Kerja");
            loading();
            $("#loadmodal").load("{{ route('jamkerja.create') }}");
        });

        $(document).on('click', '.btnEdit', function(e) {
            e.preventDefault();
            const kode_jam_kerja = $(this).attr("kode_jam_kerja");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Shift Kerja");
            loading();
            $("#loadmodal").load(`/jamkerja/${kode_jam_kerja}/edit`);
        });
    });
</script>
@endpush
