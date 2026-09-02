<footer class="content-footer footer bg-footer-theme">
    <div class="container-fluid">
        <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
            <div>
                © {{ date('Y') }} <span class="fw-semibold">{{ $general_setting->nama_aplikasi ?? 'E-Presensi GPS' }}</span>. All rights reserved.
            </div>
            <div>
                Developed by <a href="https://porto-aga.vercel.app/" target="_blank" rel="noopener noreferrer" class="fw-semibold" style="color: var(--theme-color-1, #32745e); text-decoration: underline;">Muhammad Aga Putra</a>
            </div>
        </div>
    </div>
</footer>
