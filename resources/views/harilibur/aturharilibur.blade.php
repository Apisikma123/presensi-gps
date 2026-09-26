@extends('layouts.app')
@section('titlepage', 'Atur Karyawan Libur')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('harilibur.index') }}">Hari Libur</a></li>
    <li class="breadcrumb-item active">Atur Karyawan Libur</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-2.5">
        <a href="{{ route('harilibur.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center p-2 rounded-2" title="Kembali ke Daftar Hari Libur">
            <i class="ti ti-arrow-left fs-5"></i>
        </a>
        <div>
            <h4 class="page-title mb-0">Atur Penugasan Hari Libur</h4>
            <p class="page-subtitle text-muted mb-0">Kelola daftar karyawan yang diliburkan atau dijadwalkan piket masuk.</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('harilibur.setharilibur')
            <a href="#" id="btnCreate" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                <i class="ti ti-user-plus"></i>
                <span>Kelola / Tambah Karyawan</span>
            </a>
        @endcan
    </div>
</div>

<div class="row g-3">
    <!-- Left Column: Holiday Metadata Card -->
    <div class="col-xl-4 col-lg-5 col-md-12">
        <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header py-3 px-3.5 border-bottom d-flex align-items-center gap-2" style="background: rgba(60, 42, 33, 0.03);">
                <i class="ti ti-calendar-event fs-5 text-primary"></i>
                <h6 class="mb-0 fw-bold text-dark" style="font-size: 13.5px;">Informasi Hari Libur</h6>
            </div>
            <div class="card-body p-3.5">
                <div class="mb-3 p-2.5 rounded-2 d-flex align-items-center gap-3" style="background: rgba(60, 42, 33, 0.06); border: 1px solid rgba(60, 42, 33, 0.12);">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: #3C2A21; color: #FFFFFF;">
                        <i class="ti ti-calendar-off fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Kode Libur</span>
                        <span class="fw-bold text-dark font-mono" style="font-size: 15px;">{{ $harilibur->kode_libur }}</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0 align-middle">
                        <tbody>
                            <tr class="border-bottom">
                                <td class="text-muted py-2 ps-0" style="font-size: 12.5px; width: 35%;">Tanggal</td>
                                <td class="text-end fw-semibold text-dark py-2 pe-0 font-mono" style="font-size: 13px;">
                                    {{ formatIndo($harilibur->tanggal) }}
                                    <div class="text-muted fw-normal" style="font-size: 11px;">{{ \Carbon\Carbon::parse($harilibur->tanggal)->translatedFormat('l') }}</div>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted py-2 ps-0" style="font-size: 12.5px;">Outlet / Cabang</td>
                                <td class="text-end py-2 pe-0">
                                    @if ($harilibur->kode_cabang === 'ALL')
                                        <span class="badge font-mono" style="background: rgba(60, 42, 33, 0.1); color: #3C2A21; border: 1px solid rgba(60, 42, 33, 0.2); font-size: 11px;">
                                            SEMUA CABANG
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                            {{ textUpperCase($harilibur->nama_cabang) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2 ps-0" style="font-size: 12.5px;">Keterangan</td>
                                <td class="text-end fw-semibold text-dark py-2 pe-0" style="font-size: 13px;">
                                    {{ $harilibur->keterangan }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert mt-3 mb-0 p-2.5 rounded-2 d-flex align-items-start gap-2" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 11.5px; color: #475569;">
                    <i class="ti ti-info-circle fs-5 text-primary flex-shrink-0 mt-0.5"></i>
                    <div>
                        Karyawan yang terdaftar di bawah otomatis berstatus <strong>LIBUR</strong> pada tanggal tersebut (tidak dihitung Alfa/Mangkir).
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Employee Table -->
    <div class="col-xl-8 col-lg-7 col-md-12">
        <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header py-3 px-3.5 border-bottom d-flex align-items-center justify-content-between" style="background: #FFFFFF;">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-users fs-5 text-primary"></i>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 13.5px;">Daftar Karyawan yang Diliburkan</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" id="btnRefreshList">
                        <i class="ti ti-refresh" style="font-size: 13px;"></i>
                        <span>Muat Ulang</span>
                    </button>
                </div>
            </div>
            <div class="table-responsive" style="max-height: 540px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead style="position: sticky; top: 0; z-index: 2; background: #F8FAFC;">
                        <tr>
                            <th style="width: 50px;" class="text-center">NO</th>
                            <th style="width: 120px;">NIK</th>
                            <th>NAMA KARYAWAN</th>
                            <th>DEPARTEMEN</th>
                            <th class="text-end" style="width: 90px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="loadliburkaryawan">
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                Memuat data karyawan...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" size="modal-lg" show="loadmodal" title="" />
@endsection

@push('myscript')
<script>
    $(function() {
        function loadliburkaryawan() {
            const kode_libur = "{{ Crypt::encrypt($harilibur->kode_libur) }}";
            $("#loadliburkaryawan").html(`<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Memuat data...</td></tr>`);
            $("#loadliburkaryawan").load(`/harilibur/${kode_libur}/getkaryawanlibur`);
        }
        loadliburkaryawan();

        $("#btnRefreshList").click(function(e) {
            e.preventDefault();
            loadliburkaryawan();
        });

        $("#btnCreate").click(function(e) {
            e.preventDefault();
            const kode_libur = "{{ Crypt::encrypt($harilibur->kode_libur) }}";
            $("#modal").modal("show");
            $(".modal-title").text("Kelola Karyawan Libur");
            $("#loadmodal").load(`/harilibur/${kode_libur}/aturkaryawan`);
        });

        $(document).on('click', '.delete', function(e) {
            e.preventDefault();
            const kode_libur = "{{ $harilibur->kode_libur }}";
            const nik = $(this).attr("nik");

            Swal.fire({
                title: "Batalkan Libur?",
                text: "Karyawan ini akan dilepas dari hari libur (diwajibkan masuk/absen).",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: (getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '#3C2A21'),
                cancelButtonColor: "#64748B",
                confirmButtonText: "Ya, Batalkan Libur",
                cancelButtonText: "Kembali"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: `/harilibur/deletekaryawanlibur`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            kode_libur: kode_libur,
                            nik: nik
                        },
                        cache: false,
                        success: function(respond) {
                            if (respond.success == true) {
                                loadliburkaryawan();
                            } else {
                                Swal.fire({
                                    title: "Oops!",
                                    text: respond.message,
                                    icon: "warning",
                                    showConfirmButton: true,
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
