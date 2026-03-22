<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore 3D & 360 Virtual Tours | Virtual Tours by Virtuard</title>
    <link rel="canonicalize" href="{{ url()->current() }}" />
    <meta name="description" content="Want to showcase your property, hotel, restaurant, or shop like never before? With Virtuard, you can create your listing and 360° Virtual Tour on your own – at no cost and with no assistance needed!">
    {{-- <meta name="keywords" content="keyword1, keyword2, keyword3"> --}}
    {{-- <meta name="author" content="Your Name or Company"> --}}

    <!-- favicon -->
    <link rel="icon" href="{{ asset('images/virtuard-logo.png') }}" type="image/x-icon">

    {{-- <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}"> --}}
    <style>
        * {margin: 0; padding: 0;}

        body, html {
            font-family: 'Urbanist', arial;
            min-height: 100vh;
        }

        #viewer { width: 100vw; height: 100vh; position: fixed; }

        a {
            text-decoration: none !important;
            color: #5191FA;
        }

        ul {
            display: flex;
        }

        ul li {
            list-style: none;
        }

        /* #background {
            background-color: rgba(0,0,0,.4);
            width: 100%;
            height: 95vh;
            position: fixed;
            z-index: 99;
        } */

        #main {
            position: absolute;
            z-index: 99;
            /* background-color: rgba(0,0,0,.5); */
            width: 100%;
            height: 100%;
        }

        .bg-overlay {
            background-color: rgba(0,0,0,.5);
            width: 100%;
            /* height: 100%; */
        }

        nav {
            background-color: rgba(0,0,0,.5);
        }

        #main .navbar {
            /* position: fixed;
            right: 0;
            left: 0; */
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* margin-top: 20px;
            margin-bottom: 20px; */
            z-index: 999;
            transition: .3s;
        }

        #main .navbar.active {
            background: rgba(0, 0, 0, 0.29);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(7.2px);
            -webkit-backdrop-filter: blur(7.2px);
            border: 1px solid rgba(0, 0, 0, 0.3);
        }

        #main .navbar .nav-item.language {
            margin-right: 20px;
        }
        #main .navbar .nav-item {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        #main .navbar .nav-item svg {
            fill: #fff;
            transition: .3s;
        }
        #main .navbar .nav-item .navbar-link {
            color: #fff;
            font-weight: 500;
            transition: .3s;
            font-size: 14px;
        }
        #main .navbar .nav-item:hover .navbar-link {
            color: #5191FA;
        }
        #main .navbar .nav-item.multi-lang:hover .navbar-link {
            color: #fff;
        }
        #main .navbar .nav-item:hover svg {
            fill: #5191FA;
        }

        .btn.btn-first {
            padding: 16px 24px;
            border: none;
            background-color: #5191FA;
            border: 1px solid #5191FA;
            color: #fff;
            border-radius: 999px;
            cursor: pointer;
            transition: .3s;
        }

        .btn.btn-first:hover {
            opacity: .9;
            color: white !important;
        }

        .btn.btn-second {
            padding: 16px 24px;
            border: none;
            background-color: #fff;
            border: 1px solid #fff;
            color: #222;
            border-radius: 999px;
            cursor: pointer;
            transition: .3s;
        }

        .btn.btn-second:hover {
            opacity: .9;
            color: #222 !important;
        }

        #header {
            padding-top: 80px;
            padding-bottom: 150px;
            display: flex;
            justify-content: center;
            /* height: 90vh; */
        }

        #header .header-content {
            max-width: 1000px;
            text-align: center;
            color: #fff;
        }

        #header .header-content .title {
            font-size: 60px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        #header .header-content .title span {
            color: #5191FA;
        }

        .section-header .title {
            font-size: 40px;
            font-weight: 700;
        }

        .header-content .description {
            max-width: 900px;
            margin: 20px auto;
        }

        .description {
            font-weight: 400;
            color: #ddd;
            margin-bottom: 20px;
        }

        #header .header-content .btn {
            font-size: 16px;
            padding: 20px 32px;
        }

        #header .header-content .mouse-container {
            display: flex;
            justify-content: center;
            margin-top: 100px;
        }

        #header .header-content .mouse {
            width: 40px;
            height: 70px;
            border: 2px solid #ddd;
            border-radius: 60px;
            position: relative;
            &::before {
                content: '';
                width: 12px;
                height: 12px;
                position: absolute;
                top: 10px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #fff;
                border-radius: 50%;
                opacity: 1;
                animation: wheel 2s infinite;
                -webkit-animation: wheel 2s infinite;
            }
        }

        @keyframes wheel {
            to {
                opacity: 0;
                top: 60px;
            }
        }

        @-webkit-keyframes wheel {
            to {
                opacity: 0;
                top: 60px;
            }
        }

        .get-started-container {
            padding-top: 150px;
        }

        .section-header {
            max-width: 700px;
            margin: 0 auto;
        }

        .card-feature {
            display: flex;
            align-items: center;
            border: none;
            margin-bottom: -30px;
        }

        .additional-features .card-feature {
            margin-bottom: -15px;
        }

        .additional-features .card-feature {
            align-items: flex-start;
        }

        /* .card-feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        } */

        .card-feature .card-icon {
            background: #5191FA;
            border-radius: 999px;
            padding: 5px 6px;
        }

        .feature .card-feature .card-icon svg {
            width: 24px;
            height: 24px;
        }

        .card-feature .card-title {
            font-size: 1rem;
            margin-top: 10px;
            font-weight: 600;
            color: #fff;
        }

        .additional-features .card-feature .card-title {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .card-custom {
            border: none;
            overflow: hidden;
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
            border-radius: unset;
            min-height: 150px;
            box-shadow: 0 0 12px 0 rgba(0, 0, 0, 0.2);
        }

        .card-custom.card-has-bg {
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
            background-size: 120%;
            background-repeat: no-repeat;
            background-position: center center;
        }

        .card-custom.card-has-bg:before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .card-custom.card-has-bg:hover {
            transform: scale(0.98);
            box-shadow: 0 0 5px -2px rgba(0, 0, 0, 0.3);
            background-size: 130%;
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
        }

        .card-custom.card-has-bg:hover .card-img-overlay {
            transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
            background: rgba(22, 22, 22, 0.8);
            background: linear-gradient(0deg, rgba(22, 22, 22, 0.8) 0%, #1a1b1b 100%);
        }

        .card-custom .card-title {
            font-weight: 800;
        }

        .card-custom .card-meta {
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 2px;
        }

        .card-custom .card-body {
            transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);
        }

        .card-custom:hover {
            cursor: pointer;
            transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
        }

        .card-custom .card-img-overlay {
            transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
            background: rgba(22, 22, 22, 0.3);
            background: linear-gradient(0deg, rgba(22, 22, 22, 0.3) 0%, #1a1b1b 100%);
        }

        #gmap {
            height: 100%;
        }

        .card-explore {
            width: 100%;
            height: 500px;
            position: relative;
            overflow: hidden;
            overflow-y: scroll;
        }

        .map-section {
            margin: 0 100px;
            margin-bottom: 100px;
        }

        .how-it-works .item-number {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 999px;
        }

        .how-it-works .item-number p {
            border: 1px solid #5191FA;
            padding: 2px 10px;
            margin-top: 12px;
            font-size: 18px;
            border-radius: 999px;
            color: #5191FA;
            font-weight: 700;
        }

        .footer-text {
            color: #ddd;
            font-size: 14px;
        }

        .multi-lang .dropdown {
            position: absolute;
            background: #eee;
            border-radius: 4px;
            padding: 12px 16px;
            bottom: -120px;
            transform: translateY(-1000px);
            opacity: 0;
            transition: .3s opacity;
        }

        .multi-lang .dropdown .navbar-link {
            color: #222 !important;
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
        }

        .multi-lang .dropdown-icon {
            transition: .3s;
        }

        .multi-lang.active .dropdown-icon {
            transform: rotate(180deg);
        }

        .multi-lang.active .dropdown {
            transform: translateY(0);
            opacity: 100;
        }


        @media (max-width: 768px) {
            .card-custom {
                min-height: 250px;
            }

            .card-explore {
                height: 350px;
            }
        }

        @media (max-width: 420px) {
            .header-content .title {
                font-size: 44px !important;
            }

            .card-custom {
                min-height: 250px;
            }
        }
    </style>
    {{-- bootstrap --}}
    <link href="{{ asset('libs/bootstrap/css/bootstrap.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core/index.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/virtual-tour-plugin/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/gallery-plugin/index.css" />
    <!-- Urbanist Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    {{-- css --}}
    <link rel="stylesheet" href="{{ asset('libs/flags/css/flag-icon.css') }}">

    {{-- icons --}}
    <link href="{{ asset('libs/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/icofont/icofont.min.css') }}" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/photo-sphere-viewer.js"></script>

</head>
<body>
    <main id="main">
        <nav>
            <div class="navbar container">
                <a href="/" class="navbar-brand">
                    <img src="{{ asset('images/virtuard-logo.png') }}" alt="Virtuard Logo" width="80">
                </a>
                <ul class="mt-3">
                    {{-- <li class="nav-item language">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zM4 12c0-.899.156-1.762.431-2.569L6 11l2 2v2l2 2 1 1v1.931C7.061 19.436 4 16.072 4 12zm14.33 4.873C17.677 16.347 16.687 16 16 16v-1a2 2 0 0 0-2-2h-4v-3a2 2 0 0 0 2-2V7h1a2 2 0 0 0 2-2v-.411C17.928 5.778 20 8.65 20 12a7.947 7.947 0 0 1-1.67 4.873z"></path></svg>
                        <a href="/landing?lang=id" class="navbar-link">Indonesian</a>
                    </li> --}}
                    @if (is_enable_multi_lang())
                        <li class="nav-item multi-lang position-relative mr-3 d-flex">
                            @foreach ($languages as $lang)
                                @if ($lang->locale === app()->getLocale())
                                    <a href="#" class="navbar-link">
                                        @if($lang->flag)
                                            <span class="flag-icon flag-icon-{{$lang->flag}}"></span>
                                        @endif
                                        {{$lang->name}}
                                    </a>
                                    <svg class="dropdown-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M16.293 9.293 12 13.586 7.707 9.293l-1.414 1.414L12 16.414l5.707-5.707z"></path></svg>
                                @endif
                            @endforeach
                            <div class="dropdown">
                                @foreach ($languages as $lang)
                                    @if ($lang->locale !== app()->getLocale())
                                        <a href="{{ route('language.set-lang', $lang->locale) }}" class="navbar-link">
                                            @if($lang->flag)
                                                <span class="flag-icon flag-icon-{{$lang->flag}}"></span>
                                            @endif
                                            {{$lang->name}}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a href="/register" class="navbar-link btn btn-first px-4 py-3">{{ __('Get Started') }}</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="bg-overlay">
            <header id="header" class="container">
                <div class="header-content">
                    <h1 class="title">{!! __('Explore Virtuard: Your Gateway to <span>Immersive Virtual Tours</span>') !!}</h1>
                    <p class="description">{{ __('Virtuard is a cutting-edge platform that empowers users to upload and explore virtual tours with integrated booking services. Supporting various tour formats, Virtuard features eight distinct categories: Real Estate, Hotels, Business.') }}</p>
                    <button class="btn btn-second" id="btn-demo">{{ __('Virtuard Tour Demo') }}</button>

                    <div class="mouse-container">
                        <div class="mouse"></div>
                    </div>
                </div>
            </header>
        </div>
        <div class="bg-overlay">
            <section class="container">
                <div>
                    <div class="text-center mb-5 text-white section-header">
                        <h2 class="title">{{ __('Our Locations') }}</h2>
                        <p class="description">{{ __("Discover properties, hotels, restaurants, and shops from various locations with Virtuard. Whether you're exploring a new city or showcasing your space, our platform offers an immersive 360° virtual experience to bring every location to life") }}</p>
                    </div>
                    <div class="card card-explore">
                        <div id="map-loading" class="text-center" style="
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                z-index: 1000;
                                display: none;
                                background-color: rgba(255, 255, 255, 0.1);
                                padding: 10px;
                                border-radius: 5px;
                            ">
                                <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        <div id="gmap"></div>
                    </div>
                </div>
            </section>
        </div>

        <div class="bg-overlay">
            <section class="container get-started-container">
                <div class="text-center mb-5 text-white section-header">
                    <h2 class="title">{{ __('Categories') }}</h2>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12 p-2">
                        <div class="bg-white rounded px-4 py-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"><path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4zm2-15.586 6 6V15l.001 5H16v-5c0-1.103-.897-2-2-2h-4c-1.103 0-2 .897-2 2v5H6v-9.586l6-6z"></path></svg>
                            <h4 class="card-title mt-3 text-primary font-weight-bold">{{ __('Real Estate') }}</h4>
                            <p class="card-meta">{{ __('Experience virtual tours and book residential and commercial properties seamlessly.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 p-2">
                        <div class="bg-white rounded px-4 py-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"><path d="M18 2H6c-1.103 0-2 .897-2 2v17a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4c0-1.103-.897-2-2-2zm0 18H6V4h12v16z"></path><path d="M8 6h3v2H8zm5 0h3v2h-3zm-5 4h3v2H8zm5 .031h3V12h-3zM8 14h3v2H8zm5 0h3v2h-3z"></path></svg>
                            <h4 class="card-title mt-3 text-primary font-weight-bold">{{ __('Hotels') }}</h4>
                            <p class="card-meta">{{ __('Explore and book rooms, suites, and facilities at your convenience.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 p-2">
                        <div class="bg-white rounded px-4 py-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"><path d="M5 22h14c1.103 0 2-.897 2-2V9a1 1 0 0 0-1-1h-3V7c0-2.757-2.243-5-5-5S7 4.243 7 7v1H4a1 1 0 0 0-1 1v11c0 1.103.897 2 2 2zM9 7c0-1.654 1.346-3 3-3s3 1.346 3 3v1H9V7zm-4 3h2v2h2v-2h6v2h2v-2h2l.002 10H5V10z"></path></svg>
                            <h4 class="card-title mt-3 text-primary font-weight-bold">{{ __('Business') }}</h4>
                            <p class="card-meta">{{ __('Take virtual tours and make bookings for shops, restaurants, and offices.') }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="bg-overlay">
            <section class="container get-started-container adventages">
                <div class="text-center mb-5 text-white section-header">
                    <h2 class="title">{{ __('Adventages') }}</h2>
                    <p class="description">{{ __('Here are the Virtual Tour benefits for Accommodation, Real Estate, and Shops:') }}</p>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-12 p-2">
                        <div class="bg-white rounded px-4 py-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"><path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4zm2-15.586 6 6V15l.001 5H16v-5c0-1.103-.897-2-2-2h-4c-1.103 0-2 .897-2 2v5H6v-9.586l6-6z"></path></svg>
                            <h4 class="card-title mt-3 text-primary font-weight-bold">{{ __('Hospitality') }}</h4>
                            <hr>
                            <div class="d-block">
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Immersive experience') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Guests can explore rooms and common areas realistically before booking.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Increased bookings') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Builds customer trust, reducing doubts and boosting conversion rates.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Competitive advantage') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Offering a Virtual Tour makes the property more modern and transparent.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Fewer customer inquiries') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Clients can explore the space without needing to contact customer service.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Perfect for marketing') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Virtual Tours can be integrated into websites, social media, and Google Street View to increase visibility.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12 p-2">
                        <div class="bg-white rounded px-4 py-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"><path d="M18 2H6c-1.103 0-2 .897-2 2v17a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4c0-1.103-.897-2-2-2zm0 18H6V4h12v16z"></path><path d="M8 6h3v2H8zm5 0h3v2h-3zm-5 4h3v2H8zm5 .031h3V12h-3zM8 14h3v2H8zm5 0h3v2h-3z"></path></svg>
                            <h4 class="card-title mt-3 text-primary font-weight-bold">{{ __('Real Estate') }}</h4>
                            <hr>
                            <div class="d-block">
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('24/7 virtual visits') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Potential buyers can explore properties anytime without scheduling physical appointments.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Better client selection') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Only genuinely interested buyers will request in-person visits, saving agents time.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Higher engagement') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Listings with Virtual Tours receive more views and interactions than static images.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Ideal for remote investors') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Perfect for those looking to purchase properties without traveling.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Enhanced property showcase') }}</h6>
                                        <p style="font-size: 14px;">{{ __('A well-made tour highlights every corner of the property, offering a more accurate perception.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12 p-2">
                        <div class="bg-white rounded px-4 py-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"><path d="M5 22h14c1.103 0 2-.897 2-2V9a1 1 0 0 0-1-1h-3V7c0-2.757-2.243-5-5-5S7 4.243 7 7v1H4a1 1 0 0 0-1 1v11c0 1.103.897 2 2 2zM9 7c0-1.654 1.346-3 3-3s3 1.346 3 3v1H9V7zm-4 3h2v2h2v-2h6v2h2v-2h2l.002 10H5V10z"></path></svg>
                            <h4 class="card-title mt-3 text-primary font-weight-bold">{{ __('Retail') }}</h4>
                            <hr>
                            <div class="d-block">
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Improved shopping experience') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Customers can explore the store online and decide what to buy before visiting in person.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('More in-store traffic') }}</h6>
                                        <p style="font-size: 14px;">{{ __('A Virtual Tour attracts local customers interested in what the store offers.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('E-commerce integration') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Possibility to link the tour to online purchases for an omnichannel experience.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Innovative marketing') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Shareable on social media, Google Maps, and websites to boost brand visibility.') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                                    </div>
                                    <div style="margin-top: 4px; margin-left: 8px;">
                                        <h6>{{ __('Better Google ranking') }}</h6>
                                        <p style="font-size: 14px;">{{ __('Businesses with Virtual Tours tend to rank higher in local search results.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="bg-overlay">
            <section class="container get-started-container feature">
                <div class="text-center mb-5 text-white section-header">
                    <h2 class="title">{{ __('Key Features') }}</h2>
                    <p class="description">{{ __("Virtuard offers a range of features to help you create and explore 3D & 360° Virtual Tours. Whether you're a property owner, real estate agent, or business owner, our platform provides the tools you need to showcase your space and attract customers.") }}</p>
                    {{-- <p class="description">Don’t miss the chance to stand out from the competition! 🚀</p> --}}
                </div>
                <div class="row">
                    <div class="col-md-6 col-12 d-md-block d-none">
                        <img width="100%" src="{{ asset('images/benefit-img.png') }}" alt="">
                    </div>
                    <div class="col-md-6 col-12 mt-md-5 mt-0">
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">{{ __('Self-Upload Virtual Tours') }}</h3>
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">{{ __('User-Friendly Interface: Easily create and upload tours.') }}</h3>
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">{{ __('Support for Panoramic Images and Videos: Enhance your virtual tour experience.') }}</h3>
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">{{ __('Integrated Editing Tools: Add interactive elements and refine your tours.') }}</h3>
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">{{ __('Integrated Booking System') }}</h3>
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">{{ __('Direct Booking: Book directly through virtual tours.') }}</h3>
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">{{ __('Real-Time Availability Calendar: Check availability instantly.') }}</h3>
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">{{ __('Secure Payment Options: Choose from credit cards and PayPal.') }}</h3>
                            </div>
                        </div>
                        {{-- <a href="/register" class="mt-5 btn btn-second px-4 py-3">Get Started</a> --}}
                    </div>
                </div>
            </section>
        </div>

        <div class="bg-overlay">
            <section class="container get-started-container additional-features">
                <div class="text-center mb-5 text-white section-header">
                    <h2 class="title">{{ __('Additional Features') }}</h2>
                </div>
                <div class="row">
                    <div class="col-md-6 col-12 mt-md-5 mt-0">
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M10 18a7.952 7.952 0 0 0 4.897-1.688l4.396 4.396 1.414-1.414-4.396-4.396A7.952 7.952 0 0 0 18 10c0-4.411-3.589-8-8-8s-8 3.589-8 8 3.589 8 8 8zm0-14c3.309 0 6 2.691 6 6s-2.691 6-6 6-6-2.691-6-6 2.691-6 6-6z"></path></svg>
                            </div>
                            <div class="card-body" style="margin-top: -30px;">
                                <h1 class="card-title">{{ __('Advanced Search') }}</h1>
                                <p style="color: rgba(255,255,255,.8); margin-top: -10px;">{{ __('Filter by category, location, price, and availability to find your perfect virtual tour.') }}</p>
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M16 14h.5c.827 0 1.5-.673 1.5-1.5v-9c0-.827-.673-1.5-1.5-1.5h-13C2.673 2 2 2.673 2 3.5V18l5.333-4H16zm-9.333-2L4 14V4h12v8H6.667z"></path><path d="M20.5 8H20v6.001c0 1.1-.893 1.993-1.99 1.999H8v.5c0 .827.673 1.5 1.5 1.5h7.167L22 22V9.5c0-.827-.673-1.5-1.5-1.5z"></path></svg>
                            </div>
                            <div class="card-body" style="margin-top: -30px;">
                                <h1 class="card-title">{{ __('Reviews and Ratings') }}</h1>
                                <p style="color: rgba(255,255,255,.8); margin-top: -10px;">{{ __('Benefit from user feedback to improve your experience and service quality.') }}</p>
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M12 2C6.486 2 2 6.486 2 12v4.143C2 17.167 2.897 18 4 18h1a1 1 0 0 0 1-1v-5.143a1 1 0 0 0-1-1h-.908C4.648 6.987 7.978 4 12 4s7.352 2.987 7.908 6.857H19a1 1 0 0 0-1 1V18c0 1.103-.897 2-2 2h-2v-1h-4v3h6c2.206 0 4-1.794 4-4 1.103 0 2-.833 2-1.857V12c0-5.514-4.486-10-10-10z"></path></svg>
                            </div>
                            <div class="card-body" style="margin-top: -30px;">
                                <h1 class="card-title">{{ __('Customer Support') }}</h1>
                                <p style="color: rgba(255,255,255,.8); margin-top: -10px;">{{ __('Enjoy 24/7 support via live chat, email, and phone for any queries or assistance.') }}</p>
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M19 3c-1.654 0-3 1.346-3 3 0 .502.136.968.354 1.385l-1.116 1.302A3.976 3.976 0 0 0 13 8c-.739 0-1.425.216-2.02.566L9.566 7.152A3.449 3.449 0 0 0 10 5.5C10 3.57 8.43 2 6.5 2S3 3.57 3 5.5 4.57 9 6.5 9c.601 0 1.158-.166 1.652-.434L9.566 9.98A3.972 3.972 0 0 0 9 12c0 .997.38 1.899.985 2.601l-1.692 1.692.025.025A2.962 2.962 0 0 0 7 16c-1.654 0-3 1.346-3 3s1.346 3 3 3 3-1.346 3-3c0-.476-.121-.919-.318-1.318l.025.025 1.954-1.954c.421.15.867.247 1.339.247 2.206 0 4-1.794 4-4a3.96 3.96 0 0 0-.439-1.785l1.253-1.462c.364.158.764.247 1.186.247 1.654 0 3-1.346 3-3s-1.346-3-3-3zM7 20a1 1 0 1 1 0-2 1 1 0 0 1 0 2zM5 5.5C5 4.673 5.673 4 6.5 4S8 4.673 8 5.5 7.327 7 6.5 7 5 6.327 5 5.5zm8 8.5c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2zm6-7a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path></svg>
                            </div>
                            <div class="card-body" style="margin-top: -30px;">
                                <h1 class="card-title">{{ __('Social Media Integration') }}</h1>
                                <p style="color: rgba(255,255,255,.8); margin-top: -10px;">{{ __('Easily share your tours on social networks for a wider reach.') }}</p>
                            </div>
                        </div>
                        <div class="card-feature">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="M14.844 20H6.5C5.121 20 4 18.879 4 17.5S5.121 15 6.5 15h7c1.93 0 3.5-1.57 3.5-3.5S15.43 8 13.5 8H8.639a9.812 9.812 0 0 1-1.354 2H13.5c.827 0 1.5.673 1.5 1.5s-.673 1.5-1.5 1.5h-7C4.019 13 2 15.019 2 17.5S4.019 22 6.5 22h9.593a10.415 10.415 0 0 1-1.249-2zM5 2C3.346 2 2 3.346 2 5c0 3.188 3 5 3 5s3-1.813 3-5c0-1.654-1.346-3-3-3zm0 4.5a1.5 1.5 0 1 1 .001-3.001A1.5 1.5 0 0 1 5 6.5z"></path><path d="M19 14c-1.654 0-3 1.346-3 3 0 3.188 3 5 3 5s3-1.813 3-5c0-1.654-1.346-3-3-3zm0 4.5a1.5 1.5 0 1 1 .001-3.001A1.5 1.5 0 0 1 19 18.5z"></path></svg>
                            </div>
                            <div class="card-body" style="margin-top: -30px;">
                                <h1 class="card-title">{{ __('Discover Unique Experiences with Virtuard') }}</h1>
                                <p style="color: rgba(255,255,255,.8); margin-top: -10px;">{{ __('Virtuard offers a powerful solution for exploring and booking unique experiences through immersive virtual tours. Dive into the world of Virtuard and unlock the potential of virtual exploration and booking today!') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 d-md-block d-none">
                        <img width="100%" src="{{ asset('images/additional-features.png') }}" alt="">
                    </div>
                </div>
            </section>
        </div>

        <div class="bg-overlay">
            <section class="container get-started-container how-it-works">
                <div class="text-center mb-5 text-white section-header">
                    <h2 class="title">{{ __('How It Works') }}</h2>
                    <p class="description">{{ __('📍 Simple, fast, and no technical skills required!') }}</p>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-sm-6 col-12 p-2 mb-lg-0 mb-4">
                        <div class="bg-white rounded px-4 py-3 position-relative">
                            <div class="item-number">
                                <p>1</p>
                            </div>
                            <div class="d-flex justify-content-center">
                                <img src="{{ asset('images/how-it-works/1.png') }}" width="70%" alt="">
                            </div>
                            <h6 class="card-title mt-3 font-weight-semi-bold">{{ __('Upload your images of your property, hotel, or business.') }}</h6>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-12 p-2 mb-lg-0 mb-4">
                        <div class="bg-white rounded px-4 py-3 position-relative">
                            <div class="item-number">
                                <p>2</p>
                            </div>
                            <div class="d-flex justify-content-center">
                                <img src="{{ asset('images/how-it-works/2.png') }}" width="70%" alt="">
                            </div>
                            <h6 class="card-title mt-3 font-weight-semi-bold">{{ __('Create up to 3 iPanorama (360° virtual tours) to enhance the experience.') }}</h6>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-12 p-2 mb-lg-0 mb-4">
                        <div class="bg-white rounded px-4 py-3 position-relative">
                            <div class="item-number">
                                <p>3</p>
                            </div>
                            <div class="d-flex justify-content-center">
                                <img src="{{ asset('images/how-it-works/3.png') }}" width="70%" alt="">
                            </div>
                            <h6 class="card-title mt-3 font-weight-semi-bold">{{ __('Manage bookings directly from the platform.') }}</h6>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 col-12 p-2 mb-lg-0 mb-4">
                        <div class="bg-white rounded px-4 py-3 position-relative">
                            <div class="item-number">
                                <p>4</p>
                            </div>
                            <div class="d-flex justify-content-center">
                                <img src="{{ asset('images/how-it-works/4.png') }}" width="70%" alt="">
                            </div>
                            <h6 class="card-title mt-3 font-weight-semi-bold">{{ __('Publish your listing for 1 month or 1 year (with an annual plan).') }}</h6>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="bg-overlay">
            <section class="container get-started-container adventages">
                <div class="text-center mb-5 text-white section-header">
                    <h2 class="title">{{ __('Plans & Pricing') }}</h2>
                    <p class="description">🎉 {{ __('Free Trial – 1 Month FREE') }}</p>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12 px-2 py-4">
                        <div class="bg-white rounded px-4 py-3">
                            <h5 class="card-title font-weight-semi-bold">{{ __('Free Plan') }}</h5>
                            <div class="d-flex align-items-end">
                                <h1 class="text-primary">0$</h1>
                                <p class="text-muted" style="position: relative; top: 5px;">/30 day</p>
                            </div>
                            <div class="d-block mt-2">
                                <div class="d-flex align-items-start">
                                    <div style="margin-top: -4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                                    </div>
                                    <p class="ml-1" style="font-size: 14px;">{{ __('Create and publish your listing (hotel, property, or business)') }}</p>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div style="margin-top: -4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                                    </div>
                                    <p class="ml-1" style="font-size: 14px;">{{ __('Generate 1 Virtual Tour (iPanorama) valid for 1 month. (If you don’t subscribe after the first month, your listing will remain active, but the Virtual Tour will be deactivated)') }}</p>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div style="margin-top: -4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                                    </div>
                                    <p class="ml-1" style="font-size: 14px;">{{ __('Booking hotels') }}</p>
                                </div>
                                <a href="/plan" class="navbar-link btn btn-first px-4 py-3" style="width: 100%;">{{ __('Select') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 px-2 py-4">
                        <div class="bg-white rounded px-4 py-3">
                            <h5 class="card-title font-weight-semi-bold">{{ __('Monthly Plan') }}</h5>
                            <div class="d-flex align-items-end">
                                <h1 class="text-primary">10$</h1>
                                <p class="text-muted" style="position: relative; top: 5px;">/month</p>
                            </div>
                            <div class="d-block mt-2">
                                <div class="d-flex align-items-start">
                                    <div style="margin-top: -4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                                    </div>
                                    <p class="ml-1" style="font-size: 14px;">{{ __('Unlimited Listings (create as many listings as you want)') }}</p>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div style="margin-top: -4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                                    </div>
                                    <p class="ml-1" style="font-size: 14px;">{{ __('Up to 3 iPanorama (360° virtual tours)') }}</p>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div style="margin-top: -4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                                    </div>
                                    <p class="ml-1" style="font-size: 14px;">{{ __('Publish the tour for 1 month') }}</p>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div style="margin-top: -4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                                    </div>
                                    <p class="ml-1" style="font-size: 14px;">{{ __('Booking management') }}</p>
                                </div>
                                <a href="/plan" class="navbar-link btn btn-first px-4 py-3" style="width: 100%;">{{ __('Select') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 px-2 py-4">
                        <div class="bg-white rounded px-4 py-3">
                            <h5 class="card-title font-weight-semi-bold">{{ __('Annual Plan') }}</h5>
                            <div class="d-flex align-items-end">
                                <h1 class="text-primary">99$</h1>
                                <p class="text-muted" style="position: relative; top: 5px;">/year</p>
                            </div>
                            <div class="d-block mt-2">
                                <div class="d-flex align-items-start">
                                    <div style="margin-top: -4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                                    </div>
                                    <p class="ml-1" style="font-size: 14px;">{{ __('Unlimited Listings (create as many listings as you want)') }}</p>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div style="margin-top: -4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                                    </div>
                                    <p class="ml-1" style="font-size: 14px;">{{ __('Up to 3 iPanorama (360° virtual tours)') }}</p>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div style="margin-top: -4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                                    </div>
                                    <p class="ml-1" style="font-size: 14px;">{{ __('Booking management') }}</p>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div style="margin-top: -4px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" style="fill: #5191FA;"><path d="m10 15.586-3.293-3.293-1.414 1.414L10 18.414l9.707-9.707-1.414-1.414z"></path></svg>
                                    </div>
                                    <p class="ml-1" style="font-size: 14px;">{{ __('Publish the tours for 1 year') }}</p>
                                </div>
                                <a href="/plan" class="navbar-link btn btn-first px-4 py-3" style="width: 100%;">{{ __('Select') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="bg-overlay">
            <footer class="container" style="padding-top: 100px;">
                <div class="d-flex align-items-center justify-content-between" style="border-top: 1px solid rgba(255,255,255,.2);">
                    <div class="footer-logo">
                        <img src="{{ asset('images/virtuard-logo.png') }}" alt="Virtuard Logo" width="80">
                    </div>
                    <div class="flex">
                        <a href="mailto:info@virtuard.com" class="d-flex align-items-center text-white">
                            <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white"><path d="M20 4H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V6c0-1.103-.897-2-2-2zm0 2v.511l-8 6.223-8-6.222V6h16zM4 18V9.044l7.386 5.745a.994.994 0 0 0 1.228 0L20 9.044 20.002 18H4z"></path></svg>
                            info@virtuard.com
                        </a>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p class="footer-text text-center">{{ __('Copyright © Virtuard Reality Design. Company nr AHU-0175648.AH.01.11, registered in Gianyar, Bali, Indonesia.') }}</p>
                </div>
            </footer>
        </div>
    </main>

    <div id="viewer"></div>

    <script type="importmap">
        {
            "imports": {
                "three": "https://cdn.jsdelivr.net/npm/three/build/three.module.js",
                "@photo-sphere-viewer/core": "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core/index.module.js",
                "@photo-sphere-viewer/autorotate-plugin": "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/autorotate-plugin@5/index.module.js",
                "@photo-sphere-viewer/virtual-tour-plugin": "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/virtual-tour-plugin@5/index.module.js",
                "@photo-sphere-viewer/gallery-plugin": "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/gallery-plugin@5/index.module.js",
                "@photo-sphere-viewer/markers-plugin": "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin@5/index.module.js"
            }
        }
    </script>

    <script src="{{ asset('/libs/jquery-3.6.3.min.js') }}"></script>
    <script src="https://maps.google.com/maps/api/js?key={{ get_map_gmap_key() }}&libraries=places"></script>
    <script src="https://cdn.jsdelivr.net/npm/@google/markerclusterer@2.0.9/dist/markerclusterer.min.js"></script>

    {{-- <script type="module" src="{{ asset('/assets/js/landing.js') }}"></script> --}}

    <script type="module">
        const userAgent = navigator.userAgent || navigator.vendor || window.opera;

        // Deteksi jika dibuka dari Instagram atau Facebook
        if (userAgent.includes("Instagram") || userAgent.includes("FBAN") || userAgent.includes("FBAV")) {
            // Arahkan ke browser eksternal (Google Chrome di Android)
            window.location.href = "googlechrome://virtuard.com/landing";
            // window.location.href = "intent://virtuard.com/landing#Intent;scheme=https;package=com.android.chrome;end";
        }

        $('.nav-item.multi-lang').click(function() {
            $(this).toggleClass('active');
        })


        import { Viewer } from '@photo-sphere-viewer/core';
        import { MarkersPlugin } from '@photo-sphere-viewer/markers-plugin';
        import { AutorotatePlugin } from '@photo-sphere-viewer/autorotate-plugin';
        import { VirtualTourPlugin } from '@photo-sphere-viewer/virtual-tour-plugin';
        import { GalleryPlugin } from '@photo-sphere-viewer/gallery-plugin';

        const baseUrl = '/assets/images/';
        const baseUrl2 = 'https://photo-sphere-viewer-data.netlify.app/assets/';

        const container = document.createElement('section');
        const caption = 'Deep Blue Villa New <br> <b>&copy; virtuard.com</b>';

        const nodes = [
            {
                id: '1',
                panorama: baseUrl2 + 'tour/key-biscayne-1.jpg',
                thumbnail: baseUrl2 + 'tour/key-biscayne-1-thumb.jpg',
                // name: 'One',
                caption: `[1] ${caption}`,
                links: [
                    {
                        nodeId: '2',
                        position: { yaw: 10.0, pitch: 10.0 },
                    }
                ],
                // markers: [markerLighthouse],
                gps: [-80.156479, 25.666725, 3],
                sphereCorrection: { pan: '33deg' },
            },
            {
                id: '2',
                panorama: baseUrl2 + 'tour/key-biscayne-2.jpg',
                thumbnail: baseUrl2 + 'tour/key-biscayne-2-thumb.jpg',
                // name: 'Two',
                caption: `[2] ${caption}`,
                links: [{ nodeId: '3' }, { nodeId: '1' }],
                // markers: [markerLighthouse],
                gps: [-80.156168, 25.666623, 3],
                sphereCorrection: { pan: '42deg' },
            },
            {
                id: '3',
                panorama: baseUrl2 + 'tour/key-biscayne-3.jpg',
                thumbnail: baseUrl2 + 'tour/key-biscayne-3-thumb.jpg',
                // name: 'Three',
                caption: `[3] ${caption}`,
                links: [{ nodeId: '4' }, { nodeId: '2' }, { nodeId: '5' }],
                gps: [-80.155932, 25.666498, 5],
                sphereCorrection: { pan: '50deg' },
            },
            {
                id: '4',
                panorama: baseUrl2 + 'tour/key-biscayne-4.jpg',
                thumbnail: baseUrl2 + 'tour/key-biscayne-4-thumb.jpg',
                // name: 'Four',
                caption: `[4] ${caption}`,
                links: [{ nodeId: '3' }, { nodeId: '5' }],
                gps: [-80.156089, 25.666357, 3],
                sphereCorrection: { pan: '-78deg' },
            },
            {
                id: '5',
                panorama: baseUrl2 + 'tour/key-biscayne-5.jpg',
                thumbnail: baseUrl2 + 'tour/key-biscayne-5-thumb.jpg',
                // name: 'Five',
                caption: `[5] ${caption}`,
                links: [{ nodeId: '6' }, { nodeId: '3' }, { nodeId: '4' }],
                gps: [-80.156292, 25.666446, 2],
                sphereCorrection: { pan: '170deg' },
            },
            {
                id: '6',
                panorama: baseUrl2 + 'tour/key-biscayne-6.jpg',
                thumbnail: baseUrl2 + 'tour/key-biscayne-6-thumb.jpg',
                // name: 'Six',
                caption: `[6] ${caption}`,
                links: [{ nodeId: '5' }, { nodeId: '7' }],
                gps: [-80.156465, 25.666496, 2],
                sphereCorrection: { pan: '65deg' },
            },
            {
                id: '7',
                panorama: baseUrl2 + 'tour/key-biscayne-7.jpg',
                thumbnail: baseUrl2 + 'tour/key-biscayne-7-thumb.jpg',
                // name: 'Seven',
                caption: `[7] ${caption}`,
                links: [{ nodeId: '6' }],
                gps: [-80.15707, 25.6665, 3],
                sphereCorrection: { pan: '110deg', pitch: -3 },
            },
        ];

        const viewer = new Viewer({
            container: 'viewer',
            // panorama: baseUrl + 'tour-example-360.jpg',
            caption: 'Copyright &copy; 2025 virtuard.com. All Right Reserved',
            loadingImg: null,
            // touchmoveTwoFingers: true,
            defaultYaw: 0,
            defaultPitch: 0,
            defaultZoomLvl: 20,
            fisheye: true,
            navbar: [
                "fullscreen"
            ],
            plugins: [
                [MarkersPlugin, {
                    markers: [
                        {
                            id: 'Register For Free',
                            elementLayer: container,
                            position: { yaw: 0, pitch: 0.2},
                            rotation: { yaw: 0 },
                        },
                    ],
                }],
                [AutorotatePlugin, {
                    autorotatePitch: '3deg',
                }],
                [GalleryPlugin, {
                    thumbnailSize: { width: 100, height: 100 },
                }],
                [VirtualTourPlugin, {
                    positionMode: 'gps',
                    renderMode: '3d',
                    nodes: nodes,
                    startNodeId: '1',
                }],
            ],
        });

        function handleFullscreenChange() {
            const mainElement = document.getElementById('main');
            if (document.fullscreenElement) {
                mainElement.style.zIndex = '0';
                viewer.setOptions({ navbar: ['fullscreen'] });
            } else {
                mainElement.style.zIndex = '99';
                viewer.setOptions({ navbar: [] });
            }
        }

        // Add event listeners for fullscreen change
        document.addEventListener('fullscreenchange', handleFullscreenChange);
        document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
        document.addEventListener('mozfullscreenchange', handleFullscreenChange);
        document.addEventListener('MSFullscreenChange', handleFullscreenChange);


        document.getElementById("btn-demo").addEventListener("click", function () {
            let viewer = document.getElementById("viewer");

            if (viewer.requestFullscreen) {
                viewer.requestFullscreen();
            } else if (viewer.mozRequestFullScreen) { // Firefox
                viewer.mozRequestFullScreen();
            } else if (viewer.webkitRequestFullscreen) { // Chrome, Safari, Opera
                viewer.webkitRequestFullscreen();
            } else if (viewer.msRequestFullscreen) { // IE/Edge
                viewer.msRequestFullscreen();
            }
        });

        var listMaps = [];
        let map, markerCluster, currentInfoWindow;
        let mapMarkers = [];
        let clusterConfig = {
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
            gridSize: 60,
            minimumClusterSize: 4,
            styles: [
                {
                    textColor: 'white',
                    url: '/icon/circle-24.png',
                    height: 24,
                    width: 24,
                    textSize: 12,
                    backgroundPosition: 'center',
                    backgroundRepeat: 'no-repeat',
                    backgroundColor: 'red'
                }
            ]
        };


        function initMap() {
            // Default location if geolocation fails
            const defaultLocation = { lat: 0, lng: 0 };

            // Initialize map centered at default location
            map = new google.maps.Map(document.getElementById('gmap'), {
                center: defaultLocation,
                zoom: 8,
            });

            // Try HTML5 geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };

                        // Set map center to user's current location
                        map.setCenter(userLocation);

                        // Add marker for user's location
                        // new google.maps.Marker({
                        //     position: userLocation,
                        //     map: map,
                        //     title: "You are here!"
                        // });

                        // Update hidden input fields if needed
                        $('#explore_map_lat').val(userLocation.lat);
                        $('#explore_map_lgn').val(userLocation.lng);
                    },
                    () => {
                        console.warn("Geolocation failed or was denied. Using default location.");
                        map.setCenter(defaultLocation);
                    }
                );
            } else {
                // Browser doesn't support Geolocation
                console.error("Your browser doesn't support geolocation.");
                map.setCenter(defaultLocation);
            }
        }

        function fetchMap(attr) {
            $('#map-loading').show();

            $.ajax({
                type: "POST",
                url: "/explore/map/search",
                data: attr,
                success: function(data) {
                    let maps = data.data.filter((item) => item.category == 'business' || item.category == 'hotel' || item.category == 'space');

                    resetMarkers();
                    addMarkersToMap(maps);

                    $('#map-loading').hide();
                },
                error: function(xhr) {
                    $('#map-loading').hide();
                }
            });
        }

        function resetMarkers() {
            if(markerCluster){
                markerCluster.clearMarkers();
                mapMarkers.forEach(marker => marker.setMap(null));
                mapMarkers = [];
            }

            const mapCenter = getCenterMarker([])
            map.setCenter(mapCenter)
        }

        function onFetchData(attr) {
            fetchMap(attr);
        }

        function getCenterMarker(mdata) {
            let map_lat = $('#explore_map_lat').val() ?? 0;
            let map_lgn = $('#explore_map_lgn').val() ?? 0;

            if (mdata.length !== 0) {
                let mdata_lat = mdata[0].map_lat ?? 0;
                let mdata_lgn = mdata[0].map_lgn ?? 0;

                if (mdata_lat) {
                    map_lat = mdata_lat
                }
                if (mdata_lgn) {
                    map_lat = mdata_lgn
                }
            }

            let center = {
                lat: Number(map_lat),
                lng: Number(map_lgn)
            };

            return center;
        }

        function addMarkersToMap(markerData) {
            markerData.forEach((data) => {
                const lat = Number(data.map_lat);
                const lng = Number(data.map_lng);
                const newMarker = new google.maps.Marker({
                    position: { lat, lng },
                    map: map,
                    title: data.title,
                    icon: data.icon,
                });

                let contentString = getPopupMarker(data);

                const infowindow = new google.maps.InfoWindow({
                    content: contentString,
                });

                newMarker.addListener("click", () => {
                    if (currentInfoWindow != null) {
                        currentInfoWindow.close();
                    }

                    infowindow.open(map, newMarker);

                    currentInfoWindow = infowindow;
                });

                mapMarkers.push(newMarker);
            });

            // map setCenter
            const mapCenter = getCenterMarker(markerData);
            map.setCenter(mapCenter);

            // Create the MarkerClusterer
            markerCluster = new MarkerClusterer(map, mapMarkers, clusterConfig);
        }

        function getPopupMarker(data) {
            const contentString =
                `
                    <div class="card" style="overflow: hidden;">
                        <div class="card card-custom card-has-bg click-col" style="background-image: url(${data.banner_image_id}); width: 250px;">
                            <div class="card-img-overlay d-flex align-items-end">
                                <div>
                                    <h5 class="card-title mt-0 mb-0" style="text-overflow: ellipsis; overflow:hidden; font-size: 16px;">
                                        <a class="text-white" href="${data.url}">${data.title}</a>
                                    </h5>
                                    <span class="text-white"> <i class="fa fa-map-marker"></i> ${data.address}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;

            return contentString;
        }

        initMap()
        addMarkersToMap(listMaps)
        onFetchData()
    </script>
</body>
</html>
