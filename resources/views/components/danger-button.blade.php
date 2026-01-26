@props(['as' => 'button', 'size' => 'md'])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed bg-red-600 text-white hover:bg-red-500';

$sizeClasses = match($size) {
    'sm' => 'text-xs px-3 py-1.5',
    'md' => 'text-sm px-4 py-2',
    'lg' => 'text-sm px-6 py-3',
    default => 'text-sm px-4 py-2',
};

$classes = $baseClasses . ' ' . $sizeClasses;
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
