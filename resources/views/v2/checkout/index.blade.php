@extends('v2.layouts.app')

@section('content')
    {{-- CHECKOUT PLAN PAGE --}}
    {{-- Route: GET /checkout/{planId} --}}
    {{-- Process: POST /checkout/{planId} --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================

    $plan → array
        id, title,
        price, annual_price,
        duration, duration_type, duration_text,
        max_service, max_ipanorama

    $user → array (pre-filled form data)
        Personal Information:
            business_name, user_name, email,
            first_name, last_name,
            phone, birthday

        Location Information:
            address (Address Line 1),
            address2 (Address Line 2),
            city, state, country, zip_code

    $payment → array
        base_price      → harga dasar plan
        discount        → affiliate discount (10% if applicable)
        tax_rate        → tax percentage (0%)
        tax_amount      → calculated tax
        total           → final payment amount
        is_annual       → boolean
        has_affiliate   → boolean

    ============================================
    FORM SUBMISSION:
    ============================================

    Action: POST /checkout/{{ $plan['id'] }}
    Method: POST

    Fields (sesuai design):
        Personal Information:
            business_name   → text (required)
            user_name       → text (required)
            email           → email (required)
            first_name      → text (required)
            last_name       → text (required)
            phone           → text (required, with country code)
            birthday        → date (required)

        Location Information:
            address         → text (Address Line 1, required)
            address2        → text (Address Line 2, optional)
            city            → text (required)
            state           → text (State/Province, required)
            country         → select dropdown (required)
            zip_code        → text (required)

        Terms:
            term_conditions → checkbox (required)

        Hidden:
            annual          → '1' if annual pricing

    ============================================
    PAYMENT FLOW (Midtrans):
    ============================================

    1. User fills form → clicks "Purchase Now"
    2. POST /checkout/{planId} → backend creates PlanPayment + Midtrans Snap Token
    3. Redirect → GET /checkout/confirm/{paymentCode}
    4. Confirm page loads Midtrans Snap.js → payment popup
    5. On success → POST result ke backend → redirect /plan/thank-you

    SIDEBAR "Payment Details":
        Package name:  $plan['title']
        Package price: format_money($payment['base_price'])
        Tax (0%):      format_money($payment['tax_amount'])
        Total Payment: format_money($payment['total'])
        Terms checkbox + link: route('page.terms')
        "Purchase Now" submit button

    CANCEL: "Cancel Order" → back() or route('plan')
    --}}

    {{-- Placeholder --}}
    <h1>{{ $page_title }}</h1>
    <p>Plan: {{ $plan['title'] }} — {{ format_money($payment['total']) }}</p>
@endsection