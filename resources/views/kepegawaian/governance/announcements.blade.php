@extends('layouts.app')
@section('titlepage', 'Pengumuman Internal')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Pengumuman</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Pengumuman Internal Perusahaan</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($announcements->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Komunikasi internal, surat edaran resmi, dan informasi penting bagi seluruh karyawan.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalAddAnnouncement" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-speakerphone" style="font-size: 16px;"></i>
            <span>Buat Pengumuman Baru</span>
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible mb-3" role="alert" style="border-radius: 8px;">
        <div class="d-flex">
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
    </div>
@endif

<div class="row g-3">
    @forelse($announcements as $ann)
    <div class="col-12">
        <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if($ann->is_pinned)
                            <span class="badge bg-label-warning fw-semibold"><i class="ti ti-pin me-1"></i>Sematkan</span>
                        @endif
                        <span class="badge bg-light text-dark fw-semibold">{{ $ann->category_label }}</span>
                        @if($ann->branch)
                            <span class="badge bg-label-secondary">{{ $ann->branch->nama_cabang }}</span>
                        @elseif($ann->department)
                            <span class="badge bg-label-secondary">{{ $ann->department->nama_dept }}</span>
                        @else
                            <span class="badge bg-label-primary">Semua Karyawan</span>
                        @endif
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary p-1" data-bs-toggle="dropdown" style="border-radius: 6px; width: 28px; height: 28px;">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-sm">
                            <form action="{{ route('announcement.destroy', $ann->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger delete-confirm" data-label="Pengumuman Ini">
                                    <i class="ti ti-trash me-2"></i> Hapus Pengumuman
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-2">{{ $ann->title }}</h5>
                <p class="text-secondary mb-3" style="white-space: pre-line; font-size: 13.5px; line-height: 1.6;">{{ $ann->content }}</p>

                <div class="d-flex justify-content-between align-items-center pt-2 border-top small text-muted">
                    <div>
                        <i class="ti ti-user me-1"></i>{{ $ann->author->name ?? 'HR Administrator' }}
                    </div>
                    <div>
                        <i class="ti ti-calendar me-1"></i>{{ $ann->published_at ? $ann->published_at->format('d M Y, H:i') : $ann->created_at->format('d M Y') }} WIB
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card p-4 text-center text-muted" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
            <i class="ti ti-info-circle fs-2 text-muted mb-2"></i>
            <div>Belum ada pengumuman internal aktif.</div>
        </div>
    </div>
    @endforelse
</div>

@if($announcements->hasPages())
<div class="card my-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $announcements->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

<!-- Modal Add Announcement -->
<div class="modal fade" id="modalAddAnnouncement" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 12px;">
            <form action="{{ route('announcement.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark mb-0">Buat Pengumuman Internal Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Judul Pengumuman</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Surat Edaran Libur Hari Raya Idul Fitri 1447 H" required style="border-radius: 8px;">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label required fw-semibold" style="font-size: 12px;">Kategori</label>
                            <select name="category" class="form-select" required style="border-radius: 8px;">
                                @foreach($categories as $code => $label)
                                    <option value="{{ $code }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Target Departemen</label>
                            <select name="kode_dept" class="form-select" style="border-radius: 8px;">
                                <option value="">Semua Departemen</option>
                                @foreach($departemens as $dept)
                                    <option value="{{ $dept->kode_dept }}">{{ $dept->nama_dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 12px;">Target Cabang</label>
                            <select name="kode_cabang" class="form-select" style="border-radius: 8px;">
                                <option value="">Semua Kantor / Cabang</option>
                                @foreach($cabangs as $cbg)
                                    <option value="{{ $cbg->kode_cabang }}">{{ $cbg->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Isi Pesan Pengumuman</label>
                        <textarea name="content" rows="6" class="form-control" placeholder="Tuliskan isi pengumuman resmi perusahaan..." required style="border-radius: 8px;"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_pinned" value="1" id="checkPinned">
                            <label class="form-check-label fw-semibold" for="checkPinned" style="font-size: 13px;">Sematkan di bagian teratas (Pinned Announcement)</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Publikasikan Pengumuman</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
