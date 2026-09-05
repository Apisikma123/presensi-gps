@extends('layouts.mobile.modern')

@section('title', 'Buat Izin Cuti')

@section('header_left')
    <a href="{{ route('pengajuanizin.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background: {{ $t['bg_body'] }};
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
            appearance: none;
            -webkit-appearance: none;
        }

        .form-label-group select {
            cursor: pointer;
        }

        .form-label-group .select-chevron {
            position: absolute;
            right: 15px;
            top: 14px;
            font-size: 16px;
            color: {{ $t['primary'] }};
            pointer-events: none;
            z-index: 10;
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
            margin-top: 5px;
            transition: all 0.3s;
        }

        .btn-submit-modern:active {
            transform: scale(0.97);
            background: {{ $t['primary'] }};
            filter: brightness(0.9);
        }

        .bento-cuti-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.06);
            padding: 14px 16px;
            margin-bottom: 14px;
            transition: all 0.2s ease;
        }

        .bento-cuti-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 12px;
        }

        .bento-cuti-title-group {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .bento-cuti-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(30, 77, 62, 0.08);
            color: #1E4D3E;
            font-size: 18px;
            flex-shrink: 0;
        }

        .bento-badge-leave {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 4px 8px;
            border-radius: 6px;
            background: rgba(30, 77, 62, 0.08);
            color: #1E4D3E;
            border: 1px solid rgba(30, 77, 62, 0.15);
            max-width: 140px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bento-cuti-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            background: #f8fafc;
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 12px;
            padding: 10px 4px;
            text-align: center;
        }

        .bento-cuti-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 2px 4px;
        }

        .bento-cuti-col:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -3px;
            top: 15%;
            height: 70%;
            width: 1px;
            background: rgba(15, 23, 42, 0.1);
        }

        .bento-label-micro {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748B;
            margin-bottom: 2px;
        }

        .bento-digit {
            font-family: 'JetBrains Mono', 'Geist Mono', monospace;
            font-weight: 800;
            line-height: 1;
        }

        .bento-tag-status {
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 9999px;
            margin-top: 4px;
            display: inline-block;
        }

        .bento-progress-wrap {
            margin-top: 10px;
            padding: 0 2px;
        }

        .bento-progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            font-weight: 600;
            color: #64748B;
            margin-bottom: 4px;
        }

        .bento-progress-track {
            width: 100%;
            height: 6px;
            background: #f1f5f9;
            border-radius: 9999px;
            overflow: hidden;
        }

        .bento-progress-fill {
            height: 100%;
            background: #1E4D3E;
            border-radius: 9999px;
            transition: width 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
@endpush

@section('content')
    <div class="fade-up form-container">
        {{-- BENTO CARD: SISA & KUOTA CUTI (Brew & Beam Enterprise DESIGN.md) --}}
        @php
            $infoCuti = $sisa_cuti_info ?? [
                'tahun' => date('Y'),
                'kuota' => 12,
                'terpakai' => 0,
                'pending' => 0,
                'sisa' => 12,
                'jenis_cuti_nama' => 'Cuti Tahunan',
                'kode_cuti' => 'C01'
            ];
            $pctTerpakai = $infoCuti['kuota'] > 0 ? min(100, round(($infoCuti['terpakai'] / $infoCuti['kuota']) * 100)) : 0;
        @endphp
        
        <div class="bento-cuti-card">
            {{-- Header Card --}}
            <div class="bento-cuti-header">
                <div class="bento-cuti-title-group">
                    <div class="bento-cuti-icon">
                        <ion-icon name="calendar-outline"></ion-icon>
                    </div>
                    <div style="min-width:0;">
                        <h4 style="font-size:13px; font-weight:800; color:#0F172A; margin:0; line-height:1.2;">Saldo Cuti Karyawan</h4>
                        <span style="font-size:11px; color:#64748B; font-weight:500;">Tahun Periode {{ $infoCuti['tahun'] }}</span>
                    </div>
                </div>
                <span id="badge-jenis-cuti" class="bento-badge-leave">
                    {{ $infoCuti['jenis_cuti_nama'] }}
                </span>
            </div>

            {{-- 3-Column Asymmetric Metric Bento --}}
            <div class="bento-cuti-metrics">
                {{-- Sisa --}}
                <div class="bento-cuti-col">
                    <span class="bento-label-micro">Sisa Cuti</span>
                    <div style="display:flex; align-items:baseline; gap:2px;">
                        <span id="val-sisa-cuti" class="bento-digit" style="font-size:24px; color:#1E4D3E;">{{ $infoCuti['sisa'] }}</span>
                        <span style="font-size:10px; font-weight:600; color:#64748B;">Hr</span>
                    </div>
                    <span class="bento-tag-status" style="background:#D1FAE5; color:#065F46;">Tersedia</span>
                </div>

                {{-- Terpakai --}}
                <div class="bento-cuti-col">
                    <span class="bento-label-micro">Terpakai</span>
                    <div style="display:flex; align-items:baseline; gap:2px;">
                        <span id="val-terpakai-cuti" class="bento-digit" style="font-size:20px; color:#D97706;">{{ $infoCuti['terpakai'] }}</span>
                        <span style="font-size:10px; font-weight:600; color:#64748B;">Hr</span>
                    </div>
                    <span class="bento-tag-status" style="background:#FEF3C7; color:#92400E;">Diambil</span>
                </div>

                {{-- Kuota --}}
                <div class="bento-cuti-col">
                    <span class="bento-label-micro">Total Kuota</span>
                    <div style="display:flex; align-items:baseline; gap:2px;">
                        <span id="val-kuota-cuti" class="bento-digit" style="font-size:20px; color:#334155;">{{ $infoCuti['kuota'] }}</span>
                        <span style="font-size:10px; font-weight:600; color:#64748B;">Hr</span>
                    </div>
                    <span class="bento-tag-status" style="background:#F1F5F9; color:#475569;">Plafon</span>
                </div>
            </div>

            {{-- Progress Bar Utilization --}}
            <div class="bento-progress-wrap">
                <div class="bento-progress-header">
                    <span>Pemakaian Kuota</span>
                    <span id="label-pct-cuti" style="font-family:'JetBrains Mono',monospace; font-weight:700; color:#334155;">{{ $pctTerpakai }}%</span>
                </div>
                <div class="bento-progress-track">
                    <div id="bar-pct-cuti" class="bento-progress-fill" style="width: {{ $pctTerpakai }}%;"></div>
                </div>
            </div>

            {{-- Pending notice if any --}}
            @if(!empty($infoCuti['pending']) && $infoCuti['pending'] > 0)
                <div style="margin-top:10px; padding:8px 10px; border-radius:10px; background:#FFFBEB; border:1px solid #FDE68A; color:#B45309; font-size:11px; font-weight:600; display:flex; align-items:center; gap:6px;">
                    <ion-icon name="time-outline" style="font-size:14px; flex-shrink:0;"></ion-icon>
                    <span>Ada <strong>{{ $infoCuti['pending'] }} hari</strong> cuti pending menunggu approval.</span>
                </div>
            @endif
        </div>

        <form action="{{ route('izincuti.store') }}" method="POST" id="formIzin" autocomplete="off">
            @csrf
            
            <div class="form-label-group">
                <ion-icon name="today-outline" class="input-icon"></ion-icon>
                <select name="kode_cuti" id="kode_cuti" required>
                    <option value="" disabled selected></option>
                    @foreach ($jenis_cuti as $d)
                        <option value="{{ $d->kode_cuti }}" {{ $d->kode_cuti == 'C01' ? 'selected' : '' }}>{{ $d->jenis_cuti }}</option>
                    @endforeach
                </select>
                <ion-icon name="chevron-down-outline" class="select-chevron"></ion-icon>
                <label for="kode_cuti">Pilih Jenis Cuti</label>
            </div>
            
            {{-- Info Sisa Cuti Alert Banner --}}
            <div id="info-sisa-cuti" class="mb-3 px-3 py-2 rounded-xl text-xs font-semibold flex items-center gap-2" style="background: rgba(30, 77, 62, 0.08); border: 1px solid rgba(30, 77, 62, 0.15); color: #1E4D3E;">
                <ion-icon name="information-circle-outline" class="text-base shrink-0"></ion-icon>
                <span id="label-sisa-cuti">Sisa {{ $infoCuti['jenis_cuti_nama'] }} Anda adalah {{ $infoCuti['sisa'] }} Hari</span>
            </div>

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

            <div class="form-label-group">
                <ion-icon name="briefcase-outline" class="input-icon"></ion-icon>
                <input type="text" name="pelimpahan_tugas" id="pelimpahan_tugas" placeholder=" " required>
                <label for="pelimpahan_tugas">Pelimpahan Tugas</label>
            </div>

            <div class="form-label-group">
                <ion-icon name="person-outline" class="input-icon"></ion-icon>
                <input type="text" name="nama_kepala_divisi" id="nama_kepala_divisi" placeholder=" " required>
                <label for="nama_kepala_divisi">Nama Kepala Divisi</label>
            </div>

            <div class="form-label-group">
                <ion-icon name="document-text-outline" class="input-icon"></ion-icon>
                <textarea name="keterangan" id="keterangan" placeholder=" " required></textarea>
                <label for="keterangan">Alasan Cuti</label>
            </div>

            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="paper-plane-outline"></ion-icon>
                <span>Ajukan Izin Cuti</span>
            </button>
        </form>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let sisaCuti = {{ $infoCuti['sisa'] ?? 0 }};
            let currentCutiName = "{{ $infoCuti['jenis_cuti_nama'] ?? 'Cuti Tahunan' }}";

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

            // Handle Sisa Cuti Info & Bento Card Sync
            $('#kode_cuti').on('change', function() {
                const kode_cuti = $(this).val();
                const infoBox = $('#info-sisa-cuti');
                const labelSisa = $('#label-sisa-cuti');
                
                if (kode_cuti) {
                    labelSisa.html('<ion-icon name="sync-outline" class="animate-spin"></ion-icon> Mengecek sisa cuti...');
                    infoBox.removeClass('hidden');
                    
                    $.ajax({
                        type: 'GET',
                        url: '/izincuti/getsisaharicuti',
                        data: {
                            kode_cuti: kode_cuti
                        },
                        cache: false,
                        success: function(respond) {
                            if (respond.status) {
                                labelSisa.text(respond.message);
                                sisaCuti = respond.sisa_cuti;
                                currentCutiName = respond.nama_cuti || currentCutiName;

                                $('#badge-jenis-cuti').text(currentCutiName);
                                $('#val-sisa-cuti').text(respond.sisa_cuti);
                                $('#val-terpakai-cuti').text(respond.terpakai !== undefined ? respond.terpakai : 0);
                                $('#val-kuota-cuti').text(respond.kuota !== undefined ? respond.kuota : 0);

                                let pct = respond.kuota > 0 ? Math.min(100, Math.round(((respond.terpakai || 0) / respond.kuota) * 100)) : 0;
                                $('#label-pct-cuti').text(pct + '%');
                                $('#bar-pct-cuti').css('width', pct + '%');
                            } else {
                                infoBox.addClass('hidden');
                                sisaCuti = 0;
                            }
                        },
                        error: function() {
                            infoBox.addClass('hidden');
                        }
                    });
                } else {
                    infoBox.addClass('hidden');
                }
            });

            const form = document.getElementById('formIzin');
            form.addEventListener('submit', function(e) {
                let kode_cuti = document.getElementById('kode_cuti').value;
                let dari = document.getElementById('dari').value;
                let sampai = document.getElementById('sampai').value;
                let jml_hari = document.getElementById('jml_hari').value;
                let pelimpahan_tugas = document.getElementById('pelimpahan_tugas').value;
                let nama_kepala_divisi = document.getElementById('nama_kepala_divisi').value;
                let keterangan = document.getElementById('keterangan').value;

                if (!kode_cuti) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Jenis Cuti Harus Dipilih !', icon: "warning" });
                    return;
                }

                if (!dari || !sampai) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Periode Cuti Harus Diisi !', icon: "warning" });
                    return;
                }

                if (new Date(sampai) < new Date(dari)) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Periode Cuti Tidak Valid !', icon: "warning" });
                    return;
                }

                if (parseInt(jml_hari) > parseInt(sisaCuti)) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Jumlah hari melebihi batas! ' + $('#label-sisa-cuti').text(), icon: "warning" });
                    return;
                }

                if (!pelimpahan_tugas.trim()) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Pelimpahan Tugas Harus Diisi !', icon: "warning" });
                    return;
                }

                if (!nama_kepala_divisi.trim()) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Nama Kepala Divisi Harus Diisi !', icon: "warning" });
                    return;
                }

                if (!keterangan.trim()) {
                    e.preventDefault();
                    Swal.fire({ title: "Oops!", text: 'Alasan Cuti Harus Diisi !', icon: "warning" });
                    return;
                }

                const btn = document.getElementById('btnSimpan');
                btn.disabled = true;
                btn.innerHTML = `<ion-icon name="sync-outline" class="animate-spin"></ion-icon><span>Memproses...</span>`;
            });
        });
    </script>
@endpush
