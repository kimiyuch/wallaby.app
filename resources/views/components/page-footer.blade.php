<footer class="border-t border-lilac-950/5">
    <div class="mx-auto flex max-w-6xl flex-col gap-6 px-6 py-10 sm:flex-row sm:items-center sm:justify-between lg:px-8">
        <div class="flex items-center gap-x-3">
            <img src="/favicon.svg" alt="" class="size-7">
            <p class="text-sm/6 text-gray-600">Wallaby, made with <span aria-label="love">❤️</span> by <a href="https://kimiyu.ch" class="font-medium text-lilac-800 hover:text-lilac-600">kimiyu.ch</a></p>
        </div>
        <nav class="flex flex-wrap gap-x-6 gap-y-2 whitespace-nowrap">
            <a href="{{ route('privacy') }}" class="text-sm/6 font-normal text-gray-600 hover:text-lilac-700">Privacy</a>
            <a href="{{ route('login') }}" class="text-sm/6 font-normal text-gray-600 hover:text-lilac-700">Log in</a>
            <a href="{{ route('register') }}" class="text-sm/6 font-normal text-gray-600 hover:text-lilac-700">Register</a>
        </nav>
    </div>
</footer>
