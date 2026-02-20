@extends('v2.layouts.auth')

@section('content')
    {{-- RESET PASSWORD PAGE --}}
    {{-- Route: GET /reset-password/{token}?email=xxx --}}
    {{-- Submit: POST /reset-password --}}

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

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token ?? '' }}">
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

        <input type="password" name="password" placeholder="{{ __('New Password') }}" required>
        <input type="password" name="password_confirmation" placeholder="{{ __('Confirm New Password') }}" required>

        <button type="submit">{{ __('Change Password') }}</button>
    </form>
@endsection