@extends('backend.auth.layout')

@section('heading') Login with admin credentials to get started! @endsection

@section('content')
<form method="POST" action="{{ route('backend.login.post') }}" class="needs-validation" novalidate="">
    @csrf
    <div class="form-group">
        <label for="email">Email</label>
        <input id="email" type="email" class="form-control @if ($errors->has('email')) is-invalid @endif" name="email" tabindex="1" required autofocus>
        @if ($errors->has('email'))
            <span class="invalid-feedback">{{ $errors->first('email') }}</span>
        @endif
    </div>

    <div class="form-group">
        <div class="d-block">
            <label for="password" class="control-label">Password</label>
        </div>
        <input id="password" type="password" class="form-control @if ($errors->has('password')) is-invalid @endif" name="password" tabindex="2" required>

        @if ($errors->has('password'))
            <span class="invalid-feedback">{{ $errors->first('password') }}</span>
        @endif
    </div>

    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me" {{ old('remember') ? 'checked' : '' }}>
            <label class="custom-control-label" for="remember-me">Remember Me</label>
        </div>
    </div>

    <div class="form-group text-right">
        <a href="{{ route('backend.password.request') }}" class="float-left mt-3">
            Forgot Password?
        </a>
        <button type="submit" class="btn btn-primary btn-lg btn-icon icon-right" tabindex="4">
            Login
        </button>
    </div>
</form>
@endsection
