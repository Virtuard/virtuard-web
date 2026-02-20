@extends('v2.layouts.app')

@section('content')
    {{-- MEMBER DETAIL PAGE --}}
    {{-- Route: GET /member/{username} --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================

    $profile → array
    id, name, user_name, bio, business_name,
    photo_profile (full-size avatar URL),
    email, posts_count, followers_count, following_count,
    is_following (boolean, apakah login user sudah follow member ini),
    social_links → array of: website, instagram, facebook, twitter, linkedin
    created_at

    $currentTab → 'profile' | 'virtual_tour' | 'accommodation' | 'property' | 'commercial_activities'

    TABS (via ?tab=):
    profile → user's posts (paginated)
    virtual_tour → user's ipanorama/360° tours
    accommodation → user's hotel listings
    property → user's space/property listings
    commercial_activities → user's business listings

    $tabData → varies per tab:

    TAB: profile (default)
    → LengthAwarePaginator of posts
    Setiap post: id, message, type_post, created_at,
    likes_count, comments_count, is_liked,
    medias[] → { id, url, type, is_360_media },
    author → { id, name, user_name, photo_profile }

    TAB: virtual_tour
    → Collection of: id, title, thumb (URL), code, uuid, created_at

    TAB: accommodation / property / commercial_activities
    → Collection of: id, title, slug, url, image,
    location_name, price, sale_price, price_html,
    bed, bathroom, review_score, total_review,
    is_wishlist, created_at

    INTERACTION APIs (existing):
    POST /api/follow/{user_id} → toggle follow
    POST /api/post/{id}/like → toggle like
    POST /api/post/{id}/comment → add comment
    --}}

    {{-- Placeholder --}}
    <h1>{{ $profile['name'] }}</h1>
    <p>@{{ $profile['user_name'] }}</p>
    <p>Tab: {{ $currentTab }}</p>
@endsection