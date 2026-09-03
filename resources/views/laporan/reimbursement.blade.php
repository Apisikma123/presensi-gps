@extends('layouts.app')
@section('titlepage', 'Laporan Reimbursement')
@section('content')
@section('navigasi')
    <span>Laporan Reimbursement</span>
@endsection
<div class="row">
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header d-flex justify-content-between align-items-center py-3 px-4" style="background-color: #FFFFFF !important; border-bottom: 1px solid #F1F5F9 !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 36px; height: 36px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E;">
                        <i class="ti ti-receipt fs-5"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0 fw-bold text-dark" style="font-size: 14px; color: #0F172A !important;">Cetak Laporan Reimbursement</h6>
                        <small class="text-muted" style="font-size: 11.5px;">Filter klaim pengeluaran operasional dan ekspor ke format cetak / Excel.</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('laporan.cetakreimbursement') }}" method="POST" target="_blank" id="formReimbursement">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Cabang / Outlet</label>
                        <select name="kode_cabang" id="kode_cabang_reimburse" class="form-select select2">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $d)
                                <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Departemen</label>
                        <select name="kode_dept" id="kode_dept_reimburse" class="form-select select2">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Karyawan</label>
                        <select name="nik" id="nik_reimburse" class="form-select select2">
                            <option value="">Semua Karyawan</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Status Klaim</label>
                        <select name="status" id="status_reimburse" class="form-select select2">
                            <option value="">Semua Status</option>
                            <option value="P">Pending</option>
                            <option value="A">Approved</option>
                            <option value="R">Rejected</option>
                            <option value="D">Dibayar</option>
                            <option value="B">Batal</option>
                        </select>
                    </div>
                    
                    <div class="row" id="baris_tanggal">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Dari Tanggal</label>
                                <input type="text" name="dari" id="dari" class="form-control flatpickr-date"
                                    placeholder="Dari" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="form-label text-dark fw-semibold" style="font-size: 12px;">Sampai Tanggal</label>
                                <input type="text" name="sampai" id="sampai" class="form-control flatpickr-date"
                                    placeholder="Sampai" required>
                            </div>
                        </div>
                    </div>

                    <div class="row pt-2">
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                            <button type="submit" name="submitButton" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1.5" id="submitButton"
                                style="background-color: #1E4D3E; border-color: #1E4D3E; height: 38px; border-radius: 8px; font-weight: 600; font-size: 12.5px;">
                                <i class="ti ti-printer"></i>
                                <span>Cetak Laporan</span>
                            </button>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
                            <button type="submit" name="exportButton" class="btn btn-success w-100 d-inline-flex align-items-center justify-content-center gap-1.5" id="exportButton"
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
            dropdownParent: $('#formReimbursement')
        });

        function loadKaryawan() {
            const kode_cabang = $("#kode_cabang_reimburse").val();
            const kode_dept = $("#kode_dept_reimburse").val();
            
            $.ajax({
                type: "GET",
                url: "{{ route('karyawan.getkaryawan') }}",
                data: {
                    kode_cabang: kode_cabang,
                    kode_dept: kode_dept
                },
                cache: false,
                success: function(respond) {
                    $("#nik_reimburse").empty();
                    $("#nik_reimburse").append("<option value=''>Semua Karyawan</option>");
                    respond.forEach(function(item) {
                        $("#nik_reimburse").append("<option value='" + item.nik + "'>" + item.nik + " - " + item
                            .nama_karyawan +
                            "</option>");
                    });
                }
            });
        }

        $("#kode_cabang_reimburse, #kode_dept_reimburse").change(function() {
            loadKaryawan();
        });

        loadKaryawan();

        $("#formReimbursement").submit(function(e) {
            const dari = $("#dari").val();
            const sampai = $("#sampai").val();
            
            if (dari == "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Tanggal Dari harus diisi!',
                    showConfirmButton: true,
                    didClose: () => {
                        $("#dari").focus();
                    }
                });
                return false;
            } else if (sampai == "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Tanggal Sampai harus diisi!',
                    showConfirmButton: true,
                    didClose: () => {
                        $("#sampai").focus();
                    }
                });
                return false;
            }
        });
    });
</script>
@endpush
