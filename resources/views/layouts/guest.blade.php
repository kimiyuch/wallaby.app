<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Kimiyu') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased text-sm font-sans tracking-wide bg-gray-100">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-sm">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <a href="/" class="text-3xl font-bold text-gray-900">
                        Kimiyu
                    </a>
                </div>

                <!-- Card -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
