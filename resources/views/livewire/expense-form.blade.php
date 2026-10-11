<div x-data="{ showOptions: {{ $form->is_recurring || $errors->has('form.date') || $errors->has('form.end_date') ? 'true' : 'false' }} }">
    <form wire:submit="save" class="space-y-4">
        <div>
            <x-input-label for="description" :value="__('Description')" />
            <x-text-input 
                type="text" 
                wire:model.blur="form.description" 
                id="description"
                placeholder="What was it for?"
                :error="$errors->has('form.description')"
            />
            <x-input-error :messages="$errors->get('form.description')" />
        </div>

        <div>
            <x-input-label for="amount" :value="__('Amount')" />
            <x-text-input 
                type="number" 
                step="0.05" 
                wire:model.blur="form.amount" 
                id="amount"
                placeholder="0.00"
                :error="$errors->has('form.amount')"
            />
            <x-input-error :messages="$errors->get('form.amount')" />
        </div>

        <!-- More Options Toggle -->
        <div>
            <button 
                type="button"
                @click="showOptions = !showOptions"
                class="flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition-colors"
            >
                <svg 
                    class="w-4 h-4 transition-transform duration-200" 
                    :class="{ 'rotate-90': showOptions }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
                </svg>
                <span>More options</span>
            </button>
        </div>

        <!-- Collapsible Options -->
        <div x-show="showOptions" x-collapse class="space-y-4">
            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model.live="form.is_recurring"
                       id="is_recurring"
                       class="rounded-sm border-gray-300 text-lilac-950 focus:ring-0">
                <label for="is_recurring" class="text-sm text-gray-500">Recurring monthly</label>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model="form.is_personal"
                       id="is_personal"
                       class="rounded-sm border-gray-300 text-lilac-950 focus:ring-0">
                <label for="is_personal" class="text-sm text-gray-500">Paid for partner</label>
            </div>

            <div>
                <x-input-label for="date" :value="$form->is_recurring ? __('Start date') : __('Date')" />
                <x-text-input 
                    type="date" 
                    wire:model.blur="form.date" 
                    id="date"
                    :error="$errors->has('form.date')"
                />
                <x-input-error :messages="$errors->get('form.date')" />
            </div>

            @if($form->is_recurring)
            <div>
                <x-input-label for="end_date" :value="__('End date (optional)')" />
                <x-text-input 
                    type="date" 
                    wire:model.blur="form.end_date" 
                    id="end_date"
                    :error="$errors->has('form.end_date')"
                />
                <x-input-error :messages="$errors->get('form.end_date')" />
            </div>
            @endif
        </div>

        @if($this->appliesFrom)
            <p class="text-sm text-gray-500">Changes apply from {{ $this->appliesFrom }} on. Earlier months stay as they were.</p>
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
