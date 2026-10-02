@extends('layouts.mobile.modern')

@section('title', 'Klaim & Reimbursement')

@section('header_left')
    <a href="{{ route('dashboard.index') }}"
        class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-[0.98] transition-transform duration-150"
        title="Kembali ke Dashboard">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@section('header_right')
    <a href="{{ route('reimbursement.create') }}"
        class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-[0.98] transition-transform duration-150"
        title="Ajukan Klaim">
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
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Klaim Terbayar</span>
                <span class="text-[17px] font-extrabold text-emerald-600 font-mono block">
                    Rp {{ number_format($stats['paid_amount'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-1 block">{{ $stats['total_approved'] ?? 0 }} Klaim Disetujui</span>
            </div>
            <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-sm">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Menunggu Review</span>
                <span class="text-[17px] font-extrabold text-amber-600 font-mono block">
                    Rp {{ number_format($stats['pending_amount'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 mt-1 block">{{ $stats['total_submitted'] ?? 0 }} Pengajuan Baru</span>
            </div>
        </div>

        {{-- Quick CTA --}}
        <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-sm flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h4 class="text-[13px] font-bold text-slate-800 leading-tight mb-0.5">Ada Biaya Operasional?</h4>
                <p class="text-[11.5px] text-slate-500 m-0">Klaim pengeluaran kerja atau medis dengan melampirkan kuitansi/struk.</p>
            </div>
            <a href="{{ route('reimbursement.create') }}"
               class="shrink-0 px-3.5 py-2 rounded-xl text-[12px] font-bold inline-flex items-center gap-1.5 transition-transform active:scale-[0.97]"
               style="background: var(--color-primary, var(--theme-color-1, #3C2A21)); color: var(--theme-primary-contrast, #ffffff);">
                <ion-icon name="add-outline" class="text-sm"></ion-icon>
                <span>Klaim</span>
            </a>
        </div>

        {{-- Claims List --}}
        <div class="space-y-2.5">
            <div class="flex items-center justify-between px-1">
                <span class="text-[12.5px] font-bold text-slate-700">Riwayat Pengajuan Klaim</span>
                <span class="text-[11px] text-slate-400">{{ $reimbursements->total() }} catatan</span>
            </div>

            @forelse ($reimbursements as $claim)
                @php
                    $statusStyles = [
                        'SUBMITTED' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Menunggu'],
                        'APPROVED' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'label' => 'Disetujui'],
                        'PAID' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Terbayar'],
                        'REJECTED' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Ditolak'],
                    ];
                    $st = $statusStyles[$claim->status] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => $claim->status];
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-bold font-mono uppercase text-slate-400 block mb-0.5">
                                {{ $claim->claim_number }}
                            </span>
                            <h4 class="text-[14px] font-bold text-slate-800 m-0 leading-tight">
                                {{ $claim->type?->name ?? 'Reimbursement' }}
                            </h4>
                            <span class="text-[11px] text-slate-500">
                                {{ DateToIndo($claim->claim_date) }}
                            </span>
                        </div>
                        <span class="inline-flex items-center text-[10.5px] font-bold px-2 py-0.5 rounded-md border {{ $st['bg'] }} {{ $st['text'] }} {{ $st['border'] }} shrink-0">
                            {{ $st['label'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[12px]">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Nominal Diajukan</span>
                            <span class="text-[15px] font-mono font-extrabold text-slate-800">
                                Rp {{ number_format($claim->amount, 0, ',', '.') }}
                            </span>
                        </div>
                        @if ($claim->receipt_path)
                            <a href="{{ asset('storage/' . $claim->receipt_path) }}" target="_blank"
                               class="px-2.5 py-1 rounded-lg border border-slate-200 text-[11px] font-bold text-slate-600 bg-slate-50 inline-flex items-center gap-1 active:scale-[0.98]">
                                <ion-icon name="document-attach-outline"></ion-icon>
                                <span>Bukti Struk</span>
                            </a>
                        @endif
                    </div>

                    @if ($claim->description)
                        <p class="text-[11.5px] text-slate-600 m-0 pt-1 italic border-t border-slate-50">
                            "{{ $claim->description }}"
                        </p>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <ion-icon name="receipt-outline" class="text-2xl"></ion-icon>
                    </div>
                    <h4 class="text-[14px] font-bold text-slate-700 mb-1">Belum Ada Klaim</h4>
                    <p class="text-[12px] text-slate-400 m-0">Anda belum pernah mengajukan reimbursement biaya operasional.</p>
                </div>
            @endforelse

            <div class="pt-2">
                {{ $reimbursements->links() }}
            </div>
        </div>
    </div>
@endsection
