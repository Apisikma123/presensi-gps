@extends('layouts.app')
@section('titlepage', 'Dashboard Operasional Coffee Shop')

<style>
    /* Minimalist Bento Design System */
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }

    .bento-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.03);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }

    .bento-card:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.05);
    }

    .bento-card.clickable {
        cursor: pointer;
    }

    .bento-card__icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .bento-card__title {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 0.35rem;
    }

    .bento-card__value {
        font-size: 2.2rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin: 0;
        font-family: 'JetBrains Mono', monospace;
    }

    .bento-card__meta {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    /* Minimalist Action Tiles */
    .action-tile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none !important;
        color: inherit !important;
        transition: all 0.2s ease;
    }

    .action-tile:hover {
        background: #f8fafc;
        border-color: #32745e;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px -3px rgba(50, 116, 94, 0.1);
    }

    .action-tile__icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(50, 116, 94, 0.1);
        color: #32745e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    /* Live Digital Clock Chip */
    .live-clock-chip {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.6rem 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
    }

    .live-dot {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 9999px;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>

@section('content')
@section('navigasi')
    <span>Dashboard Operasional</span>
@endsection

@php
    $authUser = auth()->user();
    $fullName = $authUser->name ?? 'Manager';
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

    $tanggalHariIni = getnamaHari(date('D')) . ', ' . DateToIndo(date('Y-m-d'));
@endphp

@if(isset($expired_alert) && $expired_alert !== null)
    <div class="alert alert-danger d-flex align-items-center mb-3" role="alert" style="border-radius: 12px; border: 1px solid #fecaca; background: #fef2f2; padding: 1rem 1.25rem;">
        <span class="text-danger me-3 fs-3">
            <i class="ti ti-alert-triangle"></i>
        </span>
        <div>
            <h6 class="alert-heading mb-0 fw-bold text-danger">
                {{ $expired_alert['is_expired'] ? 'Aplikasi Telah Kadaluarsa!' : 'Peringatan Masa Aktif Aplikasi!' }}
            </h6>
            <span class="text-secondary" style="font-size: 13px;">
                @if($expired_alert['is_expired'])
                    Masa aktif aplikasi telah berakhir pada <strong>{{ $expired_alert['date'] }}</strong>.
                @else
                    Masa aktif aplikasi akan berakhir dalam <strong>{{ $expired_alert['days_left'] }} hari</strong> lagi (tanggal <strong>{{ $expired_alert['date'] }}</strong>).
                @endif
            </span>
        </div>
    </div>
@endif

<!-- Hero Header & Live Bar -->
<div class="card mb-3 border shadow-none" style="border-radius: 16px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-color: #e2e8f0;">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge" style="background: rgba(50, 116, 94, 0.12); color: #32745e; font-weight: 600; font-size: 11px;">
                        <i class="ti ti-coffee me-1"></i> Coffee Shop Ops
                    </span>
                    <span class="text-muted" style="font-size: 13px;">• {{ $tanggalHariIni }}</span>
                </div>
                <h3 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.02em;">
                    {{ $greeting }}, {{ $userName }} 👋
                </h3>
                <p class="text-muted mb-0" style="font-size: 13.5px;">
                    Pantau kehadiran tim barista, kasir, dan operasional outlet secara real-time.
                </p>
            </div>

            <!-- Live Clock & Filter Action -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="live-clock-chip">
                    <div class="live-dot"></div>
                    <div id="clock-icon" class="text-muted">
                        <i class="ti ti-sun"></i>
                    </div>
                    <div class="fw-bold font-mono text-dark" style="font-size: 15px;">
                        <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
                        <span id="ampm" class="text-muted" style="font-size: 11px;">AM</span>
                    </div>
                </div>

                <button class="btn btn-outline-secondary d-flex align-items-center gap-1.5 shadow-none" data-bs-toggle="modal" data-bs-target="#filterDashboardModal" style="border-radius: 12px; font-size: 13px; font-weight: 600; padding: 0.6rem 1rem;">
                    <i class="ti ti-filter"></i>
                    <span>Filter Outlet</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 4 Core Operational Metric Bento Cards -->
<div class="bento-grid mb-4">
    <!-- 1. Hadir Hari Ini -->
    <div class="bento-card clickable stat-card-clickable" data-status="h">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="bento-card__title">Barista & Tim Hadir</span>
            <div class="bento-card__icon" style="background: #ecfdf5; color: #059669;">
                <i class="ti ti-user-check"></i>
            </div>
        </div>
        <h3 class="bento-card__value text-success">{{ $rekappresensi->hadir ?? 0 }}</h3>
        <div class="bento-card__meta">
            <span class="badge rounded-pill" style="background: #ecfdf5; color: #059669; font-size: 10.5px;">
                <i class="ti ti-circle-check me-1"></i> On Duty Hari Ini
            </span>
        </div>
    </div>

    <!-- 2. Terlambat -->
    <div class="bento-card clickable stat-card-clickable" data-status="h">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="bento-card__title">Terlambat Masuk</span>
            <div class="bento-card__icon" style="background: #fffbeb; color: #d97706;">
                <i class="ti ti-clock-exclamation"></i>
            </div>
        </div>
        <h3 class="bento-card__value text-warning">{{ $rekappresensi->alpa ?? 0 }}</h3>
        <div class="bento-card__meta">
            <span class="badge rounded-pill" style="background: #fffbeb; color: #d97706; font-size: 10.5px;">
                <i class="ti ti-alert-circle me-1"></i> Perlu Evaluasi
            </span>
        </div>
    </div>

    <!-- 3. Izin & Sakit -->
    <div class="bento-card clickable stat-card-clickable" data-status="i">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="bento-card__title">Izin & Sakit</span>
            <div class="bento-card__icon" style="background: #eff6ff; color: #2563eb;">
                <i class="ti ti-file-description"></i>
            </div>
        </div>
        <h3 class="bento-card__value text-primary">{{ ($rekappresensi->izin ?? 0) + ($rekappresensi->sakit ?? 0) }}</h3>
        <div class="bento-card__meta">
            <span class="badge rounded-pill" style="background: #eff6ff; color: #2563eb; font-size: 10.5px;">
                Izin: {{ $rekappresensi->izin ?? 0 }} | Sakit: {{ $rekappresensi->sakit ?? 0 }}
            </span>
        </div>
    </div>

    <!-- 4. Cuti & Libur -->
    <div class="bento-card clickable stat-card-clickable" data-status="c">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="bento-card__title">Cuti & Off Shift</span>
            <div class="bento-card__icon" style="background: #fdf4ff; color: #86198f;">
                <i class="ti ti-calendar-off"></i>
            </div>
        </div>
        <h3 class="bento-card__value" style="color: #86198f;">{{ $rekappresensi->cuti ?? 0 }}</h3>
        <div class="bento-card__meta">
            <span class="badge rounded-pill" style="background: #fdf4ff; color: #86198f; font-size: 10.5px;">
                <i class="ti ti-calendar-event me-1"></i> Terjadwal
            </span>
        </div>
    </div>
</div>

<!-- Tim Ulang Tahun Hari Ini (Jika Ada) -->
@if(isset($birthday) && count($birthday) > 0)
    <div class="card mb-4 border shadow-none" style="border-radius: 14px; background: #fffdf5; border-color: #fde68a;">
        <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #fef3c7; color: #d97706; font-size: 1.5rem;">
                    🎉
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Ulang Tahun Hari Ini!</h6>
                    <p class="mb-0 text-muted" style="font-size: 13px;">
                        @foreach($birthday as $b)
                            <strong>{{ $b->nama_karyawan }}</strong> ({{ $b->nama_jabatan }} - {{ $b->nama_cabang }})@if(!$loop->last), @endif
                        @endforeach
                    </p>
                </div>
            </div>
            <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/^0/', '62', $birthday[0]->no_hp ?? '') }}&text=Selamat%20Ulang%20Tahun%20{{ urlencode($birthday[0]->nama_karyawan) }}!%20Semoga%20sehat%20selalu%20dan%20sukses%20bersama%20tim!" 
               target="_blank" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 shadow-none" style="font-size: 12px; font-weight: 600;">
                <i class="ti ti-brand-whatsapp me-1"></i> Kirim Ucapan WhatsApp
            </a>
        </div>
    </div>
@endif

<!-- Pusat Operasional Coffee Shop (Quick Actions) -->
<div class="mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.01em;">Pusat Aksi Operasional Outlet</h5>
            <small class="text-muted">Akses cepat ke menu harian manajemen coffee shop</small>
        </div>
    </div>

    <div class="row g-3">
        <!-- 1. Monitoring Presensi Live -->
        <div class="col-lg-3 col-md-6 col-12">
            <a href="{{ route('presensi.index') }}" class="action-tile h-100">
                <div class="action-tile__icon">
                    <i class="ti ti-device-laptop"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Monitoring Presensi</h6>
                    <small class="text-muted" style="font-size: 12px;">Live clock-in, foto & peta GPS</small>
                </div>
            </a>
        </div>

        <!-- 2. Approval Pengajuan -->
        <div class="col-lg-3 col-md-6 col-12">
            <a href="{{ route('izinabsen.index') }}" class="action-tile h-100">
                <div class="action-tile__icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                    <i class="ti ti-checklist"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Persetujuan Izin</h6>
                    <small class="text-muted" style="font-size: 12px;">Approve cuti, izin & koreksi</small>
                </div>
            </a>
        </div>

        <!-- 3. Atur Shift & Jam Kerja -->
        <div class="col-lg-3 col-md-6 col-12">
            <a href="{{ route('jamkerja.index') }}" class="action-tile h-100">
                <div class="action-tile__icon" style="background: rgba(217, 119, 6, 0.1); color: #d97706;">
                    <i class="ti ti-clock"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Shift & Jam Kerja</h6>
                    <small class="text-muted" style="font-size: 12px;">Shift opening, middle, closing</small>
                </div>
            </a>
        </div>

        <!-- 4. Rekap Laporan Bulanan -->
        <div class="col-lg-3 col-md-6 col-12">
            <a href="{{ route('laporan.presensi') }}" class="action-tile h-100">
                <div class="action-tile__icon" style="background: rgba(134, 25, 143, 0.1); color: #86198f;">
                    <i class="ti ti-file-spreadsheet"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Laporan Presensi</h6>
                    <small class="text-muted" style="font-size: 12px;">Download rekap absen & Excel</small>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Modal Filter Tanggal & Outlet -->
<div class="modal fade" id="filterDashboardModal" tabindex="-1" aria-labelledby="filterDashboardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: 1px solid #e2e8f0;">
            <div class="modal-header border-bottom pb-3">
                <h5 class="modal-title fw-bold" id="filterDashboardModalLabel">Filter Presensi Outlet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Tanggal</label>
                            <x-input-with-icon label="Tanggal" icon="ti ti-calendar" name="tanggal" datepicker="flatpickr-date"
                                value="{{ Request('tanggal') }}" hideLabel />
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Outlet / Cabang</label>
                            <x-select label="Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                selected="{{ Request('kode_cabang') }}" hideLabel />
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Departemen / Divisi</label>
                            <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                                selected="{{ Request('kode_dept') }}" upperCase="true" hideLabel />
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary rounded-pill px-4 shadow-none" style="background-color: #32745e !important; border-color: #32745e !important;">
                        <i class="ti ti-search me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Karyawan Presensi (AJAX) -->
<div class="modal fade" id="modalKaryawanPresensi" tabindex="-1" aria-labelledby="modalKaryawanPresensiLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: 1px solid #e2e8f0;">
            <div class="modal-header border-bottom pb-3">
                <h5 class="modal-title fw-bold" id="modalKaryawanPresensiLabel">Detail Kehadiran Tim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalKaryawanPresensiContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted mt-2">Memuat data tim...</div>
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    function updateClock() {
        const now = new Date();
        const hours24 = now.getHours();
        let hours = hours24;
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12;
        hours = hours ? hours : 12;
        const hoursStr = String(hours).padStart(2, '0');

        const elHours = document.getElementById('hours');
        const elMinutes = document.getElementById('minutes');
        const elSeconds = document.getElementById('seconds');
        const elAmpm = document.getElementById('ampm');

        if (elHours) elHours.textContent = hoursStr;
        if (elMinutes) elMinutes.textContent = minutes;
        if (elSeconds) elSeconds.textContent = seconds;
        if (elAmpm) elAmpm.textContent = ampm;

        const iconContainer = document.getElementById('clock-icon');
        let iconClass = 'ti ti-sun';
        let iconColor = '#f59e0b';
        
        if (hours24 >= 5 && hours24 < 10) {
            iconClass = 'ti ti-sunrise';
            iconColor = '#f59e0b';
        } else if (hours24 >= 10 && hours24 < 15) {
            iconClass = 'ti ti-sun';
            iconColor = '#eab308';
        } else if (hours24 >= 15 && hours24 < 18) {
            iconClass = 'ti ti-sunset';
            iconColor = '#f97316';
        } else {
            iconClass = 'ti ti-moon-stars';
            iconColor = '#6366f1';
        }
        
        if (iconContainer) {
            iconContainer.innerHTML = `<i class="${iconClass}" style="color: ${iconColor};"></i>`;
        }
    }

    setInterval(updateClock, 1000);
    updateClock();

    $(function() {
        $('.stat-card-clickable').on('click', function() {
            var status = $(this).data('status');
            
            var urlParams = new URLSearchParams(window.location.search);
            var tanggal = urlParams.get('tanggal') || '{{ Request("tanggal") }}' || '';
            var kode_cabang = urlParams.get('kode_cabang') || '{{ Request("kode_cabang") }}' || '';
            var kode_dept = urlParams.get('kode_dept') || '{{ Request("kode_dept") }}' || '';

            $('#modalKaryawanPresensiContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted mt-2">Memuat data...</div>
                </div>
            `);
            
            $('#modalKaryawanPresensiLabel').text('Detail Kehadiran Tim');
            $('#modalKaryawanPresensi').modal('show');

            $.ajax({
                type: 'POST',
                url: '{{ route("dashboard.get.karyawan.presensi") }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                    tanggal: tanggal,
                    kode_cabang: kode_cabang,
                    kode_dept: kode_dept
                },
                success: function(response) {
                    if(response.success) {
                        $('#modalKaryawanPresensiLabel').text(response.title);
                        $('#modalKaryawanPresensiContent').html(response.html);
                    } else {
                        $('#modalKaryawanPresensiContent').html(`<div class="alert alert-danger m-3">Gagal memuat data.</div>`);
                    }
                },
                error: function(xhr, status, error) {
                    $('#modalKaryawanPresensiContent').html(`<div class="alert alert-danger m-3">Terjadi kesalahan: ${error}</div>`);
                }
            });
        });

        $(document).on('keyup', '#searchKaryawanNama', function() {
            var value = $(this).val().toLowerCase();
            $("#modalKaryawanPresensiContent table tbody tr").filter(function() {
                var nameText = $(this).find('td:eq(3)').text().toLowerCase();
                var nikText = $(this).find('td:eq(2)').text().toLowerCase();
                
                if ($(this).find('td').length > 1) {
                    $(this).toggle(nameText.indexOf(value) > -1 || nikText.indexOf(value) > -1);
                }
            });
        });
    });
</script>
@endpush
