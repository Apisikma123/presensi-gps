@extends('layouts.mobile.modern')


@section('title', 'Ajuan Izin')

@section('header_left')
    <a href="{{ route('dashboard.index') }}"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-90 transition-transform">
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

    {{-- BENTO CARD: SISA CUTI (Brew & Beam Enterprise DESIGN.md) --}}
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
                        $ket_text = 'Izin Absen';
                        $icon = 'document-text-outline';
                    } elseif ($d->ket == 's') {
                        $route = 'izinsakit.delete';
                        $ket_text = 'Izin Sakit';
                        $icon = 'medkit-outline';
                    } elseif ($d->ket == 'c') {
                        $route = 'izincuti.delete';
                        $ket_text = 'Izin Cuti';
                        $icon = 'calendar-outline';
                    } else {
                        $route = 'izinabsen.delete';
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
                    $jml_hari = date_diff(date_create($d->dari), date_create($d->sampai))->format('%a') + 1;

                    // Status styles synced with histori but adapted for approval status
                    $statusStyles = [
                        '0' => ['label' => 'Pending', 'color' => '#ff9f40', 'rgb' => '255, 159, 64'],
                        '1' => ['label' => 'Disetujui', 'color' => $t['primary'], 'rgb' => '50, 116, 94'],
                        '2' => ['label' => 'Ditolak', 'color' => '#e74c3c', 'rgb' => '231, 76, 60'],
                    ];

                    $st = $statusStyles[$d->status_izin] ?? $statusStyles['0'];
                    $bgColor = "rgba({$st['rgb']}, 0.1)";
                @endphp

                <form method="POST" name="deleteform" class="deleteform" action="{{ route($route, Crypt::encrypt($d->kode)) }}">
                    @csrf
                    @method('DELETE')
                    <div class="fade-up press mb-2.5 overflow-hidden cursor-pointer {{ $d->status_izin == 0 ? 'cancel-confirm' : '' }} bg-white rounded-xl border border-slate-200/80 p-3 shadow-[0_1px_3px_rgba(15,23,42,0.03)] hover:border-slate-300 hover:shadow-sm transition-all duration-150 active:scale-[0.99]"
                        style="animation-delay: {{ $index * 0.04 }}s;">

                        <div class="flex items-center gap-3">
                            {{-- Date Badge --}}
                            <div class="shrink-0 w-11 h-11 flex flex-col items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-center">
                                <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 leading-none">{{ $day_short }}</span>
                                <span class="text-[16px] font-extrabold text-slate-800 font-mono leading-none mt-1">{{ $tgl }}</span>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                {{-- Row 1: Tanggal & Jenis --}}
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <h3 class="text-[13px] font-bold text-slate-800 truncate m-0 leading-tight">
                                        {{ DateToIndo($d->dari) }}@if ($d->dari != $d->sampai) <span class="text-slate-400 font-normal text-xs font-sans">— {{ DateToIndo($d->sampai) }}</span>@endif
                                    </h3>
                                    <span class="text-[10px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md shrink-0">
                                        {{ $ket_text }}
                                    </span>
                                </div>

                                {{-- Row 2: Durasi & Status Badge --}}
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-1.5 text-[12px] font-mono font-semibold text-slate-700 truncate">
                                        <ion-icon name="calendar-outline" class="text-[13px] text-slate-400 shrink-0"></ion-icon>
                                        <span>{{ $jml_hari }} Hari</span>
                                        @if (!empty($d->keterangan))
                                            <span class="text-slate-300 font-sans font-normal">—</span>
                                            <span class="font-sans font-normal text-slate-500 truncate text-[11px]">{{ $d->keterangan }}</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1 shrink-0">
                                        @if ($d->status_izin == 1)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                DISETUJUI
                                            </span>
                                        @elseif ($d->status_izin == 2)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-rose-50 text-rose-600 border border-rose-200/60 font-mono">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                DITOLAK
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200/60 font-mono">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                PENDING
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
        </div>
    </div>

    {{-- Floating Action Menu - DESIGN.md & Minimalist UI --}}
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

            // Delete confirmation
            $(document).off('click.cancelconfirm', '.cancel-confirm').on('click.cancelconfirm', '.cancel-confirm', function (e) {
                var form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: 'Batalkan Pengajuan?',
                    text: "Data pengajuan ini akan dihapus permanen",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '{{ $t['primary'] ?? '#1E4D3E' }}',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Tutup',
                    borderRadius: '20px'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        })();
    </script>
@endpush