@extends('layouts.mobile.modern')

@section('title', 'Buat Izin Absen')

@section('header_left')
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('pengajuanizin.index') }}"
        onclick="if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) { event.preventDefault(); window.history.back(); }"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all"
        title="Kembali">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background: {{ $t['bg_body'] }} !important; /* Extremely light teal background from image */
        }

        .form-container {
            padding: 10px 5px calc(110px + env(safe-area-inset-bottom, 0px)) !important;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 12px; /* Gap between separate cards */
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

        /* Floating effect exactly as requested */
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
        <form action="{{ route('izinabsen.store') }}" method="POST" id="formIzin" autocomplete="off">
            @csrf

            <div class="form-label-group">
                <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                <input type="text" name="dari" id="dari" value="{{ old('dari') }}" placeholder=" " required readonly>
                <label for="dari">Dari Tanggal <span class="req-star">*</span></label>
            </div>

            <div class="form-label-group">
                <ion-icon name="calendar-outline" class="input-icon"></ion-icon>
                <input type="text" name="sampai" id="sampai" value="{{ old('sampai') }}" placeholder=" " required readonly>
                <label for="sampai">Sampai Tanggal <span class="req-star">*</span></label>
            </div>

            <div class="form-label-group">
                <ion-icon name="calculator-outline" class="input-icon"></ion-icon>
                <input type="text" name="jml_hari" id="jml_hari" value="{{ old('jml_hari') }}" placeholder=" " readonly>
                <label for="jml_hari">Jumlah Hari <span class="auto-tag">(Otomatis)</span></label>
            </div>

            <div class="form-label-group">
                <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                <textarea name="keterangan" id="keterangan" placeholder=" " required>{{ old('keterangan') }}</textarea>
                <label for="keterangan">Keterangan Izin <span class="req-star">*</span></label>
            </div>

            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="paper-plane-outline"></ion-icon>
                <span>Ajukan Izin Absen</span>
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
            const sistem_hari_kerja = "{{ $general_setting->sistem_hari_kerja ?? '6' }}";

            let hitungHariTimeout = null;
            function updateHitungHari(startDate, endDate) {
                if (!startDate || !endDate) {
                    const el = document.getElementById('jml_hari');
                    if (el) el.value = 0;
                    return;
                }
                clearTimeout(hitungHariTimeout);
                hitungHariTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('cuti.hitungHariAjax') }}",
                        type: 'GET',
                        data: { dari: startDate, sampai: endDate },
                        success: function(res) {
                            if (res && res.success) {
                                const el = document.getElementById('jml_hari');
                                if (el) el.value = res.jumlah_hari;
                            }
                        },
                        error: function() {
                            var start = new Date(startDate + 'T00:00:00');
                            var end = new Date(endDate + 'T00:00:00');
                            if (end >= start) {
                                var count = Math.round((end - start) / (1000 * 60 * 60 * 24)) + 1;
                                const el = document.getElementById('jml_hari');
                                if (el) el.value = count;
                            }
                        }
                    });
                }, 150);
            }

            function hitungHari(startDate, endDate) {
                updateHitungHari(startDate, endDate);
                return parseInt(document.getElementById('jml_hari')?.value || 0);
            }

            // Inisialisasi hitung hari jika ada value dari old()
            const initDari = document.getElementById('dari').value;
            const initSampai = document.getElementById('sampai').value;
            if (initDari && initSampai) {
                updateHitungHari(initDari, initSampai);
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
                    updateHitungHari(formattedDate, sampai);
                }
            });

            const dpSampai = new AirDatepicker('#sampai', {
                locale: localeIndo,
                autoClose: true,
                isMobile: true,
                buttons: [btnToday, 'clear'],
                onSelect: ({date, formattedDate}) => {
                    let dari = document.getElementById('dari').value;
                    updateHitungHari(dari, formattedDate);
                }
            });

            const form = document.getElementById('formIzin');
            form.addEventListener('submit', function(e) {
                let dari = document.getElementById('dari').value;
                let sampai = document.getElementById('sampai').value;
                let jml_hari = document.getElementById('jml_hari').value;
                let keterangan = document.getElementById('keterangan').value;

                if (!dari || !sampai) {
                    e.preventDefault();
                    if (typeof window.hideGlobalLoading === 'function') window.hideGlobalLoading();
                    Swal.fire({ title: "Oops!", text: 'Periode Izin Harus Diisi !', icon: "warning" });
                    return;
                }

                if (new Date(sampai) < new Date(dari)) {
                    e.preventDefault();
                    if (typeof window.hideGlobalLoading === 'function') window.hideGlobalLoading();
                    Swal.fire({ title: "Oops!", text: 'Periode Izin Tidak Valid !', icon: "warning" });
                    return;
                }

                if (batasi_hari_izin == 1 && jml_hari > jml_hari_izin_max) {
                    e.preventDefault();
                    if (typeof window.hideGlobalLoading === 'function') window.hideGlobalLoading();
                    Swal.fire({ title: "Oops!", text: 'Maksimal Izin ' + jml_hari_izin_max + ' Hari !', icon: "warning" });
                    return;
                }

                if (!keterangan.trim()) {
                    e.preventDefault();
                    if (typeof window.hideGlobalLoading === 'function') window.hideGlobalLoading();
                    Swal.fire({ title: "Oops!", text: 'Keterangan Harus Diisi !', icon: "warning" });
                    return;
                }

                e.preventDefault();
                const btn = document.getElementById('btnSimpan');
                btn.style.pointerEvents = 'none';
                btn.innerHTML = `<ion-icon name="sync-outline" class="animate-spin"></ion-icon><span>Memproses...</span>`;

                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async (response) => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Gagal menyimpan pengajuan izin');
                    }
                    if (typeof window.hideGlobalLoading === 'function') window.hideGlobalLoading();
                    Swal.fire({
                        title: 'Berhasil Diajukan!',
                        text: data.message || 'Pengajuan izin Anda telah berhasil dikirim.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        customClass: { popup: 'modern-swal-popup' }
                    }).then(() => {
                        window.location.href = "{{ route('pengajuanizin.index') }}";
                    });
                })
                .catch((error) => {
                    if (typeof window.hideGlobalLoading === 'function') window.hideGlobalLoading();
                    btn.style.pointerEvents = 'auto';
                    btn.innerHTML = `<span>Kirim Pengajuan</span><ion-icon name="send-outline"></ion-icon>`;
                    Swal.fire({
                        title: 'Oops!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonColor: '{{ $t['primary'] ?? '#1E4D3E' }}'
                    });
                });
            });
        });
    </script>
@endpush
