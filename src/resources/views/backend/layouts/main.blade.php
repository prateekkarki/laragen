@extends('backend.layouts.app')

@section('layout')
    @include('backend.includes.header')

    <div class="app-body">
        @include('backend.includes.sidebar')

        <main class="main">
            
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            </ol>

            <div class="container-fluid">
                <div class="animated fadeIn">
                    @yield('content')
                </div>
            </div>
        </main>

        @include('backend.includes.aside')
    </div>

    @include('backend.includes.footer')
@endsection
