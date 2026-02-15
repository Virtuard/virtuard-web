<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <link rel="icon" href="https://virtuard.com/uploads/0000/1/2026/02/14/logodefinitivo.png" type="image/x-icon">
    @if($isSharingMode && $imgUrl)
    <!-- Open Graph for Social Previews (only for sharing mode) -->
    <meta property="og:title" content="{{ $title }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="https://virtuard.com/uploads/0000/1/2026/02/14/logodefinitivo1.png" />
    <meta property="og:description" content="Can you solve this puzzle? Click to play!" />
    <meta property="og:url" content="{{ url()->current() }}" />
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:image" content="https://virtuard.com/uploads/0000/1/2026/02/14/logodefinitivo1.png">
    <meta name="twitter:description" content="Can you solve this puzzle? Click to play!">
    @endif
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .preview-container {
            padding: 30px 20px;
            text-align: center;
        }

        .preview-image {
            width: 100%;
            max-width: 500px;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }

        .preview-image:hover {
            transform: scale(1.02);
        }

        .icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-android {
            background: linear-gradient(135deg, #3ddc84 0%, #2bb673 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(61, 220, 132, 0.4);
        }

        .btn-android:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(61, 220, 132, 0.6);
        }

        .btn-ios {
            background: linear-gradient(135deg, #007aff 0%, #0051d5 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 122, 255, 0.4);
        }

        .btn-ios:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 122, 255, 0.6);
        }

        .btn-icon {
            margin-right: 10px;
            font-size: 20px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #667eea;
        }

        .loading.active {
            display: block;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            color: #c33;
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 20px;
            }

            .preview-container {
                padding: 20px 15px;
            }

            .btn {
                padding: 14px 24px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @if($isSharingMode && $imgUrl)
            <!-- Sharing Mode: Show image preview with deep linking -->
            <div class="header">
                <h1>{{ $title }}</h1>
                <p>Open the app to play this puzzle!</p>
            </div>

            <div class="preview-container">
                <img src="{{ $imgUrl }}" alt="Puzzle Preview" class="preview-image" id="puzzleImage">
                
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p style="margin-top: 10px;">Opening app...</p>
                </div>

                <div class="action-buttons" id="actionButtons">
                    @if($isAndroid)
                        <a href="{{ $androidStoreLink }}" 
                           class="btn btn-android" 
                           id="androidBtn"
                           onclick="trackClick('{{ $deepLink }}', '{{ $androidStoreLink }}', 'android')">
                            <span class="btn-icon">🤖</span>
                            Download for Android
                        </a>
                    @elseif($isIos)
                        <a href="{{ $iosStoreLink }}" 
                           class="btn btn-ios" 
                           id="iosBtn"
                           onclick="trackClick('{{ $iosDeepLink }}', '{{ $iosStoreLink }}', 'ios')">
                            <span class="btn-icon">🍎</span>
                            Download for iOS
                        </a>
                    @else
                        <a href="{{ $androidStoreLink }}" class="btn btn-primary" id="desktopBtn">
                            <span class="btn-icon">📱</span>
                            Get the App
                        </a>
                    @endif
                </div>
            </div>
        @else
            <!-- Placeholder Mode: Show game placeholder -->
            <div class="preview-container">
                <div class="icon">🧩</div>
                <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    Puzzle AR Web Game
                </h1>

                @if(isset($webGameUrl) && $webGameUrl)
                    <p>Loading web game...</p>
                    <script>
                        window.location.href = "{{ $webGameUrl }}";
                    </script>
                @else
                    <p style="font-size: 18px; color: #666; margin-bottom: 30px; line-height: 1.6;">
                        Web game is coming soon!
                    </p>
                @endif
            </div>
        @endif
    </div>

    @if($isSharingMode && $imgUrl)
    <script>
        var isAndroid = @json($isAndroid);
        var isIos = @json($isIos);
        var deepLink = @json($deepLink ?? '');
        var iosDeepLink = @json($iosDeepLink ?? '');
        var androidStoreLink = @json($androidStoreLink ?? '');
        var iosStoreLink = @json($iosStoreLink ?? '');

        function trackClick(deepLinkUrl, fallbackUrl, platform) {
            // Track the click
            fetch('{{ route("puzzle.track") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    deep_link: deepLinkUrl,
                    redirect_url: fallbackUrl,
                    platform: platform,
                    img_url: '{{ $imgUrl }}',
                    title: '{{ $title }}'
                })
            }).catch(err => console.log('Tracking error:', err));

            // Show loading
            document.getElementById('loading').classList.add('active');
            document.getElementById('actionButtons').style.display = 'none';

            // Try to open app
            if (platform === 'android' && deepLinkUrl) {
                window.location.href = deepLinkUrl;
                // Fallback to store after 1 second
                setTimeout(function() {
                    window.location.href = fallbackUrl;
                }, 1000);
            } else if (platform === 'ios' && iosDeepLink) {
                // Try universal link first
                window.location.href = iosDeepLink;
                // Fallback to store after 1 second
                setTimeout(function() {
                    window.location.href = fallbackUrl;
                }, 1000);
            } else {
                // Direct to store
                window.location.href = fallbackUrl;
            }
        }

        window.onload = function() {
            // Auto-redirect for mobile devices
            if (isAndroid && deepLink) {
                setTimeout(function() {
                    trackClick(deepLink, androidStoreLink, 'android');
                }, 500);
            } else if (isIos && iosDeepLink) {
                setTimeout(function() {
                    trackClick(iosDeepLink, iosStoreLink, 'ios');
                }, 500);
            }
        };
    </script>
    @endif
</body>
</html>
