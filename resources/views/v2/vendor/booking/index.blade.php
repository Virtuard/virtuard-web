@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR BOOKING HISTORY PAGE --}}
    {{-- Route: GET /vendor/booking-history --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    $bookings → LengthAwarePaginator (Gunakan $bookings->links() untuk pagination standard)
    Tiap item di dalam $bookings mengandung:
    id: "#82"
    raw_id: 82
    type: "Accomodation" | "Property" | "Business"
    listing_title: "Tropical Oasis Villa"
    listing_url: "/url-detail-listing"
    order_date: "Aug 18, 2025"
    execution_time: array(
    check_in: "08/28/2025",
    check_out: "08/29/2025",
    duration: "1 nights"
    )
    total: "$66"
    paid: "$0"
    remain: "$66"
    status: "completed" | "processing" | "cancelled" | "paid" | "unpaid" | dsb...
    status_name: "Completed" | "Processing"
    invoice_url: "/user/booking/123/invoice"

    $filters → Array filter aktif saat ini:
    sort: "newest", "oldest", "highest", "lowest"
    listing: "all", "hotel", "space", "business"
    status: status code (e.g. "completed", "all")
    search: keyword (string)

    $listing_types → Opsi dropdown Listing Type
    $statuses → Array string opsi status (bisa di-loop untuk dropdown status)

    ============================================
    CARA MENGGUNAKAN FILTER & PENCARIAN
    ============================================
    Gunakan form method GET ke rute ini:
    GET /vendor/booking-history?sort=...&listing=...&status=...&search=...

    Nilai Parameter:
    - sort: newest, oldest, highest, lowest
    - listing: all, hotel, space, business
    - status: all, completed, processing, confirmed, cancelled, paid, unpaid, dll.
    - search: string kata kunci (bisa cari ID booking atau Judul Listing)
    --}}

    {{-- Placeholder --}}
    <h1>{{ $page_title }}</h1>
    <p>Data total: {{ $bookings->total() }} bookings</p>
@endsection