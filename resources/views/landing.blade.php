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

    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">
    {{-- <style>
        * {margin: 0; padding: 0;}
        body { font-family: 'Urbanist', arial; }
        #viewer { width: 100vw; height: 100vh; }

        a {
            text-decoration: none;
            color: #5191FA;
        }

        #background {
            background-color: rgba(0,0,0,.4);
            width: 100%;
            height: 95vh;
            position: fixed;
            z-index: 99;
        }

        #main {
            position: fixed;
            z-index: 99;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* width: 100%;
            height: 100vh; */
            /* display: flex;
            justify-content: center;
            align-items: center; */
        }

        .container {
            text-align: center;
            padding: 40px;
            /* background: rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(14.4px);
            -webkit-backdrop-filter: blur(14.4px);
            border: 1px solid rgba(255, 255, 255, 0.46); */
            background-color: #fff;
            border-radius: 10px;
            min-width: 600px;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: scroll;
            scrollbar-width: none; 
            -ms-overflow-style: none;
        }

        .container::-webkit-scrollbar {
        display: none;
        }

        .container h1 {
            font-size: 52px;
            color: #5191FA;
            margin-bottom: 20px;
            font-weight: 800;
        }

        .container .description {
            font-size: 18px;
            color: #555;
            margin-bottom: 28px;
        }

        .container .description.first {
            color: #111;
            font-size: 24px;
            font-weight: 700;
        }

        .container .btn-primary {
            background-color: #5191FA;
            /* border: 1px solid #5191FA; */
            border: none;
            color: #eee;
            padding: 16px 30px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 5px;
            transition:  .3s;
            width: 100%;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .container .btn-primary:hover {
            opacity: .75;
            /* background-color: rgba(79, 70, 229, .1); */
            /* border: 1px solid #5191FA; */
            /* color: #5191FA; */
        }

        .container .btn-secondary {
            color: #5191FA;
            /* border: 1px solid #5191FA; */
            border: none;
            background-color: #eee;
            padding: 16px 30px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 5px;
            transition:  .3s;
            width: 100%;
            margin-top: 20px;
        }

        .container .btn-secondary:hover {
            opacity: .75;
            /* color: rgba(79, 70, 229, .1); */
            /* border: 1px solid #5191FA; */
            /* background-color: #5191FA; */
        }

        .container form {
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
            background-color: #fff;
            border: 1px solid #aaa;
            padding:32px 32px 32px 20px;
            border-radius: 8px;
            width: 80%;
        }

        .container form h4 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .container form .form-group {
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .container form .form-check {
            display: flex;
            align-items: start;
            gap: 5px;
            margin-bottom: 12px;
        }

        .container form .form-check-input {
            position: relative;
            top: 2px;
        }
        .container form .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            /* margin-bottom: 20px; */
        }

        .container form label {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }

        .container form .form-label.required::after {
            content: '*';
            color: red;
        }

        .container .alert-message {
            display: block;
            padding: 16px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .container .alert-message.hidden,
        .container .error-message.hidden {
            display: none;
        }

        .container .alert-message.error {
            color: rgb(220, 38, 38);
            background-color: rgb(254, 226, 226);;
        }

        .container .alert-message.success {
            color: rgb(22, 163, 74);
            background-color: rgb(220, 252, 231);;
        }

        .container .error-message {
            color: rgb(220, 38, 38);
            font-size: 12px;
            margin-top: -3px;
            display: block;
            text-align: start;
        }

        .container .error-message.error-password {
            margin-top: -6px;
            margin-bottom: 10px;
        }

        .container .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .container .list-group {
            margin-bottom: 20px;
        }

        .container .list-group li {
            list-style: none;
            display: flex;
            align-items: start;
            gap: 5px;
            margin-bottom: 4px;
        }

        .container .list-group li span:first-child {
            position: relative;
            top: -3px;
        }

        .loader {
            display: none !important;
            width: 15px;
            height: 15px;
            border: 2px solid #FFF;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
            }

            @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        } 
        .loader.active {
            display: inline-block !important;
        }

        .container .advanced {
            margin-bottom: 16px;
        }

        .btn_login_gg_link {
            background: #f34a38;
            color: #fff;
            border-radius: 5px;
            font-size: 14px;
            display: block;
            padding: 16px 30px;
            margin-top: 12px;
            transition: .3s;
            font-weight: 600;
        }

        .btn_login_gg_link:hover {
            opacity: .75;
        }

        .input-icon {
            margin-right: 4px;
        }

        .password-group {
            position: relative;
        }

        #toggle-password-register {
            position: absolute;
            right: -15px;
            bottom: 10px;
            cursor: pointer;
            background-color: #fff;
            padding: 2px 10px;
        }

        /* .psv-virtual-tour-link {
            width: 30px !important; 
            height: 30px !important;
            background-color: #111 !important;
            border: 20px solid #fff !important;
            transform: rotate(45deg) !important;
        }
        .psv-virtual-tour-link svg {
            display: none;
        } */

        @media (max-width: 768px) {
            br.break {
                display: none;
            }

            .container {
                min-width: 500px;
            }
        }

        @media (max-width: 600px) {
            .container {
                min-width: 400px;
                padding: 20px;
            }
            .container .form-check-label {
                font-size: 12px;
                text-align: left;
            }
        }

        @media (max-width: 500px) {
            .container {
                min-width: 400px;
            }
            .container .form-grid {
                grid-template-columns: repeat(1, minmax(0, 1fr));
                gap: 0;
            }
            .container h1 {
                font-size: 40px;
            }
            .container .description {
                font-size: 16px;
            }
            .container .list-group {
                font-size: 14px !important;
            }
            .btn-primary, .btn-secondary {
                font-size: 12px !important;
            }
            .sign-up-text {
                font-size: 20px !important;
            }
        }

        @media (max-width: 440px) {
            .container {
                min-width: 300px;
            }
        }

        @media (max-width: 320px) {
            .container {
                min-width: 250px;
            }
        }
    </style> --}}
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
        <nav class="navbar container">
            <a href="/" class="navbar-brand">
                <img src="{{ asset('images/virtuard-logo.png') }}" alt="Virtuard Logo" width="80">
            </a>
            <ul>
                <li class="nav-item language">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zM4 12c0-.899.156-1.762.431-2.569L6 11l2 2v2l2 2 1 1v1.931C7.061 19.436 4 16.072 4 12zm14.33 4.873C17.677 16.347 16.687 16 16 16v-1a2 2 0 0 0-2-2h-4v-3a2 2 0 0 0 2-2V7h1a2 2 0 0 0 2-2v-.411C17.928 5.778 20 8.65 20 12a7.947 7.947 0 0 1-1.67 4.873z"></path></svg>
                    <a href="/landing?lang=id" class="navbar-link">Indonesian</a>
                </li>
                <li class="nav-item">
                    <a href="/landing/register" class="navbar-link btn btn-first px-4 py-3">Get Started</a>
                </li>
            </ul>
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
        <section class="container">
            <div class="text-center mb-5 text-white section-header">
                <h2 class="title">Get Started for Free</h2>
                <p class="description">Virtuard offers a range of features to help you create and explore 3D & 360° Virtual Tours. Whether you're a property owner, real estate agent, or business owner, our platform provides the tools you need to showcase your space and attract customers.</p>
                <p class="description">Don’t miss the chance to stand out from the competition! 🚀</p>
            </div>
            <div class="row">
                <div class="col">
                    <img width="100%" src="{{ asset('images/benefit-img.png') }}" alt="">
                </div>
                <div class="col mt-5">
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
                    <a href="/landing/register" class="mt-5 btn btn-second px-4 py-3">Get Started</a>
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
                    <p class="footer-text text-center">Copyright © Virtuard Ltd. Company nr 14235775, registered in England and Walsses.</p>
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
    {{-- <script>
        const userAgent = navigator.userAgent || navigator.vendor || window.opera;

        // Deteksi jika dibuka dari Instagram atau Facebook
        if (userAgent.includes("Instagram") || userAgent.includes("FBAN") || userAgent.includes("FBAV")) {
            // Arahkan ke browser eksternal (Google Chrome di Android)
            window.location.href = "googlechrome://virtuard.com/landing"; 
            // window.location.href = "intent://virtuard.com/landing#Intent;scheme=https;package=com.android.chrome;end";
        }
  
        $('.register-form [type=submit]').click(function (e) {
            e.preventDefault();
            let form = $(this).closest('.register-form');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': form.find('meta[name="csrf-token"]').attr('content')
                }
            });
            // console.log(form)
            // console.log(form.find('input[name=email]').val());
            $.ajax({
                'url':  '/register',
                'data': {
                    'email': form.find('input[name=email]').val(),
                    'password': form.find('input[name=password]').val(),
                    'first_name': form.find('input[name=first_name]').val(),
                    'last_name': form.find('input[name=last_name]').val(),
                    'phone': form.find('input[name=phone]').val(),
                    'term': form.find('input[name=term]').is(":checked") ? 1 : '',
                    // 'g-recaptcha-response': form.find('[name=g-recaptcha-response]').val(),
                    'is_auto_login': true
                },
                'type': 'POST',
                beforeSend: function () {
                    form.find('.error').hide();
                    form.find('.loader').addClass('active')
                    $(".form-submit").attr('disabled', true);
                },
                success: function (data) {
                    // console.log(data)
                    form.find('.loader').removeClass('active');
                    $(".form-submit").attr('disabled', false);
                    if (data.error === true) {
                        if (data.messages !== undefined) {
                            for(var item in data.messages) {
                                var msg = data.messages[item];
                                form.find('.error-'+item).show().text(msg[0]);
                            }
                        }
                        if (data.messages.message_error !== undefined) {
                            form.find('.alert-message.error').removeClass('hidden').html(data.messages.message_error[0]);

                        }
                    }
                    if (typeof BravoReCaptcha !== 'undefined') {
                        BravoReCaptcha.reset('register');
                        BravoReCaptcha.reset('register_normal');
                    }
                    if (data.redirect !== undefined) {
                        window.location.href = data.redirect
                    }
                },
                error:function (e) {
                    // console.log(e)
                    form.find('.loader').removeClass('active');
                    $(".form-submit").attr('disabled', false);
                    if(typeof e.responseJSON !== "undefined" && typeof e.responseJSON.message !='undefined'){
                        form.find('.message-error').show().html('<div class="alert alert-danger">' + e.responseJSON.message + '</div>');
                    }

                    if (typeof BravoReCaptcha !== 'undefined') {
                        BravoReCaptcha.reset('register');
                        BravoReCaptcha.reset('register_normal');
                    }
                }
            });
        })
    </script> --}}
</body>
</html>