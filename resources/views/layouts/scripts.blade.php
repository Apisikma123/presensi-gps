 <!-- Core JS -->
 <script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/popper/popper.js') }}"></script>
 <script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/hammer/hammer.js') }}"></script>
 <script src="{{ asset('/assets/vendor/js/menu.js') }}"></script>
 <script src="{{ asset('assets/vendor/js/jquery.maskMoney.js') }}"></script>

 <!-- Essential Vendors JS -->
 <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
 <script src="{{ asset('assets/vendor/js/toastr.min.js') }}"></script>
 <script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>
 <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
 <script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
 <script src="{{ asset('assets/js/ui-popover.js') }}"></script>

 <script>
      // Ensure jQuery.fn.flatpickr is universally defined for all Blade scripts and plugins
    if (typeof window.flatpickr !== 'undefined' && window.jQuery && !window.jQuery.fn.flatpickr) {
        window.jQuery.fn.flatpickr = function(config) {
            return this.each(function() {
                if (this._flatpickr) {
                    try { this._flatpickr.destroy(); } catch(e) {}
                }
                var finalConfig = config || {};
                var modal = this.closest('.modal');
                if (modal && !finalConfig.appendTo) {
                    finalConfig.appendTo = modal;
                }
                var group = this.closest('.input-group');
                if (group && !finalConfig.ignoredFocusElements) {
                    finalConfig.ignoredFocusElements = [group];
                }
                window.flatpickr(this, finalConfig);
            });
        };
    }

    // Self-healing helper: guarantees an active, working flatpickr instance
    function ensureFlatpickrInstance(input) {
        if (!input || typeof window.flatpickr === 'undefined') return null;

        // If already initialized and its calendarContainer is still intact in DOM, return it
        if (input._flatpickr) {
            var container = input._flatpickr.calendarContainer;
            if (container && document.body.contains(container)) {
                return input._flatpickr;
            }
            try { input._flatpickr.destroy(); } catch(e) {}
        }

        var config = {
            dateFormat: 'Y-m-d',
            monthSelectorType: 'static',
            allowInput: true,
            static: false
        };

        var modal = input.closest ? input.closest('.modal') : null;
        if (modal) {
            config.appendTo = modal;
        }

        var group = input.closest ? input.closest('.input-group') : null;
        if (group) {
            config.ignoredFocusElements = [group];
        }

        try {
            window.flatpickr(input, config);
        } catch(e) {
            console.warn('Flatpickr init warning:', e);
        }

        return input._flatpickr;
    }

    function initGlobalFlatpickr(context) {
        if (typeof window.flatpickr === 'undefined') return;

        var selector = '.flatpickr-date, [datepicker="flatpickr-date"]';
        var target = context 
            ? (typeof context === 'string' ? $(context).find(selector) : $(context).is(selector) ? $(context) : $(context).find(selector))
            : $(selector);

        target.each(function() {
            ensureFlatpickrInstance(this);
        });
    }

    // Permanent capture-phase event listeners (immune to SPA route unmounts and jQuery off())
    (function() {
        function getTargetInput(target) {
            if (!target || !target.closest) return null;
            var group = target.closest('.input-group');
            if (group) {
                var input = group.querySelector('.flatpickr-date, [datepicker="flatpickr-date"], .flatpickr-input');
                if (input) return { input: input, isIcon: !!target.closest('.input-group-text') };
            }
            if (target.matches && target.matches('.flatpickr-date, [datepicker="flatpickr-date"], .flatpickr-input')) {
                return { input: target, isIcon: false };
            }
            return null;
        }

        document.addEventListener('click', function(e) {
            var info = getTargetInput(e.target);
            if (!info) return;

            var fp = ensureFlatpickrInstance(info.input);
            if (!fp) return;

            if (info.isIcon) {
                e.preventDefault();
                e.stopPropagation();
                if (fp.isOpen) {
                    fp.close();
                } else {
                    fp.open();
                    try { info.input.focus(); } catch(err) {}
                }
            } else {
                if (!fp.isOpen) {
                    fp.open();
                }
            }
        }, true); // CAPTURE phase

        document.addEventListener('focusin', function(e) {
            if (!e.target || !e.target.matches) return;
            if (e.target.matches('.flatpickr-date, [datepicker="flatpickr-date"]')) {
                var fp = ensureFlatpickrInstance(e.target);
                if (fp && !fp.isOpen) {
                    fp.open();
                }
            }
        }, true);
    })();

    $(function() {
        initGlobalFlatpickr();

        // Clean modal flatpickr instances when modal closes
        $(document).on('hidden.bs.modal', '.modal', function() {
            $(this).find('.flatpickr-date, [datepicker="flatpickr-date"], .flatpickr-input').each(function() {
                if (this._flatpickr) {
                    try { this._flatpickr.destroy(); } catch(e) {}
                }
            });
        });

        // Re-init flatpickr when modal is shown
        $(document).on('shown.bs.modal', '.modal', function() {
            initGlobalFlatpickr(this);
        });
    });
 </script>
 <!-- Main JS -->
 <script>
     $(document).on('click', '.delete-confirm', function(event) {
         var form = $(this).closest("form");
         var name = $(this).data("name");
         event.preventDefault();
         Swal.fire({
             title: "Hapus Data Terpilih?",
             text: "Data yang sudah dihapus tidak dapat dikembalikan lagi. Lanjutkan penghapusan?",
             icon: "warning",
             showCancelButton: true,
             confirmButtonColor: "#DC2626",
             cancelButtonColor: "#64748B",
             confirmButtonText: "Ya, Hapus Data",
             cancelButtonText: "Batal",
             reverseButtons: true
         }).then((result) => {
             if (result.isConfirmed) {
                 form.submit();
             }
         });
     });

     $(document).on('click', '.cancel-confirm', function(event) {
         var form = $(this).closest("form");
         var name = $(this).data("name");
         event.preventDefault();
         Swal.fire({
             title: "Batalkan Persetujuan?",
             text: "Status persetujuan data ini akan dibatalkan dan dikembalikan ke status sebelumnya.",
             icon: "warning",
             showCancelButton: true,
             confirmButtonColor: "#DC2626",
             cancelButtonColor: "#64748B",
             confirmButtonText: "Ya, Batalkan",
             cancelButtonText: "Kembali",
             reverseButtons: true
         }).then((result) => {
             /* Read more about isConfirmed, isDenied below */
             if (result.isConfirmed) {
                 form.submit();
             }
         });
     });
 </script>

 <script>
     $(".money").maskMoney();
 </script>

 <script>
     $(document).on('show.bs.modal', '.modal', function() {
         const zIndex = 1090 + 10 * $('.modal:visible').length;
         $(this).css('z-index', zIndex);
         setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1)
             .addClass('modal-stack'));
     });
 </script>

 <script>
    // Global auto-containment: Wrap any tables lacking .table-responsive so they scroll cleanly on mobile
    function initGlobalResponsiveTables() {
        document.querySelectorAll('table').forEach(function(tbl) {
            if (tbl.closest('.flatpickr-calendar') || tbl.closest('.air-datepicker')) return;
            if (!tbl.closest('.table-responsive')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive w-100 max-w-full';
                tbl.parentNode.insertBefore(wrapper, tbl);
                wrapper.appendChild(tbl);
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGlobalResponsiveTables);
    } else {
        initGlobalResponsiveTables();
    }
</script>

<script src="{{ asset('/assets/js/main.js') }}"></script>

@if ($message = Session::get('success'))
    <script>
        toastr.options.showEasing = 'swing';
        toastr.options.hideEasing = 'linear';
        toastr.options.progressBar = true;
        toastr.success("Berhasil", "{{ $message }}", {
            timeOut: 3000
        });
    </script>
@endif

@if ($message = Session::get('error'))
    <script>
        toastr.options.showEasing = 'swing';
        toastr.options.hideEasing = 'linear';
        toastr.options.progressBar = true;
        toastr.error("Gagal", "{{ $message }}", {
            timeOut: 3000
        });
    </script>
@endif

@if ($message = Session::get('warning'))
    <script>
        toastr.options.showEasing = 'swing';
        toastr.options.hideEasing = 'linear';
        toastr.options.progressBar = true;
        toastr.warning("Warning", "{{ $message }}", {
            timeOut: 3000
        });
    </script>
@endif

@if (isset($errors) && $errors->any())
    @php
        $err = '';
    @endphp
    @foreach ($errors->all() as $error)
        @php
            $err .= $error . ' ';
        @endphp
    @endforeach
    <script>
        toastr.options.showEasing = 'swing';
        toastr.options.hideEasing = 'linear';
        toastr.options.progressBar = true;
        toastr.error(" Gagal", "{{ addslashes(trim($err)) }}", {
            timeOut: 3000
        });
    </script>
@endif

@stack('myscript')
