@extends('layouts.mobile.modern')

@section('title', 'Peraturan Perusahaan')

@section('header_left')
    <a href="{{ route('dashboard.index') }}"
        class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/15 text-white active:scale-[0.98] transition-transform duration-150"
        title="Kembali ke Dashboard">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@section('content')
    <div class="px-3 pt-3 pb-24 space-y-3">
        {{-- Header Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
                 style="background: var(--color-primary-soft, rgba(27,54,93,0.08)); color: var(--color-primary, #1B365D);">
                <ion-icon name="shield-checkmark-outline"></ion-icon>
            </div>
            <div>
                <h3 class="text-[14px] font-bold text-slate-800 leading-tight mb-0.5">Peraturan & SOP Perusahaan</h3>
                <p class="text-[11.5px] text-slate-500 m-0">Pedoman operasional internal dan kode etik kerja.</p>
            </div>
        </div>

        {{-- Policies List --}}
        <div class="space-y-2.5">
            <div class="flex items-center justify-between px-1">
                <span class="text-[12.5px] font-bold text-slate-700">Daftar Kebijakan Aktif</span>
                <span class="text-[11px] text-slate-400">{{ $policies->count() }} dokumen</span>
            </div>

            @forelse ($policies as $policy)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-bold font-mono uppercase text-slate-400 block mb-0.5">
                                {{ $policy->policy_code }} • Versi {{ $policy->version }}
                            </span>
                            <h4 class="text-[14px] font-bold text-slate-800 m-0 leading-tight">
                                {{ $policy->title }}
                            </h4>
                        </div>
                        <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0"
                              style="background: var(--color-secondary-soft, rgba(71,85,105,0.08)); color: var(--color-secondary, #475569); border: 1px solid rgba(71,85,105,0.18);">
                            {{ $categories[$policy->category] ?? $policy->category }}
                        </span>
                    </div>

                    @if ($policy->description)
                        <div class="text-[12px] text-slate-600 pt-1 leading-relaxed border-t border-slate-100">
                            {{ $policy->description }}
                        </div>
                    @endif

                    <div class="pt-2 flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-50">
                        <span>Berlaku sejak: {{ date('d M Y', strtotime($policy->effective_date)) }}</span>
                        @if ($policy->attachment_path)
                            <a href="{{ asset('storage/' . $policy->attachment_path) }}" target="_blank"
                               class="text-[11px] font-bold text-emerald-600 inline-flex items-center gap-1 active:scale-[0.98]">
                                <ion-icon name="download-outline"></ion-icon> Unduh SOP
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <ion-icon name="document-text-outline" class="text-2xl"></ion-icon>
                    </div>
                    <h4 class="text-[14px] font-bold text-slate-700 mb-1">Belum Ada Peraturan</h4>
                    <p class="text-[12px] text-slate-400 m-0">Dokumen kebijakan dan SOP operasional belum dipublikasikan.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
