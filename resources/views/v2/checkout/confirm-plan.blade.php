@extends('v2.layouts.app')

@section('content')
    {{-- CONFIRM PLAN PAYMENT PAGE --}}
    {{-- Route: GET /checkout/confirm/{code} --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================

    $payment → PlanPayment model
    code → order ID (untuk Midtrans)
    amount → jumlah pembayaran
    status → 'draft' | 'pending' | 'completed' | 'failed'
    plan → relasi ke Plan model
    title, price, duration, duration_type, content

    $snapToken → string (Midtrans Snap Token)

    ============================================
    MIDTRANS SNAP INTEGRATION:
    ============================================

    1. Include Midtrans Snap.js:
    - Production: https://app.midtrans.com/snap/snap.js
    - Sandbox: https://app.sandbox.midtrans.com/snap/snap.js
    - Set data-client-key="{{ config('midtrans.client_key') }}"

    2. Trigger payment popup:
    snap.pay('{{ $snapToken }}', {
    onSuccess: function(result) {
    // POST result ke backend
    fetch('{{ route("midtrans.success.plan") }}', {
    method: 'POST',
    headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
    orderId: '{{ $payment->code }}',
    transactionStatus: result.transaction_status,
    paymentType: result.payment_type,
    fraudStatus: result.fraud_status,
    grossAmount: result.gross_amount,
    }),
    });
    // Redirect ke thank you page
    window.location.href = '/plan/thank-you';
    },
    onPending: function(result) {
    // Pembayaran pending (transfer bank, etc)
    },
    onError: function(result) {
    // Payment error
    }
    });

    3. Environment check:
    config('midtrans.is_production') → true/false
    config('midtrans.client_key') → dari .env MIDTRANS_CLIENT_KEY

    CANCEL: Link back ke /plan
    --}}

    {{-- Placeholder --}}
    <h1>Confirmation</h1>
    <p>Plan: {{ $payment->plan->title }}</p>
    <p>Amount: {{ format_money($payment->amount) }}</p>
@endsection