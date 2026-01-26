@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-200 text-sm focus:border-gray-300 focus:ring-0 placeholder-gray-300']) }}>
