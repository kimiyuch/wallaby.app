@props(['as' => 'button', 'size' => 'md'])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg cursor-pointer transition-colors focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed text-gray-500 hover:text-lilac-950 underline';

$sizeClasses = match($size) {
    'sm' => 'text-xs',
    'md' => 'text-sm',
    'lg' => 'text-sm',
    default => 'text-sm',
};

$classes = $baseClasses . ' ' . $sizeClasses;
@endphp

@if($as === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
