@extends('layouts.app')
@section('titlepage', 'Peraturan Perusahaan & SOP')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Kebijakan & SOP</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Peraturan Perusahaan & SOP</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ count($policies) }} Kebijakan
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Pedoman operasional internal, standard operating procedure, dan peraturan ketenagakerjaan.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('document.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-folder" style="font-size: 16px;"></i>
            <span>Brankas Karyawan</span>
        </a>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalAddPolicy" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-plus" style="font-size: 16px;"></i>
            <span>Tambah Kebijakan / SOP</span>
        </button>
    </div>
</div>

<div class="row g-3">
    @forelse($policies as $p)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-light text-dark font-mono">{{ $p->policy_code }}</span>
                    <span class="badge bg-label-secondary font-mono">Versi {{ $p->version }}</span>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $p->title }}</h5>
                <div class="text-primary small fw-semibold mb-2">{{ $p->category_label }}</div>
                <p class="text-muted small mb-3" style="font-size: 13px;">
                    {{ $p->description ?: 'Dokumen kebijakan dan pedoman resmi operasional perusahaan.' }}
                </p>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top small text-muted">
                    <span><i class="ti ti-calendar me-1"></i>{{ $p->effective_date->format('d M Y') }}</span>
                    <span class="badge bg-label-success fw-semibold">Aktif Berlaku</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card p-4 text-center text-muted" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
            Belum ada dokumen kebijakan yang didaftarkan.
        </div>
    </div>
    @endforelse
</div>

<!-- Modal Add Policy -->
<div class="modal fade" id="modalAddPolicy" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="{{ route('policy.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark mb-0">Tambah Kebijakan / SOP Perusahaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Kode Dokumen</label>
                        <input type="text" name="policy_code" class="form-control font-mono" placeholder="Contoh: PP-2026, SOP-HR-003" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Judul Kebijakan / SOP</label>
                        <input type="text" name="title" class="form-control" required style="border-radius: 8px;">
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
                        <div class="col-3">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Versi</label>
                            <input type="text" name="version" class="form-control font-mono" value="1.0" required style="border-radius: 8px;">
                        </div>
                        <div class="col-3">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Tgl Berlaku</label>
                            <input type="date" name="effective_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Deskripsi & Ruang Lingkup</label>
                        <textarea name="description" rows="3" class="form-control" style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Simpan Kebijakan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
