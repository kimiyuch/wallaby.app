<?php

use App\Livewire\ExpenseForm;
use App\Models\Expense;
use App\Models\Settlement;
use App\Models\Space;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->space = Space::factory()->create();
    $this->user = User::factory()->create(['space_id' => $this->space->id]);
});

it('can create an expense', function () {
    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->set('form.description', 'Test Expense')
        ->set('form.amount', '50.00')
        ->set('form.date', now()->format('Y-m-d'))
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('expenseUpdated');

    $this->assertDatabaseHas('expenses', [
        'user_id' => $this->user->id,
        'description' => 'Test Expense',
        'amount' => 50.00,
    ]);
});

it('validates required fields', function () {
    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->set('form.description', '')
        ->set('form.amount', '')
        ->set('form.date', '')
        ->call('save')
        ->assertHasErrors(['form.description', 'form.amount', 'form.date']);
});

it('validates minimum amount', function () {
    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->set('form.description', 'Test')
        ->set('form.amount', '0')
        ->set('form.date', now()->format('Y-m-d'))
        ->call('save')
        ->assertHasErrors(['form.amount']);
});

it('can create recurring expense', function () {
    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->set('form.description', 'Rent')
        ->set('form.amount', '1500.00')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.is_recurring', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('expenses', [
        'description' => 'Rent',
        'is_recurring' => true,
    ]);
});

it('can create personal expense', function () {
    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->set('form.description', 'Partner Gift')
        ->set('form.amount', '75.00')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.is_personal', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('expenses', [
        'description' => 'Partner Gift',
        'is_personal' => true,
    ]);
});

it('can edit own expense', function () {
    $expense = Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Original',
        'amount' => 100,
        'date' => now(),
    ]);

    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->call('editExpense', $expense->id)
        ->assertSet('form.description', 'Original')
        ->set('form.description', 'Updated')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
        'description' => 'Updated',
    ]);
});

it('cannot edit other user expense', function () {
    $otherUser = User::factory()->create(['space_id' => $this->space->id]);
    $expense = Expense::factory()->create([
        'user_id' => $otherUser->id,
        'description' => 'Other User Expense',
        'date' => now(),
    ]);

    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->call('editExpense', $expense->id)
        ->assertDispatched('toast', type: 'error');
});

it('cannot create expense in settled month', function () {
    Settlement::factory()->create([
        'space_id' => $this->space->id,
        'month' => now()->month,
        'year' => now()->year,
    ]);

    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->set('form.description', 'Late Expense')
        ->set('form.amount', '50.00')
        ->set('form.date', now()->format('Y-m-d'))
        ->call('save')
        ->assertDispatched('toast', type: 'error');

    $this->assertDatabaseMissing('expenses', [
        'description' => 'Late Expense',
    ]);
});

it('validates end date after start date', function () {
    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->set('form.description', 'Subscription')
        ->set('form.amount', '10.00')
        ->set('form.date', '2026-06-01')
        ->set('form.is_recurring', true)
        ->set('form.end_date', '2026-05-01')
        ->call('save')
        ->assertHasErrors(['form.end_date']);
});

it('only saves end date for recurring expenses', function () {
    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->set('form.description', 'One Time')
        ->set('form.amount', '50.00')
        ->set('form.date', '2026-01-15')
        ->set('form.is_recurring', false)
        ->set('form.end_date', '2026-12-31')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('expenses', [
        'description' => 'One Time',
        'end_date' => null,
    ]);
});

it('resets form after save', function () {
    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class)
        ->set('form.description', 'Test')
        ->set('form.amount', '50.00')
        ->set('form.date', now()->format('Y-m-d'))
        ->call('save')
        ->assertSet('form.description', '')
        ->assertSet('form.amount', '');
});
