@extends('v2.layouts.auth')

@section('content')
    {{-- REGISTER PAGE --}}
    {{-- Route: GET /register --}}
    {{-- Submit: POST /register --}}

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

    <form method="POST" action="{{ route('auth.register.store') }}">
        @csrf

        <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Username') }}" required>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Email Address') }}" required>
        <input type="password" name="password" placeholder="{{ __('Password') }}" required>
        <input type="password" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required>

        <label>
            <input type="checkbox" name="term" value="1" {{ old('term') ? 'checked' : '' }} required>
            {{ __('I agree to the Terms and Privacy Policy') }}
        </label>

        <button type="submit">{{ __('Sign Up') }}</button>
    </form>

    {{-- Social Login --}}
    <a href="{{ route('auth.social', 'google') }}">{{ __('Google') }}</a>
    <a href="{{ route('auth.social', 'facebook') }}">{{ __('Facebook') }}</a>

    {{-- Navigation --}}
    <a href="{{ route('login') }}">{{ __('Sign In') }}</a>
@endsection