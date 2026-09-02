<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Terminal Kiosk Presensi Wajah</title>
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
            background-color: #0b0f19;
            color: #f8fafc;
            min-height: 100vh;
            background-image: 
                radial-gradient(at 0% 0%, rgba(50, 116, 94, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(30, 41, 59, 0.2) 0px, transparent 50%);
        }

        .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .glass-card:hover {
            border-color: rgba(50, 116, 94, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -10px rgba(50, 116, 94, 0.2);
        }

        .glow-brand {
            box-shadow: 0 0 30px rgba(50, 116, 94, 0.35);
        }

        .pulse-dot {
            animation: pulse-glow 2s infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }
    </style>
</head>

<body class="flex flex-col justify-between p-4 md:p-8">
    <!-- Top Navigation / Header -->
    <header class="max-w-6xl w-full mx-auto flex items-center justify-between py-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-500/20 border border-brand-500/40 flex items-center justify-center text-emerald-400">
                <i class="ti ti-scan text-2xl"></i>
            </div>
            <div>
                <h1 class="text-base font-bold text-white tracking-tight flex items-center gap-2">
                    <span>Terminal Kiosk Outlet</span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 pulse-dot"></span>
                        AI Engine Ready
                    </span>
                </h1>
                <p class="text-xs text-slate-400 font-mono">Smart Face Recognition Hub</p>
            </div>
        </div>

        <a href="{{ route('dashboard.index') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-xs font-medium text-slate-300 hover:text-white transition-all">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali ke Admin</span>
        </a>
    </header>

    <!-- Main Bento Grid Hub -->
    <main class="max-w-6xl w-full mx-auto my-auto py-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono bg-brand-500/10 text-emerald-400 border border-brand-500/30 mb-3">
                <i class="ti ti-device-tablet"></i>
                STATION MODE
            </div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">
                Presensi Wajah Cerdas Otomatis
            </h2>
            <p class="text-sm text-slate-400 mt-2">
                Pilih mode terminal absensi kamera live outlet atau kelola kartu barcode digital karyawan.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <!-- Card 1: Primary Action - Launch Live Kiosk (Span 7) -->
            <div class="md:col-span-7 glass-panel rounded-2xl p-6 md:p-8 flex flex-col justify-between relative overflow-hidden border border-brand-500/30">
                <div class="absolute -right-12 -top-12 w-64 h-64 bg-brand-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-mono">
                            <i class="ti ti-sparkles"></i> MODE UTAMA
                        </span>
                        <span class="text-xs text-slate-500 font-mono">Full-Screen Standalone</span>
                    </div>

                    <h3 class="text-2xl font-bold text-white mb-2">Buka Terminal Kiosk Live</h3>
                    <p class="text-sm text-slate-400 mb-6 leading-relaxed">
                        Layar kamera pemindai wajah otomatis tanpa sentuh. Tempatkan di tablet kasir atau dinding outlet agar barista & staff dapat absen masuk/pulang dalam hitungan detik.
                    </p>

                    <div class="grid grid-cols-3 gap-3 mb-6 font-mono text-xs">
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                            <div class="text-slate-500 text-[10px] mb-1">AI DETEKSI</div>
                            <div class="text-white font-semibold flex items-center gap-1">
                                <i class="ti ti-cpu text-emerald-400"></i> Face API
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                            <div class="text-slate-500 text-[10px] mb-1">KECEPATAN</div>
                            <div class="text-white font-semibold flex items-center gap-1">
                                <i class="ti ti-bolt text-amber-400"></i> < 1 Detik
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                            <div class="text-slate-500 text-[10px] mb-1">ANTI SPOOF</div>
                            <div class="text-white font-semibold flex items-center gap-1">
                                <i class="ti ti-shield-check text-sky-400"></i> Liveness
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('facerecognition-presensi.scan_any') }}" 
                   class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-4 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold text-sm tracking-wide transition-all shadow-lg hover:shadow-brand-500/25 glow-brand group">
                    <i class="ti ti-camera text-lg group-hover:scale-110 transition-transform"></i>
                    <span>Luncurkan Kiosk Absensi Sekarang</span>
                    <i class="ti ti-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Card 2: QR & Barcode Generator Tool (Span 5) -->
            <div class="md:col-span-5 glass-panel rounded-2xl p-6 md:p-8 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20 font-mono">
                            <i class="ti ti-qrcode"></i> BADGE TOOLS
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-white mb-2">Generate QR Code Karyawan</h3>
                    <p class="text-xs text-slate-400 mb-5 leading-relaxed">
                        Cetak kode QR kartu ID untuk staff yang belum mendaftarkan biometrik wajah.
                    </p>

                    <form id="qrForm" class="space-y-3 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1.5 font-mono">MASUKKAN NIK KARYAWAN</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                                    <i class="ti ti-id"></i>
                                </span>
                                <input type="text" id="nik" name="nik" placeholder="Contoh: 123456789" maxlength="16" required
                                       class="w-full pl-9 pr-4 py-2.5 bg-slate-900/80 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 font-mono">
                            </div>
                        </div>
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 hover:text-white font-semibold text-xs transition-all">
                            <i class="ti ti-qrcode"></i>
                            <span>Buat QR Code ID</span>
                        </button>
                    </form>

                    <div id="errorMessage" class="hidden p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-medium"></div>

                    <div id="qrResult" class="hidden mt-4 p-4 rounded-xl bg-slate-900/90 border border-slate-800 text-center">
                        <div id="qrCode" class="inline-block p-2 bg-white rounded-lg shadow-inner mb-3"></div>
                        <div id="employeeInfo" class="text-xs text-slate-300"></div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800/80 text-[11px] text-slate-500 flex items-center gap-2 font-mono">
                    <i class="ti ti-info-circle text-slate-400"></i>
                    <span>Kompatibel dengan semua scanner barcode 2D</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-6xl w-full mx-auto text-center py-4 border-t border-slate-900 text-xs text-slate-600 font-mono">
        &copy; {{ date('Y') }} {{ $general_setting->nama_aplikasi ?? 'HR Presence' }} &bull; Smart Attendance Terminal
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

            qrCode.innerHTML = `<img src="data:image/png;base64,${data.qr_code}" alt="QR Code" class="w-32 h-32 mx-auto">`;
            employeeInfo.innerHTML = `
                <div class="font-bold text-white text-sm">${data.karyawan.nama_karyawan}</div>
                <div class="text-slate-400 font-mono mt-0.5">NIK: ${data.karyawan.nik}</div>
                <div class="inline-block mt-2 px-2 py-0.5 rounded-full text-[10px] font-semibold ${data.karyawan.status_aktif_karyawan == '1' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'}">
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
