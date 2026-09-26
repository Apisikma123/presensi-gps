@extends('layouts.app')
@section('titlepage', 'Detail Karyawan - ' . textCamelCase($karyawan->nama_karyawan))

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Data Karyawan</a></li>
    <li class="breadcrumb-item active">{{ textCamelCase($karyawan->nama_karyawan) }}</li>
@endsection

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
    <div style="height: 3px; background: linear-gradient(90deg, var(--theme-color-1, #3C2A21), var(--theme-color-2, #634832), var(--theme-color-accent, #059669)); border-top-left-radius: 13px; border-top-right-radius: 13px;"></div>
    <div class="card-body p-3 p-md-4" style="overflow: visible !important;">
        <div class="row align-items-center gy-3">
            <!-- Left: Avatar & Identity Details -->
            <div class="col-12 col-lg-7">
                <div class="d-flex align-items-start align-items-sm-center gap-3">
                    <div class="position-relative flex-shrink-0">
                        @if (!empty($karyawan->foto))
                            <img src="{{ getfotoKaryawan($karyawan->foto) }}" alt="{{ $karyawan->nama_karyawan }}"
                                class="rounded-3 border border-1 border-slate-200"
                                style="width: 68px; height: 68px; object-fit: cover; background: #F8FAFC;"
                                onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'rounded-3 border border-1 border-slate-200 d-flex align-items-center justify-content-center fw-bold font-mono text-white\' style=\'width: 68px; height: 68px; font-size: 22px; background: var(--theme-color-1, #3C2A21);\'>{{ strtoupper(substr($karyawan->nama_karyawan, 0, 2)) }}</div>';">
                        @else
                            <div class="rounded-3 border border-1 border-slate-200 d-flex align-items-center justify-content-center fw-bold font-mono text-white"
                                style="width: 68px; height: 68px; font-size: 22px; background: var(--theme-color-1, #3C2A21);">
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
                                <span class="badge bg-label-success">
                                    <i class="ti ti-check me-0.5"></i> Aktif
                                </span>
                            @else
                                <span class="badge bg-label-danger">
                                    <i class="ti ti-x me-0.5"></i> Nonaktif
                                </span>
                            @endif
                            <span class="badge bg-label-primary font-monospace">{{ $karyawan->employment_type ?? 'PKWT' }}</span>
                        </div>

                        <div class="d-flex align-items-center gap-1.5 flex-wrap text-muted" style="font-size: 12px; line-height: 1.4;">
                            <span class="fw-semibold text-dark">{{ $karyawan->nama_jabatan ?? 'Staf' }}</span>
                            <span>•</span>
                            <span>{{ $karyawan->nama_dept ?? 'Umum' }}</span>
                            @if(!empty($karyawan->nama_divisi))
                                <span>•</span>
                                <span class="badge bg-label-secondary font-monospace">{{ $karyawan->nama_divisi }}</span>
                            @endif
                            <span>•</span>
                            <span class="d-inline-flex align-items-center gap-1 text-primary">
                                <i class="ti ti-map-pin" style="font-size: 12px;"></i> {{ $karyawan->nama_cabang ?? 'Pusat' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Quick Stat Bar -->
            <div class="col-12 col-lg-5">
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div class="p-2 rounded-2 h-100 d-flex flex-column justify-content-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <small class="text-muted d-block text-truncate" style="font-size: 9.5px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">Tanggal Bergabung</small>
                            <span class="fw-bold text-dark font-mono text-truncate" style="font-size: 11.5px;">
                                {{ !empty($karyawan->tanggal_masuk) ? date('d/m/Y', strtotime($karyawan->tanggal_masuk)) : '-' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-2 h-100 d-flex flex-column justify-content-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <small class="text-muted d-block text-truncate" style="font-size: 9.5px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">Akun Mobile ESS</small>
                            <span class="fw-bold text-dark font-mono text-truncate" style="font-size: 11.5px;">
                                {{ (!empty($user) || !empty($karyawan->id_user)) ? 'Terhubung' : 'Belum Ada' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons Toolbar -->
        <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap pt-3 mt-3 border-top" style="border-color: #F1F5F9 !important;">
            @can('karyawan.edit')
                <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 btnEdit" nik="{{ Crypt::encrypt($karyawan->nik) }}">
                    <i class="ti ti-edit"></i> <span>Edit Data</span>
                </button>
            @endcan
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="nav-align-top mb-4">
    <ul class="nav nav-pills gap-1 p-1.5 rounded-3 mb-3" id="karyawanDetailTabs" role="tablist" 
        style="background: #FFFFFF !important; border: 1px solid #E2E8F0;">
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-btn active py-2 px-3 d-flex align-items-center gap-1.5" id="pills-profile-tab" data-tab="#tab-profile" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-user fs-6"></i>
                <span class="fw-semibold">Data Pribadi & Kontak</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-btn py-2 px-3 d-flex align-items-center gap-1.5" id="pills-org-tab" data-tab="#tab-org" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-briefcase fs-6"></i>
                <span class="fw-semibold">Organisasi & Kepegawaian</span>
            </button>
        </li>
        @if (module_enabled('payroll'))
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-btn py-2 px-3 d-flex align-items-center gap-1.5" id="pills-finance-tab" data-tab="#tab-finance" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-building-bank fs-6"></i>
                <span class="fw-semibold">Payroll & Pajak (BPJS)</span>
            </button>
        </li>
        @endif
        @if (module_enabled('face_recognition'))
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-btn py-2 px-3 d-flex align-items-center gap-1.5" id="pills-face-tab" data-tab="#tab-face" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-face-id fs-6"></i>
                <span class="fw-semibold">Biometrik Wajah</span>
                <span class="badge {{ $karyawan_wajah->count() > 0 ? 'bg-label-success' : 'bg-label-warning' }} ms-1 font-mono" style="font-size: 10px; padding: 2px 6px;">
                    {{ $karyawan_wajah->count() }}
                </span>
            </button>
        </li>
        @endif
        @if (module_enabled('contracts') || module_enabled('movements') || module_enabled('resignation'))
        <li class="nav-item" role="presentation">
            <button class="nav-link tab-btn py-2 px-3 d-flex align-items-center gap-1.5" id="pills-career-tab" data-tab="#tab-career" type="button" style="font-size: 12px; border-radius: 8px;">
                <i class="ti ti-file-certificate fs-6"></i>
                <span class="fw-semibold">Kontrak & Riwayat Karir</span>
                @if(isset($kontraks) && $kontraks->count() > 0)
                    <span class="badge bg-label-info ms-1 font-mono" style="font-size: 10px; padding: 2px 6px;">
                        {{ $kontraks->count() }}
                    </span>
                @endif
            </button>
        </li>
        @endif
    </ul>

    <div class="tab-content p-0 border-0 shadow-none bg-transparent" id="karyawanDetailTabContent">
        <!-- TAB 1: DATA PRIBADI & KONTAK -->
        <div class="tab-pane active" id="tab-profile" role="tabpanel">
            <div class="row g-3">
                <div class="col-lg-6 col-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-2.5 px-3 border-bottom d-flex align-items-center gap-2">
                            <i class="ti ti-id-badge text-primary"></i>
                            <h6 class="mb-0 fw-bold text-dark">Data Identitas Pribadi</h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 40%;">NIK / ID Karyawan</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ $karyawan->nik_show ?? $karyawan->nik }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Nomor KTP / NIK</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">
                                            @can('employee.sensitive_data')
                                                {{ $karyawan->no_ktp ?? '-' }}
                                            @else
                                                {{ $karyawan->masked_no_ktp ?? '-' }}
                                            @endcan
                                        </td>
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
                                        <td class="text-muted ps-0 py-2">Agama</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->religion ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Kewarganegaraan</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->nationality ?? 'WNI' }}</td>
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

                <div class="col-lg-6 col-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-2.5 px-3 border-bottom d-flex align-items-center gap-2">
                            <i class="ti ti-phone-call text-primary"></i>
                            <h6 class="mb-0 fw-bold text-dark">Kontak Pribadi & Darurat</h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 40%;">No. HP / WhatsApp</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ $karyawan->no_hp ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Email Pribadi</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->personal_email ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Email Kantor / SSO</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->company_email ?? ($karyawan->email ?? '-') }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Nama Kontak Darurat</td>
                                        <td class="fw-bold text-dark pe-0 py-2">{{ $karyawan->kontak_darurat ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Hubungan Kontak Darurat</td>
                                        <td class="pe-0 py-2">
                                            @if($karyawan->hubungan_kontak_darurat)
                                                <span class="badge bg-label-warning">{{ $karyawan->hubungan_kontak_darurat }}</span>
                                            @else
                                                <span class="text-muted">-</span>
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

        <!-- TAB 2: ORGANISASI & KEPEGAWAIAN -->
        <div class="tab-pane" id="tab-org" role="tabpanel" style="display: none;">
            <div class="row g-3">
                <div class="col-lg-6 col-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-2.5 px-3 border-bottom d-flex align-items-center gap-2">
                            <i class="ti ti-sitemap text-primary"></i>
                            <h6 class="mb-0 fw-bold text-dark">Penempatan & Struktur</h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 40%;">Kantor Cabang / Lokasi</td>
                                        <td class="fw-bold text-dark pe-0 py-2">{{ $karyawan->nama_cabang ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Departemen</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->nama_dept ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Divisi / Regu Kerja</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">
                                            @if($karyawan->nama_divisi)
                                                <span class="badge bg-label-primary">{{ $karyawan->nama_divisi }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Jabatan</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->nama_jabatan ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Grade / Level</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->grade_level ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Atasan Langsung</td>
                                        <td class="fw-bold text-dark pe-0 py-2">
                                            @if($karyawan->nama_supervisor)
                                                <span class="d-inline-flex align-items-center gap-1 text-primary">
                                                    <i class="ti ti-user-star"></i> {{ $karyawan->nama_supervisor }}
                                                </span>
                                            @else
                                                <span class="text-muted">- (Top Level)</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-2.5 px-3 border-bottom d-flex align-items-center gap-2">
                            <i class="ti ti-calendar-event text-primary"></i>
                            <h6 class="mb-0 fw-bold text-dark">Status Hubungan & Shift Kerja</h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 40%;">Tipe Hubungan Kerja</td>
                                        <td class="pe-0 py-2">
                                            <span class="badge bg-label-info font-monospace">{{ $karyawan->employment_type ?? 'PKWT' }}</span>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Shift Kerja Default</td>
                                        <td class="fw-bold font-mono text-primary pe-0 py-2">
                                            {{ $karyawan->nama_jam_kerja ?? 'Shift Pagi (07:00)' }}
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Tanggal Bergabung</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ !empty($karyawan->tanggal_masuk) ? date('d/m/Y', strtotime($karyawan->tanggal_masuk)) : '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Masa Kerja</td>
                                        <td class="fw-bold text-dark pe-0 py-2">
                                            @if(!empty($karyawan->tanggal_masuk))
                                                {{ \Carbon\Carbon::parse($karyawan->tanggal_masuk)->diffForHumans(null, true) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Status Akun Mobile</td>
                                        <td class="pe-0 py-2">
                                            @if ($user)
                                                <span class="badge bg-label-success font-mono">Aktif ({{ $user->username }})</span>
                                            @else
                                                <span class="badge bg-label-secondary font-mono">Belum Dibuatkan Akun</span>
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

        <!-- TAB 3: PAYROLL, PAJAK & BPJS -->
        @if (module_enabled('payroll'))
        <div class="tab-pane" id="tab-finance" role="tabpanel" style="display: none;">
            <div class="row g-3">
                <div class="col-lg-6 col-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-2.5 px-3 border-bottom d-flex align-items-center gap-2">
                            <i class="ti ti-credit-card text-primary"></i>
                            <h6 class="mb-0 fw-bold text-dark">Rekening Bank Gaji</h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 40%;">Nama Bank</td>
                                        <td class="fw-bold text-dark pe-0 py-2">{{ $karyawan->nama_bank ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">Nomor Rekening</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">
                                            @can('employee.sensitive_data')
                                                {{ $karyawan->no_rekening ?? '-' }}
                                            @else
                                                {{ $karyawan->masked_no_rekening ?? '-' }}
                                            @endcan
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Nama Pemilik Rekening</td>
                                        <td class="fw-semibold text-dark pe-0 py-2">{{ $karyawan->nama_rekening ?? ($karyawan->nama_karyawan) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-2.5 px-3 border-bottom d-flex align-items-center gap-2">
                            <i class="ti ti-receipt-tax text-primary"></i>
                            <h6 class="mb-0 fw-bold text-dark">Data Pajak (PPh 21) & BPJS</h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 13px;">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2" style="width: 40%;">Nomor NPWP</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">
                                            @can('employee.sensitive_data')
                                                {{ $karyawan->npwp_number ?? '-' }}
                                            @else
                                                {{ $karyawan->masked_npwp ?? '-' }}
                                            @endcan
                                        </td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-muted ps-0 py-2">BPJS Kesehatan</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ $karyawan->bpjs_kesehatan_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">BPJS Ketenagakerjaan (KPJ)</td>
                                        <td class="fw-bold font-mono text-dark pe-0 py-2">{{ $karyawan->bpjs_ketenagakerjaan_number ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>
        @endif

        <!-- TAB 4: BIOMETRIK WAJAH -->
        @if (module_enabled('face_recognition'))
        <div class="tab-pane" id="tab-face" role="tabpanel" style="display: none;">
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
                                <button type="submit" class="delete-confirm btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1.5" data-level="2" data-label="Semua Dataset Wajah" style="font-size: 12px; padding: 6px 12px; border-radius: 6px; background: #FFFFFF;">
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
                                        <img src="{{ route('file.face', ['folder' => $karyawan->nik . '-' . getNamaDepan(strtolower($karyawan->nama_karyawan)), 'filename' => $wajah->wajah]) }}" 
                                            class="card-img-top" style="height: 120px; object-fit: cover;" alt="Wajah">
                                        <div class="card-body p-2 text-center">
                                            <form action="{{ route('facerecognition.delete', $wajah->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-confirm btn btn-sm btn-outline-danger" data-label="Foto Wajah">
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
        </div>
        @endif

        @if (module_enabled('contracts') || module_enabled('movements') || module_enabled('resignation'))
        <!-- TAB 5: KONTRAK & RIWAYAT KARIR -->
        <div class="tab-pane" id="tab-career" role="tabpanel" style="display: none;">
            <!-- Resignation Notice if nonaktif -->
            @if(isset($resignations) && $resignations->count() > 0)
                @php $res = $resignations->first(); @endphp
                <div class="alert alert-danger mb-3 p-3 border-0 rounded-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-user-x fs-2"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Riwayat Berhenti / Offboarding Karyawan</h6>
                                <div class="small">Kategori: <strong>{{ $res->kategori_keluar_label }}</strong> • Efektif Keluar: <strong>{{ $res->tanggal_keluar ? $res->tanggal_keluar->format('d F Y') : '-' }}</strong></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            {!! $res->clearance_badge_html !!}
                            @if($res->dokumen)
                                <a href="{{ asset('storage/' . $res->dokumen) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                    <i class="ti ti-file-text me-1"></i>Berkas Resign
                                </a>
                            @endif
                        </div>
                    </div>
                    @if($res->alasan)
                        <div class="mt-2 small text-danger-emphasis border-top pt-2">
                            <strong>Alasan:</strong> {{ $res->alasan }}
                        </div>
                    @endif
                </div>
            @endif

            <div class="row g-3">
                <!-- Left: Active Contract Card -->
                <div class="col-12 col-lg-5">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-file-certificate text-primary"></i>
                                <h6 class="mb-0 fw-bold text-dark">Kontrak Kerja Aktif</h6>
                            </div>
                            @can('kontrak.create')
                                <a href="{{ route('kontrak.create') }}?nik={{ $karyawan->nik }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-plus me-1"></i>Kontrak Baru
                                </a>
                            @endcan
                        </div>
                        <div class="card-body p-3">
                            @if(isset($activeKontrak) && $activeKontrak)
                                <div class="p-3 bg-light rounded-3 mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-label-primary font-mono fw-bold">{{ $activeKontrak->jenis_kontrak }}</span>
                                        {!! $activeKontrak->status_badge_html !!}
                                    </div>
                                    <h5 class="mb-1 fw-bold text-dark">{{ $activeKontrak->no_kontrak }}</h5>
                                    <div class="text-muted small mb-2">{{ $activeKontrak->jabatan ?: ($karyawan->nama_jabatan ?? 'Jabatan Tidak Disebutkan') }}</div>
                                    <div class="d-flex justify-content-between small text-muted border-top pt-2">
                                        <span>Masa Berlaku:</span>
                                        <span class="fw-semibold text-dark">
                                            {{ $activeKontrak->tanggal_mulai ? $activeKontrak->tanggal_mulai->format('d/m/Y') : '-' }} s/d 
                                            {{ $activeKontrak->tanggal_selesai ? $activeKontrak->tanggal_selesai->format('d/m/Y') : 'Selamanya (Tetap)' }}
                                        </span>
                                    </div>
                                    @if($activeKontrak->tanggal_selesai)
                                        @php $rem = $activeKontrak->days_remaining; @endphp
                                        <div class="d-flex justify-content-between small mt-1 {{ $rem <= 30 ? 'text-warning fw-bold' : 'text-muted' }}">
                                            <span>Sisa Masa Kontrak:</span>
                                            <span>{{ $rem > 0 ? $rem . ' hari lagi' : ($rem === 0 ? 'Hari Ini Berakhir' : 'Lewat ' . abs($rem) . ' hari') }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    @if($activeKontrak->dokumen)
                                        <a href="{{ asset('storage/' . $activeKontrak->dokumen) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="ti ti-file-download me-1"></i>Unduh Berkas Kontrak
                                        </a>
                                    @else
                                        <span class="text-muted small"><i class="ti ti-file-off me-1"></i>Belum ada berkas</span>
                                    @endif
                                    <a href="{{ route('kontrak.edit', Crypt::encrypt($activeKontrak->id)) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="ti ti-edit me-1"></i>Kelola
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="ti ti-file-alert fs-1 d-block mb-2 text-muted" style="opacity: 0.4;"></i>
                                    <div class="fw-semibold mb-1">Belum Ada Kontrak Aktif</div>
                                    <p class="small text-muted mb-3">Karyawan ini belum memiliki dokumen kontrak terdaftar.</p>
                                    @can('kontrak.create')
                                        <a href="{{ route('kontrak.create') }}?nik={{ $karyawan->nik }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-plus me-1"></i>Terbitkan Kontrak Sekarang
                                        </a>
                                    @endcan
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Career Movement History -->
                <div class="col-12 col-lg-7">
                    <div class="card h-100 border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-arrows-transfer-up text-primary"></i>
                                <h6 class="mb-0 fw-bold text-dark">Riwayat Mutasi & Perubahan Karir</h6>
                            </div>
                            @can('movement.create')
                                <a href="{{ route('movement.create') }}?nik={{ $karyawan->nik }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-plus me-1"></i>Catat Mutasi
                                </a>
                            @endcan
                        </div>
                        <div class="card-body p-3">
                            @if(isset($movements) && $movements->count() > 0)
                                <div class="timeline timeline-simple">
                                    @foreach($movements as $m)
                                        <div class="timeline-event mb-3 pb-3 border-bottom">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <div class="d-flex align-items-center gap-1.5">
                                                    <span class="badge bg-label-info font-mono fw-bold">{{ $m->movement_type_label }}</span>
                                                    {!! $m->status_badge_html !!}
                                                </div>
                                                <span class="text-muted small">{{ $m->effective_date ? $m->effective_date->format('d/m/Y') : '-' }}</span>
                                            </div>
                                            @if($m->no_sk)
                                                <div class="small fw-semibold text-primary mb-1">SK: {{ $m->no_sk }}</div>
                                            @endif
                                            @if(!empty($m->new_values))
                                                <div class="small text-muted mb-1">
                                                    @foreach($m->new_values as $k => $val)
                                                        @php $oldV = $m->old_values[$k] ?? '-'; @endphp
                                                        <div>
                                                            <span class="text-muted">{{ ucwords(str_replace('_', ' ', $k)) }}:</span>
                                                            <span class="text-secondary text-decoration-line-through">{{ $oldV ?: '(awal)' }}</span>
                                                            <i class="ti ti-arrow-right text-primary mx-0.5"></i>
                                                            <span class="fw-bold text-dark">{{ $val }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if($m->reason)
                                                <div class="small text-muted fst-italic">"{{ $m->reason }}"</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="ti ti-history-off fs-1 d-block mb-2 text-muted" style="opacity: 0.4;"></i>
                                    <div class="fw-semibold mb-1">Belum Ada Riwayat Mutasi</div>
                                    <p class="small text-muted mb-0">Posisi dan penugasan karyawan masih sesuai data awal bergabung.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Bottom: All Contracts History Table -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border: 1px solid #E2E8F0 !important; border-radius: 12px; background: #FFFFFF !important;">
                        <div class="card-header py-2.5 px-3 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark"><i class="ti ti-history me-1 text-primary"></i>Riwayat Seluruh Dokumen Kontrak</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 12.5px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Kontrak</th>
                                        <th>Jenis</th>
                                        <th>Mulai</th>
                                        <th>Selesai</th>
                                        <th>Status</th>
                                        <th>Dokumen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($kontraks) && $kontraks->count() > 0)
                                        @foreach($kontraks as $kt)
                                            <tr>
                                                <td class="fw-bold text-dark font-mono">{{ $kt->no_kontrak }}</td>
                                                <td><span class="badge bg-label-primary font-mono">{{ $kt->jenis_kontrak }}</span></td>
                                                <td>{{ $kt->tanggal_mulai ? $kt->tanggal_mulai->format('d/m/Y') : '-' }}</td>
                                                <td>{{ $kt->tanggal_selesai ? $kt->tanggal_selesai->format('d/m/Y') : 'Selamanya' }}</td>
                                                <td>{!! $kt->status_badge_html !!}</td>
                                                <td>
                                                    @if($kt->dokumen)
                                                        <a href="{{ asset('storage/' . $kt->dokumen) }}" target="_blank" class="text-primary fw-semibold">
                                                            <i class="ti ti-paperclip me-0.5"></i>Lihat PDF
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">Belum ada riwayat dokumen kontrak</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
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
    });
</script>
@endpush
