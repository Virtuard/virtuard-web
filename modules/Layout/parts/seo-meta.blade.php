@if(!empty($seo_meta))
    @if(isset($seo_meta['seo_index']) and $seo_meta['seo_index'] == 0)
        <meta name="robots" content="noindex">
    @endif
    @php
        $page_title = $seo_meta['seo_title'] ?? $seo_meta['service_title'] ?? $page_title ?? "";
        if(!empty($page_title) and empty($seo_meta['is_homepage'])){
            $page_title .= " - ".setting_item_with_lang('site_title' ,false,'Virtuard');
        }
        if(empty($page_title)){
            $page_title = setting_item_with_lang('site_title' ,false,'Virtuard');
        }
    @endphp
    <title>{{ $page_title }}</title>
    <meta name="description" content="{{$seo_meta['seo_desc'] ?? $seo_meta['service_desc'] ?? setting_item_with_lang("site_desc")}}"/>
    <meta name="keywords" content="{{$seo_meta['seo_keywords'] ?? $seo_meta['service_keywords'] ?? setting_item_with_lang("site_keywords")}}"/>
    <meta name="robots" content="index,follow" />
    {{-- Facebook share --}}
    <meta property="og:url" content="{{$seo_meta['full_url'] ?? ''}}"/>
    <meta property="og:type" content="company"/>
    <meta property="og:title" content="{{$seo_meta['seo_share']['facebook']['title'] ?? $seo_meta['seo_title'] ?? $seo_meta['service_title'] ?? $page_title ?? ""}}"/>
    <meta property="og:description" content="{{$seo_meta['seo_share']['facebook']['desc'] ?? $seo_meta['seo_desc'] ?? $seo_meta['service_desc'] ?? ""}}"/>
    @if(!empty($seo_meta['seo_image_url']))
    <meta property="og:image" content="{{ $seo_meta['seo_image_url'] }}"/>
    @else
    <meta property="og:image" content="{{ get_file_url( $seo_meta['seo_share']['facebook']['image'] ?? $seo_meta['seo_image'] ?? $seo_meta['service_image'] ?? setting_item('logo_id') ?? "" , "full") }}"/>
    @endif
    {{-- Twitter share --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{$seo_meta['seo_share']['twitter']['title'] ?? $seo_meta['seo_title'] ?? $seo_meta['service_title'] ?? $page_title ?? ""}}">
    <meta name="twitter:description" content="{{$seo_meta['seo_share']['twitter']['desc'] ?? $seo_meta['seo_desc'] ?? $seo_meta['service_desc'] ?? ""}}">
    @if(!empty($seo_meta['seo_image_url']))
    <meta name="twitter:image" content="{{ $seo_meta['seo_image_url'] }}"/>
    @else
    <meta name="twitter:image" content="{{ get_file_url( $seo_meta['seo_share']['twitter']['image'] ?? $seo_meta['seo_image'] ?? $seo_meta['service_image'] ?? setting_item('logo_id') ?? "" , "full") }}">
    @endif
    <link rel="canonical" href="{{$seo_meta['full_url'] ?? ''}}"/>
    <link rel="canonicalize" href="{{ url()->current() }}" />

    {{-- JSON-LD Schema --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ $page_title }}",
        "url": "{{ url('/') }}",
        "description": "{{$seo_meta['seo_desc'] ?? $seo_meta['service_desc'] ?? setting_item_with_lang('site_desc')}}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/') }}/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ setting_item_with_lang('site_title') }}",
        "url": "{{ url('/') }}",
        "logo": "{{ get_file_url(setting_item('logo_id'), 'full') }}",
        "image": "{{ get_file_url($seo_meta['seo_image'] ?? setting_item('logo_id'), 'full') }}",
        "description": "{{$seo_meta['seo_desc'] ?? $seo_meta['service_desc'] ?? setting_item_with_lang('site_desc')}}",
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "{{ setting_item('location_country') }}",
            "addressLocality": "{{ setting_item('location_city') }}",
            "streetAddress": "{{ setting_item('location_address') }}"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "{{ setting_item('contact_phone') }}",
            "contactType": "customer service",
            "email": "{{ setting_item('contact_email') }}",
            "availableLanguage": ["en", "id"]
        },
        "sameAs": [
            "{{ setting_item('social_facebook') }}",
            "{{ setting_item('social_twitter') }}",
            "{{ setting_item('social_instagram') }}",
            "{{ setting_item('social_youtube') }}"
        ]
    }
    </script>

    @if(!empty($row) && !empty($row->type))
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "{{ ucfirst($row->type) }}",
        "name": "{{ $seo_meta['seo_title'] ?? $row->title ?? '' }}",
        "description": "{{ $seo_meta['seo_desc'] ?? $row->short_desc ?? '' }}",
        "image": "{{ get_file_url($row->image_id ?? '', 'full') }}",
        "url": "{{ $seo_meta['full_url'] ?? url()->current() }}",
        @if(!empty($row->price))
        "offers": {
            "@type": "Offer",
            "price": "{{ $row->price }}",
            "priceCurrency": "{{ setting_item('currency_main') }}"
        },
        @endif
        @if(!empty($row->review_score))
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $row->review_score }}",
            "reviewCount": "{{ $row->review_stats[0]['total'] ?? 1 }}"
        },
        @endif
        "publisher": {
            "@type": "Organization",
            "name": "{{ setting_item_with_lang('site_title') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ get_file_url(setting_item('logo_id'), 'full') }}"
            }
        }
    }
    </script>
    @endif
@else
    @php
        if(!empty($page_title)){
            $page_title .= " - ".setting_item_with_lang('site_title' ,false,'Virtuard');
        }else{
            $page_title = setting_item_with_lang('site_title' ,false,'Virtuard');
        }
    @endphp
    <title>{{ $page_title }}</title>
    <meta name="description" content="{{setting_item_with_lang('site_desc')}}"/>
@endif
