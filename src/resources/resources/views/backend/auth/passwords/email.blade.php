@extends('backend.auth.layout')

@section('heading') Reset Password @endsection

@section('content')

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('backend.password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email Address</label>
            <input id="email" type="email" class="form-control @if ($errors->has('email')) is-invalid @endif" name="email" tabindex="1" required autocomplete="email" autofocus>
            @if ($errors->has('email'))
                <span class="invalid-feedback">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <div class="form-group text-right">
            <a href="{{ route('backend.login') }}" class="float-left mt-3">
                Go back
            </a>
            <button type="submit" class="btn btn-primary btn-lg btn-icon icon-right" tabindex="4">
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
@endsection
