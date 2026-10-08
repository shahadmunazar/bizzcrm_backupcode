<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title', 'ZipZapGo - Lead Management System')</title>
    <meta name="description" content="@yield('description', 'ZipZapGo - Lead Management System')">
    <link rel="shortcut icon" href="{{ asset('assets/frontend/main/img/favicon.png') }}" type="image/x-icon">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/vendors/themify-icon/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/vendors/simple-line-icon/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/vendors/font-awesome/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/vendors/flaticon/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/vendors/animation/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/vendors/owl-carousel/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/vendors/magnify-pop/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/vendors/elagent/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/vendors/scroll/jquery.mCustomScrollbar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/main/css/responsive.css') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Preloader -->
    <div id="preloader">
        <div id="ctn-preloader" class="ctn-preloader">
            <div class="animation-preloader">
                <div class="spinner"></div>
                <div class="txt-loading">
                    @foreach(['Z','I','G','Z','A','P','G','O'] as $letter)
                        <span data-text-preloader="{{ $letter }}" class="letters-loading">{{ $letter }}</span>
                    @endforeach
                </div>
                <p class="text-center">Loading</p>
            </div>
            <div class="loader">
                <div class="row">
                    @foreach(range(1,4) as $i)
                        <div class="col-3 loader-section {{ $i <= 2 ? 'section-left' : 'section-right' }}">
                            <div class="bg"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="body_wrapper">
        @include('layouts.frontend.header')

        @yield('content')

        @include('layouts.frontend.footer')
    </div>
    <script data-cfasync="false" src="{{ asset('assets/frontend/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/vendors/wow/wow.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/vendors/sckroller/jquery.parallax-scroll.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/vendors/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/vendors/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/vendors/isotope/isotope-min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/vendors/magnify-pop/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/vendors/counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/vendors/counterup/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/vendors/counterup/appear.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/vendors/circle-progress/circle-progress.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/vendors/scroll/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/frontend/main/js/main.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
