@extends('layouts.mobile.modern')

@section('title', 'Pengumuman')

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
                <ion-icon name="megaphone-outline"></ion-icon>
            </div>
            <div>
                <h3 class="text-[14px] font-bold text-slate-800 leading-tight mb-0.5">Pengumuman Internal</h3>
                <p class="text-[11.5px] text-slate-500 m-0">Surat edaran dan informasi resmi bagi seluruh karyawan.</p>
            </div>
        </div>

        {{-- Announcements List --}}
        <div class="space-y-2.5">
            @forelse ($announcements as $ann)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm space-y-2.5 transition-all"
                     style="{{ $ann->is_pinned ? 'border-color: var(--color-primary, #1B365D); box-shadow: 0 4px 14px var(--color-primary-soft, rgba(27,54,93,0.08));' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="space-y-0.5">
                            @if ($ann->is_pinned)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md mb-1"
                                      style="background: var(--color-primary-soft, rgba(27,54,93,0.08)); color: var(--color-primary, #1B365D); border: 1px solid var(--theme-border, rgba(27,54,93,0.18));">
                                    <ion-icon name="pin" class="text-xs"></ion-icon> Disematkan
                                </span>
                            @endif
                            <h4 class="text-[14.5px] font-bold text-slate-800 m-0 leading-tight">
                                {{ $ann->title }}
                            </h4>
                            <span class="text-[11px] text-slate-400 block">
                                {{ date('d M Y, H:i', strtotime($ann->published_at ?? $ann->created_at)) }}
                            </span>
                        </div>
                        <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0"
                              style="background: var(--color-secondary-soft, rgba(71,85,105,0.08)); color: var(--color-secondary, #475569); border: 1px solid rgba(71,85,105,0.18);">
                            {{ $categories[$ann->category] ?? $ann->category }}
                        </span>
                    </div>

                    <div class="text-[12.5px] text-slate-700 pt-1 leading-relaxed border-t border-slate-100 whitespace-pre-line">
                        {{ $ann->content }}
                    </div>

                    @if ($ann->author)
                        <div class="pt-2 flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-50">
                            <span>Diterbitkan oleh: <strong style="color: var(--color-primary, #1B365D);">{{ $ann->author->name }}</strong></span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <ion-icon name="notifications-off-outline" class="text-2xl"></ion-icon>
                    </div>
                    <h4 class="text-[14px] font-bold text-slate-700 mb-1">Belum Ada Pengumuman</h4>
                    <p class="text-[12px] text-slate-400 m-0">Saat ini belum ada pengumuman terbaru dari manajemen perusahaan.</p>
                </div>
            @endforelse

            <div class="pt-2">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
@endsection
