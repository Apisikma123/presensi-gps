@extends('layouts.app')
@section('titlepage', 'Jabatan & Posisi')

@section('content')
@section('navigasi')
    <span>Jabatan & Posisi</span>
@endsection

<!-- Top Header Toolbar -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <span>Jabatan & Posisi Karyawan</span>
            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                {{ $jabatan->total() }} Total
            </span>
        </h5>
        <small class="text-muted" style="font-size: 12px;">Manajemen level posisi, barista, kasir, kitchen, dan manager outlet coffee shop.</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('jabatan.create')
            <a href="#" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreate" style="height: 36px; border-radius: 8px;">
                <i class="ti ti-plus"></i>
                <span>Tambah Jabatan</span>
            </a>
        @endcan
    </div>
</div>

<!-- Search & Filter Bar (Seamless Minimalist Flex Layout) -->
<div class="card mb-3 card-filter-bar">
    <div class="card-body p-2.5">
        <form action="{{ route('jabatan.index') }}" method="GET">
            <div class="d-flex align-items-center gap-2">
                <div class="flex-grow-1">
                    <x-input-with-icon label="" value="{{ Request('nama_jabatan') }}" name="nama_jabatan"
                        icon="ti ti-search" placeholder="Cari nama atau kode jabatan..." hideLabel="true" />
                </div>
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3 flex-shrink-0"
                    style="height: 36px; border-radius: 8px; font-size: 12.5px; font-weight: 600; min-width: 90px;">
                    <i class="ti ti-search"></i>
                    <span>Cari</span>
                </button>
                @if (Request('nama_jabatan'))
                    <a href="{{ route('jabatan.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-0 flex-shrink-0"
                        style="height: 36px; width: 36px; min-width: 36px; border-radius: 8px;" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                    </a>
                @endif
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
                                    style="width: 32px; height: 32px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E;">
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

<!-- Pagination Footer with Slide Controls -->
<div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <!-- Counter info -->
            <div class="text-muted" style="font-size: 12px;">
                @if ($jabatan->total() > 0)
                    Menampilkan <span class="fw-bold text-dark font-mono">{{ $jabatan->firstItem() }}</span> - <span class="fw-bold text-dark font-mono">{{ $jabatan->lastItem() }}</span> dari <span class="fw-bold text-dark font-mono">{{ $jabatan->total() }}</span> total jabatan
                @else
                    Menampilkan 0 data
                @endif
            </div>

            <!-- Interactive Page Slider (Slide Selector) -->
            @if ($jabatan->lastPage() > 1)
                <div class="page-slider-container">
                    <small class="text-muted fw-semibold" style="font-size: 11.5px;">Slide Halaman:</small>
                    <input type="range" class="page-slider-range" id="pageSlider"
                        min="1" max="{{ $jabatan->lastPage() }}" value="{{ $jabatan->currentPage() }}"
                        oninput="document.getElementById('sliderBadge').innerText = this.value"
                        onchange="navigatePage(this.value)">
                    <span class="badge bg-primary font-mono" id="sliderBadge">{{ $jabatan->currentPage() }}</span>
                </div>
            @endif

            <!-- Standard Pagination Links -->
            <div class="d-flex align-items-center">
                {{ $jabatan->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />

@endsection

@push('myscript')
<script>
    function navigatePage(pageNum) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', pageNum);
        window.location.href = url.toString();
    }

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
            $(".modal-title").text("Tambah Data Jabatan");
            loading();
            $("#loadmodal").load("{{ route('jabatan.create') }}");
        });

        $(document).on('click', '.btnEdit', function(e) {
            e.preventDefault();
            const kode_jabatan = $(this).attr("kode_jabatan");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Jabatan");
            loading();
            $("#loadmodal").load(`/jabatan/${kode_jabatan}`);
        });
    });
</script>
@endpush
