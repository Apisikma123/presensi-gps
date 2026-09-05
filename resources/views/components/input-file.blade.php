@props([
    'name' => '',
    'label' => '',
    'value' => null,
    'accept' => 'image/jpeg,image/png,image/webp,image/jpg',
    'required' => false,
    'helper' => 'Format: JPG, PNG, WEBP (Maks. 2MB)',
    'maxSizeMb' => 2,
    'class' => ''
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
            $existingUrl = asset('storage/uploads/sid/' . $value);
        } elseif ($name === 'foto') {
            $existingUrl = asset('storage/karyawan/' . $value);
        } else {
            $existingUrl = asset('storage/' . $value);
        }
    }
@endphp

<div class="form-group mb-3 global-input-file-wrapper {{ $class }}" id="wrapper_{{ $inputId }}" data-name="{{ $name }}">
    @if ($displayLabel)
        <label for="{{ $inputId }}" class="form-label fw-bold d-flex align-items-center justify-content-between" style="font-size: 13px; color: #0F172A; margin-bottom: 6px;">
            <span>
                {{ $displayLabel }}
                @if ($required)
                    <span class="text-danger">*</span>
                @endif
            </span>
            <span class="text-muted fw-normal" style="font-size: 11px;">{{ $helper }}</span>
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
            <span class="dropzone-subtitle">{{ $helper }}</span>
        </div>
        <button type="button" class="btn btn-sm btn-light border dropzone-browse-btn">
            <i class="ti ti-folder-open me-1"></i>Pilih File
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
                <span class="badge {{ !empty($existingUrl) ? 'bg-label-info' : 'bg-label-success' }} preview-badge" id="preview_badge_{{ $inputId }}">
                    <i class="ti {{ !empty($existingUrl) ? 'ti-database' : 'ti-check' }} me-1"></i>
                    <span id="preview_badge_text_{{ $inputId }}">{{ !empty($existingUrl) ? 'File Tersimpan' : 'Siap Diunggah' }}</span>
                </span>
            </div>
            <div class="preview-meta">
                <span class="preview-filesize text-muted" id="preview_size_{{ $inputId }}">
                    {{ !empty($existingUrl) ? 'Tersimpan di server' : '' }}
                </span>
            </div>
            <div class="preview-actions mt-2 d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-xs btn-outline-primary action-change-btn" id="btn_change_{{ $inputId }}">
                    <i class="ti ti-refresh me-1"></i>Ganti File
                </button>
                @if (!empty($existingUrl))
                    <a href="{{ $existingUrl }}" target="_blank" class="btn btn-xs btn-outline-secondary" id="btn_view_{{ $inputId }}">
                        <i class="ti ti-eye me-1"></i>Lihat
                    </a>
                @endif
                <button type="button" class="btn btn-xs btn-outline-danger action-remove-btn" id="btn_remove_{{ $inputId }}">
                    <i class="ti ti-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@once
    @push('mystyle')
        <style>
            /* ===== GLOBAL MODERN FILE UPLOAD STYLES (DESIGN.MD COMPLIANT) ===== */
            .global-input-file-wrapper {
                position: relative;
            }

            .custom-dropzone-box {
                border: 1.5px dashed #CBD5E1;
                border-radius: 14px;
                background-color: #F8FAF8;
                padding: 20px 16px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                cursor: pointer;
                transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
                min-height: 120px;
                gap: 8px;
            }

            .custom-dropzone-box:hover,
            .custom-dropzone-box.drag-over {
                border-color: #1E4D3E;
                background-color: rgba(30, 77, 62, 0.04);
                transform: translateY(-1px);
            }

            .custom-dropzone-box.drag-over {
                box-shadow: 0 0 0 4px rgba(30, 77, 62, 0.1);
            }

            .dropzone-icon-pill {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                background-color: rgba(30, 77, 62, 0.08);
                color: #1E4D3E;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 22px;
                transition: transform 0.2s ease;
            }

            .custom-dropzone-box:hover .dropzone-icon-pill {
                transform: scale(1.08);
            }

            .dropzone-content {
                display: flex;
                flex-direction: column;
                gap: 2px;
            }

            .dropzone-title {
                font-size: 13px;
                font-weight: 600;
                color: #0F172A;
            }

            .dropzone-subtitle {
                font-size: 11px;
                color: #64748B;
            }

            .dropzone-browse-btn {
                font-size: 11.5px;
                font-weight: 600;
                color: #1E4D3E;
                border-radius: 8px;
                padding: 4px 12px;
                background: #FFFFFF;
                box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            }

            /* Preview Card (Distinct, No Overlapping Clutter) */
            .custom-preview-card {
                border: 1px solid rgba(15, 23, 42, 0.1);
                border-radius: 14px;
                background-color: #FFFFFF;
                padding: 12px 14px;
                display: flex;
                align-items: center;
                gap: 14px;
                box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
                transition: all 0.2s ease;
            }

            .preview-thumbnail-container {
                width: 80px;
                height: 80px;
                border-radius: 10px;
                border: 1px solid rgba(15, 23, 42, 0.08);
                background-color: #F8FAFC;
                overflow: hidden;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
            }

            .preview-thumbnail-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .preview-doc-icon {
                font-size: 32px;
                color: #1E4D3E;
            }

            .preview-details {
                flex: 1;
                min-width: 0;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .preview-file-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 2px;
            }

            .preview-filename {
                font-size: 13px;
                font-weight: 600;
                color: #0F172A;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 220px;
            }

            .preview-badge {
                font-size: 10.5px;
                font-weight: 600;
                padding: 3px 8px;
                border-radius: 6px;
                flex-shrink: 0;
            }

            .preview-meta {
                font-size: 11px;
                color: #64748B;
            }

            .btn-xs {
                padding: 3px 8px;
                font-size: 11px;
                border-radius: 6px;
                font-weight: 600;
            }
        </style>
    @endpush
@endonce

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

        function handleFile(file) {
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

            if (removeFlag) removeFlag.value = '0';
            previewName.textContent = file.name;
            previewName.title = file.name;
            previewSize.textContent = formatBytes(file.size);

            if (previewBadge) {
                previewBadge.className = 'badge bg-label-success preview-badge';
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
                if (removeFlag) removeFlag.value = '1';
                previewImg.src = '';
                previewImg.style.display = 'none';
                previewCard.style.setProperty('display', 'none', 'important');
                dropzone.style.removeProperty('display');
            });
        }
    })();
</script>
