<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Kiosk Presensi Wajah - {{ $general_setting->nama_aplikasi ?? 'HR Presence' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Smart Kiosk Face Recognition Attendance Terminal" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf9',
                            100: '#ccfbf1',
                            500: '#32745e',
                            600: '#275d4b',
                            700: '#1e483a',
                            900: '#0f241d',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
        }

        .card-surface {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .card-surface:hover {
            border-color: #cbd5e1;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
        }

        .btn-brand {
            background-color: #32745e;
            color: #ffffff;
            transition: all 0.2s ease;
        }

        .btn-brand:hover {
            background-color: #275d4b;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px -2px rgba(50, 116, 94, 0.3);
        }
    </style>
</head>

<body class="flex flex-col justify-between p-4 md:p-8">
    <!-- Top Header -->
    <header class="max-w-5xl w-full mx-auto flex items-center justify-between py-2">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-600 shadow-sm">
                <i class="ti ti-scan text-2xl"></i>
            </div>
            <div>
                <h1 class="text-base font-bold text-slate-800 tracking-tight flex items-center gap-2">
                    <span>{{ $general_setting->nama_aplikasi ?? 'HR Presence' }}</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Kiosk Mode
                    </span>
                </h1>
                <p class="text-xs text-slate-500">Terminal Presensi Biometrik & QR Code</p>
            </div>
        </div>

        <a href="{{ route('dashboard.index') }}" 
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-600 hover:text-slate-800 shadow-sm transition-all">
            <i class="ti ti-arrow-left text-sm"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl w-full mx-auto my-auto py-8">
        <div class="text-center max-w-xl mx-auto mb-8">
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                Terminal Presensi Karyawan
            </h2>
            <p class="text-sm text-slate-500 mt-1.5">
                Gunakan tablet outlet untuk absensi kamera wajah otomatis atau cetak kartu QR ID karyawan.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <!-- Card 1: Launch Face Recognition Kiosk (Span 7) -->
            <div class="md:col-span-7 card-surface rounded-2xl p-6 md:p-8 flex flex-col justify-between border-brand-500/20">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="ti ti-camera"></i> Rekomendasi Outlet
                        </span>
                        <span class="text-xs text-slate-400 font-mono">Layar Penuh Tablet</span>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 mb-2">Buka Kiosk Absen Wajah (Live)</h3>
                    <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                        Layar kamera pemindai wajah otomatis tanpa sentuh. Staff (barista, kasir, kitchen) cukup berdiri di depan kamera untuk langsung tercatat absen masuk atau pulang.
                    </p>

                    <div class="grid grid-cols-3 gap-3 mb-6 text-xs">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="text-slate-400 text-[10px] font-bold uppercase mb-0.5">Metode</div>
                            <div class="text-slate-800 font-semibold flex items-center gap-1">
                                <i class="ti ti-sparkles text-emerald-600"></i> Face API
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="text-slate-400 text-[10px] font-bold uppercase mb-0.5">Kecepatan</div>
                            <div class="text-slate-800 font-semibold flex items-center gap-1">
                                <i class="ti ti-bolt text-amber-500"></i> < 1 Detik
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="text-slate-400 text-[10px] font-bold uppercase mb-0.5">Keamanan</div>
                            <div class="text-slate-800 font-semibold flex items-center gap-1">
                                <i class="ti ti-shield-check text-brand-600"></i> Anti-Spoof
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('facerecognition-presensi.scan_any') }}" 
                   class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl btn-brand font-bold text-sm tracking-wide shadow-sm">
                    <i class="ti ti-camera text-lg"></i>
                    <span>Buka Terminal Kamera Outlet</span>
                    <i class="ti ti-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Card 2: Generate QR Code ID Badge (Span 5) -->
            <div class="md:col-span-5 card-surface rounded-2xl p-6 md:p-8 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            <i class="ti ti-qrcode"></i> Alat ID Card
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 mb-1.5">Generate QR Code Karyawan</h3>
                    <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                        Cetak kode QR kartu identitas untuk karyawan yang belum mendaftarkan biometrik wajah.
                    </p>

                    <form id="qrForm" class="space-y-3 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">NIK KARYAWAN</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i class="ti ti-id"></i>
                                </span>
                                <input type="text" id="nik" name="nik" placeholder="Masukkan NIK..." maxlength="16" required
                                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-500 focus:bg-white transition-all font-mono">
                            </div>
                        </div>
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all">
                            <i class="ti ti-qrcode"></i>
                            <span>Buat QR Code</span>
                        </button>
                    </form>

                    <div id="errorMessage" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium"></div>

                    <div id="qrResult" class="hidden mt-3 p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                        <div id="qrCode" class="inline-block p-2 bg-white rounded-lg border border-slate-200 shadow-sm mb-2"></div>
                        <div id="employeeInfo" class="text-xs text-slate-700"></div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 text-[11px] text-slate-400 flex items-center gap-1.5">
                    <i class="ti ti-info-circle text-brand-600"></i>
                    <span>Dapat di-scan dengan scanner barcode standar</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-5xl w-full mx-auto text-center py-2 text-xs text-slate-400">
        &copy; {{ date('Y') }} {{ $general_setting->nama_aplikasi ?? 'HR Presence' }} &bull; Sistem Presensi Karyawan
    </footer>

    <!-- Scripts -->
    <script>
        document.getElementById('qrForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const nik = document.getElementById('nik').value.trim();
            const errorMessage = document.getElementById('errorMessage');
            const qrResult = document.getElementById('qrResult');

            errorMessage.classList.add('hidden');
            qrResult.classList.add('hidden');

            if (!nik) {
                showError('NIK tidak boleh kosong');
                return;
            }

            fetch(`/facerecognition-presensi/generate/${nik}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        showQRCode(data);
                    } else {
                        showError(data.message || 'Karyawan tidak ditemukan');
                    }
                })
                .catch(error => {
                    showError('Terjadi kesalahan saat memproses data');
                    console.error('Error:', error);
                });
        });

        function showQRCode(data) {
            const qrResult = document.getElementById('qrResult');
            const qrCode = document.getElementById('qrCode');
            const employeeInfo = document.getElementById('employeeInfo');

            qrCode.innerHTML = `<img src="data:image/png;base64,${data.qr_code}" alt="QR Code" class="w-28 h-28 mx-auto">`;
            employeeInfo.innerHTML = `
                <div class="font-bold text-slate-900 text-sm">${data.karyawan.nama_karyawan}</div>
                <div class="text-slate-500 font-mono mt-0.5">NIK: ${data.karyawan.nik}</div>
                <div class="inline-block mt-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold ${data.karyawan.status_aktif_karyawan == '1' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'}">
                    ${data.karyawan.status_aktif_karyawan == '1' ? 'Aktif' : 'Tidak Aktif'}
                </div>
            `;
            qrResult.classList.remove('hidden');
        }

        function showError(message) {
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = message;
            errorMessage.classList.remove('hidden');
        }
    </script>
</body>
</html>
