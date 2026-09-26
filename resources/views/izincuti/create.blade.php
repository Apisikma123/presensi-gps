@extends('layouts.app')
@section('titlepage', 'Buat Izin Cuti')
@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('izincuti.index') }}">Persetujuan Izin</a></li>
    <li class="breadcrumb-item active">Buat Izin Cuti</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10 col-12">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('izincuti.index') }}" class="btn btn-sm btn-icon btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i>
                    </a>
                    <h5 class="card-title mb-0 fw-bold">Buat Pengajuan Cuti Karyawan</h5>
                </div>
            </div>
            <div class="card-body p-4">
                @include('izincuti.create-modal')
            </div>
        </div>
    </div>
</div>
@endsection
