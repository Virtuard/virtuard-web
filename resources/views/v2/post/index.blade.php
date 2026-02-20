@extends('v2.layouts.app')

@section('content')
    {{-- ALL POSTS PAGE --}}
    {{-- Route: GET /post --}}
    {{-- Store: POST /post --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================

    $posts → LengthAwarePaginator
    Setiap item (array):
    id → post ID
    message → post text content
    type_post → 'normal' | '360' | etc
    created_at → Carbon date
    likes_count → jumlah likes
    comments_count → jumlah comments
    is_liked → boolean, apakah user yang login sudah like
    medias → array of:
    id, url, type (image/video), is_360_media (boolean)
    author → array of:
    id, name, user_name, photo_profile (URL)

    Pagination: $posts->links()

    $currentTab → 'newest' | 'for_you'

    TABS:
    ?tab=newest → sorted by created_at desc
    ?tab=for_you → default feed

    POST FORM (sidebar "Post Something"):
    POST /post
    Fields:
    message → text content
    media_user[] → file array (images/videos)
    privacy → 'public' | 'friends' | 'private'
    is_360_media → boolean (for 360° media)

    INTERACTION APIs (existing, from API module):
    POST /api/post/{id}/like → toggle like
    POST /api/post/{id}/comment → add comment
    DELETE /api/post/{id} → delete post
    --}}

    {{-- Placeholder --}}
    <h1>{{ $page_title }}</h1>
    <p>{{ $posts->total() }} posts</p>
@endsection