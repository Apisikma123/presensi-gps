@props([
    'name' => '',
    'label' => '',
    'value' => null,
    'accept' => 'image/jpeg,image/png,image/webp,image/jpg',
    'required' => false,
    'helper' => 'Format: JPG, PNG, WEBP (Maks. 2MB)',
    'maxSizeMb' => 2,
    'class' => '',
    'crop' => null,
    'cropRatio' => null,
    'cropShape' => null
])

@php
    $inputId = 'file_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name) . '_' . uniqid();
    $displayLabel = $label;
    if (strtolower($label) === 'sid') {
        $displayLabel = 'Surat Keterangan Dokter (SID)';
    }

    $existingUrl = null;
    $existingName = null;
    if (!empty($value)) {
        $existingName = basename($value);
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, 'data:')) {
            $existingUrl = $value;
        } elseif ($name === 'sid') {
            $existingUrl = route('file.sid', ['filename' => $value]);
        } elseif ($name === 'foto') {
            $existingUrl = asset('storage/karyawan/' . $value);
        } elseif ($name === 'logo') {
            $existingUrl = asset('storage/logo/' . $value);
        } else {
            $existingUrl = asset('storage/' . $value);
        }
    }

    $lowerName = strtolower($name);

    // Auto-detect whether cropping should be enabled
    if ($crop === null) {
        $shouldCrop = in_array($lowerName, ['foto', 'avatar', 'photo', 'profile_photo', 'logo', 'pwa_icon', 'icon', 'sid', 'surat_dokter']);
    } else {
        $shouldCrop = (bool) $crop;
    }

    // Auto-detect optimal aspect ratio (1:1 square for profile/avatar/icon, free for logo/document)
    $finalCropRatio = $cropRatio;
    if ($finalCropRatio === null) {
        if (in_array($lowerName, ['foto', 'avatar', 'photo', 'profile_photo', 'pwa_icon', 'icon'])) {
            $finalCropRatio = 1;
        } else {
            $finalCropRatio = 'free';
        }
    }

    // Auto-detect optimal shape ('round' for profile photo, 'square' for PWA icon, 'rect' for logo/doc)
    $finalCropShape = $cropShape;
    if ($finalCropShape === null) {
        if (in_array($lowerName, ['foto', 'avatar', 'photo', 'profile_photo'])) {
            $finalCropShape = 'round';
        } else {
            $finalCropShape = 'rect';
        }
    }
@endphp

<div class="form-group mb-3 global-input-file-wrapper {{ $class }}" id="wrapper_{{ $inputId }}" data-name="{{ $name }}">
    @if ($displayLabel)
        <label for="{{ $inputId }}" class="form-label fw-semibold d-flex align-items-center justify-content-between mb-1.5" style="font-size: 13px; color: #0F172A;">
            <span>
                {{ $displayLabel }}
                @if ($required)
                    <span class="text-danger fw-bold ms-0.5">*</span>
                @else
                    <span class="text-muted fw-normal font-monospace" style="font-size: 10.5px;">(Opsional)</span>
                @endif
            </span>
            @if ($helper)
                <span class="badge" style="font-size: 11px; font-weight: 600; background: rgba(30, 77, 62, 0.08); color: #1E4D3E; border: 1px solid rgba(30, 77, 62, 0.15); border-radius: 6px; padding: 3px 8px;">{{ $helper }}</span>
            @endif
        </label>
    @endif

    {{-- Real Hidden File Input --}}
    <input type="file"
           id="{{ $inputId }}"
           name="{{ $name }}"
           accept="{{ $accept }}"
           class="d-none real-file-input"
           @if($required && empty($existingUrl)) required @endif>

    {{-- Hidden flag to remove existing file if user clicks Hapus --}}
    <input type="hidden" name="hapus_{{ $name }}" id="hapus_{{ $inputId }}" value="0" class="remove-flag">

    {{-- State 1: Dropzone (Shown when no file selected and no existing value) --}}
    <div class="custom-dropzone-box"
         id="dropzone_{{ $inputId }}"
         style="{{ !empty($existingUrl) ? 'display: none !important;' : '' }}">
        <div class="dropzone-icon-pill">
            <i class="ti ti-cloud-upload"></i>
        </div>
        <div class="dropzone-content">
            <span class="dropzone-title">Klik atau seret foto ke sini</span>
            <span class="dropzone-subtitle">JPG, PNG, atau WEBP hingga {{ $maxSizeMb ?? 2 }}MB</span>
        </div>
        <button type="button" class="dropzone-browse-btn">
            <i class="ti ti-folder-open"></i>
            <span>Pilih File</span>
        </button>
    </div>

    {{-- State 2: Preview Card (Shown when file is selected or existing value exists) --}}
    <div class="custom-preview-card"
         id="preview_card_{{ $inputId }}"
         style="{{ empty($existingUrl) ? 'display: none !important;' : '' }}">
        <div class="preview-thumbnail-container">
            <img src="{{ $existingUrl ?? '' }}"
                 alt="Preview"
                 id="preview_img_{{ $inputId }}"
                 class="preview-thumbnail-img"
                 style="{{ empty($existingUrl) ? 'display: none;' : '' }}">
            <div id="preview_doc_icon_{{ $inputId }}" class="preview-doc-icon" style="display: none;">
                <i class="ti ti-file-description"></i>
            </div>
        </div>

        <div class="preview-details">
            <div class="preview-file-header">
                <span class="preview-filename" id="preview_name_{{ $inputId }}" title="{{ $existingName ?? '' }}">
                    {{ $existingName ?? 'File Terpilih' }}
                </span>
                <span class="preview-badge {{ !empty($existingUrl) ? 'badge-stored' : 'badge-ready' }}" id="preview_badge_{{ $inputId }}">
                    <i class="ti {{ !empty($existingUrl) ? 'ti-database' : 'ti-check' }} me-1"></i>
                    <span id="preview_badge_text_{{ $inputId }}">{{ !empty($existingUrl) ? 'File Tersimpan' : 'Siap Diunggah' }}</span>
                </span>
            </div>
            <div class="preview-meta">
                <span class="preview-filesize" id="preview_size_{{ $inputId }}">
                    {{ !empty($existingUrl) ? 'Tersimpan di server' : '' }}
                </span>
            </div>
            <div class="preview-actions mt-2 d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn-preview-action action-change-btn" id="btn_change_{{ $inputId }}">
                    <i class="ti ti-refresh"></i>
                    <span>Ganti File</span>
                </button>
                @if ($shouldCrop)
                    <button type="button" class="btn-preview-action action-crop-btn" id="btn_crop_{{ $inputId }}" title="Sesuaikan / Potong Foto">
                        <i class="ti ti-crop"></i>
                        <span>Potong</span>
                    </button>
                @endif
                @if (!empty($existingUrl))
                    <a href="{{ $existingUrl }}" target="_blank" class="btn-preview-action action-view-btn" id="btn_view_{{ $inputId }}">
                        <i class="ti ti-eye"></i>
                        <span>Lihat</span>
                    </a>
                @endif
                <button type="button" class="btn-preview-action action-remove-btn" id="btn_remove_{{ $inputId }}">
                    <i class="ti ti-trash"></i>
                    <span>Hapus</span>
                </button>
            </div>
        </div>
    </div>

    @if ($shouldCrop)
    {{-- Scoped Cropper Modal for this input --}}
    <div class="modal fade cropper-admin-modal" id="crop_modal_{{ $inputId }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" style="z-index: 1080;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
            <div class="modal-content border-0 shadow-2xl rounded-3 overflow-hidden" style="border: 1px solid rgba(15, 23, 42, 0.1) !important; background: #ffffff;">
                <div class="modal-header py-2.5 px-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-2" style="width: 28px; height: 28px; background: rgba(30, 77, 62, 0.1); color: #1E4D3E;">
                            <i class="ti ti-crop fs-6"></i>
                        </span>
                        <div>
                            <h6 class="modal-title mb-0 fw-bold text-dark" style="font-size: 13.5px; font-family: 'Outfit', sans-serif;">Sesuaikan Gambar</h6>
                            <span class="text-muted d-block" style="font-size: 11px;">Geser, perbesar, atau atur posisi gambar</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 position-relative" style="height: 340px; background: #0b1310; overflow: hidden;">
                    <img id="crop_img_{{ $inputId }}" src="" alt="Source" style="max-width: 100%; display: block;">
                </div>
                <div class="px-3 py-2 bg-light border-top d-flex align-items-center justify-content-between">
                    <span class="text-muted fw-semibold" style="font-size: 11px;">Kontrol Foto</span>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" id="crop_rot_l_{{ $inputId }}" title="Putar Kiri (-90°)"><i class="ti ti-rotate-2"></i></button>
                        <button type="button" class="btn btn-outline-secondary" id="crop_rot_r_{{ $inputId }}" title="Putar Kanan (+90°)"><i class="ti ti-rotate-clockwise-2"></i></button>
                        <button type="button" class="btn btn-outline-secondary" id="crop_zoom_in_{{ $inputId }}" title="Perbesar (+)"><i class="ti ti-zoom-in"></i></button>
                        <button type="button" class="btn btn-outline-secondary" id="crop_zoom_out_{{ $inputId }}" title="Perkecil (-)"><i class="ti ti-zoom-out"></i></button>
                        <button type="button" class="btn btn-outline-secondary" id="crop_reset_{{ $inputId }}" title="Reset"><i class="ti ti-refresh"></i></button>
                    </div>
                </div>
                <div class="modal-footer py-2.5 px-3 bg-white border-top d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm text-white flex-grow-1 d-inline-flex align-items-center justify-content-center gap-1" id="crop_apply_{{ $inputId }}" style="background: #1E4D3E; font-weight: 600;">
                        <i class="ti ti-check"></i>
                        <span>Terapkan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    /* Inlined Fallback Styles to guarantee rendering even in dynamic AJAX modals */
    .global-input-file-wrapper {
        position: relative;
    }

    .custom-dropzone-box {
        border: 1.5px dashed rgba(30, 77, 62, 0.28) !important;
        border-radius: 10px !important;
        background-color: #F8FAF8 !important;
        padding: 10px 16px !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        text-align: left !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        min-height: 54px !important;
        gap: 12px !important;
    }

    .custom-dropzone-box:hover,
    .custom-dropzone-box.drag-over {
        border-color: #1E4D3E !important;
        background-color: rgba(30, 77, 62, 0.04) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 16px rgba(30, 77, 62, 0.08) !important;
    }

    .custom-dropzone-box.drag-over {
        box-shadow: 0 0 0 4px rgba(30, 77, 62, 0.15) !important;
    }

    .dropzone-icon-pill {
        width: 36px !important;
        height: 36px !important;
        border-radius: 8px !important;
        background-color: rgba(30, 77, 62, 0.08) !important;
        color: #1E4D3E !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 18px !important;
        flex-shrink: 0 !important;
        transition: transform 0.2s ease !important;
    }

    .custom-dropzone-box:hover .dropzone-icon-pill {
        transform: scale(1.08) !important;
    }

    .dropzone-content {
        display: flex !important;
        flex-direction: column !important;
        gap: 1px !important;
        flex: 1 !important;
        min-width: 0 !important;
    }

    .dropzone-title {
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: #0F172A !important;
        line-height: 1.3 !important;
    }

    .dropzone-subtitle {
        font-size: 11px !important;
        color: #64748B !important;
        line-height: 1.3 !important;
    }

    .dropzone-browse-btn {
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #1E4D3E !important;
        border-radius: 7px !important;
        padding: 5px 12px !important;
        background: #FFFFFF !important;
        border: 1px solid rgba(30, 77, 62, 0.25) !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        flex-shrink: 0 !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .dropzone-browse-btn:hover {
        background: #1E4D3E !important;
        color: #FFFFFF !important;
        border-color: #1E4D3E !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(30, 77, 62, 0.2) !important;
    }

    .dropzone-browse-btn:active {
        transform: scale(0.97) !important;
    }

    /* Preview Card (Distinct, No Overlapping Clutter) */
    .custom-preview-card {
        border: 1px solid rgba(30, 77, 62, 0.2) !important;
        border-radius: 12px !important;
        background-color: #FFFFFF !important;
        padding: 12px 14px !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04) !important;
        transition: all 0.2s ease !important;
    }

    .preview-thumbnail-container {
        width: 68px !important;
        height: 68px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(15, 23, 42, 0.08) !important;
        background-color: #F8FAF8 !important;
        overflow: hidden !important;
        flex-shrink: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        position: relative !important;
    }

    .preview-thumbnail-img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
    }

    .preview-doc-icon {
        font-size: 28px !important;
        color: #1E4D3E !important;
    }

    .preview-details {
        flex: 1 !important;
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
    }

    .preview-file-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 8px !important;
        margin-bottom: 2px !important;
    }

    .preview-filename {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #0F172A !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 180px !important;
    }

    .preview-badge {
        font-size: 10px !important;
        font-weight: 600 !important;
        padding: 2px 7px !important;
        border-radius: 6px !important;
        flex-shrink: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    .preview-badge.badge-ready {
        background: rgba(5, 150, 105, 0.1) !important;
        color: #059669 !important;
        border: 1px solid rgba(5, 150, 105, 0.2) !important;
    }

    .preview-badge.badge-stored {
        background: rgba(30, 77, 62, 0.08) !important;
        color: #1E4D3E !important;
        border: 1px solid rgba(30, 77, 62, 0.18) !important;
    }

    .preview-meta {
        font-size: 11px !important;
        color: #64748B !important;
    }

    .preview-filesize {
        font-family: 'JetBrains Mono', 'Geist Mono', monospace !important;
        font-size: 11px !important;
        color: #64748B !important;
    }

    .btn-preview-action {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 4px 9px !important;
        font-size: 11px !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        text-decoration: none !important;
    }

    .action-change-btn {
        background: rgba(30, 77, 62, 0.08) !important;
        color: #1E4D3E !important;
        border: 1px solid rgba(30, 77, 62, 0.2) !important;
    }
    .action-change-btn:hover {
        background: #1E4D3E !important;
        color: #FFFFFF !important;
    }

    .action-view-btn {
        background: #F8FAFC !important;
        color: #475569 !important;
        border: 1px solid #CBD5E1 !important;
    }
    .action-view-btn:hover {
        background: #0F172A !important;
        color: #FFFFFF !important;
        border-color: #0F172A !important;
    }

    .action-remove-btn {
        background: rgba(220, 38, 38, 0.08) !important;
        color: #DC2626 !important;
        border: 1px solid rgba(220, 38, 38, 0.2) !important;
    }
    .action-remove-btn:hover {
        background: #DC2626 !important;
        color: #FFFFFF !important;
    }

    .action-crop-btn {
        background: #F1F5F9 !important;
        color: #334155 !important;
        border: 1px solid #CBD5E1 !important;
    }
    .action-crop-btn:hover {
        background: #1E4D3E !important;
        color: #FFFFFF !important;
        border-color: #1E4D3E !important;
    }

    /* Cropper mask styles for admin modal */
    .cropper-admin-modal .cropper-round-mask .cropper-view-box,
    .cropper-admin-modal .cropper-round-mask .cropper-face {
        border-radius: 50% !important;
    }
    .cropper-admin-modal .cropper-round-mask .cropper-view-box {
        outline: 2px solid rgba(255, 255, 255, 0.95) !important;
        box-shadow: 0 0 0 1000px rgba(15, 23, 42, 0.72) !important;
    }
    .cropper-admin-modal .cropper-line,
    .cropper-admin-modal .cropper-point {
        background-color: #1E4D3E !important;
    }
    .cropper-admin-modal .cropper-modal {
        background-color: transparent !important;
    }
</style>

<script>
    (function() {
        var wrapper = document.getElementById('wrapper_{{ $inputId }}');
        if (!wrapper) return;

        var input = wrapper.querySelector('.real-file-input');
        var dropzone = wrapper.querySelector('.custom-dropzone-box');
        var previewCard = wrapper.querySelector('.custom-preview-card');
        var previewImg = wrapper.querySelector('.preview-thumbnail-img');
        var previewDocIcon = wrapper.querySelector('.preview-doc-icon');
        var previewName = wrapper.querySelector('.preview-filename');
        var previewSize = wrapper.querySelector('.preview-filesize');
        var previewBadge = wrapper.querySelector('.preview-badge');
        var previewBadgeText = wrapper.querySelector('#preview_badge_text_{{ $inputId }}');
        var btnChange = wrapper.querySelector('.action-change-btn');
        var btnRemove = wrapper.querySelector('.action-remove-btn');
        var removeFlag = wrapper.querySelector('.remove-flag');
        var maxBytes = {{ (int)($maxSizeMb ?? 2) }} * 1024 * 1024;

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        @if ($shouldCrop)
        var cropModalEl = document.getElementById('crop_modal_{{ $inputId }}');
        var cropImg = document.getElementById('crop_img_{{ $inputId }}');
        var btnCrop = wrapper.querySelector('#btn_crop_{{ $inputId }}');
        var btnApplyCrop = document.getElementById('crop_apply_{{ $inputId }}');
        var btnRotL = document.getElementById('crop_rot_l_{{ $inputId }}');
        var btnRotR = document.getElementById('crop_rot_r_{{ $inputId }}');
        var btnZoomIn = document.getElementById('crop_zoom_in_{{ $inputId }}');
        var btnZoomOut = document.getElementById('crop_zoom_out_{{ $inputId }}');
        var btnReset = document.getElementById('crop_reset_{{ $inputId }}');
        var cropper = null;
        var currentFileForCrop = null;
        var bsModal = null;

        function initCropperInstance() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            if (typeof Cropper === 'undefined') return;

            cropper = new Cropper(cropImg, {
                aspectRatio: {{ (is_numeric($finalCropRatio) && $finalCropRatio > 0) ? $finalCropRatio : 'NaN' }},
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.9,
                responsive: true,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                ready: function() {
                    var container = cropImg.closest('.cropper-container') || document.querySelector('.cropper-container');
                    @if ($finalCropShape === 'round')
                    if (container) container.classList.add('cropper-round-mask');
                    @endif
                }
            });
        }

        function openCropper(fileOrBlob) {
            if (!cropModalEl || typeof bootstrap === 'undefined') return;

            currentFileForCrop = fileOrBlob;

            // Move modal to body so it layers on top of any parent modal seamlessly
            if (cropModalEl.parentElement !== document.body) {
                document.body.appendChild(cropModalEl);
            }

            if (!bsModal) {
                bsModal = new bootstrap.Modal(cropModalEl, { backdrop: 'static', keyboard: false });
            }

            if (fileOrBlob instanceof Blob) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    cropImg.src = e.target.result;
                    bsModal.show();
                };
                reader.readAsDataURL(fileOrBlob);
            } else if (typeof fileOrBlob === 'string') {
                cropImg.src = fileOrBlob;
                bsModal.show();
            }
        }

        if (cropModalEl) {
            cropModalEl.addEventListener('shown.bs.modal', function() {
                initCropperInstance();
            });

            cropModalEl.addEventListener('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                if (!input.hasAttribute('data-has-cropped') && previewImg.style.display === 'none') {
                    input.value = '';
                }
            });
        }

        if (btnRotL) btnRotL.addEventListener('click', function() { if (cropper) cropper.rotate(-90); });
        if (btnRotR) btnRotR.addEventListener('click', function() { if (cropper) cropper.rotate(90); });
        if (btnZoomIn) btnZoomIn.addEventListener('click', function() { if (cropper) cropper.zoom(0.1); });
        if (btnZoomOut) btnZoomOut.addEventListener('click', function() { if (cropper) cropper.zoom(-0.1); });
        if (btnReset) btnReset.addEventListener('click', function() { if (cropper) cropper.reset(); });

        if (btnCrop) {
            btnCrop.addEventListener('click', function(e) {
                e.stopPropagation();
                if (input.files && input.files[0]) {
                    openCropper(input.files[0]);
                } else if (previewImg.src) {
                    fetch(previewImg.src)
                        .then(function(res) { return res.blob(); })
                        .then(function(blob) { openCropper(blob); })
                        .catch(function() {
                            input.click();
                        });
                } else {
                    input.click();
                }
            });
        }

        if (btnApplyCrop) {
            btnApplyCrop.addEventListener('click', function() {
                if (!cropper) return;

                var canvasOpts = {
                    fillColor: '#ffffff',
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                };
                @if (is_numeric($finalCropRatio) && $finalCropRatio == 1)
                canvasOpts.width = 800;
                canvasOpts.height = 800;
                @else
                canvasOpts.maxWidth = 1200;
                canvasOpts.maxHeight = 1200;
                @endif

                var canvas = cropper.getCroppedCanvas(canvasOpts);

                if (!canvas) {
                    if (bsModal) bsModal.hide();
                    return;
                }

                canvas.toBlob(function(blob) {
                    if (!blob) return;

                    var originalName = (currentFileForCrop && currentFileForCrop.name) ? currentFileForCrop.name : 'image.jpg';
                    var safeName = originalName.replace(/\.[^/.]+$/, "") + "_cropped.jpg";
                    var croppedFile = new File([blob], safeName, { type: 'image/jpeg', lastModified: Date.now() });

                    try {
                        var dt = new DataTransfer();
                        dt.items.add(croppedFile);
                        input.files = dt.files;
                        input.setAttribute('data-has-cropped', 'true');
                    } catch (err) {
                        console.error('DataTransfer error:', err);
                    }

                    handleFile(croppedFile, true);
                    if (bsModal) bsModal.hide();

                    if (typeof toastr !== 'undefined') {
                        toastr.success('Gambar berhasil disesuaikan!');
                    }
                }, 'image/jpeg', 0.9);
            });
        }
        @endif

        function handleFile(file, skipCropper = false) {
            if (!file) return;

            if (file.size > maxBytes) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran Terlalu Besar',
                        text: 'Ukuran file (' + formatBytes(file.size) + ') melebihi batas maksimal {{ $maxSizeMb }}MB!'
                    });
                } else {
                    alert('Ukuran file melebihi batas maksimal {{ $maxSizeMb }}MB!');
                }
                input.value = '';
                return;
            }

            @if ($shouldCrop)
            if (!skipCropper && file.type.startsWith('image/')) {
                openCropper(file);
                return;
            }
            @endif

            if (removeFlag) removeFlag.value = '0';
            previewName.textContent = file.name;
            previewName.title = file.name;
            previewSize.textContent = formatBytes(file.size);

            if (previewBadge) {
                previewBadge.className = 'preview-badge badge-ready';
                if (previewBadgeText) previewBadgeText.textContent = 'Siap Diunggah';
            }

            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    if (previewDocIcon) previewDocIcon.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                previewImg.style.display = 'none';
                if (previewDocIcon) previewDocIcon.style.display = 'block';
            }

            dropzone.style.setProperty('display', 'none', 'important');
            previewCard.style.removeProperty('display');
        }

        // Dropzone click triggers input
        dropzone.addEventListener('click', function(e) {
            input.click();
        });

        // Drag and drop handlers
        ['dragenter', 'dragover'].forEach(function(evtName) {
            dropzone.addEventListener(evtName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(function(evtName) {
            dropzone.addEventListener(evtName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('drag-over');
            });
        });

        dropzone.addEventListener('drop', function(e) {
            var dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length) {
                input.files = dt.files;
                handleFile(dt.files[0]);
            }
        });

        // Input change
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                handleFile(this.files[0]);
            }
        });

        // Change button
        if (btnChange) {
            btnChange.addEventListener('click', function(e) {
                e.stopPropagation();
                input.click();
            });
        }

        // Remove button
        if (btnRemove) {
            btnRemove.addEventListener('click', function(e) {
                e.stopPropagation();
                input.value = '';
                input.removeAttribute('data-has-cropped');
                if (removeFlag) removeFlag.value = '1';
                previewImg.src = '';
                previewImg.style.display = 'none';
                previewCard.style.setProperty('display', 'none', 'important');
                dropzone.style.removeProperty('display');
            });
        }
    })();
</script>
