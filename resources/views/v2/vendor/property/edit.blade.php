@extends('v2.layouts.vendor')

@section('content')
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Edit Property</h1>
    </div>

    {{--
    ========================================================================
    FRONTEND GUIDE: PROPERTY EDIT FORM (V2)
    ========================================================================

    Panduan untuk Frontend Engineer.
    Data Pre-fill Target: `$row`
    Endpoint Target (POST): {{ route('property.store', ['id' => $row->id]) }}

    Cetak value lama property ke dalam input form misal: `value="{{ $row->title }}"`

    Struktur Payload yang dibutuhkan backend:

    1. CONTENT TAB
    - title
    - content
    - video
    - faqs (JSON)
    - bed (Number of rooms)
    - bathroom
    - flooring
    - square_land (Sq Meters Land)
    - square (Sq Meters Built)
    - agency
    - land_registry_category
    - phone
    - website
    - ipanorama_id

    2. LOCATION TAB
    - address, map_lat, map_lng, map_zoom

    3. PRICING TAB
    - price, sale_price

    4. ATTRIBUTES TAB
    - Attributes (Type, Service, dsb)

    5. SEO TAB
    - seo_title, seo_desc, seo_image, seo_share
    ========================================================================
    --}}
@endsection