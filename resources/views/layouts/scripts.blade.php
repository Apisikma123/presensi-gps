<!-- Core JS -->
<script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('/assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/menu.js') }}"></script>

<!-- Essential Vendors JS -->
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('assets/vendor/js/toastr.min.js') }}"></script>
<script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>
<script src="{{ asset('assets/external/js/cropper.min.js') }}"></script>

<!-- Custom App Logic & Anti-Slop Enhancements (Browser Cached) -->
<script src="{{ asset('assets/js/app-custom.js') }}?v={{ config('app.asset_version', '2.5.0') }}"></script>

<!-- Theme Main JS -->
<script src="{{ asset('/assets/js/main.js') }}?v={{ config('app.asset_version', '2.5.0') }}"></script>

<!-- Dynamic Session Flash Notifications -->
@if ($message = Session::get('success'))
    <script>
        (function() {
            var fullMsg = @json($message);
            if (fullMsg && fullMsg.indexOf('Password sementara:') !== -1) {
                if (typeof showTempPasswordPopup === 'function' && showTempPasswordPopup(fullMsg)) {
                    return;
                }
            }
            if (typeof Swal !== 'undefined') {
                var themePrimary = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '#3C2A21';
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: fullMsg,
                    confirmButtonColor: themePrimary,
                    confirmButtonText: 'Selesai',
                    timer: 3500,
                    timerProgressBar: true
                });
            }
        })();
    </script>
@endif

@if ($message = Session::get('error'))
    <script>
        if (typeof Swal !== 'undefined') {
            var themePrimary = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '#3C2A21';
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memproses Permintaan',
                text: @json($message),
                confirmButtonColor: themePrimary,
                confirmButtonText: 'Tutup'
            });
        }
    </script>
@endif

@if ($message = Session::get('warning'))
    <script>
        if (typeof Swal !== 'undefined') {
            var themePrimary = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '#3C2A21';
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: @json($message),
                confirmButtonColor: themePrimary,
                confirmButtonText: 'Mengerti'
            });
        }
    </script>
@endif

@if (isset($errors) && $errors->any())
    <script>
        if (typeof Swal !== 'undefined') {
            var themePrimary = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '#3C2A21';
            Swal.fire({
                icon: 'error',
                title: 'Terdapat Kesalahan Input',
                html: '<div class="text-start small text-muted"><ul class="mb-0 ps-3">' +
                    @json($errors->all()).map(function(e) { return '<li>' + e + '</li>'; }).join('') +
                    '</ul></div>',
                confirmButtonColor: themePrimary,
                confirmButtonText: 'Perbaiki'
            });
        }
    </script>
@endif

<div id="app-page-scripts" style="display: none;">
@stack('myscript')
</div>
