@extends('layouts.mobile.modern')

@section('title', 'Ajuan Izin')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('dashboard.index') }}"
        onclick="if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) { event.preventDefault(); window.history.back(); }"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-90 transition-transform"
        title="Kembali">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        .bento-cuti-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.06);
            padding: 14px 16px;
            margin-top: 12px;
            margin-bottom: 14px;
            transition: all 0.2s ease;
        }
        .bento-cuti-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 12px;
        }
        .bento-cuti-title-group {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .bento-cuti-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(30, 77, 62, 0.08);
            color: #1E4D3E;
            font-size: 18px;
            flex-shrink: 0;
        }
        .bento-btn-ajukan {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 10px;
            background: #1E4D3E;
            color: #ffffff !important;
            text-decoration: none !important;
            box-shadow: 0 2px 6px rgba(30, 77, 62, 0.2);
            transition: all 0.15s ease;
        }
        .bento-btn-ajukan:active {
            transform: scale(0.95);
        }
        .bento-cuti-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            background: #f8fafc;
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 12px;
            padding: 10px 4px;
            text-align: center;
        }
        .bento-cuti-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 2px 4px;
        }
        .bento-cuti-col:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -3px;
            top: 15%;
            height: 70%;
            width: 1px;
            background: rgba(15, 23, 42, 0.1);
        }
        .bento-label-micro {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748B;
            margin-bottom: 2px;
        }
        .bento-digit {
            font-family: 'JetBrains Mono', 'Geist Mono', monospace;
            font-weight: 800;
            line-height: 1;
        }
        .bento-tag-status {
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 9999px;
            margin-top: 4px;
            display: inline-block;
        }
        .bento-progress-wrap {
            margin-top: 10px;
            padding: 0 2px;
        }
        .bento-progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            font-weight: 600;
            color: #64748B;
            margin-bottom: 4px;
        }
        .bento-progress-track {
            width: 100%;
            height: 6px;
            background: #f1f5f9;
            border-radius: 9999px;
            overflow: hidden;
        }
        .bento-progress-fill {
            height: 100%;
            background: #1E4D3E;
            border-radius: 9999px;
            transition: width 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Detail Modal with True Backdrop Blur & High Elevation (DESIGN.md Bento) */
        #modal-detail {
            position: fixed !important;
            inset: 0 !important;
            z-index: 99999 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: rgba(15, 23, 42, 0.45) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        #modal-detail.is-open {
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        #modal-detail .modal-card {
            width: 100%;
            max-width: 360px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px !important;
            border: 1px solid rgba(15, 23, 42, 0.08) !important;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.2) !important;
            padding: 20px;
            transform: translateY(12px) scale(0.97);
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            max-height: 85vh;
            overflow-y: auto;
        }
        #modal-detail.is-open .modal-card {
            transform: translateY(0) scale(1);
        }

        /* Modal Close Button */
        .btn-close-modal {
            border: none !important;
            background: #f1f5f9 !important;
            outline: none !important;
            box-shadow: none !important;
            cursor: pointer !important;
            border-radius: 9999px !important;
            color: #64748b !important;
            width: 30px !important;
            height: 30px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.15s ease !important;
        }
        .btn-close-modal:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
        }
        .btn-close-modal:active {
            transform: scale(0.92);
        }

        /* Status Colors per Global Standard (matching Histori & Dashboard) */
        .status-disetujui {
            background: #f0fdf4 !important;
            border: 1px solid #bbf7d0 !important;
        }
        .status-ditolak {
            background: #fef2f2 !important;
            border: 1px solid #fecdd3 !important;
        }
        .status-pending {
            background: #fffbeb !important;
            border: 1px solid #fde68a !important;
        }

        /* Tactical Action Buttons in Modal (DESIGN.md Buttons) */
        .btn-tactile-edit {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            padding: 8px 16px !important;
            border-radius: 12px !important;
            background: var(--color-nav, #1E4D3E) !important;
            color: #ffffff !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            border: 1px solid var(--color-nav, #1E4D3E) !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            box-shadow: 0 2px 6px rgba(30, 77, 62, 0.15) !important;
            outline: none !important;
        }
        .btn-tactile-edit:hover {
            opacity: 0.92;
        }
        .btn-tactile-edit:active {
            transform: translateY(1px) !important;
        }
        .btn-tactile-cancel {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            padding: 8px 14px !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            color: #e11d48 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            border: 1px solid #fecdd3 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            outline: none !important;
        }
        .btn-tactile-cancel:hover {
            background: #fff1f2 !important;
            border-color: #fda4af !important;
        }
        .btn-tactile-cancel:active {
            transform: translateY(1px) !important;
        }

        /* Antislop-layoutmobile: ensure leave history scrolls cleanly past bottom nav & FAB */
        #data-container {
            padding-bottom: calc(110px + env(safe-area-inset-bottom, 0px)) !important;
        }

        /* Pagination Mobile per DESIGN.md & antislop-layoutmobile */
        .pagination-mobile-wrap {
            padding-top: 16px;
            padding-bottom: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .pagination-pill-nav {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
        }
        .pagination-btn {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }
        .pagination-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .pagination-btn:active {
            transform: scale(0.92);
        }
        .pagination-btn-active {
            background: #1E4D3E !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 8px rgba(30, 77, 62, 0.25) !important;
        }
        .pagination-btn-disabled {
            color: #cbd5e1 !important;
            cursor: not-allowed !important;
            pointer-events: none;
        }
    </style>
@endpush

@section('content')
    @php
        $infoCuti = $sisa_cuti_info ?? [
            'tahun' => date('Y'),
            'kuota' => 12,
            'terpakai' => 0,
            'pending' => 0,
            'sisa' => 12,
            'jenis_cuti_nama' => 'Cuti Tahunan',
            'kode_cuti' => 'C01'
        ];
        $pctTerpakai = $infoCuti['kuota'] > 0 ? min(100, round(($infoCuti['terpakai'] / $infoCuti['kuota']) * 100)) : 0;
    @endphp

    {{-- BENTO CARD: SISA CUTI --}}
    <div class="bento-cuti-card fade-up">
        <div class="bento-cuti-header">
            <div class="bento-cuti-title-group">
                <div class="bento-cuti-icon">
                    <ion-icon name="calendar-outline"></ion-icon>
                </div>
                <div style="min-width:0;">
                    <h4 style="font-size:13px; font-weight:800; color:#0F172A; margin:0; line-height:1.2;">Saldo Cuti Karyawan</h4>
                    <span style="font-size:11px; color:#64748B; font-weight:500;">Tahun Periode {{ $infoCuti['tahun'] }}</span>
                </div>
            </div>
            <a href="{{ route('izincuti.create') }}" class="bento-btn-ajukan">
                <ion-icon name="add-circle-outline" style="font-size:14px;"></ion-icon>
                <span>Ajukan</span>
            </a>
        </div>

        <div class="bento-cuti-metrics">
            <div class="bento-cuti-col">
                <span class="bento-label-micro">Sisa Cuti</span>
                <div style="display:flex; align-items:baseline; gap:2px;">
                    <span class="bento-digit" style="font-size:24px; color:#1E4D3E;">{{ $infoCuti['sisa'] }}</span>
                    <span style="font-size:10px; font-weight:600; color:#64748B;">Hr</span>
                </div>
                <span class="bento-tag-status" style="background:#D1FAE5; color:#065F46;">Tersedia</span>
            </div>
            <div class="bento-cuti-col">
                <span class="bento-label-micro">Terpakai</span>
                <div style="display:flex; align-items:baseline; gap:2px;">
                    <span class="bento-digit" style="font-size:20px; color:#D97706;">{{ $infoCuti['terpakai'] }}</span>
                    <span style="font-size:10px; font-weight:600; color:#64748B;">Hr</span>
                </div>
                <span class="bento-tag-status" style="background:#FEF3C7; color:#92400E;">Diambil</span>
            </div>
            <div class="bento-cuti-col">
                <span class="bento-label-micro">Total Kuota</span>
                <div style="display:flex; align-items:baseline; gap:2px;">
                    <span class="bento-digit" style="font-size:20px; color:#334155;">{{ $infoCuti['kuota'] }}</span>
                    <span style="font-size:10px; font-weight:600; color:#64748B;">Hr</span>
                </div>
                <span class="bento-tag-status" style="background:#F1F5F9; color:#475569;">Plafon</span>
            </div>
        </div>

        <div class="bento-progress-wrap">
            <div class="bento-progress-header">
                <span>Pemakaian Kuota {{ $infoCuti['jenis_cuti_nama'] }}</span>
                <span style="font-family:'JetBrains Mono',monospace; font-weight:700; color:#334155;">{{ $pctTerpakai }}%</span>
            </div>
            <div class="bento-progress-track">
                <div class="bento-progress-fill" style="width: {{ $pctTerpakai }}%;"></div>
            </div>
            @if(!empty($infoCuti['monthly_quota']) && $infoCuti['monthly_quota'] > 0)
                <div style="margin-top:5px; display:flex; align-items:center; justify-content:space-between; font-size:10px; color:#64748B;">
                    <span>Batas per bulan:</span>
                    <span style="font-weight:700; color:#334155;">Maks. {{ $infoCuti['monthly_quota'] }} Hari / Bulan</span>
                </div>
            @endif
        </div>

        @if(!empty($infoCuti['pending']) && $infoCuti['pending'] > 0)
            <div style="margin-top:10px; padding:8px 10px; border-radius:10px; background:#FFFBEB; border:1px solid #FDE68A; color:#B45309; font-size:11px; font-weight:600; display:flex; align-items:center; gap:6px;">
                <ion-icon name="time-outline" style="font-size:14px; flex-shrink:0;"></ion-icon>
                <span>Ada <strong>{{ $infoCuti['pending'] }} hari</strong> cuti pending menunggu approval.</span>
            </div>
        @endif
    </div>

    {{-- ===== HISTORY LIST ===== --}}
    <div id="showhistori">
        {{-- Skeleton placeholder (hidden, data renders immediately) --}}
        <div id="skeleton-container" style="display:none;" class="space-y-2">
            @for ($i = 0; $i < 5; $i++)
                <div class="rounded-[10px] p-1 border shadow-sm" style="background: #fff; border-color: #f1f5f9;">
                    <div class="flex items-center gap-2">
                        <div class="skeleton-avatar sk flex-shrink-0"></div>
                        <div class="flex-1 space-y-2 pr-2">
                            <div class="flex justify-between items-center">
                                <div class="skeleton-text w-24 sk"></div>
                                <div class="skeleton-text w-12 sk"></div>
                            </div>
                            <div class="skeleton-text w-32 sk"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Data container --}}
        <div id="data-container" class="space-y-2">
            @foreach ($pengajuan_izin as $index => $d)
                @php
                    if ($d->ket == 'i') {
                        $route = 'izinabsen.delete';
                        $route_create = route('izinabsen.create');
                        $ket_text = 'Izin Absen';
                        $icon = 'document-text-outline';
                    } elseif ($d->ket == 's') {
                        $route = 'izinsakit.delete';
                        $route_create = route('izinsakit.create');
                        $ket_text = 'Izin Sakit';
                        $icon = 'medkit-outline';
                    } elseif ($d->ket == 'c') {
                        $route = 'izincuti.delete';
                        $route_create = route('izincuti.create');
                        $ket_text = !empty($d->jenis_cuti) ? $d->jenis_cuti : 'Izin Cuti';
                        $icon = 'calendar-outline';
                    } else {
                        $route = 'izinabsen.delete';
                        $route_create = route('izinabsen.create');
                        $ket_text = 'Izin Absen';
                        $icon = 'document-text-outline';
                    }

                    $namahari = [
                        'Sun' => 'Minggu',
                        'Mon' => 'Senin',
                        'Tue' => 'Selasa',
                        'Wed' => 'Rabu',
                        'Thu' => 'Kamis',
                        'Fri' => 'Jumat',
                        'Sat' => 'Sabtu'
                    ];
                    $day_eng = date('D', strtotime($d->dari));
                    $day_indo = $namahari[$day_eng] ?? $day_eng;
                    $day_short = strtoupper(substr($day_indo, 0, 3));
                    $tgl = date('d', strtotime($d->dari));
                    $jml_hari = hitungHari($d->dari, $d->sampai);

                    $deleteUrl = route($route, Crypt::encrypt($d->kode));
                    $sidUrl = !empty($d->doc_sid) ? route('file.sid', $d->doc_sid) : '';
                @endphp

                <div class="card-item-pengajuan fade-up press mb-2.5 overflow-hidden cursor-pointer bg-white rounded-xl border border-slate-200/80 p-3 shadow-[0_1px_3px_rgba(15,23,42,0.03)] hover:border-slate-300 hover:shadow-sm transition-all duration-150 active:scale-[0.99]"
                    style="animation-delay: {{ $index * 0.04 }}s;"
                    data-kode="{{ $d->kode }}"
                    data-ket="{{ $d->ket }}"
                    data-ket-text="{{ $ket_text }}"
                    data-tgl-diajukan="{{ DateToIndo($d->tanggal) }}"
                    data-dari="{{ DateToIndo($d->dari) }}"
                    data-sampai="{{ DateToIndo($d->sampai) }}"
                    data-jml-hari="{{ $jml_hari }}"
                    data-keterangan="{{ $d->keterangan ?? '-' }}"
                    data-hrd="{{ $d->keterangan_hrd ?? '' }}"
                    data-status="{{ $d->status_izin }}"
                    data-doc-sid="{{ $sidUrl }}"
                    data-pelimpahan="{{ $d->pelimpahan_tugas ?? '' }}"
                    data-pejabat="{{ $d->nama_kepala_divisi ?? '' }}"
                    data-delete-url="{{ $deleteUrl }}"
                    data-create-url="{{ $route_create }}">

                    <div class="flex items-center gap-3.5">
                        {{-- Date Badge (Global Standard) --}}
                        <div class="shrink-0 w-12 h-12 flex flex-col items-center justify-center rounded-xl bg-white border border-slate-200 text-center">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-none">{{ $day_short }}</span>
                            <span class="text-[16px] font-bold text-slate-800 font-mono leading-none mt-1">{{ $tgl }}</span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            {{-- Row 1: Tanggal & Kategori Badge (Standard Proportional Stack Top) --}}
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <h3 class="text-[13.5px] font-bold text-slate-800 truncate m-0 leading-tight">
                                    {{ DateToIndo($d->dari) }}@if ($d->dari != $d->sampai) <span class="text-slate-400 font-normal text-xs font-sans">— {{ DateToIndo($d->sampai) }}</span>@endif
                                </h3>
                                <span class="text-[10.5px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md shrink-0">
                                    {{ $ket_text }}
                                </span>
                            </div>

                            {{-- Row 2: Durasi/Keterangan & Status Badge (Standard Proportional Stack Bottom) --}}
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5 text-[12px] font-medium text-slate-600 truncate min-w-0">
                                    <span class="font-mono font-bold text-slate-700 shrink-0">{{ $jml_hari }} Hari</span>
                                    @if (!empty($d->keterangan) && $d->keterangan !== '-')
                                        <span class="text-slate-300 font-sans shrink-0">—</span>
                                        <span class="text-slate-500 truncate text-[11.5px]">{{ $d->keterangan }}</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if ($d->status_izin == 1)
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#f0fdf4] text-[#15803d] border border-[#bbf7d0]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                                            DISETUJUI
                                        </span>
                                    @elseif ($d->status_izin == 2)
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#fef2f2] text-[#e11d48] border border-[#fecdd3]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#e11d48]"></span>
                                            DITOLAK
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#fffbeb] text-[#b45309] border border-[#fde68a]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#d97706]"></span>
                                            PENDING
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($pengajuan_izin->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4" style="background: #f1f5f9;">
                        <ion-icon name="document-text-outline" class="text-3xl" style="color: #cbd5e1;"></ion-icon>
                    </div>
                    <h3 class="text-[14px] font-bold mb-1" style="color: #334155;">Belum Ada Pengajuan</h3>
                    <p class="text-[12px] leading-relaxed max-w-[220px]" style="color: #94a3b8;">Klik tombol tambah di bawah
                        untuk membuat pengajuan izin baru.</p>
                </div>
            @endif

            {{-- ===== PAGINATION (JIKA DATA LEBIH DARI 10) ===== --}}
            @if ($pengajuan_izin->hasPages())
                <div class="pagination-mobile-wrap">
                    <span class="text-[11.5px] font-medium text-slate-500 font-sans">
                        Menampilkan <span class="font-bold text-slate-700 font-mono">{{ $pengajuan_izin->firstItem() }}</span> - <span class="font-bold text-slate-700 font-mono">{{ $pengajuan_izin->lastItem() }}</span> dari <span class="font-bold text-slate-700 font-mono">{{ $pengajuan_izin->total() }}</span> pengajuan
                    </span>

                    <nav class="pagination-pill-nav" aria-label="Navigasi Halaman">
                        {{-- Tombol Sebelumnya --}}
                        @if ($pengajuan_izin->onFirstPage())
                            <span class="pagination-btn pagination-btn-disabled" aria-disabled="true">
                                <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
                            </span>
                        @else
                            <a href="{{ $pengajuan_izin->previousPageUrl() }}" class="pagination-btn" title="Halaman Sebelumnya">
                                <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
                            </a>
                        @endif

                        {{-- Nomor Halaman (Maksimal 3 Nomor di Mobile) --}}
                        @php
                            $start = max(1, $pengajuan_izin->currentPage() - 1);
                            $end = min($pengajuan_izin->lastPage(), $pengajuan_izin->currentPage() + 1);
                            if ($pengajuan_izin->currentPage() == 1) {
                                $end = min($pengajuan_izin->lastPage(), 3);
                            } elseif ($pengajuan_izin->currentPage() == $pengajuan_izin->lastPage()) {
                                $start = max(1, $pengajuan_izin->lastPage() - 2);
                            }
                        @endphp

                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $pengajuan_izin->currentPage())
                                <span class="pagination-btn pagination-btn-active" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $pengajuan_izin->url($page) }}" class="pagination-btn">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        {{-- Tombol Berikutnya --}}
                        @if ($pengajuan_izin->hasMorePages())
                            <a href="{{ $pengajuan_izin->nextPageUrl() }}" class="pagination-btn" title="Halaman Berikutnya">
                                <ion-icon name="chevron-forward-outline" class="text-base"></ion-icon>
                            </a>
                        @else
                            <span class="pagination-btn pagination-btn-disabled" aria-disabled="true">
                                <ion-icon name="chevron-forward-outline" class="text-base"></ion-icon>
                            </span>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    </div>

    {{-- Floating Action Menu --}}
    <div id="fab-backdrop" class="fixed inset-0 z-40 bg-slate-900/10 backdrop-blur-[2px] opacity-0 pointer-events-none transition-opacity duration-200"></div>

    <div class="fixed bottom-24 right-5 z-50">
        <div class="relative">
            {{-- Unified Minimalist Action Menu --}}
            <div id="fab-menu"
                class="absolute bottom-14 right-0 w-52 bg-white rounded-2xl border border-slate-200/80 shadow-[0_12px_32px_rgba(15,23,42,0.12)] p-1.5 pointer-events-none opacity-0 translate-y-2 scale-95 origin-bottom-right transition-all duration-200">
                <div class="px-2.5 py-1.5 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                    Buat Pengajuan
                </div>
                <div class="space-y-0.5">
                    <a href="{{ route('izinabsen.create') }}" class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-slate-700 hover:bg-slate-50 active:bg-slate-100 transition-colors">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 text-blue-600 shrink-0">
                            <ion-icon name="document-text-outline" class="text-lg"></ion-icon>
                        </div>
                        <span class="text-[13px] font-semibold">Izin Absen</span>
                    </a>
                    <a href="{{ route('izinsakit.create') }}" class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-slate-700 hover:bg-slate-50 active:bg-slate-100 transition-colors">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-50 text-rose-600 shrink-0">
                            <ion-icon name="medkit-outline" class="text-lg"></ion-icon>
                        </div>
                        <span class="text-[13px] font-semibold">Izin Sakit</span>
                    </a>
                    <a href="{{ route('izincuti.create') }}" class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-slate-700 hover:bg-slate-50 active:bg-slate-100 transition-colors">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-50 text-amber-600 shrink-0">
                            <ion-icon name="calendar-outline" class="text-lg"></ion-icon>
                        </div>
                        <span class="text-[13px] font-semibold">Izin Cuti</span>
                    </a>
                    <a href="{{ route('dispensasi.create') }}" class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-slate-700 hover:bg-slate-50 active:bg-slate-100 transition-colors">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-teal-50 text-teal-600 shrink-0">
                            <ion-icon name="time-outline" class="text-lg"></ion-icon>
                        </div>
                        <span class="text-[13px] font-semibold">Dispensasi</span>
                    </a>
                </div>
            </div>

            {{-- Main Toggle Button (Squircle 12px) --}}
            <button id="fab-main"
                class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-md active:scale-90 transition-all duration-200"
                style="background: {{ $t['primary'] ?? '#1E4D3E' }};">
                <ion-icon name="add-outline" id="fab-icon" class="text-2xl transition-transform duration-200"></ion-icon>
            </button>
        </div>
    </div>

    {{-- Detail & Action Modal --}}
    <div id="modal-detail">
        <div class="modal-card">
            {{-- Header --}}
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-[16px] font-bold text-slate-900 m-0 leading-tight">Detail Pengajuan</h3>
                    <span id="dtl-kode" class="inline-block mt-0.5 text-[11px] font-mono font-medium text-slate-400"></span>
                </div>
                <button type="button" class="btn-close-modal" title="Tutup">
                    <ion-icon name="close-outline" class="text-xl"></ion-icon>
                </button>
            </div>

            {{-- Status Banner --}}
            <div id="dtl-status-box" class="my-3 px-3 py-2.5 rounded-xl border flex items-center gap-2.5">
                <div id="dtl-status-icon" class="shrink-0 flex items-center justify-center"></div>
                <div class="min-w-0 flex-1">
                    <div id="dtl-status-title" class="text-[12.5px] font-bold leading-tight"></div>
                    <div id="dtl-status-desc" class="text-[11px] leading-tight mt-0.5"></div>
                </div>
            </div>

            {{-- HRD Note if any --}}
            <div id="dtl-hrd-box" class="hidden mb-3 p-2.5 rounded-xl bg-amber-50/80 border border-amber-200/80 text-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block mb-0.5">Catatan HRD:</span>
                <span id="dtl-hrd-text" class="text-amber-900 leading-relaxed text-[11.5px]"></span>
            </div>

            {{-- Info Table (Clean, unified key-value list) --}}
            <div class="my-3 divide-y divide-slate-100 text-xs">
                <div class="flex justify-between items-center py-2.5">
                    <span class="text-slate-500 font-medium">Jenis Izin</span>
                    <span id="dtl-jenis" class="font-semibold text-slate-800 text-[12.5px]"></span>
                </div>
                <div class="flex justify-between items-center py-2.5">
                    <span class="text-slate-500 font-medium">Tanggal Diajukan</span>
                    <span id="dtl-tgl-diajukan" class="font-semibold text-slate-800 text-[12.5px]"></span>
                </div>
                <div class="flex justify-between items-center py-2.5">
                    <span class="text-slate-500 font-medium">Periode</span>
                    <span id="dtl-periode" class="font-semibold text-slate-800 text-[12.5px]"></span>
                </div>
                <div class="flex justify-between items-center py-2.5">
                    <span class="text-slate-500 font-medium">Durasi</span>
                    <span id="dtl-durasi" class="font-mono font-bold text-slate-900 text-[12.5px]"></span>
                </div>
                <div id="dtl-extra-pelimpahan-row" class="hidden flex justify-between items-center py-2.5">
                    <span class="text-slate-500 font-medium">Pelimpahan Tugas</span>
                    <span id="dtl-pelimpahan" class="font-semibold text-slate-800 text-[12.5px]"></span>
                </div>
                <div id="dtl-extra-pejabat-row" class="hidden flex justify-between items-center py-2.5">
                    <span class="text-slate-500 font-medium">Kepala Divisi</span>
                    <span id="dtl-pejabat" class="font-semibold text-slate-800 text-[12.5px]"></span>
                </div>
                <div id="dtl-extra-sid-row" class="hidden flex justify-between items-center py-2.5">
                    <span class="text-slate-500 font-medium">Surat Dokter (SID)</span>
                    <a id="dtl-sid-link" href="#" target="_blank" class="inline-flex items-center gap-1 text-emerald-700 font-semibold hover:underline text-[12px]">
                        <ion-icon name="attach-outline"></ion-icon>
                        <span>Lihat Lampiran</span>
                    </a>
                </div>
                <div class="flex justify-between items-start py-2.5">
                    <span class="text-slate-500 font-medium shrink-0">Keterangan</span>
                    <span id="dtl-keterangan" class="font-medium text-slate-800 text-[12.5px] text-right max-w-[200px] break-words"></span>
                </div>
            </div>

            {{-- Action Buttons for Pending --}}
            <div id="dtl-actions-pending" class="hidden mt-3 pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" id="dtl-btn-cancel" class="btn-tactile-cancel">
                    <ion-icon name="trash-outline" class="text-sm"></ion-icon>
                    <span>Batalkan</span>
                </button>
                <button type="button" id="dtl-btn-edit" class="btn-tactile-edit">
                    <ion-icon name="create-outline" class="text-sm"></ion-icon>
                    <span>Edit Pengajuan</span>
                </button>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script>
        (function() {
            // FAB Toggle logic
            var fabOpen = false;
            function toggleFab(open) {
                fabOpen = (open !== undefined) ? open : !fabOpen;
                if (fabOpen) {
                    $('#fab-menu').removeClass('pointer-events-none opacity-0 translate-y-2 scale-95').addClass('opacity-100 translate-y-0 scale-100 pointer-events-auto');
                    $('#fab-backdrop').removeClass('pointer-events-none opacity-0').addClass('opacity-100 pointer-events-auto');
                    $('#fab-icon').addClass('rotate-45');
                } else {
                    $('#fab-menu').removeClass('opacity-100 translate-y-0 scale-100 pointer-events-auto').addClass('pointer-events-none opacity-0 translate-y-2 scale-95');
                    $('#fab-backdrop').removeClass('opacity-100 pointer-events-auto').addClass('pointer-events-none opacity-0');
                    $('#fab-icon').removeClass('rotate-45');
                }
            }

            $(document).off('click.fabmain', '#fab-main').on('click.fabmain', '#fab-main', function (e) {
                e.stopPropagation();
                toggleFab();
            });

            $(document).off('click.fabdrop', '#fab-backdrop').on('click.fabdrop', '#fab-backdrop', function () {
                toggleFab(false);
            });

            // Close FAB when clicking outside
            $(document).off('click.faboutside').on('click.faboutside', function (e) {
                if (!$(e.target).closest('#fab-main, #fab-menu').length && fabOpen) {
                    toggleFab(false);
                }
            });

            // Modal Detail Logic
            var currentModalData = null;

            function openDetailModal(card) {
                currentModalData = {
                    kode: card.data('kode'),
                    ketText: card.data('ket-text'),
                    tglDiajukan: card.data('tgl-diajukan'),
                    dari: card.data('dari'),
                    sampai: card.data('sampai'),
                    jmlHari: card.data('jml-hari'),
                    keterangan: card.data('keterangan'),
                    hrd: card.data('hrd'),
                    status: String(card.data('status')),
                    docSid: card.data('doc-sid'),
                    pelimpahan: card.data('pelimpahan'),
                    pejabat: card.data('pejabat'),
                    deleteUrl: card.data('delete-url'),
                    createUrl: card.data('create-url')
                };

                $('#dtl-kode').text(currentModalData.kode);
                $('#dtl-jenis').text(currentModalData.ketText);
                $('#dtl-tgl-diajukan').text(currentModalData.tglDiajukan);
                $('#dtl-periode').text(currentModalData.dari + (currentModalData.dari !== currentModalData.sampai ? ' s/d ' + currentModalData.sampai : ''));
                $('#dtl-durasi').text(currentModalData.jmlHari + ' Hari');
                $('#dtl-keterangan').text(currentModalData.keterangan || '-');

                // Status Banner
                var box = $('#dtl-status-box');
                var icon = $('#dtl-status-icon');
                var title = $('#dtl-status-title');
                var desc = $('#dtl-status-desc');

                box.removeClass('status-disetujui status-ditolak status-pending bg-emerald-50 text-emerald-800 border-emerald-200 bg-rose-50 text-rose-800 border-rose-200 bg-amber-50 text-amber-800 border-amber-200');

                if (currentModalData.status === '1') {
                    box.addClass('status-disetujui');
                    icon.html('<ion-icon name="checkmark-circle" style="color: #16a34a; font-size: 20px;"></ion-icon>');
                    title.text('Pengajuan Disetujui').css('color', '#15803d');
                    desc.text('Telah disetujui atasan/HRD').css('color', '#166534');
                    $('#dtl-actions-pending').addClass('hidden');
                } else if (currentModalData.status === '2') {
                    box.addClass('status-ditolak');
                    icon.html('<ion-icon name="close-circle" style="color: #e11d48; font-size: 20px;"></ion-icon>');
                    title.text('Pengajuan Ditolak').css('color', '#be123c');
                    desc.text('Tidak disetujui atasan/HRD').css('color', '#9f1239');
                    $('#dtl-actions-pending').addClass('hidden');
                } else {
                    box.addClass('status-pending');
                    icon.html('<ion-icon name="time" style="color: #d97706; font-size: 20px;"></ion-icon>');
                    title.text('Menunggu Persetujuan').css('color', '#b45309');
                    desc.text('Sedang menunggu konfirmasi atasan/HRD').css('color', '#92400e');
                    $('#dtl-actions-pending').removeClass('hidden');
                }

                // HRD Note
                if (currentModalData.hrd && currentModalData.hrd.trim() !== '') {
                    $('#dtl-hrd-text').text(currentModalData.hrd);
                    $('#dtl-hrd-box').removeClass('hidden');
                } else {
                    $('#dtl-hrd-box').addClass('hidden');
                }

                // Pelimpahan
                if (currentModalData.pelimpahan && currentModalData.pelimpahan.trim() !== '') {
                    $('#dtl-pelimpahan').text(currentModalData.pelimpahan);
                    $('#dtl-extra-pelimpahan-row').removeClass('hidden');
                } else {
                    $('#dtl-extra-pelimpahan-row').addClass('hidden');
                }

                // Pejabat
                if (currentModalData.pejabat && currentModalData.pejabat.trim() !== '') {
                    $('#dtl-pejabat').text(currentModalData.pejabat);
                    $('#dtl-extra-pejabat-row').removeClass('hidden');
                } else {
                    $('#dtl-extra-pejabat-row').addClass('hidden');
                }

                // SID
                if (currentModalData.docSid && currentModalData.docSid.trim() !== '') {
                    $('#dtl-sid-link').attr('href', currentModalData.docSid);
                    $('#dtl-extra-sid-row').removeClass('hidden');
                } else {
                    $('#dtl-extra-sid-row').addClass('hidden');
                }

                $('#modal-detail').addClass('is-open');
            }

            function closeDetailModal() {
                $('#modal-detail').removeClass('is-open');
            }

            // Click card to open detail modal
            $(document).on('click', '.card-item-pengajuan', function() {
                openDetailModal($(this));
            });

            $(document).on('click', '.btn-close-modal', function(e) {
                e.stopPropagation();
                closeDetailModal();
            });

            $(document).on('click', '#modal-detail', function(e) {
                if ($(e.target).is('#modal-detail')) {
                    closeDetailModal();
                }
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDetailModal();
                }
            });

            // Action Batalkan
            function doCancelPengajuan(deleteUrl) {
                Swal.fire({
                    title: 'Batalkan Pengajuan?',
                    text: 'Data pengajuan ini akan dibatalkan dan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Tutup',
                    borderRadius: '20px'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Membatalkan...',
                            allowOutsideClick: false,
                            didOpen: function() {
                                Swal.showLoading();
                            }
                        });
                        var form = $('<form method="POST" action="' + deleteUrl + '">' +
                            '<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '">' +
                            '<input type="hidden" name="_method" value="DELETE">' +
                        '</form>');
                        $('body').append(form);
                        form.submit();
                    }
                });
            }

            $(document).on('click', '#dtl-btn-cancel', function() {
                if (currentModalData && currentModalData.deleteUrl) {
                    closeDetailModal();
                    doCancelPengajuan(currentModalData.deleteUrl);
                }
            });

            // Action Edit
            function doEditPengajuan(deleteUrl, createUrl) {
                Swal.fire({
                    title: 'Ubah Pengajuan?',
                    text: 'Pengajuan yang masih pending akan dibatalkan, dan form baru akan langsung dibuka.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '{{ $t['primary'] ?? '#1E4D3E' }}',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    borderRadius: '20px'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mempersiapkan...',
                            text: 'Membuka form baru...',
                            allowOutsideClick: false,
                            didOpen: function() {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            type: 'POST',
                            url: deleteUrl,
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                _method: 'DELETE'
                            },
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json'
                            },
                            success: function() {
                                window.location.href = createUrl;
                            },
                            error: function() {
                                window.location.href = createUrl;
                            }
                        });
                    }
                });
            }

            $(document).on('click', '#dtl-btn-edit', function() {
                if (currentModalData && currentModalData.deleteUrl && currentModalData.createUrl) {
                    closeDetailModal();
                    doEditPengajuan(currentModalData.deleteUrl, currentModalData.createUrl);
                }
            });
        })();
    </script>
@endpush