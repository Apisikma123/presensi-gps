@extends('layouts.mobile.modern')

@section('title', 'Ajukan Lembur (SPK)')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('overtime.index') }}"
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
    </style>
@endpush

@section('content')
    <div class="fade-up form-container">
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

        <form action="{{ route('overtime.store') }}" method="POST" id="formLembur" autocomplete="off">
            @csrf

            {{-- Hidden inputs expected by controller format (Y-m-d\TH:i) --}}
            <input type="hidden" name="lembur_mulai" id="lembur_mulai">
            <input type="hidden" name="lembur_selesai" id="lembur_selesai">

            {{-- Tanggal Lembur --}}
            <div class="form-label-group">
                <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                <input type="text" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" placeholder=" " required readonly>
                <label for="tanggal">Tanggal Lembur <span class="req-star">*</span></label>
            </div>

            {{-- Jenis Hari --}}
            <div class="form-label-group">
                <ion-icon name="briefcase-outline" class="input-icon"></ion-icon>
                <select name="day_type" id="day_type" required>
                    <option value="WORKDAY" {{ old('day_type') == 'WORKDAY' ? 'selected' : '' }}>Hari Kerja Biasa</option>
                    <option value="OFFDAY_5DAYS" {{ old('day_type') == 'OFFDAY_5DAYS' ? 'selected' : '' }}>Hari Libur Istirahat (Skema 5 Hari)</option>
                    <option value="OFFDAY_6DAYS" {{ old('day_type') == 'OFFDAY_6DAYS' ? 'selected' : '' }}>Hari Libur Istirahat (Skema 6 Hari)</option>
                    <option value="PUBLIC_HOLIDAY" {{ old('day_type') == 'PUBLIC_HOLIDAY' ? 'selected' : '' }}>Hari Libur Resmi Nasional</option>
                </select>
                <ion-icon name="chevron-down-outline" class="select-chevron"></ion-icon>
                <label for="day_type">Jenis Hari Lembur <span class="req-star">*</span></label>
            </div>

            {{-- Jam Mulai & Selesai Lembur --}}
            <div class="grid grid-cols-2 gap-2 mb-3">
                <div class="form-label-group mb-0">
                    <ion-icon name="time-outline" class="input-icon"></ion-icon>
                    <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai', '17:00') }}" placeholder=" " required>
                    <label for="jam_mulai">Dari Jam <span class="req-star">*</span></label>
                </div>
                <div class="form-label-group mb-0">
                    <ion-icon name="time-outline" class="input-icon"></ion-icon>
                    <input type="time" name="jam_selesai" id="jam_selesai" value="{{ old('jam_selesai', '20:00') }}" placeholder=" " required>
                    <label for="jam_selesai">Sampai Jam <span class="req-star">*</span></label>
                </div>
            </div>

            {{-- Durasi Indicator --}}
            <div id="infoDurasiLembur" class="mb-3 px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between"
                 style="background: var(--color-primary-soft, rgba(27,54,93,0.08)); color: var(--color-primary, #1B365D); border: 1px solid var(--theme-border, rgba(27,54,93,0.15));">
                <span class="flex items-center gap-1.5">
                    <ion-icon name="timer-outline" class="text-sm"></ion-icon>
                    <span>Estimasi Durasi:</span>
                </span>
                <span id="durasiText" class="font-bold font-mono">3 Jam</span>
            </div>

            {{-- Kebijakan Lembur --}}
            @if (isset($policies) && $policies->isNotEmpty())
                <div class="form-label-group">
                    <ion-icon name="shield-checkmark-outline" class="input-icon"></ion-icon>
                    <select name="overtime_policy_id" id="overtime_policy_id">
                        @foreach ($policies as $pol)
                            <option value="{{ $pol->id }}" {{ (old('overtime_policy_id') == $pol->id || (isset($defaultPolicy) && $defaultPolicy->id == $pol->id)) ? 'selected' : '' }}>
                                {{ $pol->policy_name }} ({{ $pol->policy_code }})
                            </option>
                        @endforeach
                    </select>
                    <ion-icon name="chevron-down-outline" class="select-chevron"></ion-icon>
                    <label for="overtime_policy_id">Kebijakan Lembur</label>
                </div>
            @endif

            {{-- Keterangan / Pekerjaan Lembur --}}
            <div class="form-label-group">
                <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                <textarea name="keterangan" id="keterangan" placeholder=" " required>{{ old('keterangan') }}</textarea>
                <label for="keterangan">Rincian Tugas Lembur <span class="req-star">*</span></label>
            </div>

            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="paper-plane-outline"></ion-icon>
                <span>Ajukan SPK Lembur</span>
            </button>
        </form>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. AirDatepicker Setup
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

            new AirDatepicker('#tanggal', {
                locale: localeIndo,
                autoClose: true,
                isMobile: true,
                buttons: [btnToday, 'clear'],
                onSelect: () => syncDatetime()
            });

            // 2. Selects has-value sync
            document.querySelectorAll('#day_type, #overtime_policy_id').forEach(sel => {
                const sync = () => {
                    if (sel.value) sel.classList.add('has-value');
                    else sel.classList.remove('has-value');
                };
                sel.addEventListener('change', sync);
                sync();
            });

            // 3. Time calculation & Sync to hidden datetime inputs
            const tglEl = document.getElementById('tanggal');
            const jmMulai = document.getElementById('jam_mulai');
            const jmSelesai = document.getElementById('jam_selesai');
            const durasiText = document.getElementById('durasiText');
            const hiddenMulai = document.getElementById('lembur_mulai');
            const hiddenSelesai = document.getElementById('lembur_selesai');

            function syncDatetime() {
                const tgl = tglEl.value || "{{ date('Y-m-d') }}";
                const mulai = jmMulai.value || '17:00';
                const selesai = jmSelesai.value || '20:00';

                hiddenMulai.value = tgl + 'T' + mulai;

                // Duration Calculation
                const [h1, m1] = mulai.split(':').map(Number);
                const [h2, m2] = selesai.split(':').map(Number);
                let diffMins = (h2 * 60 + m2) - (h1 * 60 + m1);

                if (diffMins <= 0) {
                    // Next day cross-midnight
                    diffMins += 24 * 60;
                    const nextDay = new Date(new Date(tgl + 'T00:00:00').getTime() + 86400000);
                    const y = nextDay.getFullYear();
                    const m = String(nextDay.getMonth() + 1).padStart(2, '0');
                    const d = String(nextDay.getDate()).padStart(2, '0');
                    hiddenSelesai.value = `${y}-${m}-${d}T${selesai}`;
                } else {
                    hiddenSelesai.value = tgl + 'T' + selesai;
                }

                const hrs = Math.floor(diffMins / 60);
                const mins = diffMins % 60;
                durasiText.textContent = `${hrs} Jam${mins > 0 ? ' ' + mins + ' Menit' : ''}`;
            }

            jmMulai.addEventListener('input', syncDatetime);
            jmSelesai.addEventListener('input', syncDatetime);
            tglEl.addEventListener('change', syncDatetime);
            syncDatetime();

            // Form Submit validation
            const form = document.getElementById('formLembur');
            form.addEventListener('submit', function (e) {
                syncDatetime();
                if (!hiddenMulai.value || !hiddenSelesai.value) {
                    e.preventDefault();
                    alert('Harap pastikan tanggal dan jam lembur terisi.');
                    return false;
                }
            });
        });
    </script>
@endpush
