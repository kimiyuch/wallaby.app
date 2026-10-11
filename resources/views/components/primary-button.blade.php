@props(['as' => 'button', 'size' => 'md', 'variant' => 'dark'])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg cursor-pointer transition-colors focus:outline-hidden focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variantClasses = match($variant) {
    'light' => 'bg-white text-lilac-950 hover:bg-lilac-50 focus:ring-white',
    default => 'bg-lilac-600 text-white hover:bg-lilac-700 focus:ring-lilac-500',
};

$sizeClasses = match($size) {
    'sm' => 'text-xs px-3 py-1.5',
    'md' => 'text-sm px-4 py-2',
    'lg' => 'text-sm px-6 py-3',
    default => 'text-sm px-4 py-2',
};

$classes = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses;
@endphp

@if($as === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
