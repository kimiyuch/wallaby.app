<?php

use App\Actions\DeleteAccount;
use App\Livewire\Expenses;
use App\Models\Expense;
use App\Models\Settlement;
use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Livewire\Volt\Volt;

// After the partner deleted their account, the one who stays can close the old space and
// start a new one to invite someone else.

beforeEach(function () {
    Carbon::setTestNow('2026-11-15 12:00');

    $this->space = Space::factory()->create(['invite_code' => 'blue-moon-snow']);
    $this->user = User::factory()->create(['name' => 'Peter', 'space_id' => $this->space->id]);
    $this->partner = User::factory()->create(['name' => 'Maria', 'space_id' => $this->space->id]);
});

afterEach(fn () => Carbon::setTestNow());

function partnerLeaves(User $partner): User
{
    (new DeleteAccount)($partner);

    return $partner->fresh();
}

function settleMonths(Space $space, User $by, array $months): void
{
    foreach ($months as $month) {
        Settlement::factory()->create(['space_id' => $space->id, 'settled_by_user_id' => $by->id, 'month' => $month, 'year' => 2026]);
    }
}

it('is only offered once the partner has left', function () {
    $this->actingAs($this->user);

    Volt::test('profile.space-settings')->assertDontSee('Start fresh');

    $former = partnerLeaves($this->partner);

    Volt::test('profile.space-settings')->assertSee('Start fresh');

    expect($former->hasDeparted())->toBeTrue();
});

it('asks to settle open months first', function () {
    Expense::factory()->create(['user_id' => $this->user->id, 'amount' => 40, 'date' => '2026-09-10']);
    Expense::factory()->create(['user_id' => $this->partner->id, 'amount' => 20, 'date' => '2026-11-03']);
    settleMonths($this->space, $this->user, [9]);
    partnerLeaves($this->partner);

    $this->actingAs($this->user);

    Volt::test('profile.space-settings')
        ->assertSeeHtml('href="'.route('expenses', ['period' => '2026-11']).'"')
        ->assertSee('first, so nothing is left open')
        ->assertDontSee('September 2026')
        ->call('startFresh')
        ->assertDispatched('toast', type: 'error');

    expect($this->user->fresh()->space_id)->toBe($this->space->id);
});

it('moves the user to a new space and clears the old one', function () {
    Expense::factory()->create(['user_id' => $this->user->id, 'description' => 'Groceries', 'amount' => 40, 'date' => '2026-09-10']);
    Expense::factory()->create(['user_id' => $this->partner->id, 'description' => 'Plants', 'amount' => 20, 'date' => '2026-11-03']);
    settleMonths($this->space, $this->user, [9, 11]);
    $former = partnerLeaves($this->partner);

    $this->actingAs($this->user);

    Volt::test('profile.space-settings')
        ->call('startFresh')
        ->assertDispatched('toast', type: 'success');

    $user = $this->user->fresh();

    expect($user->space_id)->not->toBe($this->space->id)
        ->and($user->invite_code)->not->toBe('blue-moon-snow')
        ->and($user->partner)->toBeNull()
        ->and(Space::find($this->space->id))->toBeNull()
        ->and(User::find($former->id))->toBeNull()
        ->and(Settlement::count())->toBe(0)
        ->and(Expense::count())->toBe(0);

    Livewire::actingAs($user)
        ->test(Expenses::class)
        ->assertSee('Invite your person')
        ->assertSee($user->invite_code);
});

it('carries running and upcoming expenses over to the new space from next month', function () {
    $rent = Expense::factory()->create(['user_id' => $this->user->id, 'description' => 'Rent', 'amount' => 900, 'date' => '2026-06-01', 'is_recurring' => true]);
    $ended = Expense::factory()->create(['user_id' => $this->user->id, 'description' => 'Old gym', 'amount' => 50, 'date' => '2026-06-01', 'is_recurring' => true, 'end_date' => '2026-10-31']);
    $flight = Expense::factory()->create(['user_id' => $this->user->id, 'description' => 'Flight', 'amount' => 300, 'date' => '2027-01-20']);
    settleMonths($this->space, $this->user, [6, 7, 8, 9, 10, 11]);
    partnerLeaves($this->partner);

    $this->actingAs($this->user);
    Volt::test('profile.space-settings')
        ->assertSee('Rent and Flight carry over from December 2026')
        ->call('startFresh');

    expect($rent->fresh()->date->toDateString())->toBe('2026-12-01')
        ->and($rent->fresh()->is_recurring)->toBeTrue()
        ->and($flight->fresh()->date->toDateString())->toBe('2027-01-20')
        ->and(Expense::find($ended->id))->toBeNull()
        ->and(Expense::count())->toBe(2);
});

it('does not need months without expenses to be settled', function () {
    Expense::factory()->create(['user_id' => $this->user->id, 'amount' => 40, 'date' => '2026-07-10']);
    settleMonths($this->space, $this->user, [7]);
    partnerLeaves($this->partner);

    $this->actingAs($this->user);

    Volt::test('profile.space-settings')
        ->assertDontSee('nothing is left open')
        ->call('startFresh')
        ->assertDispatched('toast', type: 'success');
});

it('says what carries over in a sentence that fits one expense', function () {
    Expense::factory()->create(['user_id' => $this->user->id, 'description' => 'Rent', 'date' => '2026-06-01', 'is_recurring' => true]);
    settleMonths($this->space, $this->user, [6, 7, 8, 9, 10, 11]);
    partnerLeaves($this->partner);

    $this->actingAs($this->user);

    Volt::test('profile.space-settings')->assertSee('Rent carries over from December 2026');
});
