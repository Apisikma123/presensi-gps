@extends('layouts.app')
@section('titlepage', 'Manajemen User & Akun')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.hub') }}">Pengaturan Sistem</a></li>
    <li class="breadcrumb-item active">Manajemen Akun User</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="header-title-group">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="page-title mb-0">Manajemen Akun User</h4>
            <span class="badge" style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); color: var(--color-primary); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); font-size: 11.5px; font-weight: 600; border-radius: 20px; padding: 3px 10px;">
                {{ number_format($users->total() ?? 0) }} Total
            </span>
        </div>
        <p class="page-subtitle text-muted mb-0">Kelola akun login admin, manager cabang, serta otentikasi mobile karyawan.</p>
    </div>
    <div class="header-action-group d-flex align-items-center gap-2">
        <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-1.5" id="btncreateUser" style="height: 38px; border-radius: 10px; font-weight: 600; padding: 0 16px;">
            <i class="ti ti-user-plus" style="font-size: 16px;"></i>
            <span>Tambah User</span>
        </a>
    </div>
</div>

<!-- Segmented Tabs Navigation (Consistent with Izin & DESIGN.md) -->
<div class="nav-segment-container mb-3">
    <ul class="nav nav-segment" id="userTabs">
        <li class="nav-item">
            <a href="{{ route('users.index', ['user_type' => 'biasa']) }}" data-tab="biasa" 
               class="nav-link tab-user-link {{ Request('user_type') != 'karyawan' ? 'active' : '' }}">
                <i class="tf-icons ti ti-shield-check"></i>
                <span>User Administrator & Manager</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('users.index', ['user_type' => 'karyawan']) }}" data-tab="karyawan" 
               class="nav-link tab-user-link {{ Request('user_type') == 'karyawan' ? 'active' : '' }}">
                <i class="tf-icons ti ti-users"></i>
                <span>User Karyawan / Barista</span>
            </a>
        </li>
    </ul>
</div>

<!-- 1-Page Tab Content Pane -->
<div id="user-tab-pane" style="position: relative; min-height: 300px; transition: opacity 0.15s ease;">
    <!-- Filter Toolbar -->
    <div class="card mb-3" style="border: 1px solid rgba(60, 42, 33, 0.08); border-radius: 12px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(60, 42, 33, 0.02);">
        <div class="card-body p-3">
            <form action="{{ route('users.index') }}" id="filterForm" method="GET" class="m-0">
                <input type="hidden" name="user_type" id="user_type" value="{{ Request('user_type', 'biasa') }}">
                <div class="row g-2.5 align-items-center">
                    @if (Request('user_type', 'biasa') != 'karyawan')
                        <div class="col-xl-5 col-lg-5 col-md-5 col-12">
                            <div class="input-group input-group-merge user-search-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" name="name" value="{{ Request('name') }}"
                                    placeholder="Cari nama, email, username...">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                            <select name="role_id" id="role_id" class="form-select user-filter-select" onchange="document.getElementById('filterForm').submit();">
                                <option value="">Semua Role / Hak Akses</option>
                                @foreach ($roles as $role)
                                    @if (strtolower($role->name) != 'karyawan')
                                        <option value="{{ $role->id }}" @selected(Request('role_id') == $role->id)>
                                            {{ textUpperCase($role->name) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-12">
                            <div class="d-flex align-items-center gap-2 justify-content-md-end">
                                <button type="submit" class="user-btn-primary">
                                    <i class="ti ti-search" style="font-size: 14px;"></i>
                                    <span>Cari</span>
                                </button>
                                @if (Request('name') || Request('role_id'))
                                    <a href="{{ route('users.index', ['user_type' => 'biasa']) }}" class="user-btn-reset tab-user-link" title="Reset Filter">
                                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                                        <span>Reset</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="col-xl-6 col-lg-6 col-md-7 col-12">
                            <div class="input-group input-group-merge user-search-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" name="name" value="{{ Request('name') }}"
                                    placeholder="Cari nama karyawan, username, email...">
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-5 col-12">
                            <div class="d-flex align-items-center gap-2 justify-content-md-end">
                                <button type="submit" class="user-btn-primary">
                                    <i class="ti ti-search" style="font-size: 14px;"></i>
                                    <span>Cari</span>
                                </button>
                                @if (Request('name'))
                                    <a href="{{ route('users.index', ['user_type' => 'karyawan']) }}" class="user-btn-reset tab-user-link" title="Reset Filter">
                                        <i class="ti ti-refresh" style="font-size: 14px;"></i>
                                        <span>Reset</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

<!-- Table Card -->
<div class="card mb-3" style="border: 1px solid rgba(60, 42, 33, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(60, 42, 33, 0.02);">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background: #FAF9F8; border-bottom: 1px solid rgba(60, 42, 33, 0.08);">
                <tr>
                    <th style="width: 50px; color: #755841; font-weight: 600; font-size: 11px; letter-spacing: 0.05em;" class="text-center font-mono">NO</th>
                    <th style="color: #755841; font-weight: 600; font-size: 11px; letter-spacing: 0.05em;">PENGGUNA / AKUN</th>
                    <th style="color: #755841; font-weight: 600; font-size: 11px; letter-spacing: 0.05em;">PERAN / ROLE</th>
                    <th class="text-center" style="color: #755841; font-weight: 600; font-size: 11px; letter-spacing: 0.05em;">STATUS KONEKSI</th>
                    <th style="color: #755841; font-weight: 600; font-size: 11px; letter-spacing: 0.05em;">HAK AKSES UNIT</th>
                    <th class="text-end" style="width: 100px; color: #755841; font-weight: 600; font-size: 11px; letter-spacing: 0.05em;">AKSI</th>
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
                                    style="width: 34px; height: 34px; background: rgba(var(--bs-primary-rgb), 0.08); color: var(--theme-color-1, #3C2A21); font-size: 12px;">
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
                                    <button type="submit" class="delete-confirm" data-level="2" data-label="User Admin" title="Hapus User">
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

<!-- Pagination Footer -->
<div class="card" style="border: 1px solid rgba(60, 42, 33, 0.08); border-radius: 12px; background: #FFFFFF;">
    <div class="card-body py-2.5 px-3">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
</div> <!-- /#user-tab-pane -->

<x-modal-form id="mdlcreateUser" size="" show="loadcreateUser" title="Tambah User" />
<x-modal-form id="mdleditUser" size="" show="loadeditUser" title="Edit User" />

@endsection

@push('mystyle')
<style>
    /* Strict Pure White Text & Icon on Active Nav Segment Tabs */
    .nav-segment .nav-link.active,
    .nav-segment .nav-link.active *,
    .nav-segment .nav-link.active span,
    .nav-segment .nav-link.active i,
    .nav-segment .nav-link.active .tf-icons {
        color: #FFFFFF !important;
    }

    .nav-segment .nav-link.active {
        background-color: var(--theme-color-1, #3C2A21) !important;
        border-color: var(--theme-color-1, #3C2A21) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
        box-shadow: 0 2px 6px rgba(var(--bs-primary-rgb, 60, 42, 33), 0.25) !important;
    }

    .nav-segment .nav-link:not(.active),
    .nav-segment .nav-link:not(.active) span {
        color: #64748B !important;
    }

    .nav-segment .nav-link:not(.active) i,
    .nav-segment .nav-link:not(.active) .tf-icons {
        color: #94A3B8 !important;
    }

    .nav-segment .nav-link:hover:not(.active) {
        background-color: var(--bs-primary-bg-subtle, #FAF9F8) !important;
        color: var(--theme-color-1, #3C2A21) !important;
    }

    .nav-segment .nav-link:hover:not(.active) span,
    .nav-segment .nav-link:hover:not(.active) i,
    .nav-segment .nav-link:hover:not(.active) .tf-icons {
        color: var(--theme-color-1, #3C2A21) !important;
    }

    /* Search & Filter Toolbar Controls */
    .user-search-group {
        border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.14)) !important;
        border-radius: 8px !important;
        background: #FFFFFF !important;
        transition: all 0.15s ease;
        overflow: hidden;
    }

    .user-search-group:focus-within {
        border-color: var(--theme-color-1, #3C2A21) !important;
        box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb, 60, 42, 33), 0.10) !important;
    }

    .user-search-group .input-group-text {
        background: transparent !important;
        border: none !important;
        color: #755841 !important;
        padding-left: 12px !important;
        padding-right: 6px !important;
        font-size: 15px !important;
    }

    .user-search-group .form-control {
        border: none !important;
        box-shadow: none !important;
        height: 38px !important;
        font-size: 13px !important;
        color: #1A1C1C !important;
        background: transparent !important;
        padding-left: 4px !important;
    }

    .user-search-group .form-control::placeholder {
        color: #81756F !important;
        font-size: 12.5px !important;
    }

    .user-filter-select {
        height: 38px !important;
        border: 1px solid var(--theme-border, rgba(60, 42, 33, 0.14)) !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        color: #1A1C1C !important;
        background-color: #FFFFFF !important;
        padding: 0 12px !important;
        transition: all 0.15s ease;
    }

    .user-filter-select:focus {
        border-color: var(--theme-color-1, #3C2A21) !important;
        box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb, 60, 42, 33), 0.10) !important;
    }

    .user-btn-primary {
        height: 38px !important;
        padding: 0 18px !important;
        border-radius: 8px !important;
        background: var(--theme-color-1, #3C2A21) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
        font-weight: 600 !important;
        font-size: 12.5px !important;
        border: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        transition: all 0.15s ease;
    }

    .user-btn-primary:hover {
        background: var(--color-primary-hover, var(--theme-color-2)) !important;
        color: var(--theme-primary-contrast, #FFFFFF) !important;
    }

    .user-btn-primary:active {
        transform: translateY(1px);
    }

    .user-btn-reset {
        height: 38px !important;
        padding: 0 12px !important;
        border-radius: 8px !important;
        background: #FAF9F8 !important;
        color: #755841 !important;
        font-weight: 500 !important;
        font-size: 12.5px !important;
        border: 1px solid rgba(60, 42, 33, 0.14) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
        text-decoration: none !important;
        transition: all 0.15s ease;
    }

    .user-btn-reset:hover {
        background: #F4F3F2 !important;
        color: var(--theme-color-1, #3C2A21) !important;
        border-color: var(--theme-color-1, #3C2A21) !important;
    }

    .user-btn-reset:active {
        transform: translateY(1px);
    }
</style>
@endpush

@push('myscript')
<script>
    $(function() {
        // Modal handlers
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

        // Instant 1-Page Tab Switching for User Management
        $(document).on('click', '.tab-user-link', function(e) {
            e.preventDefault();
            const targetUrl = $(this).attr('href');
            if ($(this).hasClass('active') && !$(this).hasClass('user-btn-reset')) return;

            $('.tab-user-link').removeClass('active');
            $(this).addClass('active');

            const pane = $('#user-tab-pane');
            pane.css('opacity', '0.35');

            $.get(targetUrl, function(html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newPane = doc.querySelector('#user-tab-pane');
                if (newPane) {
                    pane.html(newPane.innerHTML);
                } else {
                    window.location.href = targetUrl;
                    return;
                }
                pane.css('opacity', '1');
                window.history.pushState(null, '', targetUrl);
            }).fail(function() {
                window.location.href = targetUrl;
            });
        });

        // Seamless pagination within #user-tab-pane
        $(document).on('click', '#user-tab-pane .pagination a', function(e) {
            e.preventDefault();
            const pageUrl = $(this).attr('href');
            if (!pageUrl) return;

            const pane = $('#user-tab-pane');
            pane.css('opacity', '0.35');
            $.get(pageUrl, function(html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newPane = doc.querySelector('#user-tab-pane');
                if (newPane) {
                    pane.html(newPane.innerHTML);
                } else {
                    window.location.href = pageUrl;
                    return;
                }
                pane.css('opacity', '1');
                window.history.pushState(null, '', pageUrl);
                $('html, body').animate({ scrollTop: $('#userTabs').offset().top - 20 }, 200);
            }).fail(function() {
                window.location.href = pageUrl;
            });
        });

        // Seamless filter form submission within #user-tab-pane
        $(document).on('submit', '#filterForm', function(e) {
            e.preventDefault();
            const form = $(this);
            const targetUrl = form.attr('action') + '?' + form.serialize();

            const pane = $('#user-tab-pane');
            pane.css('opacity', '0.35');
            $.get(targetUrl, function(html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newPane = doc.querySelector('#user-tab-pane');
                if (newPane) {
                    pane.html(newPane.innerHTML);
                } else {
                    form.off('submit').submit();
                    return;
                }
                pane.css('opacity', '1');
                window.history.pushState(null, '', targetUrl);
            }).fail(function() {
                form.off('submit').submit();
            });
        });

        // Browser back/forward navigation support
        window.addEventListener('popstate', function() {
            window.location.reload();
        });
    });
</script>
@endpush
