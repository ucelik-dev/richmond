<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
    <meta name="base_url" content="{{ url('/') }}">
    <meta name="csrf_token" content="{{ csrf_token() }}">

    <title>Richmond College</title>

    <link rel="icon" type="image/png" href="{{ asset('default/favicon-32x32.png') }}">
    
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/animated_barfiller.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/venobox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/scroll_button.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/pointer.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/jquery.calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/range_slider.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/startRating.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/video_player.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/jquery.simple-bar-graph.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/sticky_menu.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.css') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5…" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="stylesheet" href="{{ asset('frontend/assets/css/spacing.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/frontend.css') }}">

    {{-- DataTables Bootstrap 5 CSS (v1.x) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <style>
        /* Make ONLY the top DT buttons white */
        .dataTables_wrapper .dt-buttons > .btn,
        .dataTables_wrapper .dt-buttons > .btn-group > .btn,
        .dataTables_wrapper .dt-buttons > .btn > span,
        .dataTables_wrapper .dt-buttons > .btn-group > .btn > span { 
            color: #fff !important;
            background-color: #667382;
        }

        /* Keep the Column visibility dropdown items readable */
        div.dt-button-collection .btn,
        div.dt-button-collection .dt-button,
        div.dt-button-collection .dropdown-item {
            color: #212529 !important;
        }

        /* If your theme uses Bootstrap variables, force white as well */
        .dataTables_wrapper .dt-buttons > .btn {
            --bs-btn-color: #fff !important;
            --bs-btn-hover-color: #fff !important;
        }
        .dataTables_wrapper table.dataTable thead th {
            font-size: .75rem;
        }

    </style>




    {{-- @vite(['resources/js/frontend/course.js']) --}}

    @stack('header_scripts')
    
</head>

<body class="home_3">


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






    <!--============ PRELOADER START ===========-->
    <div id="preloader">
        <div class="preloader_icon">
            <img src="{{ asset('frontend/assets/images/preloader2.png') }}" alt="Preloader" class="img-fluid">
        </div>
    </div>
    <!--============ PRELOADER START ===========-->


    @include('frontend.layouts.header')

    @yield('content')

    @include('frontend.layouts.footer')


    <!--================================
        SCROLL BUTTON START
    =================================-->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!--================================
        SCROLL BUTTON END
    =================================-->

    <!--jquery library js-->
    <script src="{{ asset('frontend/assets/js/jquery-3.7.1.min.js') }}"></script>
    <!--bootstrap js-->
    <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--font-awesome js-->
    <script src="{{ asset('frontend/assets/js/Font-Awesome.js') }}"></script>
    <!--marquee js-->
    <script src="{{ asset('frontend/assets/js/jquery.marquee.min.js') }}"></script>
    <!--slick js-->
    <script src="{{ asset('frontend/assets/js/slick.min.js') }}"></script>
    <!--countup js-->
    <script src="{{ asset('frontend/assets/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.countup.min.js') }}"></script>
    <!--venobox js-->
    <script src="{{ asset('frontend/assets/js/venobox.min.js') }}"></script>
    <!--nice-select js-->
    <script src="{{ asset('frontend/assets/js/jquery.nice-select.min.js') }}"></script>
    <!--Scroll Button js-->
    <script src="{{ asset('frontend/assets/js/scroll_button.js') }}"></script>
    <!--pointer js-->
    <script src="{{ asset('frontend/assets/js/pointer.js') }}"></script>
    <!--range slider js-->
    <script src="{{ asset('frontend/assets/js/range_slider.js') }}"></script>
    <!--barfiller js-->
    <script src="{{ asset('frontend/assets/js/animated_barfiller.js') }}"></script>
    <!--calendar js-->
    <script src="{{ asset('frontend/assets/js/jquery.calendar.js') }}"></script>
    <!--starRating js-->
    <script src="{{ asset('frontend/assets/js/starRating.js') }}"></script>
    <!--Bar Graph js-->
    <script src="{{ asset('frontend/assets/js/jquery.simple-bar-graph.min.js') }}"></script>
    <!--select2 js-->
    <script src="{{ asset('frontend/assets/js/select2.min.js') }}"></script>
    <!--Video player js-->
    <script src="{{ asset('frontend/assets/js/video_player.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/video_player_youtube.js') }}"></script>
    <!--wow js-->
    <script src="{{ asset('frontend/assets/js/wow.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <!--main/custom js-->
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>

    <script>
        // Stop DataTables from wrapping buttons in .btn-group
        if ($.fn.dataTable && $.fn.dataTable.Buttons) {
            $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons d-inline-flex gap-2';
        }
    </script>

    @stack('scripts')

    <script>
        var notyf = new Notyf({
            duration: 5000,
            dismissible: true
        });

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                notyf.error("{{ $error }}");
            @endforeach
        @endif
    </script>

    <script>
        /* Sweet alert confirm message */
        $(document).ready(function() {

            $('body').on('click', '.delete-item', function(e) {
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
                            data: {_token: "{{ csrf_token() }}"},
                            success: function(response) {
                                window.location.reload(); 
                            },
                            error: function(xhr, status, error) {
                                notyf.error(xhr.responseJSON.message);
                            }
                        });

                    }
                });
            })

        })
    </script>

</body>

</html>