<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Virtuard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
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

            <a href="/figma/login"
                class="text-sm font-medium text-gray-500 hover:text-gray-900 mb-8 inline-flex items-center gap-2 group">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Forgot Password |
                Virtuard Main Web
            </a>

            <!-- Header -->
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-6">
                    <div
                        class="w-8 h-8 bg-virtuard-dark rounded-md flex items-center justify-center text-white font-bold text-xs">
                        V.</div>
                </div>
                <h1 class="text-3xl font-bold mb-2">Forgot Password? 🔒</h1>
                <p class="text-gray-500 text-sm">No worries, we'll send you reset instructions.</p>
            </div>

            <!-- Form -->
            <form action="#" method="POST" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold mb-2">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="example@gmail.com"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-virtuard-green focus:border-transparent transition-all"
                        required>
                </div>

                <!-- Reset Button -->
                <button type="submit"
                    class="w-full bg-virtuard-dark hover:bg-black text-white py-3.5 rounded-xl font-bold transition-colors shadow-lg shadow-gray-200">
                    Reset Password
                </button>
            </form>

            <p class="text-center text-sm font-medium mt-10">
                Remember your password? <a href="/figma/login"
                    class="text-virtuard-green font-bold hover:underline">Back to login</a>
            </p>
        </div>
    </div>

    <!-- Right Column (Image) -->
    <div class="hidden lg:block lg:w-1/2 relative bg-neutral-900 overflow-hidden">
        <img src="https://images.unsplash.com/photo-1550684848-fac1c5b4e853?q=80&w=2070&auto=format&fit=crop"
            alt="Abstract Dark Fluid" class="absolute inset-0 w-full h-full object-cover opacity-80 mix-blend-overlay">

        <div class="absolute inset-0 bg-gradient-to-tr from-black/80 via-black/40 to-transparent"></div>
    </div>

</body>

</html>