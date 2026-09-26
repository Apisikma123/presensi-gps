@extends('layouts.app')
@section('titlepage', 'Brankas Dokumen Karyawan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Brankas Dokumen</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Brankas Dokumen Karyawan (Vault)</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($documents->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Legalitas berkas digital, arsip KTP/NPWP, sertifikat, dan dokumen kepegawaian.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('policy.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-books" style="font-size: 16px;"></i>
            <span>Kebijakan & SOP</span>
        </a>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalUploadDoc" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-upload" style="font-size: 16px;"></i>
            <span>Unggah Dokumen Baru</span>
        </button>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('document.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari judul dokumen, nama karyawan, atau NIK..." hideLabel="true" />
            </div>
            <div class="flex-shrink-0" style="min-width: 180px;">
                <select name="document_type" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Jenis Dokumen</option>
                    @foreach($types as $code => $label)
                        <option value="{{ $code }}" {{ request('document_type') === $code ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari Data</span>
                </button>
                @if(request()->hasAny(['search', 'document_type']))
                    <a href="{{ route('document.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Documents Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th>NAMA DOKUMEN</th>
                    <th>KARYAWAN</th>
                    <th>KATEGORI BERKAS</th>
                    <th>UKURAN</th>
                    <th>TANGGAL UNGGAH</th>
                    <th class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm bg-label-primary rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                <i class="ti ti-file-text fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $doc->title }}</div>
                                @if($doc->notes)
                                    <div class="text-muted small" style="font-size: 12px;">{{ $doc->notes }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $doc->karyawan->nama_karyawan ?? 'Karyawan' }}</div>
                        <div class="text-muted small font-mono">NIK: {{ $doc->nik }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark fw-semibold">{{ $doc->type_label }}</span>
                    </td>
                    <td class="font-mono small text-muted">
                        {{ $doc->file_size_kb ? $doc->file_size_kb . ' KB' : '-' }}
                    </td>
                    <td class="small text-muted">
                        {{ $doc->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="text-end">
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" style="border-radius: 6px;">
                            <i class="ti ti-download"></i> Unduh
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada dokumen tersimpan di brankas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($documents->hasPages())
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $documents->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

<!-- Modal Upload Document -->
<div class="modal fade" id="modalUploadDoc" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark mb-0">Unggah Dokumen ke Brankas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Pilih Karyawan</label>
                        <select name="nik" class="form-select" required style="border-radius: 8px;">
                            @foreach($karyawans as $k)
                                <option value="{{ $k->nik }}">{{ $k->nik }} - {{ $k->nama_karyawan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Jenis Dokumen</label>
                        <select name="document_type" class="form-select" required style="border-radius: 8px;">
                            @foreach($types as $code => $label)
                                <option value="{{ $code }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Judul / Keterangan Berkas</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: KTP Asli Hasil Scan, Ijazah S1" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Pilih File Berkas (PDF, JPG, PNG, DOCX - Maks 10MB)</label>
                        <input type="file" name="document_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Catatan Tambahan</label>
                        <textarea name="notes" rows="2" class="form-control" style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Unggah Dokumen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
