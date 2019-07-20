@extends('backend.layouts.app')

@push('page-styles')
<style>
    body {
        background-color: #ffffff !important;
    }
</style>
@endpush

@section('layout')
    <div class="container mt-1">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                <div class="page-error">
                    <div class="page-inner">
                        <h3>This area is forbidden</h3>

                        <div class="404-image">
                            <img class="img-fluid" src="{{ asset('img/403.gif') }}" alt="" width="500">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <h1> <code>&lt;error&gt;</code> 403 <code>&lt;/error&gt;</code> <h1>
                <h3> You're not allowed to access this page.</h3>
                <a class="btn btn-primary btn-lg" href="{{ url()->previous() }}">Return</a>
            </div>
            <div class="col-md-12 text-center">
                <div class="simple-footer mt-5">
                  Copyright &copy; {{ date('Y') }} | {{ config('app.name', 'Laragen Dashboard') }} 
                </div>
            </div>
        </div>
    </div>
@endsection
