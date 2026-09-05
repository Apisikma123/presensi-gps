@if (auth()->user()->hasAnyPermission(['izinabsen.index', 'izinsakit.index', 'izincuti.index', 'dispensasi.index']) || auth()->user()->hasRole('super admin'))
    <ul class="nav nav-tabs border-bottom mb-3" id="izinTabs" data-no-spa="true" style="gap: 4px; flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none;">

        @can('izinabsen.index')
            <li class="nav-item">
                <a href="{{ route('izinabsen.index') }}" data-tab="izinabsen" data-url="{{ route('izinabsen.index') }}" class="nav-link tab-izin-link {{ request()->is(['izinabsen', 'izinabsen/*']) ? 'active' : '' }}">
                    <i class="tf-icons ti ti-file-description ti-md me-1"></i> Izin Absen
                    <span class="badge bg-danger rounded-pill ms-2 badge-izin" data-badge-for="izinabsen" style="{{ empty($notifikasi_izinabsen) ? 'display: none;' : '' }}">{{ $notifikasi_izinabsen ?? '' }}</span>
                </a>
            </li>
        @endcan
        @can('izinsakit.index')
            <li class="nav-item">
                <a href="{{ route('izinsakit.index') }}" data-tab="izinsakit" data-url="{{ route('izinsakit.index') }}" class="nav-link tab-izin-link {{ request()->is(['izinsakit', 'izinsakit/*']) ? 'active' : '' }}">
                    <i class="tf-icons ti ti-file-description ti-md me-1"></i> Izin Sakit
                    <span class="badge bg-danger rounded-pill ms-2 badge-izin" data-badge-for="izinsakit" style="{{ empty($notifikasi_izinsakit) ? 'display: none;' : '' }}">{{ $notifikasi_izinsakit ?? '' }}</span>
                </a>
            </li>
        @endcan
        @can('izincuti.index')
            <li class="nav-item">
                <a href="{{ route('izincuti.index') }}" data-tab="izincuti" data-url="{{ route('izincuti.index') }}" class="nav-link tab-izin-link {{ request()->is(['izincuti', 'izincuti/*']) ? 'active' : '' }}">
                    <i class="tf-icons ti ti-file-description ti-md me-1"></i> Izin Cuti
                    <span class="badge bg-danger rounded-pill ms-2 badge-izin" data-badge-for="izincuti" style="{{ empty($notifikasi_izincuti) ? 'display: none;' : '' }}">{{ $notifikasi_izincuti ?? '' }}</span>
                </a>
            </li>
        @endcan
        <li class="nav-item">
            <a href="{{ route('dispensasi.index') }}" data-tab="dispensasi" data-url="{{ route('dispensasi.index') }}" class="nav-link tab-izin-link {{ request()->is(['dispensasi', 'dispensasi/*']) ? 'active' : '' }}">
                <i class="tf-icons ti ti-clock-check ti-md me-1"></i> Dispensasi Terlambat
                <span class="badge bg-danger rounded-pill ms-2 badge-izin" data-badge-for="dispensasi" style="{{ empty($notifikasi_dispensasi) ? 'display: none;' : '' }}">{{ $notifikasi_dispensasi ?? '' }}</span>
            </a>
        </li>
    </ul>
@endif

<x-modal-form id="modal" size="" show="loadmodal" title="" />
<x-modal-form id="mdlCreateAjuanJadwal" size="" show="loadCreateAjuanJadwal" title="Tambah Ajuan Jadwal" />

@push('myscript')
<script>
    $(function() {
        // Universal Modal Helpers & Listeners
        function loadingModal() {
            $("#loadmodal").html(
                `<div class="sk-wave sk-primary" style="margin:auto">
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                </div>`
            );
        }

        function loadModalContent(url) {
            loadingModal();
            $("#loadmodal").load(url, function() {
                if (typeof initGlobalFlatpickr === 'function') {
                    initGlobalFlatpickr('#loadmodal');
                }
            });
        }

        // Create buttons
        $(document).on('click', '#btnCreateIzinAbsen', function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $("#modal").find(".modal-title").text("Buat Izin Absen");
            loadModalContent("/izinabsen/create");
        });

        $(document).on('click', '#btnCreateIzinSakit', function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $("#modal").find(".modal-title").text("Buat Izin Sakit");
            loadModalContent("/izinsakit/create");
        });

        $(document).on('click', '#btnCreateIzinCuti', function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $("#modal").find(".modal-title").text("Buat Izin Cuti");
            loadModalContent("/izincuti/create");
        });

        $(document).on('click', '#btnCreateDispensasi', function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $("#modal").find(".modal-title").text("Buat Dispensasi Keterlambatan");
            loadModalContent("/dispensasi/create");
        });

        // Approve buttons
        $(document).on('click', '.btnApprove', function(e) {
            e.preventDefault();
            const kode_izin = $(this).attr("kode_izin");
            const kode_izin_sakit = $(this).attr("kode_izin_sakit");
            const kode_izin_cuti = $(this).attr("kode_izin_cuti");
            const id_dispensasi = $(this).attr("id_dispensasi");

            $("#modal").modal("show");

            if (kode_izin) {
                $("#modal").find(".modal-title").text("Approve Izin Absen");
                loadModalContent(`/izinabsen/${kode_izin}/approve`);
            } else if (kode_izin_sakit) {
                $("#modal").find(".modal-title").text("Approve Izin Sakit");
                loadModalContent(`/izinsakit/${kode_izin_sakit}/approve`);
            } else if (kode_izin_cuti) {
                $("#modal").find(".modal-title").text("Approve Izin Cuti");
                loadModalContent(`/izincuti/${kode_izin_cuti}/approve`);
            } else if (id_dispensasi) {
                $("#modal").find(".modal-title").text("Persetujuan Dispensasi Keterlambatan");
                loadModalContent(`/dispensasi/${id_dispensasi}/approve`);
            }
        });

        // Show (Detail) buttons
        $(document).on('click', '.btnShow', function(e) {
            e.preventDefault();
            const kode_izin = $(this).attr("kode_izin");
            const kode_izin_sakit = $(this).attr("kode_izin_sakit");
            const kode_izin_cuti = $(this).attr("kode_izin_cuti");

            $("#modal").modal("show");

            if (kode_izin) {
                $("#modal").find(".modal-title").text("Detail Izin Absen");
                loadModalContent(`/izinabsen/${kode_izin}/show`);
            } else if (kode_izin_sakit) {
                $("#modal").find(".modal-title").text("Detail Izin Sakit");
                loadModalContent(`/izinsakit/${kode_izin_sakit}/show`);
            } else if (kode_izin_cuti) {
                $("#modal").find(".modal-title").text("Detail Izin Cuti");
                loadModalContent(`/izincuti/${kode_izin_cuti}/show`);
            }
        });

        // Edit buttons
        $(document).on('click', '.btnEdit', function(e) {
            e.preventDefault();
            const kode_izin = $(this).attr("kode_izin");
            const kode_izin_sakit = $(this).attr("kode_izin_sakit");
            const kode_izin_cuti = $(this).attr("kode_izin_cuti");

            $("#modal").modal("show");

            if (kode_izin) {
                $("#modal").find(".modal-title").text("Edit Izin Absen");
                loadModalContent(`/izinabsen/${kode_izin}/edit`);
            } else if (kode_izin_sakit) {
                $("#modal").find(".modal-title").text("Edit Izin Sakit");
                loadModalContent(`/izinsakit/${kode_izin_sakit}/edit`);
            } else if (kode_izin_cuti) {
                $("#modal").find(".modal-title").text("Edit Izin Cuti");
                loadModalContent(`/izincuti/${kode_izin_cuti}/edit`);
            }
        });

        // Cetak Report Cuti
        $(document).on('click', '#btnCetakReport', function(e) {
            e.preventDefault();
            var form = $('#formFilterCuti');
            if (form.length) {
                var originalAction = form.attr('action');
                form.attr('action', "{{ route('izincuti.print-report') }}");
                form.attr('target', '_blank');
                form.submit();
                form.attr('action', originalAction);
                form.removeAttr('target');
            }
        });
    });
</script>
@endpush
