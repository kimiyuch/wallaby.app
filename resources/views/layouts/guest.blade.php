<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Wallaby – Split expenses, not relationships.</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
        <meta name="apple-mobile-web-app-title" content="Wallaby" />
        <link rel="manifest" href="/site.webmanifest" />

        @fonts

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased text-sm font-sans tracking-wide bg-lilac-50">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-sm">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <a href="/" class="inline-flex flex-col items-center gap-3 text-3xl font-display font-semibold text-lilac-950">
                        <img src="/mascot.svg" alt="" class="w-24 h-auto">
                        Wallaby
                    </a>
                </div>

                <!-- Card -->
                <div class="bg-white rounded-2xl p-6 shadow-xs">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
