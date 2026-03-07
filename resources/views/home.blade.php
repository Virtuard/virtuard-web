<!DOCTYPE html>
<html lang="{{ str_replace(\'_\', \'-\', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Virtuard - Find Your Perfect Fit</title>
    <!-- Tailwind CSS (CDN for rapid prototype) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        virtuard: {
                            green: '#105445', /* Exact deep green from Figma */
                            dark: '#181a1b', /* Dark navbar color */
                            lightBg: '#F7F9FA',
                            accent: '#1E9E81' /* Lighter green for buttons/hovers */
                        }
                    },
                    fontFamily: {
                        sans: [\'"Plus Jakarta Sans"\', \'Inter\', \'sans-serif\'],
                        serif: [\'"Playfair Display"\', \'serif\']
                    },
                    boxShadow: {
                        \'search\': \'0 10px 40px -10px rgba(0,0,0,0.1)\',
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts matching the design -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for temporary icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: \'Plus Jakarta Sans\', sans-serif; }
        .hero-title { font-family: \'Playfair Display\', serif; }
        
        /* Custom styling for the category buttons to match Figma\'s distinct look */
        .category-btn {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .category-btn:hover, .category-btn.active {
            background: rgba(255, 255, 255, 1);
            color: #105445;
        }
        .category-btn.active .icon-container {
            background: #105445;
            color: white;
        }
    </style>
</head>
<body class="bg-virtuard-lightBg text-gray-800 antialiased overflow-x-hidden">

    <!-- Navbar (Dark Theme from Figma) -->
    <header class="bg-virtuard-dark text-white py-4 px-6 md:px-10 flex items-center justify-between sticky top-0 z-50">
        <!-- Logo -->
        <div class="flex items-center gap-2 cursor-pointer">
            <span class="font-bold text-2xl tracking-wider text-white">Virtuard<span class="text-virtuard-accent">.</span></span>
        </div>
        
        <!-- Center Navigation -->
        <nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-gray-300">
            <a href="/" class="text-white relative after:content-[\'\'] after:absolute after:-bottom-2 after:left-0 after:w-full after:h-0.5 after:bg-white">Home</a>
            <a href="#explore" class="hover:text-white transition-colors">Explore</a>
            <a href="#about" class="hover:text-white transition-colors">About Us</a>
            <!-- Special Link -->
            <a href="#expert" class="hover:text-white transition-colors flex items-center gap-1">
                Become a Expert
            </a>
        </nav>
        
        <!-- Right Actions (Currency, Lang, Profile) -->
        <div class="hidden md:flex items-center gap-5 text-sm font-medium text-gray-300">
            <div class="flex items-center gap-4 border-r border-gray-700 pr-5">
                <button class="hover:text-white flex items-center gap-1">
                    EUR <i class="fa-solid fa-chevron-down text-xs ml-1"></i>
                </button>
                <button class="hover:text-white flex items-center gap-1">
                    ENG <i class="fa-solid fa-chevron-down text-xs ml-1"></i>
                </button>
            </div>
            
            <button class="flex items-center gap-2 hover:text-white transition-colors">
                <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center overflow-hidden border border-gray-500">
                    <i class="fa-solid fa-user text-xs"></i>
                </div>
                <span>Sign In</span>
            </button>
        </div>
        
        <!-- Mobile Menu Button -->
        <button class="lg:hidden text-gray-300 hover:text-white">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section (Deep Green) -->
        <section class="bg-virtuard-green text-white relative pt-16 pb-48 px-6 md:px-10 overflow-visible">
            <!-- Background Decorative Elements (Subtle curves/blobs if needed, simulating Figma gradient) -->
            <div class="absolute top-0 right-0 w-1/2 h-full opacity-20 pointer-events-none" style="background: radial-gradient(circle at top right, rgba(255,255,255,0.4) 0%, transparent 60%);"></div>
            
            <div class="max-w-6xl mx-auto relative z-10 flex flex-col items-center">
                
                <!-- Headline -->
                <h1 class="hero-title text-5xl md:text-6xl lg:text-7xl font-bold mb-6 text-center leading-tight max-w-4xl">
                    Find Your <span class="text-emerald-300 italic">Perfect Fit</span>
                </h1>
                
                <!-- Subtitle -->
                <p class="text-lg md:text-xl text-emerald-50/80 mb-12 text-center max-w-2xl font-light">
                    Look for virtual and real property tours, discover exclusive experiences, and explore the world from wherever you are.
                </p>
                
                <!-- Primary Search Pill -->
                <div class="w-full max-w-4xl bg-white rounded-full p-2 flex flex-col md:flex-row items-center justify-between shadow-2xl text-gray-800 z-20 relative transform translate-y-6">
                    
                    <!-- Location -->
                    <div class="flex-1 px-6 py-3 w-full border-b md:border-b-0 md:border-r border-gray-200 cursor-text group">
                        <label class="block text-[11px] font-bold text-gray-800 uppercase tracking-widest mb-1 group-hover:text-virtuard-green transition-colors">Location</label>
                        <input type="text" placeholder="Where are you going?" class="w-full bg-transparent outline-none text-sm text-gray-500 font-medium placeholder-gray-400">
                    </div>
                    
                    <!-- Date -->
                    <div class="flex-1 px-6 py-3 w-full border-b md:border-b-0 md:border-r border-gray-200 cursor-text group">
                        <label class="block text-[11px] font-bold text-gray-800 uppercase tracking-widest mb-1 group-hover:text-virtuard-green transition-colors">Date</label>
                        <input type="text" placeholder="Add dates" class="w-full bg-transparent outline-none text-sm text-gray-500 font-medium placeholder-gray-400">
                    </div>
                    
                    <!-- Guests -->
                    <div class="flex-1 px-6 py-3 w-full cursor-text group">
                        <label class="block text-[11px] font-bold text-gray-800 uppercase tracking-widest mb-1 group-hover:text-virtuard-green transition-colors">Guests</label>
                        <input type="text" placeholder="Add guests" class="w-full bg-transparent outline-none text-sm text-gray-500 font-medium placeholder-gray-400">
                    </div>
                    
                    <!-- Search Button -->
                    <button class="bg-virtuard-green text-white rounded-full h-14 w-full md:w-32 flex items-center justify-center font-bold hover:bg-emerald-800 transition-colors shadow-md flex-shrink-0 group">
                        <i class="fa-solid fa-search mr-2 group-hover:scale-110 transition-transform"></i> Search
                    </button>
                </div>
                
                <!-- Tour Category Toggles (Floating overlapping the bottom edge of green section) -->
                <div class="flex flex-wrap justify-center gap-4 mt-20 md:mt-16 w-full max-w-3xl absolute -bottom-8">
                    
                    <!-- Virtual Tour (Active State) -->
                    <button class="category-btn active flex items-center gap-3 px-6 py-4 rounded-xl font-semibold shadow-lg min-w-[200px]">
                        <div class="icon-container w-10 h-10 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-vr-cardboard"></i>
                        </div>
                        <span class="text-left">
                            <span class="block text-sm">Virtual</span>
                            <span class="block text-xs font-normal opacity-70">Experience in 3D</span>
                        </span>
                    </button>
                    
                    <!-- Real Tour -->
                    <button class="category-btn text-white flex items-center gap-3 px-6 py-4 rounded-xl font-semibold shadow-lg min-w-[200px]">
                        <div class="icon-container w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <span class="text-left">
                            <span class="block text-sm">Real</span>
                            <span class="block text-xs font-normal opacity-70">Physical Visit</span>
                        </span>
                    </button>
                    
                    <!-- Mixed Tour -->
                    <button class="category-btn text-white flex items-center gap-3 px-6 py-4 rounded-xl font-semibold shadow-lg min-w-[200px]">
                        <div class="icon-container w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <span class="text-left">
                            <span class="block text-sm">Mixed</span>
                            <span class="block text-xs font-normal opacity-70">Both Options</span>
                        </span>
                    </button>
                </div>

            </div>
        </section>

        <!-- Spacer for the overlapping floating elements -->
        <div class="h-16 md:h-20 w-full bg-white"></div>

        <!-- Exploring Section (Preview of what\'s below the fold) -->
        <section class="bg-white py-20 px-6 md:px-10">
            <div class="max-w-6xl mx-auto">
                <div class="flex justify-between items-end mb-10">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2 font-serif">Explore Top Destinations</h2>
                        <p class="text-gray-500">Discover incredible virtual tours from around the world.</p>
                    </div>
                    <a href="#" class="hidden md:flex items-center gap-2 text-virtuard-green font-semibold hover:underline">
                        See All <i class="fa-solid fa-arrow-right text-sm"></i>
                    </a>
                </div>
                
                <!-- Mockup Grid based on Figma style -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Placeholder Card 1 -->
                    <div class="group cursor-pointer">
                        <div class="relative w-full h-64 rounded-2xl overflow-hidden mb-4">
                            <!-- Temporary placeholder image -->
                            <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Villa" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <!-- Virtual Badge -->
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-virtuard-green flex items-center gap-1">
                                <i class="fa-solid fa-vr-cardboard"></i> Virtual Tour
                            </div>
                            <!-- Heart Icon -->
                            <button class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/50 backdrop-blur-md flex items-center justify-center text-white hover:text-red-500 hover:bg-white transition-colors">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg group-hover:text-virtuard-green transition-colors">Modern Luxury Villa</h3>
                                <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                    <i class="fa-solid fa-location-dot text-gray-400"></i> Bali, Indonesia
                                </p>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-star text-yellow-500 text-sm"></i>
                                <span class="font-bold text-sm">4.9</span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">Experience this stunning oceanfront villa in immersive 3D.</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="font-bold text-gray-900">€ 240</span><span class="text-sm text-gray-500">/night</span>
                        </div>
                    </div>
                    
                    <!-- Placeholder Card 2 -->
                    <div class="group cursor-pointer">
                        <div class="relative w-full h-64 rounded-2xl overflow-hidden mb-4">
                            <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Apartment" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-blue-600 flex items-center gap-1">
                                <i class="fa-solid fa-map-location-dot"></i> Real Tour
                            </div>
                        </div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg group-hover:text-virtuard-green transition-colors">City Center Loft</h3>
                                <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                    <i class="fa-solid fa-location-dot text-gray-400"></i> Milan, Italy
                                </p>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-star text-yellow-500 text-sm"></i>
                                <span class="font-bold text-sm">4.7</span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">Spacious loft right in the heart of the fashion district.</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="font-bold text-gray-900">€ 120</span><span class="text-sm text-gray-500">/night</span>
                        </div>
                    </div>
                    
                     <!-- Placeholder Card 3 -->
                    <div class="group cursor-pointer">
                        <div class="relative w-full h-64 rounded-2xl overflow-hidden mb-4">
                            <img src="https://images.unsplash.com/photo-1493809842364-78817add7ffb?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Apartment" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-virtuard-green flex items-center gap-1">
                                <i class="fa-solid fa-vr-cardboard"></i> Virtual Tour
                            </div>
                        </div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg group-hover:text-virtuard-green transition-colors">Cozy Alpine Cabin</h3>
                                <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                    <i class="fa-solid fa-location-dot text-gray-400"></i> Zermatt, Swiss
                                </p>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-star text-yellow-500 text-sm"></i>
                                <span class="font-bold text-sm">5.0</span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">A warm wooden cabin with a view of the Matterhorn.</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="font-bold text-gray-900">€ 350</span><span class="text-sm text-gray-500">/night</span>
                        </div>
                    </div>
                    
                     <!-- Placeholder Card 4 -->
                    <div class="group cursor-pointer">
                        <div class="relative w-full h-64 rounded-2xl overflow-hidden mb-4">
                            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Apartment" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-purple-600 flex items-center gap-1">
                                <i class="fa-solid fa-layer-group"></i> Mixed Tour
                            </div>
                        </div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg group-hover:text-virtuard-green transition-colors">Downtown Penthouse</h3>
                                <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                    <i class="fa-solid fa-location-dot text-gray-400"></i> New York, USA
                                </p>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-star text-yellow-500 text-sm"></i>
                                <span class="font-bold text-sm">4.8</span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">Luxury living with 360 panoramic views of Manhattan.</p>
                        <div class="mt-3 flex items-center gap-1">
                            <span class="font-bold text-gray-900">€ 800</span><span class="text-sm text-gray-500">/night</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <!-- Simple Footer -->
    <footer class="bg-virtuard-dark text-white py-12 px-6">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="font-bold text-2xl tracking-wider text-white">Virtuard<span class="text-virtuard-accent">.</span></span>
            </div>
            <div class="flex gap-6 text-sm text-gray-400">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                <a href="#" class="hover:text-white transition-colors">Contact</a>
            </div>
            <p class="text-sm text-gray-500">© 2026 Virtuard Reality Design. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
