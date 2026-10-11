<header class="mx-auto flex max-w-6xl items-center justify-between gap-x-6 px-6 py-5 lg:px-8">
    <a href="/" aria-label="Homepage" class="flex items-center gap-x-2 font-display text-xl font-semibold text-lilac-950">
        <img src="/favicon.svg" alt="" class="-ml-1 size-9">
        Wallaby
    </a>
    <nav class="flex items-center gap-x-8">
        <a href="/#how-it-works" class="text-sm/6 font-medium text-gray-700 hover:text-lilac-700 max-md:hidden">How it works</a>
        <a href="/#features" class="text-sm/6 font-medium text-gray-700 hover:text-lilac-700 max-md:hidden">Features</a>
        <a href="/#faq" class="text-sm/6 font-medium text-gray-700 hover:text-lilac-700 max-md:hidden">Questions</a>
        @auth
            <a href="{{ route('expenses') }}" class="rounded-full px-3 py-1.5 text-sm/6 font-medium text-lilac-800 ring-1 ring-lilac-950/10 hover:bg-lilac-50">Open Wallaby</a>
        @else
            <a href="{{ route('login') }}" class="rounded-full px-3 py-1.5 text-sm/6 font-medium text-lilac-800 ring-1 ring-lilac-950/10 hover:bg-lilac-50">Log in</a>
        @endauth
    </nav>
</header>
