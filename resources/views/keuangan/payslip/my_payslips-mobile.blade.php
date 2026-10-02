@extends('layouts.mobile.modern')

@section('title', 'Slip Gaji Saya')

@section('header_left')
    <a href="{{ route('dashboard.index') }}"
        class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-[0.98] transition-transform duration-150"
        title="Kembali ke Dashboard">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@section('content')
    <div class="px-3 pt-3 pb-24 space-y-3">
        {{-- Header Intro --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
                 style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb, 60, 42, 33), 0.08)); color: var(--color-primary);">
                <ion-icon name="cash-outline"></ion-icon>
            </div>
            <div>
                <h3 class="text-[14px] font-bold text-slate-800 leading-tight mb-0.5">Slip Gaji Digital</h3>
                <p class="text-[11.5px] text-slate-500 m-0">Riwayat slip gaji bulanan resmi yang diterbitkan bagian keuangan.</p>
            </div>
        </div>

        {{-- Payslips Card List --}}
        <div class="space-y-2.5">
            <div class="flex items-center justify-between px-1">
                <span class="text-[12.5px] font-bold text-slate-700">Daftar Periode Penggajian</span>
                <span class="text-[11px] text-slate-400">{{ $payslips->total() }} slip</span>
            </div>

            @forelse ($payslips as $ps)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400 font-mono block">
                                {{ $ps->period?->period_code ?? '-' }}
                            </span>
                            <h4 class="text-[14px] font-bold text-slate-800 m-0 leading-tight">
                                {{ $ps->period?->period_name ?? 'Periode Gaji' }}
                            </h4>
                            <span class="text-[11px] text-slate-500">
                                {{ date('F Y', mktime(0, 0, 0, $ps->period?->month ?? 1, 10, $ps->period?->year ?? date('Y'))) }}
                            </span>
                        </div>
                        <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Diterbitkan
                        </span>
                    </div>

                    {{-- Salary Figures --}}
                    <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                        <div>
                            <span class="text-[10px] text-slate-400 block mb-0.5">Bruto</span>
                            <span class="text-[11.5px] font-mono font-bold text-slate-700 block">
                                {{ number_format($ps->gross_salary, 0, ',', '.') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block mb-0.5">Potongan</span>
                            <span class="text-[11.5px] font-mono font-bold text-rose-600 block">
                                -{{ number_format($ps->total_deductions, 0, ',', '.') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block mb-0.5">Take Home Pay</span>
                            <span class="text-[12px] font-mono font-extrabold text-slate-900 block" style="color: var(--color-primary, #3C2A21);">
                                {{ number_format($ps->take_home_pay, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Action Buttons (2D Tactile) --}}
                    <div class="flex items-center gap-2 pt-1">
                        <a href="{{ route('payslip.show', $ps->id) }}"
                           class="flex-1 h-9 rounded-xl text-[12px] font-bold inline-flex items-center justify-center gap-1.5 transition-transform active:scale-[0.98] border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                            <ion-icon name="eye-outline" class="text-sm"></ion-icon>
                            <span>Rincian</span>
                        </a>
                        <a href="{{ route('payslip.print', $ps->id) }}" target="_blank"
                           class="flex-1 h-9 rounded-xl text-[12px] font-bold inline-flex items-center justify-center gap-1.5 transition-transform active:scale-[0.98]"
                           style="background: var(--color-primary, var(--theme-color-1, #3C2A21)); color: var(--theme-primary-contrast, #ffffff); border: none;">
                            <ion-icon name="print-outline" class="text-sm"></ion-icon>
                            <span>Cetak PDF</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <ion-icon name="receipt-outline" class="text-2xl"></ion-icon>
                    </div>
                    <h4 class="text-[14px] font-bold text-slate-700 mb-1">Belum Ada Slip Gaji</h4>
                    <p class="text-[12px] text-slate-400 m-0">Slip gaji bulanan Anda akan muncul di sini setelah diproses dan difinalisasi oleh tim payroll.</p>
                </div>
            @endforelse

            <div class="pt-2">
                {{ $payslips->links() }}
            </div>
        </div>
    </div>
@endsection
