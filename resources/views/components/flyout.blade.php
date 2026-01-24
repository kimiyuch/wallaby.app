@props(['show' => false, 'title' => ''])

<div x-data="{ open: @entangle($attributes->wire('model')) }"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-hidden"
     aria-labelledby="slide-over-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/20 transition-opacity"
         @click="open = false">
    </div>

    <!-- Panel -->
    <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
        <div x-show="open"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-md">
            
            <div class="flex h-full flex-col bg-white shadow-xl">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900" id="slide-over-title">
                        {{ $title }}
                    </h2>
                    <button @click="open = false" 
                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-sm transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
