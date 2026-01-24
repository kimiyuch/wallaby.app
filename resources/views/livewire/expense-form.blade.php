<div>
    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-sm text-gray-500 mb-1">Amount</label>
            <input type="number" step="0.05" wire:model="amount" 
                   class="w-full rounded-md border-gray-200 text-sm focus:border-gray-300 focus:ring-0 placeholder-gray-300"
                   placeholder="0.00">
            @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm text-gray-500 mb-1">Description</label>
            <input type="text" wire:model="description" 
                   class="w-full rounded-md border-gray-200 text-sm focus:border-gray-300 focus:ring-0 placeholder-gray-300"
                   placeholder="What was it for?">
            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" wire:model.live="is_recurring" 
                   id="is_recurring"
                   class="rounded border-gray-300 text-gray-900 focus:ring-0">
            <label for="is_recurring" class="text-sm text-gray-500">Recurring monthly</label>
        </div>

        <div>
            <label class="block text-sm text-gray-500 mb-1">{{ $is_recurring ? 'Start date' : 'Date' }}</label>
            <input type="date" wire:model="date" 
                   class="w-full rounded-md border-gray-200 text-sm focus:border-gray-300 focus:ring-0">
            @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        @if($is_recurring)
        <div>
            <label class="block text-sm text-gray-500 mb-1">End date (optional)</label>
            <input type="date" wire:model="end_date" 
                   class="w-full rounded-md border-gray-200 text-sm focus:border-gray-300 focus:ring-0">
            @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        @endif

        <div class="flex items-center justify-end gap-3 pt-4">
            <x-secondary-button type="button" wire:click="$parent.closeExpenseForm">
                Cancel
            </x-secondary-button>
            <x-primary-button type="submit">
                {{ $editingExpenseId ? 'Update' : 'Create' }}
            </x-primary-button>
        </div>
    </form>
</div>
