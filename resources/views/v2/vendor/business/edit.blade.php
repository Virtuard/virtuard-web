@extends('v2.layouts.vendor')

@section('content')
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Edit Business</h1>
    </div>

    {{--
    ========================================================================
    FRONTEND GUIDE: BUSINESS EDIT FORM (V2)
    ========================================================================

    Panduan untuk Frontend Engineer.
    Data Pre-fill Target: `$row`
    Endpoint Target (POST): {{ route('business.store', ['id' => $row->id]) }}

    Cetak value lama business ke dalam input form misal: `value="{{ $row->title }}"`

    Struktur Tab (Hanya 4 Tab) & Payload yang dibutuhkan backend:

    1. CONTENT TAB
    - title
    - content
    - video
    - faqs (JSON array)
    - items (JSON array) -> Ini Product list (Image, Title, Price) terikat pada objek item.
    - franchising
    - phone
    - website
    - ipanorama_id

    2. LOCATION TAB
    - address, map_lat, map_lng, map_zoom

    3. ATTRIBUTES TAB
    - Business Attributes (Type, Typology, Service, dsb)

    4. SEO TAB
    - seo_title, seo_desc, seo_image, seo_share
    ========================================================================
    --}}
@endsection