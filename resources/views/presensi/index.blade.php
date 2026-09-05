@extends('layouts.app')
@section('titlepage', 'Monitoring Presensi')

@section('content')
@section('navigasi')
    <span>Monitoring Presensi</span>
@endsection
@push('mystyle')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/leaflet.css') }}" />
@endpush
<style>
    /* Minimalist Editorial Table & Card System */
    .coffee-table-container {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    
    .coffee-table {
        width: 100%;
        margin-bottom: 0;
        vertical-align: middle;
        border-collapse: collapse;
    }
    
    .coffee-table thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    
    .coffee-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        font-size: 13px;
        background: #ffffff;
        transition: background-color 0.15s ease;
    }
    
    .coffee-table tbody tr:hover td {
        background-color: #fafbfd;
    }
    
    .coffee-table tbody tr:last-child td {
        border-bottom: none;
    }

    .pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 9999px;
        line-height: 1.3;
    }
    
    .pill-hadir { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .pill-terlambat { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .pill-izin { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .pill-sakit { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .pill-cuti { background: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }
    .pill-alpa { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .pill-belum { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

    /* Mobile Minimalist Card */
    .mobile-staff-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 10px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }

    .time-mono {
        font-family: 'JetBrains Mono', 'Geist Mono', 'SF Mono', monospace;
        font-weight: 600;
        letter-spacing: -0.02em;
    }

    .btn-action-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        transition: all 0.15s ease;
    }

    .btn-action-icon:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }
</style>

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Monitoring Presensi</h4>
        <p class="page-subtitle text-muted mb-0">Pantau kehadiran harian karyawan, ketepatan waktu, dan foto presensi secara langsung.</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Filter Toolbar -->
        <div class="card admin-filter-toolbar mb-3">
            <div class="card-body p-3">
                <form action="{{ route('presensi.index') }}" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-lg-3 col-md-4 col-12">
                            <label class="form-label text-xs fw-bold text-muted mb-1 d-block">Tanggal Presensi</label>
                            <x-input-with-icon label="" value="{{ Request('tanggal') }}" name="tanggal" icon="ti ti-calendar"
                                datepicker="flatpickr-date" placeholder="Pilih Tanggal..." hideLabel="true" />
                        </div>
                        <div class="col-lg-3 col-md-4 col-12">
                            <label class="form-label text-xs fw-bold text-muted mb-1 d-block">Outlet / Cabang</label>
                            <div class="form-group mb-0">
                                <x-select label="" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                    selected="{{ Request('kode_cabang') }}" upperCase="true" select2="select2Kodecabangsearch"
                                    placeholder="Semua Outlet / Cabang" hideLabel="true" />
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-12">
                            <label class="form-label text-xs fw-bold text-muted mb-1 d-block">Cari Karyawan</label>
                            <x-input-with-icon label="" value="{{ Request('nama_karyawan') }}" name="nama_karyawan" icon="ti ti-search"
                                placeholder="Nama karyawan, barista..." hideLabel="true" />
                        </div>
                        <div class="col-lg-3 col-md-12 col-12 d-flex align-items-end gap-2 pt-lg-4">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1" style="height: 38px;">
                                <i class="ti ti-search" style="font-size: 15px;"></i>
                                <span>Cari Data</span>
                            </button>
                            @if (Request('tanggal') || Request('kode_cabang') || Request('nama_karyawan'))
                                <a href="{{ route('presensi.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1" style="height: 38px; padding: 0 12px;" title="Reset Filter">
                                    <i class="ti ti-refresh" style="font-size: 14px;"></i>
                                    <span>Reset</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="coffee-table-container">
            <!-- Desktop Minimalist Table View (Screen >= 992px) -->
            <div class="table-responsive d-none d-lg-block">
                <table class="coffee-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Karyawan Outlet</th>
                            <th>Outlet & Posisi</th>
                            <th>Shift Kerja</th>
                            <th>Presensi Masuk</th>
                            <th>Presensi Pulang</th>
                            <th class="text-center">Status Kehadiran</th>
                            <th class="text-end" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($karyawan as $index => $d)
                            @php
                                $tanggal_presensi = !empty(Request('tanggal')) ? Request('tanggal') : date('Y-m-d');
                                $jam_masuk = $tanggal_presensi . ' ' . $d->jam_masuk;
                                $terlambat = hitungjamterlambat($d->jam_in, $jam_masuk);
                                $potongan_tidak_hadir = $d->status == 'a' ? $d->total_jam : 0;
                                $pulangcepat = hitungpulangcepat(
                                    $tanggal_presensi,
                                    $d->jam_out,
                                    $d->jam_pulang,
                                    $d->istirahat,
                                    $d->jam_awal_istirahat,
                                    $d->jam_akhir_istirahat,
                                    $d->lintashari,
                                );



                                $words = explode(' ', $d->nama_karyawan);
                                $initials = '';
                                foreach ($words as $w) {
                                    if (isset($w[0])) $initials .= $w[0];
                                }
                                $initials = strtoupper(substr($initials, 0, 2));
                                $row_num = $karyawan->firstItem() ? ($karyawan->firstItem() + $index) : ($index + 1);
                            @endphp
                            <tr>
                                <!-- No -->
                                <td class="text-muted font-mono" style="font-size: 12px;">{{ $row_num }}</td>

                                <!-- Karyawan -->
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        @if (!empty($d->foto))
                                            <img src="{{ getfotoKaryawan($d->foto) }}" alt="Avatar"
                                                class="rounded-circle shadow-none flex-shrink-0"
                                                style="width: 38px; height: 38px; object-fit: cover; border: 1px solid #e2e8f0;"
                                                onerror="this.onerror=null;this.src='{{ asset('assets/img/avatars/default.png') }}';">
                                        @else
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                                style="width: 38px; height: 38px; background: rgba(50, 116, 94, 0.1); color: #32745e; font-size: 12.5px; border: 1px solid rgba(50, 116, 94, 0.2);">
                                                {{ $initials }}
                                            </div>
                                        @endif
                                        <div class="overflow-hidden">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 13.5px;">{{ $d->nama_karyawan }}</div>
                                            <div class="text-muted font-mono mt-0.5" style="font-size: 11px;">
                                                <i class="ti ti-id me-0.5"></i>{{ $d->nik_show ?? $d->nik }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Outlet & Role -->
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge" style="background: #f1f5f9; color: #334155; font-size: 11px; font-weight: 600; width: fit-content;">
                                            {{ $d->nama_jabatan ?? $d->kode_dept }}
                                        </span>
                                        <span class="text-muted" style="font-size: 11.5px;">
                                            <i class="ti ti-map-pin me-1 text-slate-400"></i>{{ $d->nama_cabang ?? $d->kode_cabang }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Shift Kerja -->
                                <td>
                                    @if ($d->kode_jam_kerja != null)
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-dark" style="font-size: 12.5px;">{{ $d->nama_jam_kerja }}</span>
                                            <span class="text-muted font-mono" style="font-size: 11px;">
                                                {{ date('H:i', strtotime($d->jam_masuk)) }} - {{ date('H:i', strtotime($d->jam_pulang)) }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 12px;">-</span>
                                    @endif
                                </td>

                                <!-- Jam Masuk -->
                                <td>
                                    @if ($d->jam_in != null)
                                        <div class="d-flex align-items-center gap-1.5">
                                            <a href="#" class="btnShowpresensi_in time-mono text-dark text-decoration-none fw-bold" id="{{ $d->id }}" status="in" style="font-size: 13px;" title="Lihat Foto Masuk">
                                                {{ date('H:i', strtotime($d->jam_in)) }}
                                                @if (!empty($d->foto_in))
                                                    <i class="ti ti-camera text-primary ms-0.5" style="font-size: 13px;"></i>
                                                @endif
                                            </a>
                                            @if ($terlambat != null && $terlambat['menitterlambat'] > 0)
                                                <span class="badge rounded-pill bg-label-danger font-mono" style="font-size: 10px;" title="Terlambat {{ $terlambat['menitterlambat'] }} Menit">
                                                    +{{ $terlambat['menitterlambat'] }}m
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 12px;">-</span>
                                    @endif
                                </td>

                                <!-- Jam Pulang -->
                                <td>
                                    @if ($d->jam_out != null)
                                        <div class="d-flex align-items-center gap-1.5">
                                            <a href="#" class="btnShowpresensi_out time-mono text-dark text-decoration-none fw-bold" id="{{ $d->id }}" status="out" style="font-size: 13px;" title="Lihat Foto Pulang">
                                                {{ date('H:i', strtotime($d->jam_out)) }}
                                                @if (!empty($d->foto_out))
                                                    <i class="ti ti-camera text-primary ms-0.5" style="font-size: 13px;"></i>
                                                @endif
                                            </a>
                                            @if ($pulangcepat > 0)
                                                <span class="badge rounded-pill bg-label-warning font-mono" style="font-size: 10px;" title="Pulang Cepat">
                                                    -{{ $pulangcepat }}j
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 12px;">-</span>
                                    @endif
                                </td>

                                <!-- Status Kehadiran -->
                                <td class="text-center">
                                    @if ($d->status == 'h')
                                        @if (!empty($d->is_dispensasi))
                                            <span class="pill-badge" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">
                                                <i class="ti ti-clock-check"></i> Dispensasi
                                            </span>
                                        @elseif ($terlambat != null && $terlambat['menitterlambat'] > 0)
                                            <span class="pill-badge pill-terlambat">
                                                <i class="ti ti-clock-exclamation"></i> Terlambat
                                            </span>
                                        @else
                                            <span class="pill-badge pill-hadir">
                                                <i class="ti ti-circle-check"></i> Tepat Waktu
                                            </span>
                                        @endif
                                    @elseif($d->status == 'i')
                                        <span class="pill-badge pill-izin">
                                            <i class="ti ti-file-description"></i> Izin
                                        </span>
                                    @elseif($d->status == 's')
                                        <span class="pill-badge pill-sakit">
                                            <i class="ti ti-ambulance"></i> Sakit
                                        </span>
                                    @elseif($d->status == 'c')
                                        <span class="pill-badge pill-cuti">
                                            <i class="ti ti-calendar-event"></i> Cuti
                                        </span>
                                    @elseif($d->status == 'a')
                                        <span class="pill-badge pill-alpa">
                                            <i class="ti ti-x"></i> Tidak Hadir
                                        </span>
                                    @else
                                        <span class="pill-badge pill-belum">
                                            <i class="ti ti-clock"></i> Belum Masuk
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @if (isset($d->status_potongan))
                                            <button class="btn-action-icon" disabled title="Laporan Terkunci"><i class="ti ti-lock"></i></button>
                                        @else
                                            <a href="#" class="btn-action-icon koreksiPresensi text-success" nik="{{ Crypt::encrypt($d->nik) }}"
                                                tanggal="{{ $tanggal_presensi }}" title="Koreksi Presensi">
                                                <i class="ti ti-edit"></i>
                                            </a>

                                            @if(!empty($d->id))
                                            <form action="{{ route('presensi.delete', $d->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action-icon text-danger delete-confirm" title="Hapus Presensi">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="ti ti-users-minus fs-1 mb-2 opacity-50"></i>
                                        <h6 class="fw-bold mb-1 text-dark">Tidak Ada Data Presensi Karyawan</h6>
                                        <small class="text-muted">Gunakan filter tanggal atau outlet di atas untuk memuat data.</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Clean Responsive Cards (Screen < 992px) -->
            <div class="d-lg-none p-2">
                @forelse ($karyawan as $d)
                    @php
                        $tanggal_presensi = !empty(Request('tanggal')) ? Request('tanggal') : date('Y-m-d');
                        $jam_masuk = $tanggal_presensi . ' ' . $d->jam_masuk;
                        $terlambat = hitungjamterlambat($d->jam_in, $jam_masuk);
                        $words = explode(' ', $d->nama_karyawan);
                        $initials = '';
                        foreach ($words as $w) {
                            if (isset($w[0])) $initials .= $w[0];
                        }
                        $initials = strtoupper(substr($initials, 0, 2));
                    @endphp
                    <div class="mobile-staff-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                @if (!empty($d->foto))
                                    <img src="{{ getfotoKaryawan($d->foto) }}" alt="Avatar"
                                        class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;"
                                        onerror="this.onerror=null;this.src='{{ asset('assets/img/avatars/default.png') }}';">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                        style="width: 36px; height: 36px; background: rgba(50, 116, 94, 0.1); color: #32745e; font-size: 12px;">
                                        {{ $initials }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 13.5px;">{{ $d->nama_karyawan }}</div>
                                    <small class="text-muted">{{ $d->nama_jabatan ?? $d->kode_dept }} • {{ $d->kode_cabang }}</small>
                                </div>
                            </div>
                            <div>
                                @if ($d->status == 'h')
                                    @if (!empty($d->is_dispensasi))
                                        <span class="pill-badge" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; font-size: 10.5px;">Dispensasi</span>
                                    @elseif ($terlambat != null && $terlambat['menitterlambat'] > 0)
                                        <span class="pill-badge pill-terlambat" style="font-size: 10.5px;">Telat</span>
                                    @else
                                        <span class="pill-badge pill-hadir" style="font-size: 10.5px;">Hadir</span>
                                    @endif
                                @elseif($d->status == 'i')
                                    <span class="pill-badge pill-izin" style="font-size: 10.5px;">Izin</span>
                                @elseif($d->status == 's')
                                    <span class="pill-badge pill-sakit" style="font-size: 10.5px;">Sakit</span>
                                @elseif($d->status == 'c')
                                    <span class="pill-badge pill-cuti" style="font-size: 10.5px;">Cuti</span>
                                @elseif($d->status == 'a')
                                    <span class="pill-badge pill-alpa" style="font-size: 10.5px;">Tidak Hadir</span>
                                @else
                                    <span class="pill-badge pill-belum" style="font-size: 10.5px;">Belum</span>
                                @endif
                            </div>
                        </div>

                        <div class="row g-2 py-2 my-1 border-top border-bottom bg-slate-50 rounded-2 text-center" style="background: #f8fafc;">
                            <div class="col-4">
                                <small class="text-muted d-block text-uppercase" style="font-size: 9.5px; font-weight: 700;">Shift</small>
                                <span class="fw-semibold text-dark" style="font-size: 11.5px;">{{ $d->nama_jam_kerja ?? '-' }}</span>
                            </div>
                            <div class="col-4 border-start border-end">
                                <small class="text-muted d-block text-uppercase" style="font-size: 9.5px; font-weight: 700;">Masuk</small>
                                <span class="time-mono text-dark" style="font-size: 12px;">{{ $d->jam_in ? date('H:i', strtotime($d->jam_in)) : '-' }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block text-uppercase" style="font-size: 9.5px; font-weight: 700;">Pulang</small>
                                <span class="time-mono text-dark" style="font-size: 12px;">{{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : '-' }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <a href="#" class="btn btn-sm btn-outline-success koreksiPresensi py-1 px-2.5 rounded-2" nik="{{ Crypt::encrypt($d->nik) }}"
                                tanggal="{{ $tanggal_presensi }}" style="font-size: 11.5px;">
                                <i class="ti ti-edit me-1"></i> Koreksi
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-users-minus fs-1 mb-2 opacity-50"></i>
                        <p class="mb-0 fw-semibold text-dark">Tidak Ada Data Presensi</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Footer -->
            <div class="p-3 border-top" style="background: #FFFFFF; border-color: #e2e8f0 !important;">
                {{ $karyawan->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
<x-modal-form id="modal" size="modal-xl" show="loadmodal" title="" />
@endsection
@push('myscript')
<script src="{{ asset('assets/external/js/leaflet.js') }}" defer></script>
<script>
    $(function() {
        $(document).on('click', '.koreksiPresensi', function() {
            let nik = $(this).attr('nik');
            let tanggal = $(this).attr('tanggal');
            $.ajax({
                type: 'POST',
                url: "{{ route('presensi.edit') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    nik: nik,
                    tanggal: tanggal
                },
                cache: false,
                success: function(res) {
                    $('#modal').modal('show');
                    $('#modal').find('.modal-title').text('Koreksi Presensi');
                    $('#loadmodal').html(res);
                }
            });
        });

        $(".btnShowpresensi_in, .btnShowpresensi_out").click(function(e) {
            e.preventDefault();
            const id = $(this).attr("id");
            const status = $(this).attr("status");
            $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
            </div>`);
            $("#modal").modal("show");
            $(".modal-title").text("Data Presensi");
            $("#loadmodal").load(`/presensi/${id}/${status}/show`);
        });

        $(".btngetDatamesin").click(function(e) {
            e.preventDefault();
            var pin = $(this).attr("pin");
            var tanggal = $(this).attr("tanggal");
            $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);
            $("#modal").modal("show");
            $(".modal-title").text("Get Data Mesin");
            $.ajax({
                type: 'POST',
                url: '/presensi/getdatamesin',
                data: {
                    _token: "{{ csrf_token() }}",
                    pin: pin,
                    tanggal: tanggal,
                },
                cache: false,
                success: function(respond) {
                    console.log(respond);
                    $("#loadmodal").html(respond);
                }
            });
        });

        $(".delete-confirm").click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin Data Ini Akan Dihapus ?',
                text: "Jika Dihapus Maka Data Akan Hilang ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#32745e',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus Saja!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>
@endpush
