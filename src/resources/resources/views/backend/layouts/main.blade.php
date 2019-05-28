@extends('backend.layouts.app')

@push('page-styles')
    <link rel="stylesheet" href="{{ asset('css/laragen.css') }}">
@endpush

@section('layout')
    @include('backend.includes.header')

    @include('backend.includes.sidebar')

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                @yield('page-header')
            </div>
            <div class="section-header-placeholder" style="display:none;">
            </div>

            <div class="section-body">
                @yield('content')
            </div>
        </section>
    </div>

    @include('backend.includes.footer')

@endsection

@push('page-scripts')
    <script src="{{ asset('js/laragen.js') }}"></script>
@endpush