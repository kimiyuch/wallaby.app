<?php

use App\Livewire\Expenses;
use App\Models\Expense;
use App\Models\Space;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->space = Space::factory()->create(['invite_code' => 'ruby-glow-kiwi']);
    $this->user = User::factory()->create(['name' => 'Marcel', 'space_id' => $this->space->id]);
});

it('welcomes a new user with their invite code and link', function () {
    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->assertSee('Welcome to Wallaby, Marcel')
        ->assertSee('ruby-glow-kiwi')
        ->assertSee(route('register', ['code' => 'ruby-glow-kiwi']), escape: false)
        ->assertSee('Add your first expense');
});

it('keeps asking for the partner once the first expense is in', function () {
    Expense::factory()->create(['user_id' => $this->user->id, 'date' => now()]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->assertDontSee('Welcome to Wallaby')
        ->assertSee('Invite your person')
        ->assertSee('ruby-glow-kiwi');
});

it('goes away once the partner has joined', function () {
    User::factory()->create(['space_id' => $this->space->id]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->assertDontSee('Invite your person')
        ->assertDontSee('ruby-glow-kiwi');
});
