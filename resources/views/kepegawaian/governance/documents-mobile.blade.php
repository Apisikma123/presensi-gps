@extends('layouts.mobile.modern')

@section('title', 'Brankas Dokumen')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('dashboard.index') }}"
        onclick="if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) { event.preventDefault(); window.history.back(); }"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all"
        title="Kembali">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@section('content')
    <div class="px-3 pt-3 pb-24 space-y-3">
        {{-- Flash Alerts --}}
        @if (session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs flex items-center gap-2">
                <ion-icon name="checkmark-circle" class="text-base text-emerald-600 shrink-0"></ion-icon>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs space-y-1">
                <div class="flex items-center gap-1.5 font-bold">
                    <ion-icon name="alert-circle" class="text-base text-rose-600 shrink-0"></ion-icon>
                    <span>Gagal mengunggah berkas:</span>
                </div>
                <ul class="list-disc pl-5 space-y-0.5 m-0 text-[11.5px]">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Header Card & Upload CTA --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
                     style="background: var(--color-primary-soft, rgba(27,54,93,0.08)); color: var(--color-primary);">
                    <ion-icon name="folder-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="text-[14px] font-bold text-slate-800 leading-tight mb-0.5">Brankas Dokumen Saya</h3>
                    <p class="text-[11.5px] text-slate-500 m-0">Arsip berkas identitas & dokumen resmi Anda.</p>
                </div>
            </div>

            <a href="{{ route('document.create') }}"
               class="shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 transition-transform active:scale-[0.98] border border-transparent shadow-sm"
               style="background: var(--color-primary, {{ $t['primary'] ?? '#1B365D' }}); color: var(--color-primary-contrast, #ffffff);">
                <ion-icon name="cloud-upload-outline" class="text-sm"></ion-icon>
                <span>Unggah</span>
            </a>
        </div>

        {{-- Documents List --}}
        <div class="space-y-2.5">
            <div class="flex items-center justify-between px-1">
                <span class="text-[12.5px] font-bold text-slate-700">Berkas Terlampir</span>
                <span class="text-[11px] text-slate-400">{{ $documents->total() }} dokumen</span>
            </div>

            @forelse ($documents as $doc)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg shrink-0">
                                <ion-icon name="document-text-outline"></ion-icon>
                            </div>
                            <div>
                                <h4 class="text-[13.5px] font-bold text-slate-800 m-0 leading-tight">
                                    {{ $doc->title }}
                                </h4>
                                <span class="text-[11px] text-slate-400">
                                    Diunggah {{ date('d M Y', strtotime($doc->created_at)) }}
                                </span>
                            </div>
                        </div>
                        <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0"
                              style="background: var(--color-secondary-soft, rgba(71,85,105,0.08)); color: var(--color-secondary, #475569); border: 1px solid rgba(71,85,105,0.18);">
                            {{ $types[$doc->document_type] ?? $doc->document_type }}
                        </span>
                    </div>

                    @if ($doc->notes)
                        <p class="text-[11.5px] text-slate-500 m-0 italic pt-1 border-t border-slate-50">
                            "{{ $doc->notes }}"
                        </p>
                    @endif

                    <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                        <span class="text-[11px] text-slate-400">
                            @if ($doc->expiry_date)
                                Exp: {{ date('d M Y', strtotime($doc->expiry_date)) }}
                            @else
                                Berlaku Tetap
                            @endif
                        </span>
                        @if ($doc->file_path)
                            <a href="{{ route('file.document', $doc->id) }}" target="_blank"
                               class="px-3 py-1.5 rounded-xl text-[11.5px] font-bold inline-flex items-center gap-1.5 transition-transform active:scale-[0.98] border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100">
                                <ion-icon name="cloud-download-outline" class="text-sm"></ion-icon>
                                <span>Unduh</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <ion-icon name="folder-open-outline" class="text-2xl"></ion-icon>
                    </div>
                    <h4 class="text-[14px] font-bold text-slate-700 mb-1">Belum Ada Dokumen</h4>
                    <p class="text-[12px] text-slate-400 m-0 mb-4">Berkas administrasi Anda belum diunggah ke brankas sistem.</p>
                    <a href="{{ route('document.create') }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 active:scale-[0.98] transition-transform"
                       style="background: var(--color-primary, {{ $t['primary'] ?? '#1B365D' }}); color: var(--color-primary-contrast, #ffffff);">
                        <ion-icon name="cloud-upload-outline"></ion-icon>
                        <span>Unggah Dokumen Pertama</span>
                    </a>
                </div>
            @endforelse

            <div class="pt-2">
                {{ $documents->links() }}
            </div>
        </div>
    </div>
@endsection
