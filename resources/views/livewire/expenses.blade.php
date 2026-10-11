<div>
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-2">
            @if(!$this->isSettled)
                <button wire:click="openExpenseForm"
                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-lilac-50 cursor-pointer rounded-sm transition-colors"
                        title="Add Expense">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            @endif
            <h1 class="text-xl text-lilac-950 font-display font-semibold">Expenses</h1>
            @if($this->isSettled)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-mint-100 text-mint-700">Settled</span>
            @endif
        </div>
        <!-- Month Navigation -->
        <div class="flex items-center gap-1">
            <button wire:click="previousMonth" wire:loading.attr="disabled" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-lilac-50 rounded-sm transition-colors disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <span class="text-sm text-gray-500 min-w-[120px] text-center" wire:loading.class="opacity-50">{{ $monthName }} {{ $selectedYear }}</span>
            <button wire:click="nextMonth" wire:loading.attr="disabled" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-lilac-50 rounded-sm transition-colors disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-8">
        <div class="border border-gray-200 bg-gray-50/50 rounded-md p-4">
            <div class="flex items-center gap-2 mb-1">
                <p class="text-sm text-gray-500">{{ Auth::user()->name }}</p>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <p class="text-xl text-lilac-950 font-semibold">{{ number_format($myTotal, 2) }}</p>
            <p class="text-xs text-gray-400">This month</p>
        </div>
        
        @if($this->partner)
        <div class="border border-gray-200 bg-gray-50/50 rounded-md p-4">
            <div class="flex items-center gap-2 mb-1">
                <p class="text-sm text-gray-500">{{ $this->partner->name }}</p>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <p class="text-xl text-lilac-950 font-semibold">{{ number_format($partnerTotal, 2) }}</p>
            <p class="text-xs text-gray-400">This month</p>
        </div>
        @endif
        
        <div class="border border-gray-200 bg-gray-50/50 rounded-md p-4">
            <div class="flex items-center gap-2 mb-1">
                <p class="text-sm text-gray-500">Total</p>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-xl text-lilac-950 font-semibold">{{ number_format($myTotal + $partnerTotal, 2) }}</p>
            <p class="text-xs text-gray-400">Combined expenses</p>
        </div>
        
        <div class="{{ $this->isSettled ? 'border-mint-300 bg-mint-50' : 'border-gray-200 bg-gray-50/50' }} border rounded-md p-4 relative">
            <div class="flex items-center gap-2 mb-1">
                <p class="text-sm {{ $this->isSettled ? 'text-mint-700' : 'text-gray-500' }}">Balance</p>
                @if($this->isSettled)
                    <svg class="w-4 h-4 text-mint-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                @endif
            </div>
            @if(!$this->isSettled && $this->partner && ($myTotal > 0 || $partnerTotal > 0))
                <button 
                    @click="$dispatch('confirm-modal', {
                        title: 'Settle this month?',
                        message: 'Both partners will be notified and no more expenses can be added.',
                        type: 'primary',
                        confirmText: 'Settle',
                        onConfirm: () => $wire.settleMonth()
                    })"
                    class="absolute top-2 right-2 w-6 h-6 flex items-center justify-center rounded-full border border-gray-300 text-gray-400 hover:border-mint-500 hover:text-mint-500 hover:bg-mint-50 transition-colors"
                    title="Settle month"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            @endif
            @if(isset($balance[Auth::id()]))
                @if($balance[Auth::id()] > 0)
                    <p class="text-xl {{ $this->isSettled ? 'text-mint-700' : 'text-red-600' }} font-semibold">-{{ number_format($balance[Auth::id()], 2) }}</p>
                    <p class="text-xs {{ $this->isSettled ? 'text-mint-700' : 'text-gray-400' }}">{{ $this->isSettled ? 'You paid' : 'You owe' }}</p>
                @elseif($balance[Auth::id()] < 0)
                    <p class="text-xl text-mint-700 font-semibold">+{{ number_format(abs($balance[Auth::id()]), 2) }}</p>
                    <p class="text-xs {{ $this->isSettled ? 'text-mint-700' : 'text-gray-400' }}">{{ $this->isSettled ? 'You received' : 'You get back' }}</p>
                @else
                    <p class="text-xl {{ $this->isSettled ? 'text-mint-700' : 'text-lilac-950' }} font-semibold">0.00</p>
                    <p class="text-xs {{ $this->isSettled ? 'text-mint-700' : 'text-gray-400' }}">{{ $this->isSettled ? 'Settled' : 'Even' }}</p>
                @endif
            @else
                <p class="text-xl text-gray-400 font-semibold">—</p>
            @endif
        </div>
    </div>

    <!-- Split View -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Left Column: Current User's Expenses -->
        <div>
            <h2 class="text-sm text-lilac-950 font-display font-semibold pb-2 border-b border-gray-200">{{ Auth::user()->name }}</h2>
            @if($myExpenses->isEmpty())
                <p class="text-sm text-gray-400 py-4 text-center">No expenses this month</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($myExpenses as $expense)
                        <li class="relative flex items-center justify-between py-3 transition-colors group">
                            <div class="flex items-center gap-2 lg:gap-3 min-w-0 flex-1">
                                <span class="text-sm text-lilac-950 truncate">{{ $expense->description }}</span>
                                @if($expense->is_recurring)
                                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-xs bg-lilac-100 text-lilac-700">Recurring</span>
                                @endif
                                @if($expense->is_personal)
                                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-xs bg-gold-100 text-gold-800">Paid for partner</span>
                                @endif
                            </div>
                            <span class="text-sm text-lilac-950 tabular-nums">{{ number_format($expense->amount, 2) }}</span>
                            <!-- Hover overlay -->
                            @if(!$this->isSettled)
                            <div class="absolute inset-y-0 right-0 flex items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="w-12 h-full bg-gradient-to-r from-transparent to-white"></div>
                                <div class="flex items-center bg-white pr-1">
                                    <button wire:click="editExpense({{ $expense->id }})" 
                                            class="p-2 text-gray-400 hover:text-gray-600 rounded-sm transition-colors"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <button @click="$dispatch('confirm-modal', {
                                                title: 'Delete expense?',
                                                message: 'This action cannot be undone.',
                                                type: 'danger',
                                                confirmText: 'Delete',
                                                onConfirm: () => $wire.deleteExpense({{ $expense->id }})
                                            })"
                                            class="p-2 text-gray-400 hover:text-red-600 rounded-sm transition-colors"
                                            title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Right Column: Partner's Expenses -->
        <div>
            @if($this->partner)
                <h2 class="text-sm text-lilac-950 font-display font-semibold pb-2 border-b border-gray-200">{{ $this->partner->name }}</h2>
                @if($partnerExpenses->isEmpty())
                    <p class="text-sm text-gray-400 py-4 text-center">No expenses this month</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($partnerExpenses as $expense)
                            <li class="flex items-center justify-between py-3 hover:bg-gray-50/50 transition-colors">
                                <div class="flex items-center gap-2 lg:gap-3 min-w-0 flex-1">
                                    <span class="text-sm text-lilac-950 truncate">{{ $expense->description }}</span>
                                    @if($expense->is_recurring)
                                        <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-xs bg-lilac-100 text-lilac-700">Recurring</span>
                                    @endif
                                    @if($expense->is_personal)
                                        <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-xs bg-gold-100 text-gold-800">Paid for partner</span>
                                    @endif
                                </div>
                                <span class="text-sm text-lilac-950 tabular-nums">{{ number_format($expense->amount, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif
        </div>
    </div>

    <!-- Expense Form Flyout -->
    <div x-data="{ open: @entangle('showExpenseForm') }"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-hidden"
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
             class="fixed inset-0 bg-lilac-950/20 transition-opacity"
             @click="open = false">
        </div>

        <!-- Panel -->
        <div class="fixed inset-0 lg:inset-y-0 lg:left-auto lg:right-0 flex lg:max-w-full lg:pl-10">
            <div x-show="open"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-y-full lg:translate-y-0 lg:translate-x-full"
                 x-transition:enter-end="translate-y-0 lg:translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-y-0 lg:translate-x-0"
                 x-transition:leave-end="translate-y-full lg:translate-y-0 lg:translate-x-full"
                 class="relative bg-white w-full shadow-xl flex flex-col h-full lg:rounded-xl lg:my-4 lg:mr-4 lg:h-[calc(100%-2rem)] overflow-y-auto lg:min-w-[25rem] lg:max-w-4xl">
                
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-display font-semibold text-lilac-950">New Expense</h2>
                    <button @click="open = false" 
                            class="-mr-2 p-2 text-gray-400 hover:text-gray-600 hover:bg-lilac-50 rounded-sm transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <livewire:expense-form :selectedMonth="$selectedMonth" :selectedYear="$selectedYear" />
                </div>
            </div>
        </div>
    </div>
</div>
