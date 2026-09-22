 <!-- Core JS -->
 <script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/popper/popper.js') }}"></script>
 <script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/hammer/hammer.js') }}"></script>
 <script src="{{ asset('/assets/vendor/js/menu.js') }}"></script>
 <script>
    if (typeof Menu !== 'undefined') {
        const OrigMenu = window.Menu;
        window.Menu = function(el, config, ps) {
            config = config || {};
            config.accordion = false; // Force accordion false globally
            return new OrigMenu(el, config, ps);
        };
        window.Menu.prototype = OrigMenu.prototype;
        Object.assign(window.Menu, OrigMenu);
        window.Menu.prototype._closeOther = function() {};
    }
 </script>

 <!-- Essential Vendors JS -->
 <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
 <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
 <script src="{{ asset('assets/vendor/js/toastr.min.js') }}"></script>
 <script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>
 <script src="{{ asset('assets/external/js/cropper.min.js') }}"></script>

 <script>
    // Lightweight Inlined Popover Initializer (Zero extra HTTP request)
    document.addEventListener('DOMContentLoaded', function() {
        // Instant flash notification from previous AJAX submission
        try {
            var _flash = sessionStorage.getItem('flash_success');
            if (_flash) {
                sessionStorage.removeItem('flash_success');
                if (_flash.indexOf('Password sementara:') !== -1 && typeof showTempPasswordPopup === 'function') {
                    showTempPasswordPopup(_flash);
                } else if (typeof toastr !== 'undefined') {
                    toastr.options.progressBar = true;
                    toastr.options.timeOut = 3000;
                    toastr.success(_flash, 'Berhasil');
                }
            }
        } catch(e) {}

        var popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
        if (popovers.length && typeof bootstrap !== 'undefined' && bootstrap.Popover) {
            popovers.forEach(function(el) { new bootstrap.Popover(el); });
        }

        // Universal Global Modal Stacking Fix: Guarantee all Bootstrap modals sit directly in body
        if (window.jQuery) {
            $(document).on('show.bs.modal', '.modal', function () {
                if (!$(this).parent().is('body')) {
                    $(this).appendTo('body');
                }
            });
        }
    });
 </script>

 <script>
       // Ensure jQuery.fn.flatpickr is universally defined for all Blade scripts and plugins
    if (typeof window.flatpickr !== 'undefined' && window.jQuery && !window.jQuery.fn.flatpickr) {
        window.jQuery.fn.flatpickr = function(config) {
            return this.each(function() {
                if (this._flatpickr) {
                    try { this._flatpickr.destroy(); } catch(e) {}
                }
                var finalConfig = config || {};
                var group = this.closest('.input-group');
                if (group && !finalConfig.ignoredFocusElements) {
                    finalConfig.ignoredFocusElements = [group];
                }
                window.flatpickr(this, finalConfig);
            });
        };
    }

    // Fixed Viewport Position Calculator (bulletproof inside modals, cards, or body)
    function positionFixedFlatpickr(self, customPositionElement) {
        var posEl = customPositionElement || self._positionElement || self._input;
        if (!posEl || !self.calendarContainer) return;
        var rect = posEl.getBoundingClientRect();
        var cal = self.calendarContainer;
        var calHeight = cal.offsetHeight || 160;
        var calWidth = cal.offsetWidth || 280;

        var top = rect.bottom + 4;
        var left = rect.left;

        if (top + calHeight > window.innerHeight && rect.top > calHeight) {
            top = Math.max(10, rect.top - calHeight - 4);
        }
        if (left + calWidth > window.innerWidth) {
            left = Math.max(10, window.innerWidth - calWidth - 16);
        }

        cal.style.position = 'fixed';
        cal.style.top = Math.round(top) + 'px';
        cal.style.left = Math.round(left) + 'px';
        cal.style.right = 'auto';
        cal.style.bottom = 'auto';
        cal.style.zIndex = '999999';
    }

    // Self-healing helper: guarantees an active, working single flatpickr instance
    function ensureFlatpickrInstance(input) {
        if (!input || typeof window.flatpickr === 'undefined') return null;
        if (input.tagName !== 'INPUT') return null;
        if (input.closest && input.closest('.flatpickr-calendar')) return null;

        // If already initialized, return existing instance directly (never duplicate)
        if (input._flatpickr) {
            return input._flatpickr;
        }

        var isTime = input.classList.contains('flatpickr-time') || 
                     input.getAttribute('datepicker') === 'flatpickr-time' || 
                     input.getAttribute('datepicker') === 'time';

        if (isTime) {
            input.readOnly = true;
            input.style.cursor = 'pointer';
        }

        var config = isTime ? {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            allowInput: false,
            clickOpens: false, // controlled explicitly on click
            minuteIncrement: 5,
            static: false,
            position: positionFixedFlatpickr,
            onChange: function(selectedDates, dateStr, instance) {
                if (instance && instance.input) {
                    instance.input.dispatchEvent(new Event('change', { bubbles: true }));
                    instance.input.dispatchEvent(new Event('input', { bubbles: true }));
                    $(instance.input).trigger('change').trigger('input');
                }
            },
            onValueUpdate: function(selectedDates, dateStr, instance) {
                if (instance && instance.input) {
                    instance.input.dispatchEvent(new Event('change', { bubbles: true }));
                    instance.input.dispatchEvent(new Event('input', { bubbles: true }));
                    $(instance.input).trigger('change').trigger('input');
                }
            },
            onClose: function(selectedDates, dateStr, instance) {
                if (instance && instance.input) {
                    instance.input.dispatchEvent(new Event('change', { bubbles: true }));
                    instance.input.dispatchEvent(new Event('input', { bubbles: true }));
                    $(instance.input).trigger('change').trigger('input');
                }
            },
            onOpen: function(selectedDates, dateStr, instance) {
                if (instance && instance.positionCalendar) {
                    instance.positionCalendar();
                }
                // Close any other open flatpickr instances cleanly without breaking display
                document.querySelectorAll('input.flatpickr-input, input[datepicker]').forEach(function(el) {
                    if (el._flatpickr && el._flatpickr !== instance && el._flatpickr.isOpen) {
                        try { el._flatpickr.close(); } catch(e) {}
                    }
                });
            }
        } : {
            dateFormat: 'Y-m-d',
            monthSelectorType: 'static',
            allowInput: true,
            static: false,
            onChange: function(selectedDates, dateStr, instance) {
                if (instance && instance.input) {
                    instance.input.dispatchEvent(new Event('change', { bubbles: true }));
                    instance.input.dispatchEvent(new Event('input', { bubbles: true }));
                    $(instance.input).trigger('change').trigger('input');
                }
            },
            onOpen: function(selectedDates, dateStr, instance) {
                document.querySelectorAll('input.flatpickr-input, input[datepicker]').forEach(function(el) {
                    if (el._flatpickr && el._flatpickr !== instance && el._flatpickr.isOpen) {
                        try { el._flatpickr.close(); } catch(e) {}
                    }
                });
            }
        };

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

        var container = context ? (typeof context === 'string' ? document.querySelector(context) : (context.jquery ? context[0] : context)) : document;
        if (!container) return;

        var selector = 'input.flatpickr-date, input[datepicker="flatpickr-date"], input.flatpickr-time, input[datepicker="flatpickr-time"], input[datepicker="time"]';
        // Fast-path: early exit if no datepicker or time inputs exist
        if (!container.querySelector(selector) && !container.querySelector('input[type="time"]')) {
            return;
        }

        var ctx = $(container);

        // Auto-upgrade any native input[type="time"] across all forms to uniform flatpickr-time
        ctx.find('input[type="time"]').each(function() {
            try { this.type = 'text'; } catch(e) {}
            this.setAttribute('datepicker', 'flatpickr-time');
            this.classList.add('flatpickr-time');
        });

        // Ensure time inputs are readonly and non-typeable
        ctx.find('input.flatpickr-time, input[datepicker="flatpickr-time"], input[datepicker="time"]').each(function() {
            this.readOnly = true;
            this.style.cursor = 'pointer';
        });

        // Strictly target INPUT elements to prevent matching flatpickr's own div.flatpickr-time container
        var target = context 
            ? (typeof context === 'string' ? $(context).find(selector) : $(context).is(selector) ? $(context) : $(context).find(selector))
            : $(selector);

        target.each(function() {
            ensureFlatpickrInstance(this);
        });
    }

    // Toggle on click of input or calendar/clock icon
    $(document).on('click', '.input-group', function(e) {
        if (e.target.closest && e.target.closest('.flatpickr-calendar')) return;
        var input = this.querySelector('input.flatpickr-time, input[datepicker="flatpickr-time"], input[datepicker="time"], input.flatpickr-date, input[datepicker="flatpickr-date"]');
        if (!input) return;
        var fp = ensureFlatpickrInstance(input);
        if (!fp) return;

        var isTime = input.classList.contains('flatpickr-time') || 
                     input.getAttribute('datepicker') === 'flatpickr-time' || 
                     input.getAttribute('datepicker') === 'time';

        var isIcon = !!e.target.closest('.input-group-text');
        var isInput = e.target === input;

        if (isTime && (isIcon || isInput)) {
            e.preventDefault();
            e.stopPropagation();
            if (fp.isOpen) {
                fp.close();
            } else {
                fp.open();
            }
        } else if (!isTime && isIcon) {
            e.preventDefault();
            e.stopPropagation();
            if (fp.isOpen) {
                fp.close();
            } else {
                fp.open();
            }
        }
    });

    $(function() {
        initGlobalFlatpickr();

        // Prevent clock picker internal clicks (arrows/steppers) from bubbling up and triggering form/modal listeners
        $(document).on('click mousedown pointerdown', '.flatpickr-calendar', function(e) {
            e.stopPropagation();
        });

        // Reposition on scroll only if a calendar is open (zero layout cost during normal scroll)
        window.addEventListener('scroll', function() {
            if (!document.querySelector('.flatpickr-calendar.open')) return;
            document.querySelectorAll('input.flatpickr-input').forEach(function(el) {
                if (el._flatpickr && el._flatpickr.isOpen && typeof el._flatpickr.positionCalendar === 'function') {
                    el._flatpickr.positionCalendar();
                }
            });
        }, { passive: true, capture: true });

        // Auto re-init when any dynamic Ajax modal content finishes loading
        $(document).ajaxComplete(function() {
            initGlobalFlatpickr();
        });

        // Clean modal flatpickr instances when modal closes
        $(document).on('hidden.bs.modal', '.modal', function() {
            $(this).find('input').each(function() {
                if (this._flatpickr) {
                    try {
                        this._flatpickr.close();
                        this._flatpickr.destroy();
                    } catch(e) {}
                    delete this._flatpickr;
                }
            });
            $('.flatpickr-calendar').remove();
        });

        // Re-init flatpickr when modal is shown
        $(document).on('shown.bs.modal', '.modal', function() {
            initGlobalFlatpickr(this);
        });

        // Self-healing page-level select2 (clean init without duplicate runtime script injection)
        if ($.fn.select2 && $('.select2').length) {
            $('.select2').each(function() {
                var $el = $(this);
                if (!$el.hasClass('select2-hidden-accessible')) {
                    $el.select2({ dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : $('body') });
                }
            });
        }
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
     // Lazy maskMoney: only load and execute if .money exists on page
     if (document.querySelector('.money')) {
         if (!$.fn.maskMoney) {
             $.getScript("{{ asset('assets/vendor/js/jquery.maskMoney.js') }}", function() {
                 $(".money").maskMoney();
             });
         } else {
             $(".money").maskMoney();
         }
     }
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
        var tables = document.querySelectorAll('table');
        if (!tables.length) return;
        tables.forEach(function(tbl) {
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

<script src="{{ asset('/assets/js/main.js') }}?v={{ file_exists(public_path('assets/js/main.js')) ? filemtime(public_path('assets/js/main.js')) : time() }}"></script>

<!-- Sidebar Navigation Safeguard -->
<script>
    (function() {
        function ensureMenuInitialized() {
            var menuEl = document.getElementById('layout-menu');
            if (!menuEl) return;
            if (!menuEl.menuInstance && typeof Menu !== 'undefined') {
                try {
                    new Menu(menuEl, {
                        orientation: 'vertical',
                        closeChildren: false,
                        accordion: false
                    });
                } catch(err) {
                    console.warn('Sidebar Menu initialization fallback:', err);
                }
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', ensureMenuInitialized);
        } else {
            ensureMenuInitialized();
        }

        // Direct fallback: if menuInstance is ever missing or detached, handle toggle smoothly
        $(document).on('click', '#layout-menu .menu-item > .menu-link.menu-toggle', function(e) {
            var menuEl = document.getElementById('layout-menu');
            if (!menuEl || !menuEl.menuInstance) {
                e.preventDefault();
                var $item = $(this).closest('.menu-item');
                $item.toggleClass('open');
            }
        });

        // Smoothly ensure bottom menu items and logout are fully visible when expanded
        $(document).on('click', '#layout-menu .menu-item > .menu-link.menu-toggle', function() {
            var $item = $(this).closest('.menu-item');
            var menuInner = document.querySelector('#layout-menu .menu-inner');
            if (!menuInner) return;

            var willOpen = !$item.hasClass('open');
            if (willOpen) {
                setTimeout(function() {
                    try {
                        var innerRect = menuInner.getBoundingClientRect();
                        var subMenu = $item.find('.menu-sub')[0];
                        var targetEl = subMenu || $item[0];
                        var targetRect = targetEl.getBoundingClientRect();

                        if (targetRect.bottom > innerRect.bottom) {
                            var overflow = Math.round(targetRect.bottom - innerRect.bottom) + 36;
                            menuInner.scrollTop += overflow;
                            if (window.Helpers && window.Helpers.menuPsScroll) {
                                try { window.Helpers.menuPsScroll.update(); } catch(e) {}
                            }
                        }
                    } catch(err) {}
                }, 280);
            }
        });

        // Explicit Mobile Sidebar Close Handlers (Guaranteed close on 'X' button or overlay click)
        function closeMobileSidebar() {
            if (window.Helpers && typeof window.Helpers.setCollapsed === 'function') {
                window.Helpers.setCollapsed(true, true);
            }
            $('html').removeClass('layout-menu-expanded');
        }

        $(document).on('click', '.layout-menu-close, #btn-close-sidebar, .layout-overlay', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeMobileSidebar();
        });

        // Mobile sidebar toggle safeguard (only when clicking hamburger button outside menu)
        $(document).on('click', '#layout-navbar .layout-menu-toggle, [aria-label="Toggle Navigation"]', function(e) {
            if (!window.Helpers || typeof window.Helpers.toggleCollapsed !== 'function') {
                e.preventDefault();
                $('html').toggleClass('layout-menu-expanded');
            }
        });
    })();

    // Global double-submit guard for all standard forms
    $(document).on('submit', 'form:not(.no-double-submit)', function(e) {
        var $form = $(this);
        if ($form.closest('.modal').length) return; // Modal forms handled by modal AJAX handler
        if ($form.data('submitting')) {
            e.preventDefault();
            return false;
        }
        var $btn = $form.find('button[type="submit"]:not(.no-disable), input[type="submit"]:not(.no-disable)').first();
        if ($btn.length && !$btn.prop('disabled')) {
            $form.data('submitting', true);
            setTimeout(function() {
                $btn.prop('disabled', true);
            }, 0);
        }
    });
</script>

<script>
    function showTempPasswordPopup(fullMsg) {
        var match = fullMsg.match(/Password sementara:\s*([^\)]+)/i);
        var tempPw = match ? match[1].trim() : '';
        if (tempPw && typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Akun Mobile Berhasil Dibuat',
                html: '<div class="text-start fs-6 mt-2">' +
                      '<p class="text-muted mb-2" style="font-size: 13px;">' + fullMsg + '</p>' +
                      '<div class="p-3 rounded-3 mb-3 text-center" style="background: #F8FAFC; border: 1px solid #E2E8F0;">' +
                      '<span class="text-muted small d-block mb-1">Password Sementara:</span>' +
                      '<span class="fw-bold font-mono text-primary" style="font-size: 18px; letter-spacing: 1px;" id="swalTempPwText">' + tempPw + '</span>' +
                      '</div>' +
                      '<button type="button" id="btnCopyTempPwGlobal" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1.5" style="border-radius: 8px; font-weight: 600;">' +
                      '<i class="ti ti-copy" style="font-size: 16px;"></i> <span id="btnCopyTempPwGlobalText">Salin Password</span>' +
                      '</button>' +
                      '</div>',
                confirmButtonColor: '#1E4D3E',
                confirmButtonText: 'Selesai',
                didOpen: function() {
                    var copyBtn = document.getElementById('btnCopyTempPwGlobal');
                    if (copyBtn) {
                        copyBtn.addEventListener('click', function() {
                            var done = function() {
                                document.getElementById('btnCopyTempPwGlobalText').innerText = 'Berhasil Disalin!';
                                copyBtn.className = 'btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1.5';
                            };
                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                navigator.clipboard.writeText(tempPw).then(done);
                            } else {
                                var tempInput = document.createElement('textarea');
                                tempInput.value = tempPw;
                                document.body.appendChild(tempInput);
                                tempInput.select();
                                document.execCommand('copy');
                                document.body.removeChild(tempInput);
                                done();
                            }
                        });
                    }
                }
            });
            return true;
        }
        return false;
    }

    // Global Export Excel Handler: SweetAlert Loading + Auto Download + SweetAlert Success
    $(document).on('click', 'a[href*="/export"], a[href*="export"], a[href*="download_template"], a[href$=".xlsx"], .btn-export-excel', function(e) {
        var $btn = $(this);
        var href = $btn.attr('href');
        if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('#')) return;

        e.preventDefault();
        e.stopPropagation();

        if (typeof Swal === 'undefined') {
            window.location.href = href;
            return;
        }

        var themePrimary = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '{{ $general_setting->theme_color_1 ?? "#1E4D3E" }}';
        var themeSecondary = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-2').trim() || '{{ $general_setting->theme_color_2 ?? "#32745E" }}';

        Swal.fire({
            title: 'Menyiapkan File Excel...',
            html: '<div class="text-muted small mb-3">Mohon tunggu, sistem sedang menyiapkan data untuk diekspor ke Excel.</div>' +
                  '<div class="d-flex justify-content-center my-3">' +
                  '  <div class="spinner-border export-loading-spinner" role="status" style="width: 3rem; height: 3rem; border-width: 0.25em; border-color: ' + themePrimary + '; border-right-color: transparent;"></div>' +
                  '</div>',
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        });

        fetch(href, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            var disposition = response.headers.get('Content-Disposition') || response.headers.get('content-disposition');
            var filename = 'Data_Export.xlsx';
            if (disposition && disposition.indexOf('filename=') !== -1) {
                var matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            return response.blob().then(function(blob) {
                return { blob: blob, filename: filename };
            });
        })
        .then(function(res) {
            var blobUrl = window.URL.createObjectURL(res.blob);
            var tempLink = document.createElement('a');
            tempLink.style.display = 'none';
            tempLink.href = blobUrl;
            tempLink.download = res.filename;
            document.body.appendChild(tempLink);
            tempLink.click();
            setTimeout(function() {
                window.URL.revokeObjectURL(blobUrl);
                if (tempLink.parentNode) tempLink.parentNode.removeChild(tempLink);
            }, 500);

            Swal.fire({
                icon: 'success',
                title: 'Export Excel Berhasil!',
                html: '<div class="text-start fs-6 mt-2">' +
                      '  <p class="text-muted mb-2" style="font-size: 13.5px;">File Excel berhasil diunduh dan tersimpan di folder unduhan perangkat Anda:</p>' +
                      '  <div class="p-2.5 rounded export-file-badge text-center font-mono fw-bold mb-1" style="font-size: 13px; background-color: rgba(30, 77, 62, 0.05); border: 1px dashed ' + themeSecondary + '; color: ' + themePrimary + ';">' +
                      '    <i class="ti ti-file-spreadsheet me-1.5" style="color: ' + themeSecondary + '; font-size: 17px;"></i> ' + res.filename +
                      '  </div>' +
                      '</div>',
                confirmButtonColor: themePrimary,
                confirmButtonText: 'Tutup'
            });
        })
        .catch(function(err) {
            console.error('[ExportExcel] Error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Export Excel',
                text: 'Terjadi kendala saat mengekspor data ke Excel. Silakan coba lagi.',
                confirmButtonColor: themePrimary,
                confirmButtonText: 'Tutup'
            });
        });
    });
</script>

@if ($message = Session::get('success'))
    <script>
        (function() {
            var fullMsg = @json($message);
            if (fullMsg && fullMsg.indexOf('Password sementara:') !== -1) {
                if (showTempPasswordPopup(fullMsg)) {
                    return;
                }
            }
            if (typeof toastr !== 'undefined') {
                toastr.options.showEasing = 'swing';
                toastr.options.hideEasing = 'linear';
                toastr.options.progressBar = true;
                toastr.success("Berhasil", fullMsg, {
                    timeOut: 3000
                });
            }
        })();
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

<script>
    // =========================================================================
    // GLOBAL ADMIN MODAL FORM HANDLER & VALIDATION (DESIGN.md)
    // Prevents modal from closing when required fields are missing or invalid
    // =========================================================================
    $(function() {
        $(document).on('submit', '.modal form:not(.no-ajax-modal)', function(e) {
            var form = this;
            var $form = $(form);

            // If form already handled by its own custom ajax, let it proceed without global submission
            if ($form.data('custom-ajax') || $form.hasClass('no-ajax-modal') || $form.is('[data-custom-ajax]')) return;

            e.preventDefault();

            // Clear previous invalid states
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback.auto-feedback').remove();

            // Check required fields
            var missing = [];
            var $firstInvalid = null;

            $form.find('input[required], select[required], textarea[required]').each(function() {
                var $el = $(this);
                var val = $el.val();
                var isMissing = false;

                if ($el.is(':checkbox') || $el.is(':radio')) {
                    var name = $el.attr('name');
                    if (!$form.find('[name="' + name + '"]:checked').length) {
                        isMissing = true;
                    }
                } else if (!val || (typeof val === 'string' && val.trim() === '')) {
                    isMissing = true;
                }

                if (isMissing) {
                    $el.addClass('is-invalid');
                    var labelText = $el.closest('.form-group').find('label').first().text().replace('*', '').replace('(Opsional)', '').trim() || $el.attr('placeholder') || $el.attr('name');
                    missing.push(labelText);
                    if (!$firstInvalid) {
                        $firstInvalid = $el;
                    }
                    if (!$el.parent().find('.invalid-feedback').length) {
                        $el.parent().append('<div class="invalid-feedback d-block auto-feedback">' + labelText + ' wajib diisi</div>');
                    }
                }
            });

            // If required fields missing, halt & keep modal open!
            if (missing.length > 0) {
                if ($firstInvalid) {
                    $firstInvalid.focus();
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'Kolom Wajib Belum Diisi',
                    html: '<div class="text-start" style="font-size: 13.5px;">Silakan lengkapi kolom yang bertanda bintang (<span class="text-danger fw-bold">*</span>):' +
                          '<ul class="mt-2 mb-0 ps-3">' +
                          missing.slice(0, 6).map(function(m) { return '<li><strong>' + m + '</strong></li>'; }).join('') +
                          (missing.length > 6 ? '<li>...dan kolom lainnya</li>' : '') +
                          '</ul></div>',
                    confirmButtonColor: '#1E4D3E',
                    confirmButtonText: 'Lengkapi Sekarang'
                });
                return false;
            }

            // AJAX Submission: Modal stays open upon server errors
            var $btnSubmit = $form.find('button[type="submit"]').first();
            var originalBtnHtml = $btnSubmit.html();
            $btnSubmit.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');

            var formData = new FormData(form);

            $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method') || 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    $form.closest('.modal').modal('hide');
                    var msg = (response && response.message) ? response.message : 'Data berhasil disimpan.';
                    try {
                        sessionStorage.setItem('flash_success', msg);
                    } catch(e) {}
                    // Instant single reload without artificial 1.5s delay
                    window.location.reload();
                },
                error: function(xhr) {
                    $btnSubmit.prop('disabled', false).html(originalBtnHtml);

                    // MODAL STAYS OPEN!
                    var errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.status === 422 && xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var errorList = Object.keys(errors).map(function(key) {
                                $form.find('[name="' + key + '"]').addClass('is-invalid');
                                return '<li>' + errors[key][0] + '</li>';
                            }).join('');
                            errorMsg = '<ul class="text-start mt-2 ps-3 mb-0">' + errorList + '</ul>';
                        } else if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        html: errorMsg,
                        confirmButtonColor: '#1E4D3E'
                    });
                }
            });
        });
    });

    // ==========================================================================
    // FAST, NON-BLOCKING MODAL LOADER & CACHE (/ponytail)
    // ==========================================================================
    window._globalModalCache = window._globalModalCache || {};

    window.normalizeCacheUrl = function(u) {
        if (!u || typeof u !== 'string') return '';
        try {
            var a = document.createElement('a');
            a.href = u;
            return (a.pathname || '').replace(/\/$/, '') || '/';
        } catch(e) {
            return u.replace(/^https?:\/\/[^\/]+/, '').replace(/\/$/, '');
        }
    };

    window.getModalSkeletonHtml = function() {
        return '<div class="d-flex flex-column align-items-center justify-content-center py-5">' +
            '<div class="spinner-border mb-3" role="status" style="width: 2.2rem; height: 2.2rem; color: #1E4D3E;">' +
            '<span class="visually-hidden">Loading...</span>' +
            '</div>' +
            '<span class="text-muted fw-semibold" style="font-size: 13px;">Memuat formulir...</span>' +
            '</div>';
    };

    (function($) {
        var origLoad = $.fn.load;
        $.fn.load = function(url, params, callback) {
            var self = this;
            if (typeof params === 'function') {
                callback = params;
                params = null;
            }
            if (typeof url !== 'string') {
                return origLoad.apply(this, arguments);
            }

            var key = window.normalizeCacheUrl(url);
            var isGet = !params;
            var isCreateForm = key.endsWith('/create');

            function renderModalHtml(html) {
                self.html(html);
                if (typeof initGlobalFlatpickr === 'function') {
                    initGlobalFlatpickr(self);
                }
                if (self.find('.select2').length && $.fn.select2) {
                    self.find('.select2').each(function() {
                        var $el = $(this);
                        if (!$el.hasClass('select2-hidden-accessible')) {
                            $el.select2({ dropdownParent: self.closest('.modal').length ? self.closest('.modal') : $('body') });
                        }
                    });
                }
                if (self.find('#jam_masuk,#jam_pulang').length && !$.fn.mask) {
                    $.getScript("{{ asset('assets/js/jquery.mask.min.js') }}", function() {
                        self.find("#jam_masuk,#jam_pulang,#batas_presensi_pulang").mask("00:00");
                    });
                }
                if (self.find('#map').length) {
                    [100, 300].forEach(function(d) {
                        setTimeout(function() {
                            try {
                                if (window._cabangEditMap && typeof window._cabangEditMap.invalidateSize === 'function') {
                                    window._cabangEditMap.invalidateSize();
                                }
                            } catch(e) {}
                            try {
                                if (window._cabangCreateMap && typeof window._cabangCreateMap.invalidateSize === 'function') {
                                    window._cabangCreateMap.invalidateSize();
                                }
                            } catch(e) {}
                        }, d);
                    });
                }
                if (callback) callback.call(self[0], html, "success");
            }

            // If already in memory cache and is static create form, show immediately (0ms)
            if (isGet && isCreateForm && window._globalModalCache[key]) {
                renderModalHtml(window._globalModalCache[key]);
                return self;
            }

            // Instant visual feedback
            self.html(window.getModalSkeletonHtml());

            // Fetch directly without blocking other requests
            $.ajax({
                url: url,
                type: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).done(function(html) {
                // If response is accidentally a full login page, reload to login cleanly
                if (typeof html === 'string' && html.indexOf('id="formAuthentication"') !== -1) {
                    window.location.reload();
                    return;
                }
                if (isCreateForm) {
                    window._globalModalCache[key] = html;
                }
                renderModalHtml(html);
            }).fail(function(xhr, status, err) {
                if (xhr.status === 401) {
                    window.location.reload();
                    return;
                }
                self.html('<div class="alert alert-danger m-3 py-2 px-3">Gagal memuat form. Silakan coba lagi.</div>');
                if (callback) callback.call(self[0], null, "error");
            });

            return self;
        };
    })(jQuery);

    // Universal Plugin Initializer (Idempotent, Safe for Initial Load and PJAX Navigations)
    window.initAppPlugins = function(context) {
        if (typeof initGlobalFlatpickr === 'function') {
            try { initGlobalFlatpickr(context); } catch(e) {}
        }
        if (typeof initGlobalResponsiveTables === 'function') {
            try { initGlobalResponsiveTables(); } catch(e) {}
        }
        if (window.jQuery && window.jQuery.fn.select2) {
            var $ctx = context ? window.jQuery(context) : window.jQuery(document);
            $ctx.find('.select2').each(function() {
                var $el = window.jQuery(this);
                if (!$el.hasClass('select2-hidden-accessible')) {
                    try {
                        $el.select2({ dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : window.jQuery('body') });
                    } catch(e) {}
                }
            });
        }
        if (typeof bootstrap !== 'undefined') {
            if (bootstrap.Tooltip) {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                    try { if (!el._tooltip) el._tooltip = new bootstrap.Tooltip(el); } catch(e) {}
                });
            }
            if (bootstrap.Popover) {
                document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function(el) {
                    try { if (!el._popover) el._popover = new bootstrap.Popover(el); } catch(e) {}
                });
            }
    // Global Table Search: Prevent Enter key from submitting/triggering search since live filtering is automatic
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            var target = e.target;
            if (!target || target.tagName !== 'INPUT') return;

            var isTableSearch = target.type === 'search' ||
                target.closest('.admin-filter-toolbar, form[id*="filter" i], .table-search, .admin-table-search') ||
                (target.name && /nama|search|cari/i.test(target.name)) ||
                (target.placeholder && /search|cari/i.test(target.placeholder));

            if (isTableSearch) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }
    }, true);
    };
</script>

<div id="app-page-scripts" style="display: none;">
@stack('myscript')
</div>

