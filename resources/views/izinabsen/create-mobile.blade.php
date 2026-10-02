@extends('layouts.mobile.modern')

@section('title', 'Ajukan Permisi / Izin')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('pengajuanizin.index') }}"
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
            background: {{ $t['bg_body'] }} !important;
        }

        .form-container {
            padding: 10px 5px calc(110px + env(safe-area-inset-bottom, 0px)) !important;
        }

        /* Mode Selector Pills */
        .mode-pill-container {
            display: flex;
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.1);
            border-radius: 14px;
            padding: 4px;
            gap: 4px;
            margin-bottom: 14px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        .mode-pill {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 4px;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .mode-pill ion-icon {
            font-size: 15px;
        }

        .mode-pill.active {
            background: {{ $t['primary'] }};
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }

        /* Form Controls */
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
        .form-label-group select,
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
            height: 76px !important;
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
        .form-label-group select:focus ~ label,
        .form-label-group select:valid ~ label,
        .form-label-group textarea:focus ~ label,
        .form-label-group textarea:not(:placeholder-shown) ~ label {
            top: 2px;
            left: 42px;
            font-size: 10px;
            font-weight: 600;
            color: {{ $t['primary'] }};
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
            margin-top: 15px;
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
        {{-- Mode Selector: Izin Jam vs Pulang Cepat --}}
        <div class="mode-pill-container">
            <button type="button" class="mode-pill active" data-mode="jam" id="pillModeJam">
                <ion-icon name="time-outline"></ion-icon>
                <span>Izin Jam</span>
            </button>
            <button type="button" class="mode-pill" data-mode="pulang_cepat" id="pillModePulang">
                <ion-icon name="exit-outline"></ion-icon>
                <span>Pulang Cepat</span>
            </button>
        </div>

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

        <form action="{{ route('izinabsen.store') }}" method="POST" id="formIzin" autocomplete="off">
            @csrf

            <input type="hidden" name="tipe_izin" id="tipe_izin" value="jam">

            {{-- 1. Tanggal Izin (Untuk Izin Jam & Pulang Cepat) --}}
            <div id="sectionTanggalSingle">
                <div class="form-label-group">
                    <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                    <input type="text" name="tanggal_izin" id="tanggal_izin" value="{{ old('tanggal_izin', date('Y-m-d')) }}" placeholder=" " required readonly>
                    <label for="tanggal_izin">Tanggal Izin <span class="req-star">*</span></label>
                </div>
            </div>

            {{-- 2. Section Mode: Izin Jam Kerja (Dari Jam s/d Jam) --}}
            <div id="sectionIzinJam">
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div class="form-label-group mb-0">
                        <ion-icon name="time-outline" class="input-icon"></ion-icon>
                        <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai', '10:00') }}" placeholder=" " required>
                        <label for="jam_mulai">Dari Jam <span class="req-star">*</span></label>
                    </div>
                    <div class="form-label-group mb-0">
                        <ion-icon name="time-outline" class="input-icon"></ion-icon>
                        <input type="time" name="jam_selesai" id="jam_selesai" value="{{ old('jam_selesai', '12:00') }}" placeholder=" " required>
                        <label for="jam_selesai">Sampai Jam <span class="req-star">*</span></label>
                    </div>
                </div>
                <div id="infoDurasiJam" class="mb-3 px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between"
                     style="background: var(--color-primary-soft, rgba(var(--bs-primary-rgb), 0.08)); border: 1px solid var(--theme-border, rgba(var(--bs-primary-rgb), 0.15)); color: var(--color-primary);">
                    <div class="flex items-center gap-1.5">
                        <ion-icon name="stopwatch-outline" class="text-sm"></ion-icon>
                        <span>Estimasi Durasi Izin:</span>
                    </div>
                    <span id="labelDurasiJam" class="font-mono font-bold">2 Jam</span>
                </div>
            </div>

            {{-- 3. Section Mode: Izin Pulang Lebih Cepat --}}
            <div id="sectionPulangCepat" style="display: none;">
                <div class="form-label-group">
                    <ion-icon name="exit-outline" class="input-icon"></ion-icon>
                    <input type="time" name="jam_pulang_cepat" id="jam_pulang_cepat" value="{{ old('jam_pulang_cepat', '15:00') }}" placeholder=" ">
                    <label for="jam_pulang_cepat">Jam Rencana Pulang <span class="req-star">*</span></label>
                </div>
                <div class="mb-3 px-3 py-2 rounded-xl text-[11.5px] leading-relaxed flex items-start gap-2 bg-amber-50/70 border border-amber-200/80 text-amber-900">
                    <ion-icon name="information-circle-outline" class="text-base text-amber-600 shrink-0 mt-0.5"></ion-icon>
                    <span>Pengajuan ini mengizinkan Anda meninggalkan outlet/kantor lebih awal dari jadwal shift reguler.</span>
                </div>
            </div>

            {{-- 4. Keperluan Permisi (Input String / Free Text) --}}
            <div class="form-label-group">
                <ion-icon name="create-outline" class="input-icon"></ion-icon>
                <input type="text" name="keperluan" id="keperluan" value="{{ old('keperluan') }}" placeholder=" " required maxlength="150">
                <label for="keperluan">Keperluan / Acara Permisi <span class="req-star">*</span></label>
            </div>

            {{-- 6. Keterangan Rincian Tambahan --}}
            <div class="form-label-group">
                <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                <textarea name="keterangan_detail" id="keterangan_detail" placeholder=" ">{{ old('keterangan_detail') }}</textarea>
                <label for="keterangan_detail">Rincian Tambahan (Opsional)</label>
            </div>

            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="paper-plane-outline"></ion-icon>
                <span>Ajukan Permisi / Izin</span>
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

            // Mode Selector
            const pills = document.querySelectorAll('.mode-pill');
            const inputTipeIzin = document.getElementById('tipe_izin');
            const sectionTanggalSingle = document.getElementById('sectionTanggalSingle');
            const sectionIzinJam = document.getElementById('sectionIzinJam');
            const sectionPulangCepat = document.getElementById('sectionPulangCepat');

            function switchMode(mode) {
                pills.forEach(p => p.classList.remove('active'));
                const activeBtn = document.querySelector(`.mode-pill[data-mode="${mode}"]`);
                if (activeBtn) activeBtn.classList.add('active');
                inputTipeIzin.value = mode;

                if (mode === 'pulang_cepat') {
                    sectionTanggalSingle.style.display = 'block';
                    sectionIzinJam.style.display = 'none';
                    sectionPulangCepat.style.display = 'block';
                    document.getElementById('jam_mulai').required = false;
                    document.getElementById('jam_selesai').required = false;
                    document.getElementById('jam_pulang_cepat').required = true;
                } else {
                    // jam
                    sectionTanggalSingle.style.display = 'block';
                    sectionIzinJam.style.display = 'block';
                    sectionPulangCepat.style.display = 'none';
                    document.getElementById('jam_mulai').required = true;
                    document.getElementById('jam_selesai').required = true;
                    document.getElementById('jam_pulang_cepat').required = false;
                }
            }

            pills.forEach(pill => {
                pill.addEventListener('click', function() {
                    switchMode(this.getAttribute('data-mode'));
                });
            });

            // Date Picker (Tanggal Izin)
            new AirDatepicker('#tanggal_izin', {
                locale: localeIndo,
                autoClose: true,
                isMobile: true,
                buttons: [btnToday, 'clear']
            });

            // Duration calculator for hourly permission
            const inputJamMulai = document.getElementById('jam_mulai');
            const inputJamSelesai = document.getElementById('jam_selesai');
            const labelDurasi = document.getElementById('labelDurasiJam');

            function calculateHourlyDuration() {
                const start = inputJamMulai.value;
                const end = inputJamSelesai.value;
                if (!start || !end) return;

                const [sh, sm] = start.split(':').map(Number);
                const [eh, em] = end.split(':').map(Number);
                let diffMins = (eh * 60 + em) - (sh * 60 + sm);

                if (diffMins <= 0) {
                    labelDurasi.textContent = 'Jam selesai harus lewat jam mulai';
                    labelDurasi.style.color = '#e11d48';
                } else {
                    const hours = Math.floor(diffMins / 60);
                    const mins = diffMins % 60;
                    let text = '';
                    if (hours > 0) text += hours + ' Jam ';
                    if (mins > 0) text += mins + ' Menit';
                    labelDurasi.textContent = text.trim();
                    labelDurasi.style.color = 'inherit';
                }
            }

            inputJamMulai.addEventListener('change', calculateHourlyDuration);
            inputJamSelesai.addEventListener('change', calculateHourlyDuration);
            calculateHourlyDuration();

            // Form Submit Safety
            const form = document.getElementById('formIzin');
            form.addEventListener('submit', function(e) {
                const mode = inputTipeIzin.value;
                if (mode === 'pulang_cepat') {
                    const tgl = document.getElementById('tanggal_izin').value;
                    const jamPulang = document.getElementById('jam_pulang_cepat').value;
                    if (!tgl || !jamPulang) {
                        e.preventDefault();
                        Swal.fire({ title: "Oops!", text: "Lengkapi tanggal dan jam pulang cepat.", icon: "warning" });
                        return;
                    }
                } else {
                    const tgl = document.getElementById('tanggal_izin').value;
                    const start = inputJamMulai.value;
                    const end = inputJamSelesai.value;
                    if (!tgl || !start || !end) {
                        e.preventDefault();
                        Swal.fire({ title: "Oops!", text: "Lengkapi tanggal dan jam permisi.", icon: "warning" });
                        return;
                    }
                    if (start >= end) {
                        e.preventDefault();
                        Swal.fire({ title: "Jam Tidak Valid", text: "Jam selesai harus lebih akhir dari jam mulai.", icon: "warning" });
                        return;
                    }
                }

                if (!document.getElementById('keperluan').value.trim()) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: "Tuliskan keperluan permisi Anda.", icon: "warning" });
                    return;
                }

                if (typeof window.showGlobalLoading === 'function') {
                    window.showGlobalLoading();
                }
            });
        });
    </script>
@endpush
