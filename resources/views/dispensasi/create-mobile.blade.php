@extends('layouts.mobile.modern')

@section('title', 'Ajukan Dispensasi')

@section('header_left')
    <a href="{{ route('dispensasi.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.css" rel="stylesheet">
    <style>
        body {
            background: {{ $t['bg_body'] ?? '#F8FAF8' }} !important;
        }

        .form-container {
            padding: 10px 5px;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 12px;
            background: #ffffff !important;
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .form-label-group:focus-within {
            border-color: {{ $t['primary'] ?? '#1E4D3E' }};
            box-shadow: 0 0 0 3px rgba(30, 77, 62, 0.1);
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 14px;
            top: 13px;
            font-size: 20px;
            color: {{ $t['primary'] ?? '#1E4D3E' }};
            z-index: 10;
            pointer-events: none;
        }

        .form-label-group input,
        .form-label-group textarea {
            width: 100% !important;
            height: 48px;
            padding: 18px 14px 2px 44px !important;
            font-size: 14px;
            font-weight: 500;
            color: #0f172a;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            display: block !important;
        }

        .form-label-group textarea {
            height: 90px !important;
            padding-top: 24px !important;
            resize: none;
        }

        .form-label-group label {
            position: absolute;
            top: 13px;
            left: 44px;
            font-size: 13px;
            color: #64748b;
            pointer-events: none;
            transition: all 0.2s ease-in-out;
            margin-bottom: 0;
            z-index: 5;
        }

        .form-label-group input:focus ~ label,
        .form-label-group input:not(:placeholder-shown) ~ label,
        .form-label-group textarea:focus ~ label,
        .form-label-group textarea:not(:placeholder-shown) ~ label {
            top: 3px;
            left: 44px;
            font-size: 10px;
            font-weight: 600;
            color: {{ $t['primary'] ?? '#1E4D3E' }};
        }

        .btn-submit-modern {
            width: 100%;
            height: 48px;
            background: {{ $t['primary'] ?? '#1E4D3E' }};
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 12px rgba(30, 77, 62, 0.25);
        }

        .btn-submit-modern:active {
            transform: scale(0.97);
            filter: brightness(0.92);
        }
    </style>
@endpush

@section('content')
    <div class="fade-up form-container pt-2 pb-24">
        {{-- Info Banner --}}
        <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-3.5 mb-3 flex items-start gap-3">
            <ion-icon name="information-circle-outline" class="text-xl text-[#1E4D3E] shrink-0 mt-0.5"></ion-icon>
            <div class="text-[12px] text-emerald-900 leading-relaxed">
                Dispensasi digunakan apabila Anda mengalami kendala perjalanan atau instruksi outlet sehingga memerlukan penyesuaian batas jam absensi masuk.
            </div>
        </div>

        <form action="{{ route('dispensasi.store') }}" method="POST" id="formDispensasi" autocomplete="off">
            @csrf

            {{-- Tanggal Dispensasi --}}
            <div class="form-label-group">
                <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                <input type="text" name="tanggal" id="tanggal" placeholder=" " value="{{ date('Y-m-d') }}" required readonly>
                <label for="tanggal">Tanggal Dispensasi</label>
            </div>

            {{-- Batas Jam Dispensasi --}}
            <div class="form-label-group">
                <ion-icon name="time-outline" class="input-icon"></ion-icon>
                <input type="time" name="batas_dispensasi" id="batas_dispensasi" placeholder=" " value="08:00" step="60" required>
                <label for="batas_dispensasi">Batas Jam Masuk (Toleransi)</label>
            </div>

            {{-- Alasan Dispensasi --}}
            <div class="form-label-group">
                <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                <textarea name="alasan" id="alasan" placeholder=" " required></textarea>
                <label for="alasan">Alasan Keterlambatan</label>
            </div>

            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="paper-plane-outline"></ion-icon>
                <span>Kirim Pengajuan Dispensasi</span>
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
                daysShort: ['Min', 'Sen', 'Rab', 'Kam', 'Jum', 'Sab'],
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
                selectedDates: [new Date()]
            });

            const form = document.getElementById('formDispensasi');
            form.addEventListener('submit', function(e) {
                const tanggal = document.getElementById('tanggal').value;
                const batas = document.getElementById('batas_dispensasi').value;
                const alasan = document.getElementById('alasan').value;

                if (!tanggal || !batas || !alasan.trim()) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: "Oops!",
                            text: 'Harap isi semua kolom formulir dispensasi!',
                            icon: "warning"
                        });
                    }
                    return;
                }

                const btn = document.getElementById('btnSimpan');
                btn.disabled = true;
                btn.innerHTML = `<ion-icon name="sync-outline" class="animate-spin text-lg"></ion-icon><span>Memproses...</span>`;
            });
        });
    </script>
@endpush
