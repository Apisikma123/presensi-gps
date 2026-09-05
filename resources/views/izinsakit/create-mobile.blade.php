@extends('layouts.mobile.modern')

@section('title', 'Buat Izin Sakit')

@section('header_left')
    <a href="{{ route('pengajuanizin.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background: {{ $t['bg_body'] }} !important;
        }

        .form-container {
            padding: 10px 5px;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 12px;
            background: transparent !important;
            border: 1px solid {{ $t['primary'] }};
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 14px;
            top: 11px;
            font-size: 20px;
            color: {{ $t['primary'] }};
            z-index: 10;
            pointer-events: none;
        }

        .form-label-group input,
        .form-label-group textarea {
            width: 100% !important;
            height: 44px;
            padding: 18px 14px 2px 42px !important;
            font-size: 14px;
            font-weight: 500;
            color: {{ $t['primary'] }};
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            display: block !important;
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
            color: {{ $t['primary'] }};
            opacity: 0.8;
            pointer-events: none;
            transition: all 0.2s ease-in-out;
            margin-bottom: 0;
            z-index: 5;
        }

        .form-label-group input:focus ~ label,
        .form-label-group input:not(:placeholder-shown) ~ label,
        .form-label-group textarea:focus ~ label,
        .form-label-group textarea:not(:placeholder-shown) ~ label {
            top: 2px;
            left: 42px;
            font-size: 10px;
            font-weight: 600;
            color: {{ $t['primary'] }};
        }

        /* Modern Mobile File Upload (DESIGN.md Compliant) */
        .mobile-upload-box {
            border: 1.5px dashed rgba(30, 77, 62, 0.28);
            border-radius: 14px;
            background: #ffffff;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            margin-bottom: 12px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            min-height: 100px;
        }

        .mobile-upload-box:active {
            transform: scale(0.99);
            background: rgba(30, 77, 62, 0.04);
            border-color: {{ $t['primary'] ?? '#1E4D3E' }};
        }

        .mobile-upload-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(30, 77, 62, 0.08);
            color: {{ $t['primary'] ?? '#1E4D3E' }};
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 4px;
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

        /* Preview Card - Separate Thumbnail and Meta (Zero Overlap) */
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
            width: 72px;
            height: 72px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: #f8fafc;
            flex-shrink: 0;
            display: block;
        }

        .mobile-preview-info {
            flex: 1;
            min-width: 0;
        }

        .mobile-preview-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        .mobile-preview-size {
            font-size: 11px;
            color: #64748b;
            display: block;
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
            color: {{ $t['primary'] ?? '#1E4D3E' }};
            background: rgba(30, 77, 62, 0.08);
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
            background: {{ $t['primary'] }};
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 5px;
            transition: all 0.3s;
        }

        .btn-submit-modern:active {
            transform: scale(0.97);
            background: {{ $t['primary'] }};
            filter: brightness(0.9);
        }
    </style>
@endpush

@section('content')
    <div class="fade-up form-container">
        <form action="{{ route('izinsakit.store') }}" method="POST" id="formIzin" enctype="multipart/form-data" autocomplete="off">
            @csrf

            <div class="form-label-group">
                <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                <input type="text" name="dari" id="dari" placeholder=" " required readonly>
                <label for="dari">Dari Tanggal</label>
            </div>

            <div class="form-label-group">
                <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                <input type="text" name="sampai" id="sampai" placeholder=" " required readonly>
                <label for="sampai">Sampai Tanggal</label>
            </div>

            <div class="form-label-group">
                <ion-icon name="calculator-outline" class="input-icon"></ion-icon>
                <input type="text" name="jml_hari" id="jml_hari" placeholder=" " readonly>
                <label for="jml_hari">Jumlah Hari</label>
            </div>

            {{-- Hidden File Input --}}
            <input type="file" name="sid" id="sid" accept="image/jpeg,image/png,image/webp" style="display: none;">

            {{-- State 1: Modern Empty Dropzone --}}
            <div class="mobile-upload-box" id="mobileUploadDropzone">
                <div class="mobile-upload-icon">
                    <ion-icon name="cloud-upload-outline"></ion-icon>
                </div>
                <div class="mobile-upload-title">Upload Surat Dokter (SID)</div>
                <div class="mobile-upload-hint">Ketuk untuk memilih foto dokumen (Maks. 2MB)</div>
            </div>

            {{-- State 2: Modern Preview Card (Separate Image and Meta, Zero Overlapping) --}}
            <div class="mobile-preview-card" id="mobilePreviewCard" style="display: none;">
                <img src="" id="mobilePreviewThumb" alt="Preview SID" class="mobile-preview-thumb">
                <div class="mobile-preview-info">
                    <span class="mobile-preview-name" id="mobilePreviewName">-</span>
                    <span class="mobile-preview-size" id="mobilePreviewSize">-</span>
                    <span class="mobile-preview-badge">
                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                        Dokumen Siap Dikirim
                    </span>
                    <div class="mobile-preview-actions">
                        <button type="button" class="btn-change-photo" id="btnChangePhoto">Ganti Foto</button>
                        <button type="button" class="btn-delete-photo" id="btnDeletePhoto">Hapus</button>
                    </div>
                </div>
            </div>

            <div class="form-label-group">
                <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                <textarea name="keterangan" id="keterangan" placeholder=" " required></textarea>
                <label for="keterangan">Keterangan</label>
            </div>

            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="paper-plane-outline"></ion-icon>
                <span>Kirim Izin Sakit</span>
            </button>
        </form>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const localeIndo = {
                days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
                months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                today: 'Hari ini',
                clear: 'Hapus',
                dateFormat: 'yyyy-MM-dd',
                timeFormat: 'HH:mm',
                firstDay: 1
            };

            const batasi_hari_izin = "{{ $general_setting->batasi_hari_izin ?? 0 }}";
            const jml_hari_izin_max = "{{ $general_setting->jml_hari_izin_max ?? 0 }}";

            function hitungHari(startDate, endDate) {
                if (startDate && endDate) {
                    var start = new Date(startDate);
                    var end = new Date(endDate);
                    var timeDifference = end - start + (1000 * 3600 * 24);
                    var dayDifference = Math.ceil(timeDifference / (1000 * 3600 * 24));
                    return dayDifference > 0 ? dayDifference : 0;
                }
                return 0;
            }

            const btnToday = {
                content: 'Hari ini',
                className: 'air-datepicker-button-today',
                onClick: (dp) => {
                    const today = new Date();
                    dp.selectDate(today);
                    dp.setViewDate(today);
                }
            };

            const dpDari = new AirDatepicker('#dari', {
                locale: localeIndo,
                autoClose: true,
                isMobile: true,
                buttons: [btnToday, 'clear'],
                onSelect: ({date, formattedDate}) => {
                    let sampai = document.getElementById('sampai').value;
                    let jmlhari = hitungHari(formattedDate, sampai);
                    document.getElementById('jml_hari').value = jmlhari;
                }
            });

            const dpSampai = new AirDatepicker('#sampai', {
                locale: localeIndo,
                autoClose: true,
                isMobile: true,
                buttons: [btnToday, 'clear'],
                onSelect: ({date, formattedDate}) => {
                    let dari = document.getElementById('dari').value;
                    let jmlhari = hitungHari(dari, formattedDate);
                    document.getElementById('jml_hari').value = jmlhari;
                }
            });

            // Modern Mobile File Upload Handling
            const fileInput = document.getElementById('sid');
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
                        previewCard.style.display = 'none';
                        dropzone.style.display = 'flex';
                    });
                }

                fileInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (!file) return;

                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            title: "Ukuran Terlalu Besar",
                            text: "Ukuran foto maksimal adalah 2MB! (" + formatBytes(file.size) + ")",
                            icon: "warning"
                        });
                        this.value = '';
                        return;
                    }

                    previewName.textContent = file.name;
                    previewSize.textContent = formatBytes(file.size);

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewThumb.src = e.target.result;
                        dropzone.style.display = 'none';
                        previewCard.style.display = 'flex';
                    };
                    reader.readAsDataURL(file);
                });
            }

            const form = document.getElementById('formIzin');
            form.addEventListener('submit', function(e) {
                let dari = document.getElementById('dari').value;
                let sampai = document.getElementById('sampai').value;
                let jml_hari = document.getElementById('jml_hari').value;
                let keterangan = document.getElementById('keterangan').value;
                let hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

                if (!dari || !sampai) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Periode Izin Harus Diisi !', icon: "warning" });
                    return;
                }

                if (new Date(sampai) < new Date(dari)) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Periode Izin Tidak Valid !', icon: "warning" });
                    return;
                }

                if (batasi_hari_izin == 1 && jml_hari > jml_hari_izin_max) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Maksimal Izin ' + jml_hari_izin_max + ' Hari !', icon: "warning" });
                    return;
                }

                if (!hasFile) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Surat Dokter Harus Diupload !', icon: "warning" });
                    return;
                }

                if (!keterangan.trim()) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Keterangan Harus Diisi !', icon: "warning" });
                    return;
                }

                const btn = document.getElementById('btnSimpan');
                btn.disabled = true;
                btn.innerHTML = `<ion-icon name="sync-outline" class="animate-spin"></ion-icon><span>Memproses...</span>`;
            });
        });
    </script>
@endpush
