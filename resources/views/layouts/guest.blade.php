<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/Logo tanpa kotak.png') }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    {{-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-[Poppins] text-gray-900 dark:text-gray-100 antialiased bg-gray-50 dark:bg-black">
    <div class="min-h-screen flex">
        <!-- Left Side - Interactive Branding -->
        <div
            class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-red-700 to-red-900 dark:from-red-950 dark:to-black items-center justify-center p-12 relative overflow-hidden">
            <!-- Pattern Background -->
            <div class="absolute inset-0 opacity-10">
                <div
                    class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]">
                </div>
            </div>

            <!-- Main Content Container -->
            <div class="text-center text-white relative z-10">
                <!-- Logo Container -->
                <div class="mx-auto relative w-40 h-40 mb-8 group">
                    <!-- Outer Ring with Animation -->
                    <div class="absolute inset-0 bg-red-600/30 rounded-full animate-ping opacity-75"></div>

                    <!-- Logo Circle -->
                    <div
                        class="w-full h-full rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center relative animate-bounce transition-all duration-1000">
                        <img src="{{ asset('img/Logo tanpa kotak.png') }}" alt="Logo"
                            class="w-24 h-24 object-contain transition-all duration-300 transform group-hover:scale-110" />
                    </div>
                </div>

                <!-- Text Content -->
                <h1 class="font-[Montserrat] text-4xl font-black mb-4 tracking-tight">
                    Karunia Auto Car
                </h1>
                <p class="text-xl mb-8 opacity-90">Manage your automotive inventory with ease</p>

                <!-- Feature Cards -->
                <div class="grid grid-cols-2 gap-4 max-w-lg mx-auto">
                    <!-- Card 1 -->
                    <div
                        class="group bg-black/30 backdrop-blur-sm rounded-xl p-6 transition-all duration-300 hover:scale-105 hover:bg-black/40 cursor-pointer">
                        <i
                            class="ri-car-line text-3xl mb-3 transition-transform duration-300 group-hover:scale-110"></i>
                        <p class="text-sm font-medium group-hover:text-white/90">Vehicle Management</p>
                    </div>

                    <!-- Card 2 -->
                    <div
                        class="group bg-black/30 backdrop-blur-sm rounded-xl p-6 transition-all duration-300 hover:scale-105 hover:bg-black/40 cursor-pointer">
                        <i
                            class="ri-settings-line text-3xl mb-3 transition-transform duration-300 group-hover:scale-110"></i>
                        <p class="text-sm font-medium group-hover:text-white/90">Easy Configuration</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login/Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-white dark:bg-black">
            <div class="w-full max-w-md">
                <div
                    class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-red-900/20 shadow-2xl rounded-2xl overflow-hidden backdrop-blur-sm">
                    <div class="p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
