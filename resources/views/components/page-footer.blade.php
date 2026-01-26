<footer class="py-8 px-6 border-t border-gray-200">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Copyright {{ date('Y') }} kimiyu. All rights reserved.
            </div>
            <div class="flex items-center gap-6 text-sm text-gray-500">
                <a href="{{ route('privacy') }}" class="hover:text-gray-900 transition-colors">Privacy</a>
                <a href="{{ route('login') }}" class="hover:text-gray-900 transition-colors">Log in</a>
                <a href="{{ route('register') }}" class="hover:text-gray-900 transition-colors">Register</a>
            </div>
        </div>
    </div>
</footer>

<div class="pt-4 pb-4 px-6">
    <div class="text-center text-[0.625rem] text-gray-400 font-mono">
        Made with ❤️ by <a href="https://marceli.to" target="_blank" class="hover:text-gray-600 transition-colors">marceli.to</a>
    </div>
</div>
