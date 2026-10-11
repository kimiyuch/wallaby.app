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

it('explains why a signed-in user opening their own invite link lands back on expenses', function () {
    $this->actingAs($this->user)
        ->get('/register?code=ruby-glow-kiwi')
        ->assertRedirect(route('expenses'))
        ->assertSessionHas('toast.message', "That's your own invite link. Send it to your person, it opens the sign-up with your code filled in.");
});

it('tells a signed-in user to log out before joining someone else\'s space', function () {
    Space::factory()->create(['invite_code' => 'blue-moon-snow']);

    $this->actingAs($this->user)
        ->get('/register?code=blue-moon-snow')
        ->assertRedirect(route('expenses'))
        ->assertSessionHas('toast.message', 'You\'re signed in as '.$this->user->email.'. Log out first to join with this invite.');
});

it('sends signed-in users from guest pages to their expenses', function () {
    $this->actingAs($this->user)
        ->get('/login')
        ->assertRedirect(route('expenses'))
        ->assertSessionMissing('toast');
});
