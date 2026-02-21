@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR BOOKING REPORT DETAIL PAGE (3 TABS) --}}
    {{-- Route: GET /vendor/booking-report/{id} --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    $booking → Object Model lengkap Bravo Booking
    $summary → Array berisi ringkasan detail pemesanan (sudah diformat)
    id
    status
    status_name
    order_date
    vendor_name
    check_in
    check_out
    nights
    adults
    total
    paid
    remain

    $personal_info → Array berisi ringkasan data customer / pemesan utama
    first_name
    last_name
    email
    phone
    address
    address2
    city
    state
    country (Nama negara diformat dari custom function)
    zip_code

    Note: Sama seperti Booking Detail biasa, tab "Guest Information" dapat menggunakan variabel `$personal_info`.
    --}}

    {{-- Placeholder --}}
    <h1>{{ $page_title }}</h1>

    <h2>Tabs UI here</h2>
    <ul>
        <li>Booking Detail</li>
        <li>Personal Information</li>
        <li>Guests Information</li>
    </ul>

    <h3>Summary Dump:</h3>
    <pre>@json($summary, JSON_PRETTY_PRINT)</pre>
@endsection