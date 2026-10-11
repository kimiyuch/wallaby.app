<?php

use App\Models\Space;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $invite_code = '';

    public function mount(): void
    {
        // Check for invite code in URL
        $this->invite_code = request()->query('code', '');
    }

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'invite_code' => ['nullable', 'string'],
        ]);

        // Handle space joining/creation
        $space = null;
        
        if (!empty($this->invite_code)) {
            $space = Space::findByInviteCode($this->invite_code);
            
            if (!$space) {
                $this->addError('invite_code', 'Invalid invite code.');
                return;
            }
            
            if ($space->isFull()) {
                $this->addError('invite_code', 'This space already has two members.');
                return;
            }
        } else {
            // Create new space for this user
            $space = Space::create([
                'invite_code' => Space::generateInviteCode(),
            ]);
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['space_id'] = $space->id;
        
        unset($validated['invite_code']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('expenses', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register" class="space-y-4">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div>
            <h3 class="text-sm text-lilac-950 font-display font-semibold mb-1">{{ __('Invite Code') }} <span class="font-normal text-gray-400">(optional)</span></h3>
            <p class="text-sm text-gray-500 mb-3">{{ __('Got an invite from your partner? Enter their code to join their space. No code? We\'ll create a new space for you to share.') }}</p>
            <x-text-input 
                wire:model="invite_code" 
                id="invite_code" 
                type="text" 
                name="invite_code" 
                placeholder="e.g. blue-moon-snow"
            />
            <x-input-error :messages="$errors->get('invite_code')" class="mt-1" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Register') }}
        </x-primary-button>

        <div class="text-center">
            <a class="text-sm text-gray-500 hover:text-gray-700" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>
        </div>
    </form>
</div>
