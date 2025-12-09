<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        
        <!-- jQuery Utilities -->
        <script src="{{ asset('js/jquery-utils.js') }}"></script>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-600 via-cyan-600 to-teal-600">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"1\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>

            <div class="relative w-full sm:max-w-md mt-6 px-6">
                <!-- Logo Section -->
                <div class="text-center mb-8">
                    <a href="/" class="inline-block">
                        <div class="w-20 h-20 bg-white rounded-2xl shadow-2xl flex items-center justify-center mx-auto mb-4 transform hover:scale-110 transition-transform">
                            <span class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">LMS</span>
                        </div>
                    </a>
                    <h1 class="text-3xl font-bold text-white mb-2">Selamat Datang</h1>
                    <p class="text-white/90">Platform belajar Flutter & Web Development terbaik</p>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <div class="p-8">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Footer Links -->
                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="text-white/90 hover:text-white text-sm font-medium transition-colors">
                        ← Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
