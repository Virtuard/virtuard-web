@extends('v2.layouts.auth')

@section('content')
    {{-- FORGOT PASSWORD PAGE --}}
    {{-- Route: GET /forgot-password --}}
    {{-- Submit: POST /forgot-password --}}

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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Enter your email') }}" required>

        <button type="submit">{{ __('Send Password Reset Link') }}</button>
    </form>

    {{-- Navigation --}}
    <a href="{{ route('login') }}">{{ __('Back to Sign In') }}</a>
@endsection