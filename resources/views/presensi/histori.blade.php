@extends('layouts.mobile.modern')


@section('title', 'Histori Presensi')

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
        /* Antislop-layoutmobile: ensure attendance list scrolls cleanly past bottom navigation */
        #data-container {
            padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px)) !important;
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

    {{-- ===== FILTER ===== --}}
    <form method="GET" action="{{ route('presensi.histori') }}" id="formHistori">
        <div class="mt-1 mb-3 rounded-2xl overflow-hidden border border-slate-200/80 bg-white"
             style="box-shadow: 0 1px 3px rgba(15,23,42,0.04);">
            {{-- Filter Header --}}
            <div class="flex items-center gap-2 px-3.5 py-2.5 bg-slate-50/60 border-b border-slate-100">
                <div class="w-6 h-6 rounded-md flex items-center justify-center bg-[#1E4D3E]/10 text-[#1E4D3E]">
                    <ion-icon name="calendar-outline" class="text-[13px]"></ion-icon>
                </div>
                <span class="text-[12px] font-bold text-slate-700">Pilih Rentang Tanggal</span>
            </div>
            {{-- Filter Inputs --}}
            <div class="p-3">
                <div class="flex items-center gap-2" style="display: flex !important; align-items: center !important; gap: 8px !important; width: 100% !important;">
                    {{-- Dari --}}
                    <div class="flex-1 relative" style="flex: 1 1 0% !important; min-width: 0 !important;">
                        <input type="text" name="dari" id="dari" 
                            class="w-full rounded-xl py-2 px-3 text-[12px] font-medium text-center font-mono focus:outline-none focus:ring-1 focus:ring-[#1E4D3E] focus:border-[#1E4D3E] transition-all bg-[#F8FAF8] border border-slate-200/80 text-slate-700"
                            style="width: 100% !important; height: 38px !important; box-sizing: border-box !important;"
                            placeholder="Dari" value="{{ Request('dari') }}" autocomplete="off" required readonly>
                    </div>
                    <div class="flex-shrink-0 text-slate-300 flex items-center justify-center" style="flex-shrink: 0 !important; width: 16px !important; display: flex !important; align-items: center !important; justify-content: center !important;">
                        <ion-icon name="arrow-forward-outline" class="text-xs"></ion-icon>
                    </div>
                    {{-- Sampai --}}
                    <div class="flex-1 relative" style="flex: 1 1 0% !important; min-width: 0 !important;">
                        <input type="text" name="sampai" id="sampai" 
                            class="w-full rounded-xl py-2 px-3 text-[12px] font-medium text-center font-mono focus:outline-none focus:ring-1 focus:ring-[#1E4D3E] focus:border-[#1E4D3E] transition-all bg-[#F8FAF8] border border-slate-200/80 text-slate-700"
                            style="width: 100% !important; height: 38px !important; box-sizing: border-box !important;"
                            placeholder="Sampai" value="{{ Request('sampai') }}" autocomplete="off" required readonly>
                    </div>
                    {{-- Button --}}
                    <button type="submit" id="btnCari"
                        class="flex-shrink-0 w-10 h-[38px] rounded-xl text-white flex items-center justify-center active:scale-95 transition-all shadow-sm"
                        style="background: #1E4D3E !important; width: 40px !important; min-width: 40px !important; max-width: 40px !important; height: 38px !important; flex: 0 0 40px !important; padding: 0 !important; display: flex !important; align-items: center !important; justify-content: center !important; border: 0 !important;">
                        <ion-icon name="search-outline" class="text-base" style="font-size: 18px !important;"></ion-icon>
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ===== HISTORY LIST ===== --}}
    <div id="showhistori">
        {{-- Skeleton for search/filter submission --}}
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
        <div id="data-container" class="space-y-2.5">
            @foreach ($datapresensi as $index => $d)
                @php
                    $namahari = [
                        'Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu',
                        'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'
                    ];
                    $day_eng = date('D', strtotime($d->tanggal));
                    $day_indo = $namahari[$day_eng] ?? $day_eng;
                    $day_short = strtoupper(substr($day_indo, 0, 3));
                    $tgl = date('d', strtotime($d->tanggal));
                    $bulan_indo = getNamabulan((int)date('m', strtotime($d->tanggal)));
                    $tahun = date('Y', strtotime($d->tanggal));

                    $is_late = false;
                    $jam_telat = 0;
                    $menit_telat = 0;
                    $pulangcepat = 0;

                    if ($d->status == 'h') {
                        $jam_in_ts = strtotime($d->jam_in);
                        $jam_masuk_ts = strtotime($d->tanggal . ' ' . $d->jam_masuk);
                        $is_late = $jam_in_ts > $jam_masuk_ts;

                        if ($is_late && $d->jam_in) {
                            $terlambat_selisih = $jam_in_ts - $jam_masuk_ts;
                            $jam_telat = floor($terlambat_selisih / 3600);
                            $sisa = $terlambat_selisih % 3600;
                            $menit_telat = floor($sisa / 60);
                        }

                        $is_archived = !empty($d->is_archived);
                        $archive_month = \Carbon\Carbon::parse($d->tanggal)->translatedFormat('F Y');
                    }
                @endphp

                <div class="press overflow-hidden cursor-pointer presensi-card bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm hover:border-slate-300 transition-all duration-150 active:scale-[0.99]"
                     data-tanggal="{{ DateToIndo($d->tanggal) }}"
                     data-jam-in="{{ $d->jam_in != null ? date('H:i', strtotime($d->jam_in)) : '-' }}"
                     data-jam-out="{{ $d->jam_out != null ? date('H:i', strtotime($d->jam_out)) : '-' }}"
                     data-is-archived="{{ $is_archived ? '1' : '0' }}"
                     data-archive-month="{{ $archive_month }}"
                     data-has-foto-in="{{ !empty($d->foto_in) ? '1' : '0' }}"
                     data-has-foto-out="{{ !empty($d->foto_out) ? '1' : '0' }}"
                     data-foto-in="{{ (!$is_archived && !empty($d->foto_in)) ? url('/storage/uploads/absensi/' . $d->foto_in) : '' }}"
                     data-foto-out="{{ (!$is_archived && !empty($d->foto_out)) ? url('/storage/uploads/absensi/' . $d->foto_out) : '' }}"
                     data-status="{{ $d->status }}"
                     data-jam-kerja="{{ $d->nama_jam_kerja }}"
                     data-keterangan="{{ $d->status == 'h' ? 'Hadir' : ($d->status == 'i' ? 'Izin: ' . $d->keterangan_izin : ($d->status == 's' ? 'Sakit: ' . $d->keterangan_izin_sakit : ($d->status == 'c' ? 'Cuti: ' . $d->keterangan_izin_cuti : 'Alpha'))) }}"
                     data-nama-mesin="{{ $d->nama_mesin }}">

                    <div class="flex items-center gap-3.5">
                        {{-- Date Badge --}}
                        <div class="shrink-0 w-12 h-12 flex flex-col items-center justify-center rounded-xl bg-white border border-slate-200 text-center">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-none">{{ $day_short }}</span>
                            <span class="text-[16px] font-bold text-slate-800 font-mono leading-none mt-1">{{ $tgl }}</span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            {{-- Row 1: Tanggal & Shift --}}
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <h3 class="text-[13.5px] font-bold text-slate-800 truncate m-0 leading-tight">
                                    {{ DateToIndo($d->tanggal) }}
                                </h3>
                                <span class="text-[10.5px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md shrink-0">
                                    {{ $d->nama_jam_kerja }}
                                </span>
                            </div>

                            @if ($d->status == 'h')
                                {{-- Row 2: Jam & Status Badges --}}
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    {{-- Jam In & Out --}}
                                    <div class="flex items-center gap-1.5 text-[12px] font-mono font-medium text-slate-700">
                                        <ion-icon name="time-outline" class="text-[13px] text-slate-400"></ion-icon>
                                        <span>{{ $d->jam_in ? date('H:i', strtotime($d->jam_in)) : '--:--' }}</span>
                                        <span class="text-slate-300 font-sans font-normal">—</span>
                                        <span>{{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : '--:--' }}</span>
                                    </div>

                                    {{-- Badge Cluster --}}
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if (!empty($d->is_dispensasi))
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#e0f2fe] text-[#0284c7] border border-[#bae6fd]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#0284c7]"></span>
                                                DISPENSASI
                                            </span>
                                        @elseif ($is_late)
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#fef2f2] text-[#e11d48] border border-[#fecdd3]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#e11d48]"></span>
                                                TELAT
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#f0fdf4] text-[#15803d] border border-[#bbf7d0]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#16a34a]"></span>
                                                TEPAT WAKTU
                                            </span>
                                        @endif

                                        @if ($pulangcepat > 0)
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#fffbeb] text-[#b45309] border border-[#fde68a]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#d97706]"></span>
                                                PULANG CEPAT
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @elseif ($d->status == 'i')
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-1.5 text-[12px] font-medium text-slate-600 truncate">
                                        <ion-icon name="document-text-outline" class="text-[13px] text-slate-400 shrink-0"></ion-icon>
                                        <span class="truncate">{{ !empty($d->keterangan_izin) ? $d->keterangan_izin : 'Izin Absen' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#f0f9ff] text-[#0369a1] border border-[#bae6fd]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#0284c7]"></span>
                                            IZIN
                                        </span>
                                    </div>
                                </div>
                            @elseif ($d->status == 's')
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-1.5 text-[12px] font-medium text-slate-600 truncate">
                                        <ion-icon name="medkit-outline" class="text-[13px] text-slate-400 shrink-0"></ion-icon>
                                        <span class="truncate">{{ !empty($d->keterangan_izin_sakit) ? $d->keterangan_izin_sakit : 'Izin Sakit' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#fef2f2] text-[#be123c] border border-[#fecdd3]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#e11d48]"></span>
                                            SAKIT
                                        </span>
                                    </div>
                                </div>
                            @elseif ($d->status == 'c')
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-1.5 text-[12px] font-medium text-slate-600 truncate">
                                        <ion-icon name="calendar-outline" class="text-[13px] text-slate-400 shrink-0"></ion-icon>
                                        <span class="truncate">{{ !empty($d->keterangan_izin_cuti) ? $d->keterangan_izin_cuti : 'Cuti' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#fffbeb] text-[#b45309] border border-[#fde68a]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#d97706]"></span>
                                            CUTI
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-1.5 text-[12px] font-medium text-slate-600 truncate">
                                        <ion-icon name="close-circle-outline" class="text-[13px] text-slate-400 shrink-0"></ion-icon>
                                        <span class="truncate">Tanpa Keterangan</span>
                                    </div>
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#fef2f2] text-[#dc2626] border border-[#fecaca]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#dc2626]"></span>
                                            ALPHA
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($datapresensi->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4" style="background: #f1f5f9;">
                        <ion-icon name="calendar-outline" class="text-3xl" style="color: #cbd5e1;"></ion-icon>
                    </div>
                    <h3 class="text-[14px] font-bold mb-1" style="color: #334155;">Tidak Ada Data</h3>
                    <p class="text-[12px] leading-relaxed max-w-[220px]" style="color: #94a3b8;">Pilih rentang tanggal untuk melihat histori presensi Anda.</p>
                </div>
            @endif

            {{-- ===== PAGINATION (JIKA DATA LEBIH DARI 10) ===== --}}
            @if ($datapresensi->hasPages())
                <div class="pagination-mobile-wrap">
                    <span class="text-[11.5px] font-medium text-slate-500 font-sans">
                        Menampilkan <span class="font-bold text-slate-700 font-mono">{{ $datapresensi->firstItem() }}</span> - <span class="font-bold text-slate-700 font-mono">{{ $datapresensi->lastItem() }}</span> dari <span class="font-bold text-slate-700 font-mono">{{ $datapresensi->total() }}</span> riwayat
                    </span>

                    <nav class="pagination-pill-nav" aria-label="Navigasi Halaman">
                        {{-- Tombol Sebelumnya --}}
                        @if ($datapresensi->onFirstPage())
                            <span class="pagination-btn pagination-btn-disabled" aria-disabled="true">
                                <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
                            </span>
                        @else
                            <a href="{{ $datapresensi->previousPageUrl() }}" class="pagination-btn" title="Halaman Sebelumnya">
                                <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
                            </a>
                        @endif

                        {{-- Nomor Halaman (Maksimal 3 Nomor di Mobile) --}}
                        @php
                            $start = max(1, $datapresensi->currentPage() - 1);
                            $end = min($datapresensi->lastPage(), $datapresensi->currentPage() + 1);
                            if ($datapresensi->currentPage() == 1) {
                                $end = min($datapresensi->lastPage(), 3);
                            } elseif ($datapresensi->currentPage() == $datapresensi->lastPage()) {
                                $start = max(1, $datapresensi->lastPage() - 2);
                            }
                        @endphp

                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $datapresensi->currentPage())
                                <span class="pagination-btn pagination-btn-active" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $datapresensi->url($page) }}" class="pagination-btn">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        {{-- Tombol Berikutnya --}}
                        @if ($datapresensi->hasMorePages())
                            <a href="{{ $datapresensi->nextPageUrl() }}" class="pagination-btn" title="Halaman Berikutnya">
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
    
    {{-- ===== DETAIL PRESENSI MODAL ===== --}}
    <div id="detailPresensiModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm modal-close"></div>
        <div class="relative bg-white rounded-[30px] w-full max-w-[360px] overflow-hidden shadow-2xl transition-all">
            <div class="p-6">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-bold text-gray-800">Detail Presensi</h3>
                    <button class="text-gray-400 hover:text-gray-600 modal-close">
                        <ion-icon name="close-circle-outline" style="font-size:28px;"></ion-icon>
                    </button>
                </div>

                <div id="modalContent">
                    <div class="mb-4">
                        <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal & Status</span>
                        <div class="flex justify-between items-center">
                            <span id="modalTanggal" class="text-lg font-bold text-gray-800"></span>
                            <span id="modalStatus" class="px-3 py-1 rounded-full text-xs font-bold text-white"></span>
                        </div>
                        <p id="modalKeterangan" class="text-sm text-gray-500 mt-1"></p>
                    </div>

                    <div id="modalMesinSection" class="mb-4 p-3 rounded-2xl bg-emerald-50 border border-emerald-100 hidden">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#1E4D3E] flex items-center justify-center text-white shrink-0">
                                <ion-icon name="finger-print" style="font-size:20px;"></ion-icon>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Fingerprint Machine</span>
                                <span id="modalNamaMesin" class="text-sm font-bold text-slate-900"></span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        {{-- Foto Masuk --}}
                        <div class="text-center">
                            <span class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Foto Masuk</span>
                            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100 border border-gray-100 shadow-sm mb-2">
                                <img id="modalImgIn" src="" class="w-full h-full object-cover hidden">
                                <div id="modalNoImgIn" class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                    <ion-icon name="camera-outline" style="font-size:32px;"></ion-icon>
                                    <span class="text-[10px] mt-1">No Photo</span>
                                </div>
                            </div>
                            <span id="modalJamIn" class="text-sm font-bold text-gray-700"></span>
                        </div>
                        {{-- Foto Pulang --}}
                        <div class="text-center">
                            <span class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Foto Pulang</span>
                            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100 border border-gray-100 shadow-sm mb-2">
                                <img id="modalImgOut" src="" class="w-full h-full object-cover hidden">
                                <div id="modalNoImgOut" class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                    <ion-icon name="camera-outline" style="font-size:32px;"></ion-icon>
                                    <span class="text-[10px] mt-1">No Photo</span>
                                </div>
                            </div>
                            <span id="modalJamOut" class="text-sm font-bold text-gray-700"></span>
                        </div>
                    </div>

                    <button class="w-full py-4 rounded-2xl bg-gray-100 text-gray-600 font-bold modal-close active:scale-95 transition-all">
                        Tutup Detail
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script>
        (function() {
            var localeIndo = {
                days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
                months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                today: 'Hari ini', clear: 'Hapus', dateFormat: 'yyyy-MM-dd', timeFormat: 'HH:mm', firstDay: 1
            };
            var btnToday = {
                content: 'Hari ini',
                className: 'air-datepicker-button-today',
                onClick: function(dp) {
                    var today = new Date();
                    dp.selectDate(today);
                    dp.setViewDate(today);
                }
            };
            var dpOpt = { locale: localeIndo, autoClose: true, isMobile: true, buttons: [btnToday, 'clear'], position: 'bottom center' };
            try {
                new AirDatepicker('#dari', dpOpt);
                new AirDatepicker('#sampai', dpOpt);
            } catch(e) {}

            function showSkeleton() { $('#data-container').hide(); $('#skeleton-container').show(); }
            function hideSkeleton() { $('#skeleton-container').hide(); $('#data-container').show(); }
            $('#formHistori').off('submit').on('submit', function() { showSkeleton(); });

            // Presensi Detail Modal Handler (Delegated to document for SPA stability)
            $(document).off('click.presensi', '.presensi-card').on('click.presensi', '.presensi-card', function() {
                var data = $(this).data();
                
                $("#modalTanggal").text(data.tanggal);
                $("#modalJamIn").text(data.jamIn);
                $("#modalJamOut").text(data.jamOut);
                $("#modalKeterangan").text(data.keterangan);
                
                // Machine Info
                if (data.namaMesin) {
                    $("#modalNamaMesin").text(data.namaMesin);
                    $("#modalMesinSection").show();
                } else {
                    $("#modalMesinSection").hide();
                }

                // Status Badge
                var statusMap = {
                    'h': { text: 'Hadir', color: 'bg-emerald-500' },
                    'i': { text: 'Izin', color: 'bg-blue-500' },
                    's': { text: 'Sakit', color: 'bg-rose-500' },
                    'c': { text: 'Cuti', color: 'bg-orange-500' },
                    'a': { text: 'Alpha', color: 'bg-slate-500' }
                };
                
                var status = statusMap[data.status] || { text: 'Alpha', color: 'bg-slate-500' };
                $("#modalStatus").text(status.text).removeClass().addClass('px-3 py-1 rounded-full text-xs font-bold text-white ' + status.color);

                var isAdmin = {{ auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->can('presensi.index')) ? 'true' : 'false' }};
                var archiveLabel = data.archiveMonth ? ' (Arsip: ' + data.archiveMonth + ')' : '';

                // Photo In
                if (data.isArchived == 1 && data.hasFotoIn == 1) {
                    $("#modalImgIn").hide();
                    $("#modalNoImgIn").show().find('span').text('Foto telah diarsipkan' + (isAdmin ? archiveLabel : ''));
                } else if (data.fotoIn) {
                    $("#modalImgIn").attr('src', data.fotoIn).show();
                    $("#modalNoImgIn").hide();
                } else {
                    $("#modalImgIn").hide();
                    $("#modalNoImgIn").show().find('span').text('Tidak ada foto');
                }

                // Photo Out
                if (data.isArchived == 1 && data.hasFotoOut == 1) {
                    $("#modalImgOut").hide();
                    $("#modalNoImgOut").show().find('span').text('Foto telah diarsipkan' + (isAdmin ? archiveLabel : ''));
                } else if (data.fotoOut) {
                    $("#modalImgOut").attr('src', data.fotoOut).show();
                    $("#modalNoImgOut").hide();
                } else {
                    $("#modalImgOut").hide();
                    $("#modalNoImgOut").show().find('span').text('Tidak ada foto');
                }

                $("#modalImgIn").off('error').on('error', function() {
                    $(this).hide();
                    $("#modalNoImgIn").show().find('span').text('Foto telah diarsipkan' + (isAdmin && data.archiveMonth ? ' (Arsip: ' + data.archiveMonth + ')' : ''));
                });
                $("#modalImgOut").off('error').on('error', function() {
                    $(this).hide();
                    $("#modalNoImgOut").show().find('span').text('Foto telah diarsipkan' + (isAdmin && data.archiveMonth ? ' (Arsip: ' + data.archiveMonth + ')' : ''));
                });

                $("#detailPresensiModal").fadeIn(300);
            });

            $(document).off('click.presensiclose', '.modal-close').on('click.presensiclose', '.modal-close', function() {
                $("#detailPresensiModal").fadeOut(200);
            });
        })();
    </script>
@endpush
