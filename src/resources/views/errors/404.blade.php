@extends('backend.layouts.app')

@push('page_styles')
<style>
    body {
        background-color: #fff !important;
    }
</style>
@endpush

@section('layout')
    <div class="container mt-1">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                <div class="page-error">
                    <div class="page-inner">
                        <h3>HuH? ... How did you get here?</h3>

                        <div class="404-image">
                            <img class="img-fluid" src="{{ asset('img/404.gif') }}" alt="" width="500">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <h1> <code>&lt;error&gt;</code> 404 <code>&lt;/error&gt;</code> <h1>
                <h3> The page you were looking for could not be found.</h3>
                <a class="btn btn-primary btn-lg" href="{{url('/')}}">Return</a>
            </div>
            <div class="col-md-12 text-center">
                <div class="simple-footer mt-5">
                  Copyright &copy; {{ date('Y') }} | {{ config('app.name', 'Laragen Dashboard') }} 
                </div>
            </div>
        </div>
    </div>
@endsection
