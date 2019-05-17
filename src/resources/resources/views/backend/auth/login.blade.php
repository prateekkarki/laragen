@extends('backend.auth.layout')

@section('content')
<div class="p-4 m-3">
    <img src="{{ asset('img/stisla-fill.svg') }}" title="Company Logo" alt="logo" width="80" class="shadow-light rounded-circle mb-5 mt-2">
    <img src="{{ asset('img/stisla-fill.svg') }}" title="Client Logo" alt="logo" width="80" class="shadow-light rounded-circle mb-5 mt-2 mr-3"> 
    <h4 class="text-dark font-weight-normal">Welcome to <span class="font-weight-bold">{{ config('app.name', 'Laragen Dashboard') }}</span></h4>
    <p class="text-muted">Login with admin credentials to get started!</p>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form method="POST" action="{{ route('login.post') }}" class="needs-validation" novalidate="">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
            <div class="invalid-feedback">
                Please fill in your email
            </div>
        </div>

        <div class="form-group">
            <div class="d-block">
                <label for="password" class="control-label">Password</label>
            </div>
            <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
            <div class="invalid-feedback">
                Please fill in your password
            </div>
        </div>

        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me" {{ old('remember') ? 'checked' : '' }}>
                <label class="custom-control-label" for="remember-me">Remember Me</label>
            </div>
        </div>

        <div class="form-group text-right">
            <a href="{{ route('password.request') }}" class="float-left mt-3">
                Forgot Password?
            </a>
            <button type="submit" class="btn btn-primary btn-lg btn-icon icon-right" tabindex="4">
                Login
            </button>
        </div>
    </form>

    <div class="text-center mt-5 text-small">
        Copyright &copy; {{ date('Y') }} | {{ config('app.name', 'Laragen Dashboard') }}
    </div>
</div>
@endsection
