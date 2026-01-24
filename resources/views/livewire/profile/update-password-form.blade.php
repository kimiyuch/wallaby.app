<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
        $this->dispatch('toast', type: 'success', message: 'Password updated');
    }
}; ?>

<section>
    <header class="mb-4">
        <h2 class="text-sm text-gray-900 font-bold pb-2 border-b border-gray-200">
            {{ __('Update Password') }}
        </h2>
    </header>

    <form wire:submit="updatePassword" class="space-y-4">
        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input wire:model="password" id="update_password_password" name="password" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>
