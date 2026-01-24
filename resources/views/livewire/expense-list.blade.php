<div>
    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-50 text-green-800 rounded-md text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if($expenses->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <p>No expenses this month</p>
        </div>
    @else
        <div class="border-t border-gray-100">
            <ul class="divide-y divide-gray-100">
                @foreach($expenses as $expense)
                    <li class="flex items-center justify-between py-4 hover:bg-gray-50/50 transition-colors group">
                        <div class="flex items-center gap-x-6">
                            <span class="text-sm text-gray-400 w-12">{{ $expense->date->format('d.m') }}</span>
                            <span class="text-sm text-gray-900">{{ $expense->description }}</span>
                            <span class="text-sm font-medium text-gray-500">{{ $expense->user->name }}</span>
                            @if($expense->is_recurring)
                                <span class="text-xs text-blue-600">Recurring</span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $expense->category_label }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-900">{{ number_format($expense->amount, 2) }}</span>
                            @if($expense->user_id === $currentUserId)
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="editExpense({{ $expense->id }})" 
                                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-sm transition-colors"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="deleteExpense({{ $expense->id }})" 
                                            wire:confirm="Delete this expense?"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-sm transition-colors"
                                            title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        
        @if($expenses->hasPages())
        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
        @endif
    @endif
</div>
