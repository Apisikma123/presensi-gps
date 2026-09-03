 <!-- Core JS -->
 <script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/popper/popper.js') }}"></script>
 <script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
 <script src="{{ asset('/assets/vendor/libs/hammer/hammer.js') }}"></script>
 <script src="{{ asset('/assets/vendor/js/menu.js') }}"></script>
 <script src="{{ asset('assets/vendor/js/jquery.maskMoney.js') }}"></script>
 <script src="{{ asset('assets/vendor/js/easy-number-separator.js') }}"></script>

 <!-- Essential Vendors JS -->
 <script src="{{ asset('assets/vendor/js/feather.min.js') }}" defer></script>
 <script src="{{ asset('assets/external/js/leaflet.js') }}" defer></script>
 <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
 <script src="{{ asset('assets/vendor/js/toastr.min.js') }}"></script>
 <script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>
 <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
 <script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
 <script src="{{ asset('assets/vendor/js/freeze-table.js') }}"></script>
 <script src="{{ asset('assets/js/ui-popover.js') }}"></script>

 <script>
     $(function() {
         $(".flatpickr-date").flatpickr();
     });
 </script>
 <!-- Main JS -->
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

 @if ($errors->any())
     @php
         $err = '';
     @endphp
     @foreach ($errors->all() as $error)
         @php
             $err .= $error;
         @endphp
     @endforeach
     <script>
         toastr.options.showEasing = 'swing';
         toastr.options.hideEasing = 'linear';
         toastr.options.progressBar = true;
         toastr.error(" Gagal", "{{ $err }}", {
             timeOut: 3000
         });
     </script>
 @endif
 <script>
     $('.delete-confirm').click(function(event) {
         var form = $(this).closest("form");
         var name = $(this).data("name");
         event.preventDefault();
         Swal.fire({
             title: `Apakah Anda Yakin Ingin Menghapus Data Ini ?`,
             text: "Jika dihapus maka data akan hilang permanent.",
             icon: "warning",
             buttons: true,
             dangerMode: true,
             showCancelButton: true,
             confirmButtonColor: "#32745e",
             cancelButtonColor: "#d33",
             confirmButtonText: "Ya, Hapus!"
         }).then((result) => {
             /* Read more about isConfirmed, isDenied below */
             if (result.isConfirmed) {
                 form.submit();
             }
         });
     });


     $('.cancel-confirm').click(function(event) {
         var form = $(this).closest("form");
         var name = $(this).data("name");
         event.preventDefault();
         Swal.fire({
             title: `Apakah Anda Yakin Ingin Membatalkan Data Ini ?`,
             text: "Data ini akan dibatalkan.",
             icon: "warning",
             buttons: true,
             dangerMode: true,
             showCancelButton: true,
             confirmButtonColor: "#32745e",
             cancelButtonColor: "#d33",
             confirmButtonText: "Ya, Batalkan!"
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
        document.querySelectorAll('table:not(.flatpickr-calendar table)').forEach(function(tbl) {
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
    document.addEventListener('spa:page-loaded', initGlobalResponsiveTables);
</script>

 <script src="{{ asset('/assets/js/main.js') }}"></script>

 @stack('myscript')
