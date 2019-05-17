@extends('backend.layouts.app')

@section('layout')
<div class="d-flex flex-wrap align-items-stretch">
    <div class="col-lg-4 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white">        
        @yield('content')
    </div>
    <div class="col-lg-8 col-12 order-lg-2 order-1 min-vh-100 background-walk-y position-relative overlay-gradient-bottom" data-background="{{ asset('img/unsplash/login-bg.jpg') }}" style="background-image:url({{ asset('img/unsplash/login-bg.jpg') }})">
        <div class="absolute-bottom-left index-2">
            <div class="text-light p-5 pb-2">
                <div class="mb-5 pb-3">
                    <h1 class="mb-2 display-4 font-weight-bold">Good Morning</h1>
                    <h5 class="font-weight-normal text-muted-transparent">Bali, Indonesia</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
