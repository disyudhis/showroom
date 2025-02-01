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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-50 dark:bg-gray-900">
        <div class="min-h-screen flex">
            <!-- Left Side - Illustration/Branding -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 to-teal-500 dark:from-blue-800 dark:to-teal-700 items-center justify-center p-12">
                <div class="text-center text-white">
                    <h1 class="text-4xl font-bold mb-4">Karunia Auto Car</h1>
                    <p class="text-xl mb-8 opacity-90">Manage your automotive inventory with ease</p>
                    <div class="bg-white/20 dark:bg-black/20 rounded-xl p-8">
                        <i class="ri-car-line text-6xl mx-auto block mb-4 text-white dark:text-gray-200"></i>
                        <p class="text-sm text-white dark:text-gray-300">Streamline your showroom operations</p>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login/Register Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-white dark:bg-gray-800">
                <div class="w-full max-w-md">
                    <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-2xl rounded-2xl overflow-hidden">
                        <div class="p-8">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
