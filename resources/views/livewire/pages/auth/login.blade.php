<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input 
                wire:model="form.email" 
                id="email" 
                type="email" 
                name="email" 
                required 
                autofocus 
                autocomplete="username" 
            />
            <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input 
                wire:model="form.password" 
                id="password"
                type="password"
                name="password"
                required 
                autocomplete="current-password" 
            />
            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <div class="flex items-center">
            <input 
                wire:model="form.remember" 
                id="remember" 
                type="checkbox" 
                class="rounded border-gray-300 text-gray-900 focus:ring-0" 
                name="remember"
            >
            <label for="remember" class="ms-2 text-sm text-gray-500">
                {{ __('Remember me') }}
            </label>
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Log in') }}
        </x-primary-button>

        @if (Route::has('password.request'))
            <div class="text-center">
                <a class="text-sm text-gray-500 hover:text-gray-700" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            </div>
        @endif
    </form>
</div>
