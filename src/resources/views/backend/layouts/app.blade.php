<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laragen Dashboard') }}</title>
    <meta name="description" content="@yield('meta_description', 'Laragen Dashboard')">
    <meta name="author" content="@yield('meta_author', 'Prateek Karki')">
    @yield('meta')

    @stack('before-styles')
    <link href="{{ asset('css/coreui.min.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('css/fa-all.min.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('css/simple-line-icons.css') }}" rel="stylesheet" type="text/css" media="all" />
    @stack('after-styles')
</head>

<body class="app sidebar-lg-show @yield('body_classes')">
    
    @yield('layout')

    <!-- Scripts -->
    @stack('before-scripts')
    <script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/coreui.min.js') }}"></script>
    @stack('after-scripts')
</body>
</html>
