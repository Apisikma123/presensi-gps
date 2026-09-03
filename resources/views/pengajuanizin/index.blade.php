@extends('layouts.mobile.modern')


@section('title', 'Ajuan Izin')

@section('header_left')
    <a href="{{ route('dashboard.index') }}"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-90 transition-transform">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@section('content')

    {{-- ===== HISTORY LIST ===== --}}
    <div id="showhistori">
        {{-- Skeleton synced with dashboard & histori --}}
        <div id="skeleton-container" class="space-y-2">
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

        {{-- Data synced with dashboard & histori --}}
        <div id="data-container" style="display:none;" class="space-y-2">
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
                    } elseif ($d->ket == 'd') {
                        $route = 'izindinas.delete';
                        $ket_text = 'Izin Dinas';
                        $icon = 'airplane-outline';
                    } elseif ($d->ket == 'k') {
                        $route = 'koreksi.delete';
                        $ket_text = 'Koreksi Absen';
                        $icon = 'create-outline';
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
                    <div class="fade-up press mb-2.5 overflow-hidden cursor-pointer {{ $d->status_izin == 0 ? 'cancel-confirm' : '' }} bg-white rounded-2xl border border-slate-200/80 p-3 transition-all hover:border-[#1E4D3E]/40"
                        style="box-shadow: 0 1px 3px rgba(15,23,42,0.03); animation-delay: {{ $index * 0.04 }}s;">

                        <div class="flex items-center gap-3">
                            {{-- Date Badge --}}
                            <div class="shrink-0 w-12 h-12 flex flex-col items-center justify-center rounded-xl border"
                                 style="background: {{ $d->status_izin == 1 ? '#F0FDF4' : ($d->status_izin == 2 ? '#FEF2F2' : '#FFFBEB') }};
                                        color: {{ $d->status_izin == 1 ? '#1E4D3E' : ($d->status_izin == 2 ? '#DC2626' : '#D97706') }};
                                        border-color: {{ $d->status_izin == 1 ? '#DCFCE7' : ($d->status_izin == 2 ? '#FECACA' : '#FDE68A') }};">
                                <span class="text-[10px] font-bold uppercase tracking-wider leading-none">{{ $day_short }}</span>
                                <span class="text-[17px] font-black leading-tight mt-0.5 font-mono">{{ $tgl }}</span>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <ion-icon name="{{ $icon }}" class="text-slate-400 text-sm shrink-0"></ion-icon>
                                        <h3 class="text-[13px] font-bold text-slate-800 truncate m-0">
                                            {{ $ket_text }}
                                        </h3>
                                    </div>
                                    @if ($d->status_izin == 1)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Disetujui
                                        </span>
                                    @elseif ($d->status_izin == 2)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 border border-rose-200 shrink-0">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Pending
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <div class="flex items-center gap-1.5 text-[12px] font-mono text-slate-600 truncate">
                                        <ion-icon name="calendar-clear-outline" class="text-slate-400 text-xs"></ion-icon>
                                        <span>{{ DateToIndo($d->dari) }}</span>
                                        @if ($d->dari != $d->sampai)
                                            <span class="text-slate-300 font-sans">—</span>
                                            <span>{{ DateToIndo($d->sampai) }}</span>
                                        @endif
                                    </div>
                                    <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-mono font-bold text-[10px] shrink-0">
                                        {{ $jml_hari }} Hari
                                    </span>
                                </div>

                                @if (!empty($d->keterangan))
                                    <p class="text-[11px] text-slate-500 truncate m-0">
                                        {{ $d->keterangan }}
                                    </p>
                                @endif
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
                    <a href="{{ route('izindinas.create') }}" class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-slate-700 hover:bg-slate-50 active:bg-slate-100 transition-colors">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-teal-50 text-teal-600 shrink-0">
                            <ion-icon name="airplane-outline" class="text-lg"></ion-icon>
                        </div>
                        <span class="text-[13px] font-semibold">Izin Dinas</span>
                    </a>
                    <a href="{{ route('koreksi.create') }}" class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-slate-700 hover:bg-slate-50 active:bg-slate-100 transition-colors">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-purple-50 text-purple-600 shrink-0">
                            <ion-icon name="create-outline" class="text-lg"></ion-icon>
                        </div>
                        <span class="text-[13px] font-semibold">Koreksi Absen</span>
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
        function showSkeleton() { $('#data-container').hide(); $('#skeleton-container').show(); }
        function hideSkeleton() { $('#skeleton-container').fadeOut(200, function () { $('#data-container').fadeIn(300); }); }
        $(document).ready(function () {
            setTimeout(hideSkeleton, 400);

            // FAB Toggle logic
            let fabOpen = false;
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

            $('#fab-main').on('click', function (e) {
                e.stopPropagation();
                toggleFab();
            });

            $('#fab-backdrop').on('click', function () {
                toggleFab(false);
            });

            // Close FAB when clicking outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#fab-main, #fab-menu').length && fabOpen) {
                    toggleFab(false);
                }
            });

            // Delete confirmation
            $(".cancel-confirm").click(function (e) {
                var form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: 'Batalkan Pengajuan?',
                    text: "Data pengajuan ini akan dihapus permanen",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '{{ $t['primary'] }}',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Tutup',
                    borderRadius: '20px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            });
        });
    </script>
@endpush