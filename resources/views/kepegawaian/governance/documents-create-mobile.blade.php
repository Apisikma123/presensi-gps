@extends('layouts.mobile.modern')

@section('title', 'Unggah Dokumen')

@section('header_left')
    <a href="{{ route('document.index') }}"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all"
        title="Kembali">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.css" rel="stylesheet">
    <style>
        body {
            background: {{ $t['bg_body'] ?? '#f8fafc' }} !important;
        }

        .form-container {
            padding: 12px 6px calc(110px + env(safe-area-inset-bottom, 0px)) !important;
        }

        /* Form Controls Matching Form Izin DNA */
        .form-label-group {
            position: relative;
            margin-bottom: 12px;
            background: #ffffff !important;
            border: 1px solid {{ $t['primary'] ?? '#1B365D' }};
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 14px;
            top: 11px;
            font-size: 20px;
            color: {{ $t['primary'] ?? '#1B365D' }};
            z-index: 10;
            pointer-events: none;
        }

        .form-label-group input,
        .form-label-group select,
        .form-label-group textarea {
            width: 100% !important;
            height: 44px;
            padding: 18px 14px 2px 42px !important;
            font-size: 14px;
            font-weight: 500;
            color: {{ $t['primary'] ?? '#1B365D' }};
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            display: block !important;
            appearance: none;
            -webkit-appearance: none;
        }

        .form-label-group select {
            padding-right: 36px !important;
            cursor: pointer;
        }

        .select-chevron {
            position: absolute;
            right: 14px;
            top: 12px;
            font-size: 18px;
            color: {{ $t['primary'] ?? '#1B365D' }};
            pointer-events: none;
            z-index: 5;
        }

        .form-label-group textarea {
            height: 80px !important;
            padding-top: 22px !important;
            resize: none;
        }

        .form-label-group label {
            position: absolute;
            top: 11px;
            left: 42px;
            font-size: 14px;
            color: {{ $t['primary'] ?? '#1B365D' }};
            opacity: 0.8;
            pointer-events: none;
            transition: all 0.2s ease-in-out;
            margin-bottom: 0;
            z-index: 5;
        }

        .form-label-group input:focus ~ label,
        .form-label-group input:not(:placeholder-shown) ~ label,
        .form-label-group select:focus ~ label,
        .form-label-group select:valid ~ label,
        .form-label-group select.has-value ~ label,
        .form-label-group textarea:focus ~ label,
        .form-label-group textarea:not(:placeholder-shown) ~ label {
            top: 2px;
            left: 42px;
            font-size: 10px;
            font-weight: 600;
            color: {{ $t['primary'] ?? '#1B365D' }};
        }

        .req-star {
            color: #ef4444;
            font-weight: bold;
        }

        /* Modern Mobile File Upload (Consistent with Form Izin Sakit) */
        .mobile-upload-box {
            border: 1.5px dashed {{ $t['primary'] ?? '#1B365D' }};
            border-radius: 14px;
            background: #ffffff;
            padding: 18px 14px;
            text-align: center;
            cursor: pointer;
            margin-bottom: 12px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            min-height: 105px;
        }

        .mobile-upload-box:active {
            transform: scale(0.99);
            background: var(--color-primary-soft, rgba(27, 54, 93, 0.04));
        }

        .mobile-upload-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--color-primary-soft, rgba(27, 54, 93, 0.08));
            color: {{ $t['primary'] ?? '#1B365D' }};
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 4px;
        }

        .mobile-upload-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .mobile-upload-hint {
            font-size: 11px;
            color: #64748b;
        }

        /* Preview Card */
        .mobile-preview-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(15, 23, 42, 0.1);
            padding: 12px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .mobile-preview-thumb {
            width: 68px;
            height: 68px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: #f8fafc;
            flex-shrink: 0;
            display: block;
        }

        .mobile-preview-thumb-doc {
            width: 68px;
            height: 68px;
            border-radius: 10px;
            background: var(--color-primary-soft, rgba(27, 54, 93, 0.08));
            color: {{ $t['primary'] ?? '#1B365D' }};
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            flex-shrink: 0;
            border: 1px solid rgba(15, 23, 42, 0.06);
        }

        .mobile-preview-info {
            flex: 1;
            min-width: 0;
        }

        .mobile-preview-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mobile-preview-meta {
            font-size: 11px;
            color: #64748b;
            margin-top: 1px;
        }

        .mobile-preview-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 600;
            color: #059669;
            background: #d1fae5;
            padding: 2px 7px;
            border-radius: 6px;
            margin-top: 4px;
        }

        .mobile-preview-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
        }

        .btn-change-photo {
            font-size: 11px;
            font-weight: 600;
            color: {{ $t['primary'] ?? '#1B365D' }};
            background: var(--color-primary-soft, rgba(27, 54, 93, 0.08));
            border: none;
            border-radius: 8px;
            padding: 4px 10px;
            cursor: pointer;
        }

        .btn-delete-photo {
            font-size: 11px;
            font-weight: 600;
            color: #dc2626;
            background: #fee2e2;
            border: none;
            border-radius: 8px;
            padding: 4px 10px;
            cursor: pointer;
        }

        .btn-submit-modern {
            width: 100%;
            height: 48px;
            background: {{ $t['primary'] ?? '#1B365D' }};
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            transition: all 0.3s;
        }

        .btn-submit-modern:active {
            transform: scale(0.97);
            filter: brightness(0.9);
        }
    </style>
@endpush

@section('content')
    <div class="fade-up form-container">
        {{-- Flash Error Alerts --}}
        @if (isset($errors) && $errors->any())
            <div class="p-3 mb-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs space-y-1">
                <div class="flex items-center gap-1.5 font-bold">
                    <ion-icon name="alert-circle" class="text-base text-rose-600 shrink-0"></ion-icon>
                    <span>Gagal mengunggah berkas:</span>
                </div>
                <ul class="list-disc pl-5 space-y-0.5 m-0 text-[11.5px]">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('document.store') }}" method="POST" id="formUploadDoc" enctype="multipart/form-data" autocomplete="off">
            @csrf

            {{-- Jika Admin / HR: Pilih Karyawan --}}
            @if (!empty($isKaryawan) && !$isKaryawan && count($karyawans) > 0)
                <div class="form-label-group">
                    <ion-icon name="person-outline" class="input-icon"></ion-icon>
                    <select name="nik" id="nik" required>
                        <option value="" disabled selected hidden></option>
                        @foreach ($karyawans as $k)
                            <option value="{{ $k->nik }}" {{ old('nik') == $k->nik ? 'selected' : '' }}>
                                {{ $k->nama_karyawan }} ({{ $k->nik }})
                            </option>
                        @endforeach
                    </select>
                    <label for="nik">Pilih Karyawan <span class="req-star">*</span></label>
                    <div class="select-chevron">
                        <ion-icon name="chevron-down-outline"></ion-icon>
                    </div>
                </div>
            @endif

            {{-- 1. Jenis Dokumen --}}
            <div class="form-label-group">
                <ion-icon name="folder-open-outline" class="input-icon"></ion-icon>
                <select name="document_type" id="document_type" required>
                    <option value="" disabled {{ old('document_type') ? '' : 'selected' }} hidden></option>
                    @foreach ($types as $key => $val)
                        <option value="{{ $key }}" {{ old('document_type') == $key ? 'selected' : '' }}>
                            {{ $val }} ({{ $key }})
                        </option>
                    @endforeach
                </select>
                <label for="document_type">Jenis Dokumen <span class="req-star">*</span></label>
                <div class="select-chevron">
                    <ion-icon name="chevron-down-outline"></ion-icon>
                </div>
            </div>

            {{-- 2. Judul / Nama Dokumen --}}
            <div class="form-label-group">
                <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder=" ">
                <label for="title">Judul / Nama Dokumen <span class="req-star">*</span></label>
            </div>

            {{-- 3. File Input (Hidden Native + Form Izin Style Dropzone & Preview) --}}
            <input type="file" name="document_file" id="document_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: none;" required>

            <div class="mobile-upload-box" id="mobileUploadDropzone">
                <div class="mobile-upload-icon">
                    <ion-icon name="cloud-upload-outline"></ion-icon>
                </div>
                <div class="mobile-upload-title">Pilih Berkas Dokumen</div>
                <div class="mobile-upload-hint">Format PDF, JPG, PNG, DOC (Maks 10MB)</div>
            </div>

            <div class="mobile-preview-card" id="mobilePreviewCard" style="display: none;">
                <img src="" id="mobilePreviewThumb" class="mobile-preview-thumb" alt="Preview" style="display: none;">
                <div id="mobilePreviewDocIcon" class="mobile-preview-thumb-doc" style="display: none;">
                    <ion-icon name="document-text-outline"></ion-icon>
                </div>
                <div class="mobile-preview-info">
                    <div class="mobile-preview-name" id="mobilePreviewName">-</div>
                    <div class="mobile-preview-meta" id="mobilePreviewSize">-</div>
                    <span class="mobile-preview-badge" id="mobilePreviewBadge">
                        <ion-icon name="checkmark-circle-outline"></ion-icon> Berkas Terpilih
                    </span>
                    <div class="mobile-preview-actions">
                        <button type="button" class="btn-change-photo" id="btnChangeFile">Ganti</button>
                        <button type="button" class="btn-delete-photo" id="btnDeleteFile">Hapus</button>
                    </div>
                </div>
            </div>

            {{-- 4. Masa Berlaku (Opsional via AirDatepicker) --}}
            <div class="form-label-group">
                <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                <input type="text" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}" placeholder=" " readonly>
                <label for="expiry_date">Masa Berlaku (Opsional)</label>
            </div>

            {{-- 5. Catatan Berkas (Opsional) --}}
            <div class="form-label-group">
                <ion-icon name="create-outline" class="input-icon"></ion-icon>
                <textarea name="notes" id="notes" placeholder=" ">{{ old('notes') }}</textarea>
                <label for="notes">Catatan Berkas (Opsional)</label>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn-submit-modern" id="btnSubmitDoc">
                <ion-icon name="cloud-upload-outline" class="text-xl"></ion-icon>
                <span>Unggah Dokumen</span>
            </button>
        </form>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. AirDatepicker Locale & Init
            const localeIndo = {
                days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
                months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                today: 'Hari ini',
                clear: 'Hapus',
                dateFormat: 'yyyy-MM-dd',
                firstDay: 1
            };

            const btnToday = {
                content: 'Hari ini',
                className: 'air-datepicker-button-today',
                onClick: (dp) => {
                    const today = new Date();
                    dp.selectDate(today);
                    dp.setViewDate(today);
                }
            };

            new AirDatepicker('#expiry_date', {
                locale: localeIndo,
                autoClose: true,
                isMobile: true,
                buttons: [btnToday, 'clear']
            });

            // 1b. Select has-value sync
            document.querySelectorAll('.form-label-group select').forEach(sel => {
                const sync = () => {
                    if (sel.value) sel.classList.add('has-value');
                    else sel.classList.remove('has-value');
                };
                sel.addEventListener('change', sync);
                sync();
            });

            // 2. Upload Box & Preview Handlers
            const fileInput = document.getElementById('document_file');
            const dropzone = document.getElementById('mobileUploadDropzone');
            const previewCard = document.getElementById('mobilePreviewCard');
            const previewThumb = document.getElementById('mobilePreviewThumb');
            const previewDocIcon = document.getElementById('mobilePreviewDocIcon');
            const previewName = document.getElementById('mobilePreviewName');
            const previewSize = document.getElementById('mobilePreviewSize');
            const btnChange = document.getElementById('btnChangeFile');
            const btnDelete = document.getElementById('btnDeleteFile');

            function formatBytes(bytes) {
                if (!bytes) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }

            if (dropzone && fileInput) {
                dropzone.addEventListener('click', function() {
                    fileInput.click();
                });

                if (btnChange) {
                    btnChange.addEventListener('click', function(e) {
                        e.stopPropagation();
                        fileInput.click();
                    });
                }

                if (btnDelete) {
                    btnDelete.addEventListener('click', function(e) {
                        e.stopPropagation();
                        fileInput.value = '';
                        previewThumb.src = '';
                        previewThumb.style.display = 'none';
                        previewDocIcon.style.display = 'none';
                        previewCard.style.display = 'none';
                        dropzone.style.display = 'flex';
                    });
                }

                fileInput.addEventListener('change', async function() {
                    let file = this.files[0];
                    if (!file) return;

                    // Auto-compress photo on client if image
                    if (file.type && file.type.startsWith('image/') && typeof window.compressImageFile === 'function') {
                        try {
                            const compressed = await window.compressImageFile(file, { maxDimension: 1600, quality: 0.85 });
                            if (compressed && compressed.size < file.size) {
                                file = compressed;
                                const dt = new DataTransfer();
                                dt.items.add(file);
                                fileInput.files = dt.files;
                            }
                        } catch(err) {
                            console.warn('Client compression fallback', err);
                        }
                    }

                    // Max 10MB validation
                    if (file.size > 10 * 1024 * 1024) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: "Ukuran Terlalu Besar",
                                text: "Ukuran berkas maksimal adalah 10MB! (" + formatBytes(file.size) + ")",
                                icon: "warning"
                            });
                        } else {
                            alert("Ukuran berkas maksimal 10MB!");
                        }
                        this.value = '';
                        return;
                    }

                    previewName.textContent = file.name;
                    previewSize.textContent = formatBytes(file.size);

                    if (file.type && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewThumb.src = e.target.result;
                            previewThumb.style.display = 'block';
                            previewDocIcon.style.display = 'none';
                            dropzone.style.display = 'none';
                            previewCard.style.display = 'flex';
                        };
                        reader.readAsDataURL(file);
                    } else {
                        previewThumb.style.display = 'none';
                        previewDocIcon.style.display = 'flex';
                        dropzone.style.display = 'none';
                        previewCard.style.display = 'flex';
                    }
                });
            }

            // 3. Form Submit Validation
            const form = document.getElementById('formUploadDoc');
            form.addEventListener('submit', function(e) {
                const docType = document.getElementById('document_type').value;
                const title = document.getElementById('title').value;
                const files = fileInput ? fileInput.files : null;

                if (!docType) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ title: 'Peringatan', text: 'Silakan pilih Jenis Dokumen!', icon: 'warning' });
                    }
                    return false;
                }

                if (!title.trim()) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ title: 'Peringatan', text: 'Judul dokumen wajib diisi!', icon: 'warning' });
                    }
                    return false;
                }

                if (!files || files.length === 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ title: 'Peringatan', text: 'Silakan pilih berkas dokumen yang akan diunggah!', icon: 'warning' });
                    }
                    return false;
                }

                const btn = document.getElementById('btnSubmitDoc');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengunggah...';
                }
            });
        });
    </script>
@endpush
