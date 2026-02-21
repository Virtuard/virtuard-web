@extends('v2.layouts.app')

@section('content')
    {{-- LISTING DETAIL PAGE (SHARED TEMPLATE) --}}
    {{-- Routes: --}}
    {{-- GET /hotel/{slug} → hotel --}}
    {{-- GET /space/{slug} → property --}}
    {{-- GET /business/{slug} → business --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================

    $type → 'hotel' | 'space' | 'business'

    $listing → array
    id, title, slug, content (HTML description),
    image (main image URL), gallery[] (array of image URLs),
    address, map_lat, map_lng, map_zoom,
    location_name,
    price, sale_price, price_html,
    bed, bathroom, square, max_guests,
    is_wishlist (boolean),
    has_360 (boolean), ipanorama_url,
    share_url (full URL for share popup),
    created_at

    $host → array | null
    id, name, photo_profile, member_since

    $review → array
    score_total → rata-rata score (e.g. 4.8)
    total_review → jumlah review
    rate_score → breakdown per criteria
    list → Collection of Review model objects
    Each review: id, title, content, score, author_ip,
    create_user (reviewer), created_at,
    replies, rate_number[]

    $terms → Collection (grouped by attr_id)
    Each group: [ { id, name, icon }, ... ]
    Contoh: amenities, facilities, room features

    $booking_data → array (pricing/room data from model)

    $related → array of related listings
    Each: id, title, slug, url, image, location_name,
    price, price_html, review_score, total_review, is_wishlist

    $seo_meta → array (title, desc, keywords, image)

    $row → Eloquent model (raw, for advanced usage)

    ============================================
    SIDEBAR (sesuai design):
    ============================================

    "Hosted By" section:
    $host['name'], $host['photo_profile'], $host['member_since']
    "Message Host" button → existing chat system or enquiry popup

    Actions:
    ♡ Wishlist → POST /wishlist (existing API)
    📍 Direction → open Google Maps with $listing['map_lat'], $listing['map_lng']
    ↗ Share → popup (frontend-only, lihat di bawah)

    ============================================
    SEND ENQUIRY POPUP:
    ============================================

    Endpoint: POST /enquiry/send
    Headers: X-CSRF-TOKEN, Content-Type: application/json

    Fields:
    object_id → $listing['id'] (integer, required)
    object_model → $type ('hotel'|'space'|'business', required)
    name → Username (string, required, max 255)
    email → Email Address (string, required, email)
    phone → Phone Number (string, required, max 50)
    note → Note/message (string, optional, max 500, "0/200" counter)

    Pre-fill from auth user:
    name → {{ auth()->user()->name }}
    email → {{ auth()->user()->email }}
    phone → {{ auth()->user()->phone }}

    Response JSON:
    { status: true, message: "Enquiry sent successfully!..." }

    ============================================
    SHARE LINK POPUP (frontend-only):
    ============================================

    Title: "Share This {{ ucfirst($type) }}"
    Description: "Choose a platform or copy the link below to share."

    Share URL: $listing['share_url']

    Social platforms (URL format):
    Facebook: https://facebook.com/sharer/sharer.php?u={url}
    X/Twitter: https://twitter.com/intent/tweet?url={url}&text={title}
    WhatsApp: https://wa.me/?text={title}+{url}
    Telegram: https://t.me/share/url?url={url}&text={title}
    Pinterest: https://pinterest.com/pin/create/button/?url={url}&description={title}
    LinkedIn: https://linkedin.com/sharing/share-offsite/?url={url}
    Tumblr: https://tumblr.com/widgets/share/tool?canonicalUrl={url}
    VKontakte: https://vk.com/share.php?url={url}&title={title}
    Email: mailto:?subject={title}&body={url}

    "Or copy link" → input field with copy button

    ============================================
    WRITE A REVIEW FORM:
    ============================================

    Existing endpoint: POST /review (from Review module)
    Fields: object_id, object_model, content, rate_number, title
    Auth required.
    --}}

    {{-- Placeholder --}}
    <h1>{{ $listing['title'] }}</h1>
    <p>Type: {{ $type }}</p>
@endsection