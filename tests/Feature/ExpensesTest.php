<?php

use App\Livewire\Expenses;
use App\Models\Expense;
use App\Models\Settlement;
use App\Models\Space;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->space = Space::factory()->create();
    $this->user = User::factory()->create(['space_id' => $this->space->id]);
    $this->partner = User::factory()->create(['space_id' => $this->space->id]);
});

it('requires authentication', function () {
    $this->get('/expenses')->assertRedirect('/login');
});

it('requires email verification', function () {
    $unverified = User::factory()->unverified()->create();

    $this->actingAs($unverified)
        ->get('/expenses')
        ->assertRedirect('/verify-email');
});

it('loads for authenticated user', function () {
    $this->actingAs($this->user)
        ->get('/expenses')
        ->assertOk()
        ->assertSeeLivewire(Expenses::class);
});

it('displays user expenses', function () {
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Groceries',
        'amount' => 50.00,
        'date' => now(),
    ]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->assertSee('Groceries')
        ->assertSee('50.00');
});

it('displays partner expenses', function () {
    Expense::factory()->create([
        'user_id' => $this->partner->id,
        'description' => 'Electricity',
        'amount' => 120.00,
        'date' => now(),
    ]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->assertSee('Electricity')
        ->assertSee('120.00');
});

it('can navigate between months', function () {
    $currentMonth = now()->month;
    $currentYear = now()->year;

    $component = Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->assertSet('selectedMonth', $currentMonth)
        ->assertSet('selectedYear', $currentYear);

    $component->call('previousMonth');

    $expected = now()->subMonth();
    $component->assertSet('selectedMonth', $expected->month)
        ->assertSet('selectedYear', $expected->year);
});

it('only shows expenses for selected month', function () {
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'January Item',
        'date' => now()->setMonth(1)->setYear(2026),
    ]);

    Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'February Item',
        'date' => now()->setMonth(2)->setYear(2026),
    ]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->set('selectedMonth', 1)
        ->set('selectedYear', 2026)
        ->assertSee('January Item')
        ->assertDontSee('February Item');
});

it('can delete own expense', function () {
    $expense = Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'To Delete',
        'date' => now(),
    ]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->call('deleteExpense', $expense->id)
        ->assertDispatched('toast');

    $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
});

it('cannot delete partner expense', function () {
    $expense = Expense::factory()->create([
        'user_id' => $this->partner->id,
        'description' => 'Partner Expense',
        'date' => now(),
    ]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->call('deleteExpense', $expense->id);

    $this->assertDatabaseHas('expenses', ['id' => $expense->id]);
});

it('cannot delete expense in settled month', function () {
    Settlement::factory()->create([
        'space_id' => $this->space->id,
        'month' => now()->month,
        'year' => now()->year,
    ]);

    $expense = Expense::factory()->create([
        'user_id' => $this->user->id,
        'date' => now(),
    ]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->call('deleteExpense', $expense->id);

    $this->assertDatabaseHas('expenses', ['id' => $expense->id]);
});

it('can settle month', function () {
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'amount' => 100,
        'date' => now(),
    ]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->call('settleMonth')
        ->assertDispatched('toast');

    $this->assertDatabaseHas('settlements', [
        'space_id' => $this->space->id,
        'month' => now()->month,
        'year' => now()->year,
    ]);
});

it('cannot settle already settled month', function () {
    Settlement::factory()->create([
        'space_id' => $this->space->id,
        'month' => now()->month,
        'year' => now()->year,
    ]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->call('settleMonth')
        ->assertDispatched('toast', type: 'error');

    $this->assertDatabaseCount('settlements', 1);
});

it('shows recurring expenses', function () {
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Netflix',
        'is_recurring' => true,
        'date' => now()->subMonths(3),
        'end_date' => null,
    ]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->assertSee('Netflix')
        ->assertSee('Recurring');
});

it('calculates balance correctly', function () {
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'amount' => 100,
        'date' => now(),
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(Expenses::class);

    $component->assertSee('+50.00');
});
