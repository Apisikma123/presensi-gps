@extends('layouts.app')
@section('titlepage', 'Buat Template Onboarding')

@section('navigasi')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('karyawan.index') }}">Kepegawaian</a></li>
    <li class="breadcrumb-item"><a href="{{ route('onboarding.index') }}">Onboarding</a></li>
    <li class="breadcrumb-item"><a href="{{ route('onboarding.templates') }}">Template Checklist</a></li>
    <li class="breadcrumb-item active">Buat Template</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="admin-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('onboarding.templates') }}" class="text-decoration-none text-muted mb-1 d-inline-flex align-items-center gap-1" style="font-size: 13px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Template
        </a>
        <h4 class="page-title mb-1">Buat Template Checklist Onboarding</h4>
        <p class="page-subtitle text-muted mb-0">Rancang urutan tugas orientasi kerja dan tenggat waktu per kategori untuk karyawan baru.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('onboarding.templates') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5">
            <i class="ti ti-arrow-left"></i>
            <span>Batal</span>
        </a>
    </div>
</div>

<div class="card mx-auto shadow-sm" style="max-width: 900px; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; overflow: hidden; background: #FFFFFF;">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title fw-bold text-dark mb-0">Informasi Template</h5>
    </div>
    <form action="{{ route('onboarding.templates.store') }}" method="POST">
        @csrf
        <div class="card-body p-4">
            @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label class="form-label required fw-semibold" style="font-size: 12px;">Nama Template</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Onboarding Tim IT & Software Engineer" value="{{ old('name') }}" required style="border-radius: 8px;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Departemen Khusus (Opsional)</label>
                    <select name="kode_dept" class="form-select" style="border-radius: 8px;">
                        <option value="">-- Berlaku Semua Departemen --</option>
                        @foreach($departemens as $d)
                            <option value="{{ $d->kode_dept }}" {{ old('kode_dept') === $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Deskripsi Template</label>
                    <textarea name="description" rows="2" class="form-control" placeholder="Tujuan atau panduan singkat tentang template ini..." style="border-radius: 8px;">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="border-top pt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0">Daftar Tugas Checklist</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" id="btnAddTask">
                        <i class="ti ti-plus"></i>
                        <span>Tambah Baris Tugas</span>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableTasks" style="border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 8px;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50%;">NAMA TUGAS / PROSEDUR</th>
                                <th style="width: 30%;">KATEGORI</th>
                                <th style="width: 15%;">HARI KE (H+)</th>
                                <th style="width: 5%;" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="taskRows">
                            <tr>
                                <td>
                                    <input type="text" name="tasks[0][task_name]" class="form-control form-control-sm" placeholder="Contoh: Penyerahan Berkas KTP/NPWP" required style="border-radius: 6px;">
                                </td>
                                <td>
                                    <select name="tasks[0][category]" class="form-select form-select-sm" required style="border-radius: 6px;">
                                        <option value="DOCUMENT">Dokumen & Legalitas</option>
                                        <option value="IT_ACCESS">Akses IT & Sistem</option>
                                        <option value="HR_BRIEFING" selected>Orientasi HR</option>
                                        <option value="ASSET">Perangkat & Aset</option>
                                        <option value="TRAINING">Pelatihan & SOP</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="tasks[0][day_offset]" class="form-control form-control-sm font-mono" min="0" value="1" required style="border-radius: 6px;">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-icon remove-row" disabled style="border-radius: 6px;">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light d-flex justify-content-end gap-2 py-3 px-4 border-top">
            <a href="{{ route('onboarding.templates') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">Batal</a>
            <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-1.5">
                <i class="ti ti-check"></i>
                <span>Simpan Template</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 1;
    const btnAddTask = document.getElementById('btnAddTask');
    const taskRows = document.getElementById('taskRows');

    btnAddTask.addEventListener('click', function () {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" name="tasks[${rowIndex}][task_name]" class="form-control form-control-sm" placeholder="Nama tugas..." required style="border-radius: 6px;">
            </td>
            <td>
                <select name="tasks[${rowIndex}][category]" class="form-select form-select-sm" required style="border-radius: 6px;">
                    <option value="DOCUMENT">Dokumen & Legalitas</option>
                    <option value="IT_ACCESS">Akses IT & Sistem</option>
                    <option value="HR_BRIEFING" selected>Orientasi HR</option>
                    <option value="ASSET">Perangkat & Aset</option>
                    <option value="TRAINING">Pelatihan & SOP</option>
                </select>
            </td>
            <td>
                <input type="number" name="tasks[${rowIndex}][day_offset]" class="form-control form-control-sm font-mono" min="0" value="${rowIndex + 1}" required style="border-radius: 6px;">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-icon remove-row" style="border-radius: 6px;">
                    <i class="ti ti-trash"></i>
                </button>
            </td>
        `;
        taskRows.appendChild(tr);
        rowIndex++;
    });

    taskRows.addEventListener('click', function (e) {
        if (e.target.closest('.remove-row')) {
            const btn = e.target.closest('.remove-row');
            if (!btn.disabled && taskRows.children.length > 1) {
                btn.closest('tr').remove();
            }
        }
    });
});
</script>
@endsection
