@extends('backend.layouts.app')

@push('page-styles')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endpush

@section('layout')
    @include('backend.includes.header')

    @include('backend.includes.sidebar')
    <!-- @include('backend.includes.aside') -->


    <div class="main-content">
        <section class="section">
            <!-- <div class="section-header">
                <h1>Form</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Bootstrap Components</a></div>
                    <div class="breadcrumb-item">Form</div>
                </div>
            </div> -->

            <div class="section-body">
                @yield('content')
            </div>
        </section>
    </div>

    @include('backend.includes.footer')

@endsection

@push('after-scripts')
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush