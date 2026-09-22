<form action="#" id="frmKaryawan">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 p-2.5 rounded-2" style="background: rgba(30, 77, 62, 0.04); border: 1px solid rgba(30, 77, 62, 0.1);">
        <div>
            <span class="fw-bold text-dark d-block" style="font-size: 13px;">Aksi Cepat Massal</span>
            <span class="text-muted" style="font-size: 11px;">Terapkan penugasan libur ke seluruh karyawan yang tampil.</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5 py-1.5 px-3" id="tambahkansemua" style="font-size: 12.5px;">
                <i class="ti ti-check"></i>
                <span>Tambahkan Semua</span>
            </button>
            <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-1.5 py-1.5 px-3" id="batalkansemua" style="font-size: 12.5px;">
                <i class="ti ti-circle-minus"></i>
                <span>Batalkan Semua</span>
            </button>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept" select2="select2Group"
                upperCase="true" />
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-input-with-icon label="Nama Karyawan" name="nama_karyawan" icon="ti ti-search" placeholder="Ketik nama karyawan..." />
        </div>
    </div>

    <div class="card mb-1" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 10px; overflow: hidden;">
        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0" id="tabelkaryawan">
                <thead style="position: sticky; top: 0; z-index: 2; background: #F8FAFC;">
                    <tr>
                        <th style="width: 50px;" class="text-center">NO</th>
                        <th style="width: 120px;">NIK</th>
                        <th>NAMA KARYAWAN</th>
                        <th>DEPARTEMEN</th>
                        <th class="text-end" style="width: 90px;">AKSI</th>
                    </tr>
                </thead>
                <tbody id="loadkaryawan">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Memuat daftar karyawan...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        const form = $('#frmKaryawan');

        function loadliburkaryawan() {
            const kode_libur = "{{ Crypt::encrypt($harilibur->kode_libur) }}";
            $("#loadliburkaryawan").load(`/harilibur/${kode_libur}/getkaryawanlibur`);
        }

        function loadkaryawan() {
            const kode_libur = "{{ Crypt::encrypt($harilibur->kode_libur) }}";
            const kode_dept = form.find("#kode_dept").val();
            const nama_karyawan = form.find("#nama_karyawan").val();
            
            $("#loadkaryawan").html(`<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Memuat data...</td></tr>`);

            $.ajax({
                type: 'POST',
                url: `/harilibur/getkaryawan`,
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_libur: kode_libur,
                    kode_dept: kode_dept,
                    nama_karyawan: nama_karyawan
                },
                cache: false,
                success: function(respond) {
                    $("#loadkaryawan").html(respond);
                    loadliburkaryawan();
                }
            });
        }

        loadkaryawan();

        form.find("#kode_dept").change(function() {
            loadkaryawan();
        });

        let searchTimeout;
        form.find("#nama_karyawan").on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                loadkaryawan();
            }, 300);
        });

        $(document).off('click', '#tabelkaryawan .updateLibur').on('click', '#tabelkaryawan .updateLibur', function(e) {
            e.preventDefault();
            const btn = $(this);
            const nik = btn.attr('nik');
            const kode_libur = "{{ $harilibur->kode_libur }}";

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');

            $.ajax({
                type: 'POST',
                url: `/harilibur/updateliburkaryawan`,
                data: {
                    _token: "{{ csrf_token() }}",
                    nik: nik,
                    kode_libur: kode_libur
                },
                cache: false,
                success: function(respond) {
                    if (respond.success == true) {
                        loadkaryawan();
                    } else {
                        Swal.fire({
                            title: "Oops!",
                            text: respond.message,
                            icon: "warning",
                            showConfirmButton: true,
                        });
                        loadkaryawan();
                    }
                }
            });
        });

        $("#tambahkansemua").click(function(e) {
            e.preventDefault();
            const kode_libur = "{{ $harilibur->kode_libur }}";
            const kode_dept = form.find("#kode_dept").val();
            const nama_karyawan = form.find("#nama_karyawan").val();

            $("#loadkaryawan").html(`<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Mendaftarkan seluruh karyawan...</td></tr>`);

            $.ajax({
                type: 'POST',
                url: `/harilibur/tambahkansemua`,
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_libur: kode_libur,
                    kode_dept: kode_dept,
                    nama_karyawan: nama_karyawan
                },
                cache: false,
                success: function(respond) {
                    if (respond.success == true) {
                        loadkaryawan();
                    } else {
                        Swal.fire({
                            title: "Oops!",
                            text: respond.message,
                            icon: "warning",
                            showConfirmButton: true,
                        });
                        loadkaryawan();
                    }
                }
            });
        });

        $("#batalkansemua").click(function(e) {
            e.preventDefault();
            const kode_libur = "{{ $harilibur->kode_libur }}";
            const kode_dept = form.find("#kode_dept").val();
            const nama_karyawan = form.find("#nama_karyawan").val();

            $("#loadkaryawan").html(`<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Membatalkan seluruh karyawan...</td></tr>`);

            $.ajax({
                type: 'POST',
                url: `/harilibur/batalkansemua`,
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_libur: kode_libur,
                    kode_dept: kode_dept,
                    nama_karyawan: nama_karyawan
                },
                cache: false,
                success: function(respond) {
                    if (respond.success == true) {
                        loadkaryawan();
                    } else {
                        Swal.fire({
                            title: "Oops!",
                            text: respond.message,
                            icon: "warning",
                            showConfirmButton: true,
                        });
                        loadkaryawan();
                    }
                }
            });
        });
    });
</script>
