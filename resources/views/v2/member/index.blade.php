@extends('v2.layouts.app')

@section('content')
    {{-- ALL MEMBERS PAGE --}}
    {{-- Route: GET /member --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================

    $members → LengthAwarePaginator
    Setiap item (array):
    id → user ID
    name → display name (business_name or first+last)
    user_name → @username
    bio → user bio/description
    business_name → business/role title
    photo_profile → avatar URL
    posts_count → jumlah posts
    followers_count → jumlah followers
    following_count → jumlah following
    last_login_at → last login date
    created_at → registration date

    Pagination: $members->links()

    $currentFilter → 'all' | 'followers' | 'following'
    $keyword → search keyword or null

    FILTERS (via query params):
    ?filter=all → semua member
    ?filter=followers → orang yang follow saya (auth required)
    ?filter=following → orang yang saya follow (auth required)
    ?keyword=alex → search by name/username/email

    SORTING:
    Default: last_login_at DESC, created_at DESC
    (user yang baru login dan baru register muncul di atas)
    --}}

    {{-- Placeholder --}}
    <h1>{{ $page_title }}</h1>
    <p>{{ $members->total() }} members</p>
@endsection