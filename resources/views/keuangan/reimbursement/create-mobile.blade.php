@extends('layouts.mobile.modern')

@section('title', 'Ajukan Reimbursement')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('reimbursement.index') }}"
        onclick="if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) { event.preventDefault(); window.history.back(); }"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all"
        title="Kembali">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.css" rel="stylesheet">
    <style>
        body {
            background: var(--bg-body, {{ $t['bg_body'] ?? '#F8FAFC' }}) !important;
        }

        .form-container {
            padding: 10px 5px calc(110px + env(safe-area-inset-bottom, 0px)) !important;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 12px;
            background: #ffffff !important;
            border: 1px solid var(--color-primary, {{ $t['primary'] ?? '#1B365D' }});
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 14px;
            top: 11px;
            font-size: 20px;
            color: var(--color-primary, {{ $t['primary'] ?? '#1B365D' }});
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
            color: var(--color-primary, {{ $t['primary'] ?? '#1B365D' }});
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            display: block !important;
            appearance: none;
            -webkit-appearance: none;
        }

        .form-label-group select {
            padding-right: 38px !important;
            cursor: pointer;
        }

        .select-chevron {
            position: absolute;
            right: 14px;
            top: 13px;
            font-size: 16px;
            color: var(--color-primary, {{ $t['primary'] ?? '#1B365D' }});
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
            color: var(--color-primary, {{ $t['primary'] ?? '#1B365D' }});
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
            color: var(--color-primary, {{ $t['primary'] ?? '#1B365D' }});
        }

        .req-star {
            color: #ef4444;
            font-weight: bold;
        }

        .btn-submit-modern {
            width: 100%;
            height: 48px;
            background: var(--color-primary, {{ $t['primary'] ?? '#1B365D' }});
            color: var(--color-primary-contrast, #ffffff);
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
            transition: all 0.3s;
        }

        .btn-submit-modern:active {
            transform: scale(0.97);
            filter: brightness(0.9);
        }

        /* Modern Mobile Upload Box & Preview */
        .mobile-upload-box {
            border: 1.5px dashed var(--color-primary, {{ $t['primary'] ?? '#1B365D' }});
            border-radius: 14px;
            background: #ffffff;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .mobile-upload-box:active {
            transform: scale(0.99);
            background: var(--color-primary-soft, rgba(27, 54, 93, 0.04));
        }

        .mobile-upload-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--color-primary-soft, rgba(27, 54, 93, 0.08));
            color: var(--color-primary, {{ $t['primary'] ?? '#1B365D' }});
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 2px;
        }

        .mobile-upload-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .mobile-upload-hint {
            font-size: 11px;
            color: #64748b;
        }

        .mobile-preview-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(15, 23, 42, 0.1);
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .mobile-preview-thumb {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: #f8fafc;
            flex-shrink: 0;
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="fade-up form-container">
        {{-- Flash Alerts --}}
        @if (isset($errors) && $errors->any())
            <div class="mb-3 p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-[12px] space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <ion-icon name="alert-circle" class="text-base text-rose-600"></ion-icon>
                    <span>Terjadi kesalahan:</span>
                </div>
                @foreach ($errors->all() as $err)
                    <div>• {{ $err }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('reimbursement.store') }}" method="POST" enctype="multipart/form-data" id="formKlaim" autocomplete="off">
            @csrf

            {{-- Jenis Reimbursement --}}
            <div class="form-label-group">
                <ion-icon name="receipt-outline" class="input-icon"></ion-icon>
                <select name="reimbursement_type_id" id="reimbursement_type_id" required>
                    <option value="" disabled {{ old('reimbursement_type_id') ? '' : 'selected' }} hidden></option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}" {{ old('reimbursement_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} (Maks. Rp {{ number_format($type->max_amount, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                <ion-icon name="chevron-down-outline" class="select-chevron"></ion-icon>
                <label for="reimbursement_type_id">Jenis Reimbursement <span class="req-star">*</span></label>
            </div>

            {{-- Tanggal Transaksi --}}
            <div class="form-label-group">
                <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                <input type="text" name="claim_date" id="claim_date" value="{{ old('claim_date', date('Y-m-d')) }}" placeholder=" " required readonly>
                <label for="claim_date">Tanggal Transaksi / Nota <span class="req-star">*</span></label>
            </div>

            {{-- Nominal Pengeluaran --}}
            <div class="form-label-group">
                <ion-icon name="cash-outline" class="input-icon"></ion-icon>
                <input type="number" name="amount" id="amount" value="{{ old('amount') }}" placeholder=" " required min="1000" step="1000">
                <label for="amount">Jumlah Pengeluaran (Rp) <span class="req-star">*</span></label>
            </div>

            {{-- Upload Struk / Nota (Modern Mobile Upload Box with Preview & Auto-Compress) --}}
            <div class="mb-3">
                <input type="file" name="receipt" id="receipt" accept="image/*,.pdf" style="display: none;">
                <div class="mobile-upload-box" id="mobileUploadDropzone">
                    <div class="mobile-upload-icon">
                        <ion-icon name="cloud-upload-outline"></ion-icon>
                    </div>
                    <div class="mobile-upload-title">Unggah Bukti Struk / Kuitansi</div>
                    <div class="mobile-upload-hint">Format JPG, PNG, WEBP, PDF (Maks. 2MB)</div>
                </div>

                {{-- Preview Card --}}
                <div class="mobile-preview-card" id="mobilePreviewCard" style="display: none;">
                    <img id="mobilePreviewThumb" src="" alt="Thumbnail" class="mobile-preview-thumb">
                    <div class="flex-1 min-w-0">
                        <div class="text-[13px] font-bold text-slate-800 truncate" id="mobilePreviewName">nama_file.jpg</div>
                        <div class="text-[11px] text-slate-500 font-mono mt-0.5" id="mobilePreviewSize">0 KB</div>
                        <div class="flex items-center gap-2 mt-2">
                            <button type="button" id="btnChangePhoto" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-700 active:scale-95 transition-all">Ganti</button>
                            <button type="button" id="btnDeletePhoto" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-rose-50 text-rose-600 active:scale-95 transition-all">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Keterangan / Rincian --}}
            <div class="form-label-group">
                <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                <textarea name="description" id="description" placeholder=" " required>{{ old('description') }}</textarea>
                <label for="description">Rincian / Keperluan Klaim <span class="req-star">*</span></label>
            </div>

            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="paper-plane-outline"></ion-icon>
                <span>Ajukan Klaim Reimbursement</span>
            </button>
        </form>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. AirDatepicker Locale & Setup
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

            new AirDatepicker('#claim_date', {
                locale: localeIndo,
                autoClose: true,
                isMobile: true,
                buttons: [btnToday, 'clear']
            });

            // 2. Select has-value sync
            const selectEl = document.getElementById('reimbursement_type_id');
            if (selectEl) {
                const sync = () => {
                    if (selectEl.value) {
                        selectEl.classList.add('has-value');
                    } else {
                        selectEl.classList.remove('has-value');
                    }
                };
                selectEl.addEventListener('change', sync);
                sync();
            }

            // 3. File upload handling
            const fileInput = document.getElementById('receipt');
            const dropzone = document.getElementById('mobileUploadDropzone');
            const previewCard = document.getElementById('mobilePreviewCard');
            const previewThumb = document.getElementById('mobilePreviewThumb');
            const previewName = document.getElementById('mobilePreviewName');
            const previewSize = document.getElementById('mobilePreviewSize');
            const btnChange = document.getElementById('btnChangePhoto');
            const btnDelete = document.getElementById('btnDeletePhoto');

            function formatBytes(bytes) {
                if (!bytes) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }

            if (dropzone && fileInput) {
                dropzone.addEventListener('click', () => fileInput.click());
                if (btnChange) btnChange.addEventListener('click', (e) => { e.stopPropagation(); fileInput.click(); });
                if (btnDelete) btnDelete.addEventListener('click', (e) => {
                    e.stopPropagation();
                    fileInput.value = '';
                    previewThumb.src = '';
                    previewCard.style.display = 'none';
                    dropzone.style.display = 'flex';
                });

                fileInput.addEventListener('change', async function() {
                    let file = this.files[0];
                    if (!file) return;

                    // Auto-compress on mobile client if image
                    if (file.type && file.type.startsWith('image/') && typeof window.compressImageFile === 'function') {
                        try {
                            const compressed = await window.compressImageFile(file, { maxDimension: 1280, quality: 0.82 });
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

                    if (file.size > 2 * 1024 * 1024) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: "Ukuran Terlalu Besar",
                                text: "Ukuran berkas maksimal adalah 2MB! (" + formatBytes(file.size) + ")",
                                icon: "warning"
                            });
                        } else {
                            alert("Ukuran berkas maksimal 2MB!");
                        }
                        this.value = '';
                        return;
                    }

                    previewName.textContent = file.name;
                    previewSize.textContent = formatBytes(file.size);

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewThumb.src = e.target.result;
                            dropzone.style.display = 'none';
                            previewCard.style.display = 'flex';
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // PDF placeholder
                        previewThumb.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ef4444'%3E%3Cpath d='M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9.5 8.5h-2v1h2c.28 0 .5-.22.5-.5s-.22-.5-.5-.5zm5 0h-2v3h2c.28 0 .5-.22.5-.5v-2c0-.28-.22-.5-.5-.5zm-8.5-4c0-.55.45-1 1-1h3c1.1 0 2 .9 2 2v1c0 1.1-.9 2-2 2h-2v2H6V7.5zm7 0c0-.55.45-1 1-1h3c1.1 0 2 .9 2 2v3c0 1.1-.9 2-2 2h-3c-.55 0-1-.45-1-1v-5z'/%3E%3C/svg%3E";
                        dropzone.style.display = 'none';
                        previewCard.style.display = 'flex';
                    }
                });
            }
        });
    </script>
@endpush
