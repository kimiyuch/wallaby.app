<?php

use App\Livewire\ExpenseForm;
use App\Livewire\Expenses;
use App\Models\Expense;
use App\Models\Settlement;
use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Carbon;
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

    $expected = now()->startOfMonth()->subMonth();
    $component->assertSet('selectedMonth', $expected->month)
        ->assertSet('selectedYear', $expected->year);
});

it('only shows expenses for selected month', function () {
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'January Item',
        'date' => '2026-01-15',
    ]);

    Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'February Item',
        'date' => '2026-02-15',
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

it('steps through months correctly at the end of a long month', function () {
    Carbon::setTestNow('2026-10-31 12:00');

    $component = Livewire::actingAs($this->user)->test(Expenses::class);

    $component->call('previousMonth')->assertSet('period', '2026-09')->assertSee('September 2026');
    $component->call('previousMonth')->assertSet('period', '2026-08')->assertSee('August 2026');
    $component->call('nextMonth')->call('nextMonth')->call('nextMonth')
        ->assertSet('period', '2026-11')->assertSee('November 2026');

    Carbon::setTestNow();
});

it('names a month opened by link on the 31st correctly', function () {
    Carbon::setTestNow('2026-10-31 12:00');

    Livewire::actingAs($this->user)
        ->withQueryParams(['period' => '2026-02'])
        ->test(Expenses::class)
        ->assertSee('February 2026');

    Carbon::setTestNow();
});

it('cannot settle a month that has not started yet', function () {
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'amount' => 100,
        'date' => now()->startOfMonth(),
        'is_recurring' => true,
    ]);

    $next = now()->startOfMonth()->addMonth();

    Livewire::actingAs($this->user)
        ->withQueryParams(['period' => $next->format('Y-m')])
        ->test(Expenses::class)
        ->assertDontSeeHtml('title="Settle month"')
        ->call('settleMonth')
        ->assertDispatched('toast', type: 'error');

    expect(Settlement::count())->toBe(0);
});

it('cannot settle without a partner', function () {
    $this->partner->delete();
    Expense::factory()->create(['user_id' => $this->user->id, 'amount' => 100, 'date' => now()]);

    Livewire::actingAs($this->user)
        ->test(Expenses::class)
        ->call('settleMonth')
        ->assertDispatched('toast', type: 'error');

    expect(Settlement::count())->toBe(0);
});

it('handles both partners settling the same month at once', function () {
    Expense::factory()->create(['user_id' => $this->user->id, 'amount' => 100, 'date' => now()]);

    $mine = Livewire::actingAs($this->user)->test(Expenses::class);

    // The partner settles while this page is still open.
    Settlement::factory()->create([
        'space_id' => $this->space->id,
        'settled_by_user_id' => $this->partner->id,
        'month' => now()->month,
        'year' => now()->year,
    ]);

    $mine->call('settleMonth')->assertDispatched('toast', type: 'error');

    expect(Settlement::count())->toBe(1);
});

it('starts a new expense with an empty form after editing another one', function () {
    $rent = Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Rent',
        'amount' => 1000,
        'date' => now(),
    ]);

    $page = Livewire::actingAs($this->user)->test(Expenses::class);
    $page->call('editExpense', $rent->id)->assertSee('Edit expense');

    // Close the panel without saving, then add a new expense.
    $page->set('showExpenseForm', false)
        ->call('openExpenseForm')
        ->assertDispatched('resetExpenseForm')
        ->assertSee('New expense');

    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->call('editExpense', $rent->id)
        ->dispatch('resetExpenseForm')
        ->assertSet('editingExpenseId', null)
        ->assertSet('form.description', '')
        ->set('form.description', 'Pizza')
        ->set('form.amount', '20')
        ->call('save');

    expect(Expense::pluck('description')->sort()->values()->all())->toBe(['Pizza', 'Rent']);
});
