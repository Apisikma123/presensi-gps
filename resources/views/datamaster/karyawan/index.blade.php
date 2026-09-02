@extends('layouts.app')
@section('titlepage', 'Karyawan')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Karyawan
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen data master karyawan, unit kerja, jabatan, dan konfigurasi akses operasional.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">
                        <i class="ti ti-home-2 ti-xs"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">
                        <i class="ti ti-database ti-xs me-1"></i> Data Master
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-users ti-xs me-1"></i> Karyawan
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('karyawan.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah
                        Karyawan</a>
                    <a href="{{ route('karyawan.export', request()->query()) }}" class="btn btn-success"><i class="ti ti-file-export me-2"></i> Export Excel</a>
                    <a href="#" class="btn btn-success" id="btnImport"><i class="ti ti-file-import me-2"></i> Import Excel</a>
                    @can('users.create')
                        <a href="{{ route('karyawan.generatealluser') }}" class="btn btn-warning"><i class="ti ti-user-plus me-2"></i> Buat User (All)</a>
                        <a href="{{ route('karyawan.deletealluser') }}" class="btn btn-danger delete-all-user"><i class="ti ti-user-x me-2"></i> Reset All User</a>
                    @endcan
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('karyawan.index') }}">
                            <div class="row g-2">
                                <div class="col-lg-3 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Nama Karyawan" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                                        icon="ti ti-search" hideLabel />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <x-select label="Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                        selected="{{ Request('kode_cabang') }}" hideLabel />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                                        selected="{{ Request('kode_dept') }}" upperCase="true" hideLabel />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <x-select label="Jabatan" name="kode_jabatan" :data="$jabatan" key="kode_jabatan" textShow="nama_jabatan"
                                        selected="{{ Request('kode_jabatan') }}" upperCase="true" hideLabel />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <div class="form-group mb-3">
                                        <select name="sort_by" id="sort_by" class="form-select">
                                            <option value="nama_karyawan" {{ Request('sort_by') == 'nama_karyawan' ? 'selected' : '' }}>Sort By Nama</option>
                                            <option value="nik_show" {{ Request('sort_by') == 'nik_show' ? 'selected' : '' }}>Sort By NIK</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-sm-12 col-md-12">
                                    <button class="btn btn-primary w-100"><i class="ti ti-icons ti-search me-1"></i></button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">

                        <div class="row g-2">
                            <div class="col-12">
                                @foreach ($karyawan as $d)
                                    @php
                                        // Initials for avatar
                                        $words = explode(' ', $d->nama_karyawan);
                                        $initials = '';
                                        foreach ($words as $w) {
                                            if (isset($w[0])) $initials .= $w[0];
                                        }
                                        $initials = strtoupper(substr($initials, 0, 2));

                                        $awal = new DateTime($d->tanggal_masuk);
                                        $akhir = new DateTime();
                                        $masa_kerja = $akhir->diff($awal);
                                    @endphp
                                    <div class="card mb-2 shadow-sm border" style="border-radius: 12px; border-color: #e2e8f0; transition: all 0.2s ease;">
                                        <div class="card-body p-3">
                                            <div class="row align-items-center g-2">
                                                <!-- Avatar & Identity -->
                                                <div class="col-lg-5 col-md-12 d-flex align-items-center gap-3">
                                                    @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                                        <img src="{{ getfotoKaryawan($d->foto) }}" alt="Avatar"
                                                            class="rounded-circle shadow-sm flex-shrink-0"
                                                            style="width: 44px; height: 44px; object-fit: cover; border: 2px solid #e2e8f0;">
                                                    @else
                                                        <div class="rounded-circle shadow-sm flex-shrink-0 d-flex align-items-center justify-content-center fw-bold"
                                                            style="width: 44px; height: 44px; background: rgba(50, 116, 94, 0.12); color: #32745e; font-size: 14px; border: 2px solid rgba(50, 116, 94, 0.2);">
                                                            {{ $initials }}
                                                        </div>
                                                    @endif

                                                    <div class="overflow-hidden">
                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            <span class="fw-bold text-dark" style="font-size: 14px;">{{ $d->nama_karyawan }}</span>
                                                            <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 600;">
                                                                <i class="ti ti-id me-1"></i>{{ $d->nik_show ?? $d->nik }}
                                                            </span>
                                                        </div>
                                                        <div class="mt-1 d-flex flex-wrap gap-1">
                                                            <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-size: 10.5px; font-weight: 600;">{{ $d->nama_jabatan }}</span>
                                                            <span class="badge" style="background: #f0fdf4; color: #15803d; font-size: 10.5px; font-weight: 600;">{{ $d->nama_dept }}</span>
                                                            <span class="badge" style="background: #fdf4ff; color: #86198f; font-size: 10.5px; font-weight: 600;">{{ $d->nama_cabang }}</span>
                                                            @if ($d->status_karyawan)
                                                                @php
                                                                    $status_karyawan_text = $d->status_karyawan == 'K' ? 'Kontrak' : ($d->status_karyawan == 'T' ? 'Tetap' : $d->status_karyawan);
                                                                @endphp
                                                                <span class="badge" style="background: #fffbeb; color: #b45309; font-size: 10.5px; font-weight: 600;">{{ $status_karyawan_text }}</span>
                                                            @endif
                                                            @if ($d->jenis_upah)
                                                                <span class="badge" style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; font-size: 10.5px; font-weight: 600;">{{ $d->jenis_upah }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Status & Masa Kerja -->
                                                <div class="col-lg-3 col-md-6 text-center d-flex flex-column align-items-center justify-content-center">
                                                    <div>
                                                        @if ($d->status_aktif_karyawan == '1')
                                                            <span class="badge rounded-pill px-2.5 py-1" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 11px; font-weight: 600;">
                                                                <i class="ti ti-check me-1"></i>Aktif
                                                            </span>
                                                        @else
                                                            <span class="badge rounded-pill px-2.5 py-1" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-size: 11px; font-weight: 600;">
                                                                <i class="ti ti-x me-1"></i>Non Aktif
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted mt-1" style="font-size: 11px;">
                                                        Masuk: <span class="fw-semibold text-dark">{{ date('d M Y', strtotime($d->tanggal_masuk)) }}</span>
                                                        <span class="text-slate-400 mx-1">•</span>
                                                        <span>{{ $masa_kerja->y . ' Th ' . $masa_kerja->m . ' Bln' }}</span>
                                                    </div>
                                                </div>

                                                <!-- Lock Security Controls -->
                                                <div class="col-lg-2 col-md-6 d-flex justify-content-center align-items-center gap-3">
                                                    <div class="d-flex align-items-center gap-1 p-1 px-2 rounded-2" style="background: #f8fafc; border: 1px solid #edf2f7;">
                                                        @if ($d->lock_location == '1')
                                                            <a href="{{ route('karyawan.lockunlocklocation', Crypt::encrypt($d->nik)) }}"
                                                                class="text-success" data-bs-toggle="tooltip" title="Location Terkunci (Klik untuk Unlock)">
                                                                <i class="ti ti-lock fs-5"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('karyawan.lockunlocklocation', Crypt::encrypt($d->nik)) }}"
                                                                class="text-danger" data-bs-toggle="tooltip" title="Location Bebas (Klik untuk Lock)">
                                                                <i class="ti ti-lock-open fs-5"></i>
                                                            </a>
                                                        @endif
                                                        <span style="font-size: 10px; font-weight: 600; color: #64748b;">GPS</span>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-1 p-1 px-2 rounded-2" style="background: #f8fafc; border: 1px solid #edf2f7;">
                                                        @if ($d->lock_jam_kerja == '1')
                                                            <a href="{{ route('karyawan.lockunlockjamkerja', Crypt::encrypt($d->nik)) }}"
                                                                class="text-success" data-bs-toggle="tooltip" title="Shift Terkunci (Klik untuk Unlock)">
                                                                <i class="ti ti-lock fs-5"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('karyawan.lockunlockjamkerja', Crypt::encrypt($d->nik)) }}"
                                                                class="text-danger" data-bs-toggle="tooltip" title="Shift Bebas (Klik untuk Lock)">
                                                                <i class="ti ti-lock-open fs-5"></i>
                                                            </a>
                                                        @endif
                                                        <span style="font-size: 10px; font-weight: 600; color: #64748b;">Shift</span>
                                                    </div>
                                                </div>

                                                <!-- Actions -->
                                                <div class="col-lg-2 col-md-12 text-lg-end text-center">
                                                    <div class="d-flex flex-lg-column align-items-lg-end align-items-center justify-content-center gap-1">
                                                        <div class="btn-group shadow-sm" role="group">
                                                            @can('karyawan.setjamkerja')
                                                                <a href="#" class="btn btn-sm btn-outline-secondary btnSetJamkerja py-1 px-2"
                                                                    nik="{{ Crypt::encrypt($d->nik) }}" title="Set Jam Kerja">
                                                                    <i class="ti ti-device-watch"></i>
                                                                </a>
                                                            @endcan
                                                            @can('karyawan.setcabang')
                                                                <a href="#" class="btn btn-sm btn-outline-secondary btnSetCabang py-1 px-2"
                                                                    nik="{{ Crypt::encrypt($d->nik) }}" title="Set Cabang">
                                                                    <i class="ti ti-map"></i>
                                                                </a>
                                                            @endcan
                                                            @can('karyawan.edit')
                                                                <a href="#" class="btn btn-sm btn-outline-primary btnEdit py-1 px-2"
                                                                    nik="{{ Crypt::encrypt($d->nik) }}" title="Edit">
                                                                    <i class="ti ti-edit"></i>
                                                                </a>
                                                            @endcan
                                                            @can('karyawan.show')
                                                                <a href="{{ route('karyawan.show', Crypt::encrypt($d->nik)) }}"
                                                                    class="btn btn-sm btn-outline-info py-1 px-2" title="Detail">
                                                                    <i class="ti ti-file-description"></i>
                                                                </a>
                                                            @endcan
                                                            @can('karyawan.delete')
                                                                <form method="POST" name="deleteform" class="deleteform d-inline"
                                                                    action="{{ route('karyawan.delete', Crypt::encrypt($d->nik)) }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-outline-danger delete-confirm rounded-0 rounded-end py-1 px-2"
                                                                        title="Delete">
                                                                        <i class="ti ti-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                        @can('users.create')
                                                            @if (empty($d->id_user))
                                                                <a href="{{ route('karyawan.createuser', Crypt::encrypt($d->nik)) }}"
                                                                    class="btn btn-sm btn-label-danger py-0 px-2 rounded-pill mt-1" style="font-size: 10px; font-weight: 600;">
                                                                    <i class="ti ti-user-plus me-1"></i> Buat User
                                                                </a>
                                                            @else
                                                                <a href="{{ route('karyawan.deleteuser', Crypt::encrypt($d->nik)) }}"
                                                                    class="btn btn-sm btn-label-success py-0 px-2 rounded-pill mt-1" style="font-size: 10px; font-weight: 600;">
                                                                    <i class="ti ti-user-check me-1"></i> Akun Aktif
                                                                </a>
                                                            @endif
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {{ $karyawan->links() }}
                        </div>
                    </div>
                </div>
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
    $(function() {
        $("#btnCreate").click(function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Data Karyawan");
            $("#loadmodal").load("{{ route('karyawan.create') }}");
        });

        $("#btnImport").click(function(e) {
            e.preventDefault();
            $("#modalImport").modal("show");
            $("#loadmodalImport").load("{{ route('karyawan.import') }}");
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            const nik = $(this).attr("nik");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Karyawan");
            $("#loadmodal").load(`/karyawan/${nik}/edit`);
        });

        $(".btnSetJamkerja").click(function() {
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

        $(".btnSetCabang").click(function() {
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

        $(".delete-all-user").click(function(e) {
            e.preventDefault();
            var href = $(this).attr("href");
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Semua User dengan Role Karyawan akan dihapus!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#32745e",
                cancelButtonColor: "#d33",
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
