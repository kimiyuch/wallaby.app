<?php

use App\Models\Expense;
use App\Models\Space;
use App\Models\User;
use App\Services\BalanceCalculator;

beforeEach(function () {
    $this->calculator = new BalanceCalculator;

    $space = Space::factory()->create();
    $this->user = User::factory()->create(['space_id' => $space->id]);
    $this->partner = User::factory()->create(['space_id' => $space->id]);
});

it('returns empty array without partner', function () {
    $userWithoutPartner = User::factory()->create();

    $result = $this->calculator->calculate(
        collect(),
        collect(),
        $userWithoutPartner,
        null
    );

    expect($result)->toBeEmpty();
});

it('splits evenly with equal expenses', function () {
    $userExpenses = collect([
        Expense::factory()->make(['amount' => 100, 'is_personal' => false]),
    ]);
    $partnerExpenses = collect([
        Expense::factory()->make(['amount' => 100, 'is_personal' => false]),
    ]);

    $result = $this->calculator->calculate(
        $userExpenses,
        $partnerExpenses,
        $this->user,
        $this->partner
    );

    expect($result[$this->user->id])->toBe(0.0)
        ->and($result[$this->partner->id])->toBe(0.0);
});

it('shows user owes when partner paid more', function () {
    $userExpenses = collect([
        Expense::factory()->make(['amount' => 50, 'is_personal' => false]),
    ]);
    $partnerExpenses = collect([
        Expense::factory()->make(['amount' => 150, 'is_personal' => false]),
    ]);

    $result = $this->calculator->calculate(
        $userExpenses,
        $partnerExpenses,
        $this->user,
        $this->partner
    );

    expect($result[$this->user->id])->toBe(50.0)
        ->and($result[$this->partner->id])->toBe(-50.0);
});

it('shows partner owes when user paid more', function () {
    $userExpenses = collect([
        Expense::factory()->make(['amount' => 200, 'is_personal' => false]),
    ]);
    $partnerExpenses = collect([
        Expense::factory()->make(['amount' => 0, 'is_personal' => false]),
    ]);

    $result = $this->calculator->calculate(
        $userExpenses,
        $partnerExpenses,
        $this->user,
        $this->partner
    );

    expect($result[$this->user->id])->toBe(-100.0)
        ->and($result[$this->partner->id])->toBe(100.0);
});

it('charges personal expenses fully to partner', function () {
    $userExpenses = collect([
        Expense::factory()->make(['amount' => 100, 'is_personal' => true]),
    ]);
    $partnerExpenses = collect();

    $result = $this->calculator->calculate(
        $userExpenses,
        $partnerExpenses,
        $this->user,
        $this->partner
    );

    expect($result[$this->user->id])->toBe(-100.0)
        ->and($result[$this->partner->id])->toBe(100.0);
});

it('handles mixed personal and shared expenses', function () {
    $userExpenses = collect([
        Expense::factory()->make(['amount' => 100, 'is_personal' => false]),
        Expense::factory()->make(['amount' => 50, 'is_personal' => true]),
    ]);
    $partnerExpenses = collect([
        Expense::factory()->make(['amount' => 100, 'is_personal' => false]),
    ]);

    $result = $this->calculator->calculate(
        $userExpenses,
        $partnerExpenses,
        $this->user,
        $this->partner
    );

    expect($result[$this->user->id])->toBe(-50.0)
        ->and($result[$this->partner->id])->toBe(50.0);
});

it('handles zero expenses', function () {
    $result = $this->calculator->calculate(
        collect(),
        collect(),
        $this->user,
        $this->partner
    );

    expect($result[$this->user->id])->toBe(0.0)
        ->and($result[$this->partner->id])->toBe(0.0);
});

it('handles decimal amounts correctly', function () {
    $userExpenses = collect([
        Expense::factory()->make(['amount' => 33.33, 'is_personal' => false]),
    ]);
    $partnerExpenses = collect([
        Expense::factory()->make(['amount' => 66.67, 'is_personal' => false]),
    ]);

    $result = $this->calculator->calculate(
        $userExpenses,
        $partnerExpenses,
        $this->user,
        $this->partner
    );

    expect($result[$this->user->id])->toBe(16.67)
        ->and($result[$this->partner->id])->toBe(-16.67);
});
