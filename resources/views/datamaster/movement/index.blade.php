@extends('layouts.app')
@section('titlepage', 'Mutasi & Riwayat Karir Karyawan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Data Karyawan</a></li>
    <li class="breadcrumb-item active">Mutasi & Karir</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Mutasi & Riwayat Karir</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($movements->total()) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Pencatatan mutasi cabang, rotasi departemen, promosi jabatan, pergantian atasan, dan perubahan status kepegawaian.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        @can('movement.create')
            <a href="{{ route('movement.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Catat Mutasi / Promosi</span>
            </a>
        @endcan
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('movement.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari No. SK, NIK, Nama Karyawan..." hideLabel="true" />
            </div>
            <div class="flex-shrink-0" style="min-width: 180px;">
                <select name="movement_type" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Jenis Perubahan</option>
                    @foreach($types as $code => $label)
                        <option value="{{ $code }}" {{ request('movement_type') === $code ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-shrink-0" style="min-width: 150px;">
                <select name="status" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Status</option>
                    <option value="APPLIED" {{ request('status') === 'APPLIED' ? 'selected' : '' }}>Diterapkan</option>
                    <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Disetujui</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Menunggu</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'movement_type', 'status']))
                    <a href="{{ route('movement.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
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
                    <th style="width: 50px;">NO</th>
                    <th>TANGGAL EFEKTIF</th>
                    <th>NO. SK / DOKUMEN</th>
                    <th>KARYAWAN</th>
                    <th>JENIS PERUBAHAN</th>
                    <th>RINGKASAN PERUBAHAN</th>
                    <th>STATUS</th>
                    <th class="text-end" style="width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $m)
                    <tr>
                        <td class="text-muted font-mono">{{ $loop->iteration + ($movements->currentPage() - 1) * $movements->perPage() }}</td>
                        <td>
                            <span class="fw-semibold text-dark font-mono small">{{ $m->effective_date ? $m->effective_date->format('d/m/Y') : '-' }}</span>
                        </td>
                        <td>
                            <span class="fw-semibold text-primary font-mono small">{{ $m->no_sk ?: 'Tanpa No. SK' }}</span>
                            @if($m->document_path)
                                <div>
                                    <a href="{{ asset('storage/' . $m->document_path) }}" target="_blank" class="small text-muted d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                        <i class="ti ti-paperclip"></i>Lihat Berkas
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($m->karyawan)
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-label-primary rounded-circle me-2 fw-bold d-flex align-items-center justify-content-center font-mono" style="width: 32px; height: 32px; font-size: 11px;">
                                        {{ strtoupper(substr($m->karyawan->nama_karyawan, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('karyawan.show', Crypt::encrypt($m->karyawan->nik)) }}" class="fw-semibold text-dark text-decoration-none">
                                            {{ $m->karyawan->nama_karyawan }}
                                        </a>
                                        <div class="text-muted small font-mono">NIK: {{ $m->nik }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted font-mono">NIK: {{ $m->nik }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-info fw-semibold">{{ $m->movement_type_label }}</span>
                        </td>
                        <td>
                            <div class="small">
                                @if(!empty($m->new_values))
                                    @foreach($m->new_values as $key => $newVal)
                                        @php $oldVal = $m->old_values[$key] ?? '-'; @endphp
                                        <div class="mb-0.5">
                                            <span class="text-muted">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                            <span class="text-secondary text-decoration-line-through">{{ $oldVal ?: '(kosong)' }}</span>
                                            <i class="ti ti-arrow-right text-primary mx-1"></i>
                                            <span class="fw-semibold text-dark">{{ $newVal ?: '(dikosongkan)' }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-muted">{{ $m->reason ?: 'Tidak ada rincian nilai' }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            {!! $m->status_badge_html !!}
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('movement.show', Crypt::encrypt($m->id)) }}" class="btn btn-sm btn-outline-info p-1" title="Rincian & SK" style="border-radius: 6px; width: 28px; height: 28px;">
                                    <i class="ti ti-eye fs-5"></i>
                                </a>
                                @can('movement.delete')
                                    <form action="{{ route('movement.delete', Crypt::encrypt($m->id)) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger p-1" title="Hapus Riwayat" style="border-radius: 6px; width: 28px; height: 28px;">
                                            <i class="ti ti-trash fs-5"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="ti ti-history-off fs-1 text-muted mb-2 d-block"></i>
                            <p class="fw-bold mb-1">Belum Ada Riwayat Mutasi / Perubahan</p>
                            <p class="small text-muted mb-0">Klik "Catat Mutasi / Promosi" untuk mendokumentasikan perpindahan cabang, rotasi, atau jenjang karir.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Footer Card -->
@if($movements->hasPages())
    <div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
        <div class="card-body py-2.5 px-3">
            {{ $movements->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
@endsection

@push('myscript')
<script>
    $('.form-delete').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Hapus Riwayat Mutasi?',
            text: 'Catatan mutasi ini akan dihapus dari riwayat karir karyawan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
