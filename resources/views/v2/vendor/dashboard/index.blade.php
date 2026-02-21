@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR DASHBOARD PAGE --}}
    {{-- Route: GET /vendor/dashboard --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================

    $analytics → array
    total_pending → (string/formatted money) "150.00"
    total_earnings → (string/formatted money) "235.00"
    total_bookings → (int) 218
    total_services → (int) 16

    $chart → array
    filter → string (current active filter, e.g. 'last_7_days')
    from_date → string (Y-m-d)
    to_date → string (Y-m-d)
    labels → array of strings (e.g. ["12/14/2025", "12/15/2025", ...])
    earnings → array of numbers (e.g. [0.2, 0.5, 1.0, 0.8, ...])

    ============================================
    DATE FILTER CHART AJAX API:
    ============================================

    Untuk update chart saat user mengganti filter (tanpa reload page):

    Endpoint: GET /vendor/dashboard
    Query Params:
    chart_filter → 'today' | 'yesterday' | 'last_7_days' | 'last_30_days' | 'this_week' | 'this_month' | 'last_month' |
    'this_year' | 'custom_range'
    from → (only if custom_range) e.g., '2025-12-14'
    to → (only if custom_range) e.g., '2025-12-20'

    Header: X-Requested-With: XMLHttpRequest (wajib untuk trigger AJAX response)

    Response JSON:
    {
    "status": true,
    "chart": {
    "filter": "last_7_days",
    "from_date": "2025-12-14",
    "to_date": "2025-12-20",
    "labels": ["12/14/2025", "12/15/2025", ...],
    "earnings": [0.2, 0.5, 1.0, 0.8, 0.3, 0.6, 0.7] // Gunakan data ini untuk render bar chart
    }
    }
    --}}

    {{-- Placeholder --}}
    <h1>{{ $page_title }}</h1>
    <p>Total Bookings: {{ $analytics['total_bookings'] }}</p>
@endsection