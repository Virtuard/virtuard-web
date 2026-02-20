@extends('v2.layouts.app')

@section('content')
    {{-- EXPLORE PAGE --}}
    {{-- Route: GET /explore --}}
    {{-- AJAX Search: POST /explore/search --}}
    {{-- AJAX Map: POST /explore/map --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================

    $listings → LengthAwarePaginator of listing items
    Setiap item:
    id, type (hotel/space/business), title, slug, url, image,
    location_name, price, sale_price, price_html,
    bed, bathroom (hotel only),
    review_score, total_review, is_wishlist,
    map_lat, map_lng, has_360, created_at

    Pagination methods:
    $listings->links() → pagination HTML
    $listings->currentPage() → current page number
    $listings->lastPage() → total pages
    $listings->total() → total items

    $filters → Array of filter options for sidebar
    locations → [{id, name}, ...]
    accommodation_types → [{id, name, attr_id}, ...]
    business_types → [{id, name, attr_id}, ...]
    rent_types → [{id, name, attr_id}, ...]
    service_types → [{value, label}, ...] (All Listings, Accommodation, Property, Commercial)
    sort_options → [{value, label}, ...] (Latest, Top Rated, Random)

    $currentFilters → Array of currently active filters
    service_type → 'all' | 'hotel' | 'space' | 'business'
    sort_by → 'latest' | 'top_rated' | 'random'
    location_id → int | null
    keyword → string | null
    proximity → int | null
    has_360 → bool | null
    terms → array of term IDs
    page → int

    AJAX Endpoints:
    POST /explore/search → returns JSON { status, data[], pagination{} }
    POST /explore/map → returns JSON { status, data[] } (map markers with lat/lng)

    Query Parameters (for filtering via URL):
    ?service_type=hotel
    ?sort_by=top_rated
    ?location_id=1
    ?keyword=villa
    ?proximity=10
    ?has_360=1
    ?terms[]=1&terms[]=2
    ?page=2
    --}}

    {{-- Placeholder --}}
    <h1>{{ $page_title }}</h1>
    <p>Showing {{ $listings->total() }} results</p>
@endsection