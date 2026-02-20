@extends('v2.layouts.auth')

@section('content')
    {{-- LOGIN PAGE --}}
    {{-- Route: GET /login --}}
    {{-- Submit: POST /login --}}

    @if(session('error'))
        <div>{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Email Address') }}" required>
        <input type="password" name="password" placeholder="{{ __('Password') }}" required>

        <label>
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            {{ __('Remember Me') }}
        </label>

        <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>

        <button type="submit">{{ __('Sign In') }}</button>
    </form>

    {{-- Social Login --}}
    <a href="{{ route('auth.social', 'google') }}">{{ __('Google') }}</a>
    <a href="{{ route('auth.social', 'facebook') }}">{{ __('Facebook') }}</a>

    {{-- Navigation --}}
    <a href="{{ route('auth.register') }}">{{ __('Sign Up') }}</a>
@endsection