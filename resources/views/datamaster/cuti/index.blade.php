@extends('layouts.app')
@section('titlepage', 'Jenis Cuti')

@section('content')
@section('navigasi')
    <span>Jenis Cuti</span>
@endsection

<!-- Top Header Toolbar -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <span>Jenis & Alokasi Cuti Karyawan</span>
            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                {{ $cuti->total() }} Total
            </span>
        </h5>
        <small class="text-muted" style="font-size: 12px;">Manajemen kuota hak cuti tahunan, cuti menikah, melahirkan, dan izin khusus karyawan.</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('cuti.create')
            <a href="#" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreate" style="height: 36px; border-radius: 8px;">
                <i class="ti ti-plus"></i>
                <span>Tambah Cuti</span>
            </a>
        @endcan
    </div>
</div>

<!-- Search & Filter Bar (Seamless Minimalist Flex Layout) -->
<div class="card mb-3 card-filter-bar">
    <div class="card-body p-2.5">
        <form action="{{ route('cuti.index') }}" method="GET">
            <div class="d-flex align-items-center gap-2">
                <div class="flex-grow-1">
                    <x-input-with-icon label="" value="{{ Request('nama_cuti') }}" name="nama_cuti"
                        icon="ti ti-search" placeholder="Cari nama atau kode jenis cuti..." hideLabel="true" />
                </div>
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3 flex-shrink-0"
                    style="height: 36px; border-radius: 8px; font-size: 12.5px; font-weight: 600; min-width: 90px;">
                    <i class="ti ti-search"></i>
                    <span>Cari</span>
                </button>
                @if (Request('nama_cuti'))
                    <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-0 flex-shrink-0"
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
                    <th>JENIS CUTI</th>
                    <th class="text-center" style="width: 160px;">ALOKASI HARI</th>
                    <th class="text-end" style="width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cuti as $d)
                    <tr>
                        <td class="text-center font-mono text-muted" style="font-size: 12px;">
                            {{ $loop->iteration + ($cuti->currentPage() - 1) * $cuti->perPage() }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                {{ $d->kode_cuti }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 32px; height: 32px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E;">
                                    <i class="ti ti-calendar-event fs-6"></i>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 13px;">{{ $d->jenis_cuti }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-primary font-mono fw-bold">
                                {{ $d->jumlah_hari }} Hari / Tahun
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                @can('cuti.edit')
                                    <button type="button" class="btnEdit" kode_cuti="{{ Crypt::encrypt($d->kode_cuti) }}" title="Edit Cuti">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                @endcan

                                @can('cuti.delete')
                                    <form method="POST" name="deleteform" class="deleteform d-inline m-0"
                                        action="{{ route('cuti.delete', Crypt::encrypt($d->kode_cuti)) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-confirm" title="Hapus Cuti">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="ti ti-calendar-off text-muted fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data Cuti</h6>
                            <small class="text-muted">Klik Tambah Cuti untuk menambahkan data baru.</small>
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
                @if ($cuti->total() > 0)
                    Menampilkan <span class="fw-bold text-dark font-mono">{{ $cuti->firstItem() }}</span> - <span class="fw-bold text-dark font-mono">{{ $cuti->lastItem() }}</span> dari <span class="fw-bold text-dark font-mono">{{ $cuti->total() }}</span> total cuti
                @else
                    Menampilkan 0 data
                @endif
            </div>

            <!-- Interactive Page Slider (Slide Selector) -->
            @if ($cuti->lastPage() > 1)
                <div class="page-slider-container">
                    <small class="text-muted fw-semibold" style="font-size: 11.5px;">Slide Halaman:</small>
                    <input type="range" class="page-slider-range" id="pageSlider"
                        min="1" max="{{ $cuti->lastPage() }}" value="{{ $cuti->currentPage() }}"
                        oninput="document.getElementById('sliderBadge').innerText = this.value"
                        onchange="navigatePage(this.value)">
                    <span class="badge bg-primary font-mono" id="sliderBadge">{{ $cuti->currentPage() }}</span>
                </div>
            @endif

            <!-- Standard Pagination Links -->
            <div class="d-flex align-items-center">
                {{ $cuti->links('pagination::bootstrap-5') }}
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
            $(".modal-title").text("Tambah Jenis Cuti");
            loading();
            $("#loadmodal").load("{{ route('cuti.create') }}");
        });

        $(document).on('click', '.btnEdit', function(e) {
            e.preventDefault();
            const kode_cuti = $(this).attr("kode_cuti");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Jenis Cuti");
            loading();
            $("#loadmodal").load(`/cuti/${kode_cuti}`);
        });
    });
</script>
@endpush
