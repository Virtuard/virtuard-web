<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore 3D & 360 Virtual Tours | Virtual Tours by Virtuard</title>

    <meta name="description" content="Want to showcase your property, hotel, restaurant, or shop like never before? With Virtuard, you can create your listing and 360° Virtual Tour on your own – at no cost and with no assistance needed!">
    {{-- <meta name="keywords" content="keyword1, keyword2, keyword3"> --}}
    {{-- <meta name="author" content="Your Name or Company"> --}}

    <!-- favicon -->
    <link rel="icon" href="{{ asset('images/virtuard-logo.png') }}" type="image/x-icon">

    {{-- <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}"> --}}
    <style>
        * {margin: 0; padding: 0;}

        body { 
            font-family: 'Urbanist', arial; 
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
            background-color: rgba(0,0,0,.5);
            width: 100%;
            height: 325%;
        }

        #main .navbar {
            /* position: fixed;
            right: 0;
            left: 0; */
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 20px;
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
            margin-top: 80px;
            margin-bottom: 80px;
            display: flex;
            justify-content: center;
            height: 90vh;
        }

        #header .header-content {
            max-width: 700px;
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
            margin-top: 150px;
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

        /* .card-feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        } */

        .card-feature .card-icon svg {
            fill: #5191FA;
            width: 40px;
            height: 40px;
        }

        .card-feature .card-title {
            font-size: 1rem;
            margin-top: 10px;
            font-weight: 600;
            color: #fff;
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

        footer {
            border-top: 1px solid rgba(255,255,255,.2);
        }

        .footer-text {
            color: #ddd;
            font-size: 14px;
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

    {{-- icons --}}
    <link href="{{ asset('libs/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/icofont/icofont.min.css') }}" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/photo-sphere-viewer.js"></script>

</head>
<body>
    <main id="main">
        {{-- <section class="container">
            <h1>Virtuard</h1>
            <p class="description first">Create Your Listing and Virtual Tour in Minutes – For Free!</p>
            <p class="description">Want to showcase your property, hotel, restaurant, or shop like never before? With Virtuard, you can create your listing and 360° Virtual Tour on your own – at no cost and with no assistance needed!</p>
            <p class="description">
                <div style="display: flex; justify-content: center;">
                    <ul style="text-align: left; font-size: 18px;" class="list-group">
                        <li>
                            <span>✅</span> 
                            <span>Free registration</span>
                        </li>
                        <li>
                            <span>✅</span> 
                            <span>1-month free trial, no commitment</span>
                        </li>
                        <li>
                            <span>✅</span> 
                            <span>Create your Virtual Tour in just a few clicks</span>
                        </li>
                        <li>
                            <span>✅</span> 
                            <span>Showcase your space in an innovative and engaging way</span>
                        </li>
                        <li>
                            <span>✅</span> 
                            <span>Credit card is not required</span>    
                        </li>
                    </ul>
                </div>
            </p>
            <p class="description">
                Don’t miss the chance to stand out from the competition! 🚀<br>
                Get started now – it's free!
            </p>
            <form class="register-form" action="{{route('auth.register.store')}}" method="POST">
                @csrf
                @if(setting_item('google_enable'))
                    <div class="advanced">
                        <div class="row">
                            <div class="col-xs-12 col-sm-4">
                                <a href="javascript:void(0);" id="btn-google-login" class="btn btn_login_gg_link" data-channel="google">
                                    <i class="input-icon fa fa-google"></i>
                                    {{ __('Sign Up With Google') }}
                                </a>
                            </div>
                            
                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                            
                            <script>
                                var hasAffiliateId = {{ Cookie::has('affiliate_id') ? 'true' : 'false' }};
                            
                                document.getElementById('btn-google-login').addEventListener('click', function () {
                                    // if (hasAffiliateId) {
                                    //     Swal.fire({
                                    //         icon: 'error',
                                    //         title: 'Sorry',
                                    //         text: 'You are using the affiliate feature, please register without using Google.',
                                    //     });
                                    // } else {
                                    //     window.location.href = "{{ url('social-login/google') }}";
                                    // }
                                    window.location.href = "{{ url('social-login/google') }}";
                                });
                            </script>
                        </div>
                    </div>
                @endif
                <div class="mb-3">
                    <h4 class="sign-up-text">Sign Up for Free</h4>
                </div>
                <div class="alert-message hidden error">Registration failed</div>
                <div class="form-grid">
                    <div class="form-group" style="padding-right: 10px;">
                        <label for="first_name" class="form-label required">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" autocomplete="off" aria-describedby="firstNameHelp" placeholder="{{__("Your first name")}}">
                        <span class="error-message error error-first_name"></span>
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="form-label required">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" autocomplete="off" aria-describedby="lastNameHelp" placeholder="{{__("Your last name")}}">
                        <span class="error-message error error-last_name"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone" class="form-label">Phone (optional)</label>
                    <input type="text" class="form-control" id="phone" name="phone" autocomplete="off" aria-describedby="phoneHelp" placeholder="{{__("Your phone number")}}">
                    <span class="error-message error error-phone"></span>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label required">Email address</label>
                    <input type="text" class="form-control" id="email" name="email" autocomplete="off" aria-describedby="emailHelp" placeholder="{{__("Your email address")}}">
                    <span class="error-message error error-email"></span>
                </div>
                <div class="form-group password-group">
                    <label for="password" class="form-label required">Password</label>
                    <input type="password" class="form-control" id="password" name="password" autocomplete="off" placeholder="{{__("Your password")}}">
                    <span class="input-icon icofont-eye" id="toggle-password-register"></span>
                </div>
                <span class="error-message error error-password"></span>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="term" id="privacyPolicyCheck">
                    <label class="form-check-label" for="privacyPolicyCheck">I have read and accept the <a href="https://virtuard.com/page/terms-and-conditions-for-virtuard-ltd" target="_blank">Terms and Privacy Policy</a></label>
                </div>
                <span class="error-message error error-term"></span>
                @if(setting_item("user_enable_register_recaptcha"))
                    <div class="form-group">
                        {{recaptcha_field($captcha_action ?? 'register')}}
                    </div>
                    <div><span class="error-message error error-g-recaptcha-response"></span></div>
                @endif
                <div class="error message-error invalid-feedback"></div>
                <div>
                    <button type="submit" class="btn btn-primary form-submit">
                        Sign Up <span class="loader"></span>
                    </button>
                    <a href="https://virtuard.com/create" target="_blank">
                        <button type="button" class="btn btn-secondary">Create Your Listing</button>
                    </a>
                </div>
            </form>
        </section> --}}
        <nav>
            <div class="navbar container">
                <a href="/" class="navbar-brand">
                    <img src="{{ asset('images/virtuard-logo.png') }}" alt="Virtuard Logo" width="80">
                </a>
                <ul class="mt-3">
                    <li class="nav-item language">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zM4 12c0-.899.156-1.762.431-2.569L6 11l2 2v2l2 2 1 1v1.931C7.061 19.436 4 16.072 4 12zm14.33 4.873C17.677 16.347 16.687 16 16 16v-1a2 2 0 0 0-2-2h-4v-3a2 2 0 0 0 2-2V7h1a2 2 0 0 0 2-2v-.411C17.928 5.778 20 8.65 20 12a7.947 7.947 0 0 1-1.67 4.873z"></path></svg>
                        <a href="/landing?lang=id" class="navbar-link">Indonesian</a>
                    </li>
                    <li class="nav-item">
                        <a href="/register" class="navbar-link btn btn-first px-4 py-3">Get Started</a>
                    </li>
                </ul>
            </div>
        </nav>
        <header id="header" class="container">
            <div class="header-content">
                <h1 class="title">Explore 3D & 360 <br> <span>Virtual Tours</span></h1>
                <p class="description">Want to showcase your property, hotel, restaurant, or shop like never before? With Virtuard, you can create your listing and 360° Virtual Tour on your own – at no cost and with no assistance needed!</p>
                <button class="btn btn-second" id="btn-demo">Virtual Tour Demo</button>

                <div class="mouse-container">
                    <div class="mouse"></div>
                </div>
            </div>
        </header>
        <section class="container">
            <div>
                <div class="text-center mb-5 text-white section-header">
                    <h2 class="title">Our Locations</h2>
                    <p class="description">Discover properties, hotels, restaurants, and shops from various locations with Virtuard. Whether you're exploring a new city or showcasing your space, our platform offers an immersive 360° virtual experience to bring every location to life</p>
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
        <section class="container get-started-container">
            <div class="text-center mb-5 text-white section-header">
                <h2 class="title">Get Started for Free</h2>
                <p class="description">Virtuard offers a range of features to help you create and explore 3D & 360° Virtual Tours. Whether you're a property owner, real estate agent, or business owner, our platform provides the tools you need to showcase your space and attract customers.</p>
                <p class="description">Don’t miss the chance to stand out from the competition! 🚀</p>
            </div>
            <div class="row">
                <div class="col-md-6 col-12 d-md-block d-none">
                    <img width="100%" src="{{ asset('images/benefit-img.png') }}" alt="">
                </div>
                <div class="col-md-6 col-12 mt-md-5 mt-0">
                    <div class="card-feature">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706z"></path></svg>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Free Registration</h3>
                        </div>
                    </div>
                    <div class="card-feature">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706z"></path></svg>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">1-month free trial, no commitment</h3>
                        </div>
                    </div>
                    <div class="card-feature">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706z"></path></svg>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Create your Virtual Tour in just a few clicks</h3>
                        </div>
                    </div>
                    <div class="card-feature">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706z"></path></svg>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Showcase your space in an innovative and engaging way</h3>
                        </div>
                    </div>
                    <div class="card-feature">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706z"></path></svg>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Credit card is not required</h3>
                        </div>
                    </div>
                    <a href="/register" class="mt-5 btn btn-second px-4 py-3">Get Started</a>
                </div>
            </div>
        </section>
        <footer>
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
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
                    <p class="footer-text text-center">Copyright © Virtuard Reality Design. Company nr AHU-0175648.AH.01.11, registered in Gianyar, Bali, Indonesia.</p>
                </div>
            </div>
        </footer>
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

    <script>
        // document.getElementById('toggle-password-register').addEventListener('click', function () {
        //     var passwordField = document.getElementById('password'); 
        //     var passwordType = passwordField.type === 'password' ? 'text' : 'password';
        //     passwordField.type = passwordType;
    
        //     if (passwordType === 'password') {
        //         this.classList.remove('icofont-eye-blocked'); 
        //         this.classList.add('icofont-eye'); 
        //     } else {
        //         this.classList.remove('icofont-eye'); 
        //         this.classList.add('icofont-eye-blocked'); 
        //     }
        // });
    </script>

    <script src="{{ asset('/libs/jquery-3.6.3.min.js') }}"></script>
    <script src="https://maps.google.com/maps/api/js?key={{ get_map_gmap_key() }}&libraries=places"></script>
    <script src="https://cdn.jsdelivr.net/npm/@google/markerclusterer@2.0.9/dist/markerclusterer.min.js"></script>
    
    <script type="module" src="{{ asset('/assets/js/landing.js') }}"></script>
    {{-- <script src="{{ asset('/assets/js/landing-register.js') }}"></script> --}}
    <script>
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
            // Initialize the autocomplete
            // initAutocomplete();

            map = new google.maps.Map(document.getElementById('gmap'), {
                center: { lat: 0, lng: 0 },
                zoom: 3,
                mapTypeId: 'satellite'
            });

            // add merker to map
            // addMarkersToMap(listMaps);
        }

        function fetchMap(attr) {
            $('#map-loading').show();

            $.ajax({
                type: "POST",
                url: "/explore/map/search",
                data: attr,
                success: function(data) {
                    let maps = data.data;

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