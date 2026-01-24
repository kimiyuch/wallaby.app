<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <p class="text-sm text-gray-500 mb-4">
        {{ __('Enter your email and we\'ll send you a reset link.') }}
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-4">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Send Reset Link') }}
        </x-primary-button>

        <div class="text-center">
            <a class="text-sm text-gray-500 hover:text-gray-700" href="{{ route('login') }}" wire:navigate>
                {{ __('Back to login') }}
            </a>
        </div>
    </form>
</div>
