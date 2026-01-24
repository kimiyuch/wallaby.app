<button {{ $attributes->merge(['type' => 'button', 'class' => 'text-sm inline-flex items-center justify-center gap-2 rounded-md cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-500 px-4 py-2']) }}>
    {{ $slot }}
</button>
