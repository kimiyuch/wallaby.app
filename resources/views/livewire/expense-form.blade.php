<div>
    <form wire:submit="save" class="space-y-4">
        <div>
            <x-input-label for="amount" :value="__('Amount')" />
            <x-text-input 
                type="number" 
                step="0.05" 
                wire:model.blur="form.amount" 
                id="amount"
                placeholder="0.00"
            />
            <x-input-error :messages="$errors->get('form.amount')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="description" :value="__('Description')" />
            <x-text-input 
                type="text" 
                wire:model.blur="form.description" 
                id="description"
                placeholder="What was it for?"
            />
            <x-input-error :messages="$errors->get('form.description')" class="mt-1" />
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" wire:model.live="form.is_recurring"
                   id="is_recurring"
                   class="rounded border-gray-300 text-gray-900 focus:ring-0">
            <label for="is_recurring" class="text-sm text-gray-500">Recurring monthly</label>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" wire:model="form.is_personal"
                   id="is_personal"
                   class="rounded border-gray-300 text-gray-900 focus:ring-0">
            <label for="is_personal" class="text-sm text-gray-500">Paid for partner</label>
        </div>

        <div>
            <x-input-label for="date" :value="$form->is_recurring ? __('Start date') : __('Date')" />
            <x-text-input 
                type="date" 
                wire:model.blur="form.date" 
                id="date"
            />
            <x-input-error :messages="$errors->get('form.date')" class="mt-1" />
        </div>

        @if($form->is_recurring)
        <div>
            <x-input-label for="end_date" :value="__('End date (optional)')" />
            <x-text-input 
                type="date" 
                wire:model.blur="form.end_date" 
                id="end_date"
            />
            <x-input-error :messages="$errors->get('form.end_date')" class="mt-1" />
        </div>
        @endif

        <div class="flex items-center justify-end gap-3 pt-2">
            <x-secondary-button type="button" wire:click="$parent.closeExpenseForm">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-primary-button type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">
                    {{ $editingExpenseId ? __('Update') : __('Create') }}
                </span>
                <span wire:loading wire:target="save">
                    {{ __('Saving...') }}
                </span>
            </x-primary-button>
        </div>
    </form>
</div>
