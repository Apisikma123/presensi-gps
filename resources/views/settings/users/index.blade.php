@extends('layouts.app')
@section('titlepage', 'Manajemen User & Akun')

@section('content')
@section('navigasi')
    <span>Manajemen Akun</span>
@endsection

<!-- Top Header Toolbar -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <span>Manajemen Akun User</span>
            <span class="badge bg-light text-dark font-mono" style="border: 1px solid #E2E8F0; font-size: 11px;">
                {{ $users->total() }} Total
            </span>
        </h5>
        <small class="text-muted" style="font-size: 12px;">Kelola akun login admin, manager cabang, serta otentikasi mobile karyawan.</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="#" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" id="btncreateUser" style="height: 36px; border-radius: 8px;">
            <i class="ti ti-user-plus"></i>
            <span>Tambah User</span>
        </a>
    </div>
</div>

<!-- Tabs & Filter Bar -->
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);">
    <div class="card-header border-bottom py-2 px-3" style="background: #FAFBFC;">
        <ul class="nav nav-pills gap-1" id="userTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link py-1 px-3 {{ Request('user_type') != 'karyawan' ? 'active fw-bold' : 'text-muted' }}" 
                   href="{{ route('users.index', ['user_type' => 'biasa']) }}" style="font-size: 12.5px; border-radius: 6px;">
                    <i class="ti ti-shield-check me-1"></i> User Administrator & Manager
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-3 {{ Request('user_type') == 'karyawan' ? 'active fw-bold' : 'text-muted' }}" 
                   href="{{ route('users.index', ['user_type' => 'karyawan']) }}" style="font-size: 12.5px; border-radius: 6px;">
                    <i class="ti ti-users me-1"></i> User Karyawan / Barista
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body p-2.5">
        <form action="{{ route('users.index') }}" id="filterForm" method="GET">
            <input type="hidden" name="user_type" id="user_type" value="{{ Request('user_type', 'biasa') }}">
            <div class="row g-2 align-items-center">
                @if (Request('user_type', 'biasa') != 'karyawan')
                    <div class="col-lg-6 col-md-6 col-12">
                        <x-input-with-icon label="" value="{{ Request('name') }}" name="name" icon="ti ti-search"
                            placeholder="Cari nama, email, username..." hideLabel="true" />
                    </div>
                    <div class="col-lg-4 col-md-3 col-12">
                        <select name="role_id" id="role_id" class="form-select" onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Role / Akses</option>
                            @foreach ($roles as $role)
                                @if (strtolower($role->name) != 'karyawan')
                                    <option value="{{ $role->id }}" @selected(Request('role_id') == $role->id)>
                                        {{ textUpperCase($role->name) }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-3 col-12 d-flex align-items-center gap-1.5" style="display: flex !important; flex-direction: row !important; align-items: center !important;">
                        <button type="submit" class="btn w-100 text-white fw-semibold shadow-sm"
                            style="background-color: #1E4D3E; border: 1px solid #11382C; border-radius: 8px; height: 36px; font-size: 12.5px; display: inline-flex !important; flex-direction: row !important; align-items: center !important; justify-content: center !important; gap: 6px !important; white-space: nowrap !important;">
                            <i class="ti ti-search" style="font-size: 14px; display: inline-block !important; width: auto !important;"></i>
                            <span style="display: inline-block !important; width: auto !important;">Cari</span>
                        </button>
                        @if (Request('name') || Request('role_id'))
                            <a href="{{ route('users.index', ['user_type' => 'biasa']) }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-0"
                                style="height: 36px; width: 36px; min-width: 36px; border-radius: 8px;" title="Reset Filter">
                                <i class="ti ti-refresh" style="font-size: 14px;"></i>
                            </a>
                        @endif
                    </div>
                @else
                    <div class="col-lg-10 col-md-9 col-12">
                        <x-input-with-icon label="" value="{{ Request('name') }}" name="name" icon="ti ti-search"
                            placeholder="Cari nama karyawan, username, email..." hideLabel="true" />
                    </div>
                    <div class="col-lg-2 col-md-3 col-12 d-flex align-items-center gap-1.5" style="display: flex !important; flex-direction: row !important; align-items: center !important;">
                        <button type="submit" class="btn w-100 text-white fw-semibold shadow-sm"
                            style="background-color: #1E4D3E; border: 1px solid #11382C; border-radius: 8px; height: 36px; font-size: 12.5px; display: inline-flex !important; flex-direction: row !important; align-items: center !important; justify-content: center !important; gap: 6px !important; white-space: nowrap !important;">
                            <i class="ti ti-search" style="font-size: 14px; display: inline-block !important; width: auto !important;"></i>
                            <span style="display: inline-block !important; width: auto !important;">Cari</span>
                        </button>
                        @if (Request('name'))
                            <a href="{{ route('users.index', ['user_type' => 'karyawan']) }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-0"
                                style="height: 36px; width: 36px; min-width: 36px; border-radius: 8px;" title="Reset Filter">
                                <i class="ti ti-refresh" style="font-size: 14px;"></i>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">NO</th>
                    <th>PENGGUNA / AKUN</th>
                    <th>PERAN / ROLE</th>
                    <th class="text-center">STATUS KONEKSI</th>
                    <th>HAK AKSES UNIT</th>
                    <th class="text-end" style="width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $d)
                    <tr>
                        <td class="text-center font-mono text-muted" style="font-size: 12px;">
                            {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold"
                                    style="width: 34px; height: 34px; background: rgba(30, 77, 62, 0.08); color: #1E4D3E; font-size: 12px;">
                                    {{ strtoupper(substr($d->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 13px;">
                                        {{ $d->name }}
                                        <span class="text-muted fw-normal font-mono ms-1" style="font-size: 11px;">({{ $d->username }})</span>
                                    </div>
                                    <small class="text-muted d-flex align-items-center gap-1" style="font-size: 11.5px;">
                                        <i class="ti ti-mail" style="font-size: 12px;"></i> {{ $d->email }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach ($d->roles as $role)
                                    <span class="badge bg-label-primary font-mono" style="font-size: 10.5px;">
                                        {{ ucwords($role->name) }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-center">
                            @if (!empty($d->nik))
                                <span class="badge bg-label-success font-mono" style="font-size: 10.5px;">
                                    <i class="ti ti-link me-0.5"></i> Terhubung NIK
                                </span>
                            @elseif(Request('user_type') == 'karyawan')
                                <span class="badge bg-label-danger font-mono" style="font-size: 10.5px;">
                                    <i class="ti ti-link-off me-0.5"></i> Tidak Terhubung
                                </span>
                            @else
                                <span class="text-muted font-mono" style="font-size: 11px;">Akun Admin</span>
                            @endif
                        </td>
                        <td>
                            @if (Request('user_type', 'biasa') != 'karyawan')
                                <div class="d-flex flex-column gap-1" style="font-size: 11px;">
                                    <div>
                                        @if ($d->hasRole('super admin'))
                                            <span class="badge bg-primary font-mono">Semua Cabang</span>
                                        @elseif ($d->cabangs && $d->cabangs->count() > 0)
                                            <span class="badge bg-label-primary font-mono" title="{{ $d->cabangs->pluck('nama_cabang')->implode(', ') }}">
                                                {{ $d->cabangs->count() }} Cabang Outlet
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if ($d->hasRole('super admin'))
                                            <span class="badge bg-success font-mono">Semua Dept</span>
                                        @elseif ($d->departemens && $d->departemens->count() > 0)
                                            <span class="badge bg-label-success font-mono" title="{{ $d->departemens->pluck('nama_dept')->implode(', ') }}">
                                                {{ $d->departemens->count() }} Departemen
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-light text-muted font-mono" style="border: 1px solid #E2E8F0; font-size: 10.5px;">
                                    Karyawan Mobile
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                <button type="button" class="btnEdit editUser" id="{{ Crypt::encrypt($d->id) }}" title="Edit User">
                                    <i class="ti ti-edit"></i>
                                </button>

                                <form method="POST" name="deleteform" class="deleteform d-inline m-0"
                                    action="{{ route('users.delete', Crypt::encrypt($d->id)) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-confirm" title="Hapus User">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="ti ti-users-minus text-muted fs-1 d-block mb-2" style="opacity: 0.4;"></i>
                            <h6 class="mb-1 text-dark fw-semibold">Tidak Ada Data User</h6>
                            <small class="text-muted">Klik Tambah User untuk membuat akun baru.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination Footer with Slide Controls -->
<div class="card" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <!-- Counter info -->
            <div class="text-muted" style="font-size: 12px;">
                @if ($users->total() > 0)
                    Menampilkan <span class="fw-bold text-dark font-mono">{{ $users->firstItem() }}</span> - <span class="fw-bold text-dark font-mono">{{ $users->lastItem() }}</span> dari <span class="fw-bold text-dark font-mono">{{ $users->total() }}</span> total user
                @else
                    Menampilkan 0 data
                @endif
            </div>

            <!-- Interactive Page Slider (Slide Selector) -->
            @if ($users->lastPage() > 1)
                <div class="page-slider-container">
                    <small class="text-muted fw-semibold" style="font-size: 11.5px;">Slide Halaman:</small>
                    <input type="range" class="page-slider-range" id="pageSlider"
                        min="1" max="{{ $users->lastPage() }}" value="{{ $users->currentPage() }}"
                        oninput="document.getElementById('sliderBadge').innerText = this.value"
                        onchange="navigatePage(this.value)">
                    <span class="badge bg-primary font-mono" id="sliderBadge">{{ $users->currentPage() }}</span>
                </div>
            @endif

            <!-- Standard Pagination Links -->
            <div class="d-flex align-items-center">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<x-modal-form id="mdlcreateUser" size="" show="loadcreateUser" title="Tambah User" />
<x-modal-form id="mdleditUser" size="" show="loadeditUser" title="Edit User" />

@endsection

@push('myscript')
<script>
    function navigatePage(pageNum) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', pageNum);
        window.location.href = url.toString();
    }

    $(function() {
        $("#btncreateUser").click(function(e) {
            e.preventDefault();
            $('#mdlcreateUser').modal("show");
            $("#loadcreateUser").load('/users/create');
        });

        $(document).on('click', '.editUser', function(e) {
            e.preventDefault();
            var id = $(this).attr("id");
            $('#mdleditUser').modal("show");
            $("#loadeditUser").load('/users/' + id + '/edit');
        });
    });
</script>
@endpush
