@extends('layouts.app')
@section('titlepage', 'Manajemen Saldo Kuota Cuti Karyawan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('leave_types.index') }}">Jenis Cuti</a></li>
    <li class="breadcrumb-item active">Saldo Kuota Cuti</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Saldo & Kuota Cuti Karyawan</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($quotas->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Pemantauan alokasi hak cuti tahunan, cuti terpakai, dan penyesuaian saldo cuti karyawan.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('leave_types.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-list" style="font-size: 16px;"></i>
            <span>Master Jenis Cuti</span>
        </a>
        @can('leave_quotas.adjust')
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalGenerate" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-sparkles" style="font-size: 16px;"></i>
                <span>Inisialisasi Kuota Tahunan</span>
            </button>
        @endcan
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-primary rounded p-2 me-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-calendar-plus fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Dialokasikan</div>
                        <h4 class="mb-0 fw-bold font-mono">{{ number_format($stats['total_allocated'], 1) }} <small class="fs-6 text-muted">Hari</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-warning rounded p-2 me-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-calendar-minus fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Terpakai</div>
                        <h4 class="mb-0 fw-bold text-warning font-mono">{{ number_format($stats['total_used'], 1) }} <small class="fs-6 text-muted">Hari</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-success rounded p-2 me-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-wallet fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Sisa Kuota Tersedia</div>
                        <h4 class="mb-0 fw-bold text-success font-mono">{{ number_format($stats['total_remaining'], 1) }} <small class="fs-6 text-muted">Hari</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-info rounded p-2 me-3 d-flex align-items-center justify-content-center">
                        <i class="ti ti-users fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Karyawan Terdata ({{ $year }})</div>
                        <h4 class="mb-0 fw-bold font-mono">{{ number_format($stats['employees_count']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card admin-filter-toolbar mb-3">
    <form action="{{ route('leave_quotas.index') }}" method="GET" class="m-0 w-100">
        <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap w-100">
            <div class="flex-grow-1" style="min-width: 240px;">
                <x-input-with-icon label="" value="{{ request('search') }}" name="search"
                    icon="ti ti-search" placeholder="Cari NIK, Nama Karyawan..." hideLabel="true" />
            </div>
            <div class="flex-shrink-0" style="min-width: 140px;">
                <select name="year" class="form-select form-select-sm" style="border-radius: 8px;">
                    @for($y = date('Y') + 1; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-shrink-0" style="min-width: 180px;">
                <select name="leave_type_id" class="form-select form-select-sm" style="border-radius: 8px;">
                    <option value="">Semua Jenis Cuti</option>
                    @foreach($leaveTypes as $lt)
                        <option value="{{ $lt->id }}" {{ request('leave_type_id') == $lt->id ? 'selected' : '' }}>
                            {{ $lt->code }} - {{ $lt->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-3">
                    <i class="ti ti-search" style="font-size: 14px;"></i>
                    <span>Cari Data</span>
                </button>
                @if(request()->anyFilled(['search', 'leave_type_id']) || request('year') != date('Y'))
                    <a href="{{ route('leave_quotas.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1 px-3" title="Reset Filter">
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
                    <th>Jenis Cuti</th>
                    <th>Tahun</th>
                    <th>Hak Diperoleh</th>
                    <th>Terpakai</th>
                    <th>Penyesuaian</th>
                    <th>Sisa Kuota</th>
                    <th class="text-end" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotas as $q)
                    <tr>
                        <td class="text-muted font-mono">{{ $loop->iteration + ($quotas->currentPage() - 1) * $quotas->perPage() }}</td>
                        <td>
                            @if($q->karyawan)
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm bg-label-primary text-primary rounded-circle me-2 fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ strtoupper(substr($q->karyawan->nama_karyawan, 0, 2)) }}
                                    </span>
                                    <div>
                                        <a href="{{ route('karyawan.show', Crypt::encrypt($q->karyawan->nik)) }}" class="fw-bold text-reset text-decoration-none">
                                            {{ $q->karyawan->nama_karyawan }}
                                        </a>
                                        <div class="text-muted small font-mono">NIK: {{ $q->nik }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted font-mono">NIK: {{ $q->nik }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-primary font-mono fw-bold">{{ $q->leaveType ? $q->leaveType->code : '-' }}</span>
                            <div class="small text-muted">{{ $q->leaveType ? $q->leaveType->name : '-' }}</div>
                        </td>
                        <td class="fw-semibold text-dark font-mono">{{ $q->year }}</td>
                        <td>
                            <span class="fw-bold text-dark font-mono">{{ (float)$q->earned }} Hari</span>
                        </td>
                        <td>
                            <span class="text-warning fw-semibold font-mono">{{ (float)$q->used }} Hari</span>
                        </td>
                        <td>
                            @if($q->adjustment > 0)
                                <span class="badge bg-label-success font-mono">+{{ (float)$q->adjustment }}</span>
                            @elseif($q->adjustment < 0)
                                <span class="badge bg-label-danger font-mono">{{ (float)$q->adjustment }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $q->closing_balance > 0 ? 'bg-label-success' : 'bg-label-danger' }} fw-bold font-mono">
                                {{ (float)$q->closing_balance }} Hari
                            </span>
                        </td>
                        <td class="text-end">
                            @can('leave_quotas.adjust')
                                <button type="button" class="btn btn-sm btn-outline-primary btn-adjust"
                                    data-id="{{ $q->id }}"
                                    data-nama="{{ $q->karyawan ? $q->karyawan->nama_karyawan : $q->nik }}"
                                    data-type="{{ $q->leaveType ? $q->leaveType->name : '-' }}"
                                    data-balance="{{ (float)$q->closing_balance }}"
                                    title="Sesuaikan Saldo">
                                    <i class="ti ti-adjustments me-1"></i>Sesuaikan
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="empty">
                                <div class="empty-icon text-muted mb-2"><i class="ti ti-wallet-off fs-1"></i></div>
                                <p class="empty-title fw-bold">Belum Ada Kuota Cuti Periode {{ $year }}</p>
                                <p class="empty-subtitle text-muted">Klik tombol "Inisialisasi Kuota Tahunan" untuk mengalokasikan hak cuti tahunan kepada seluruh karyawan aktif.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($quotas->hasPages())
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $quotas->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif

<!-- Modal Adjust Balance -->
<div class="modal fade" id="modalAdjust" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('leave_quotas.adjust') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="leave_quota_id" id="adjustQuotaId">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-adjustments me-2 text-primary"></i>Penyesuaian Saldo Kuota Cuti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Karyawan</label>
                    <div id="adjustNamaKaryawan" class="fw-bold fs-3 text-dark">-</div>
                    <div id="adjustLeaveType" class="text-primary small">-</div>
                    <div class="small text-muted mt-1">Saldo Saat Ini: <strong id="adjustCurrentBalance" class="text-dark">0</strong> Hari</div>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Jumlah Penyesuaian (Hari)</label>
                    <input type="number" step="0.5" name="amount" class="form-control" placeholder="Contoh: 1.0 (tambah) atau -1.0 (kurang)" required>
                    <div class="form-text text-muted">Gunakan angka positif untuk menambah saldo, atau negatif (-) untuk memotong saldo.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Alasan / Catatan Penyesuaian</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Kompensasi kerja lembur di hari libur nasional..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i>Simpan Penyesuaian
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Generate Annual Quotas -->
<div class="modal fade" id="modalGenerate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('leave_quotas.generate') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-sparkles me-2 text-primary"></i>Inisialisasi Kuota Cuti Tahunan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Sistem akan mengalokasikan hak cuti tahunan (default 12 hari) untuk seluruh karyawan yang berstatus aktif pada tahun yang dipilih.</p>
                <div class="mb-3">
                    <label class="form-label required">Pilih Tahun Periode Kuota</label>
                    <select name="year" class="form-select" required>
                        @for($y = date('Y') + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="alert alert-info mb-0">
                    <i class="ti ti-info-circle me-1"></i>Karyawan yang sudah memiliki kuota pada tahun tersebut tidak akan ditimpa atau diduplikasi.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-check me-1"></i>Proses Inisialisasi Kuota
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        $('.btn-adjust').on('click', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var type = $(this).data('type');
            var balance = $(this).data('balance');

            $('#adjustQuotaId').val(id);
            $('#adjustNamaKaryawan').text(nama);
            $('#adjustLeaveType').text(type);
            $('#adjustCurrentBalance').text(balance);
            $('#modalAdjust').modal('show');
        });
    });
</script>
@endpush
