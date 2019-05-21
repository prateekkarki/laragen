@extends('backend.layouts.app')

@section('layout')
<div class="d-flex flex-wrap align-items-stretch">
    <div class="col-lg-4 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white">
        <div class="p-4 m-3">
            <img src="{{ asset('img/stisla-fill.svg') }}" title="Company Logo" alt="logo" width="80" class="shadow-light rounded-circle mb-5 mt-2">
            <img src="{{ asset('img/stisla-fill.svg') }}" title="Client Logo" alt="logo" width="80" class="shadow-light rounded-circle mb-5 mt-2 mr-3"> 
            <h4 class="text-dark font-weight-normal">Welcome to <span class="font-weight-bold">{{ config('app.name', 'Laragen Dashboard') }}</span></h4>
            <p class="text-muted">@yield('heading')</p>
            @if ($errors->any())
            <div class="alert alert-danger">
                <span>Please fix the errors below.</span>
            </div>
            @endif

            @yield('content')

            <div class="text-center mt-5 text-small">
                Copyright &copy; {{ date('Y') }} | {{ config('app.name', 'Laragen Dashboard') }}
            </div>
        </div>
    </div>
    <div class="col-lg-8 col-12 order-lg-2 order-1 min-vh-100 background-walk-y position-relative overlay-gradient-bottom" data-background="{{ asset('img/unsplash/login-bg.jpg') }}" style="background-image:url({{ asset('img/unsplash/login-bg.jpg') }})">
        <div class="absolute-bottom-left index-2">
            <div class="text-light p-5 pb-2">
                <div class="mb-5 pb-3">
                    <h1 class="mb-2 display-4 font-weight-bold" id="login-greeting"></h1>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var d = new Date();
    var h = d.getHours();
    var greeting = '';
    if (h < 12) 
        greeting = 'Good morning';
    else if(h < 17)
        greeting = 'Good afternoon';
    else if(h < 19)
        greeting = 'Good evening';
    else
        greeting = 'Good night';
    document.getElementById("login-greeting").innerHTML = greeting;
</script>
@endsection
