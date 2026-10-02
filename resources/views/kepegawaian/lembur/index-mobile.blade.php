@extends('layouts.mobile.modern')

@section('title', 'Lembur & SPK')

@section('header_left')
    <a href="{{ route('dashboard.index') }}"
        class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-[0.98] transition-transform duration-150"
        title="Kembali ke Dashboard">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@section('header_right')
    <a href="{{ route('overtime.create') }}"
        class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-[0.98] transition-transform duration-150"
        title="Ajukan Lembur">
        <ion-icon name="add-outline" class="text-xl"></ion-icon>
    </a>
@endsection

@section('content')
    <div class="px-3 pt-3 pb-24 space-y-3">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-[12.5px] flex items-center gap-2">
                <ion-icon name="checkmark-circle-outline" class="text-lg text-emerald-600 shrink-0"></ion-icon>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-[12.5px] flex items-center gap-2">
                <ion-icon name="alert-circle-outline" class="text-lg text-rose-600 shrink-0"></ion-icon>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Metrics Row --}}
        <div class="grid grid-cols-2 gap-2.5">
            <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-sm">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Jam Disetujui</span>
                <span class="text-[18px] font-extrabold text-slate-800 font-mono block">
                    {{ $stats['total_rate_hours_month'] ?? 0 }} <span class="text-[12px] font-sans font-medium text-slate-400">Jam</span>
                </span>
                <span class="text-[11px] text-slate-400 mt-1 block">Bulan ini</span>
            </div>
            <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-sm">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Status Pengajuan</span>
                <span class="text-[18px] font-extrabold text-amber-600 font-mono block">
                    {{ $stats['pending_approval'] ?? 0 }} <span class="text-[12px] font-sans font-medium text-slate-400">Menunggu</span>
                </span>
                <span class="text-[11px] text-slate-400 mt-1 block">{{ $stats['approved_month'] ?? 0 }} Disetujui</span>
            </div>
        </div>

        {{-- Action Bar --}}
        <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-sm flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h4 class="text-[13px] font-bold text-slate-800 leading-tight mb-0.5">Kerja di Luar Jam?</h4>
                <p class="text-[11.5px] text-slate-500 m-0">Buat SPK lembur dan submit sebelum shift lembur dimulai.</p>
            </div>
            <a href="{{ route('overtime.create') }}"
               class="shrink-0 px-3.5 py-2 rounded-xl text-[12px] font-bold inline-flex items-center gap-1.5 transition-transform active:scale-[0.97]"
               style="background: var(--color-primary, var(--theme-color-1, #3C2A21)); color: var(--theme-primary-contrast, #ffffff);">
                <ion-icon name="add-outline" class="text-sm"></ion-icon>
                <span>Ajukan</span>
            </a>
        </div>

        {{-- SPK List --}}
        <div class="space-y-2.5">
            <div class="flex items-center justify-between px-1">
                <span class="text-[12.5px] font-bold text-slate-700">Daftar SPK Lembur Saya</span>
                <span class="text-[11px] text-slate-400">{{ $lemburs->total() }} catatan</span>
            </div>

            @forelse ($lemburs as $lembur)
                @php
                    $statusStyles = [
                        'PENDING' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Menunggu'],
                        'APPROVED' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Disetujui'],
                        'REJECTED' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Ditolak'],
                    ];
                    $st = $statusStyles[$lembur->status] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => $lembur->status];
                    
                    $dayLabels = [
                        'WORKDAY' => 'Hari Kerja',
                        'OFFDAY_5DAYS' => 'Hari Libur (5 Hari)',
                        'OFFDAY_6DAYS' => 'Hari Libur (6 Hari)',
                        'PUBLIC_HOLIDAY' => 'Hari Libur Nasional',
                    ];
                @endphp
                <a href="{{ route('overtime.show', $lembur->id) }}"
                   class="block bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm hover:border-slate-300 transition-all duration-150 active:scale-[0.99] text-decoration-none">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <span class="text-[10.5px] font-bold font-mono uppercase text-slate-400 block mb-0.5">
                                {{ $lembur->no_spk }}
                            </span>
                            <h4 class="text-[13.5px] font-bold text-slate-800 m-0 leading-tight">
                                {{ DateToIndo($lembur->tanggal) }}
                            </h4>
                            <span class="text-[11px] text-slate-500">
                                {{ $dayLabels[$lembur->day_type] ?? $lembur->day_type }}
                            </span>
                        </div>
                        <span class="inline-flex items-center text-[10.5px] font-bold px-2 py-0.5 rounded-md border {{ $st['bg'] }} {{ $st['text'] }} {{ $st['border'] }} shrink-0">
                            {{ $st['label'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-[11.5px]">
                        <div class="flex items-center gap-1.5 text-slate-600 font-mono">
                            <ion-icon name="time-outline" class="text-sm text-slate-400"></ion-icon>
                            <span>{{ date('H:i', strtotime($lembur->lembur_mulai)) }} - {{ date('H:i', strtotime($lembur->lembur_selesai)) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-700 font-mono">{{ $lembur->duration_hours }} Jam Kerja</span>
                            @if ($lembur->calculated_rate_hours > 0)
                                <span class="text-[10.5px] text-emerald-600 block">({{ $lembur->calculated_rate_hours }} Jam Pengali)</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <ion-icon name="time-outline" class="text-2xl"></ion-icon>
                    </div>
                    <h4 class="text-[14px] font-bold text-slate-700 mb-1">Belum Ada SPK Lembur</h4>
                    <p class="text-[12px] text-slate-400 m-0">Anda belum memiliki riwayat pengajuan surat perintah kerja lembur.</p>
                </div>
            @endforelse

            <div class="pt-2">
                {{ $lemburs->links() }}
            </div>
        </div>
    </div>
@endsection
