@extends('layouts.app')
@section('titlepage', 'Edit SPK Lembur ' . $lembur->no_spk)

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('overtime.index') }}">SPK Lembur</a></li>
    <li class="breadcrumb-item active">Edit SPK</li>
@endsection

@section('content')

<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="page-title mb-1">Edit SPK Lembur {{ $lembur->no_spk }}</h4>
        <p class="page-subtitle text-muted mb-0 font-mono" style="font-size: 12.5px;">
            Karyawan: {{ $lembur->karyawan->nama_karyawan }} ({{ $lembur->nik }}) &bull; Dept: {{ $lembur->karyawan->departemen->nama_dept ?? '-' }}
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
        <div class="fw-bold mb-1">Periksa kembali isian:</div>
        <ul class="mb-0 ps-3" style="font-size: 13px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card mb-3" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);">
            <div class="card-header border-bottom py-3" style="background: #FAF9F8;">
                <h5 class="card-title mb-0" style="font-family: 'Outfit', sans-serif; font-size: 15px; color: #3C2A21;">
                    <i class="ti ti-file-pencil me-1.5 text-primary"></i> Perbarui Informasi Penugasan
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('overtime.update', $lembur->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">Karyawan</label>
                        <input type="text" class="form-control" value="{{ $lembur->karyawan->nama_karyawan }} ({{ $lembur->nik }})" disabled style="background: #F4F3F2; border-radius: 8px; font-size: 13.5px;">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">
                                Tanggal Lembur <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal" class="form-control"
                                value="{{ old('tanggal', $lembur->tanggal->format('Y-m-d')) }}" required style="border-radius: 8px; font-size: 13.5px;">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">
                                Tipe Hari <span class="text-danger">*</span>
                            </label>
                            <select name="day_type" class="form-select" required style="border-radius: 8px; font-size: 13.5px;">
                                <option value="WORKDAY" {{ old('day_type', $lembur->day_type) == 'WORKDAY' ? 'selected' : '' }}>Hari Kerja</option>
                                <option value="OFFDAY_5DAYS" {{ old('day_type', $lembur->day_type) == 'OFFDAY_5DAYS' ? 'selected' : '' }}>Libur Mingguan (5 Hari Kerja)</option>
                                <option value="OFFDAY_6DAYS" {{ old('day_type', $lembur->day_type) == 'OFFDAY_6DAYS' ? 'selected' : '' }}>Libur Mingguan (6 Hari Kerja)</option>
                                <option value="PUBLIC_HOLIDAY" {{ old('day_type', $lembur->day_type) == 'PUBLIC_HOLIDAY' ? 'selected' : '' }}>Libur Nasional</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">
                                Waktu Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" name="lembur_mulai" class="form-control"
                                value="{{ old('lembur_mulai', $lembur->lembur_mulai->format('Y-m-d\TH:i')) }}" required
                                style="border-radius: 8px; font-family: 'JetBrains Mono', monospace; font-size: 13px;">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">
                                Waktu Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" name="lembur_selesai" class="form-control"
                                value="{{ old('lembur_selesai', $lembur->lembur_selesai->format('Y-m-d\TH:i')) }}" required
                                style="border-radius: 8px; font-family: 'JetBrains Mono', monospace; font-size: 13px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">Kebijakan Lembur</label>
                        <select name="overtime_policy_id" class="form-select" style="border-radius: 8px; font-size: 13.5px;">
                            @foreach ($policies as $pol)
                                <option value="{{ $pol->id }}" {{ old('overtime_policy_id', $lembur->overtime_policy_id) == $pol->id ? 'selected' : '' }}>
                                    {{ $pol->name }} ({{ $pol->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #4F4540; text-transform: uppercase;">
                            Uraian Pekerjaan / Tugas Lembur <span class="text-danger">*</span>
                        </label>
                        <textarea name="keterangan" rows="3" class="form-control" required
                            style="border-radius: 8px; font-size: 13px;">{{ old('keterangan', $lembur->keterangan) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5">
                            <i class="ti ti-device-floppy"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
