@extends('layouts.app')
@section('titlepage', 'Data Karyawan')

@push('mystyle')
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
@endpush

@section('content')

<!-- Standard Page Header -->
<div class="admin-page-header">
    <div class="header-title-group">
        <h5 class="page-title">
            <span>Data Karyawan</span>
            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                {{ $karyawan->total() }} Total
            </span>
        </h5>
        <p class="page-subtitle">Manajemen data profil karyawan, penugasan outlet, shift kerja, dan status akun operasional.</p>
    </div>

    <div class="header-action-group">
        @can('karyawan.create')
            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-1.5" id="btnCreate" style="height: 38px;">
                <i class="ti ti-plus"></i>
                <span>Tambah Karyawan</span>
            </a>
            <a href="{{ route('karyawan.export', request()->query()) }}" class="btn btn-outline-success d-inline-flex align-items-center gap-1.5" style="height: 38px;">
                <i class="ti ti-file-spreadsheet"></i>
                <span>Export Excel</span>
            </a>
            <a href="#" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" id="btnImport" style="height: 38px;">
                <i class="ti ti-file-upload"></i>
                <span>Import Excel</span>
            </a>
        @endcan
    </div>
</div>

<!-- Standard Filter Toolbar (Spacious 2-Tier Responsive Layout) -->
<div class="admin-filter-toolbar mb-3">
    <form action="{{ route('karyawan.index') }}" method="GET" id="filterKaryawanForm">
        <div class="row g-2 mb-2">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="ti ti-search"></i></span>
                    <input type="text" name="nama_karyawan" class="form-control" placeholder="Cari nama atau NIK karyawan..."
                        value="{{ Request('nama_karyawan') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <select name="kode_cabang" class="form-select" onchange="document.getElementById('filterKaryawanForm').submit();">
                    <option value="">Semua Outlet / Cabang</option>
                    @foreach ($cabang as $c)
                        <option value="{{ $c->kode_cabang }}" {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                            {{ textUpperCase($c->nama_cabang) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <select name="kode_dept" class="form-select" onchange="document.getElementById('filterKaryawanForm').submit();">
                    <option value="">Semua Departemen</option>
                    @foreach ($departemen as $d)
                        <option value="{{ $d->kode_dept }}" {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                            {{ textUpperCase($d->nama_dept) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6 col-12">
                <select name="kode_jabatan" class="form-select" onchange="document.getElementById('filterKaryawanForm').submit();">
                    <option value="">Semua Jabatan</option>
                    @foreach ($jabatan as $j)
                        <option value="{{ $j->kode_jabatan }}" {{ Request('kode_jabatan') == $j->kode_jabatan ? 'selected' : '' }}>
                            {{ textUpperCase($j->nama_jabatan) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row g-2 align-items-center">
            <div class="col-lg-3 col-md-4 col-6">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label text-muted small mb-0 text-nowrap">Urutkan:</label>
                    <select name="sort_by" class="form-select form-select-sm" onchange="document.getElementById('filterKaryawanForm').submit();">
                        <option value="nama_karyawan" {{ Request('sort_by') == 'nama_karyawan' ? 'selected' : '' }}>Nama Karyawan</option>
                        <option value="nik_show" {{ Request('sort_by') == 'nik_show' ? 'selected' : '' }}>NIK / ID</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label text-muted small mb-0 text-nowrap">Baris:</label>
                    <select name="per_page" class="form-select form-select-sm font-mono" onchange="document.getElementById('filterKaryawanForm').submit();">
                        <option value="10" {{ Request('per_page', 10) == 10 ? 'selected' : '' }}>10 baris</option>
                        <option value="25" {{ Request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ Request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                        <option value="100" {{ Request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-6 col-md-4 col-12 d-flex justify-content-lg-end justify-content-start gap-2">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-3">
                    <i class="ti ti-search"></i>
                    <span>Cari Data</span>
                </button>
                @if(Request('nama_karyawan') || Request('kode_cabang') || Request('kode_dept') || Request('kode_jabatan') || Request('sort_by') != 'nama_karyawan' || Request('per_page') != 10)
                    <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5 px-3" title="Reset Filter">
                        <i class="ti ti-refresh"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
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
                                            <span class="badge-status badge-status-aktif"><span class="badge-status-dot"></span>Aktif</span>
                                        @else
                                            <span class="badge-status badge-status-nonaktif"><span class="badge-status-dot"></span>Non Aktif</span>
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
                            <div class="d-flex flex-column gap-1">
                                @if (empty($d->id_user))
                                    <span class="badge bg-label-secondary font-mono" style="font-size: 10px; width: fit-content;">
                                        <i class="ti ti-user-x me-1"></i>Belum Ada Akun
                                    </span>
                                @else
                                    <span class="badge bg-label-success font-mono" style="font-size: 10px; width: fit-content;">
                                        <i class="ti ti-user-check me-1"></i>Akun Aktif
                                    </span>
                                @endif
                                <div class="d-flex align-items-center gap-1" style="font-size: 10.5px;">
                                    <span class="d-inline-flex align-items-center gap-1 px-1.5 py-0.5 rounded {{ $d->lock_location == '1' ? 'bg-light text-success' : 'bg-light text-danger' }}" style="border: 1px solid #E2E8F0; line-height: 1;" title="Status Kunci GPS">
                                        <i class="ti {{ $d->lock_location == '1' ? 'ti-lock' : 'ti-lock-open' }}" style="font-size: 11px;"></i> GPS
                                    </span>
                                    <span class="d-inline-flex align-items-center gap-1 px-1.5 py-0.5 rounded {{ $d->lock_jam_kerja == '1' ? 'bg-light text-success' : 'bg-light text-danger' }}" style="border: 1px solid #E2E8F0; line-height: 1;" title="Status Kunci Shift">
                                        <i class="ti {{ $d->lock_jam_kerja == '1' ? 'ti-lock' : 'ti-lock-open' }}" style="font-size: 11px;"></i> Shift
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

<!-- Pagination Footer -->
<div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px;">
    <div class="card-body py-2.5 px-3">
        {{ $karyawan->links('pagination::bootstrap-5') }}
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" size="modal-lg" />
<x-modal-form id="modalSetJamkerja" show="loadmodalSetJamkerja" size="modal-lg" title="Set Jam Kerja" />
<x-modal-form id="modalSetCabang" show="loadmodalSetCabang" size="modal-lg" title="Set Cabang Karyawan" />
<x-modal-form id="modalImport" show="loadmodalImport" size="modal-lg" title="Import Data Karyawan" />

@endsection

@push('myscript')
<script>
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
