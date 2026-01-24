<button {{ $attributes->merge(['type' => 'submit', 'class' => 'text-sm inline-flex items-center justify-center gap-2 rounded-md cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-red-600 text-white hover:bg-red-500 focus:ring-red-500 px-4 py-2']) }}>
    {{ $slot }}
</button>
