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
    <link rel="icon" href="/images/virtuard-logo.png" type="image/x-icon">

    <link rel="stylesheet" href="/assets/css/landing.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core/index.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/virtual-tour-plugin/index.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/gallery-plugin/index.css" />
    <!-- Urbanist Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/photo-sphere-viewer.js"></script>

</head>
<body>
    <main id="main">
        <section class="container">
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
            <!-- <p class="description">
                Create Your Listing and Virtual Tour in Minutes – For Free!<br><br>
                Want to showcase your property, hotel, restaurant, or shop like never before? With Virtuard, you can create your listing and 360° Virtual Tour on your own – at no cost and with no assistance needed!
                <div style="display: flex; justify-content: center;">
                    <div style="text-align: left; font-size: 18px;" class="list-group">
                        ✅ Free registration<br>
                        ✅ 1-month free trial, no commitment<br>
                        ✅ Create your Virtual Tour in just a few clicks<br>
                        ✅ Showcase your space in an innovative and engaging way<br>
                        ✅ Credit card is not required<br><br>
                    </div>
                </div>
                Don’t miss the chance to stand out from the competition! 🚀<br>
                Get started now – it's free!
            </p> -->
            <form class="register-form">
                <div class="mb-3">
                    <h4 class="sign-up-text">Sign Up for Free</h4>
                </div>
                <div class="alert-message hidden error">Registration failed</div>
                <div class="alert-message hidden success">Account created successfully</div>
                <div class="form-grid">
                    <div class="form-group" style="padding-right: 10px;">
                        <label for="first_name" class="form-label required">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" aria-describedby="firstNameHelp" placeholder="Your first name">
                        <p class="error-message first_name hidden"></p>
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="form-label required">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" aria-describedby="lastNameHelp" placeholder="Your last name">
                        <p class="error-message last_name hidden"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" aria-describedby="phoneHelp" placeholder="Your phone number">
                    <p class="error-message phone hidden"></p>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label required">Email address</label>
                    <input type="text" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Your email address">
                    <p class="error-message email hidden"></p>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label required">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Your password">
                    <p class="error-message password hidden"></p>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="term" id="privacyPolicyCheck">
                    <label class="form-check-label" for="privacyPolicyCheck">I have read and accept the <a href="https://virtuard.com/page/terms-and-conditions-for-virtuard-ltd" target="_blank">Terms and Privacy Policy</a></label>
                </div>
                <p class="error-message term hidden"></p>
                <div>
                    <button type="submit" class="btn btn-primary">
                        Sign Up <span class="loader"></span>
                    </button>
                    <a href="https://virtuard.com/create" target="_blank">
                        <button type="button" class="btn btn-secondary">Create Your Listing</button>
                    </a>
                </div>
            </form>
        </section>
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

    <script type="module" src="/assets/js/landing.js"></script>
    <script src="/assets/js/landing-register.js"></script>
</body>
</html>