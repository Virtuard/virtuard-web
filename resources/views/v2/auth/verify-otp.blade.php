@extends('v2.layouts.auth')

@section('content')
    {{-- VERIFY OTP PAGE --}}
    {{-- Route: GET /verify-otp --}}
    {{-- Submit: POST /verify-otp --}}
    {{-- Resend: POST /resend-otp --}}

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

    <form method="POST" action="{{ route('auth.verify-otp.submit') }}">
        @csrf

        <input type="text" name="otp" maxlength="6" placeholder="{{ __('Enter OTP code') }}" required>

        <button type="submit">{{ __('Confirm') }}</button>
    </form>

    {{-- Resend OTP --}}
    <form method="POST" action="{{ route('auth.resend-otp') }}">
        @csrf
        <button type="submit">{{ __('Resend code') }}</button>
    </form>
@endsection