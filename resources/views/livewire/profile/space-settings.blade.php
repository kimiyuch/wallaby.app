<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function inviteCode(): string
    {
        return $this->user->invite_code ?? '';
    }

    #[Computed]
    public function partnerName(): ?string
    {
        return $this->user->partner?->name;
    }

    #[Computed]
    public function partnerEmail(): ?string
    {
        return $this->user->partner?->email;
    }

    public function regenerateCode(): void
    {
        if (!$this->user->space) {
            $this->dispatch('toast', type: 'error', message: 'No space found.');
            return;
        }
        
        $this->user->space->regenerateInviteCode();
        unset($this->inviteCode); // Clear computed cache
        $this->dispatch('toast', type: 'success', message: 'Invite code regenerated');
    }
}; ?>

<section>
    <header class="mb-4">
        <h2 class="text-sm text-lilac-950 font-display font-semibold">
            {{ __('Partner') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            @if($this->user->partner?->hasDeparted())
                {{ __('Your partner deleted their account. Their expenses stay here so your history still adds up.') }}
            @elseif($this->partnerName)
                {!! __("You're connected with <span class=\"font-semibold text-lilac-950\">:name</span> (:email) and share expenses together.", ['name' => e($this->partnerName), 'email' => e($this->partnerEmail)]) !!}
            @else
                {{ __('Connect with your partner to share and split expenses together.') }}
            @endif
        </p>
    </header>

    <div class="space-y-4">
        @if(!$this->partnerName)
            {{-- Waiting for partner: show invite code --}}
            <div class="p-3 bg-gold-50 border border-gold-200 rounded-lg">
                <p class="text-sm text-gold-800">
                    {{ __('No partner yet. Share your invite code to connect with someone.') }}
                </p>
            </div>

            <div>
                <x-input-label for="invite_code" :value="__('Invite Code')" />
                <x-text-input 
                    id="invite_code"
                    type="text" 
                    value="{{ $this->inviteCode }}" 
                    readonly
                />
                <p class="mt-1 text-xs text-gray-500">
                    {{ __('Share this code with your partner so they can join your space when registering.') }}
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <x-tertiary-button 
                    wire:click="regenerateCode"
                    wire:confirm="Are you sure? This will invalidate your current invite code."
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="regenerateCode">{{ __('Regenerate code') }}</span>
                    <span wire:loading wire:target="regenerateCode">{{ __('Regenerating...') }}</span>
                </x-tertiary-button>
                <x-primary-button 
                    type="button"
                    x-data
                    x-on:click="
                        navigator.clipboard.writeText('{{ $this->inviteCode }}');
                        $dispatch('toast', { type: 'success', message: 'Copied to clipboard' });
                    "
                >
                    {{ __('Copy') }}
                </x-primary-button>
            </div>
        @endif
    </div>
</section>
