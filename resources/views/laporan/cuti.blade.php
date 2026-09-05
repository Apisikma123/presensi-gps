@extends('layouts.app')
@section('titlepage', 'Laporan Cuti')
@section('content')
@section('navigasi')
    <span>Laporan Cuti</span>
@endsection
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Laporan Cuti Karyawan</h4>
        <p class="page-subtitle text-muted mb-0">Filter dan cetak atau ekspor rekapitulasi hak serta pemakaian cuti ke format Excel.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header d-flex justify-content-between align-items-center py-3 px-4" style="background-color: #FFFFFF !important; border-bottom: 1px solid #F1F5F9 !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 36px; height: 36px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E;">
                        <i class="ti ti-calendar-stats fs-5"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0 fw-bold text-dark" style="font-size: 14px; color: #0F172A !important;">Cetak Rekap Cuti Karyawan</h6>
                        <small class="text-muted" style="font-size: 11.5px;">Filter dan cetak atau ekspor rekapitulasi hak & pemakaian cuti ke Excel.</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('laporan.cetakcuti') }}" method="POST" target="_blank" id="formLaporanCuti">
                    @csrf
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
                        <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Jenis Cuti <span class="text-danger">*</span></label>
                        <select name="kode_cuti" id="kode_cuti" class="form-select select2">
                            <option value="">Pilih Jenis Cuti</option>
                            @foreach ($cuti as $d)
                                <option value="{{ $d->kode_cuti }}">{{ textUpperCase($d->jenis_cuti) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Tahun</label>
                        <select name="tahun" id="tahun" class="form-select">
                            <option value="">Tahun</option>
                            @for ($t = $start_year; $t <= date('Y'); $t++)
                                <option {{ date('Y') == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                            @endfor
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
        $(".select2").select2({
            width: '100%',
            dropdownParent: $('#formLaporanCuti')
        });

        $("#formLaporanCuti").submit(function(e) {
            let kode_cuti = $("#kode_cuti").val();
            if(kode_cuti == "") {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Jenis Cuti Harus Diisi',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    didClose: function() {
                        $('#kode_cuti').focus();
                    }
                });
                return false;
            }
        });
    });
</script>
@endpush
