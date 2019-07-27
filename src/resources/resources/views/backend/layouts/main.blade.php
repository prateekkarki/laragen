@extends('backend.layouts.app')

@push('page-styles')
    <link rel="stylesheet" href="{{ asset('css/laragen.css') }}">
    <link rel="stylesheet" href="{{ asset('css/iziToast.min.css') }}" >
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
    
    <script type="text/javascript">
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                iziToast.error({
                    title: 'Error',
                    message: '{{ $error }}'
                });
            @endforeach
        @endif
        
        @if(session('success'))
            iziToast.success({
                title: 'Success',
                message: '{{ session("success") }}'
            });
        @endif        
    </script>
@endpush