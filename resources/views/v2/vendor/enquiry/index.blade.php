@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR ENQUIRY REPORT PAGE --}}
    {{-- Route: GET /vendor/enquiry-report --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    $enquiries → LengthAwarePaginator (Gunakan $enquiries->links() untuk pagination standard)
    Tiap item di dalam $enquiries mengandung:
    id: "#82"
    raw_id: 82
    service_title: "Tropical Oasis Villa"
    customer_name: "Andi Irfan"
    date: "Aug 18, 2025"
    replies_count: 4
    status: "completed" | "pending"
    status_name: "Completed" | "Pending"
    detail_url: "/vendor/enquiry-report/82/reply" <-- Link aksi melihat & membalas (mata biru / balon chat) $filters → Array
        filter aktif saat ini: sort: "newest" , "oldest" , "most_replies" , "least_replies" status: status code
        (e.g. "completed" , "pending" , "all" ) search: keyword (string id / customer name) $statuses → Array string opsi
        status============================================CARA MENGGUNAKAN FILTER &
        PENCARIAN============================================Gunakan form method GET ke rute ini: GET
        /vendor/enquiry-report?sort=...&status=...&search=... Nilai Parameter: - sort: newest, oldest, most_replies,
        least_replies - status: all, completed, pending, dsb - search: string kata kunci pencarian Note: Action button di
        mockup (Mata untuk detail report, Balon chat untuk membalas) bisa menggunakan satu URL action yang sama yaitu
        `detail_url` karena UI membalas berada di dalam halaman yang sama. --}} {{-- Placeholder --}} <h1>{{ $page_title }}
        </h1>
        <p>Data total: {{ $enquiries->total() }} enquiries</p>
@endsection