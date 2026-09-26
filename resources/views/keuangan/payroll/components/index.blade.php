@extends('layouts.app')
@section('titlepage', 'Komponen Upah & Potongan')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Keuangan & Payroll</a></li>
    <li class="breadcrumb-item active">Komponen Gaji</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <h4 class="page-title mb-1 d-flex align-items-center gap-2">
            <span>Komponen Upah & Potongan Gaji</span>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ count($components) }} Total
            </span>
        </h4>
        <p class="page-subtitle text-muted mb-0">Master komponen penghasilan tetap, variabel, potongan disiplin, dan kasbon.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5" style="height: 38px; border-radius: 10px; font-weight: 500; padding: 0 14px;">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali ke Payroll</span>
        </a>
        @can('salary_component.create')
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1.5"
                data-bs-toggle="modal" data-bs-target="#modalCreateComponent" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
                <i class="ti ti-plus" style="font-size: 16px;"></i>
                <span>Tambah Komponen</span>
            </button>
        @endcan
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check fs-5 me-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle fs-5 me-2"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Table Card -->
<div class="table-karyawan-wrapper mb-3 w-100 max-w-full" style="border-radius: 12px; overflow: hidden; border: 1px solid #E2E8F0; background: #FFFFFF !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);">
    <div class="table-responsive w-100 max-w-full" style="overflow-x: auto;">
        <table class="table table-hover align-middle w-100 mb-0" style="font-size: 13px;">
            <thead>
                <tr>
                    <th>KODE</th>
                    <th>NAMA KOMPONEN</th>
                    <th>TIPE</th>
                    <th>SIFAT UPAH</th>
                    <th>DEFAULT NOMINAL</th>
                    <th>STATUS</th>
                    <th class="text-end" style="width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($components as $comp)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                                {{ $comp->code }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">{{ $comp->name }}</span>
                        </td>
                        <td>
                            @if ($comp->type === 'EARNING')
                                <span class="badge bg-label-success">Penghasilan</span>
                            @else
                                <span class="badge bg-label-danger">Potongan</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">
                                {{ $comp->is_fixed ? 'Tetap' : 'Variabel' }}
                            </span>
                        </td>
                        <td>
                            <span class="font-mono text-dark" style="font-size: 12.5px;">
                                Rp {{ number_format($comp->default_amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            @if ($comp->is_active)
                                <span class="badge bg-label-success">Aktif</span>
                            @else
                                <span class="badge bg-label-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                @can('salary_component.edit')
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $comp->id }}" title="Edit" style="border-radius: 6px;">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                @endcan
                                @if ($comp->code !== 'BASIC_SALARY')
                                    @can('salary_component.delete')
                                        <form action="{{ route('salary_components.delete', $comp->id) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger delete-confirm" data-label="Komponen {{ $comp->name }}" title="Hapus" style="border-radius: 6px;">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </div>

                            {{-- Modal Edit --}}
                            @can('salary_component.edit')
                                <div class="modal fade" id="modalEdit{{ $comp->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <form action="{{ route('salary_components.update', $comp->id) }}" method="POST" class="modal-content" style="border-radius: 12px;">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-bottom py-3" style="background: #FAF9F8;">
                                                <h5 class="modal-title fw-bold" style="font-family: 'Outfit', sans-serif; color: #3C2A21; font-size: 15px;">
                                                    Edit Komponen {{ $comp->code }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                <div class="mb-3">
                                                    <label class="form-label" style="font-size: 12px; font-weight: 600;">Nama Komponen</label>
                                                    <input type="text" name="name" class="form-control" value="{{ old('name', $comp->name) }}" required style="border-radius: 8px;">
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label" style="font-size: 12px; font-weight: 600;">Sifat Upah</label>
                                                        <select name="is_fixed" class="form-select" style="border-radius: 8px;">
                                                            <option value="1" {{ $comp->is_fixed ? 'selected' : '' }}>Tetap</option>
                                                            <option value="0" {{ !$comp->is_fixed ? 'selected' : '' }}>Variabel</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label" style="font-size: 12px; font-weight: 600;">Kena Pajak PPh21</label>
                                                        <select name="is_taxable" class="form-select" style="border-radius: 8px;">
                                                            <option value="1" {{ $comp->is_taxable ? 'selected' : '' }}>Ya</option>
                                                            <option value="0" {{ !$comp->is_taxable ? 'selected' : '' }}>Tidak</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label" style="font-size: 12px; font-weight: 600;">Dasar Upah BPJS</label>
                                                        <select name="is_bpjs_basis" class="form-select" style="border-radius: 8px;">
                                                            <option value="1" {{ $comp->is_bpjs_basis ? 'selected' : '' }}>Ya</option>
                                                            <option value="0" {{ !$comp->is_bpjs_basis ? 'selected' : '' }}>Tidak</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label" style="font-size: 12px; font-weight: 600;">Status</label>
                                                        <select name="is_active" class="form-select" style="border-radius: 8px;">
                                                            <option value="1" {{ $comp->is_active ? 'selected' : '' }}>Aktif</option>
                                                            <option value="0" {{ !$comp->is_active ? 'selected' : '' }}>Nonaktif</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label" style="font-size: 12px; font-weight: 600;">Default Nominal (Rp)</label>
                                                    <input type="number" name="default_amount" class="form-control" value="{{ old('default_amount', $comp->default_amount) }}" min="0" required style="border-radius: 8px; font-family: 'JetBrains Mono', monospace;">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top py-2.5">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Create Component --}}
@can('salary_component.create')
    <div class="modal fade" id="modalCreateComponent" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('salary_components.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
                @csrf
                <div class="modal-header border-bottom py-3" style="background: #FAF9F8;">
                    <h5 class="modal-title fw-bold" style="font-family: 'Outfit', sans-serif; color: #3C2A21; font-size: 15px;">
                        Tambah Komponen Gaji Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600;">Kode Unik</label>
                            <input type="text" name="code" class="form-control" placeholder="MISAL: TUNJANGAN_SHIFT" required style="border-radius: 8px; text-transform: uppercase; font-family: 'JetBrains Mono', monospace;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600;">Tipe</label>
                            <select name="type" class="form-select" required style="border-radius: 8px;">
                                <option value="EARNING">Penghasilan (Earning)</option>
                                <option value="DEDUCTION">Potongan (Deduction)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px; font-weight: 600;">Nama Komponen</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Tunjangan Shift Malam" required style="border-radius: 8px;">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600;">Sifat Upah</label>
                            <select name="is_fixed" class="form-select" style="border-radius: 8px;">
                                <option value="1">Tetap</option>
                                <option value="0">Variabel</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600;">Kena Pajak PPh21</label>
                            <select name="is_taxable" class="form-select" style="border-radius: 8px;">
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600;">Dasar Upah BPJS</label>
                            <select name="is_bpjs_basis" class="form-select" style="border-radius: 8px;">
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600;">Urutan Tampilan</label>
                            <input type="number" name="sort_order" class="form-control" value="10" style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" style="font-size: 12px; font-weight: 600;">Default Nominal (Rp)</label>
                        <input type="number" name="default_amount" class="form-control" value="0" min="0" required style="border-radius: 8px; font-family: 'JetBrains Mono', monospace;">
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">Simpan Komponen</button>
                </div>
            </form>
        </div>
    </div>
@endcan
@endsection
