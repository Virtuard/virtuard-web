@extends('v2.layouts.app')

@section('content')
    <div class="container" style="padding: 60px 20px; text-align: center;">
        <h1>Homepage V2 - Redesign in progress</h1>
        <p style="color: #666; margin-top: 10px;">This page is being redesigned. Data is ready for the frontend.</p>
    </div>

{{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================

    $categories → Array of space categories (Accommodation, Property, Commercial Activities)
    Setiap item: name, count, min_price, price_html, url

    $accommodations → Collection of top 6 Hotels
    Setiap item: id, title, slug, url, image, location_name,
    price, sale_price, price_html, bed, bathroom,
    review_score, total_review, is_wishlist

    $properties → Collection of top 6 Spaces
    Setiap item: id, title, slug, url, image, location_name,
    price, sale_price, price_html,
    review_score, total_review, is_wishlist

    $businesses → Collection of top 6 Businesses
    Setiap item: id, title, slug, url, image, location_name,
    price, sale_price, price_html,
    review_score, total_review, is_wishlist

    $plans → Collection of Plans (ordered by price asc)
    Setiap item: id, title, content (features HTML),
    price, price_html, annual_price, annual_html,
    duration, duration_type, duration_text,
    max_service, max_ipanorama, is_recommended

    $total_users → Total registered users count
    $page_title → Page title for SEO
--}}
@endsection