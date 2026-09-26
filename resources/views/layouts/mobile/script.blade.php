<!-- ///////////// Js Files ////////////////////  -->
<!-- Jquery - Required early, tidak bisa defer -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap - Required after jQuery -->
<script src="{{ asset('assets/template/js/lib/popper.min.js') }}"></script>
<script src="{{ asset('assets/template/js/lib/bootstrap.min.js') }}"></script>
<!-- Ionicons - Module/nomodule pattern -->
<script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js" defer></script>
<!-- jQuery Circle Progress - jQuery dependent -->
<script src="{{ asset('assets/template/js/plugins/jquery-circle-progress/circle-progress.min.js') }}"></script>
<!-- Base Js File - Required untuk layout -->
<script src="{{ asset('assets/template/js/base.js') }}"></script>
<!-- SweetAlert2 Lokal -->
<script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>
<!-- MaskMoney - jQuery dependent -->
<script src="{{ asset('assets/template/js/maskMoney.js') }}" defer></script>
{{-- <script src="{{ asset('assets/vendor/face-api.min.js') }}"></script> --}}

@php
    $themePrimary = \App\Services\ThemeResolver::resolve()['primary'] ?? '#3C2A21';
@endphp

<script>
    // Universal GlobalSwal Helper for Mobile
    window.GlobalSwal = {
        toast: function(icon, message, title) {
            Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: function(toast) {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            }).fire({
                icon: icon || 'success',
                title: title ? (title + ': ' + message) : message
            });
        },
        success: function(message, title) {
            Swal.fire({
                icon: 'success',
                title: title || 'Berhasil!',
                text: message,
                confirmButtonColor: "{{ $themePrimary }}",
                confirmButtonText: 'Selesai',
                timer: 2500,
                timerProgressBar: true
            });
        },
        error: function(message, title) {
            Swal.fire({
                icon: 'error',
                title: title || 'Gagal',
                html: message,
                confirmButtonColor: "{{ $themePrimary }}",
                confirmButtonText: 'Tutup'
            });
        },
        warning: function(message, title) {
            Swal.fire({
                icon: 'warning',
                title: title || 'Peringatan',
                text: message,
                confirmButtonColor: "{{ $themePrimary }}",
                confirmButtonText: 'Mengerti'
            });
        }
    };

    // Universal Toastr Bridge
    window.toastr = {
        options: {},
        success: function(msg, title) { window.GlobalSwal.toast('success', msg, title); },
        error: function(msg, title) { window.GlobalSwal.error(msg, title); },
        warning: function(msg, title) { window.GlobalSwal.toast('warning', msg, title); },
        info: function(msg, title) { window.GlobalSwal.toast('info', msg, title); }
    };
</script>

@if ($message = Session::get('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.GlobalSwal.success(@json($message));
        });
    </script>
@endif

@if ($message = Session::get('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.GlobalSwal.error(@json($message));
        });
    </script>
@endif

@if ($message = Session::get('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.GlobalSwal.warning(@json($message));
        });
    </script>
@endif

@if (isset($errors) && $errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.GlobalSwal.warning(@json(implode("\n", $errors->all())), 'Periksa Formulir');
        });
    </script>
@endif
<script>
    $('.cancel-confirm').click(function(event) {
        var form = $(this).closest("form");
        var name = $(this).data("name");
        event.preventDefault();
        Swal.fire({
            title: `Apakah Anda Yakin Ingin Membatalkan Data Ini ?`,
            text: "Data ini akan dibatalkan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "{{ $themePrimary }}",
            cancelButtonColor: "#64748B",
            confirmButtonText: "Ya, Batalkan!",
            cancelButtonText: "Kembali"
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $(document).on('submit', 'form.form-confirm', function(event) {
        var form = $(this);
        if (form.data('swal-approved') === true) {
            form.removeData('swal-approved');
            return true;
        }
        event.preventDefault();
        var message = form.data('message') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        var title = form.data('title') || 'Konfirmasi Tindakan';
        var isDanger = form.hasClass('form-danger') || /hapus|delete|tolak|batal/i.test(message);

        Swal.fire({
            title: title,
            text: message,
            icon: isDanger ? "warning" : "question",
            showCancelButton: true,
            confirmButtonColor: isDanger ? "#DC2626" : "{{ $themePrimary }}",
            cancelButtonColor: "#64748B",
            confirmButtonText: form.data('confirm-text') || (isDanger ? "Ya, Lanjutkan" : "Ya, Konfirmasi"),
            cancelButtonText: "Batal",
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                form.data('swal-approved', true);
                form.submit();
            }
        });
    });

    $(document).on('click', '.btn-confirm-action, [data-confirm]', function(event) {
        event.preventDefault();
        var btn = $(this);
        var form = btn.closest('form');
        var message = btn.data('confirm') || btn.data('message') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        var title = btn.data('title') || 'Konfirmasi Tindakan';
        var isDanger = btn.data('destructive') || btn.hasClass('btn-danger') || btn.hasClass('text-danger') || /hapus|delete|tolak|batal/i.test(message);

        Swal.fire({
            title: title,
            text: message,
            icon: isDanger ? "warning" : "question",
            showCancelButton: true,
            confirmButtonColor: isDanger ? "#DC2626" : "{{ $themePrimary }}",
            cancelButtonColor: "#64748B",
            confirmButtonText: btn.data('confirm-text') || (isDanger ? "Ya, Lanjutkan" : "Ya, Konfirmasi"),
            cancelButtonText: "Batal",
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                if (btn.is('a') && btn.attr('href') && btn.attr('href') !== '#') {
                    window.location.href = btn.attr('href');
                } else if (form.length) {
                    form.submit();
                }
            }
        });
    });

    function sanitizeNativeConfirmsMobile() {
        document.querySelectorAll('form[onsubmit*="confirm("]').forEach(function(form) {
            var rawOnsubmit = form.getAttribute('onsubmit');
            var match = rawOnsubmit ? rawOnsubmit.match(/confirm\s*\(\s*(['"`])(.*?)\1\s*\)/) : null;
            if (match && match[2]) {
                var confirmText = match[2];
                form.removeAttribute('onsubmit');
                form.setAttribute('data-message', confirmText);
                form.classList.add('form-confirm');
            }
        });

        document.querySelectorAll('button[onclick*="confirm("], a[onclick*="confirm("]').forEach(function(el) {
            var rawOnclick = el.getAttribute('onclick');
            var match = rawOnclick ? rawOnclick.match(/confirm\s*\(\s*(['"`])(.*?)\1\s*\)/) : null;
            if (match && match[2]) {
                var confirmText = match[2];
                el.removeAttribute('onclick');
                el.setAttribute('data-message', confirmText);
                el.classList.add('btn-confirm-action');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', sanitizeNativeConfirmsMobile);
    if (window.MutationObserver) {
        new MutationObserver(function() { sanitizeNativeConfirmsMobile(); }).observe(document.body, { childList: true, subtree: true });
    }
</script>
<script>
    $(document).ready(function() {

        // function adjustZoom() {
        //     var width = $(window).width(); // Ambil lebar layar
        //     //alert(width);
        //     // $('body').css('zoom', '120%');
        //     if (width <= 400) { // Misalnya untuk layar kecil (mobile)
        //         $('body').css('zoom', '88%'); // Zoom out ke 80%
        //     } else if (width <= 768) { // Untuk tablet kecil
        //         $('body').css('zoom', '90%');
        //     } else {
        //         $('body').css('zoom', '100%'); // Normal zoom
        //     }
        // }

        // adjustZoom(); // Panggil saat halaman dimuat

        // $(window).resize(function() {
        //     adjustZoom(); // Panggil lagi saat ukuran layar berubah
        // });
    });
</script>

@stack('myscript')
