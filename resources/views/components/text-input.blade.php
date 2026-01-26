@props(['disabled' => false, 'error' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg text-sm focus:ring-0 placeholder-gray-300 ' . ($error ? 'border-red-300 focus:border-red-400' : 'border-gray-200 focus:border-gray-300')]) }}>
