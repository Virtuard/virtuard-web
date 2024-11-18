    @extends('layouts.user')
    @section('content')
        <h2 class="title-bar no-border-bottom">
            {{ __('Referral Report') }}
        </h2>
        @include('admin.message')
        <div class="booking-history-manager">
            <div class="tabbable">
                <ul class="nav nav-tabs ht-nav-tabs">
                    {{-- <li class="nav-item">
                        <a href="javascript:void(0);" id="referalServicesTab" onclick="showContent('referalServices')"
                            >
                            {{ __('Referal Services') }}
                        </a>
                    </li> --}}
                    <li class="active">
                        <a>
                            {{ __('Plan') }}
                        </a>
                    </li>
                    <li class="pull-right" style="margin-left: auto;">
                        @if (Auth::user()->role_id == 2)
                            <button id="copyButton" class="btn btn-link"
                                style="border: none; text-decoration: none; color: inherit;">
                                <i class="fa fa-copy"></i> Copy Referral Link
                            </button>
                        @endif
                    </li>
                </ul>
                <div>
                    <div class="table-responsive">
                        @if (Auth::user()->role_id == 2)
                            <table class="table table-bordered table-striped table-booking-history">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Plan') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($referals && $referals->isNotEmpty())
                                        @foreach ($referals as $booking)
                                            <tr>
                                                <td>{{ $booking->user->name }}</td>
                                                <td>{{ $booking->plan->title }}</td>
                                                <td>{{ format_money($booking->referal_amount) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3">{{ __('No Referral Report') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="pagination-wrapper">
                                {{ $referals->links() }}
                            </div>
                        @elseif (Auth::user()->role_id == 1)
                            <table class="table table-bordered table-striped table-booking-history">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">{{ __('Status') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Plan') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Referal Share Name') }}</th>
                                        <th>{{ __('Referal Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($referals && $referals->isNotEmpty())
                                        @foreach ($referals as $booking)
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <span
                                                        style="
                                                            display: inline-block;
                                                            width: 15px;
                                                            height: 15px;
                                                            border-radius: 50%;
                                                            background-color: {{ $booking->referal_user_id ? 'green' : 'orange' }};
                                                        "></span>
                                                </td>
                                                <td>{{ $booking->user->name }}</td>
                                                <td>{{ $booking->plan->title }}</td>
                                                <td>{{ format_money($booking->price) }}</td>
                                                <td>{{ $booking->referalName->name ?? 'Direct' }}</td>
                                                <td>{{ format_money($booking->referal_amount ?? '-') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4">{{ __('No Referral Report') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="pagination-wrapper">
                                {{ $referals->links() }}
                            </div>
                        @elseif (Auth::user()->role_id == 3)
                            <p>{{ __('No Referral Report') }}</p>
                        @endif
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
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h2 class="title-bar no-border-bottom">
        </h2>
        @include('admin.message')
        <div class="booking-history-manager">
            <div class="tabbable">
                <ul class="nav nav-tabs ht-nav-tabs">
                    <li class="active">
                        <a>
                            {{ __('Services') }}
                        </a>
                    </li>
                    <li class="pull-right" style="margin-left: auto;">
                        @if (Auth::user()->role_id == 2)
                            <button id="copyButton" class="btn btn-link"
                                style="border: none; text-decoration: none; color: inherit;">
                                <i class="fa fa-copy"></i> Copy Referral Link
                            </button>
                        @endif
                    </li>
                </ul>
                <div>
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
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('Close') }}</button>
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
       
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $('#modal_booking_detail').on('show.bs.modal', function(e) {
                var btn = $(e.relatedTarget);
                $(this).find('.user_id').html(btn.data('id'));
                $(this).find('.modal-body').html(
                    '<div class="d-flex justify-content-center">{{ __('
                                        Loading...') }}</div>');
                var modal = $(this);
                $.get(btn.data('ajax'), function(html) {
                    modal.find('.modal-body').html(html);
                })
            })
        </script>
        <script>
            document.getElementById('copyButton').addEventListener('click', function() {
                const domain = window.location.origin;
                const userInfo = @json($userInfo);

                const username = `${userInfo.userId}_${userInfo.username}`;
                const referralLink = `${domain}/affiliate-${username}`;

                const tempInput = document.createElement('input');
                document.body.appendChild(tempInput);
                tempInput.value = referralLink;
                tempInput.select();
                tempInput.setSelectionRange(0, 99999);

                document.execCommand('copy');
                document.body.removeChild(tempInput);

                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Referral link copied to clipboard.',
                    confirmButtonText: 'OK',
                    timer: 2000,
                    position: 'top-end',
                    toast: true,
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            });
        </script>
    @endpush
