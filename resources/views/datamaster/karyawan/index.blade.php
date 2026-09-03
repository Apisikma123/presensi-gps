@extends('layouts.app')
@section('titlepage', 'Data Karyawan')

<style>
    /* Table Animations & Clean Design */
    .table-karyawan-wrapper {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #E2E8F0;
        background: #FFFFFF !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .table-karyawan {
        margin-bottom: 0;
        background: #FFFFFF !important;
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
        animation: rowSlideIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) both;
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

    @keyframes rowSlideIn {
        from {
            opacity: 0;
            transform: translateY(4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Page Slider Control */
    .page-slider-container {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: #F8FAFC;
        padding: 6px 14px;
        border-radius: 10px;
        border: 1px solid #E2E8F0;
    }

    .page-slider-range {
        accent-color: #1E4D3E;
        cursor: pointer;
        height: 5px;
        width: 120px;
    }

    .page-slider-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        font-weight: 700;
        color: #1E4D3E;
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        padding: 2px 8px;
        border-radius: 6px;
    }

    /* Clean 3-Dots Action Button & Dropstart Menu (Opens to Left) */
    .btn-action-dots {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        color: #475569;
        transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
    }

    .btn-action-dots:hover, .btn-action-dots:focus, .show > .btn-action-dots {
        background: #F1F5F9;
        border-color: #CBD5E1;
        color: #0F172A;
    }

    .dropstart .dropdown-menu-karyawan {
        margin-right: 8px !important;
        min-width: 220px;
        border-radius: 12px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.14), 0 2px 8px rgba(15, 23, 42, 0.04);
        padding: 6px;
        background: #FFFFFF;
        z-index: 1060;
    }

    .dropdown-menu-karyawan .dropdown-item {
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #334155;
        transition: all 0.15s ease;
        background: transparent;
        border: none;
        width: 100%;
        text-align: left;
    }

    .dropdown-menu-karyawan .dropdown-item:hover {
        background-color: #F8FAF8;
        color: #1E4D3E;
    }

    .dropdown-menu-karyawan .dropdown-item.text-danger:hover {
        background-color: #FEF2F2;
        color: #DC2626 !important;
    }

    .dropdown-menu-karyawan .dropdown-item i {
        font-size: 15px;
        width: 18px;
        text-align: center;
    }

    .dropdown-menu-karyawan .dropdown-header {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94A3B8;
        padding: 6px 12px 2px 12px;
    }

    .dropdown-menu-karyawan .dropdown-divider {
        margin: 4px 0;
        border-top-color: #F1F5F9;
    }
</style>

@section('content')

<!-- Header Card Toolbar -->
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px;">
    <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0 fw-bold text-dark">Data Karyawan</h5>
                    <span class="badge bg-label-primary font-mono" style="font-size: 11px;">
                        {{ $karyawan->total() }} Total
                    </span>
                </div>
                <small class="text-muted" style="font-size: 12px;">Manajemen data karyawan, penugasan cabang, jam kerja & akun operasional.</small>
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">
                @can('karyawan.create')
                    <a href="#" class="btn btn-sm btn-primary d-flex align-items-center gap-1.5" id="btnCreate">
                        <i class="ti ti-plus" style="font-size: 15px;"></i>
                        <span>Tambah Karyawan</span>
                    </a>
                    <a href="{{ route('karyawan.export', request()->query()) }}" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1.5">
                        <i class="ti ti-file-spreadsheet" style="font-size: 15px;"></i>
                        <span>Export Excel</span>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1.5" id="btnImport">
                        <i class="ti ti-file-upload" style="font-size: 15px;"></i>
                        <span>Import Excel</span>
                    </a>
                    @can('users.create')
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-users-cog me-1"></i> Aksi User
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 10px; border: 1px solid #E2E8F0;">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('karyawan.generatealluser') }}">
                                        <i class="ti ti-user-plus text-warning"></i>
                                        <span>Buat Akun User (Semua)</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger delete-all-user" href="{{ route('karyawan.deletealluser') }}">
                                        <i class="ti ti-user-x"></i>
                                        <span>Reset Semua Akun User</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endcan
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);">
    <div class="card-body p-2.5">
        <form action="{{ route('karyawan.index') }}" method="GET" id="filterKaryawanForm">
            <div class="row g-2 align-items-center">
                <div class="col-xl-3 col-lg-2 col-md-6 col-12">
                    <div class="input-group" style="height: 38px;">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 8px 0 0 8px; border-color: #E2E8F0; padding: 0 10px;">
                            <i class="ti ti-search" style="font-size: 15px;"></i>
                        </span>
                        <input type="text" name="nama_karyawan" class="form-control border-start-0 ps-1" placeholder="Cari Nama / NIK..."
                            value="{{ Request('nama_karyawan') }}" style="height: 38px; border-radius: 0 8px 8px 0; border-color: #E2E8F0; font-size: 13px;">
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-6">
                    <select name="kode_cabang" class="form-select" onchange="document.getElementById('filterKaryawanForm').submit();"
                        style="height: 38px; border-radius: 8px; border-color: #E2E8F0; font-size: 13px;">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-6">
                    <select name="kode_dept" class="form-select" onchange="document.getElementById('filterKaryawanForm').submit();"
                        style="height: 38px; border-radius: 8px; border-color: #E2E8F0; font-size: 13px;">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                {{ strtoupper($d->nama_dept) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-6">
                    <select name="kode_jabatan" class="form-select" onchange="document.getElementById('filterKaryawanForm').submit();"
                        style="height: 38px; border-radius: 8px; border-color: #E2E8F0; font-size: 13px;">
                        <option value="">Semua Jabatan</option>
                        @foreach ($jabatan as $j)
                            <option value="{{ $j->kode_jabatan }}" {{ Request('kode_jabatan') == $j->kode_jabatan ? 'selected' : '' }}>
                                {{ $j->nama_jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-1 col-lg-1 col-md-3 col-6">
                    <select name="sort_by" class="form-select" onchange="document.getElementById('filterKaryawanForm').submit();"
                        style="height: 38px; border-radius: 8px; border-color: #E2E8F0; font-size: 13px;">
                        <option value="nama_karyawan" {{ Request('sort_by') == 'nama_karyawan' ? 'selected' : '' }}>Nama</option>
                        <option value="nik_show" {{ Request('sort_by') == 'nik_show' ? 'selected' : '' }}>NIK</option>
                    </select>
                </div>
                <div class="col-xl-1 col-lg-1 col-md-3 col-6">
                    <select name="per_page" class="form-select font-mono" onchange="document.getElementById('filterKaryawanForm').submit();"
                        style="height: 38px; border-radius: 8px; border-color: #E2E8F0; font-size: 13px;">
                        <option value="10" {{ Request('per_page', 10) == 10 ? 'selected' : '' }}>10 / hal</option>
                        <option value="25" {{ Request('per_page') == 25 ? 'selected' : '' }}>25 / hal</option>
                        <option value="50" {{ Request('per_page') == 50 ? 'selected' : '' }}>50 / hal</option>
                        <option value="100" {{ Request('per_page') == 100 ? 'selected' : '' }}>100 / hal</option>
                    </select>
                </div>
                <div class="col-xl-1 col-lg-2 col-md-6 col-12 d-flex align-items-center gap-1.5">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center shadow-sm"
                        title="Cari Data"
                        style="height: 38px; width: 38px; min-width: 38px; border-radius: 8px; background-color: var(--theme-color-1, #1E4D3E); border: 1px solid #163C30; padding: 0; transition: all 0.2s ease;">
                        <i class="ti ti-search" style="font-size: 16px;"></i>
                    </button>
                    @if(Request('nama_karyawan') || Request('kode_cabang') || Request('kode_dept') || Request('kode_jabatan') || Request('sort_by') != 'nama_karyawan' || Request('per_page') != 10)
                        <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                            style="height: 38px; width: 38px; min-width: 38px; border-radius: 8px; border-color: #CBD5E1; color: #64748B; padding: 0; transition: all 0.2s ease;" title="Reset Filter">
                            <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table / Card List -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover table-karyawan align-middle w-100">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Karyawan</th>
                    <th>Unit & Posisi</th>
                    <th>Penugasan & Status</th>
                    <th>Masa Kerja</th>
                    <th>Akun & Keamanan</th>
                    <th class="text-end" style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawan as $d)
                    @php
                        $words = explode(' ', $d->nama_karyawan);
                        $initials = '';
                        foreach ($words as $w) {
                            if (isset($w[0])) $initials .= $w[0];
                        }
                        $initials = strtoupper(substr($initials, 0, 2));

                        $awal = new DateTime($d->tanggal_masuk);
                        $akhir = new DateTime();
                        $masa_kerja = $akhir->diff($awal);
                        $status_karyawan_text = $d->status_karyawan == 'K' ? 'Kontrak' : ($d->status_karyawan == 'T' ? 'Tetap' : $d->status_karyawan);
                        $no = ($karyawan->currentPage() - 1) * $karyawan->perPage() + $loop->iteration;
                    @endphp
                    <tr>
                        <td class="text-muted font-mono" style="font-size: 11px;">{{ $no }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                @if (!empty($d->foto))
                                    <img src="{{ getfotoKaryawan($d->foto) }}" alt="Avatar" class="rounded-circle flex-shrink-0"
                                        style="width: 38px; height: 38px; object-fit: cover; border: 1px solid #E2E8F0;"
                                        onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                    <div class="rounded-circle flex-shrink-0 align-items-center justify-content-center fw-bold"
                                        style="display: none; width: 38px; height: 38px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E; font-size: 12.5px; border: 1px solid rgba(30, 77, 62, 0.15);">
                                        {{ $initials }}
                                    </div>
                                @else
                                    <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center fw-bold"
                                        style="width: 38px; height: 38px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E; font-size: 12.5px; border: 1px solid rgba(30, 77, 62, 0.15);">
                                        {{ $initials }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 13.5px;">{{ $d->nama_karyawan }}</div>
                                    <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                        <span class="badge bg-light text-muted font-mono" style="font-size: 10.5px; border: 1px solid #E2E8F0;">
                                            {{ $d->nik_show ?? $d->nik }}
                                        </span>
                                        @if ($d->status_aktif_karyawan == '1')
                                            <span class="badge rounded-pill bg-label-success" style="font-size: 9.5px;">Aktif</span>
                                        @else
                                            <span class="badge rounded-pill bg-label-danger" style="font-size: 9.5px;">Non Aktif</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="badge bg-label-primary align-self-start" style="font-size: 11px;">
                                    {{ $d->nama_jabatan }}
                                </span>
                                <small class="text-muted" style="font-size: 11.5px;">
                                    <i class="ti ti-building me-1"></i>{{ $d->nama_dept }}
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="text-dark fw-semibold" style="font-size: 12.5px;">
                                    <i class="ti ti-map-pin me-1 text-muted"></i>{{ $d->nama_cabang }}
                                </span>
                                <div class="d-flex gap-1 align-items-center">
                                    <span class="badge bg-label-warning" style="font-size: 10px;">{{ $status_karyawan_text }}</span>
                                    @if ($d->jenis_upah)
                                        <span class="badge bg-label-secondary font-mono" style="font-size: 10px;">{{ $d->jenis_upah }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark fw-semibold" style="font-size: 12px;">{{ date('d M Y', strtotime($d->tanggal_masuk)) }}</div>
                            <small class="text-muted font-mono" style="font-size: 11px;">{{ $masa_kerja->y }} th {{ $masa_kerja->m }} bln</small>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1.5">
                                @if (empty($d->id_user))
                                    <span class="badge bg-label-secondary align-self-start font-mono" style="font-size: 10px;">
                                        <i class="ti ti-user-x me-1"></i>Belum Ada Akun
                                    </span>
                                @else
                                    <span class="badge bg-label-success align-self-start font-mono" style="font-size: 10px;">
                                        <i class="ti ti-user-check me-1"></i>Akun Aktif
                                    </span>
                                @endif
                                <div class="d-flex align-items-center gap-1.5" style="font-size: 11px;">
                                    <span class="d-inline-flex align-items-center gap-1 px-1.5 py-0.5 rounded {{ $d->lock_location == '1' ? 'bg-light text-success' : 'bg-light text-danger' }}" style="border: 1px solid #E2E8F0;" title="Status Kunci GPS">
                                        <i class="ti {{ $d->lock_location == '1' ? 'ti-lock' : 'ti-lock-open' }}" style="font-size: 12px;"></i> GPS
                                    </span>
                                    <span class="d-inline-flex align-items-center gap-1 px-1.5 py-0.5 rounded {{ $d->lock_jam_kerja == '1' ? 'bg-light text-success' : 'bg-light text-danger' }}" style="border: 1px solid #E2E8F0;" title="Status Kunci Shift">
                                        <i class="ti {{ $d->lock_jam_kerja == '1' ? 'ti-lock' : 'ti-lock-open' }}" style="font-size: 12px;"></i> Shift
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            @can('karyawan.show')
                                <a href="{{ route('karyawan.show', Crypt::encrypt($d->nik)) }}" 
                                    class="btn-table-detail" 
                                    title="Lihat Profil & Kelola Karyawan">
                                    <span>Detail</span>
                                    <i class="ti ti-chevron-right"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="ti ti-users-minus text-muted fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data Karyawan</h6>
                            <small class="text-muted">Coba ubah kata kunci pencarian atau reset filter.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Footer with Slide Controls -->
<div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px;">
    <div class="card-body py-2.5 px-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <!-- Counter info -->
            <div class="text-muted" style="font-size: 12px;">
                @if ($karyawan->total() > 0)
                    Menampilkan <span class="fw-bold text-dark font-mono">{{ $karyawan->firstItem() }}</span> - <span class="fw-bold text-dark font-mono">{{ $karyawan->lastItem() }}</span> dari <span class="fw-bold text-dark font-mono">{{ $karyawan->total() }}</span> total karyawan
                @else
                    Menampilkan 0 data
                @endif
            </div>

            <!-- Interactive Page Slider (Slide Selector) -->
            @if ($karyawan->lastPage() > 1)
                <div class="page-slider-container">
                    <small class="text-muted fw-semibold" style="font-size: 11.5px;">Slide Halaman:</small>
                    <input type="range" class="page-slider-range" id="pageSlider"
                        min="1" max="{{ $karyawan->lastPage() }}" value="{{ $karyawan->currentPage() }}"
                        oninput="document.getElementById('sliderPageVal').textContent = this.value;"
                        onchange="navigatePage(this.value)">
                    <span class="page-slider-badge">
                        Hal <span id="sliderPageVal">{{ $karyawan->currentPage() }}</span> / {{ $karyawan->lastPage() }}
                    </span>
                </div>
            @endif

            <!-- Standard Pagination Links -->
            <div class="d-flex align-items-center">
                {{ $karyawan->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />
<x-modal-form id="modalSetJamkerja" show="loadmodalSetJamkerja" size="modal-lg" title="Set Jam Kerja" />
<x-modal-form id="modalSetCabang" show="loadmodalSetCabang" size="modal-lg" title="Set Cabang Karyawan" />
<x-modal-form id="modalImport" show="loadmodalImport" size="modal-lg" title="Import Data Karyawan" />

@endsection

@push('myscript')
<script>
    function navigatePage(pageNum) {
        if (window.startPageProgress) window.startPageProgress();
        const url = new URL(window.location.href);
        url.searchParams.set('page', pageNum);
        window.location.href = url.toString();
    }

    $(function() {
        $(document).on('click', '#btnCreate', function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Data Karyawan");
            $("#loadmodal").load("{{ route('karyawan.create') }}");
        });

        $(document).on('click', '#btnImport', function(e) {
            e.preventDefault();
            $("#modalImport").modal("show");
            $("#loadmodalImport").load("{{ route('karyawan.import') }}");
        });

        $(document).on('click', '.btnEdit', function(e) {
            e.preventDefault();
            const nik = $(this).attr("nik");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Karyawan");
            $("#loadmodal").load(`/karyawan/${nik}/edit`);
        });

        $(document).on('click', '.btnSetJamkerja', function(e) {
            e.preventDefault();
            const nik = $(this).attr("nik");
            $("#modalSetJamkerja").modal("show");
            $("#loadmodalSetJamkerja").html(`<div class="sk-wave sk-primary" style="margin:auto">
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
            </div>`);
            $("#loadmodalSetJamkerja").load(`/karyawan/${nik}/setjamkerja`);
        });

        $(document).on('click', '.btnSetCabang', function(e) {
            e.preventDefault();
            const nik = $(this).attr("nik");
            $("#modalSetCabang").modal("show");
            $("#loadmodalSetCabang").html(`<div class="sk-wave sk-primary" style="margin:auto">
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
            </div>`);
            $("#loadmodalSetCabang").load(`/karyawan/${nik}/setcabang`);
        });

        $(document).on('click', '.delete-all-user', function(e) {
            e.preventDefault();
            var href = $(this).attr("href");
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Semua User dengan Role Karyawan akan dihapus!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1E4D3E",
                cancelButtonColor: "#DC2626",
                confirmButtonText: "Ya, Hapus Semua!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
</script>
@endpush
