<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Virtuard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        virtuard: {
                            green: '#105445',
                            dark: '#181a1b',
                            lightBg: '#F7F9FA',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-white min-h-screen flex text-gray-900 font-sans">

    <!-- Left Column (Form) -->
    <div class="w-full lg:w-1/2 flex flex-col px-8 sm:px-16 md:px-24 py-10 justify-center">
        <div class="max-w-md w-full mx-auto flex flex-col flex-grow justify-center">

            <!-- Back to Home -->
            <a href="/"
                class="text-sm font-medium text-gray-500 hover:text-gray-900 mb-8 inline-flex items-center gap-2 group">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Login Page |
                Virtuard Main Web
            </a>

            <!-- Header -->
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-6">
                    <!-- Placeholder Logo icon, replacing exact Figma SVG for now -->
                    <div
                        class="w-8 h-8 bg-virtuard-dark rounded-md flex items-center justify-center text-white font-bold text-xs">
                        V.</div>
                </div>
                <h1 class="text-3xl font-bold mb-2">Welcome Back! ✨</h1>
                <p class="text-gray-500 text-sm">Please login to access your account.</p>
            </div>

            <!-- Validation Errors (Hidden by default, shown via JS/Backend) -->
            <!--
            <div class="bg-red-50 text-red-600 text-sm px-4 py-3 rounded-xl mb-6 flex items-start gap-3 border border-red-100">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <ul class="list-disc list-inside">
                    <li>We could not verify your email address.</li>
                    <li>Password does not match.</li>
                </ul>
            </div>
            -->

            <!-- Form -->
            <form action="#" method="POST" class="space-y-5">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold mb-2">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="example@gmail.com"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-virtuard-green focus:border-transparent transition-all"
                        required>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Enter your password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-virtuard-green focus:border-transparent transition-all"
                            required>
                        <button type="button"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <!-- Forgot Password Link -->
                <div class="flex justify-end">
                    <a href="/password/reset" class="text-sm font-semibold text-virtuard-green hover:underline">Forgot
                        password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="w-full bg-virtuard-dark hover:bg-black text-white py-3.5 rounded-xl font-bold transition-colors mt-2 shadow-lg shadow-gray-200">
                    Login
                </button>
            </form>

            <!-- Divider -->
            <div class="relative flex items-center py-8">
                <div class="flex-grow border-t border-gray-200"></div>
                <span class="flex-shrink-0 mx-4 text-gray-400 text-sm">Or continue with</span>
                <div class="flex-grow border-t border-gray-200"></div>
            </div>

            <!-- Social Logins -->
            <div class="grid grid-cols-2 gap-4">
                <button type="button"
                    class="flex items-center justify-center gap-2 w-full py-3 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors font-semibold text-sm">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google"> Google
                </button>
                <button type="button"
                    class="flex items-center justify-center gap-2 w-full py-3 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors font-semibold text-sm">
                    <i class="fa-brands fa-apple text-xl mb-0.5"></i> Apple
                </button>
            </div>

            <!-- Sign Up Link -->
            <p class="text-center text-sm font-medium mt-10">
                Don't have an account? <a href="/figma/register"
                    class="text-virtuard-green font-bold hover:underline">Sign Up</a>
            </p>
        </div>
    </div>

    <!-- Right Column (Image from Figma showing the abstract fluid dark glass) -->
    <div class="hidden lg:block lg:w-1/2 relative bg-neutral-900 overflow-hidden">
        <!-- Connecting to an Unsplash image that closely resembles the dark fluid metallic texture in the design -->
        <img src="https://images.unsplash.com/photo-1550684848-fac1c5b4e853?q=80&w=2070&auto=format&fit=crop"
            alt="Abstract Dark Fluid" class="absolute inset-0 w-full h-full object-cover opacity-80 mix-blend-overlay">

        <!-- Gradient overlay to match the branding -->
        <div class="absolute inset-0 bg-gradient-to-tr from-black/80 via-black/40 to-transparent"></div>

        <!-- Bottom left text overlay seen on the mockups -->
        <div class="absolute bottom-12 left-12 right-12 text-white">
            <h2 class="text-2xl font-serif font-bold mb-3">Virtuard Experiences.</h2>
            <p class="text-gray-300 text-sm max-w-md font-light">Explore incredible real estate properties, virtual
                tours, and unique accommodations around the world from a single platform.</p>
        </div>
    </div>

</body>

</html>