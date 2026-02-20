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
    base_price → harga dasar plan
    discount → affiliate discount (10% if applicable)
    tax_rate → tax percentage (0%)
    tax_amount → calculated tax
    total → final payment amount
    is_annual → boolean
    has_affiliate → boolean

    FORM ACTION:
    POST /checkout/{{ $plan['id'] }}
    Fields (matching design):
    business_name, user_name, email,
    first_name, last_name, phone, birthday,
    address, address2, city, state, country, zip_code,
    term_conditions (checkbox, required),
    annual (hidden, if annual pricing)

    CANCEL: Link to /plan or back()

    SIDEBAR "Payment Details":
    Package name: $plan['title']
    Tax: $payment['tax_rate']% → $payment['tax_amount']
    Total Payment: $payment['total']
    Terms checkbox + link to terms page
    "Purchase Now" submit button
    --}}

    {{-- Placeholder --}}
    <h1>{{ $page_title }}</h1>
    <p>Plan: {{ $plan['title'] }} — {{ format_money($payment['total']) }}</p>
@endsection