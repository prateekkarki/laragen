<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laragen Dashboard') }}</title>
    <meta name="description" content="@yield('meta_description', 'Laragen Dashboard')">
    <meta name="author" content="@yield('meta_author', 'Prateek Karki')">
    @yield('meta')

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}" >
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    @stack('page-styles')
    @stack('after-styles')
    <script type="text/javascript">
        var APP_URL = '{!! url('/') !!}';
    </script>

</head>

<body class="@yield('body_classes')">
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
        
            @yield('layout')

            <!-- Scripts -->
            <!-- General JS Scripts -->
            <script src="{{ asset('js/jquery-3.3.1.min.js') }}" ></script>
            <script src="{{ asset('js/popper.min.js') }}" ></script>
            <script src="{{ asset('js/bootstrap.min.js') }}"></script>
            <script src="{{ asset('js/jquery.nicescroll.min.js') }}"></script>
            <script src="{{ asset('js/moment.min.js') }}"></script>
            <script src="{{ asset('js/stisla.js') }}"></script>

            <!-- JS Libraries -->
            @stack('page-scripts')
            @stack('after-scripts')

        </div>
    </div>
</body>
</html>
