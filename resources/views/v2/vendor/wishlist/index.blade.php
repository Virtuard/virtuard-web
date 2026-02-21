@extends('v2.layouts.vendor')

@section('content')
    {{-- VENDOR WISHLIST PAGE --}}
    {{-- Route: GET /vendor/wishlist --}}

    {{--
    ============================================
    DATA TERSEDIA UNTUK FRONTEND:
    ============================================
    $wishlist → Paginator objects. Tiap item berisi:
    - id: 5 (ID Wishlist, bisa dipakai bila perlu endpoint HAPUS WISHLIST di kemudian hari)
    - object_id: 11
    - object_model: "hotel"
    - title: "Oceanview Luxury Villa"
    - url: "https://virtaurd.com/hotel/oceanview-luxury-villa"
    - image_url: "https://virtaurd.com/uploads/foo/bar.jpg"
    - location: "Bali, Indonesia"
    - price: "$220.00" (Sudah formatted money)
    - price_unit: "/ night"
    - review_score: 4.8
    - review_count: 312

    $listing_options → Key-value array dinamis yang dibentuk dari daftar entri di tabel database, misal:
    [ "hotel" => "Hotel", "space" => "Space" ]
    Bisa dipakai untuk me-loop `<select name="listing">`.

        $filters → Filter aktif saat ini yang dipasang di URL GET parameters:
        - sort: "recent" | "rating" | "name"
        - listing: "none" | "hotel" | "space" | "business" | (lainnya)
        - price: "none" | "low" | "high"

        ============================================
        PENGGUNAAN PENCARIAN
        ============================================
        Kirim form GET di atas dengan parameter yang diperlukan. Parameter yg tidak dipilih biarkan bernilai default.
        Contoh: /vendor/wishlist?sort=rating&listing=hotel&price=low
        --}}

        <div style="display:flex; justify-content: space-between; align-items: center;">
            <h1>{{ $page_title }}</h1>
            <a href="#" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">Copy Referral Link</a> {{--
            Placebo, header requirement di mockup --}}
        </div>

        {{-- FILTER FORM --}}
        <form method="GET" action="" style="display:flex; gap: 15px; margin-bottom: 25px;">
            <select name="sort" onchange="this.form.submit()">
                <option value="recent" {{ $filters['sort'] == 'recent' ? 'selected' : '' }}>Sort by: Recently Added</option>
                <option value="rating" {{ $filters['sort'] == 'rating' ? 'selected' : '' }}>Sort by: Rating</option>
                <option value="name" {{ $filters['sort'] == 'name' ? 'selected' : '' }}>Sort by: Name (A-Z)</option>
            </select>

            <select name="listing" onchange="this.form.submit()">
                <option value="none" {{ $filters['listing'] == 'none' ? 'selected' : '' }}>Listing: All Types</option>
                @foreach($listing_options as $key => $label)
                    <option value="{{ $key }}" {{ $filters['listing'] == $key ? 'selected' : '' }}>Listing: {{ $label }}</option>
                @endforeach
            </select>

            <select name="price" onchange="this.form.submit()">
                <option value="none" {{ $filters['price'] == 'none' ? 'selected' : '' }}>Price: None</option>
                <option value="low" {{ $filters['price'] == 'low' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="high" {{ $filters['price'] == 'high' ? 'selected' : '' }}>Price: High to Low</option>
            </select>
        </form>

        {{-- GRID SECTION --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
            @foreach($wishlist as $item)
                <div style="border: 1px solid #eee; border-radius: 10px; overflow: hidden; position: relative;">
                    {{-- Mockup Like icon di kanan atas card --}}
                    <div
                        style="position: absolute; right: 10px; top: 10px; background: red; height: 30px; width: 30px; border-radius: 50%;">
                    </div>

                    {{-- Image Thumbnail --}}
                    <div
                        style="height: 150px; background: #ddd; background-image: url('{{ $item['image_url'] }}'); background-size: cover; background-position: center;">
                    </div>

                    {{-- Data Card --}}
                    <div style="padding: 15px;">
                        <h3 style="margin: 0; font-size: 16px;">{{ $item['title'] }}</h3>
                        <div
                            style="display:flex; justify-content: space-between; font-size: 13px; color: #555; margin-top: 5px;">
                            <span>📍 {{ $item['location'] }}</span>
                            <span><b>{{ $item['price'] }}</b>{{ $item['price_unit'] }}</span>
                        </div>

                        <div
                            style="display:flex; justify-content: space-between; font-size: 13px; margin-top: 15px; padding-top: 10px; border-top: 1px dashed #eee;">
                            <span>⭐ {{ $item['review_score'] }}</span>
                            <span style="color: #aaa;">{{ $item['review_count'] }} reviews</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        <div style="margin-top: 30px;">
            {{ $wishlist->links() }}
        </div>

@endsection