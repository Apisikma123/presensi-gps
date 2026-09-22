@extends('layouts.app')
@section('titlepage', 'Buat Izin Sakit')
@section('navigasi')
    <span><a href="{{ route('izinsakit.index') }}">Izin Sakit</a></span> / <span>Buat Izin Sakit</span>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10 col-12">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('izinsakit.index') }}" class="btn btn-sm btn-icon btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i>
                    </a>
                    <h5 class="card-title mb-0 fw-bold">Buat Pengajuan Izin Sakit Karyawan</h5>
                </div>
            </div>
            <div class="card-body p-4">
                @include('izinsakit.create-modal')
            </div>
        </div>
    </div>
</div>
@endsection
