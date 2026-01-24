@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm text-gray-500 mb-1']) }}>
    {{ $value ?? $slot }}
</label>
