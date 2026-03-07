
@extends('layouts.app')
@push('css')
    <link href="{{ asset('module/booking/css/checkout.css?_ver='.config('app.asset_version')) }}" rel="stylesheet">
@endpush
@section('content')
@php
$translate = $payment->plan->translate();
if(request()->query('annual')!=1){
    // $price = $hasAffiliatePlan ? $plan->price * 0.9 : $plan->price;
    $duration_text = $payment->plan->duration_type_text;
}else{
    $price = $hasAffiliatePlan ? $plan->annual_price * 0.9 : $plan->annual_price;
    $duration_text = __('Year');
}
    $term_conditions = setting_item('booking_term_conditions');

@endphp
<section class="pricing-section bravo-booking-page">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @include('admin.message')
                    <div class="sec-title text-center mb-5">
                        <h2>Confirmation Virtuard Plan</h2>
                    </div>
                    <div class="pricing-tabs tabs-box">
                        
                         
                            <div class="pricing-table col-12">
                                <div class="inner-box">
                                    <div class="title">{{$payment->plan->title}}</div>
                                    
                                    <div class="price">
                                      
                                        <span>{{ format_money(  $payment->amount) }}</span>
                                        
                                        <span class="duration">/ {{ $payment->plan->duration ?? '-' }} {{ $payment->plan->duration_type}}</span>
                                       
                                        
                                    </div>
                                    {{-- @if($hasAffiliatePlan)
                                        <p class="text-muted" style="color: green;">You are using a price discounted by 10%.</p>
                                        @endif --}}
                                    {{-- <div class="price">{{ format_money($price)}}
                                        @if($price)
                                            <span class="duration">/ {{$duration_text}}</span>
                                        @endif
                                    </div> --}}
                                    <div class="table-content">
                                        {!! clean( $payment->plan->content) !!}
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="form-section col-12">
                                @include('Booking::frontend.booking.checkout-payment')
                            </div> --}}
                            <div class="form-actions col-12">
                                <button href="javascript:void(0);" id="payNowButton" class="btn btn-danger">Purchase Plan</button>
                            </div>
            

                    </div>
                </div>
            </div>
        </div>
    </section>
    @php
    $snapTokena = $payment->getMeta('snap_token');
    $orderId = $payment->code;
    @endphp

    @if (config('midtrans.is_production'))
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @else
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
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
                        window.location.href = '/plan/thank-you';
    
                        fetch('{{ route('midtrans.success.plan') }}', {
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
@section('footer')
@endsection
