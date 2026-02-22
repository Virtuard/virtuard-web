@extends('v2.layouts.vendor')

@section('content')
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Add New Accommodation</h1>
    </div>

    {{--
    ========================================================================
    FRONTEND GUIDE: ACCOMMODATION ADD FORM (V2)
    ========================================================================

    Bagian ini hanya sebagai penanda / panduan untuk tim Frontend Engineer
    membangun UI form sesuai Mockup.

    Endpoint Target (POST): {{ route('accommodation.store') }}

    Struktur Tab & Field Name yang dibutuhkan backend (di Controller store):

    1. CONTENT TAB
    - title [type: text, required]
    - content [type: textarea, required]
    - video [type: url, optional] -> for Youtube link
    - Banner Image, Gallery, Featured Image -> [Gunakan file upload / ID Asset bawaan system]
    - ipanorama_id [type: select, optional] -> Untuk referensi model asset 360 viewer
    - phone [type: text, optional]
    - website [type: url, optional]
    - chain [type: text, optional]
    - Extra Info -> number of rooms dsb, map ke `bed`, `bathroom` dkk di attribute database

    2. LOCATION TAB
    - address [type: text, optional]
    - map_lat [type: text, optional]
    - map_lng [type: text, optional]
    - map_zoom [type: number, optional]

    3. PRICING TAB
    - check_in_time [type: text/time, optional]
    - check_out_time [type: text/time, optional]
    - min_day_stays [type: number, optional]
    - price [type: number, required]
    - enable_extra_price [type: checkbox, on/off]
    - enable_service_fee [type: checkbox, on/off]

    4. ATTRIBUTES TAB
    - Map form checkbox Facilities & Typoology ini ke form array sesuai standard
    HotelTerm attribute table (`bravo_hotel_term`).

    5. ICAL TAB
    - ical_import_url [type: url, optional]

    6. SEO TAB
    - seo_title, seo_desc, seo_image, seo_share [opsional]

    ** Catatan:
    Semua form field POST akan secara strict divalidasi oleh Backend
    sesuai rule di `VendorAccommodationController::store`.

    Gunakan library styling modern / Vue component yang disediakan
    di frontend architecture!
    ========================================================================
    --}}
@endsection