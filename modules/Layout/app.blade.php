<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $html_class ?? '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.preconnect')
    @php event(new \Modules\Layout\Events\LayoutBeginHead()); @endphp
    @php
        $favicon = setting_item('site_favicon');
        if (isset($seo_meta) && isset($seo_meta['seo_image'])) {
            $favicon = $seo_meta['seo_image'];
        }
    @endphp
    @if ($favicon && (request()->is('profile/*') || request()->is('user/profile')))
        @php
            $avatarUrl = $user->getAvatarUrl() ?? url('images/favicon.png');
        @endphp
        <link rel="icon" type="image/png" href="{{ $avatarUrl }}" />
    @else
        @php
            $file = (new \Modules\Media\Models\MediaFile())->findById($favicon);
        @endphp
        @if (!empty($file))
            <link rel="icon" type="{{ $file['file_type'] }}" href="{{ asset('uploads/' . $file['file_path']) }}" />
        @else
            <link rel="icon" type="image/png" href="{{ url('images/favicon.png') }}" />
        @endif
    @endif


    @include('Layout::parts.seo-meta')
    <link href="{{ asset('libs/bootstrap/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/icofont/icofont.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dist/frontend/css/notification.css') }}" rel="newest stylesheet">
    <link href="{{ asset('dist/frontend/css/app.css?_ver=' . config('app.asset_version')) }}" rel="stylesheet">
    <link href="{{ asset('libs/lightbox2/dist/css/lightbox.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('libs/daterange/daterangepicker.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css" />
    <!-- Fonts -->
    <link rel='stylesheet' id='google-font-css-css'
        href='https://fonts.googleapis.com/css?family=Poppins%3A300%2C400%2C500%2C600&display=swap' type='text/css'
        media='all' />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-app.css') }}">
    {!! \App\Helpers\Assets::css() !!}
    {!! \App\Helpers\Assets::js() !!}
    @include('Layout::parts.global-script')
    <!-- Styles -->
    @stack('css')
    {{-- Custom Style --}}
    <link href="{{ route('core.style.customCss') }}" rel="stylesheet">
    <link href="{{ asset('libs/carousel-2/owl.carousel.css') }}" rel="stylesheet">
    @if (!is_demo_mode())
        {!! setting_item('head_scripts') !!}
        {!! setting_item_with_lang_raw('head_scripts') !!}
    @endif

    <style>
        @media (min-width: 993px) {
            .notification-container {
                display: none;
            }
        }
        @media (max-width: 992px) {
            .notification-container {
                display: flex;
                justify-content: center;
                opacity: 1;
                transition: all .3s;
                .notification-create-listing {
                    margin: 0 16px;
                    position: fixed;
                    bottom: -150px;
                    max-width: 400px;
                    border: 1px solid rgba(0, 0, 0, 0.1);
                    z-index: 99;
                    background-color: #fff;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    padding: 20px 32px;
                    border-radius: 8px;
                    transition: all .3s;    
                }
                &.active {
                    opacity: 1;
                    .notification-create-listing {
                        bottom: 70px;
                    }
                }
            }
        }
    </style>
</head>

<body
    class="frontend-page {{ !empty($row->header_style) ? 'header-' . $row->header_style : 'header-normal' }} {{ $body_class ?? '' }} @if (is_api()) is_api @endif">
    @if (!is_demo_mode())
        {!! setting_item('body_scripts') !!}
        {!! setting_item_with_lang_raw('body_scripts') !!}
    @endif
    <div class="bravo_wrap">
        @if (!is_api())
            {{-- @include('Layout::parts.topbar') --}}
            @include('Layout::parts.header')
        @endif

        @yield('content')

        @if (Request::is('/') || Request::is('/' . app()->getLocale()))
            <div class="notification-container">
                <div class="notification-create-listing">
                    <p>Ready to share your space? Create a listing now!</p>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('create') }}" class="btn btn-primary">Create Listing for Free</a>
                    </div>
                </div>
            </div>
        @endif

        @include('Layout::parts.footer')
    </div>
    @if (!is_demo_mode())
        {!! setting_item('footer_scripts') !!}
        {!! setting_item_with_lang_raw('footer_scripts') !!}
    @endif
    <script src="{{ asset('assets/js/custom-app.js') }}"></script>
    @if (setting_item('google_translate_enable'))
        <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
        <script>
            function googleTranslateElementInit() {
                if (window.innerWidth >= 768) {
                    var webTarget = document.getElementById('gtranslate-web');
                    webTarget.innerHTML = '<div id="google_translate_element"></div>';
                    new google.translate.TranslateElement({
                        pageLanguage: 'en'
                    }, 'google_translate_element');
                } else {
                    var mobileTarget = document.getElementById('gtranslate-mobile');
                    mobileTarget.innerHTML = '<div id="google_translate_element"></div>';
                    new google.translate.TranslateElement({
                        pageLanguage: 'en'
                    }, 'google_translate_element');
                }
            }
            window.addEventListener('resize', googleTranslateElementInit);
        </script>
    @endif

    <script>
        const userAgent = navigator.userAgent || navigator.vendor || window.opera;

        // Deteksi jika dibuka dari Instagram atau Facebook
        if (userAgent.includes("Instagram") || userAgent.includes("FBAN") || userAgent.includes("FBAV")) {
            // Arahkan ke browser eksternal (Google Chrome di Android)
            window.location.href = "googlechrome://virtuard.com"; 
        }

        // Notification create listing
        window.addEventListener('scroll', function() {
            let scrollTop = window.scrollY;
            let windowHeight = window.innerHeight;
            let documentHeight = document.body.scrollHeight;

            if (scrollTop + windowHeight >= documentHeight - 300) {
                // Jika pengguna telah mencapai bagian bawah halaman, hapus class "active"
                $(".notification-container").removeClass("active");
            } else if (scrollTop > 515) {
                // Tambahkan class "active" jika pengguna menggulir lebih dari 515px
                $(".notification-container").addClass("active");
            } else {
                $(".notification-container").removeClass("active");
            }
        });

    </script>
</body>

</html>
