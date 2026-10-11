@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-lilac-400 text-start text-base font-medium text-lilac-700 bg-lilac-50 focus:outline-none focus:text-lilac-800 focus:bg-lilac-100 focus:border-lilac-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-lilac-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-lilac-50 focus:border-lilac-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
