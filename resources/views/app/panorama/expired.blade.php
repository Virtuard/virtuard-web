@extends ('layouts.app')
@section('content')
    <div class="bravo_detail_event">
        @include('Layout::parts.bc')
        <div class="bravo_content">
            <div class="container">
                <div class="row my-2">
                    <div class="col-md-12 text-center expired-plan">
                        <h2>{{__('Subscription Expired')}}</h2>
                        <p class="py-3">{{__('This virtual tour has expired. If you is owner of this virtual tour, please upgrade your subscription to continue using the service.')}}</p>
                        <a href="{{ route('user.plan') }}" class="btn btn-primary">{{__('Upgrade Subscription')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <style>
        .expired-plan {
            min-height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
    </style>
@endpush

