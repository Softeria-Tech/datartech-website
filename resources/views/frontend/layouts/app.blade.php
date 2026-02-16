<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datartech Digital Solutions</title>
    <!-- fav icon link -->
    <link rel="shortcut icon" href="{{ asset('assets/frontend/images/homepage/jtcyber-logo.png') }}" type="image/x-icon">
    <!-- font awesome-->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/fontawesome.min.css') }}">
    <!-- google fonts -->
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link
        href="{{ asset('assets/frontend/css/css258d5.css') }}?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&amp;family=Jost:ital,wght@0,100..900;1,100..900&amp;family=Manrope:wght@200..800&amp;family=Raleway:ital,wght@0,100..900;1,100..900&amp;family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap"
        rel="stylesheet">

    <!-- Link slick slider CSS -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/slick-theme.css') }}">
    <!-- letter animation -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/cssanimation.min.css') }}">
    <!-- lordicon icons animation -->
    <script src="{{ asset('assets/frontend/js/lordicon.js') }}"></script>
    <!-- AOS animation -->
    <link href="{{ asset('assets/frontend/css/aos.css') }}" rel="stylesheet">
    <!-- bootstrap icons -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/bootstrap-icons.min.css') }}">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/bootstrap.min.css') }}">
    <!-- css link -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/style.min.css') }}">
    <!--shop css -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/LineIcons.3.0.css') }}">

</head>

<body>

    @include('frontend.layouts.header')

    <!-- =====main section===== -->
    <main class="home-one">
        @yield('content')
    </main>

    @include('frontend.layouts.footer')
    @include('frontend.layouts.scripts')

    @stack('scripts')
</body>

</html>
