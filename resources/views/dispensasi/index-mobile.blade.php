@extends('layouts.mobile.modern')

@section('title', 'Dispensasi Kehadiran')

@section('header_left')
    <a href="{{ route('dashboard.index') }}"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-90 transition-transform">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@section('header_right')
    <a href="{{ route('dispensasi.create') }}"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-90 transition-transform"
        title="Ajukan Dispensasi">
        <ion-icon name="add-outline" class="text-xl"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        .dispensasi-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.06);
            padding: 14px 16px;
            margin-bottom: 12px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .dispensasi-card:active {
            transform: scale(0.99);
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-pending {
            background-color: #FEF3C7;
            color: #D97706;
            border: 1px solid #FDE68A;
        }
        .status-approved {
            background-color: #D1FAE5;
            color: #059669;
            border: 1px solid #A7F3D0;
        }
        .status-rejected {
            background-color: #FEE2E2;
            color: #DC2626;
            border: 1px solid #FECACA;
        }
    </style>
@endpush

@section('content')
    <div class="px-1 pt-2 pb-24">
        {{-- Banner Info --}}
        <div class="bg-white rounded-2xl p-4 mb-4 border border-slate-200/80 shadow-xs flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgba(30, 77, 62, 0.1); color: #1E4D3E;">
                    <ion-icon name="time-outline" class="text-2xl"></ion-icon>
                </div>
                <div>
                    <h3 class="text-[13px] font-bold text-slate-800 m-0">Dispensasi Keterlambatan</h3>
                    <p class="text-[11px] text-slate-500 m-0">Pengajuan toleransi jam kehadiran masuk</p>
                </div>
            </div>
            <a href="{{ route('dispensasi.create') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-white text-[12px] font-bold shadow-xs active:scale-95 transition-transform"
               style="background: #1E4D3E;">
                <ion-icon name="add-outline" class="text-base"></ion-icon>
                <span>Ajukan</span>
            </a>
        </div>

        {{-- List of Dispensasi --}}
        <div class="space-y-3">
            @forelse ($dispensasi as $d)
                <div class="dispensasi-card">
                    <div class="flex items-start justify-between gap-2 mb-2 pb-2 border-b border-slate-100">
                        <div>
                            <span class="text-[12px] font-bold text-slate-900 block font-mono">
                                {{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('l, d F Y') }}
                            </span>
                            <span class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                <ion-icon name="alarm-outline" class="text-slate-400"></ion-icon>
                                Batas Jam Masuk: <strong class="text-slate-700 font-mono">{{ date('H:i', strtotime($d->batas_dispensasi)) }}</strong>
                            </span>
                        </div>
                        <div>
                            @if ($d->status === 'APPROVED')
                                <span class="status-badge status-approved">
                                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                                    Disetujui
                                </span>
                            @elseif ($d->status === 'REJECTED')
                                <span class="status-badge status-rejected">
                                    <ion-icon name="close-circle-outline"></ion-icon>
                                    Ditolak
                                </span>
                            @else
                                <span class="status-badge status-pending">
                                    <ion-icon name="time-outline"></ion-icon>
                                    Menunggu
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-[12px] text-slate-600 bg-slate-50 rounded-xl p-2.5 mb-2">
                        <span class="font-medium text-slate-500 block text-[10px] uppercase tracking-wider mb-0.5">Alasan:</span>
                        {{ $d->alasan }}
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                        <span>Diajukan: {{ \Carbon\Carbon::parse($d->created_at)->diffForHumans() }}</span>
                        @if ($d->status === 'PENDING')
                            <form action="{{ route('dispensasi.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan dispensasi ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold text-[11px] flex items-center gap-1">
                                    <ion-icon name="trash-outline"></ion-icon>
                                    <span>Batalkan</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 text-center border border-slate-100 shadow-xs mt-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-3" style="background: rgba(30, 77, 62, 0.08); color: #1E4D3E;">
                        <ion-icon name="calendar-outline" class="text-3xl"></ion-icon>
                    </div>
                    <h4 class="text-[14px] font-bold text-slate-800 mb-1">Belum Ada Pengajuan</h4>
                    <p class="text-[12px] text-slate-500 mb-4 max-w-xs mx-auto">Anda belum pernah mengajukan dispensasi keterlambatan presensi.</p>
                    <a href="{{ route('dispensasi.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-[13px] font-bold shadow-xs active:scale-95 transition-transform"
                       style="background: #1E4D3E;">
                        <ion-icon name="add-outline" class="text-lg"></ion-icon>
                        <span>Ajukan Dispensasi Sekarang</span>
                    </a>
                </div>
            @endforelse
        </div>

        @if ($dispensasi->hasPages())
            <div class="mt-4">
                {{ $dispensasi->links() }}
            </div>
        @endif
    </div>
@endsection
