@extends('v2.layouts.vendor')

@section('content')
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Add New Business</h1>
    </div>

    {{--
    ========================================================================
    FRONTEND GUIDE: BUSINESS ADD FORM (V2)
    ========================================================================

    Panduan untuk tim Frontend Engineer memahat UI form Add Business.
    Endpoint Target (POST): {{ route('business.store') }}

    Struktur Tab (Hanya 4 Tab) & Payload yang dibutuhkan backend:

    1. CONTENT TAB
    - title [type: text, required]
    - content [type: textarea, required]
    - video [type: url, optional] -> Youtube video ID / Link
    - faqs [type: JSON array / array, optional]
    - items [type: JSON array / array, optional] -> Ini adalah bagian Product List (Image, Title, Price) yang nampak di
    mockup
    - Banner Image, Gallery, Featured Image -> Backend file upload handling
    - ipanorama_id [type: select, optional] -> Untuk referensi model asset 360 viewer

    Extra Info:
    - franchising [type: text, optional] -> Franchising Company
    - phone [type: text, optional] -> Phone Number
    - website [type: url, optional] -> Website URL

    2. LOCATION TAB
    - address [type: text, optional]
    - map_lat [type: text, optional]
    - map_lng [type: text, optional]
    - map_zoom [type: number, optional]

    3. ATTRIBUTES TAB
    - Map checkbox untuk "Business Type", "Business Typology", dan "Business Service".
    - Sesuai standar framework Bravo/Booking Core (disimpan dalam relasi term business `bravo_business_term`).

    4. SEO TAB
    - seo_title [type: text, optional]
    - seo_desc [type: textarea, optional]
    - seo_image [type: file/asset_id, optional]
    - seo_share [type: textarea, optional]

    ========================================================================
    --}}
@endsection