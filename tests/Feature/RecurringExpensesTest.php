<?php

use App\Livewire\ExpenseForm;
use App\Livewire\Expenses;
use App\Models\Expense;
use App\Models\Settlement;
use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

// Changes to a recurring expense apply from the month you're looking at onwards,
// so months that are already settled stay exactly as they were.

beforeEach(function () {
    Carbon::setTestNow('2026-11-15 12:00');

    $this->space = Space::factory()->create();
    $this->user = User::factory()->create(['space_id' => $this->space->id]);
    $this->partner = User::factory()->create(['space_id' => $this->space->id]);

    $this->rent = Expense::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Rent',
        'amount' => 100,
        'date' => '2026-08-01',
        'is_recurring' => true,
    ]);
});

afterEach(fn () => Carbon::setTestNow());

function settle(Space $space, User $by, int $month, int $year = 2026): void
{
    Settlement::factory()->create([
        'space_id' => $space->id,
        'settled_by_user_id' => $by->id,
        'month' => $month,
        'year' => $year,
    ]);
}

function totalFor(User $user, string $period): float
{
    return (float) Livewire::actingAs($user)
        ->withQueryParams(['period' => $period])
        ->test(Expenses::class)
        ->viewData('myTotal');
}

function editRent(User $user, string $period, Expense $rent): Testable
{
    [$year, $month] = array_map('intval', explode('-', $period));

    return Livewire::actingAs($user)
        ->test(ExpenseForm::class, ['selectedMonth' => $month, 'selectedYear' => $year])
        ->call('editExpense', $rent->id);
}

it('stops a recurring expense from the viewed month on when deleted', function () {
    settle($this->space, $this->user, 9);

    Livewire::actingAs($this->user)
        ->withQueryParams(['period' => '2026-11'])
        ->test(Expenses::class)
        ->call('deleteExpense', $this->rent->id)
        ->assertDispatched('toast', type: 'success');

    expect($this->rent->fresh()->end_date->toDateString())->toBe('2026-10-31')
        ->and(totalFor($this->user, '2026-09'))->toBe(100.0)
        ->and(totalFor($this->user, '2026-10'))->toBe(100.0)
        ->and(totalFor($this->user, '2026-11'))->toBe(0.0);
});

it('deletes a recurring expense completely in the month it starts', function () {
    Livewire::actingAs($this->user)
        ->withQueryParams(['period' => '2026-08'])
        ->test(Expenses::class)
        ->call('deleteExpense', $this->rent->id);

    expect(Expense::find($this->rent->id))->toBeNull();
});

it('does not delete a recurring expense that runs into a later settled month', function () {
    settle($this->space, $this->user, 10);

    Livewire::actingAs($this->user)
        ->withQueryParams(['period' => '2026-09'])
        ->test(Expenses::class)
        ->call('deleteExpense', $this->rent->id)
        ->assertDispatched('toast', type: 'error');

    expect($this->rent->fresh()->end_date)->toBeNull();
});

it('splits a recurring expense when it is changed in a later month', function () {
    settle($this->space, $this->user, 9);

    editRent($this->user, '2026-11', $this->rent)
        ->assertSet('form.date', '2026-11-01')
        ->set('form.amount', '300')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('toast', type: 'success');

    $new = Expense::where('id', '!=', $this->rent->id)->sole();

    expect($this->rent->fresh()->end_date->toDateString())->toBe('2026-10-31')
        ->and((float) $this->rent->fresh()->amount)->toBe(100.0)
        ->and($new->date->toDateString())->toBe('2026-11-01')
        ->and((float) $new->amount)->toBe(300.0)
        ->and($new->is_recurring)->toBeTrue()
        ->and(totalFor($this->user, '2026-09'))->toBe(100.0)
        ->and(totalFor($this->user, '2026-11'))->toBe(300.0)
        ->and(totalFor($this->user, '2026-12'))->toBe(300.0);
});

it('can change a recurring expense whose first month is settled', function () {
    settle($this->space, $this->user, 8);

    editRent($this->user, '2026-11', $this->rent)
        ->assertNotDispatched('toast')
        ->set('form.amount', '300')
        ->call('save')
        ->assertDispatched('toast', type: 'success');

    expect(totalFor($this->user, '2026-08'))->toBe(100.0)
        ->and(totalFor($this->user, '2026-11'))->toBe(300.0);
});

it('does not move the start of a split before the viewed month', function () {
    editRent($this->user, '2026-11', $this->rent)
        ->set('form.date', '2026-09-01')
        ->call('save')
        ->assertHasErrors('form.date');

    expect(Expense::count())->toBe(1);
});

it('edits a recurring expense in place in the month it starts', function () {
    editRent($this->user, '2026-08', $this->rent)
        ->assertSet('form.date', '2026-08-01')
        ->set('form.amount', '120')
        ->call('save');

    expect(Expense::count())->toBe(1)
        ->and((float) $this->rent->fresh()->amount)->toBe(120.0);
});

it('does not split when nothing was changed', function () {
    editRent($this->user, '2026-11', $this->rent)->call('save');

    expect(Expense::count())->toBe(1)
        ->and($this->rent->fresh()->end_date)->toBeNull();
});

it('does not change a recurring expense that runs into a later settled month', function () {
    settle($this->space, $this->user, 10);

    editRent($this->user, '2026-09', $this->rent)
        ->set('form.amount', '300')
        ->call('save')
        ->assertDispatched('toast', type: 'error');

    expect(Expense::count())->toBe(1)
        ->and((float) $this->rent->fresh()->amount)->toBe(100.0);
});

it('does not add a recurring expense that would reach into a settled month', function () {
    settle($this->space, $this->user, 10);

    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class, ['selectedMonth' => 9, 'selectedYear' => 2026])
        ->set('form.description', 'Gym')
        ->set('form.amount', '50')
        ->set('form.date', '2026-09-01')
        ->set('form.is_recurring', true)
        ->call('save')
        ->assertDispatched('toast', type: 'error');

    expect(Expense::where('description', 'Gym')->exists())->toBeFalse();
});

it('can add a recurring expense that starts after a settled month', function () {
    settle($this->space, $this->user, 10);

    Livewire::actingAs($this->user)
        ->test(ExpenseForm::class, ['selectedMonth' => 11, 'selectedYear' => 2026])
        ->set('form.description', 'Gym')
        ->set('form.amount', '50')
        ->set('form.date', '2026-11-01')
        ->set('form.is_recurring', true)
        ->call('save')
        ->assertDispatched('toast', type: 'success');

    expect(Expense::where('description', 'Gym')->exists())->toBeTrue();
});

it('tells the user which month a change applies from', function () {
    editRent($this->user, '2026-11', $this->rent)
        ->assertSee('Changes apply from November 2026 on');

    editRent($this->user, '2026-08', $this->rent)
        ->assertDontSee('Changes apply from');
});

it('asks to stop rather than delete a recurring expense from an earlier month', function () {
    Livewire::actingAs($this->user)
        ->withQueryParams(['period' => '2026-11'])
        ->test(Expenses::class)
        ->assertSee('Stop recurring expense?')
        ->assertDontSee('Delete expense?');

    Livewire::actingAs($this->user)
        ->withQueryParams(['period' => '2026-08'])
        ->test(Expenses::class)
        ->assertSee('Delete expense?');
});
