<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';
    public bool $showDeleteModal = false;

    public function confirmDelete(): void
    {
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->password = '';
    }

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section>
    <header class="mb-4">
        <h2 class="text-sm text-lilac-950 font-display font-semibold">
            {{ __('Delete Account') }}
        </h2>
    </header>

    <p class="text-sm text-gray-500 mb-4">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
    </p>

    <x-danger-button wire:click="confirmDelete">
        {{ __('Delete Account') }}
    </x-danger-button>

    <!-- Delete Confirmation Modal -->
    <div
        x-data="{ show: @entangle('showDeleteModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[100] overflow-y-auto"
    >
        <!-- Backdrop -->
        <div
            x-show="show"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-lilac-950/30"
            @click="$wire.cancelDelete()"
        ></div>

        <!-- Modal -->
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div
                x-show="show"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white rounded-xl shadow-xl max-w-md w-full p-6"
                @click.stop
            >
                <h3 class="text-sm font-display font-semibold text-lilac-950 mb-2">Delete your account?</h3>
                <p class="text-sm text-gray-500 mb-4">This action cannot be undone. Please enter your password to confirm.</p>
                
                <form wire:submit="deleteUser">
                    <div class="mb-4">
                        <x-input-label for="delete_password" :value="__('Password')" />
                        <x-text-input
                            wire:model="password"
                            id="delete_password"
                            name="password"
                            type="password"
                            placeholder="{{ __('Password') }}"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <x-secondary-button type="button" wire:click="cancelDelete">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-danger-button type="submit">
                            {{ __('Delete Account') }}
                        </x-danger-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
