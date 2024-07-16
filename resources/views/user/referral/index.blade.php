@extends('layouts.user')
@section('content')
    <h2 class="title-bar no-border-bottom">
        {{ __('Referral Report') }}
    </h2>
    @include('admin.message')
    <div class="booking-history-manager">
        <div class="tabbable">
            <ul class="nav nav-tabs ht-nav-tabs">
                <?php $status_type = Request::query('status'); ?>
                <li class="@if (empty($status_type)) active @endif">
                    <a href="">{{ __('All Referral') }}</a>
                </li>
            </ul>
            @if (!empty($bookings) and $bookings->total() > 0)
                <div class="tab-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-booking-history">
                            <thead>
                                <tr>
                                    <th width="2%">{{ __('Type') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th class="a-hidden">{{ __('Order Date') }}</th>
                                    <th>{{ __('Commission') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr>
                                        <td class="booking-history-type">
                                            @if ($service = $booking->service)
                                                <i class="{{ $service->getServiceIconFeatured() }}"></i>
                                            @endif
                                            <small>{{ $booking->object_model }}</small>
                                        </td>
                                        <td>
                                            @if ($service = $booking->service)
                                                <a target="_blank" href="{{ $service->getDetailUrl() }}">
                                                    {{ $service->title }}
                                                </a>
                                            @else
                                                {{ __('[Deleted]') }}
                                            @endif
                                        </td>
                                        <td class="a-hidden">{{ display_date($booking->created_at) }}</td>
                                        <td>
                                            {{ format_money($booking->ref_commission) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="bravo-pagination">
                        {{ $bookings->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                {{ __('No Referral Report') }}
            @endif
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

@push('js')
    <script>
        $('#modal_booking_detail').on('show.bs.modal', function(e) {
            var btn = $(e.relatedTarget);
            $(this).find('.user_id').html(btn.data('id'));
            $(this).find('.modal-body').html(
                '<div class="d-flex justify-content-center">{{ __('Loading...') }}</div>');
            var modal = $(this);
            $.get(btn.data('ajax'), function(html) {
                modal.find('.modal-body').html(html);
            })
        })
    </script>
@endpush
