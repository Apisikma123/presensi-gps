@extends('layouts.app')
@section('titlepage', 'Import Karyawan & Operasi Massal')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Data Karyawan</a></li>
    <li class="breadcrumb-item active">Import Massal</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Import Karyawan & Operasi Massal</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                Bulk Sync
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Impor data pegawai via berkas CSV, unduh format template, dan eksekusi mutasi serentak.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="{{ route('karyawan.import.template') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-download" style="font-size: 16px;"></i>
            <span>Unduh Format CSV</span>
        </a>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalBulkAction" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-adjustments" style="font-size: 16px;"></i>
            <span>Operasi Massal</span>
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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible mb-3" role="alert" style="border-radius: 8px;">
        <div class="d-flex">
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
    </div>
@endif

<!-- Upload Card -->
<div class="card mb-4" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="card-body p-4">
        <h5 class="card-title text-dark fw-bold mb-1">Unggah Berkas CSV Karyawan</h5>
        <p class="text-muted small mb-3">
            Gunakan format file CSV sesuai template standar. Kolom wajib: <code>nik</code> dan <code>nama_karyawan</code>. Kolom opsional: <code>kode_dept</code>, <code>kode_cabang</code>, <code>kode_jabatan</code>, <code>jenis_kelamin</code>, <code>tanggal_masuk</code>, <code>ptkp_status</code>, <code>gaji_pokok</code>.
        </p>
        <form action="{{ route('karyawan.import.process') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-center">
            @csrf
            <div class="col-md-9">
                <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required style="border-radius: 8px;">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1.5" style="border-radius: 8px;">
                    <i class="ti ti-upload"></i>
                    <span>Mulai Proses Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Employees List with Checkboxes for Bulk Action -->
<form id="formBulkSelection" action="{{ route('karyawan.bulk_action') }}" method="POST">
    @csrf
    <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="card-title text-dark fw-bold mb-0">Daftar Karyawan Terdaftar</h6>
            <span class="text-muted small">Pilih kotak centang untuk aksi serentak</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;" class="text-center">
                            <input type="checkbox" id="checkAll" class="form-check-input">
                        </th>
                        <th>NIK & NAMA KARYAWAN</th>
                        <th>DEPARTEMEN</th>
                        <th>KANTOR / CABANG</th>
                        <th>JABATAN</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawans as $k)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="niks[]" value="{{ $k->nik }}" class="form-check-input row-check">
                        </td>
                        <td>
                            <span class="font-mono text-muted small d-block">{{ $k->nik }}</span>
                            <span class="fw-semibold text-dark">{{ $k->nama_karyawan }}</span>
                        </td>
                        <td>{{ $k->departemen->nama_dept ?? '-' }}</td>
                        <td>{{ $k->cabang->nama_cabang ?? '-' }}</td>
                        <td>{{ $k->jabatan->nama_jabatan ?? '-' }}</td>
                        <td>
                            @if($k->status_aktif_karyawan === '1')
                                <span class="badge bg-label-success fw-semibold">Aktif</span>
                            @else
                                <span class="badge bg-label-secondary">Non-Aktif</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data karyawan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($karyawans->hasPages())
        <div class="card-footer d-flex align-items-center py-2">{{ $karyawans->links() }}</div>
        @endif
    </div>

    <!-- Modal Bulk Action -->
    <div class="modal fade" id="modalBulkAction" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark mb-0">Operasi Massal Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label required fw-semibold" style="font-size: 12px;">Tindakan Serentak</label>
                        <select name="action_type" id="selectActionType" class="form-select" required style="border-radius: 8px;">
                            <option value="">-- Pilih Jenis Tindakan --</option>
                            <option value="change_dept">Ubah Departemen Secara Serentak</option>
                            <option value="change_branch">Ubah Kantor / Cabang Secara Serentak</option>
                            <option value="change_status">Ubah Status Aktif / Non-Aktif</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="groupTargetDept">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Departemen Baru</label>
                        <select name="target_dept" class="form-select" style="border-radius: 8px;">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemens as $d)
                                <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="groupTargetBranch">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Cabang Baru</label>
                        <select name="target_branch" class="form-select" style="border-radius: 8px;">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabangs as $c)
                                <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="groupTargetStatus">
                        <label class="form-label fw-semibold" style="font-size: 12px;">Status Baru</label>
                        <select name="target_status" class="form-select" style="border-radius: 8px;">
                            <option value="1">Aktif</option>
                            <option value="0">Non-Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 8px;">Terapkan Perubahan Massal</button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('checkAll');
        const rowChecks = document.querySelectorAll('.row-check');
        if (checkAll) {
            checkAll.addEventListener('change', function () {
                rowChecks.forEach(cb => cb.checked = checkAll.checked);
            });
        }

        const selectAction = document.getElementById('selectActionType');
        const grpDept = document.getElementById('groupTargetDept');
        const grpBranch = document.getElementById('groupTargetBranch');
        const grpStatus = document.getElementById('groupTargetStatus');

        if (selectAction) {
            selectAction.addEventListener('change', function () {
                grpDept.classList.add('d-none');
                grpBranch.classList.add('d-none');
                grpStatus.classList.add('d-none');

                if (this.value === 'change_dept') grpDept.classList.remove('d-none');
                if (this.value === 'change_branch') grpBranch.classList.remove('d-none');
                if (this.value === 'change_status') grpStatus.classList.remove('d-none');
            });
        }
    });
</script>
@endpush
@endsection
