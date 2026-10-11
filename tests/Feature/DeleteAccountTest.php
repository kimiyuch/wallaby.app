<?php

use App\Livewire\Expenses;
use App\Models\Expense;
use App\Models\Settlement;
use App\Models\Space;
use App\Models\User;
use App\Notifications\MonthSettledNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Livewire\Volt\Volt;

beforeEach(function () {
    Carbon::setTestNow('2026-11-15 12:00');

    $this->space = Space::factory()->create();
    $this->user = User::factory()->create(['name' => 'Peter', 'email' => 'peter@example.com', 'space_id' => $this->space->id]);
    $this->partner = User::factory()->create(['name' => 'Maria', 'space_id' => $this->space->id]);
});

afterEach(fn () => Carbon::setTestNow());

function deleteAccount(User $user)
{
    test()->actingAs($user);

    return Volt::test('profile.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');
}

it('keeps the expenses for the partner but removes the personal data', function () {
    $expense = Expense::factory()->create(['user_id' => $this->user->id, 'amount' => 80, 'date' => '2026-09-10']);
    Settlement::factory()->create(['space_id' => $this->space->id, 'settled_by_user_id' => $this->user->id, 'month' => 9, 'year' => 2026]);

    deleteAccount($this->user)->assertHasNoErrors()->assertRedirect('/');

    $this->assertGuest();

    $former = $this->user->fresh();
    expect($former)->not->toBeNull()
        ->and($former->name)->toBe('Former partner')
        ->and($former->email)->not->toContain('peter')
        ->and($former->departed_at)->not->toBeNull()
        ->and(Expense::find($expense->id))->not->toBeNull()
        ->and(Settlement::isSettled($this->space->id, 9, 2026))->toBeTrue();

    $this->post('/logout');
    expect(auth()->attempt(['email' => 'peter@example.com', 'password' => 'password']))->toBeFalse();

    Livewire::actingAs($this->partner)
        ->withQueryParams(['period' => '2026-09'])
        ->test(Expenses::class)
        ->assertSee('Former partner')
        ->assertSee('80.00');
});

it('signs the former partner out everywhere', function () {
    DB::table('sessions')->insert(['id' => 'other-device', 'user_id' => $this->user->id, 'payload' => '', 'last_activity' => now()->getTimestamp()]);

    deleteAccount($this->user);

    expect(DB::table('sessions')->where('user_id', $this->user->id)->exists())->toBeFalse();
});

it('deletes everything when there is no partner', function () {
    $this->partner->delete();
    Expense::factory()->create(['user_id' => $this->user->id, 'date' => now()]);

    deleteAccount($this->user)->assertRedirect('/');

    expect(User::find($this->user->id))->toBeNull()
        ->and(Expense::count())->toBe(0)
        ->and(Space::find($this->space->id))->toBeNull();
});

it('deletes everything when the partner has already left', function () {
    Expense::factory()->create(['user_id' => $this->partner->id, 'date' => now()]);
    Expense::factory()->create(['user_id' => $this->user->id, 'date' => now()]);
    deleteAccount($this->partner);

    deleteAccount($this->user->fresh());

    expect(User::count())->toBe(0)
        ->and(Expense::count())->toBe(0)
        ->and(Space::find($this->space->id))->toBeNull();
});

it('does not email a former partner when a month is settled', function () {
    Notification::fake();
    Expense::factory()->create(['user_id' => $this->partner->id, 'amount' => 50, 'date' => now()]);
    deleteAccount($this->user);

    Livewire::actingAs($this->partner->fresh())
        ->test(Expenses::class)
        ->call('settleMonth')
        ->assertDispatched('toast', type: 'success');

    Notification::assertSentTo($this->partner, MonthSettledNotification::class);
    Notification::assertNotSentTo($this->user->fresh(), MonthSettledNotification::class);
});

it('tells the user what stays and what is still owed before deleting', function () {
    // August is settled, so only September to November count: Peter paid 100 shared, Maria 40.
    Settlement::factory()->create(['space_id' => $this->space->id, 'settled_by_user_id' => $this->user->id, 'month' => 8, 'year' => 2026]);
    Expense::factory()->create(['user_id' => $this->user->id, 'amount' => 500, 'date' => '2026-08-10']);
    Expense::factory()->create(['user_id' => $this->user->id, 'amount' => 100, 'date' => '2026-09-10']);
    Expense::factory()->create(['user_id' => $this->partner->id, 'amount' => 40, 'date' => '2026-11-02']);

    $this->actingAs($this->user);

    Volt::test('profile.delete-user-form')
        ->assertSee('Your expenses stay visible to Maria, without your name or email.')
        ->assertSee('Maria still owes you 30.00 from months that aren’t settled.');
});

it('shows a former partner on the profile page', function () {
    deleteAccount($this->user);

    $this->actingAs($this->partner->fresh());

    Volt::test('profile.space-settings')
        ->assertSee('Your partner deleted their account.')
        ->assertDontSee('former-');
});
