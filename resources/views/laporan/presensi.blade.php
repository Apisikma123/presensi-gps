@extends('layouts.app')
@section('titlepage', 'Laporan Presensi')

@section('content')
@section('navigasi')
    <span>Laporan Presensi</span>
@endsection
<div class="row">
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header d-flex justify-content-between align-items-center py-3 px-4" style="background-color: #FFFFFF !important; border-bottom: 1px solid #F1F5F9 !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 36px; height: 36px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E;">
                        <i class="ti ti-file-analytics fs-5"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0 fw-bold text-dark" style="font-size: 14px; color: #0F172A !important;">Laporan Presensi Karyawan</h6>
                        <small class="text-muted" style="font-size: 11.5px;">Filter rekapitulasi kehadiran, keterlambatan, dan ekspor ke Excel.</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('laporan.cetakpresensi') }}" method="POST" target="_blank" id="formPresensi">
                    @csrf
                    <input type="hidden" name="format_laporan" value="1">
                    <div class="form-group mb-3">
                        <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Cabang / Outlet</label>
                        <select name="kode_cabang" id="kode_cabang" class="form-select select2">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $d)
                                <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Departemen</label>
                        <select name="kode_dept" id="kode_dept" class="form-select select2">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Karyawan</label>
                        <select name="nik" id="nik" class="form-select select2">
                            <option value="">Semua Karyawan</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Periode Laporan</label>
                        <select name="periode_laporan" id="periode_laporan" class="form-select">
                            <option value="">Periode Laporan</option>
                            <option value="1" selected>Periode Gaji</option>
                            <option value="2">Bulan Berjalan</option>
                            <option value="3">Range Tanggal</option>
                        </select>
                    </div>

                    <div class="row" id="baris_tanggal">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Dari Tanggal</label>
                                <input type="text" name="dari" id="dari" class="form-control flatpickr-date" placeholder="Dari">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Sampai Tanggal</label>
                                <input type="text" name="sampai" id="sampai" class="form-control flatpickr-date" placeholder="Sampai">
                            </div>
                        </div>
                    </div>

                    <div class="row" id="baris_bulan">
                        <div class="col">
                            <div class="form-group mb-3">
                                <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Bulan</label>
                                <select name="bulan" id="bulan" class="form-select">
                                    <option value="">Bulan</option>
                                    @foreach ($list_bulan as $d)
                                        <option {{ date('m') == $d['kode_bulan'] ? 'selected' : '' }} value="{{ $d['kode_bulan'] }}">
                                            {{ $d['nama_bulan'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="baris_tahun">
                        <div class="col">
                            <div class="form-group mb-3">
                                <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Tahun</label>
                                <select name="tahun" id="tahun" class="form-select">
                                    <option value="">Tahun</option>
                                    @for ($t = $start_year; $t <= date('Y'); $t++)
                                        <option {{ date('Y') == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Format Rekap</label>
                        <select name="format_rekap" id="format_rekap" class="form-select">
                            <option value="1">Format 1 (Default)</option>
                            <option value="2">Format 2 (Struktur Baru)</option>
                        </select>
                    </div>

                    <div class="row pt-2">
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                            <button type="submit" name="submitButton" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1.5"
                                style="background-color: #1E4D3E; border-color: #1E4D3E; height: 38px; border-radius: 8px; font-weight: 600; font-size: 12.5px;">
                                <i class="ti ti-printer"></i>
                                <span>Cetak Laporan</span>
                            </button>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                            <button type="submit" name="exportButton" class="btn btn-success w-100 d-inline-flex align-items-center justify-content-center gap-1.5"
                                style="background-color: #059669; border-color: #059669; height: 38px; border-radius: 8px; font-weight: 600; font-size: 12.5px;">
                                <i class="ti ti-download"></i>
                                <span>Export Excel</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        // Initialize Select2
        const initSelect2 = (selector) => {
            $(selector).each(function() {
                var $this = $(this);
                var placeholder = $this.find("option:first").text();
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: placeholder,
                    allowClear: true,
                    dropdownParent: $this.parent()
                });
            });
        };

        initSelect2(".select2");

        // Load Karyawan Function
        function loadKaryawan() {
            const kode_cabang = $("#kode_cabang").val();
            const kode_dept = $("#kode_dept").val();
            const dari = $("#dari").val();
            const targetSelect = $("#nik");

            $.ajax({
                type: "GET",
                url: "{{ route('karyawan.getkaryawan') }}",
                data: {
                    kode_cabang: kode_cabang,
                    kode_dept: kode_dept,
                    tanggal: dari // Optional use
                },
                cache: false,
                success: function(respond) {
                    targetSelect.empty();
                    targetSelect.append(`<option value=''>Semua Karyawan</option>`);
                    respond.forEach(function(item) {
                        targetSelect.append(`<option value="${item.nik}">${item.nik} - ${item.nama_karyawan}</option>`);
                    });
                }
            });
        }

        $("#kode_cabang, #kode_dept, #dari").change(function() {
            loadKaryawan();
        });

        loadKaryawan();

        // Toggle logic for Periode
        function togglePeriode() {
            const periode = $("#periode_laporan").val();
            if (periode == "3") {
                $("#baris_tanggal").show();
                $("#baris_bulan, #baris_tahun").hide();
            } else {
                $("#baris_tanggal").hide();
                $("#baris_bulan, #baris_tahun").show();
            }
        }

        $("#periode_laporan").change(togglePeriode);
        togglePeriode();

        $("#formPresensi").submit(function(e) {
            const periode = $("#periode_laporan").val();
            if (periode === "") {
                Swal.fire('Warning', 'Periode Laporan harus diisi!', 'warning');
                e.preventDefault();
                return false;
            }
            if (periode === "3") {
                if ($("#dari").val() === "" || $("#sampai").val() === "") {
                    Swal.fire('Warning', 'Range Tanggal harus diisi!', 'warning');
                    e.preventDefault();
                    return false;
                }
            } else {
                if ($("#bulan").val() === "" || $("#tahun").val() === "") {
                    Swal.fire('Warning', 'Bulan/Tahun harus diisi!', 'warning');
                    e.preventDefault();
                    return false;
                }
            }
        });
    });
</script>
@endpush
