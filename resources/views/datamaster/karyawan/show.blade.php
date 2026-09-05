@extends('layouts.app')
@section('titlepage', 'Detail Karyawan - ' . textCamelCase($karyawan->nama_karyawan))

@section('content')

<!-- Header Breadcrumb Toolbar -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <a href="{{ route('karyawan.index') }}" class="btn btn-sm btn-white text-dark border d-inline-flex align-items-center gap-1.5 shadow-xs" 
        style="background: #FFFFFF; border-color: #E2E8F0 !important; border-radius: 8px; font-size: 12.5px; font-weight: 600; padding: 6px 12px;">
        <i class="ti ti-arrow-left"></i> <span>Kembali ke Daftar</span>
    </a>
    <div class="d-flex align-items-center gap-1.5">
        <span class="badge bg-white text-muted font-mono border px-2.5 py-1.5" style="border-color: #E2E8F0 !important; font-size: 11px; border-radius: 6px;">
            NIK: <strong class="text-dark">{{ $karyawan->nik_show ?? $karyawan->nik }}</strong>
        </span>
    </div>
</div>

<!-- 1. Hero Profile & Action Card -->
<div class="card mb-3 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 14px; overflow: visible !important; background: #FFFFFF !important;">
    <div style="height: 3px; background: linear-gradient(90deg, #1E4D3E, #32745E, #059669); border-top-left-radius: 13px; border-top-right-radius: 13px;"></div>
    <div class="card-body p-3 p-md-4" style="overflow: visible !important;">
        <div class="row align-items-center gy-3">
            <!-- Left: Avatar & Identity Details -->
            <div class="col-12 col-lg-7">
                <div class="d-flex align-items-start align-items-sm-center gap-3">
                    <div class="position-relative flex-shrink-0">
                        @if (!empty($karyawan->foto))
                            <img src="{{ getfotoKaryawan($karyawan->foto) }}" alt="{{ $karyawan->nama_karyawan }}"
                                class="rounded-3 border border-1 border-slate-200"
                                style="width: 64px; height: 64px; object-fit: cover; background: #F8FAFC;"
                                onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'rounded-3 border border-1 border-slate-200 d-flex align-items-center justify-content-center fw-bold font-mono text-white\' style=\'width: 64px; height: 64px; font-size: 20px; background: #1E4D3E;\'>{{ strtoupper(substr($karyawan->nama_karyawan, 0, 2)) }}</div>';">
                        @else
                            <div class="rounded-3 border border-1 border-slate-200 d-flex align-items-center justify-content-center fw-bold font-mono text-white"
                                style="width: 64px; height: 64px; font-size: 20px; background: #1E4D3E;">
                                {{ strtoupper(substr($karyawan->nama_karyawan, 0, 2)) }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 p-1 {{ $karyawan->status_aktif_karyawan === '1' ? 'bg-success' : 'bg-danger' }} border border-2 border-white rounded-circle"
                            title="{{ $karyawan->status_aktif_karyawan === '1' ? 'Karyawan Aktif' : 'Nonaktif' }}"></span>
                    </div>

                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h5 class="mb-0 fw-bold text-dark text-truncate" style="letter-spacing: -0.01em;">{{ textCamelCase($karyawan->nama_karyawan) }}</h5>
                            @if ($karyawan->status_aktif_karyawan === '1')
                                <span class="badge badge-status-aktif">
                                    <i class="ti ti-check me-0.5"></i> Aktif
                                </span>
                            @else
                                <span class="badge badge-status-nonaktif">
                                    <i class="ti ti-x me-0.5"></i> Nonaktif
                                </span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-1.5 flex-wrap text-muted" style="font-size: 12px; line-height: 1.4;">
                            <span class="fw-semibold text-dark">{{ $karyawan->nama_jabatan ?? 'Staf' }}</span>
                            <span>•</span>
                            <span>{{ $karyawan->nama_dept ?? 'Operasional' }}</span>
                            <span>•</span>
                            <span class="d-inline-flex align-items-center gap-1 text-primary">
                                <i class="ti ti-map-pin" style="font-size: 12px;"></i> {{ $karyawan->nama_cabang ?? 'Outlet' }}
                            </span>
                            <span>•</span>
                            <span class="badge bg-label-info font-mono">{{ $karyawan->nama_jam_kerja ?? 'Shift Pagi' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Quick Stat Bar -->
            <div class="col-12 col-lg-5">
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div class="p-2 rounded-2 h-100 d-flex flex-column justify-content-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <small class="text-muted d-block text-truncate" style="font-size: 9.5px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">Tanggal Masuk</small>
                            <span class="fw-bold text-dark font-mono text-truncate" style="font-size: 11.5px;">
                                {{ !empty($karyawan->tanggal_masuk) ? date('d/m/Y', strtotime($karyawan->tanggal_masuk)) : '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-2 h-100 d-flex flex-column justify-content-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <small class="text-muted d-block text-truncate" style="font-size: 9.5px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">Shift Utama</small>
                            <span class="fw-bold text-dark font-mono text-truncate" style="font-size: 11.5px;">
                                {{ $karyawan->nama_jam_kerja ?? 'Shift Pagi' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                @can('karyawan.edit')
                    <a href="#" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 btnEdit" nik="{{ Crypt::encrypt($karyawan->nik) }}" style="font-size: 12px; padding: 6px 14px; border-radius: 6px;">
                        <i class="ti ti-edit"></i> <span>Edit Karyawan</span>
                    </a>
                @endcan
            </div>

            <div class="d-flex align-items-center gap-2">
                @can('users.create')
                    @if (empty($user))
                        <a class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5" href="{{ route('karyawan.createuser', Crypt::encrypt($karyawan->nik)) }}" style="font-size: 12px; padding: 6px 12px; border-radius: 6px;">
                            <i class="ti ti-user-plus"></i> Buat Akun Mobile
                        </a>
                    @else
                        <a class="btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1.5" href="{{ route('karyawan.deleteuser', Crypt::encrypt($karyawan->nik)) }}" style="font-size: 12px; padding: 6px 12px; border-radius: 6px;">
                            <i class="ti ti-user-x"></i> Reset Akun Mobile
                        </a>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="nav-align-top mb-4">
    <ul class="nav nav-pills gap-1 p-1.5 rounded-3 mb-3" id="karyawanDetailTabs" role="tablist" 
        style="background: #FFFFFF !important; border: 1px solid #E2E8F0;">
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-btn active py-2 px-3 d-flex align-items-center gap-1.5" id="pills-face-tab" data-tab="#tab-face" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-face-id fs-6"></i>
                <span class="fw-semibold">Biometrik Wajah</span>
                <span class="badge {{ $karyawan_wajah->count() > 0 ? 'bg-label-success' : 'bg-label-warning' }} ms-1 font-mono" style="font-size: 10px; padding: 2px 6px;">
                    {{ $karyawan_wajah->count() }}
                </span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-btn py-2 px-3 d-flex align-items-center gap-1.5" id="pills-profile-tab" data-tab="#tab-profile" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-user fs-6"></i>
                <span class="fw-semibold">Data Karyawan & Penugasan</span>
            </button>
        </li>
    </ul>

    <div class="tab-content p-0 border-0 shadow-none bg-transparent" id="karyawanDetailTabContent">
        <!-- TAB 1: BIOMETRIK WAJAH -->
        <div class="tab-pane active" id="tab-face" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                <div class="card-header py-3 px-3 px-md-4 d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="ti ti-face-id text-primary"></i>
                            <span>Dataset Biometrik Wajah (Face Recognition)</span>
                        </h6>
                        <small class="text-muted" style="font-size: 12px;">Foto dataset biometrik digunakan untuk verifikasi kehadiran karyawan saat absen.</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" id="btnAddface" style="font-size: 12px; padding: 6px 12px; border-radius: 6px;">
                            <i class="ti ti-camera-plus"></i> Tambah Foto Wajah
                        </button>
                        @if ($karyawan_wajah->isNotEmpty())
                            <form id="formHapusSemuaWajah" method="POST"
                                action="{{ route('facerecognition.destroyAll', Crypt::encrypt($karyawan->nik)) }}" class="d-inline m-0">
                                @csrf
                                <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1.5" id="btnHapusSemuaWajah" style="font-size: 12px; padding: 6px 12px; border-radius: 6px; background: #FFFFFF;">
                                    <i class="ti ti-trash"></i> Hapus Semua Dataset
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    @if ($karyawan_wajah->isNotEmpty())
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3">
                            @foreach ($karyawan_wajah as $wajah)
                                <div class="col">
                                    <div class="card h-100 border shadow-none" style="border-radius: 8px; overflow: hidden;">
                                        <img src="{{ asset('storage/uploads/facerecognition/' . $karyawan->nik . '-' . getNamaDepan(strtolower($karyawan->nama_karyawan)) . '/' . $wajah->wajah) }}" 
                                            class="card-img-top" style="height: 120px; object-fit: cover;" alt="Wajah">
                                        <div class="card-body p-2 text-center">
                                            <form action="{{ route('facerecognition.delete', $wajah->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2" style="font-size: 11px;">
                                                    <i class="ti ti-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="ti ti-face-id-error fs-1 d-block mb-2 text-muted" style="opacity: 0.4;"></i>
                            Belum ada foto wajah didaftarkan untuk verifikasi presensi.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 2: DATA KARYAWAN & PENUGASAN -->
        <div class="tab-pane" id="tab-profile" role="tabpanel" style="display: none;">
            <div class="row g-3">
                <div class="col-lg-6 col-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-3 px-3 px-md-4 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="ti ti-user text-primary"></i>
                                <span>Informasi Karyawan</span>
                            </h6>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 38%;">NIK / ID</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ $karyawan->nik_show ?? $karyawan->nik }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Nama Lengkap</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->nama_karyawan }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Jenis Kelamin</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">No. Handphone</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ $karyawan->no_hp ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Alamat</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->alamat ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-3 px-3 px-md-4 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="ti ti-briefcase text-primary"></i>
                                <span>Penugasan & Shift Presensi</span>
                            </h6>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 38%;">Outlet / Cabang</td>
                                        <td class="fw-bold text-dark pe-0 py-2">{{ $karyawan->nama_cabang ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Departemen</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->nama_dept ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Jabatan</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->nama_jabatan ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Shift Kerja</td>
                                        <td class="fw-bold font-mono text-primary pe-0 py-2">
                                            {{ $karyawan->nama_jam_kerja ?? 'Shift Pagi (07:00)' }}
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Tanggal Bergabung</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ !empty($karyawan->tanggal_masuk) ? date('d/m/Y', strtotime($karyawan->tanggal_masuk)) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Status Akun Mobile</td>
                                        <td class="pe-0 py-2">
                                            @if ($user)
                                                <span class="badge bg-label-success font-mono">Aktif ({{ $user->username }})</span>
                                            @else
                                                <span class="badge bg-label-secondary font-mono">Belum Ada Akun</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />

@endsection

@push('myscript')
<script>
    $(function() {
        $(document).on('click', '.tab-btn', function(e) {
            e.preventDefault();
            const target = $(this).attr('data-tab');
            $('.tab-btn').removeClass('active').attr('aria-selected', 'false');
            $(this).addClass('active').attr('aria-selected', 'true');
            $('#karyawanDetailTabContent > .tab-pane').removeClass('active show').hide();
            $(target).addClass('active show').show();
        });

        $('#btnAddface').click(function(e) {
            e.preventDefault();
            $('#modal').modal('show');
            $('#modal').find('.modal-title').text('Tambah Foto Wajah');
            $('#modal').find('#loadmodal').load('{{ route("facerecognition.create", Crypt::encrypt($karyawan->nik)) }}');
        });

        $('.btnEdit').click(function(e) {
            e.preventDefault();
            const nik = $(this).attr('nik');
            $('#modal').modal('show');
            $('#modal').find('.modal-title').text('Edit Karyawan');
            $('#modal').find('#loadmodal').load(`/karyawan/${nik}/edit`);
        });

        $('#btnHapusSemuaWajah').click(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Semua Wajah?',
                text: 'Semua dataset wajah biometrik karyawan ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Semua!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#formHapusSemuaWajah').submit();
                }
            });
        });
    });
</script>
@endpush
