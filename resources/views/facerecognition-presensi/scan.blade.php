<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Scan QR Code - {{ $karyawan->nama_karyawan }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistem Presensi QR Code & Biometrik" name="description" />
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

    <!-- QR Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

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
            background-color: #080c14;
            color: #f1f5f9;
            min-height: 100vh;
            background-image: 
                radial-gradient(at 0% 0%, rgba(50, 116, 94, 0.18) 0px, transparent 45%),
                radial-gradient(at 100% 100%, rgba(15, 23, 42, 0.4) 0px, transparent 50%);
        }

        .glass-panel {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }

        .camera-container, .canvas-container, .loading, .status-message {
            display: none;
        }

        #qr-reader {
            border: 2px solid rgba(50, 116, 94, 0.3) !important;
            border-radius: 16px !important;
            background: #020617 !important;
            overflow: hidden !important;
        }

        #qr-reader video {
            width: 100% !important;
            border-radius: 14px !important;
        }

        #qr-reader__scan_region, #qr-reader__scan_region>img, #qr-reader__status_span {
            display: none !important;
        }

        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid #10b981;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="p-4 md:p-6 flex flex-col justify-between">
    <!-- Top Bar -->
    <header class="max-w-2xl w-full mx-auto flex items-center justify-between pb-4">
        <button onclick="window.location.href='{{ route('facerecognition-presensi.index') }}'"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-700/60 text-xs font-semibold text-slate-300 hover:text-white transition-all">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali</span>
        </button>

        <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900/80 border border-slate-800 text-xs font-mono text-emerald-400">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>QR STATION SCAN</span>
        </div>
    </header>

    <!-- Main Card -->
    <main class="max-w-2xl w-full mx-auto my-auto">
        <div class="glass-panel rounded-2xl p-6 md:p-8">
            <!-- Employee Card Header -->
            <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center gap-4 mb-6">
                <div class="w-14 h-14 rounded-xl bg-brand-500/20 border border-brand-500/40 flex items-center justify-center text-emerald-400 flex-shrink-0">
                    <i class="ti ti-user text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white tracking-tight">{{ $karyawan->nama_karyawan }}</h2>
                    <div class="text-xs text-slate-400 font-mono mt-0.5">
                        <i class="ti ti-id me-1"></i>NIK: {{ $karyawan->nik }}
                    </div>
                </div>
            </div>

            <!-- Clock Display -->
            <div class="text-center mb-6 font-mono">
                <div class="text-3xl md:text-4xl font-extrabold text-emerald-400 tracking-tight" id="timeDisplay">--:--:--</div>
                <div class="text-xs text-slate-400 mt-1" id="dateDisplay">Memuat tanggal...</div>
            </div>

            <!-- QR Reader Viewport -->
            <div class="mb-6">
                <div id="qr-reader" class="rounded-xl overflow-hidden shadow-inner"></div>
            </div>

            <!-- Manual Action Buttons -->
            <div class="grid grid-cols-2 gap-3 mb-4 font-mono">
                <button class="py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs tracking-wider flex items-center justify-center gap-2 transition-all"
                        onclick="manualAbsen(1)">
                    <i class="ti ti-login"></i>
                    <span>Absen Masuk</span>
                </button>
                <button class="py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs tracking-wider flex items-center justify-center gap-2 transition-all"
                        onclick="manualAbsen(0)">
                    <i class="ti ti-logout"></i>
                    <span>Absen Pulang</span>
                </button>
            </div>

            <!-- Hidden Elements for Camera Capture -->
            <div class="camera-container" id="cameraContainer">
                <video id="video" autoplay></video>
            </div>
            <div class="canvas-container" id="canvasContainer">
                <canvas id="canvas"></canvas>
            </div>

            <!-- Loading & Status -->
            <div class="loading text-center p-3" id="loading">
                <div class="spinner mb-2"></div>
                <p class="text-xs text-slate-400 font-mono">Memproses presensi...</p>
            </div>

            <div class="status-message p-3 rounded-xl text-center text-xs font-mono font-semibold" id="statusMessage"></div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-2xl w-full mx-auto text-center py-2 text-xs text-slate-600 font-mono">
        &copy; {{ date('Y') }} {{ $general_setting->nama_aplikasi ?? 'HR Presence' }} &bull; QR Code Attendance
    </footer>

    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    <script>
        let stream = null;
        let currentStatus = null;
        let html5QrcodeScanner = null;
        const karyawan = @json($karyawan);

        // Update waktu real-time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');
            const dateString = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            document.getElementById('timeDisplay').textContent = timeString;
            document.getElementById('dateDisplay').textContent = dateString;
        }

        // Update waktu setiap detik
        setInterval(updateTime, 1000);
        updateTime();

        // Initialize QR Scanner
        function initQRScanner() {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    },
                    aspectRatio: 1.0,
                    showTorchButtonIfSupported: true,
                    showZoomSliderIfSupported: true,
                    rememberLastUsedCamera: true,
                    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
                },
                false
            );

            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        // QR Scan Success
        function onScanSuccess(decodedText, decodedResult) {
            // Stop scanner
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }

            // Parse QR code data
            try {
                const url = new URL(decodedText);
                const pathParts = url.pathname.split('/');
                const scannedNik = pathParts[pathParts.length - 1];

                // Validate if scanned NIK matches current employee
                if (scannedNik === karyawan.nik) {
                    showStatus('QR Code terdeteksi! Memulai proses absen...', 'success');

                    // Auto detect status (masuk/pulang) based on time
                    const currentHour = new Date().getHours();
                    const status = currentHour < 12 ? 1 : 0; // Masuk before 12, pulang after 12

                    setTimeout(() => {
                        startAbsenProcess(status);
                    }, 1000);
                } else {
                    showStatus('QR Code tidak valid untuk karyawan ini', 'error');
                    // Restart scanner
                    setTimeout(() => {
                        initQRScanner();
                    }, 2000);
                }
            } catch (error) {
                showStatus('QR Code tidak valid', 'error');
                // Restart scanner
                setTimeout(() => {
                    initQRScanner();
                }, 2000);
            }
        }

        // QR Scan Failure
        function onScanFailure(error) {
            // Handle scan failure silently
            console.log(`QR scan failure: ${error}`);
        }

        // Manual absen function
        function manualAbsen(status) {
            currentStatus = status;
            startAbsenProcess(status);
        }

        // Start absen process
        function startAbsenProcess(status) {
            currentStatus = status;

            // Disable buttons
            document.querySelectorAll('.btn-absen').forEach(btn => btn.disabled = true);

            // Show camera for photo capture
            startCamera();
        }

        // Start camera
        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: {
                            ideal: 1280
                        },
                        height: {
                            ideal: 720
                        }
                    }
                });

                const video = document.getElementById('video');
                video.srcObject = stream;

                document.getElementById('cameraContainer').style.display = 'block';

                // Auto capture after 3 seconds
                setTimeout(() => {
                    capturePhoto();
                }, 3000);

            } catch (error) {
                console.error('Error accessing camera:', error);
                showStatus('Tidak dapat mengakses kamera. Silakan izinkan akses kamera.', 'error');
                enableButtons();
            }
        }

        // Capture photo
        function capturePhoto() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0);

            // Stop camera
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }

            document.getElementById('cameraContainer').style.display = 'none';
            document.getElementById('canvasContainer').style.display = 'block';

            // Process absen
            processAbsen();
        }

        // Process absen
        async function processAbsen() {
            const canvas = document.getElementById('canvas');
            const imageData = canvas.toDataURL('image/png');

            // Get location
            let location = '';
            if (navigator.geolocation) {
                try {
                    const position = await getCurrentPosition();
                    location = `${position.coords.latitude},${position.coords.longitude}`;
                } catch (error) {
                    console.error('Error getting location:', error);
                    location = '0,0'; // Default location
                }
            } else {
                location = '0,0'; // Default location
            }

            // Get cabang location from database
            const cabangLocation = '{{ $cabang->lokasi_cabang ?? '0,0' }}';

            // Show loading
            document.getElementById('loading').style.display = 'block';

            // Send data to server
            try {
                const response = await fetch('{{ route('qrpresensi.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nik: karyawan.nik,
                        status: currentStatus,
                        image: imageData,
                        lokasi: location,
                        lokasi_cabang: cabangLocation,
                        kode_jam_kerja: '{{ $karyawan->kode_jadwal ?? '0001' }}' // Default jam kerja
                    })
                });

                const result = await response.json();

                if (result.status) {
                    showStatus(result.message, 'success');
                    playNotificationSound('success');
                } else {
                    showStatus(result.message, 'error');
                    playNotificationSound('error');
                }

            } catch (error) {
                console.error('Error sending data:', error);
                showStatus('Terjadi kesalahan saat mengirim data', 'error');
            }

            // Hide loading and enable buttons
            document.getElementById('loading').style.display = 'none';
            enableButtons();

            // Hide canvas after 3 seconds
            setTimeout(() => {
                document.getElementById('canvasContainer').style.display = 'none';
            }, 3000);
        }

        // Get current position
        function getCurrentPosition() {
            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                });
            });
        }

        // Show status message
        function showStatus(message, type) {
            const statusElement = document.getElementById('statusMessage');
            statusElement.textContent = message;
            statusElement.className = `status-message status-${type}`;
            statusElement.style.display = 'block';

            // Hide after 5 seconds
            setTimeout(() => {
                statusElement.style.display = 'none';
            }, 5000);
        }

        // Enable buttons
        function enableButtons() {
            document.querySelectorAll('.btn-absen').forEach(btn => btn.disabled = false);
        }

        // Play notification sound
        function playNotificationSound(type) {
            const audio = new Audio();
            if (type === 'success') {
                audio.src = '{{ asset('assets/sound/absenmasuk.wav') }}';
            } else {
                audio.src = '{{ asset('assets/sound/akhirabsen.wav') }}';
            }
            audio.play().catch(e => console.log('Audio play failed:', e));
        }

        // Initialize QR Scanner when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Request camera permission immediately
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(function(stream) {
                    // Camera permission granted, initialize scanner
                    initQRScanner();
                    stream.getTracks().forEach(track => track.stop()); // Stop the test stream
                })
                .catch(function(err) {
                    console.log('Camera permission denied, but continuing...');
                    initQRScanner();
                });
        });
    </script>
</body>

</html>
