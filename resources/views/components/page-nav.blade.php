<nav class="sticky top-0 z-50">
    <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="/" class="text-xl font-bold">kimiyu</a>
        <div class="flex items-center gap-4">
            @auth
                <x-primary-button as="a" href="{{ route('expenses') }}" size="sm">Dashboard</x-primary-button>
            @else
                <x-tertiary-button as="a" href="{{ route('login') }}" class="no-underline hover:no-underline text-gray-600 hover:text-gray-900">Log in</x-tertiary-button>
                <x-primary-button as="a" href="{{ route('register') }}" size="sm">Get Started</x-primary-button>
            @endauth
        </div>
    </div>
</nav>
