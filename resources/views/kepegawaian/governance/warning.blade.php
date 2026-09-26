@extends('layouts.app')
@section('titlepage', 'Disiplin & Surat Peringatan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Surat Peringatan</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Disiplin & Surat Peringatan (SP)</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($warnings->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Tata tertib kerja, hubungan industrial, dan penerbitan sanksi kedisiplinan karyawan.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalAddWarning" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-alert-triangle" style="font-size: 16px;"></i>
            <span>Terbitkan Surat Peringatan</span>
        </button>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('warning.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari nomor SP, nama karyawan, atau NIK..." hideLabel="true" />
            </div>
            <div class="flex-shrink-0" style="min-width: 180px;">
                <select name="level" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Tingkat Sanksi</option>
                    <option value="TEGURAN_LISAN" {{ request('level') === 'TEGURAN_LISAN' ? 'selected' : '' }}>Teguran Lisan</option>
                    <option value="SP_1" {{ request('level') === 'SP_1' ? 'selected' : '' }}>Surat Peringatan I (SP 1)</option>
                    <option value="SP_2" {{ request('level') === 'SP_2' ? 'selected' : '' }}>Surat Peringatan II (SP 2)</option>
                    <option value="SP_3" {{ request('level') === 'SP_3' ? 'selected' : '' }}>Surat Peringatan III (SP 3)</option>
                    <option value="SKORSING" {{ request('level') === 'SKORSING' ? 'selected' : '' }}>Skorsing</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari Data</span>
                </button>
                @if(request()->hasAny(['search', 'level']))
                    <a href="{{ route('warning.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Warnings Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th>NOMOR & TINGKAT SP</th>
                    <th>KARYAWAN</th>
                    <th>URAIAN PELANGGARAN & PASAL</th>
                    <th>TANGGAL BERLAKU</th>
                    <th>BERLAKU HINGGA</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warnings as $w)
                @php
                    $isExpired = $w->expiry_date->isPast();
                @endphp
                <tr>
                    <td>
                        <div class="font-mono text-muted small">{{ $w->sp_number }}</div>
                        <div class="mt-1">{!! $w->level_badge_html !!}</div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $w->karyawan->nama_karyawan ?? 'Karyawan' }}</div>
                        <div class="text-muted small font-mono">NIK: {{ $w->nik }} &bull; {{ $w->karyawan->departemen->nama_dept ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="text-dark small text-truncate" style="max-width: 280px;" title="{{ $w->violation_description }}">
                            {{ $w->violation_description }}
                        </div>
                        @if($w->pasal_pelanggaran)
                            <div class="text-muted small font-mono">Pasal: {{ $w->pasal_pelanggaran }}</div>
                        @endif
                    </td>
                    <td>
                        <div>{{ $w->effective_date->format('d M Y') }}</div>
                        <div class="text-muted small">Insiden: {{ $w->incident_date->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div class="{{ $isExpired ? 'text-muted' : 'text-danger fw-bold' }}">
                            {{ $w->expiry_date->format('d M Y') }}
                        </div>
                        <div class="text-muted small">{{ $isExpired ? 'Sudah Lewat 6 Bulan' : 'Masa Aktif 6 Bulan' }}</div>
                    </td>
                    <td>
                        @if($isExpired)
                            <span class="badge bg-light text-muted">Kedaluwarsa</span>
                        @else
                            {!! $w->status_badge_html !!}
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada catatan surat peringatan aktif.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($warnings->hasPages())
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $warnings->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

<!-- Modal Add Warning -->
<div class="modal fade" id="modalAddWarning" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="{{ route('warning.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark mb-0">Terbitkan Surat Peringatan (SP)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="alert alert-warning small mb-3" style="border-radius: 8px;">
                        <i class="ti ti-info-circle me-1"></i>
                        Sesuai regulasi ketenagakerjaan Indonesia, Surat Peringatan otomatis berlaku selama <strong>6 (enam) bulan</strong> sejak tanggal penerbitan.
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Pilih Karyawan</label>
                        <select name="nik" class="form-select" required style="border-radius: 8px;">
                            @foreach($karyawans as $k)
                                <option value="{{ $k->nik }}">{{ $k->nik }} - {{ $k->nama_karyawan }} ({{ $k->departemen->nama_dept ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Tingkat Sanksi</label>
                            <select name="level" class="form-select" required style="border-radius: 8px;">
                                <option value="TEGURAN_LISAN">Teguran Lisan</option>
                                <option value="SP_1" selected>Surat Peringatan I (SP 1)</option>
                                <option value="SP_2">Surat Peringatan II (SP 2)</option>
                                <option value="SP_3">Surat Peringatan III (SP 3)</option>
                                <option value="SKORSING">Skorsing Sementara</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Tanggal Efektif Berlaku</label>
                            <input type="date" name="effective_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Pasal Peraturan Perusahaan yang Dilanggar</label>
                        <input type="text" name="pasal_pelanggaran" class="form-control" placeholder="Contoh: Pasal 14 Ayat 2 (Keterlambatan berturut-turut)" style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Uraian Bentuk Pelanggaran</label>
                        <textarea name="violation_description" rows="3" class="form-control" placeholder="Jelaskan secara kronologis kronologi pelanggaran..." required style="border-radius: 8px;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Rencana Perbaikan & Kesepakatan (Action Plan)</label>
                        <textarea name="action_plan" rows="2" class="form-control" placeholder="Tindakan korektif yang wajib dilakukan karyawan..." style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-danger" style="border-radius: 8px;">Terbitkan SP</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
