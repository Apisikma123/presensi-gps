@extends('layouts.mobile.modern')

@section('title', 'Detail Kasbon')

@section('header_left')
    <a href="{{ route('loan.index') }}"
        class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-[0.98] transition-transform duration-150"
        title="Kembali">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@section('content')
    <div class="px-3 pt-3 pb-24 space-y-3">
        @php
            $statusStyles = [
                'PENDING' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Menunggu Persetujuan'],
                'ACTIVE' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Pinjaman Berjalan'],
                'PAID_OFF' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'label' => 'Lunas'],
                'REJECTED' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Pengajuan Ditolak'],
            ];
            $st = $statusStyles[$loan->status] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => $loan->status];
            $paidAmount = $loan->total_amount - $loan->remaining_amount;
            $percent = $loan->total_amount > 0 ? round(($paidAmount / $loan->total_amount) * 100) : 0;
        @endphp

        {{-- Main Loan Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[11px] font-bold font-mono text-slate-400 uppercase">{{ $loan->loan_number }}</span>
                <span class="inline-flex items-center text-[10.5px] font-bold px-2 py-0.5 rounded-md border {{ $st['bg'] }} {{ $st['text'] }} {{ $st['border'] }}">
                    {{ $st['label'] }}
                </span>
            </div>

            <div class="mb-3">
                <span class="text-[11px] text-slate-400 block mb-0.5">Total Nominal Kasbon</span>
                <h2 class="text-[22px] font-extrabold text-slate-800 font-mono m-0">
                    Rp {{ number_format($loan->total_amount, 0, ',', '.') }}
                </h2>
            </div>

            {{-- Progress Pelunasan --}}
            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                <div class="flex justify-between text-[11.5px]">
                    <span class="text-slate-500">Progres Pelunasan</span>
                    <span class="font-bold text-slate-700 font-mono">{{ $percent }}%</span>
                </div>
                <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300"
                         style="width: {{ $percent }}%; background: var(--color-primary, var(--theme-color-1, #3C2A21));"></div>
                </div>
                <div class="flex justify-between text-[11px] text-slate-400">
                    <span>Terbayar: Rp {{ number_format($paidAmount, 0, ',', '.') }}</span>
                    <span>Sisa: Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Loan Specs Bento --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm divide-y divide-slate-100 text-[12.5px]">
            <div class="py-2 flex justify-between">
                <span class="text-slate-500">Cicilan per Bulan</span>
                <span class="font-bold text-slate-800 font-mono">Rp {{ number_format($loan->monthly_installment, 0, ',', '.') }}</span>
            </div>
            <div class="py-2 flex justify-between">
                <span class="text-slate-500">Durasi Tenor</span>
                <span class="font-bold text-slate-800">{{ $loan->installment_months }} Bulan</span>
            </div>
            <div class="py-2 flex justify-between">
                <span class="text-slate-500">Mulai Pemotongan</span>
                <span class="font-bold text-slate-800">{{ date('d M Y', strtotime($loan->start_date)) }}</span>
            </div>
            @if ($loan->notes)
                <div class="py-2">
                    <span class="text-slate-500 block mb-0.5">Catatan Pengajuan:</span>
                    <p class="text-slate-700 m-0 italic">{{ $loan->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Jadwal Cicilan --}}
        @if ($loan->installments->isNotEmpty())
            <div class="space-y-2">
                <h4 class="text-[12.5px] font-bold text-slate-700 px-1">Jadwal Angsuran Bulanan</h4>
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden divide-y divide-slate-100">
                    @foreach ($loan->installments as $inst)
                        <div class="p-3 flex items-center justify-between text-[12px]">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-[11px] {{ $inst->status === 'PAID' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    #{{ $inst->installment_number }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 block">
                                        {{ date('d M Y', strtotime($inst->due_date)) }}
                                    </span>
                                    <span class="text-[10.5px] text-slate-400">Jatuh Tempo Potong Gaji</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-mono font-bold text-slate-800 block">
                                    Rp {{ number_format($inst->amount, 0, ',', '.') }}
                                </span>
                                @if ($inst->status === 'PAID')
                                    <span class="text-[10px] font-bold text-emerald-600">✓ Lunas</span>
                                @else
                                    <span class="text-[10px] font-medium text-slate-400">Belum dipotong</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
