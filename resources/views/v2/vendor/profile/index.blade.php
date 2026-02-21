@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR PROFILE SETTING PAGE --}}
    {{-- Route: GET /vendor/profile --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    $user → array berisi data user saat ini
    id
    first_name
    last_name
    business_name
    user_name
    email
    birthday (Format YYYY-MM-DD)
    website_url
    instagram_url
    facebook_url
    twitter_url
    linkedin_url
    bio
    address
    address2
    city
    state
    country
    zip_code
    avatar_url (URL gambar avatar full size)
    is_verified (1 = terverifikasi, 0 = belum)

    ============================================
    API ENDPOINTS UNTUK FORM:
    ============================================

    1. UPDATE PROFILE DATA (AJAX POST)
    -----------------------------------
    Endpoint: POST /vendor/profile
    Headers: X-CSRF-TOKEN
    Fields:
    first_name, last_name, business_name, user_name, email, birthday,
    website_url, instagram_url, facebook_url, twitter_url, linkedin_url,
    bio, address, address2, city, state, country, zip_code
    avatar_id (opsional, ID dari media model hasil upload)
    Response JSON:
    { status: true, message: "Profile updated successfully", user: { name: "...", avatar_url: "..." } }
    atau 422 { status: false, message: "Validation error..." }

    2. CHANGE PASSWORD (AJAX POST)
    -----------------------------------
    Endpoint: POST /vendor/profile/password
    Headers: X-CSRF-TOKEN
    Fields:
    current_password
    new_password (minimal 8 karakter, huruf besar & kecil, angka, simbol)
    new_password_confirmation
    Response JSON:
    { status: true, message: "Password changed successfully!" }
    atau 422 { status: false, message: "..." }

    3. ACCOUNT VERIFICATION (AJAX POST)
    -----------------------------------
    Endpoint: POST /vendor/profile/verify
    Headers: X-CSRF-TOKEN
    Fields:
    id_card_media_id (integer - ID file upload KTP yang sudah berhasil diupload via endpoint media)
    phone (string awalan kode negara '+62812...')
    Response JSON:
    { status: true, message: "Verification data submitted..." }
    atau 422 error

    ============================================
    BUTTON PREVIEW PROFILE:
    ============================================
    Button 'Preview Profile' di sidebar seharusnya me-redirect ke halaman profile publik merchant.
    Gunakan URL / route ini:
    href="{{ route('profile.preview') }}"
    Ini akan mengalihkan vendor ke `/member/username-mereka`.
    --}}

    {{-- Placeholder --}}
    <h1>{{ $page_title }}</h1>
    <p>Welcome back, {{ $user['user_name'] }}</p>
@endsection