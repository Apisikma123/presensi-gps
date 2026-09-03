<footer class="content-footer footer bg-footer-theme py-3 mt-4" style="border-top: 1px solid rgba(15, 23, 42, 0.06); font-size: 12.5px;">
    <div class="container-xxl">
        <div class="footer-container d-flex align-items-center justify-content-between flex-md-row flex-column gap-2 text-muted">
            <div>
                © {{ date('Y') }} <span class="fw-bold text-dark">{{ $general_setting->nama_aplikasi ?? 'E-Presensi GPS' }}</span>. All rights reserved.
            </div>
            <div>
                Developed by <a href="https://porto-aga.vercel.app/" target="_blank" rel="noopener noreferrer" class="fw-semibold text-decoration-none" style="color: var(--theme-color-1, #1E4D3E);">Muhammad Aga Putra</a>
            </div>
        </div>
    </div>
</footer>
