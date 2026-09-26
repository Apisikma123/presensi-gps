@extends('layouts.app')
@section('titlepage', 'Hosting Readiness Diagnostic')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.hub') }}">Pengaturan Sistem</a></li>
    <li class="breadcrumb-item active">Hosting Readiness Diagnostic</li>
@endsection

@section('content')

<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">Rumahweb Hosting Readiness Audit</h5>
                    <small class="text-muted">Target: Shared Hosting 2 Cabang (30–50 Karyawan)</small>
                </div>
                <div>
                    @if($data['verdict'] === 'READY FOR REAL LOAD TEST')
                        <span class="badge bg-success fs-6 px-3 py-2">READY FOR REAL LOAD TEST</span>
                    @elseif($data['verdict'] === 'READY WITH WARNING')
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">READY WITH WARNING</span>
                    @else
                        <span class="badge bg-danger fs-6 px-3 py-2">NOT READY</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info py-2 mb-4">
                    <i class="ti ti-shield-lock me-1"></i>
                    <strong>Diagnostic Security Notice:</strong> Halaman diagnostik ini hanya aktif jika <code>HOSTING_READINESS_ENABLED=true</code> diatur pada file <code>.env</code>. Tidak ada kredensial, kata sandi database, atau API key yang ditampilkan.
                </div>

                <!-- 1. Core Health Summary -->
                <h6 class="text-primary mb-3"><i class="ti ti-heart-rate-monitor me-1"></i> 1. Core System Health</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Komponen</th>
                                <th>Status</th>
                                <th>Detail Ukuran / Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>PHP Environment</strong></td>
                                <td><span class="badge bg-{{ $data['php']['status'] === 'PASS' ? 'success' : ($data['php']['status'] === 'WARNING' ? 'warning text-dark' : 'danger') }}">{{ $data['php']['status'] }}</span></td>
                                <td>PHP {{ $data['php']['version'] }} (Memory: {{ $data['php']['memory_limit'] }}, Max Exec: {{ $data['php']['max_execution_time'] }})</td>
                            </tr>
                            <tr>
                                <td><strong>MySQL Connection</strong></td>
                                <td><span class="badge bg-{{ $data['database']['status'] === 'PASS' ? 'success' : ($data['database']['status'] === 'WARNING' ? 'warning text-dark' : 'danger') }}">{{ $data['database']['status'] }}</span></td>
                                <td>MySQL v{{ $data['database']['version'] }} (Ping Latency: {{ $data['database']['ping_latency_ms'] }} ms)</td>
                            </tr>
                            <tr>
                                <td><strong>Storage Filesystem</strong></td>
                                <td><span class="badge bg-{{ $data['storage']['status'] === 'PASS' ? 'success' : ($data['storage']['status'] === 'WARNING' ? 'warning text-dark' : 'danger') }}">{{ $data['storage']['status'] }}</span></td>
                                <td>Write: {{ $data['storage']['write_speed_mb_s'] }} MB/s, Read: {{ $data['storage']['read_speed_mb_s'] }} MB/s (Upload & Archive Writable)</td>
                            </tr>
                            <tr>
                                <td><strong>OPcache Engine</strong></td>
                                <td><span class="badge bg-{{ $data['opcache']['status'] === 'PASS' ? 'success' : 'warning text-dark' }}">{{ $data['opcache']['status'] }}</span></td>
                                <td>Hit Rate: {{ $data['opcache']['hit_rate'] }} (Memory Used: {{ $data['opcache']['memory_used'] }})</td>
                            </tr>
                            <tr>
                                <td><strong>Laravel Production Setup</strong></td>
                                <td><span class="badge bg-{{ $data['laravel']['status'] === 'PASS' ? 'success' : 'warning text-dark' }}">{{ $data['laravel']['status'] }}</span></td>
                                <td>Env: {{ $data['laravel']['env'] }}, Debug: {{ $data['laravel']['debug'] ? 'True (Warning)' : 'False (Safe)' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Scheduler Heartbeat</strong></td>
                                <td><span class="badge bg-{{ $data['scheduler']['heartbeat_status'] === 'NORMAL' ? 'success' : ($data['scheduler']['heartbeat_status'] === 'WARNING' ? 'warning text-dark' : 'secondary') }}">{{ $data['scheduler']['heartbeat_status'] }}</span></td>
                                <td>{{ $data['scheduler']['last_heartbeat'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 2. Measured Metrics -->
                <h6 class="text-primary mb-3"><i class="ti ti-dashboard me-1"></i> 2. Measured Server Limits & Extensions</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light text-center">
                            <small class="text-muted d-block">Upload Max Filesize</small>
                            <span class="fs-5 fw-bold">{{ $data['php']['upload_max_filesize'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light text-center">
                            <small class="text-muted d-block">Post Max Size</small>
                            <span class="fs-5 fw-bold">{{ $data['php']['post_max_size'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light text-center">
                            <small class="text-muted d-block">Disk Free Space</small>
                            <span class="fs-5 fw-bold">{{ $data['disk']['disk_free'] }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light text-center">
                            <small class="text-muted d-block">WebP Photo Support</small>
                            <span class="fs-5 fw-bold text-{{ $data['php']['webp_support'] ? 'success' : 'danger' }}">{{ $data['php']['webp_support'] ? 'Active (GD WebP)' : 'Disabled' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Extensions checklist -->
                <h6 class="text-muted mb-2 fs-7">PHP Extensions Verified:</h6>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @foreach($data['php']['extensions'] as $ext => $loaded)
                        <span class="badge {{ $loaded ? 'bg-label-success' : 'bg-label-danger' }}">
                            {{ $ext }}: {{ $loaded ? 'OK' : 'MISSING' }}
                        </span>
                    @endforeach
                </div>

                <!-- 3. cPanel Checklist -->
                <h6 class="text-primary mb-3"><i class="ti ti-list-check me-1"></i> 3. cPanel / CloudLinux CageFS Manual Checklist</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Resource / Quota</th>
                                <th>Rekomendasi Shared Hosting (2 Cabang)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['cpanel_checklist'] as $key => $val)
                                <tr>
                                    <td><strong>{{ $key }}</strong></td>
                                    <td><span class="text-muted">{{ $val }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
