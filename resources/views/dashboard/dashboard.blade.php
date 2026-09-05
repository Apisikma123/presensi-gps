@extends('layouts.app')
@section('titlepage', 'Dashboard Presensi')

@push('mystyle')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
<style>
    /* Dashboard Clean Operational Architecture */
    .dashboard-header-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 1.15rem 1.5rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }

    .digital-clock-chip {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        padding: 0.45rem 0.85rem;
        border-radius: 8px;
        font-family: 'JetBrains Mono', 'Geist Mono', monospace;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #0F172A;
        font-weight: 700;
        font-size: 0.95rem;
    }

    /* 4 Priority Stat Cards Grid */
    .stat-priority-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }

    @media (max-width: 1199.98px) {
        .stat-priority-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 575.98px) {
        .stat-priority-grid {
            grid-template-columns: 1fr;
        }
    }

    .stat-card-priority {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 125px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .stat-card-priority:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px -2px rgba(15, 23, 42, 0.06);
    }

    .stat-card-priority .stat-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748B;
        margin-bottom: 4px;
    }

    .stat-card-priority .stat-value {
        font-size: 2.15rem;
        font-weight: 800;
        font-family: 'JetBrains Mono', 'Geist Mono', monospace;
        color: #0F172A;
        line-height: 1.1;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .stat-card-priority .stat-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-card-priority .stat-subtext {
        font-size: 12px;
        color: #64748B;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Sub-metrics Operational Strip */
    .operational-sub-strip {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 0.85rem 1.25rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
    }

    .sub-metric-pill {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        transition: all 0.15s ease;
    }

    .sub-metric-pill:hover {
        background: #F1F5F9;
    }

    .sub-metric-pill .sub-metric-value {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        font-size: 1rem;
        line-height: 1;
    }

    .sub-metric-pill .sub-metric-label {
        font-size: 11.5px;
        color: #475569;
        font-weight: 500;
        line-height: 1.2;
    }

    /* Chart Cards */
    .dashboard-chart-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .dashboard-chart-card .card-header-clean {
        padding: 1.15rem 1.25rem 0.5rem 1.25rem;
        border-bottom: none;
        background: transparent;
    }

    .dashboard-chart-card .card-body-chart {
        padding: 0 0.75rem 1rem 0.75rem;
        flex-grow: 1;
        min-height: 290px;
    }

    /* Feed & List Cards */
    .operational-feed-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }

    .operational-feed-card .feed-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #E2E8F0;
        background: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
</style>
@endpush

@section('content')

@php
    $authUser = auth()->user();
    $fullName = $authUser->name ?? 'Pengguna';
    $userName = explode(' ', $fullName)[0];
    $currentHour = (int) date('H');

    if ($currentHour >= 5 && $currentHour < 12) {
        $greeting = 'Selamat Pagi';
    } elseif ($currentHour >= 12 && $currentHour < 15) {
        $greeting = 'Selamat Siang';
    } elseif ($currentHour >= 15 && $currentHour < 19) {
        $greeting = 'Selamat Sore';
    } else {
        $greeting = 'Selamat Malam';
    }

    $tanggalDisplay = \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y');
@endphp

<!-- Header Banner -->
<div class="dashboard-header-card mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                style="background: rgba(30, 77, 62, 0.08); color: #1E4D3E; width: 44px; height: 44px; border: 1px solid rgba(30, 77, 62, 0.15);">
                <i class="ti ti-layout-dashboard fs-3"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">{{ $greeting }}, {{ $userName }}</h5>
                <small class="text-muted d-flex align-items-center gap-1 mt-0.5" style="font-size: 12.5px;">
                    <i class="ti ti-calendar text-muted"></i>
                    <span class="fw-semibold text-dark">{{ $tanggalDisplay }}</span>
                    <span class="text-muted mx-1">•</span>
                    <span>Ringkasan Kondisi & Monitoring Kehadiran Karyawan</span>
                </small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="digital-clock-chip" id="digital-clock">
                <span id="clock-icon" class="text-muted"><i class="ti ti-clock"></i></span>
                <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
                <span class="badge bg-light text-muted px-1.5 py-0.5 rounded font-mono" style="font-size: 10px;">WIB</span>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar (Clean & Aligned) -->
<div class="admin-filter-toolbar mb-3">
    <form action="{{ route('dashboard.index') }}" method="GET">
        <div class="row g-2 align-items-center">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="ti ti-calendar"></i></span>
                    <input type="text" name="tanggal" value="{{ $tanggal }}" class="form-control flatpickr-date" placeholder="Pilih Tanggal">
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <select name="kode_cabang" class="form-select">
                    <option value="">Semua Outlet / Cabang</option>
                    @foreach ($cabang as $c)
                        <option value="{{ $c->kode_cabang }}" {{ $selectedCabang == $c->kode_cabang ? 'selected' : '' }}>
                            {{ textUpperCase($c->nama_cabang) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <select name="kode_dept" class="form-select">
                    <option value="">Semua Departemen</option>
                    @foreach ($departemen as $d)
                        <option value="{{ $d->kode_dept }}" {{ $selectedDept == $d->kode_dept ? 'selected' : '' }}>
                            {{ textUpperCase($d->nama_dept) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-6 col-12 d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1.5" style="height: 38px;">
                    <i class="ti ti-filter"></i>
                    <span>Terapkan Filter</span>
                </button>
                @if($selectedCabang || $selectedDept || $tanggal != date('Y-m-d'))
                    <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center px-2.5" style="height: 38px;" title="Reset Filter">
                        <i class="ti ti-refresh"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Pending Approval Banner (Alert if pending items exist) -->
@if ($pending_approval > 0)
    <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 p-3 rounded-3 border" style="background: #FFFBEB; border-color: #FDE68A !important;">
        <div class="d-flex align-items-center gap-2.5">
            <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-dark flex-shrink-0" style="width: 36px; height: 36px;">
                <i class="ti ti-bell-ringing fs-4"></i>
            </div>
            <div>
                <strong class="text-dark" style="font-size: 13.5px;">Terdapat {{ $pending_approval }} Pengajuan Menunggu Persetujuan</strong>
                <div class="text-muted" style="font-size: 12px;">Permohonan Izin Absen, Sakit, Cuti, atau Dispensasi membutuhkan konfirmasi Admin.</div>
            </div>
        </div>
        <a href="{{ route('izinabsen.index') }}" class="btn btn-sm btn-warning text-dark fw-bold d-inline-flex align-items-center gap-1">
            <span>Buka Persetujuan</span>
            <i class="ti ti-arrow-right"></i>
        </a>
    </div>
@endif

<!-- 4 Priority Statistic Cards -->
<div class="stat-priority-grid mb-3">
    <!-- 1. Total Karyawan -->
    <div class="stat-card-priority">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="stat-label">Total Karyawan</div>
                <h3 class="stat-value">{{ $total_karyawan }}</h3>
            </div>
            <div class="stat-icon-wrapper" style="background: #F1F5F9; color: #334155;">
                <i class="ti ti-users"></i>
            </div>
        </div>
        <div class="stat-subtext">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: #334155;"></span>
            <span>Karyawan aktif operasional</span>
        </div>
    </div>

    <!-- 2. Hadir Tepat Waktu -->
    <div class="stat-card-priority stat-card-clickable" style="cursor: pointer;" data-status="h" title="Klik untuk lihat rincian">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="stat-label">Hadir Hari Ini</div>
                <h3 class="stat-value" style="color: #059669;">{{ $hadir_hari_ini }}</h3>
            </div>
            <div class="stat-icon-wrapper" style="background: #ECFDF5; color: #059669;">
                <i class="ti ti-user-check"></i>
            </div>
        </div>
        <div class="stat-subtext">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
            <span>Tepat waktu &le; 07:05 / Dispensasi</span>
        </div>
    </div>

    <!-- 3. Telat Hari Ini -->
    <div class="stat-card-priority">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="stat-label">Telat Hari Ini</div>
                <h3 class="stat-value" style="color: #D97706;">{{ $telat_hari_ini }}</h3>
            </div>
            <div class="stat-icon-wrapper" style="background: #FFFBEB; color: #D97706;">
                <i class="ti ti-clock-exclamation"></i>
            </div>
        </div>
        <div class="stat-subtext">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706;"></span>
            <span>Masuk melebihi jam toleransi</span>
        </div>
    </div>

    <!-- 4. Pengajuan Pending -->
    <div class="stat-card-priority" style="cursor: pointer;" onclick="window.location='{{ route('izinabsen.index') }}'">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="stat-label">Pengajuan Pending</div>
                <h3 class="stat-value" style="color: {{ $pending_approval > 0 ? '#DC2626' : '#64748B' }};">{{ $pending_approval }}</h3>
            </div>
            <div class="stat-icon-wrapper" style="background: {{ $pending_approval > 0 ? '#FEF2F2' : '#F8FAFC' }}; color: {{ $pending_approval > 0 ? '#DC2626' : '#64748B' }};">
                <i class="ti ti-file-certificate"></i>
            </div>
        </div>
        <div class="stat-subtext">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $pending_approval > 0 ? '#DC2626' : '#64748B' }};"></span>
            <span>Izin, sakit, cuti, dispensasi</span>
        </div>
    </div>
</div>

<!-- Sub-metrics Operational Strip (Izin, Sakit, Cuti, Alpa, Dispensasi) -->
<div class="operational-sub-strip mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted fw-bold small text-uppercase" style="font-size: 11px; letter-spacing: 0.05em;">Rincian Status:</span>
            
            <div class="sub-metric-pill stat-card-clickable" style="cursor: pointer;" data-status="i" title="Klik untuk lihat daftar izin">
                <div class="sub-metric-value" style="color: #2563EB;">{{ $izin_hari_ini }}</div>
                <div class="sub-metric-label">Izin Absen</div>
            </div>

            <div class="sub-metric-pill stat-card-clickable" style="cursor: pointer;" data-status="s" title="Klik untuk lihat daftar sakit">
                <div class="sub-metric-value" style="color: #EA580C;">{{ $sakit_hari_ini }}</div>
                <div class="sub-metric-label">Izin Sakit</div>
            </div>

            <div class="sub-metric-pill stat-card-clickable" style="cursor: pointer;" data-status="c" title="Klik untuk lihat daftar cuti">
                <div class="sub-metric-value" style="color: #9333EA;">{{ $cuti_hari_ini }}</div>
                <div class="sub-metric-label">Cuti</div>
            </div>

            <div class="sub-metric-pill">
                <div class="sub-metric-value text-danger">{{ $tidak_hadir }}</div>
                <div class="sub-metric-label">Tidak Hadir (Alpa)</div>
            </div>

            <div class="sub-metric-pill">
                <div class="sub-metric-value" style="color: #0284C7;">{{ $dispensasi_hari_ini }}</div>
                <div class="sub-metric-label">Dispensasi Disetujui</div>
            </div>
        </div>

        <small class="text-muted font-mono" style="font-size: 11.5px;">
            <i class="ti ti-info-circle me-1"></i>Data per {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
        </small>
    </div>
</div>

<!-- Exactly 4 Meaningful Chart Sections (2-Column Desktop Grid) -->
<div class="row g-3 mb-4">
    <!-- CHART 1: Tren Kehadiran (Line Chart) -->
    <div class="col-lg-6 col-12">
        <div class="dashboard-chart-card">
            <div class="card-header-clean d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">Tren Kehadiran</h6>
                    <small class="text-muted" style="font-size: 12px;">Perkembangan hadir tepat waktu vs terlambat 7 hari terakhir</small>
                </div>
                <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">7 Hari</span>
            </div>
            <div class="card-body-chart">
                <div id="chartTrenKehadiran" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- CHART 2: Status Kehadiran Hari Ini (Donut Chart) -->
    <div class="col-lg-6 col-12">
        <div class="dashboard-chart-card">
            <div class="card-header-clean d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">Status Kehadiran Hari Ini</h6>
                    <small class="text-muted" style="font-size: 12px;">Komposisi status absensi seluruh karyawan</small>
                </div>
                <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">Hari Ini</span>
            </div>
            <div class="card-body-chart d-flex align-items-center justify-content-center">
                <div id="chartStatusHariIni" style="width: 100%; min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- CHART 3: Kehadiran per Shift (Grouped Bar Chart) -->
    <div class="col-lg-6 col-12">
        <div class="dashboard-chart-card">
            <div class="card-header-clean d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">Kehadiran per Shift</h6>
                    <small class="text-muted" style="font-size: 12px;">Perbandingan hadir vs telat berdasarkan jadwal shift</small>
                </div>
                <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">Shift</span>
            </div>
            <div class="card-body-chart">
                <div id="chartKehadiranShift" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- CHART 4: Izin, Sakit & Cuti (Bar / Area Chart) -->
    <div class="col-lg-6 col-12">
        <div class="dashboard-chart-card">
            <div class="card-header-clean d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">Izin, Sakit & Cuti</h6>
                    <small class="text-muted" style="font-size: 12px;">Tren permohonan ketidakhadiran karyawan 7 hari terakhir</small>
                </div>
                <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">Permohonan</span>
            </div>
            <div class="card-body-chart">
                <div id="chartIzinSakitCuti" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- 2 Operational Feeds: Pengajuan Terbaru & Aktivitas Kehadiran Terbaru -->
<div class="row g-3 mb-4">
    <!-- Feed 1: Pengajuan Terbaru -->
    <div class="col-lg-6 col-12">
        <div class="operational-feed-card h-100">
            <div class="feed-header">
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 13.5px;">Pengajuan Terbaru</h6>
                    <small class="text-muted" style="font-size: 11.5px;">Permohonan izin karyawan yang menunggu tindakan</small>
                </div>
                <a href="{{ route('izinabsen.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-2.5" style="font-size: 12px;">
                    Lihat Semua <i class="ti ti-chevron-right ms-0.5"></i>
                </a>
            </div>
            <div class="p-0">
                @if($pengajuanTerbaru->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                            <thead style="background: #F8FAFC;">
                                <tr>
                                    <th class="py-2 px-3 text-muted text-uppercase" style="font-size: 10.5px;">Karyawan</th>
                                    <th class="py-2 px-3 text-muted text-uppercase" style="font-size: 10.5px;">Jenis</th>
                                    <th class="py-2 px-3 text-muted text-uppercase" style="font-size: 10.5px;">Tanggal</th>
                                    <th class="py-2 px-3 text-end text-muted text-uppercase" style="font-size: 10.5px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengajuanTerbaru as $p)
                                    <tr>
                                        <td class="py-2.5 px-3">
                                            <div class="fw-semibold text-dark">{{ $p->nama_karyawan }}</div>
                                            <small class="text-muted text-truncate d-inline-block" style="max-width: 170px;">{{ $p->keterangan ?? '-' }}</small>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            @if($p->kode_tipe == 'i')
                                                <span class="badge-status badge-status-izin">{{ $p->tipe }}</span>
                                            @elseif($p->kode_tipe == 's')
                                                <span class="badge-status badge-status-sakit">{{ $p->tipe }}</span>
                                            @elseif($p->kode_tipe == 'c')
                                                <span class="badge-status badge-status-cuti">{{ $p->tipe }}</span>
                                            @else
                                                <span class="badge-status badge-status-dispensasi">{{ $p->tipe }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 font-mono text-muted" style="font-size: 11.5px;">
                                            {{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-2.5 px-3 text-end">
                                            <a href="{{ route('izinabsen.index') }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 11.5px;">
                                                Proses
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-checkbox text-muted mb-2 d-block" style="font-size: 2.2rem; opacity: 0.35;"></i>
                        <div class="fw-semibold text-dark" style="font-size: 13px;">Semua Permohonan Selesai</div>
                        <small class="text-muted">Tidak ada pengajuan pending yang memerlukan persetujuan saat ini.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Feed 2: Aktivitas Presensi Hari Ini -->
    <div class="col-lg-6 col-12">
        <div class="operational-feed-card h-100">
            <div class="feed-header">
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 13.5px;">Presensi Hari Ini</h6>
                    <small class="text-muted" style="font-size: 11.5px;">Presensi jam masuk terbaru karyawan hari ini</small>
                </div>
                <a href="{{ route('presensi.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-2.5" style="font-size: 12px;">
                    Monitoring Lengkap <i class="ti ti-chevron-right ms-0.5"></i>
                </a>
            </div>
            <div class="p-0">
                @if($aktivitasTerbaru->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                            <thead style="background: #F8FAFC;">
                                <tr>
                                    <th class="py-2 px-3 text-muted text-uppercase" style="font-size: 10.5px;">Karyawan</th>
                                    <th class="py-2 px-3 text-muted text-uppercase" style="font-size: 10.5px;">Shift / Cabang</th>
                                    <th class="py-2 px-3 text-muted text-uppercase" style="font-size: 10.5px;">Jam Masuk</th>
                                    <th class="py-2 px-3 text-end text-muted text-uppercase" style="font-size: 10.5px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($aktivitasTerbaru as $a)
                                    @php
                                        $batas = $a->batas_toleransi ? date('H:i:s', strtotime($a->batas_toleransi)) : '07:05:00';
                                        $jamMasuk = date('H:i:s', strtotime($a->jam_in));
                                        $isTepatWaktu = ($a->is_dispensasi == 1) || ($jamMasuk <= $batas);
                                    @endphp
                                    <tr>
                                        <td class="py-2.5 px-3">
                                            <div class="d-flex align-items-center gap-2">
                                                @if (!empty($a->foto))
                                                    <img src="{{ getfotoKaryawan($a->foto) }}" alt="Avatar" class="rounded-circle flex-shrink-0" style="width: 28px; height: 28px; object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-muted fw-bold flex-shrink-0" style="width: 28px; height: 28px; font-size: 11px;">
                                                        {{ strtoupper(substr($a->nama_karyawan, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="overflow-hidden">
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 140px;">{{ $a->nama_karyawan }}</div>
                                                    <small class="text-muted font-mono" style="font-size: 10.5px;">{{ $a->nik_show }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <span class="d-block text-dark fw-medium" style="font-size: 12px;">{{ $a->nama_jam_kerja }}</span>
                                            <small class="text-muted" style="font-size: 11px;">{{ $a->nama_cabang }}</small>
                                        </td>
                                        <td class="py-2.5 px-3 font-mono fw-bold" style="font-size: 12px; color: #0F172A;">
                                            {{ date('H:i:s', strtotime($a->jam_in)) }}
                                        </td>
                                        <td class="py-2.5 px-3 text-end">
                                            @if($isTepatWaktu)
                                                <span class="badge-status badge-status-hadir">Hadir Tepat</span>
                                            @else
                                                <span class="badge-status badge-status-telat">Terlambat</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-clock-off text-muted mb-2 d-block" style="font-size: 2.2rem; opacity: 0.35;"></i>
                        <div class="fw-semibold text-dark" style="font-size: 13px;">Belum Ada Presensi Masuk</div>
                        <small class="text-muted">Aktivitas presensi hari ini akan otomatis tercatat di sini.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Karyawan (Klik Card) -->
<x-modal-form id="modalKaryawanList" size="modal-lg" title="Daftar Karyawan" />

@endsection

@push('myscript')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
    (function() {
        // 1. Live Digital Clock
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();

            hours = hours < 10 ? "0" + hours : hours;
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            $('#hours').text(hours);
            $('#minutes').text(minutes);
            $('#seconds').text(seconds);
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 2. Click Stat Cards to View Modal
        $('.stat-card-clickable').click(function() {
            const status = $(this).data('status');
            const tanggal = "{{ $tanggal }}";
            const kode_cabang = "{{ $selectedCabang }}";
            const kode_dept = "{{ $selectedDept }}";

            $('#modalKaryawanList').modal('show');
            $('#modalKaryawanList').find('.modal-title').text('Memuat...');
            $('#modalKaryawanList').find('#loadmodal').html('<div class="text-center py-4"><i class="ti ti-loader-2 ti-spin fs-2 text-primary"></i></div>');

            $.post("{{ route('dashboard.get.karyawan.presensi') }}", {
                _token: "{{ csrf_token() }}",
                status: status,
                tanggal: tanggal,
                kode_cabang: kode_cabang,
                kode_dept: kode_dept
            }, function(res) {
                if (res.success) {
                    $('#modalKaryawanList').find('.modal-title').text(res.title);
                    $('#modalKaryawanList').find('#loadmodal').html(res.html);
                }
            });
        });

        // 3. Render Exactly 4 Meaningful Charts via ApexCharts
        let chartTrenInstance = null;
        let chartStatusInstance = null;
        let chartShiftInstance = null;
        let chartIzinInstance = null;

        function initDashboardCharts() {
            if (typeof ApexCharts === 'undefined') return;

            // Chart 1: Tren Kehadiran (Line Chart)
            const elChart1 = document.querySelector('#chartTrenKehadiran');
            if (elChart1) {
                if (chartTrenInstance) {
                    try { chartTrenInstance.destroy(); } catch(e) {}
                    chartTrenInstance = null;
                }
                elChart1.innerHTML = '';
                const opt1 = {
                    series: [
                        { name: 'Hadir Tepat Waktu', data: @json($chart1Hadir) },
                        { name: 'Terlambat', data: @json($chart1Telat) }
                    ],
                    chart: {
                        type: 'line',
                        height: 280,
                        toolbar: { show: false },
                        zoom: { enabled: false }
                    },
                    colors: ['#059669', '#D97706'],
                    stroke: { curve: 'smooth', width: [3, 2.5] },
                    markers: { size: 4, strokeWidth: 2, strokeColors: '#FFFFFF', hover: { size: 6 } },
                    xaxis: {
                        categories: @json($dateLabels),
                        labels: { style: { colors: '#64748B', fontSize: '11px', fontFamily: 'Inter, sans-serif' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: { style: { colors: '#64748B', fontSize: '11px' } },
                        min: 0,
                        forceNiceScale: true
                    },
                    grid: { borderColor: '#F1F5F9', strokeDashArray: 4 },
                    legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
                    tooltip: { theme: 'light' }
                };
                chartTrenInstance = new ApexCharts(elChart1, opt1);
                chartTrenInstance.render();
            }

            // Chart 2: Status Kehadiran Hari Ini (Donut Chart)
            const elChart2 = document.querySelector('#chartStatusHariIni');
            if (elChart2) {
                if (chartStatusInstance) {
                    try { chartStatusInstance.destroy(); } catch(e) {}
                    chartStatusInstance = null;
                }
                elChart2.innerHTML = '';
                const seriesData = @json($chart2Series);
                const hasData = seriesData.some(v => v > 0);

                if (hasData) {
                    const opt2 = {
                        series: seriesData,
                        labels: @json($chart2Labels),
                        chart: {
                            type: 'donut',
                            height: 280
                        },
                        colors: ['#059669', '#D97706', '#2563EB', '#EA580C', '#9333EA', '#DC2626'],
                        dataLabels: { enabled: false },
                        legend: {
                            position: 'bottom',
                            fontSize: '11.5px',
                            markers: { radius: 3 }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '72%',
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: 'Tercatat',
                                            fontSize: '12px',
                                            fontWeight: 600,
                                            color: '#64748B',
                                            formatter: function(w) {
                                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        tooltip: { theme: 'light' }
                    };
                    chartStatusInstance = new ApexCharts(elChart2, opt2);
                    chartStatusInstance.render();
                } else {
                    elChart2.innerHTML = '<div class="text-center py-5 text-muted"><i class="ti ti-chart-pie-off fs-2 mb-2 d-block opacity-50"></i><span style="font-size: 13px;">Belum ada data kehadiran untuk tanggal ini.</span></div>';
                }
            }

            // Chart 3: Kehadiran per Shift (Grouped Bar Chart)
            const elChart3 = document.querySelector('#chartKehadiranShift');
            if (elChart3) {
                if (chartShiftInstance) {
                    try { chartShiftInstance.destroy(); } catch(e) {}
                    chartShiftInstance = null;
                }
                elChart3.innerHTML = '';
                const shiftCategories = @json($chart3Categories);
                const shiftHadir = @json($chart3Hadir);
                const shiftTelat = @json($chart3Telat);
                const hasShiftData = shiftHadir.some(v => v > 0) || shiftTelat.some(v => v > 0);

                if (hasShiftData || shiftCategories.length > 0) {
                    const opt3 = {
                        series: [
                            { name: 'Hadir Tepat', data: shiftHadir },
                            { name: 'Terlambat', data: shiftTelat }
                        ],
                        chart: {
                            type: 'bar',
                            height: 280,
                            toolbar: { show: false }
                        },
                        colors: ['#059669', '#D97706'],
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '45%',
                                borderRadius: 4
                            }
                        },
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ['transparent'] },
                        xaxis: {
                            categories: shiftCategories,
                            labels: { style: { colors: '#64748B', fontSize: '11px' } }
                        },
                        yaxis: {
                            labels: { style: { colors: '#64748B', fontSize: '11px' } },
                            min: 0,
                            forceNiceScale: true
                        },
                        grid: { borderColor: '#F1F5F9', strokeDashArray: 4 },
                        legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
                        tooltip: { theme: 'light' }
                    };
                    chartShiftInstance = new ApexCharts(elChart3, opt3);
                    chartShiftInstance.render();
                } else {
                    elChart3.innerHTML = '<div class="text-center py-5 text-muted"><i class="ti ti-chart-bar-off fs-2 mb-2 d-block opacity-50"></i><span style="font-size: 13px;">Belum ada jadwal shift untuk tanggal ini.</span></div>';
                }
            }

            // Chart 4: Izin, Sakit & Cuti (Area/Bar Chart)
            const elChart4 = document.querySelector('#chartIzinSakitCuti');
            if (elChart4) {
                if (chartIzinInstance) {
                    try { chartIzinInstance.destroy(); } catch(e) {}
                    chartIzinInstance = null;
                }
                elChart4.innerHTML = '';
                const opt4 = {
                    series: [
                        { name: 'Izin', data: @json($chart4Izin) },
                        { name: 'Sakit', data: @json($chart4Sakit) },
                        { name: 'Cuti', data: @json($chart4Cuti) }
                    ],
                    chart: {
                        type: 'bar',
                        height: 280,
                        stacked: true,
                        toolbar: { show: false }
                    },
                    colors: ['#2563EB', '#EA580C', '#9333EA'],
                    plotOptions: {
                        bar: {
                            borderRadius: 3,
                            columnWidth: '40%'
                        }
                    },
                    dataLabels: { enabled: false },
                    xaxis: {
                        categories: @json($dateLabels),
                        labels: { style: { colors: '#64748B', fontSize: '11px' } }
                    },
                    yaxis: {
                        labels: { style: { colors: '#64748B', fontSize: '11px' } },
                        min: 0,
                        forceNiceScale: true
                    },
                    grid: { borderColor: '#F1F5F9', strokeDashArray: 4 },
                    legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
                    tooltip: { theme: 'light' }
                };
                chartIzinInstance = new ApexCharts(elChart4, opt4);
                chartIzinInstance.render();
            }
        }

        initDashboardCharts();
    })();
</script>
@endpush
