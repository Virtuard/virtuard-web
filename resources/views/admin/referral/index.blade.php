@extends('admin.layouts.app')
@section('content')
    <h2 class="title-bar no-border-bottom">
        {{ __('Referral Report') }}
    </h2>

    @include('admin.message')

    <div class="booking-history-manager mt-3">
        <div class="tabbable">
            <ul class="nav nav-tabs ht-nav-tabs">
                <li class="active">
                    <a>
                        {{ __('Plan') }}
                    </a>
                </li>
            </ul>
            <div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-booking-history">
                        <thead>
                            <tr>
                                <th>{{ __('#') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Plan') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Referral Name') }}</th>
                                <th>{{ __('Referral Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($referals && $referals->isNotEmpty())
                                @foreach ($referals as $key => $booking)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $booking->user->name ?? '' }}</td>
                                        <td>{{ $booking->plan->title ?? '' }}</td>
                                        <td>{{ format_money($booking->price ?? 0) }}</td>
                                        <td>{{ $booking->referalName->name ?? '' }}</td>
                                        <td>{{ format_money($booking->referal_amount ?? 0) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6">{{ __('No Referral Report') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <div class="pagination-wrapper">
                        {{ $referals->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" tabindex="-1" id="modal_booking_detail">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Booking ID: #') }} <span class="user_id"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-center">{{ __('Loading...') }}</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
