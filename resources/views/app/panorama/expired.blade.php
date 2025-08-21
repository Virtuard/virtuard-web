@extends ('layouts.app')
@section('content')
    <div class="bravo_detail_event">
        @include('Layout::parts.bc')
        <div class="bravo_content">
            <div class="container">
                <div class="row my-2">
                    <div class="col-md-12 text-center expired-plan">
                        <h2>Subscription Expired</h2>
                        <p>Your subscription has expired. Please upgrade to continue using the service.</p>
                        <a href="{{ route('user.plan') }}" class="btn btn-primary">Upgrade Subscription</a>
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

