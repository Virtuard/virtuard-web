@extends('layouts.user')
@section('content')
    <h2 class="title-bar no-border-bottom">
        Plan Status
    </h2>
    @include('admin.message')

    @if (!empty($data) && isset($data))
        @if ($data->status === 'PENDING' || $data->status === 'REJECTED' || $data->status === 'STOP')
            <div class="alert alert-danger" role="alert">
                Your service is not active yet, please subscribe to our plan. <a href="{{ route('user.plan') }}">Click
                    here</a> to
                subscribe.
            </div>
        @endif
    @else
        <div class="alert alert-danger" role="alert">
            Your service is not active yet, please subscribe to our plan. <a href="{{ route('user.plan') }}">Click
                here</a> to
            subscribe.
        </div>
    @endif
    <div class="border rounded text-center p-4">

        @if (!empty($data) && isset($data))
            @if ($data->status === 'PENDING')
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
                <h1>Locked feature</h1>
                <p>Your service is being processed for validation</p>
            @elseif($data->status === 'SUCCESS')
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-unlock"></i></span>
                <h1>Unlocked feature</h1>
                <p>Your service is active until {{ $data->expired_date }}</p>
            @elseif($data->status === 'REJECTED')
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
                <h1>Locked feature</h1>
                <p>Your application was rejected, please reapply</p>

                <a href="{{ route('user.plan') }}" class="btn btn-primary">
                    Subscribe
                </a>
            @elseif($data->status === 'STOP')
                <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
                <h1>Locked feature</h1>
                <p>Please activate the service by making a payment</p>

                <a href="{{ route('user.plan') }}" class="btn btn-primary">
                    Subscribe
                </a>
            @endif
        @else
            <span class="icon text-center" style="font-size: 5rem;"><i class="fa fa-lock"></i></span>
            <h1>Locked feature</h1>
            <p>Please activate the service by making a payment</p>

            <a href="{{ route('user.plan') }}" class="btn btn-primary">
                Subscribe
            </a>
        @endif
    </div>
@endsection
