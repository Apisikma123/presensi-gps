@extends('layouts.mobile.modern')

@section('title', 'Ajukan Kasbon')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('loan.index') }}"
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

        .info-preview-box {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.1);
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 14px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
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

        <form action="{{ route('loan.store') }}" method="POST" id="formPinjaman" autocomplete="off">
            @csrf

            {{-- Jumlah Pinjaman --}}
            <div class="form-label-group">
                <ion-icon name="cash-outline" class="input-icon"></ion-icon>
                <input type="number" name="loan_amount" id="loan_amount" value="{{ old('loan_amount', 500000) }}" placeholder=" " required min="50000" step="10000">
                <label for="loan_amount">Jumlah Kasbon / Pinjaman (Rp) <span class="req-star">*</span></label>
            </div>

            {{-- Tenor Cicilan --}}
            <div class="form-label-group">
                <ion-icon name="calendar-number-outline" class="input-icon"></ion-icon>
                <select name="installment_months" id="installment_months" required>
                    <option value="1" {{ old('installment_months', '1') == '1' ? 'selected' : '' }}>1 Bulan (Potong gaji berikutnya)</option>
                    <option value="2" {{ old('installment_months') == '2' ? 'selected' : '' }}>2 Bulan</option>
                    <option value="3" {{ old('installment_months') == '3' ? 'selected' : '' }}>3 Bulan</option>
                    <option value="4" {{ old('installment_months') == '4' ? 'selected' : '' }}>4 Bulan</option>
                    <option value="5" {{ old('installment_months') == '5' ? 'selected' : '' }}>5 Bulan</option>
                    <option value="6" {{ old('installment_months') == '6' ? 'selected' : '' }}>6 Bulan</option>
                    <option value="12" {{ old('installment_months') == '12' ? 'selected' : '' }}>12 Bulan (1 Tahun)</option>
                </select>
                <ion-icon name="chevron-down-outline" class="select-chevron"></ion-icon>
                <label for="installment_months">Tenor Cicilan <span class="req-star">*</span></label>
            </div>

            {{-- Tanggal Mulai / Pencairan --}}
            <div class="form-label-group">
                <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                <input type="text" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" placeholder=" " required readonly>
                <label for="start_date">Tanggal Mulai / Pencairan <span class="req-star">*</span></label>
            </div>

            {{-- Alasan / Keperluan --}}
            <div class="form-label-group">
                <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                <textarea name="notes" id="notes" placeholder=" ">{{ old('notes') }}</textarea>
                <label for="notes">Alasan / Keperluan Kasbon</label>
            </div>

            {{-- Preview Cicilan --}}
            <div class="info-preview-box">
                <div class="flex justify-between items-center mb-1 text-slate-600">
                    <span class="text-xs font-semibold">Estimasi Cicilan per Bulan:</span>
                    <span id="preview-cicilan" class="font-mono font-bold text-slate-900 text-sm">Rp 500.000</span>
                </div>
                <div class="text-[11px] text-slate-400">
                    * Cicilan otomatis dipotong dari slip gaji setiap periode payroll.
                </div>
            </div>

            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="paper-plane-outline"></ion-icon>
                <span>Ajukan Kasbon</span>
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

            new AirDatepicker('#start_date', {
                locale: localeIndo,
                autoClose: true,
                isMobile: true,
                buttons: [btnToday, 'clear']
            });

            // 2. Select has-value sync
            const selectEl = document.getElementById('installment_months');
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

            // 3. Calculation Preview
            const amountInput = document.getElementById('loan_amount');
            const previewEl = document.getElementById('preview-cicilan');

            function updatePreview() {
                const amount = parseFloat(amountInput.value) || 0;
                const months = parseInt(selectEl.value) || 1;
                const monthly = Math.round(amount / months);
                previewEl.innerText = 'Rp ' + monthly.toLocaleString('id-ID');
            }

            amountInput.addEventListener('input', updatePreview);
            selectEl.addEventListener('change', updatePreview);
            updatePreview();
        });
    </script>
@endpush
