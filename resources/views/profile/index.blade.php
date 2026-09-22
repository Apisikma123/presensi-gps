@extends('layouts.mobile.modern')

@section('title', 'Profile')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('dashboard.index') }}"
        onclick="if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) { event.preventDefault(); window.history.back(); }"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all"
        title="Kembali">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <link rel="stylesheet" href="{{ asset('assets/external/css/cropper.min.css') }}">
    <style>
        /* ===== CROPPER MODAL DEDICATED STYLES (ANTISLOP-UI & DESIGN.MD) ===== */
        #cropperModal {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999999 !important;
            display: none;
            align-items: flex-end;
            justify-content: center;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }
        @media (min-width: 640px) {
            #cropperModal {
                align-items: center !important;
                padding: 16px !important;
            }
        }
        #cropperModal.active {
            display: flex !important;
        }

        .cropper-custom-backdrop {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: rgba(15, 23, 42, 0.85) !important;
            -webkit-backdrop-filter: blur(6px) !important;
            backdrop-filter: blur(6px) !important;
            z-index: 1 !important;
        }

        .cropper-modal-dialog {
            position: relative !important;
            z-index: 2 !important;
            width: 100% !important;
            max-width: 420px !important;
            background: #ffffff !important;
            border-top-left-radius: 24px !important;
            border-top-right-radius: 24px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.35) !important;
            display: flex !important;
            flex-direction: column !important;
            max-height: 90vh !important;
            overflow: hidden !important;
            animation: cropperSlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @media (min-width: 640px) {
            .cropper-modal-dialog {
                border-radius: 24px !important;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4) !important;
                animation: cropperScaleIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
        }
        @keyframes cropperSlideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes cropperScaleIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .cropper-modal-header {
            padding: 14px 16px !important;
            background: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        .cropper-header-title-box {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }
        .cropper-header-icon {
            width: 32px !important;
            height: 32px !important;
            border-radius: 10px !important;
            background: rgba(30, 77, 62, 0.08) !important;
            color: #1E4D3E !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 18px !important;
            flex-shrink: 0 !important;
        }
        .cropper-title {
            font-family: 'Outfit', sans-serif !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.2 !important;
            margin: 0 !important;
        }
        .cropper-subtitle {
            font-family: 'Inter', sans-serif !important;
            font-size: 11px !important;
            color: #64748b !important;
            display: block !important;
            margin-top: 2px !important;
            line-height: 1.2 !important;
        }
        .cropper-close-btn {
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
            background: #f1f5f9 !important;
            color: #64748b !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 20px !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            -webkit-tap-highlight-color: transparent !important;
        }
        .cropper-close-btn:active {
            transform: scale(0.92) !important;
            background: #e2e8f0 !important;
        }

        .cropper-canvas-box {
            position: relative !important;
            width: 100% !important;
            height: 250px !important;
            background: #0f172a !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
        }
        @media (min-height: 700px) {
            .cropper-canvas-box {
                height: 275px !important;
            }
        }
        .cropper-bg {
            background: #0f172a !important;
        }

        .cropper-avatar-mask .cropper-view-box,
        .cropper-avatar-mask .cropper-face {
            border-radius: 50% !important;
        }
        .cropper-avatar-mask .cropper-view-box {
            outline: 2px solid rgba(255, 255, 255, 0.95) !important;
            outline-offset: -1px !important;
            box-shadow: 0 0 0 1000px rgba(15, 23, 42, 0.8) !important;
        }
        .cropper-avatar-mask .cropper-line,
        .cropper-avatar-mask .cropper-point {
            background-color: #1E4D3E !important;
        }
        .cropper-avatar-mask .cropper-modal {
            background-color: transparent !important;
        }

        .cropper-toolbar-row {
            padding: 8px 16px !important;
            background: #f8fafc !important;
            border-top: 1px solid #e2e8f0 !important;
            border-bottom: 1px solid #e2e8f0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        .cropper-toolbar-label {
            font-family: 'Inter', sans-serif !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
        }
        .cropper-toolbar-actions {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }
        .cropper-tool-btn {
            width: 34px !important;
            height: 34px !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 16px !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            -webkit-tap-highlight-color: transparent !important;
        }
        .cropper-tool-btn:active {
            transform: scale(0.92) !important;
            background: #f1f5f9 !important;
            border-color: #94a3b8 !important;
        }

        .cropper-modal-footer {
            padding: 12px 16px !important;
            padding-bottom: max(16px, env(safe-area-inset-bottom, 16px)) !important;
            background: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            box-sizing: border-box !important;
        }
        .cropper-btn-cancel {
            flex: 1 !important;
            height: 44px !important;
            min-height: 44px !important;
            max-height: 44px !important;
            background: #f8fafc !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 12px !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            white-space: nowrap !important;
            -webkit-tap-highlight-color: transparent !important;
        }
        .cropper-btn-cancel:active {
            transform: translateY(1px) scale(0.98) !important;
            background: #f1f5f9 !important;
        }
        .cropper-btn-apply {
            flex: 1.3 !important;
            height: 44px !important;
            min-height: 44px !important;
            max-height: 44px !important;
            background: #1E4D3E !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 12px !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            cursor: pointer !important;
            box-shadow: 0 2px 8px rgba(30, 77, 62, 0.25) !important;
            transition: all 0.15s ease !important;
            white-space: nowrap !important;
            -webkit-tap-highlight-color: transparent !important;
        }
        .cropper-btn-apply ion-icon {
            font-size: 18px !important;
            display: inline-block !important;
            margin: 0 !important;
            color: #ffffff !important;
        }
        .cropper-btn-apply span {
            display: inline-block !important;
            line-height: 1 !important;
            color: #ffffff !important;
        }
        .cropper-btn-apply:active {
            transform: translateY(1px) scale(0.98) !important;
            background: #16382E !important;
        }

        .form-container {
            padding: 12px 6px calc(110px + env(safe-area-inset-bottom, 0px)) !important;
        }

        .profile-card-surface {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            padding: 20px 16px;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 14px;
            background: #F8FAF8 !important;
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-label-group:focus-within {
            border-color: #1E4D3E !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(30, 77, 62, 0.08) !important;
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 14px;
            top: 13px;
            font-size: 18px;
            color: #94a3b8;
            z-index: 10;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-label-group:focus-within .input-icon {
            color: #1E4D3E;
        }

        .form-label-group input,
        .form-label-group textarea {
            width: 100% !important;
            height: 48px;
            padding: 18px 14px 2px 42px !important;
            font-size: 13px;
            font-weight: 500;
            color: #0f172a;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            display: block !important;
        }

        .form-label-group textarea {
            height: 84px !important;
            padding-top: 22px !important;
            resize: none;
        }

        .form-label-group label {
            position: absolute;
            top: 13px;
            left: 42px;
            font-size: 13px;
            color: #64748b;
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            margin-bottom: 0;
            z-index: 5;
        }

        .form-label-group input:focus ~ label,
        .form-label-group input:not(:placeholder-shown) ~ label,
        .form-label-group textarea:focus ~ label,
        .form-label-group textarea:not(:placeholder-shown) ~ label {
            top: 4px;
            left: 42px;
            font-size: 10px;
            font-weight: 700;
            color: #1E4D3E;
        }

        /* Foto Profil */
        .profile-photo-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 22px;
            position: relative;
        }

        .profile-photo-box {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            padding: 3px;
            background: #ffffff;
            border: 2px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
            cursor: pointer;
        }

        .profile-photo-box img,
        .profile-photo-box .photo-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-photo-box .photo-placeholder {
            background-size: cover;
            background-position: center;
        }

        .profile-photo-edit-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #1E4D3E;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        }

        .btn-submit-modern {
            width: 100%;
            height: 50px;
            background: #1E4D3E !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
            box-shadow: 0 2px 6px rgba(30, 77, 62, 0.15);
            transition: all 0.15s ease;
        }

        .btn-submit-modern:active {
            transform: scale(0.98);
            background: #16382E !important;
        }
    </style>
@endpush

@section('content')
    <div class="fade-up form-container pb-24">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="formProfile" autocomplete="off">
            @csrf
            @method('PUT')

            {{-- Profile Photo & Header --}}
            <div class="profile-photo-wrapper">
                <div class="profile-photo-box" onclick="document.getElementById('foto').click()">
                    @if (!empty($karyawan->foto))
                        <div class="photo-placeholder" style="background-image: url({{ getfotoKaryawan($karyawan->foto) }});"></div>
                    @else
                        <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt="Profile Photo">
                    @endif
                    <div class="profile-photo-edit-badge">
                        <ion-icon name="camera" style="font-size:14px;"></ion-icon>
                    </div>
                </div>
                <h4 class="text-[14px] font-bold text-slate-800 mt-2 mb-0.5 text-center">
                    {{ $karyawan->nama_karyawan ?? $user->name }}
                </h4>
                <span class="text-[11px] font-mono font-medium px-2.5 py-0.5 rounded-full bg-white text-slate-600 border border-slate-200/80 shadow-xs">
                    NIK: {{ $karyawan->nik ?? '-' }}
                </span>
                <button type="button" onclick="document.getElementById('foto').click()" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 hover:bg-slate-200/80 active:scale-95 text-slate-700 text-[11px] font-medium transition-all cursor-pointer border border-slate-200/70 shadow-2xs">
                    <ion-icon name="camera-outline" class="text-xs text-slate-500"></ion-icon>
                    <span>Ganti Foto <span class="opt-tag font-normal">(Opsional, Maks. 2MB)</span></span>
                </button>
                <span class="text-[10px] text-slate-400 mt-1">Format: WebP, PNG, JPG • Auto Kompres</span>
            </div>

            {{-- Hidden Input Foto --}}
            <input type="file" name="foto" id="foto" accept=".jpg, .jpeg, .png, .webp" style="display: none;">

            {{-- Nama Lengkap --}}
            <div class="form-label-group">
                <ion-icon name="person-outline" class="input-icon"></ion-icon>
                <input type="text" name="nama_karyawan" id="nama_karyawan" placeholder=" " value="{{ $karyawan->nama_karyawan ?? '' }}" required>
                <label for="nama_karyawan">Nama Lengkap <span class="req-star">*</span></label>
            </div>

            {{-- No. HP --}}
            <div class="form-label-group">
                <ion-icon name="call-outline" class="input-icon"></ion-icon>
                <input type="text" name="no_hp" id="no_hp" placeholder=" " value="{{ $karyawan->no_hp ?? '' }}" required>
                <label for="no_hp">No. HP <span class="req-star">*</span></label>
            </div>

            {{-- Alamat --}}
            <div class="form-label-group">
                <ion-icon name="location-outline" class="input-icon"></ion-icon>
                <textarea name="alamat" id="alamat" placeholder=" " required>{{ $karyawan->alamat ?? '' }}</textarea>
                <label for="alamat">Alamat Domisili <span class="req-star">*</span></label>
            </div>

            {{-- Username --}}
            <div class="form-label-group">
                <ion-icon name="at-outline" class="input-icon"></ion-icon>
                <input type="text" name="username" id="username" placeholder=" " value="{{ $user->username }}" required>
                <label for="username">Username <span class="req-star">*</span></label>
            </div>

            {{-- Email --}}
            <div class="form-label-group">
                <ion-icon name="mail-outline" class="input-icon"></ion-icon>
                <input type="email" name="email" id="email" placeholder=" " value="{{ $user->email }}" required>
                <label for="email">Email <span class="req-star">*</span></label>
            </div>



            {{-- Submit Button --}}
            <button type="submit" class="btn btn-submit-modern" id="btnSimpan" style="margin-bottom: 12px;">
                <ion-icon name="save-outline" style="font-size:18px;"></ion-icon>
                <span>Update Profile</span>
            </button>

            {{-- Safe Area Bottom Spacer --}}
            <div style="height: calc(85px + env(safe-area-inset-bottom, 0px)); width: 100%;"></div>
        </form>

        {{-- ===== CROPPER MODAL (ANTISLOP-UI & DESIGN.MD) ===== --}}
        <div id="cropperModal">
            {{-- Backdrop --}}
            <div id="cropperBackdrop" class="cropper-custom-backdrop"></div>

            {{-- Modal Sheet / Card --}}
            <div class="cropper-modal-dialog" id="cropperDialog">
                {{-- Header --}}
                <div class="cropper-modal-header">
                    <div class="cropper-header-title-box">
                        <div class="cropper-header-icon">
                            <ion-icon name="crop-outline"></ion-icon>
                        </div>
                        <div>
                            <h3 class="cropper-title">Sesuaikan Foto Profil</h3>
                            <span class="cropper-subtitle">Geser, putar, atau cubit untuk memperbesar</span>
                        </div>
                    </div>
                    <button type="button" id="btnCropperCancelTop" class="cropper-close-btn" aria-label="Tutup">
                        <ion-icon name="close-outline"></ion-icon>
                    </button>
                </div>

                {{-- Cropper Viewport Container --}}
                <div class="cropper-canvas-box">
                    <img id="cropperImage" src="" alt="Source" style="display: block; max-width: 100%;">
                </div>

                {{-- Toolbar Controls --}}
                <div class="cropper-toolbar-row">
                    <span class="cropper-toolbar-label">Atur Posisi</span>
                    <div class="cropper-toolbar-actions">
                        <button type="button" id="btnCropRotateLeft" class="cropper-tool-btn" title="Putar Kiri (-90°)">
                            <ion-icon name="arrow-undo-outline"></ion-icon>
                        </button>
                        <button type="button" id="btnCropRotateRight" class="cropper-tool-btn" title="Putar Kanan (+90°)">
                            <ion-icon name="arrow-redo-outline"></ion-icon>
                        </button>
                        <button type="button" id="btnCropZoomIn" class="cropper-tool-btn" title="Perbesar (+)">
                            <ion-icon name="add-outline"></ion-icon>
                        </button>
                        <button type="button" id="btnCropZoomOut" class="cropper-tool-btn" title="Perkecil (-)">
                            <ion-icon name="remove-outline"></ion-icon>
                        </button>
                        <button type="button" id="btnCropReset" class="cropper-tool-btn" title="Reset Ukuran">
                            <ion-icon name="refresh-outline"></ion-icon>
                        </button>
                    </div>
                </div>

                {{-- Footer Action Buttons --}}
                <div class="cropper-modal-footer">
                    <button type="button" id="btnCropperCancel" class="cropper-btn-cancel">
                        Batal
                    </button>
                    <button type="button" id="btnCropperApply" class="cropper-btn-apply">
                        <ion-icon name="checkmark-outline"></ion-icon>
                        <span>Gunakan Foto</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="{{ asset('assets/external/js/cropper.min.js') }}"></script>
    <script>
        let cropperInstance = null;
        let originalFileName = 'profile.jpg';
        const fotoInput = document.getElementById('foto');
        const cropperModal = document.getElementById('cropperModal');
        const cropperImage = document.getElementById('cropperImage');
        const maxCropSizeMb = 2;

        function openCropperModal(imageSrc) {
            // Escape any parent stacking context by appending directly to document.body
            if (cropperModal.parentElement !== document.body) {
                document.body.appendChild(cropperModal);
            }

            cropperImage.src = imageSrc;
            cropperModal.classList.add('active');

            if (cropperInstance) {
                cropperInstance.destroy();
                cropperInstance = null;
            }

            cropperInstance = new Cropper(cropperImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.9,
                responsive: true,
                restore: false,
                guides: false,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                ready: function() {
                    const container = cropperImage.closest('.cropper-container') || document.querySelector('.cropper-container');
                    if (container) {
                        container.classList.add('cropper-avatar-mask');
                    }
                }
            });
        }

        function closeCropperModal(resetInput = false) {
            cropperModal.classList.remove('active');
            if (cropperInstance) {
                cropperInstance.destroy();
                cropperInstance = null;
            }
            if (resetInput && !fotoInput.hasAttribute('data-has-cropped')) {
                fotoInput.value = '';
            }
        }

        // Event listener for foto input change
        fotoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    title: "Format Tidak Sesuai",
                    text: "Silakan pilih file gambar (JPG, PNG, atau WEBP)!",
                    icon: "warning"
                });
                this.value = '';
                return;
            }

            if (file.size > maxCropSizeMb * 1024 * 1024) {
                Swal.fire({
                    title: "Ukuran Terlalu Besar",
                    text: "Ukuran foto maksimal adalah " + maxCropSizeMb + "MB! (" + (file.size / (1024 * 1024)).toFixed(2) + " MB)",
                    icon: "warning"
                });
                this.value = '';
                return;
            }

            originalFileName = file.name || 'profile.jpg';
            const reader = new FileReader();
            reader.onload = function(e) {
                openCropperModal(e.target.result);
            };
            reader.readAsDataURL(file);
        });

        // Cropper toolbar actions
        document.getElementById('btnCropRotateLeft').addEventListener('click', function() {
            if (cropperInstance) cropperInstance.rotate(-90);
        });
        document.getElementById('btnCropRotateRight').addEventListener('click', function() {
            if (cropperInstance) cropperInstance.rotate(90);
        });
        document.getElementById('btnCropZoomIn').addEventListener('click', function() {
            if (cropperInstance) cropperInstance.zoom(0.1);
        });
        document.getElementById('btnCropZoomOut').addEventListener('click', function() {
            if (cropperInstance) cropperInstance.zoom(-0.1);
        });
        document.getElementById('btnCropReset').addEventListener('click', function() {
            if (cropperInstance) cropperInstance.reset();
        });

        // Cancel actions
        document.getElementById('btnCropperCancel').addEventListener('click', function() {
            closeCropperModal(true);
        });
        document.getElementById('btnCropperCancelTop').addEventListener('click', function() {
            closeCropperModal(true);
        });
        document.getElementById('cropperBackdrop').addEventListener('click', function() {
            closeCropperModal(true);
        });

        // Apply Cropped Image
        document.getElementById('btnCropperApply').addEventListener('click', function() {
            if (!cropperInstance) return;

            // Fill transparent backgrounds with white to prevent black background artifacts on transparent PNGs
            const canvas = cropperInstance.getCroppedCanvas({
                width: 800,
                height: 800,
                fillColor: '#ffffff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            if (!canvas) {
                closeCropperModal(true);
                return;
            }

            canvas.toBlob(function(blob) {
                if (!blob) return;

                const safeName = originalFileName.replace(/\.[^/.]+$/, "") + "_cropped.jpg";
                const croppedFile = new File([blob], safeName, { type: 'image/jpeg', lastModified: Date.now() });

                try {
                    const dt = new DataTransfer();
                    dt.items.add(croppedFile);
                    fotoInput.files = dt.files;
                    fotoInput.setAttribute('data-has-cropped', 'true');
                } catch (err) {
                    console.error('DataTransfer error:', err);
                }

                // Update Avatar Preview
                const box = document.querySelector('.profile-photo-box');
                const placeholder = box ? box.querySelector('.photo-placeholder') : null;
                const img = box ? box.querySelector('img') : null;
                const previewUrl = URL.createObjectURL(blob);

                if (placeholder) {
                    placeholder.style.backgroundImage = 'url(' + previewUrl + ')';
                } else if (img) {
                    img.src = previewUrl;
                }

                closeCropperModal(false);

                if (typeof toastr !== 'undefined') {
                    toastr.success('Foto berhasil dipotong & disesuaikan!');
                }
            }, 'image/jpeg', 0.9);
        });

        $(function() {
            $("#formProfile").submit(function(e) {
                let nama_karyawan = $('input[name="nama_karyawan"]').val();
                let no_hp = $('input[name="no_hp"]').val();
                let alamat = $('textarea[name="alamat"]').val();
                let username = $('input[name="username"]').val();
                let email = $('input[name="email"]').val();

                if (nama_karyawan == "" || no_hp == "" || alamat == "" || username == "" || email == "") {
                    e.preventDefault();
                    Swal.fire({title: "Oops!", text: 'Semua Bidang Harus Diisi !', icon: "warning"});
                    return false;
                }

                const btn = document.getElementById('btnSimpan');
                btn.disabled = true;
                btn.innerHTML = `<ion-icon name="sync-outline" class="animate-spin"></ion-icon><span>Menyimpan...</span>`;
            });
        });
    </script>
@endpush
