@extends('v2.layouts.vendor')

@section('content')
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Add New Property</h1>
    </div>

    {{--
    ========================================================================
    FRONTEND GUIDE: PROPERTY ADD FORM (V2)
    ========================================================================

    Bagian ini adalah panduan untuk tim Frontend Engineer memahat UI form Add Property.
    Endpoint Target (POST): {{ route('property.store') }}

    Struktur Tab & Payload yang dibutuhkan backend (sesuai VendorPropertyController::store):

    1. CONTENT TAB
    - title [type: text, required]
    - content [type: textarea, required]
    - video [type: url, optional] -> Youtube video ID / Link
    - faqs [type: JSON array / array, optional]
    - Banner Image, Gallery, Featured Image -> Backend file upload handling
    - ipanorama_id [type: select, optional] -> Untuk referensi model asset 360 viewer

    Extra Info:
    - bed [type: number, optional] -> Number of beds
    - bathroom [type: number, optional] -> Number of bathrooms
    - flooring [type: number, optional] -> Number of flooring
    - square_land [type: number, optional] -> Square meters of land
    - square [type: number, optional] -> Square meters built
    - agency [type: text, optional] -> Agency Name
    - land_registry_category [type: text, optional]
    - phone [type: text, optional] -> Phone Number
    - website [type: url, optional] -> Website URL

    2. LOCATION TAB
    - address [type: text, optional]
    - map_lat [type: text, optional]
    - map_lng [type: text, optional]
    - map_zoom [type: number, optional]

    3. PRICING TAB
    - price [type: number, required] -> Base Price ($)
    - sale_price [type: number, optional] -> Sale Price ($)

    4. ATTRIBUTES TAB
    - Map checkbox untuk "Property Type", "Business Type", dan "Property Service".
    - Sesuai standar framework Bravo/Booking Core (disimpan dalam relasi term space `bravo_space_term`).

    5. SEO TAB
    - seo_title [type: text, optional]
    - seo_desc [type: textarea, optional]
    - seo_image [type: file/asset_id, optional]
    - seo_share [type: textarea, optional]

    ========================================================================
    --}}
@endsection