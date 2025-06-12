@extends('layouts.app')
@push('css')
    <link href="{{ asset('module/booking/css/checkout.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">
@endpush
@section('content')
    <div class="bravo-booking-page padding-content" >
        <div class="container">
            <div class="row booking-success-notice">
                <div class="col-lg-8 col-md-8">
                    <div class="d-flex align-items-center">
                        <img loading='lazy' src="{{url('images/ico_success.svg')}}" alt="Payment Success">
                        <div class="notice-success">
                            <p class="line1"><span>{{$booking->first_name}},</span>
                                {{__('your booking confirmation ')}}
                            </p>
                            <p class="line2">{{__('Booking details has been sent to:')}} <span>{{$booking->email}}</span></p>
                            {{-- @if($note = $gateway->getOption("payment_note"))
                                <div class="line2">{!! clean($note) !!}</div>
                            @endif --}} 
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <ul class="booking-info-detail">
                        <li><span>{{__('Booking Number')}}:</span> {{$booking->id}}</li>
                        <li><span>{{__('Booking Date')}}:</span> {{display_date($booking->created_at)}}</li>
                        @if(!empty($gateway))
                        <li><span>{{__('Payment Method')}}:</span> {{$gateway->name}}</li>
                        @endif
                        <li><span>{{__('Booking Status')}}:</span> {{ $booking->status_name }}</li>
                    </ul>
                </div>
            </div>
            <div class="row booking-success-detail">
                <div class="col-md-8">
                    @include ($service->booking_customer_info_file ?? 'Booking::frontend/booking/booking-customer-info')
                    <div class="text-center">
                        <a href="javascript:void(0);" id="payNowButton" class="btn btn-primary">{{__('Payout Booking')}}</a>
                    </div>
                </div>
                <div class="col-md-4">
                    @include ($service->checkout_booking_detail_file ?? '')
                </div>
            </div>
        </div>
    </div>
    
    @php
    $snapTokena = $booking->getMeta('snap_token');
    $orderId = $booking->code;
@endphp


<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    window.onload = function() {
        document.getElementById('payNowButton').addEventListener('click', function(e) {
            e.preventDefault();

            const snapToken = '{{ $snapTokena }}';
            const orderId = '{{ $orderId }}';

            if (!snapToken) {
                alert('Snap token not found!');
                return;
            }   

            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Payment Success:', result);
                    window.location.href = '/thankyou/booking';

                    fetch('{{ route('midtrans.notification') }}', {
                            method: 'POST',
                            headers: {
                            'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            orderId: orderId,
                            transactionStatus: result.transaction_status,
                            paymentType: result.payment_type,
                            fraudStatus: result.fraud_status,
                            grossAmount: result.gross_amount,
                        }),
                    })
                    .then(response => response.json())
                    .then(data => console.log('Booking updated:', data))
                    .catch(error => console.error('Error updating booking:', error));
                },

                onPending: function(result) {
                    console.log('Payment Pending:', result);
                    // alert('Pembayaran sedang diproses.');
                },
                onError: function(result) {
                    console.error('Payment Error:', result);
                    // alert('Terjadi kesalahan saat pembayaran.');
                }
            });
        });
    };
</script>
@endsection


