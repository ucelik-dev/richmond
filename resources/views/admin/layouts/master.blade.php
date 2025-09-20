<!doctype html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <meta name="base_url" content="{{ url('/') }}">
    <meta name="csrf_token" content="{{ csrf_token() }}">
    
    <title>Richmond College</title>

    <link rel="icon" type="image/png" href="{{ asset('default/favicon-32x32.png') }}">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-MzJx...==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">

    <!-- CSS files -->
    <link href="{{ asset('admin/assets/dist/css/tabler.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/assets/dist/css/demo.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('admin/assets/dist/css/style.css') }}" rel="stylesheet" />

    {{-- DataTables Bootstrap 5 CSS (v1.x) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    

    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>

    <style>
        /* Remove Bootstrap's default arrow */
        .accordion-button::after {
            display: none !important;
        }

    </style>

</head>

<body>

    @if(session('impersonator_id'))
        <div class="position-fixed bottom-0 end-0 m-3 z-3">
            <div class="d-flex align-items-center gap-2 bg-warning-subtle text-warning-emphasis border border-warning-subtle shadow-lg rounded-3 py-2 px-3 small lh-sm text-nowrap">
                <span class="fw-semibold">You are impersonating this user.</span>

                <form action="{{ route('admin.impersonate.stop') }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm rounded-2">Stop Impersonating</button>
                </form>

                <button type="button"
                        class="btn btn-link p-0 small text-warning-emphasis"
                        onclick="this.closest('.position-fixed').remove()"
                        aria-label="Hide">&times;</button>
            </div>
        </div>
    @endif
    
    <script src="{{ asset('admin/assets/dist/js/demo-theme.min.js') }}"></script>
    <div class="page">
        <!-- Sidebar -->
        @include('admin.layouts.sidebar')

        <!-- Navbar -->
        @include('admin.layouts.header')

        <div class="page-wrapper">

            @yield('content')

            {{-- @include('admin.layouts.footer') --}}

        </div>

    </div>
    
    <script src="{{ asset('admin/assets/dist/libs/tom-select/dist/js/tom-select.base.min.js') }}"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

    <!-- DATATABLES -->
      {{-- DataTables core (v1.x) + Bootstrap 5 integration --}}
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

  {{-- Buttons (only if you enabled buttons in your DataTable) --}}
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- DATATABLES -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Tabler Core -->
    <script src="{{ asset('admin/assets/dist/js/tabler.min.js') }}" defer></script>
    <script src="{{ asset('admin/assets/dist/js/demo.min.js') }}" defer></script>

    <script>
    // Stop DataTables from wrapping buttons in .btn-group
    if ($.fn.dataTable && $.fn.dataTable.Buttons) {
        $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons d-inline-flex gap-2';
    }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const notyf = new Notyf({
                duration: 5000,
                dismissible: true,
                position: { x: 'right', y: 'top' }
            });

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    notyf.error(@json($error));
                @endforeach
            @endif
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.select2-multiple').select2();
        });
    </script>



    <script>
    /* Sweet alert confirm message */
    $(document).ready(function () {

        $('body').on('click', '.delete-item', function (e) {
            e.preventDefault();
            let url = $(this).attr('href');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        method: 'DELETE',
                        url: url,
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function (xhr) {
                            let message = 'Something went wrong.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Delete Failed',
                                text: message
                            });
                        }
                    });

                }
            });
        });

    });
</script>

 @stack('scripts')

</body>

</html>
