@extends('layouts.mobile.modern')

@section('title', 'Detail SPK Lembur')

@section('header_left')
    <a href="{{ route('overtime.index') }}"
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
                'APPROVED' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Disetujui'],
                'REJECTED' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Ditolak'],
            ];
            $st = $statusStyles[$lembur->status] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => $lembur->status];
            
            $dayLabels = [
                'WORKDAY' => 'Hari Kerja Biasa',
                'OFFDAY_5DAYS' => 'Hari Libur Mingguan (5 Hari)',
                'OFFDAY_6DAYS' => 'Hari Libur Mingguan (6 Hari)',
                'PUBLIC_HOLIDAY' => 'Hari Libur Nasional',
            ];
        @endphp

        {{-- Main SPK Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[11px] font-bold font-mono text-slate-400 uppercase">{{ $lembur->no_spk }}</span>
                <span class="inline-flex items-center text-[10.5px] font-bold px-2 py-0.5 rounded-md border {{ $st['bg'] }} {{ $st['text'] }} {{ $st['border'] }}">
                    {{ $st['label'] }}
                </span>
            </div>

            <div class="mb-3">
                <span class="text-[11px] text-slate-400 block mb-0.5">Tanggal Pelaksanaan</span>
                <h3 class="text-[16px] font-extrabold text-slate-800 m-0">
                    {{ DateToIndo($lembur->tanggal) }}
                </h3>
                <span class="text-[12px] text-slate-500">{{ $dayLabels[$lembur->day_type] ?? $lembur->day_type }}</span>
            </div>

            {{-- Hours Bento --}}
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-center">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10.5px] text-slate-400 block mb-0.5">Durasi Nyata</span>
                    <span class="text-[16px] font-mono font-extrabold text-slate-800 block">
                        {{ $lembur->duration_hours }} <span class="text-[11px] font-sans font-normal text-slate-500">Jam</span>
                    </span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10.5px] text-slate-400 block mb-0.5">Jam Pengali (Depnaker)</span>
                    <span class="text-[16px] font-mono font-extrabold text-emerald-600 block">
                        {{ $lembur->calculated_rate_hours }} <span class="text-[11px] font-sans font-normal text-slate-500">Jam</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Details List --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm divide-y divide-slate-100 text-[12.5px]">
            <div class="py-2 flex justify-between">
                <span class="text-slate-500">Waktu Mulai</span>
                <span class="font-bold text-slate-800 font-mono">{{ date('H:i, d M Y', strtotime($lembur->lembur_mulai)) }}</span>
            </div>
            <div class="py-2 flex justify-between">
                <span class="text-slate-500">Waktu Selesai</span>
                <span class="font-bold text-slate-800 font-mono">{{ date('H:i, d M Y', strtotime($lembur->lembur_selesai)) }}</span>
            </div>
            @if ($lembur->policy)
                <div class="py-2 flex justify-between">
                    <span class="text-slate-500">Kebijakan</span>
                    <span class="font-bold text-slate-800">{{ $lembur->policy->policy_name }}</span>
                </div>
            @endif
            @if ($lembur->approver)
                <div class="py-2 flex justify-between">
                    <span class="text-slate-500">Disetujui Oleh</span>
                    <span class="font-bold text-slate-800">{{ $lembur->approver->name }}</span>
                </div>
            @endif
            <div class="py-2">
                <span class="text-slate-500 block mb-0.5">Uraian Tugas / Instruksi:</span>
                <p class="text-slate-700 m-0 italic">{{ $lembur->keterangan }}</p>
            </div>
        </div>
    </div>
@endsection
