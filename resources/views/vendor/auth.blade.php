@extends('layouts.app')
@section('titlepage', 'Otorisasi Vendor Deployment')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow-sm border-0" style="border: 1px solid #E2E8F0 !important; border-radius: 14px; background: #FFFFFF;">
            <div class="card-body p-4 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                    style="width: 56px; height: 56px; background: rgba(15, 23, 42, 0.06); color: #0F172A;">
                    <i class="ti ti-shield-lock fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Otorisasi Vendor Deployment</h5>
                <p class="text-muted small mb-4">
                    Masukkan kunci otorisasi vendor deployment untuk mengakses konsol konfigurasi paket instalasi klien.
                </p>

                @if(session('error'))
                    <div class="alert alert-danger py-2 px-3 small text-start mb-3" role="alert" style="border-radius: 8px;">
                        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('vendor.deployment.auth') }}" method="POST">
                    @csrf
                    <div class="mb-3 text-start">
                        <label for="vendor_key" class="form-label text-muted small fw-medium">Kunci Otorisasi Vendor:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px;">
                                <i class="ti ti-key text-muted"></i>
                            </span>
                            <input type="password"
                                   id="vendor_key"
                                   name="vendor_key"
                                   class="form-control border-start-0 ps-0"
                                   placeholder="Masukkan kunci vendor"
                                   required
                                   autocomplete="current-password"
                                   style="border-radius: 0 8px 8px 0; height: 42px;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-2 d-inline-flex align-items-center justify-content-center gap-1.5" style="border-radius: 8px; font-weight: 600;">
                        <i class="ti ti-lock-open"></i>
                        <span>Verifikasi Otorisasi</span>
                    </button>
                </form>

                <div class="mt-4 pt-3 border-top text-muted small" style="font-size: 11.5px;">
                    <i class="ti ti-info-circle me-1"></i> Akses ini diawasi dan dibatasi waktu (30 menit sesi aktif).
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
