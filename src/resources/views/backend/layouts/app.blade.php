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

    @stack('before-styles')
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}" >
    
    <!-- CSS Libraries -->
    @stack('page_styles')

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    @stack('after-styles')

</head>

<body class="@yield('body_classes')">
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
        
            @yield('layout')

            <!-- Scripts -->
            @stack('before-scripts')

            <!-- General JS Scripts -->
            <script src="{{ asset('js/jquery-3.3.1.min.js') }}" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
            <script src="{{ asset('js/popper.min.js') }}" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
            <script src="{{ asset('js/bootstrap.min.js') }}" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
            <script src="{{ asset('js/dropzone.min.js') }}"></script>
            <script src="{{ asset('js/jquery.nicescroll.min.js') }}"></script>
            <script src="{{ asset('js/moment.min.js') }}"></script>
            <script src="{{ asset('js/stisla.js') }}"></script>

            <!-- JS Libraies -->

            <!-- Page Specific JS File -->
            @stack('page_scripts')


            <!-- Template JS File -->
            <script src="{{ asset('js/scripts.js') }}"></script>
            <script src="{{ asset('js/custom.js') }}"></script>
            @stack('after-scripts')

        </div>
    </div>
</body>
</html>
