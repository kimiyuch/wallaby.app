<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>kimiyu – Split expenses, not relationships.</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
        <meta name="apple-mobile-web-app-title" content="kimiyu" />
        <link rel="manifest" href="/site.webmanifest" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased text-sm font-sans tracking-wide bg-gray-100">
        <div>
            <!-- Desktop Sidebar -->
            <aside class="fixed left-0 top-0 min-h-screen w-64 bg-gray-100 hidden lg:flex flex-col z-50">
                <!-- Logo -->
                <div class="py-4 px-6">
                    <a href="{{ route('expenses') }}" class="text-2xl font-bold text-gray-900">
                        kimiyu
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 py-4 px-6 space-y-1">
                    <a href="{{ route('expenses') }}"
                       class="flex items-center gap-3 py-4 text-sm font-regular transition-all duration-200 {{ request()->routeIs('expenses') ? 'text-black' : 'text-gray-500 hover:text-black' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                        </svg>
                        Expenses
                    </a>
                    <a href="{{ route('profile') }}" 
                       class="flex items-center gap-3 py-4 text-sm font-regular transition-all duration-200 {{ request()->routeIs('profile') ? 'text-black' : 'text-gray-500 hover:text-black' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile
                    </a>
                </nav>

                <!-- Bottom Section -->
                <div class="px-6 pb-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 py-4 text-xs text-gray-500 font-regular cursor-pointer hover:text-black transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Mobile Header -->
            <header class="lg:hidden bg-gray-100 px-4 py-3 flex items-center justify-between">
                <a href="{{ route('expenses') }}" class="text-xl font-bold text-gray-900">kimiyu</a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="p-2 text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-gray-100 rounded-lg shadow-lg py-2 z-50">
                        <a href="{{ route('expenses') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Expenses</a>
                        <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="lg:ml-64 bg-white lg:m-4 lg:rounded-2xl lg:min-h-[calc(100vh-2rem)]">
                <div class="p-4 lg:p-6">
                    {{ $slot }}
                </div>
            </main>

            <!-- Toast Notifications -->
            <x-toast />
            
            <!-- Confirm Modal -->
            <x-confirm-modal />
        </div>
    </body>
</html>
