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

<!-- 1. Hero Profile & Action Card (Unified, Responsive & Crisp) -->
<div class="card mb-3 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 14px; overflow: visible !important; background: #FFFFFF !important;">
    <div style="height: 3px; background: linear-gradient(90deg, #1E4D3E, #32745E, #059669); border-top-left-radius: 13px; border-top-right-radius: 13px;"></div>
    <div class="card-body p-3 p-md-4" style="overflow: visible !important;">
        <!-- Top Profile Info & Quick Stats Grid -->
        <div class="row align-items-center gy-3">
            <!-- Left: Avatar & Identity Details -->
            <div class="col-12 col-lg-7">
                <div class="d-flex align-items-start align-items-sm-center gap-3">
                    <div class="position-relative flex-shrink-0">
                        @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                            <img src="{{ getfotoKaryawan($karyawan->foto) }}" alt="{{ $karyawan->nama_karyawan }}"
                                class="rounded-3 border border-1 border-slate-200"
                                style="width: 64px; height: 64px; object-fit: cover; background: #F8FAFC;">
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
                                <span class="badge bg-label-success font-mono" style="font-size: 10.5px; padding: 2px 7px;">
                                    <i class="ti ti-check me-0.5"></i> Aktif
                                </span>
                            @else
                                <span class="badge bg-label-danger font-mono" style="font-size: 10.5px; padding: 2px 7px;">
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
                                <i class="ti ti-map-pin" style="font-size: 12px;"></i> {{ $karyawan->nama_cabang ?? 'Outlet Utama' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: 3-Tile Stat Bar (Guaranteed Responsive 3 Columns) -->
            <div class="col-12 col-lg-5">
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="p-2 rounded-2 h-100 d-flex flex-column justify-content-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <small class="text-muted d-block text-truncate" style="font-size: 9.5px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">Masuk</small>
                            <span class="fw-bold text-dark font-mono text-truncate" style="font-size: 11.5px;">
                                {{ !empty($karyawan->tanggal_masuk) ? date('d/m/y', strtotime($karyawan->tanggal_masuk)) : '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-4">
                        @php
                            $masuk = !empty($karyawan->tanggal_masuk) ? \Carbon\Carbon::parse($karyawan->tanggal_masuk) : null;
                            $now = \Carbon\Carbon::now();
                            $diff = $masuk ? $masuk->diff($now) : null;
                        @endphp
                        <div class="p-2 rounded-2 h-100 d-flex flex-column justify-content-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <small class="text-muted d-block text-truncate" style="font-size: 9.5px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">Masa Kerja</small>
                            <span class="fw-bold text-dark font-mono text-truncate" style="font-size: 11.5px;">
                                @if($diff)
                                    {{ $diff->y > 0 ? $diff->y . 'th ' : '' }}{{ $diff->m }}bln
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-2 h-100 d-flex flex-column justify-content-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <small class="text-muted d-block text-truncate" style="font-size: 9.5px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">Face ID AI</small>
                            <span class="fw-bold font-mono text-truncate {{ $karyawan_wajah->count() > 0 ? 'text-success' : 'text-warning' }}" style="font-size: 11.5px;">
                                {{ $karyawan_wajah->count() }} Sample
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Integrated Clean Responsive Action Bar -->
        <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
            <!-- Left Primary Actions -->
            <div class="d-flex align-items-center flex-wrap gap-1.5">
                <!-- Edit Data (Primary Action) -->
                @can('karyawan.edit')
                    <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 btnEdit"
                        nik="{{ Crypt::encrypt($karyawan->nik) }}" style="font-size: 12px; height: 32px; border-radius: 6px; font-weight: 600;">
                        <i class="ti ti-edit" style="font-size: 14px;"></i> <span>Edit Data</span>
                    </button>
                @endcan

                <!-- Atur Shift -->
                @can('karyawan.setjamkerja')
                    <button type="button" class="btn btn-sm btn-white text-dark border d-inline-flex align-items-center gap-1 btnSetJamkerja"
                        nik="{{ Crypt::encrypt($karyawan->nik) }}" style="font-size: 12px; height: 32px; border-radius: 6px; background: #FFFFFF; border-color: #CBD5E1 !important;">
                        <i class="ti ti-clock-play text-success" style="font-size: 14px;"></i> <span>Shift Kerja</span>
                    </button>
                @endcan

                <!-- Atur Cabang -->
                @can('karyawan.setcabang')
                    <button type="button" class="btn btn-sm btn-white text-dark border d-inline-flex align-items-center gap-1 btnSetCabang"
                        nik="{{ Crypt::encrypt($karyawan->nik) }}" style="font-size: 12px; height: 32px; border-radius: 6px; background: #FFFFFF; border-color: #CBD5E1 !important;">
                        <i class="ti ti-map-pin text-warning" style="font-size: 14px;"></i> <span>Outlet Cabang</span>
                    </button>
                @endcan

                <!-- Toggle Lock GPS (Desktop Only - In Dropdown for Mobile) -->
                @can('karyawan.edit')
                    <button type="button" 
                        data-url="{{ route('karyawan.lockunlocklocation', Crypt::encrypt($karyawan->nik)) }}" 
                        class="btn btn-sm btn-white text-dark border d-none d-md-inline-flex align-items-center gap-1 btnToggleLock"
                        data-type="location"
                        style="font-size: 12px; height: 32px; border-radius: 6px; background: #FFFFFF; border-color: #CBD5E1 !important;">
                        <i class="ti {{ $karyawan->lock_location == '1' ? 'ti-lock text-success' : 'ti-lock-open text-danger' }}" style="font-size: 14px;"></i>
                        <span>GPS: {{ $karyawan->lock_location == '1' ? 'Locked' : 'Unlocked' }}</span>
                    </button>
                @endcan

                <!-- Toggle Lock Shift (Desktop Only - In Dropdown for Mobile) -->
                @can('karyawan.edit')
                    <button type="button" 
                        data-url="{{ route('karyawan.lockunlockjamkerja', Crypt::encrypt($karyawan->nik)) }}" 
                        class="btn btn-sm btn-white text-dark border d-none d-lg-inline-flex align-items-center gap-1 btnToggleLock"
                        data-type="shift"
                        style="font-size: 12px; height: 32px; border-radius: 6px; background: #FFFFFF; border-color: #CBD5E1 !important;">
                        <i class="ti {{ $karyawan->lock_jam_kerja == '1' ? 'ti-lock text-success' : 'ti-lock-open text-danger' }}" style="font-size: 14px;"></i>
                        <span>Shift: {{ $karyawan->lock_jam_kerja == '1' ? 'Locked' : 'Unlocked' }}</span>
                    </button>
                @endcan

                <!-- Dropdown Opsi Lainnya (Organized & Compact) -->
                <div class="dropdown" style="position: relative; z-index: 25;">
                    <button class="btn btn-sm btn-white text-dark border dropdown-toggle d-inline-flex align-items-center gap-1" 
                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="font-size: 12px; height: 32px; border-radius: 6px; background: #FFFFFF; border-color: #CBD5E1 !important;">
                        <i class="ti ti-dots-vertical" style="font-size: 14px;"></i>
                        <span>Opsi Lainnya</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-start shadow-lg py-1" style="border-radius: 10px; border: 1px solid #E2E8F0; min-width: 210px; font-size: 12.5px; z-index: 1060 !important;">
                        <!-- GPS Lock for Mobile -->
                        @can('karyawan.edit')
                            <li class="d-md-none">
                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 btnToggleLock" data-type="location" data-url="{{ route('karyawan.lockunlocklocation', Crypt::encrypt($karyawan->nik)) }}">
                                    <i class="ti {{ $karyawan->lock_location == '1' ? 'ti-lock text-success' : 'ti-lock-open text-danger' }}"></i>
                                    <span>GPS Lock: <strong>{{ $karyawan->lock_location == '1' ? 'Terkunci' : 'Bebas' }}</strong></span>
                                </button>
                            </li>
                            <li class="d-lg-none">
                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 btnToggleLock" data-type="shift" data-url="{{ route('karyawan.lockunlockjamkerja', Crypt::encrypt($karyawan->nik)) }}">
                                    <i class="ti {{ $karyawan->lock_jam_kerja == '1' ? 'ti-lock text-success' : 'ti-lock-open text-danger' }}"></i>
                                    <span>Shift Lock: <strong>{{ $karyawan->lock_jam_kerja == '1' ? 'Terkunci' : 'Bebas' }}</strong></span>
                                </button>
                            </li>
                            <li class="d-md-none"><hr class="dropdown-divider my-1"></li>
                        @endcan

                        <!-- Akun Mobile -->
                        @can('users.create')
                            @if (empty($user))
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('karyawan.createuser', Crypt::encrypt($karyawan->nik)) }}">
                                        <i class="ti ti-user-plus text-primary"></i>
                                        <span>Buat Akun Mobile</span>
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-warning delete-confirm" href="{{ route('karyawan.deleteuser', Crypt::encrypt($karyawan->nik)) }}">
                                        <i class="ti ti-user-x"></i>
                                        <span>Reset Akun Mobile</span>
                                    </a>
                                </li>
                            @endif
                        @endcan

                        <!-- Cetak ID Card -->
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('karyawan.idcard', Crypt::encrypt($karyawan->nik)) }}" target="_blank">
                                <i class="ti ti-id text-secondary"></i>
                                <span>Cetak ID Card</span>
                            </a>
                        </li>

                        <!-- Hapus Karyawan (In Dropdown for Safety & Compactness) -->
                        @can('karyawan.delete')
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" name="deleteform" class="deleteform m-0"
                                    action="{{ route('karyawan.delete', Crypt::encrypt($karyawan->nik)) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger delete-confirm border-0 bg-transparent w-100 text-start">
                                        <i class="ti ti-trash"></i>
                                        <span>Hapus Karyawan</span>
                                    </button>
                                </form>
                            </li>
                        @endcan
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Instant Zero-Delay Tab Switching */
    #karyawanDetailTabs .nav-link {
        cursor: pointer;
        user-select: none;
        transition: none !important;
    }
    #karyawanDetailTabs .nav-link * {
        pointer-events: none !important;
    }
</style>

<!-- 3. MAIN WORKSPACE TABS SYSTEM (RESPONSIVE & CLEAN) -->
<div class="nav-align-top mb-4">
    <ul class="nav nav-pills gap-1 p-1.5 rounded-3 mb-3" id="karyawanDetailTabs" role="tablist" 
        style="background: #FFFFFF !important; border: 1px solid #E2E8F0; overflow-x: auto; flex-wrap: nowrap; -webkit-overflow-scrolling: touch;">
        <li class="nav-item flex-shrink-0" role="presentation">
            <button class="nav-link tab-btn active py-2 px-2.5 px-md-3 d-flex align-items-center gap-1.5" id="pills-face-tab" data-tab="#tab-face" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-face-id fs-6"></i>
                <span class="fw-semibold"><span class="d-none d-sm-inline">Biometrik </span>Wajah</span>
                <span class="badge {{ $karyawan_wajah->count() > 0 ? 'bg-label-success' : 'bg-label-warning' }} ms-1 font-mono" style="font-size: 10px; padding: 2px 6px;">
                    {{ $karyawan_wajah->count() }}
                </span>
            </button>
        </li>
        <li class="nav-item flex-shrink-0" role="presentation">
            <button class="nav-link tab-btn py-2 px-2.5 px-md-3 d-flex align-items-center gap-1.5" id="pills-profile-tab" data-tab="#tab-profile" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-user fs-6"></i>
                <span class="fw-semibold"><span class="d-none d-sm-inline">Data </span>Pribadi</span>
            </button>
        </li>
        <li class="nav-item flex-shrink-0" role="presentation">
            <button class="nav-link tab-btn py-2 px-2.5 px-md-3 d-flex align-items-center gap-1.5" id="pills-employment-tab" data-tab="#tab-employment" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-briefcase fs-6"></i>
                <span class="fw-semibold">Kepegawaian</span>
            </button>
        </li>
        <li class="nav-item flex-shrink-0" role="presentation">
            <button class="nav-link tab-btn py-2 px-2.5 px-md-3 d-flex align-items-center gap-1.5" id="pills-mutation-tab" data-tab="#tab-mutation" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-arrows-exchange fs-6"></i>
                <span class="fw-semibold">Mutasi</span>
                <span class="badge bg-label-primary ms-1 font-mono" style="font-size: 10px; padding: 2px 6px;">{{ $mutasi->count() }}</span>
            </button>
        </li>
        <li class="nav-item flex-shrink-0" role="presentation">
            <button class="nav-link tab-btn py-2 px-2.5 px-md-3 d-flex align-items-center gap-1.5" id="pills-training-tab" data-tab="#tab-training" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-certificate fs-6"></i>
                <span class="fw-semibold">Pelatihan</span>
            </button>
        </li>
    </ul>

    <div class="tab-content p-0 border-0 shadow-none bg-transparent" id="karyawanDetailTabContent">
        <!-- TAB 1: BIOMETRIK WAJAH (FACE ID) -->
        <div class="tab-pane active" id="tab-face" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                <div class="card-header py-3 px-3 px-md-4 d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="ti ti-face-id text-primary"></i>
                            <span>Dataset Biometrik Wajah (Face Recognition AI)</span>
                        </h6>
                        <small class="text-muted" style="font-size: 12px;">Foto dataset biometrik digunakan sistem AI untuk mencocokkan wajah karyawan saat absen mobile / scanner.</small>
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
                            @foreach ($karyawan_wajah as $d)
                                @php
                                    $folder = $karyawan->nik . '-' . getNamaDepan(strtolower($karyawan->nama_karyawan));
                                    $url = url('/storage/uploads/facerecognition/' . $folder . '/' . $d->wajah);
                                    $urlWithTimestamp = $url . '?v=' . time();
                                @endphp
                                <div class="col">
                                    <div class="card h-100 overflow-hidden shadow-none" style="border: 1px solid #E2E8F0 !important; border-radius: 10px; background: #FFFFFF !important;">
                                        <div class="position-relative" style="background: #F8FAFC;">
                                            <img src="{{ $urlWithTimestamp }}" class="card-img-top face-image" alt="Wajah"
                                                style="height: 140px; width: 100%; object-fit: cover; cursor: pointer;"
                                                data-bs-toggle="modal" data-bs-target="#modalFotoWajah" data-image="{{ $urlWithTimestamp }}">
                                            <span class="position-absolute top-0 start-0 m-1.5 badge bg-dark text-white font-mono" style="font-size: 10px; opacity: 0.85;">
                                                #{{ $loop->iteration }}
                                            </span>
                                        </div>
                                        <div class="p-2 d-flex justify-content-between align-items-center bg-white border-top">
                                            <small class="text-muted font-mono" style="font-size: 10px;">Sample Wajah</small>
                                            <form method="POST" name="deleteform" class="deleteform d-inline m-0"
                                                action="{{ route('facerecognition.delete', Crypt::encrypt($d->id)) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger delete-confirm px-1.5 py-0.5" title="Hapus Sample" style="border-radius: 4px;">
                                                    <i class="ti ti-trash" style="font-size: 12px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 p-3 rounded-3" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 12px;">
                                <i class="ti ti-bulb text-warning fs-5"></i>
                                <span><strong>Tips Akurasi AI:</strong> Pastikan minimal 3-5 variasi sample wajah telah terdaftar (ekspresi netral, senyum tipis, sudut sedikit miring) dengan pencahayaan yang jelas tanpa masker atau topi.</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5 px-3">
                            <div class="avatar avatar-xl rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="background: #FEF3C7; color: #D97706; width: 64px; height: 64px;">
                                <i class="ti ti-face-id fs-1"></i>
                            </div>
                            <h5 class="mb-1 text-dark fw-bold">Belum Ada Dataset Wajah Terdaftar</h5>
                            <p class="text-muted mb-3 mx-auto" style="max-width: 480px; font-size: 13px;">
                                Karyawan belum dapat diverifikasi oleh sistem Face Recognition saat melakukan presensi. Daftarkan minimal 3 sample foto wajah sekarang.
                            </p>
                            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" id="btnAddfaceEmpty" style="border-radius: 8px; font-size: 13px; padding: 8px 16px;">
                                <i class="ti ti-camera-plus"></i> Daftarkan Wajah Sekarang
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 2: DATA PRIBADI & KONTAK -->
        <div class="tab-pane" id="tab-profile" role="tabpanel" style="display: none;">
            <div class="row g-3">
                <!-- Left: Identitas Pribadi -->
                <div class="col-lg-6 col-md-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-3 px-3 px-md-4 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="ti ti-id text-primary"></i>
                                <span>Identitas KTP & Biodata</span>
                            </h6>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 38%;">No. KTP</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ $karyawan->no_ktp ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Nama Lengkap</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->nama_karyawan }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Tempat, Tanggal Lahir</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">
                                            {{ $karyawan->tempat_lahir ?? '-' }}, {{ !empty($karyawan->tanggal_lahir) ? DateToIndo($karyawan->tanggal_lahir) : '-' }}
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Jenis Kelamin</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Status Pernikahan</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->status_kawin ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Pendidikan Terakhir</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">
                                            {{ $karyawan->pendidikan_terakhir ?? '-' }} @if(!empty($karyawan->jurusan)) ({{ $karyawan->jurusan }}) @endif
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Alamat Sesuai KTP</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->alamat_sesuai_ktp ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Alamat Domisili</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->alamat ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right: Kontak & Perbankan -->
                <div class="col-lg-6 col-md-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-3 px-3 px-md-4 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="ti ti-phone text-primary"></i>
                                <span>Kontak & Perbankan</span>
                            </h6>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 38%;">No. Handphone</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">
                                            @if (!empty($karyawan->no_hp))
                                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $karyawan->no_hp)) }}" target="_blank" class="text-primary d-inline-flex align-items-center gap-1">
                                                    <i class="ti ti-brand-whatsapp text-success"></i> {{ $karyawan->no_hp }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Alamat Email</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->email ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Kontak Darurat</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">
                                            {{ $karyawan->kontak_darurat ?? '-' }}
                                            @if (!empty($karyawan->hubungan_kontak_darurat))
                                                <small class="text-muted">({{ $karyawan->hubungan_kontak_darurat }})</small>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Nama Bank</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">
                                            @if (!empty($karyawan->nama_bank))
                                                <span class="badge bg-light text-dark font-mono" style="border: 1px solid #CBD5E1;">{{ strtoupper($karyawan->nama_bank) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">No. Rekening</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ $karyawan->no_rekening ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Atas Nama Rekening</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->nama_rekening ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Nomor NPWP</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ $karyawan->npwp ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Perhitungan PPh 21</td>
                                        <td class="pe-0 py-2">
                                            @if (($karyawan->hitung_pph21 ?? 1) == 1)
                                                <span class="badge bg-label-success font-mono">Dihitung</span>
                                            @else
                                                <span class="badge bg-label-secondary font-mono">Tidak Dihitung</span>
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

        <!-- TAB 3: KEPEGAWAIAN & AKUN -->
        <div class="tab-pane" id="tab-employment" role="tabpanel" style="display: none;">
            <div class="row g-3">
                <!-- Left: Status Kepegawaian -->
                <div class="col-lg-6 col-md-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-3 px-3 px-md-4 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="ti ti-briefcase text-primary"></i>
                                <span>Status Kepegawaian & Penugasan</span>
                            </h6>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 38%;">Cabang Penugasan</td>
                                        <td class="fw-bold text-dark pe-0 py-2">
                                            <i class="ti ti-map-pin text-primary me-1"></i> {{ $karyawan->nama_cabang ?? 'Outlet Utama' }}
                                        </td>
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
                                        <td class="text-muted ps-0 py-2">Status Ikatan Kerja</td>
                                        <td class="pe-0 py-2">
                                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #CBD5E1;">
                                                {{ $karyawan->status_karyawan ?? 'Tetap' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Tanggal Bergabung</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ !empty($karyawan->tanggal_masuk) ? DateToIndo($karyawan->tanggal_masuk) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Status Aktif</td>
                                        <td class="pe-0 py-2">
                                            @if ($karyawan->status_aktif_karyawan === '1')
                                                <span class="badge bg-label-success">Aktif Bekerja</span>
                                            @else
                                                <span class="badge bg-label-danger">Non-Aktif / Resign</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right: Autentikasi & Akun Mobile -->
                <div class="col-lg-6 col-md-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-3 px-3 px-md-4 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="ti ti-shield-lock text-primary"></i>
                                <span>Keamanan Presensi & Akun Mobile</span>
                            </h6>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 38%;">Akun Mobile Karyawan</td>
                                        <td class="pe-0 py-2">
                                            @if ($user)
                                                <span class="badge bg-label-success font-mono">
                                                    <i class="ti ti-check me-1"></i> Aktif ({{ $user->username }})
                                                </span>
                                            @else
                                                <span class="badge bg-label-secondary font-mono">
                                                    Belum Didaftarkan
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Lock Lokasi GPS</td>
                                        <td class="pe-0 py-2">
                                            <span id="badge-lock-location" class="badge {{ $karyawan->lock_location == '1' ? 'bg-label-success' : 'bg-label-warning' }}">
                                                {{ $karyawan->lock_location == '1' ? 'Terkunci di Outlet' : 'Bebas Lokasi' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Lock Jam Shift</td>
                                        <td class="pe-0 py-2">
                                            <span id="badge-lock-shift" class="badge {{ $karyawan->lock_jam_kerja == '1' ? 'bg-label-success' : 'bg-label-warning' }}">
                                                {{ $karyawan->lock_jam_kerja == '1' ? 'Terkunci Sesuai Jadwal' : 'Bebas Jam Kerja' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">RFID Tag UID</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ $karyawan->rfid_uid ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">PIN Presensi Karyawan</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ $karyawan->pin ? '••••••' : 'Default (0000)' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: RIWAYAT MUTASI -->
        <div class="tab-pane" id="tab-mutation" role="tabpanel" style="display: none;">
            <div class="card border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                <div class="card-header py-3 px-3 px-md-4 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="ti ti-arrows-exchange text-primary"></i>
                        <span>Log Histori Mutasi & Promosi Cabang</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="padding: 12px 16px;">Tanggal</th>
                                    <th>Jenis Mutasi</th>
                                    <th>Outlet / Cabang Asal</th>
                                    <th>Outlet / Cabang Tujuan</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mutasi as $m)
                                    <tr>
                                        <td class="font-mono" style="padding: 12px 16px; font-size: 12px;">{{ DateToIndo($m->tanggal_mutasi) }}</td>
                                        <td><span class="badge bg-label-primary">{{ $m->jenis_mutasi ?? 'Mutasi' }}</span></td>
                                        <td style="font-size: 13px;">{{ $m->cabangLama->nama_cabang ?? '-' }}</td>
                                        <td style="font-size: 13px;" class="fw-semibold text-dark">{{ $m->cabangBaru->nama_cabang ?? '-' }}</td>
                                        <td style="font-size: 13px;" class="text-muted">{{ $m->keterangan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted" style="font-size: 13px;">
                                            <i class="ti ti-arrows-shuffle fs-1 d-block mb-2 text-muted" style="opacity: 0.35;"></i>
                                            Belum ada log mutasi atau promosi tercatat untuk karyawan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: PELATIHAN & SKILL -->
        <div class="tab-pane" id="tab-training" role="tabpanel" style="display: none;">
            <div class="card border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                <div class="card-header py-3 px-3 px-md-4 d-flex align-items-center justify-content-between border-bottom">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="ti ti-certificate text-primary"></i>
                        <span>Pelatihan, Sertifikasi & Keahlian</span>
                    </h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" id="btnAddTraining" style="font-size: 12px; padding: 6px 12px; border-radius: 6px;">
                            <i class="ti ti-plus"></i> Tambah Pelatihan
                        </button>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4" id="load-training">
                    <div class="text-center py-5 text-muted" style="font-size: 13px;">
                        <i class="ti ti-loader-2 ti-spin fs-2 d-block mb-2 text-primary"></i>
                        Memuat data pelatihan...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dialogs -->
<x-modal-form id="modal" show="loadmodal" />

<!-- Modal Zoom Foto Wajah -->
<div class="modal fade" id="modalFotoWajah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; background: #1E293B;">
            <div class="modal-body p-0 text-center">
                <img src="" id="modalImage" class="img-fluid" style="max-height: 440px; width: 100%; object-fit: contain;">
            </div>
            <div class="modal-footer py-2 px-3 border-0 justify-content-center">
                <button type="button" class="btn btn-sm btn-outline-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    let trainingLoaded = false;
    function loadTraining() {
        if (trainingLoaded) return;
        var nik = "{{ Crypt::encrypt($karyawan->nik) }}";
        $("#load-training").html(`<div class="text-center py-4"><i class="ti ti-loader-2 ti-spin fs-2 text-primary d-block mb-2"></i><span class="text-muted">Memuat data pelatihan...</span></div>`);
        $("#load-training").load('/pelatihan/' + nik + '/index', function() {
            trainingLoaded = true;
        });
    }

    $(function() {
        function activateTab(targetPane) {
            if (!targetPane) return;
            
            // Highlight button
            $('.tab-btn').removeClass('active').attr('aria-selected', 'false');
            $(`.tab-btn[data-tab="${targetPane}"]`).addClass('active').attr('aria-selected', 'true');

            // Show pane directly (instantaneous, zero lag, zero blink)
            $('#karyawanDetailTabContent > .tab-pane').removeClass('active show').hide();
            $(targetPane).addClass('active show').show();

            // Lazy load training if selected
            if (targetPane === '#tab-training') {
                loadTraining();
            }
        }

        // Instant Single-Click Tab Switcher
        $(document).on('click', '.tab-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const target = $(this).attr('data-tab');
            activateTab(target);
        });

        // Check hash on page load only (no hashchange loop)
        const initialHash = window.location.hash.toLowerCase();
        if (initialHash === '#profil' || initialHash === '#profile') activateTab('#tab-profile');
        else if (initialHash === '#kepegawaian' || initialHash === '#employment') activateTab('#tab-employment');
        else if (initialHash === '#mutasi' || initialHash === '#mutation') activateTab('#tab-mutation');
        else if (initialHash === '#pelatihan' || initialHash === '#training') activateTab('#tab-training');
        else activateTab('#tab-face');

        // Instant Single-Click Lock/Unlock Toggle (Shift & GPS) - Zero Reload
        let lockToggling = false;
        $(document).on('click', '.btnToggleLock', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (lockToggling) return;
            lockToggling = true;

            const $this = $(this);
            const url = $this.attr('data-url') || $this.attr('href');
            const type = $this.data('type'); // 'shift' or 'location'

            // Subtle feedback during quick async request
            $('.btnToggleLock[data-type="' + type + '"]').css('opacity', '0.6').css('pointer-events', 'none');

            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(res) {
                    if (res && res.success) {
                        const isLocked = res.status == 1;

                        if (type === 'shift') {
                            // Update desktop button
                            const $desktopBtn = $('.btnToggleLock[data-type="shift"]:not(.dropdown-item)');
                            $desktopBtn.find('i')
                                .removeClass('ti-lock ti-lock-open text-success text-danger')
                                .addClass(isLocked ? 'ti-lock text-success' : 'ti-lock-open text-danger');
                            $desktopBtn.find('span').text('Shift: ' + (isLocked ? 'Locked' : 'Unlocked'));

                            // Update mobile dropdown item
                            const $mobileItem = $('.btnToggleLock[data-type="shift"].dropdown-item');
                            $mobileItem.find('i')
                                .removeClass('ti-lock ti-lock-open text-success text-danger')
                                .addClass(isLocked ? 'ti-lock text-success' : 'ti-lock-open text-danger');
                            $mobileItem.find('span').html('Shift Lock: <strong>' + (isLocked ? 'Terkunci' : 'Bebas') + '</strong>');

                            // Update Kepegawaian tab badge
                            $('#badge-lock-shift')
                                .removeClass('bg-label-success bg-label-warning')
                                .addClass(isLocked ? 'bg-label-success' : 'bg-label-warning')
                                .text(isLocked ? 'Terkunci Sesuai Jadwal' : 'Bebas Jam Kerja');
                        } else if (type === 'location') {
                            // Update desktop button
                            const $desktopBtn = $('.btnToggleLock[data-type="location"]:not(.dropdown-item)');
                            $desktopBtn.find('i')
                                .removeClass('ti-lock ti-lock-open text-success text-danger')
                                .addClass(isLocked ? 'ti-lock text-success' : 'ti-lock-open text-danger');
                            $desktopBtn.find('span').text('GPS: ' + (isLocked ? 'Locked' : 'Unlocked'));

                            // Update mobile dropdown item
                            const $mobileItem = $('.btnToggleLock[data-type="location"].dropdown-item');
                            $mobileItem.find('i')
                                .removeClass('ti-lock ti-lock-open text-success text-danger')
                                .addClass(isLocked ? 'ti-lock text-success' : 'ti-lock-open text-danger');
                            $mobileItem.find('span').html('GPS Lock: <strong>' + (isLocked ? 'Terkunci' : 'Bebas') + '</strong>');

                            // Update Kepegawaian tab badge
                            $('#badge-lock-location')
                                .removeClass('bg-label-success bg-label-warning')
                                .addClass(isLocked ? 'bg-label-success' : 'bg-label-warning')
                                .text(isLocked ? 'Terkunci di Outlet' : 'Bebas Lokasi');
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.success(res.message, 'Status Diperbarui', { timeOut: 2000 });
                        }
                    }
                },
                error: function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Gagal memperbarui status', 'Error');
                    }
                },
                complete: function() {
                    $('.btnToggleLock[data-type="' + type + '"]').css('opacity', '1').css('pointer-events', 'auto');
                    lockToggling = false;
                }
            });
        });

        // Face registration triggers
        $(document).on('click', '#btnAddface, #btnAddfaceEmpty', function(e) {
            e.preventDefault();
            $('#modal').modal("show");
            $('#modal').find(".modal-title").text("Perekaman Dataset Wajah AI");
            $("#loadmodal").html(`<div class="text-center py-5"><i class="ti ti-loader-2 ti-spin fs-2 text-primary d-block mb-2"></i><span class="text-muted">Membuka kamera biometrik...</span></div>`);
            $("#loadmodal").load('/facerecognition/' + '{{ Crypt::encrypt($karyawan->nik) }}' + '/create');
        });

        // Delete all faces confirm
        $(document).on('click', '#btnHapusSemuaWajah', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Semua Dataset Wajah?',
                text: "Semua sample wajah karyawan ini akan dihapus dan harus didaftarkan ulang.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#formHapusSemuaWajah').submit();
                }
            });
        });

        // Edit Profile Modal
        $(document).on('click', '.btnEdit', function() {
            const nik = $(this).attr("nik");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Karyawan");
            $("#loadmodal").html(`<div class="text-center py-5"><i class="ti ti-loader-2 ti-spin fs-2 text-primary d-block mb-2"></i><span class="text-muted">Memuat formulir...</span></div>`);
            $("#loadmodal").load(`/karyawan/${nik}/edit`);
        });

        // Set Jam Kerja Modal
        $(document).on('click', '.btnSetJamkerja', function() {
            const nik = $(this).attr("nik");
            $("#modal").modal("show");
            $(".modal-title").text("Atur Shift Kerja Karyawan");
            $("#loadmodal").html(`<div class="text-center py-5"><i class="ti ti-loader-2 ti-spin fs-2 text-primary d-block mb-2"></i><span class="text-muted">Memuat shift kerja...</span></div>`);
            $("#loadmodal").load(`/karyawan/${nik}/setjamkerja`);
        });

        // Set Cabang Modal
        $(document).on('click', '.btnSetCabang', function() {
            const nik = $(this).attr("nik");
            $("#modal").modal("show");
            $(".modal-title").text("Atur Cabang Penugasan");
            $("#loadmodal").html(`<div class="text-center py-5"><i class="ti ti-loader-2 ti-spin fs-2 text-primary d-block mb-2"></i><span class="text-muted">Memuat penugasan...</span></div>`);
            $("#loadmodal").load(`/karyawan/${nik}/setcabang`);
        });

        // Add Training Modal
        $(document).on('click', '#btnAddTraining', function(e) {
            e.preventDefault();
            var nik = "{{ Crypt::encrypt($karyawan->nik) }}";
            $('#modal').modal("show");
            $('#modal').find(".modal-title").text("Tambah Pelatihan");
            $("#loadmodal").html(`<div class="text-center py-5"><i class="ti ti-loader-2 ti-spin fs-2 text-primary d-block mb-2"></i><span class="text-muted">Memuat formulir...</span></div>`);
            $("#loadmodal").load('/pelatihan/' + nik + '/create');
        });

        // Image Zoom Modal
        const modalFotoWajah = document.getElementById('modalFotoWajah');
        if (modalFotoWajah) {
            modalFotoWajah.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const imageUrl = button.getAttribute('data-image');
                const modalImage = this.querySelector('#modalImage');
                modalImage.src = imageUrl;
            });
        }
    });
</script>
@endpush
