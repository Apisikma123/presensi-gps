@extends('layouts.app')
@section('titlepage', 'Log Jejak Audit Sistem')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.hub') }}">Pengaturan Sistem</a></li>
    <li class="breadcrumb-item active">Audit Trail</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Log Jejak Audit (Audit Trail System)</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($logs->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Pencatatan riwayat transaksi administratif, perubahan master data, otorisasi, dan forensik keamanan.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('settings.audit_logs.export', request()->query()) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-download" style="font-size: 16px;"></i>
            <span>Ekspor CSV Log</span>
        </a>
        <a href="{{ route('settings.hub') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-arrow-left" style="font-size: 16px;"></i>
            <span>Kembali ke Direktori</span>
        </a>
    </div>
</div>

{{-- Metric Summary Cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px; background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary);">
                        <i class="ti ti-file-text fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Aktivitas</div>
                        <h4 class="mb-0 fw-bold font-mono text-dark">{{ number_format($metrics['total_logs']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px; background: rgba(74, 103, 65, 0.1); color: #4A6741;">
                        <i class="ti ti-calendar-time fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Aktivitas Hari Ini</div>
                        <h4 class="mb-0 fw-bold font-mono text-success">{{ number_format($metrics['logs_today']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px; background: rgba(115, 103, 240, 0.1); color: #7367f0;">
                        <i class="ti ti-user-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Operator Aktif</div>
                        <h4 class="mb-0 fw-bold font-mono text-dark">{{ number_format($metrics['unique_users_today']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 40px; height: 40px; background: rgba(40, 199, 111, 0.1); color: #28c76f;">
                        <i class="ti ti-shield-lock fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Integritas Log</div>
                        <span class="badge bg-label-success font-mono" style="font-size: 11px;">100% Terenkripsi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Toolbar --}}
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('settings.audit_logs.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-shrink-0" style="min-width: 150px;">
                <select name="module" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $m)
                        <option value="{{ $m }}" {{ request('module') == $m ? 'selected' : '' }}>{{ strtoupper($m) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-shrink-0" style="min-width: 140px;">
                <select name="action" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $a)
                        <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow-1" style="min-width: 200px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari NIK / IP / keyword..." hideLabel="true" />
            </div>
            <div class="flex-shrink-0" style="min-width: 150px;">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" style="border-radius: 8px;" title="Dari Tanggal">
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari Data</span>
                </button>
                @if (request('module') || request('action') || request('search') || request('date_from'))
                    <a href="{{ route('settings.audit_logs.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Table Card --}}
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;" class="text-center">ID</th>
                    <th style="width: 150px;">WAKTU</th>
                    <th>PENGGUNA / OPERATOR</th>
                    <th style="width: 110px;">AKSI</th>
                    <th style="width: 120px;">MODUL</th>
                    <th style="width: 110px;">REKORD</th>
                    <th style="width: 130px;">IP ADDRESS</th>
                    <th>DETAIL AKTIVITAS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="text-center font-mono text-muted" style="font-size: 12px;">#{{ $log->id }}</td>
                        <td class="font-mono text-muted" style="font-size: 12px; white-space: nowrap;">
                            {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark" style="font-size: 13px;">{{ $log->user ? $log->user->name : 'Sistem' }}</div>
                            <div class="text-muted font-mono" style="font-size: 11px;">{{ $log->user ? $log->user->email : 'system-job' }}</div>
                        </td>
                        <td>
                            @php
                                $badgeClass = 'bg-label-primary';
                                if (in_array($log->action, ['CREATE', 'APPROVE', 'APPLY_PRESET'])) $badgeClass = 'bg-label-success';
                                elseif (in_array($log->action, ['DELETE', 'REJECT'])) $badgeClass = 'bg-label-danger';
                                elseif (in_array($log->action, ['UPDATE'])) $badgeClass = 'bg-label-warning';
                            @endphp
                            <span class="badge {{ $badgeClass }} font-mono" style="font-size: 11px;">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                {{ strtoupper($log->module) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="font-size: 11px;">
                                {{ $log->record_id ?? '-' }}
                            </span>
                        </td>
                        <td class="font-mono text-muted" style="font-size: 12px;">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                        <td style="max-width: 320px;">
                            @if(is_array($log->details))
                                <div class="font-mono" style="font-size: 11.5px; color: #334155; max-height: 48px; overflow-y: auto;">
                                    @foreach($log->details as $k => $v)
                                        <span><strong>{{ $k }}:</strong> {{ is_array($v) ? json_encode($v) : $v }}</span><br>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted" style="font-size: 12px;">{{ $log->details ?? '-' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Belum ada log jejak audit yang terekam atau cocok dengan kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($logs->hasPages())
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif
@endsection
