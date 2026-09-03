@extends('layouts.app')
@section('titlepage', 'Cabang / Outlet')

@section('content')
@section('navigasi')
    <span>Cabang & Outlet</span>
@endsection

<!-- Top Header Toolbar -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <span>Cabang / Outlet Coffee</span>
            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                {{ $cabang->total() }} Total
            </span>
        </h5>
        <small class="text-muted" style="font-size: 12px;">Manajemen data multi-outlet cabang, radius presensi, dan titik koordinat GPS.</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('cabang.create')
            <a href="#" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" id="btncreateCabang" style="height: 36px; border-radius: 8px;">
                <i class="ti ti-plus"></i>
                <span>Tambah Cabang</span>
            </a>
        @endcan
    </div>
</div>

<!-- Search & Filter Bar (Seamless Minimalist Flex Layout) -->
<div class="card mb-3 card-filter-bar">
    <div class="card-body p-2.5">
        <form action="{{ route('cabang.index') }}" method="GET">
            <div class="d-flex align-items-center gap-2">
                <div class="flex-grow-1">
                    <x-input-with-icon label="" value="{{ Request('nama_cabang') }}" name="nama_cabang"
                        icon="ti ti-search" placeholder="Cari nama atau kode cabang..." hideLabel="true" />
                </div>
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3 flex-shrink-0"
                    style="height: 36px; border-radius: 8px; font-size: 12.5px; font-weight: 600; min-width: 90px;">
                    <i class="ti ti-search"></i>
                    <span>Cari</span>
                </button>
                @if (Request('nama_cabang'))
                    <a href="{{ route('cabang.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-0 flex-shrink-0"
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
                                    style="width: 32px; height: 32px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E;">
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

<!-- Pagination Footer with Slide Controls -->
<div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <!-- Counter info -->
            <div class="text-muted" style="font-size: 12px;">
                @if ($cabang->total() > 0)
                    Menampilkan <span class="fw-bold text-dark font-mono">{{ $cabang->firstItem() }}</span> - <span class="fw-bold text-dark font-mono">{{ $cabang->lastItem() }}</span> dari <span class="fw-bold text-dark font-mono">{{ $cabang->total() }}</span> total cabang
                @else
                    Menampilkan 0 data
                @endif
            </div>

            <!-- Interactive Page Slider (Slide Selector) -->
            @if ($cabang->lastPage() > 1)
                <div class="page-slider-container">
                    <small class="text-muted fw-semibold" style="font-size: 11.5px;">Slide Halaman:</small>
                    <input type="range" class="page-slider-range" id="pageSlider"
                        min="1" max="{{ $cabang->lastPage() }}" value="{{ $cabang->currentPage() }}"
                        oninput="document.getElementById('sliderBadge').innerText = this.value"
                        onchange="navigatePage(this.value)">
                    <span class="badge bg-primary font-mono" id="sliderBadge">{{ $cabang->currentPage() }}</span>
                </div>
            @endif

            <!-- Standard Pagination Links -->
            <div class="d-flex align-items-center">
                {{ $cabang->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<x-modal-form id="mdlcreateCabang" size="" show="loadcreateCabang" title="Tambah Cabang" />
<x-modal-form id="mdleditCabang" size="" show="loadeditCabang" title="Edit Cabang" />

@endsection

@push('myscript')
<script>
    function navigatePage(pageNum) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', pageNum);
        window.location.href = url.toString();
    }

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
    });
</script>
@endpush
