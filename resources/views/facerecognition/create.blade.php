<style>
    /* Responsive Camera Modal Container */
    .face-capture-wrapper {
        position: relative;
        width: 100%;
        max-width: 540px;
        margin: 0 auto;
        border-radius: 14px;
        overflow: hidden;
        background: #0F172A;
        border: 1px solid #1E293B;
    }

    .camera-container {
        position: relative;
        width: 100%;
        height: 380px;
        max-height: 55vh;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #000;
    }

    #webcam-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scaleX(-1); /* Mirror effect */
    }

    /* UI Overlays */
    .overlay-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 16px;
    }

    /* Face Frame Guide */
    .face-frame {
        position: absolute;
        top: 48%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 190px;
        height: 250px;
        border: 2px dashed rgba(255, 255, 255, 0.4);
        border-radius: 120px;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.65);
        transition: all 0.25s ease;
    }

    .face-frame.active {
        border: 3px solid #10B981;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.75), 0 0 25px rgba(16, 185, 129, 0.4);
    }

    .face-frame.scanning::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 120px;
        box-shadow: inset 0 0 15px #10B981;
        animation: pulse-green 1.2s infinite;
    }

    @keyframes pulse-green {
        0% { opacity: 0.2; }
        50% { opacity: 0.7; }
        100% { opacity: 0.2; }
    }

    /* Status & Instructions */
    .status-badge {
        align-self: center;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: 20px;
        color: #fff;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid rgba(255, 255, 255, 0.15);
        text-align: center;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .status-dot {
        width: 7px;
        height: 7px;
        background: #ef4444;
        border-radius: 50%;
        display: inline-block;
    }
    .status-dot.ready { background: #10b981; box-shadow: 0 0 8px #10b981; }

    /* Scanning Progress */
    .scan-progress-container {
        position: absolute;
        bottom: 15px;
        left: 50%;
        transform: translateX(-50%);
        width: 180px;
        text-align: center;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .scan-progress-container.show { opacity: 1; }

    .progress-bar-wrapper {
        background: rgba(255,255,255,0.25);
        height: 5px;
        border-radius: 3px;
        width: 100%;
        overflow: hidden;
        margin-top: 4px;
    }
    .progress-bar-fill {
        background: #10b981;
        height: 100%;
        width: 0%;
        transition: width 0.2s linear;
    }
    .scan-text {
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Action Buttons */
    .action-area {
        position: absolute;
        bottom: 18px;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: center;
        pointer-events: auto;
    }

    .btn-modern-start {
        background: #1E4D3E;
        color: #FFFFFF;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 10px 24px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 13.5px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .btn-modern-start:hover { background: #163B30; }
    .btn-modern-start:active { transform: scale(0.96); }

    .loading-spinner {
        display: none;
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Success State */
    .success-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.95);
        z-index: 20;
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        pointer-events: auto;
        border-radius: 14px;
    }
    .success-icon {
        font-size: 56px;
        color: #10b981;
        margin-bottom: 12px;
    }

    /* Warning Toast */
    .warning-toast {
        position: absolute;
        top: 12px;
        left: 50%;
        transform: translateX(-50%) translateY(-50px);
        background: rgba(220, 38, 38, 0.9);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        transition: transform 0.25s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 100;
    }
    .warning-toast.show { transform: translateX(-50%) translateY(0); }
</style>

<div class="face-capture-wrapper">
    <!-- Camera View -->
    <div class="camera-container">
        <video id="webcam-video" autoplay playsinline muted></video>
    </div>

    <!-- Interface Overlays -->
    <div class="overlay-container">
        <!-- Top Status -->
        <div class="status-badge">
            <span class="status-dot" id="statusDot"></span>
            <span id="statusText">Menghubungkan kamera...</span>
        </div>

        <!-- Warning Toast -->
        <div class="warning-toast" id="warningToast">
            <i class="ti ti-alert-circle"></i>
            <span id="warningMessage">Peringatan</span>
        </div>

        <!-- Center Face Frame -->
        <div class="face-frame" id="faceFrame">
            <!-- Progress shown below frame -->
            <div class="scan-progress-container" id="scanProgress">
                <div class="scan-text">Merekam Wajah...</div>
                <div class="progress-bar-wrapper">
                    <div class="progress-bar-fill" id="progressBarFill"></div>
                </div>
                <div style="font-size: 11px; color: rgba(255,255,255,0.7); margin-top: 3px;">Tahan posisi... <span id="progressPercent">0%</span></div>
            </div>
        </div>

        <!-- Bottom Actions -->
        <div class="action-area" id="actionArea">
            <button class="btn-modern-start" id="btnStart" onclick="startScanning()">
                <div class="loading-spinner" id="btnSpinner"></div>
                <i class="ti ti-camera" id="btnIcon"></i>
                <span id="btnText">Mulai Ambil Sample Wajah</span>
            </button>
        </div>
    </div>

    <!-- Success Screen -->
    <div class="success-overlay" id="successScreen">
        <i class="ti ti-circle-check-filled success-icon"></i>
        <h5 class="mb-1 text-white fw-bold">Perekaman Berhasil!</h5>
        <p class="text-white-50 text-center px-3 mb-0" style="font-size: 12.5px;">Dataset wajah berhasil disimpan ke AI engine.<br>Memperbarui halaman...</p>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<!-- Assuming jQuery is already loaded by the admin layout. If not, uncomment next line -->
<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->

<script>
    // Configuration
    const TOTAL_IMAGES_NEEDED = 5;
    const CAPTURE_INTERVAL = 300; // ms between captures
    const CONFIDENCE_THRESHOLD = 0.5;
    
    // State
    let modelLoaded = false;
    let isScanning = false;
    let imagesCaptured = [];
    let videoEl = document.getElementById('webcam-video');
    let stream = null;

    // UI Elements
    const statusText = document.getElementById('statusText');
    const statusDot = document.getElementById('statusDot');
    const faceFrame = document.getElementById('faceFrame');
    const warningToast = document.getElementById('warningToast');
    const btnStart = document.getElementById('btnStart');
    const actionArea = document.getElementById('actionArea');
    const scanProgress = document.getElementById('scanProgress');
    const progressBarFill = document.getElementById('progressBarFill');
    const progressPercent = document.getElementById('progressPercent');

    // Initialize
    // Using simple immediate execution or checking readiness
    (async function init() {
        await startCamera();
        await loadModels();
    })();

    // 1. Start Camera
    async function startCamera() {
        try {
            // Constraints for optimal face/portrait mode
            const constraints = {
                video: {
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };
            
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            videoEl.srcObject = stream;
            
            // Wait for video to play to confirm dimensions
            await new Promise(resolve => videoEl.onloadedmetadata = resolve);
            videoEl.play();
            
            updateStatus('ready', 'Kamera Siap. Klik Mulai.');
            btnStart.disabled = false;
            
        } catch (err) {
            console.error("Camera Error:", err);
            updateStatus('error', 'Gagal akses kamera. Izinkan akses.');
            showWarning('Gagal mengakses kamera.', true);
        }
    }

    // 2. Load Face API Models
    async function loadModels() {
        updateStatus('loading', 'Memuat model AI...');
        btnStart.disabled = true;
        
        try {
            await faceapi.nets.tinyFaceDetector.loadFromUri('{{ asset("models") }}');
            // Optional: load landmarks if we want strict checks, but tinyDetector is enough for simple presence
            // await faceapi.nets.faceLandmark68Net.loadFromUri('/models'); 
            
            modelLoaded = true;
            updateStatus('ready', 'Kamera Siap. Klik tombol Mulai.');
            btnStart.disabled = false;
            console.log("Models loaded");
        } catch (err) {
            console.error("Model Load Error:", err);
            updateStatus('error', 'Gagal memuat model AI.');
        }
    }

    // 3. User Clicks Start
    async function startScanning() {
        if (!modelLoaded || !stream) return;
        
        isScanning = true;
        imagesCaptured = []; // Reset
        
        // UI Updates
        btnStart.style.display = 'none'; // Hide button to declutter
        faceFrame.classList.add('scanning');
        scanProgress.classList.add('show');
        updateProgress(0);
        
        updateStatus('loading', 'Mencari wajah...');
        
        // Start Detection Loop
        detectLoop();
    }

    // 4. Detection Loop
    async function detectLoop() {
        if (!isScanning) return;

        // Detect face using TinyFaceDetector (fastest)
        // We use inputSize 224 or 320 for speed on mobile
        const detection = await faceapi.detectSingleFace(videoEl, new faceapi.TinyFaceDetectorOptions({ inputSize: 320 }));

        if (detection && detection.score > CONFIDENCE_THRESHOLD) {
            // Face detected!
            const box = detection.box;
            
            // Simple centering check (optional, but good UX)
            // We define a "safe zone" in the center
            const videoWidth = videoEl.videoWidth;
            const videoHeight = videoEl.videoHeight;
            const centerX = box.x + (box.width / 2);
            const centerY = box.y + (box.height / 2);
            
            // Check if face is roughly centered (within middle 60%)
            const isCentered = (centerX > videoWidth * 0.2 && centerX < videoWidth * 0.8) &&
                               (centerY > videoHeight * 0.2 && centerY < videoHeight * 0.8);
            
            // Check if face is big enough
            const isCloseEnough = box.width > videoWidth * 0.15; // Face width > 15% of screen

            if (isCentered && isCloseEnough) {
                faceFrame.classList.add('active'); // Green border
                updateStatus('success', 'Wajah terdeteksi. Tahan...');
                hideWarning();
                
                // Capture Frame!
                await captureFrame();
                
            } else {
                faceFrame.classList.remove('active');
                if (!isCloseEnough) {
                    updateStatus('warning', 'Mendekat ke kamera');
                } else {
                    updateStatus('warning', 'Posisikan wajah di tengah');
                }
            }
        } else {
            faceFrame.classList.remove('active');
            updateStatus('loading', 'Wajah tidak terdeteksi...');
        }

        // Continue loop if not done
        if (imagesCaptured.length < TOTAL_IMAGES_NEEDED) {
            requestAnimationFrame(detectLoop);
        } else {
            finishScanning();
        }
    }

    // 5. Capture Frame Logic
    let lastCaptureTime = 0;
    async function captureFrame() {
        const now = Date.now();
        if (now - lastCaptureTime < CAPTURE_INTERVAL) return; // Debounce
        
        lastCaptureTime = now;
        
        // Draw video frame to canvas
        const canvas = document.createElement('canvas');
        canvas.width = videoEl.videoWidth;
        canvas.height = videoEl.videoHeight;
        const ctx = canvas.getContext('2d');
        
        // Mirror flip if using front camera usually mirrors, but we want the raw image?
        // Actually, for recognition, standard orientation is best. 
        // The video preview is css mirrored. We draw raw.
        ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);
        
        // Convert to base64
        const dataUrl = canvas.toDataURL('image/jpeg', 0.8); // 80% quality
        
        imagesCaptured.push(dataUrl);
        console.log(`Captured ${imagesCaptured.length}/${TOTAL_IMAGES_NEEDED}`);
        
        // Update Progress UI
        const pct = Math.round((imagesCaptured.length / TOTAL_IMAGES_NEEDED) * 100);
        updateProgress(pct);
    }

    function updateProgress(percent) {
        progressBarFill.style.width = percent + '%';
        progressPercent.innerText = percent + '%';
    }

    // 6. Finish & Upload
    async function finishScanning() {
        isScanning = false;
        faceFrame.classList.remove('scanning');
        faceFrame.classList.add('active'); // Stay green
        
        updateStatus('success', 'Perekaman Selesai! Mengunggah...');
        scanProgress.classList.remove('show');
        
        // Show Spinner on Start Button (if we wanted to reuse it, but we hid it)
        // Let's create an upload form data
        
        try {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('nik', '{{ isset($nik) ? $nik : "" }}');
            
            // Convert captured base64 images to Blobs and append
            for (let i = 0; i < imagesCaptured.length; i++) {
                const blob = await (await fetch(imagesCaptured[i])).blob();
                formData.append('files[]', blob, `capture_${i+1}.jpg`);
            }
            
            // Add dummy metadata to satisfy backend requirement structure
            // Backend expects metadata json with direction keys
            const metadata = imagesCaptured.map(() => ({ direction: 'front' }));
            formData.append('metadata', JSON.stringify(metadata));

            // Send AJAX
            updateStatus('loading', 'Mengeirim data ke server...');
            
            $.ajax({
                type: 'POST',
                url: '{{ route("facerecognition.store") }}',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showSuccessScreen();
                    } else {
                        handleUploadError(response.message);
                    }
                },
                error: function(err) {
                    console.error("Upload Error", err);
                    handleUploadError('Terjadi kesalahan koneksi.');
                }
            });

        } catch (err) {
            console.error("Processing Error", err);
            handleUploadError('Gagal memproses gambar.');
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }

    // Stop camera when modal is closed
    $('#modal').on('hidden.bs.modal', function() {
        stopCamera();
    });

    function showSuccessScreen() {
        stopCamera();
        const successScreen = document.getElementById('successScreen');
        successScreen.style.display = 'flex';
        setTimeout(() => {
            window.location.href = window.location.pathname + '#wajah';
            window.location.reload();
        }, 1500);
    }

    function handleUploadError(msg) {
        alert("Error: " + msg);
        // Reset to allow retry
        isScanning = false;
        imagesCaptured = [];
        btnStart.style.display = 'flex';
        faceFrame.classList.remove('active', 'scanning');
        updateProgress(0);
        updateStatus('error', 'Gagal. Silakan coba lagi.');
    }

    // Helpers
    function updateStatus(type, text) {
        statusText.innerText = text;
        statusDot.className = 'status-dot'; // reset
        if (type === 'ready' || type === 'success') statusDot.classList.add('ready');
        if (type === 'loading') statusDot.style.background = '#fbbf24'; // yellow
        if (type === 'error') statusDot.style.background = '#ef4444'; // red
    }

    let warningTimeout;
    function showWarning(msg) {
        const warningEl = document.getElementById('warningMessage');
        warningEl.innerText = msg;
        warningToast.classList.add('show');
        
        clearTimeout(warningTimeout);
        warningTimeout = setTimeout(() => {
            warningToast.classList.remove('show');
        }, 3000);
    }
    
    function hideWarning() {
        warningToast.classList.remove('show');
    }

</script>
