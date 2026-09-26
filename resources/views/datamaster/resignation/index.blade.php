@extends('layouts.app')
@section('titlepage', 'Resignasi & Offboarding Karyawan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item active">Resignasi & Offboarding</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Resignasi & Offboarding Karyawan</span>
            <span class="badge" style="background: rgba(220, 38, 38, 0.08); color: #DC2626; border: 1px solid rgba(220, 38, 38, 0.15); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($resignations->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Manajemen pemutusan hubungan kerja, pengunduran diri, checklist serah terima aset (clearance), dan arsip nonaktif.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        @can('resignation.create')
            <a href="{{ route('resignation.create') }}" class="btn btn-danger d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-user-minus" style="font-size: 16px;"></i>
                <span>Proses Resign / Keluar</span>
            </a>
        @endcan
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-danger rounded p-2 me-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-user-off fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Karyawan Nonaktif/Keluar</div>
                        <h4 class="mb-0 fw-bold font-mono">{{ number_format($stats['total']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-warning rounded p-2 me-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-clock-pause fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Clearance Pending / Handover</div>
                        <h4 class="mb-0 fw-bold text-warning font-mono">{{ number_format($stats['pending_clearance']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-success rounded p-2 me-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-shield-check fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Clearance Selesai (Cleared)</div>
                        <h4 class="mb-0 fw-bold text-success font-mono">{{ number_format($stats['cleared']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('resignation.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <div class="input-icon">
                    <span class="input-icon-addon"><i class="ti ti-search"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIK, Nama Karyawan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="flex-shrink-0" style="min-width: 180px;">
                <select name="kategori_keluar" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $code => $label)
                        <option value="{{ $code }}" {{ request('kategori_keluar') === $code ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-shrink-0" style="min-width: 160px;">
                <select name="status_clearance" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Clearance</option>
                    <option value="PENDING" {{ request('status_clearance') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                    <option value="IN_PROGRESS" {{ request('status_clearance') === 'IN_PROGRESS' ? 'selected' : '' }}>Dalam Proses</option>
                    <option value="CLEARED" {{ request('status_clearance') === 'CLEARED' ? 'selected' : '' }}>Cleared</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'kategori_keluar', 'status_clearance']))
                    <a href="{{ route('resignation.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Karyawan</th>
                    <th>Tanggal Keluar</th>
                    <th>Kategori Keluar</th>
                    <th>Status Clearance</th>
                    <th>Dokumen</th>
                    <th>Alasan</th>
                    <th class="text-end" style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resignations as $r)
                    <tr>
                        <td class="text-muted font-mono">{{ $loop->iteration + ($resignations->currentPage() - 1) * $resignations->perPage() }}</td>
                        <td>
                            @if($r->karyawan)
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm bg-label-danger text-danger rounded-circle me-2 fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ strtoupper(substr($r->karyawan->nama_karyawan, 0, 2)) }}
                                    </span>
                                    <div>
                                        <a href="{{ route('karyawan.show', Crypt::encrypt($r->karyawan->nik)) }}" class="fw-bold text-reset text-decoration-none">
                                            {{ $r->karyawan->nama_karyawan }}
                                        </a>
                                        <div class="text-muted small font-mono">NIK: {{ $r->nik }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted font-mono">NIK: {{ $r->nik }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold text-dark font-mono">{{ $r->tanggal_keluar ? $r->tanggal_keluar->format('d/m/Y') : '-' }}</span>
                            <div class="text-muted small font-mono">Diajukan: {{ $r->tanggal_pengajuan ? $r->tanggal_pengajuan->format('d/m/Y') : '-' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-label-secondary font-mono">{{ $r->kategori_keluar_label }}</span>
                        </td>
                        <td>
                            {!! $r->clearance_badge_html !!}
                        </td>
                        <td>
                            @if($r->dokumen)
                                <a href="{{ asset('storage/' . $r->dokumen) }}" target="_blank" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1">
                                    <i class="ti ti-file-text"></i>Lihat Berkas
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted small text-truncate d-inline-block" style="max-width: 200px;" title="{{ $r->alasan }}">
                                {{ $r->alasan ?: '-' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('offboarding.show', $r->id) }}" class="btn btn-outline-info" title="Exit Clearance & Hak Akhir">
                                    <i class="ti ti-checklist"></i>
                                </a>
                                @can('resignation.edit')
                                    <button type="button" class="btn btn-outline-primary btn-edit-clearance"
                                        data-id="{{ Crypt::encrypt($r->id) }}"
                                        data-clearance="{{ $r->status_clearance }}"
                                        data-notes="{{ $r->catatan_hr }}"
                                        data-nama="{{ $r->karyawan ? $r->karyawan->nama_karyawan : $r->nik }}"
                                        title="Update Status Clearance">
                                        <i class="ti ti-shield-check"></i>
                                    </button>
                                @endcan
                                @can('resignation.delete')
                                    <form action="{{ route('resignation.delete', Crypt::encrypt($r->id)) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-success" title="Batalkan & Aktifkan Kembali Karyawan">
                                            <i class="ti ti-user-check"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty">
                                <div class="empty-icon text-muted mb-2"><i class="ti ti-mood-smile fs-1"></i></div>
                                <p class="empty-title fw-bold">Tidak Ada Data Resignasi</p>
                                <p class="empty-subtitle text-muted">Seluruh karyawan berstatus aktif normal.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Footer Card -->
@if($resignations->hasPages())
    <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
        <div class="card-body py-2.5 px-3">
            {{ $resignations->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif

<!-- Modal Update Clearance -->
<div class="modal fade" id="modalClearance" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formClearance" action="" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-shield-check me-2 text-primary"></i>Status Serah Terima / Clearance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Karyawan</label>
                    <div id="clearanceNamaKaryawan" class="fw-bold fs-3 text-dark">-</div>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Status Clearance Aset & Tanggung Jawab</label>
                    <select name="status_clearance" id="selectStatusClearance" class="form-select" required>
                        <option value="PENDING">PENDING — Belum mengembalikan aset/handover</option>
                        <option value="IN_PROGRESS">IN_PROGRESS — Proses serah terima berlangsung</option>
                        <option value="CLEARED">CLEARED — Aset, dokumen & clearance tuntas 100%</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Tim HR / Log Handover</label>
                    <textarea name="catatan_hr" id="textareaCatatanHr" class="form-control" rows="3" placeholder="Contoh: Laptop dan ID card telah diserahkan ke bagian IT pada 20/09..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i>Simpan Pembaruan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        $('.btn-edit-clearance').on('click', function() {
            var id = $(this).data('id');
            var clearance = $(this).data('clearance');
            var notes = $(this).data('notes');
            var nama = $(this).data('nama');

            $('#clearanceNamaKaryawan').text(nama);
            $('#selectStatusClearance').val(clearance);
            $('#textareaCatatanHr').val(notes);
            $('#formClearance').attr('action', '/resignation/' + id);
            $('#modalClearance').modal('show');
        });

        $('.form-delete').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Batalkan Resign & Aktifkan Kembali?',
                text: 'Karyawan akan dipulihkan status aktifnya dan dapat melakukan presensi kembali.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2fb344',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Pulihkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
