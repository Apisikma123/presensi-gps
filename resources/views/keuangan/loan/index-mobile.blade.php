@extends('layouts.mobile.modern')

@section('title', 'Pinjaman & Kasbon')

@section('header_left')
    <a href="{{ route('dashboard.index') }}"
        class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-[0.98] transition-transform duration-150"
        title="Kembali ke Dashboard">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@section('header_right')
    <a href="{{ route('loan.create') }}"
        class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-[0.98] transition-transform duration-150"
        title="Ajukan Kasbon">
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

        {{-- Summary Metric Card --}}
        <div class="grid grid-cols-2 gap-2.5">
            <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-sm">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Sisa Kasbon</span>
                <span class="text-[17px] font-extrabold text-slate-800 font-mono block">
                    Rp {{ number_format($stats['total_remaining'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-1 block">
                    {{ $stats['active_loans'] ?? 0 }} Pinjaman Aktif
                </span>
            </div>
            <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-sm">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Diajukan</span>
                <span class="text-[17px] font-extrabold text-slate-800 font-mono block">
                    Rp {{ number_format($stats['total_disbursed'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-1 block">
                    {{ $stats['total_loans'] ?? 0 }} Pengajuan
                </span>
            </div>
        </div>

        {{-- Quick CTA --}}
        <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-sm flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h4 class="text-[13px] font-bold text-slate-800 leading-tight mb-0.5">Butuh Dana Mendesak?</h4>
                <p class="text-[11.5px] text-slate-500 m-0">Ajukan kasbon dengan cicilan potong gaji otomatis.</p>
            </div>
            <a href="{{ route('loan.create') }}"
               class="shrink-0 px-3.5 py-2 rounded-xl text-[12px] font-bold inline-flex items-center gap-1.5 transition-transform active:scale-[0.97]"
               style="background: var(--color-primary, var(--theme-color-1, #3C2A21)); color: var(--theme-primary-contrast, #ffffff);">
                <ion-icon name="add-outline" class="text-sm"></ion-icon>
                <span>Ajukan</span>
            </a>
        </div>

        {{-- List Kasbon Karyawan --}}
        <div class="space-y-2.5">
            <div class="flex items-center justify-between px-1">
                <span class="text-[12.5px] font-bold text-slate-700">Riwayat Pengajuan Kasbon</span>
                <span class="text-[11px] text-slate-400">{{ $loans->total() }} catatan</span>
            </div>

            @forelse ($loans as $loan)
                @php
                    $statusStyles = [
                        'PENDING' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Menunggu'],
                        'ACTIVE' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Berjalan'],
                        'PAID_OFF' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'label' => 'Lunas'],
                        'REJECTED' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Ditolak'],
                    ];
                    $st = $statusStyles[$loan->status] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => $loan->status];
                @endphp
                <a href="{{ route('loan.show', $loan->id) }}"
                   class="block bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm hover:border-slate-300 transition-all duration-150 active:scale-[0.99] text-decoration-none">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <span class="text-[10.5px] font-bold font-mono uppercase text-slate-400 block mb-0.5">
                                {{ $loan->loan_number }}
                            </span>
                            <h3 class="text-[15px] font-extrabold text-slate-800 font-mono m-0 leading-tight">
                                Rp {{ number_format($loan->total_amount, 0, ',', '.') }}
                            </h3>
                        </div>
                        <span class="inline-flex items-center text-[10.5px] font-bold px-2 py-0.5 rounded-md border {{ $st['bg'] }} {{ $st['text'] }} {{ $st['border'] }} shrink-0">
                            {{ $st['label'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-[11.5px]">
                        <div>
                            <span class="text-slate-400 block text-[10px]">Cicilan / Bulan</span>
                            <span class="font-mono font-bold text-slate-700">
                                Rp {{ number_format($loan->monthly_installment, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-400 block text-[10px]">Tenor / Sisa</span>
                            <span class="font-bold text-slate-700">
                                {{ $loan->installment_months }} Bln <span class="text-slate-300">|</span> <span class="font-mono text-emerald-600">Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}</span>
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <ion-icon name="wallet-outline" class="text-2xl"></ion-icon>
                    </div>
                    <h4 class="text-[14px] font-bold text-slate-700 mb-1">Belum Ada Pengajuan</h4>
                    <p class="text-[12px] text-slate-400 m-0">Anda belum pernah mengajukan pinjaman atau kasbon operasional.</p>
                </div>
            @endforelse

            <div class="pt-2">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
@endsection
