@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR BOOKING REPORT PAGE --}}
    {{-- Route: GET /vendor/booking-report --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    $bookings → LengthAwarePaginator (Gunakan $bookings->links() untuk pagination standard)
    Tiap item di dalam $bookings mengandung:
    id: "#82"
    raw_id: 82
    listing_title: "Tropical Oasis Villa"
    listing_url: "/url-detail-listing"
    customer_name: "Virtuard Tour"
    stay_date: "Aug 18, 2025"
    total: "$66"
    paid: "$0"
    remain: "$66"
    vendor_earning: "$59"
    status: "completed" | "processing" | "cancelled" | "paid" | "unpaid" | dsb...
    status_name: "Completed" | "Processing"
    detail_url: "/vendor/booking-report/82" <-- Link aksi (mata biru) $filters → Array filter aktif saat ini: sort: "newest"
        , "oldest" , "highest" , "lowest" status: status code (e.g. "completed" , "all" ) search: keyword (string) $statuses
        → Array string opsi status (bisa di-loop untuk dropdown status)============================================CARA
        MENGGUNAKAN FILTER & PENCARIAN============================================Gunakan form method GET ke rute ini: GET
        /vendor/booking-report?sort=...&status=...&search=... Nilai Parameter: - sort: newest, oldest, highest, lowest -
        status: all, completed, processing, confirmed, cancelled, paid, unpaid, dll. - search: string kata kunci (bisa cari
        ID booking atau Judul Listing / Nama Customer) --}} {{-- Placeholder --}} <h1>{{ $page_title }}</h1>
        <p>Data total: {{ $bookings->total() }} reports</p>
@endsection