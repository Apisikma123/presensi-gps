@extends('layouts.app')
@section('titlepage', 'Aset & Inventaris Karyawan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Aset & Inventaris</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Aset & Inventaris Karyawan</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($assets->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Manajemen penugasan perangkat kerja, inventaris perusahaan, dan berita acara pengembalian fasilitas.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalAddAsset" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-plus" style="font-size: 16px;"></i>
            <span>Penugasan Aset Baru</span>
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible mb-3" role="alert" style="border-radius: 10px; border: 1px solid #bbf7d0; background: #f0fdf4; color: #15803d;">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
    </div>
@endif

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('asset.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari nama barang, kode aset, S/N, atau nama karyawan..." hideLabel="true" />
            </div>
            <div class="flex-shrink-0" style="min-width: 180px;">
                <select name="status" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Status</option>
                    <option value="ASSIGNED" {{ request('status') === 'ASSIGNED' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    <option value="RETURNED" {{ request('status') === 'RETURNED' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                    <option value="UNDER_MAINTENANCE" {{ request('status') === 'UNDER_MAINTENANCE' ? 'selected' : '' }}>Perbaikan / Servis</option>
                    <option value="LOST" {{ request('status') === 'LOST' ? 'selected' : '' }}>Hilang</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari Data</span>
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('asset.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Assets Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th>KODE & NAMA ASET</th>
                    <th>KATEGORI</th>
                    <th>KARYAWAN PEMEGANG</th>
                    <th>NOMOR SERI (S/N)</th>
                    <th>TGL PENUGASAN</th>
                    <th>KONDISI</th>
                    <th>STATUS</th>
                    <th class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $a)
                <tr>
                    <td>
                        <span class="font-mono text-muted small d-block">{{ $a->asset_code }}</span>
                        <span class="fw-semibold text-dark">{{ $a->name }}</span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark font-mono">{{ $a->category_label }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $a->karyawan->nama_karyawan ?? 'Karyawan' }}</div>
                        <div class="text-muted small font-mono">NIK: {{ $a->nik }}</div>
                    </td>
                    <td>
                        <span class="font-mono small">{{ $a->serial_number ?: '-' }}</span>
                    </td>
                    <td>
                        <div>{{ $a->assigned_date->format('d M Y') }}</div>
                        @if($a->returned_date)
                            <div class="text-success small"><i class="ti ti-check me-1"></i>Kembali: {{ $a->returned_date->format('d M Y') }}</div>
                        @endif
                    </td>
                    <td>
                        @php
                            $condBadge = match($a->condition) {
                                'EXCELLENT' => 'bg-label-success',
                                'GOOD' => 'bg-label-primary',
                                'FAIR' => 'bg-label-warning',
                                'DAMAGED' => 'bg-label-danger',
                                default => 'bg-light text-dark'
                            };
                        @endphp
                        <span class="badge {{ $condBadge }} font-mono">{{ $a->condition }}</span>
                    </td>
                    <td>{!! $a->status_badge_html !!}</td>
                    <td class="text-end">
                        @if($a->status === 'ASSIGNED')
                        <button type="button" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalReturnAsset" 
                            data-asset-id="{{ $a->id }}" 
                            data-asset-name="{{ $a->name }}" 
                            data-asset-code="{{ $a->asset_code }}"
                            style="border-radius: 6px;">
                            <i class="ti ti-arrow-back-up"></i>
                            <span>Kembalikan</span>
                        </button>
                        @else
                        <span class="text-muted small">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data penugasan aset fasilitas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($assets->hasPages())
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $assets->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

<!-- Modal Add Asset -->
<div class="modal fade" id="modalAddAsset" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="{{ route('asset.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark mb-0">Penugasan Aset Fasilitas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Karyawan Penerima Aset</label>
                        <select name="nik" class="form-select" required style="border-radius: 8px;">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->nik }}">{{ $k->nama_karyawan }} (NIK: {{ $k->nik }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Nama Barang / Fasilitas</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: MacBook Air M2, Mobil Avanza, ID Card" required style="border-radius: 8px;">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Kategori</label>
                            <select name="category" class="form-select" required style="border-radius: 8px;">
                                @foreach($categories as $code => $label)
                                    <option value="{{ $code }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Nomor Seri / Plat No</label>
                            <input type="text" name="serial_number" class="form-control font-mono" placeholder="S/N atau Plat Nomor" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Tgl Penyerahan</label>
                            <input type="date" name="assigned_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Kondisi Awal</label>
                            <select name="condition" class="form-select" required style="border-radius: 8px;">
                                <option value="EXCELLENT">EXCELLENT (Sangat Baik / Baru)</option>
                                <option value="GOOD" selected>GOOD (Baik / Layak Pakai)</option>
                                <option value="FAIR">FAIR (Cukup / Ada Baret Halus)</option>
                                <option value="DAMAGED">DAMAGED (Rusak / Perlu Perbaikan)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Kelengkapan & Catatan</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Contoh: Termasuk charger bawaan, mouse, dan tas laptop." style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Simpan & Tugaskan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Return Asset -->
<div class="modal fade" id="modalReturnAsset" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius: 12px;">
            <form id="formReturnAsset" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark mb-0">Pengembalian Aset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <div class="text-muted small">Barang yang dikembalikan:</div>
                        <div class="fw-bold text-dark fs-5" id="returnAssetName">-</div>
                        <div class="font-mono text-muted small" id="returnAssetCode">-</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Kondisi Saat Dikembalikan</label>
                        <select name="condition" class="form-select" required style="border-radius: 8px;">
                            <option value="EXCELLENT">EXCELLENT (Sangat Baik)</option>
                            <option value="GOOD" selected>GOOD (Baik / Lengkap)</option>
                            <option value="FAIR">FAIR (Ada Aus Pemakaian)</option>
                            <option value="DAMAGED">DAMAGED (Rusak / Tidak Lengkap)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Konfirmasi Pengembalian</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalReturn = document.getElementById('modalReturnAsset');
        if (modalReturn) {
            modalReturn.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const assetId = button.getAttribute('data-asset-id');
                const assetName = button.getAttribute('data-asset-name');
                const assetCode = button.getAttribute('data-asset-code');
                
                document.getElementById('returnAssetName').textContent = assetName;
                document.getElementById('returnAssetCode').textContent = assetCode;
                document.getElementById('formReturnAsset').action = `/assets/${assetId}/return`;
            });
        }
    });
</script>
@endpush
@endsection
