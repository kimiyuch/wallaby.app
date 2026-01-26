<?php

use App\Models\Expense;
use App\Models\User;

it('belongs to a user', function () {
    $user = User::factory()->create();
    $expense = Expense::factory()->create(['user_id' => $user->id]);

    expect($expense->user)
        ->toBeInstanceOf(User::class)
        ->and($expense->user->id)->toBe($user->id);
});

it('casts amount to decimal', function () {
    $expense = Expense::factory()->create(['amount' => 99.99]);

    expect($expense->amount)->toBe('99.99');
});

it('casts date to carbon', function () {
    $expense = Expense::factory()->create(['date' => '2026-01-15']);

    expect($expense->date)
        ->toBeInstanceOf(Carbon\Carbon::class)
        ->and($expense->date->format('Y-m-d'))->toBe('2026-01-15');
});

it('casts booleans correctly', function () {
    $expense = Expense::factory()->create([
        'is_recurring' => 1,
        'is_personal' => 0,
    ]);

    expect($expense->is_recurring)->toBeTrue()
        ->and($expense->is_personal)->toBeFalse();
});

it('returns false for isActiveForMonth when not recurring', function () {
    $expense = Expense::factory()->create([
        'is_recurring' => false,
        'date' => '2026-01-15',
    ]);

    expect($expense->isActiveForMonth(1, 2026))->toBeFalse();
});

it('returns true for isActiveForMonth in current and future months', function () {
    $expense = Expense::factory()->create([
        'is_recurring' => true,
        'date' => '2026-01-15',
        'end_date' => null,
    ]);

    expect($expense->isActiveForMonth(1, 2026))->toBeTrue()
        ->and($expense->isActiveForMonth(2, 2026))->toBeTrue()
        ->and($expense->isActiveForMonth(12, 2026))->toBeTrue();
});

it('returns false for isActiveForMonth before start date', function () {
    $expense = Expense::factory()->create([
        'is_recurring' => true,
        'date' => '2026-06-01',
        'end_date' => null,
    ]);

    expect($expense->isActiveForMonth(5, 2026))->toBeFalse()
        ->and($expense->isActiveForMonth(1, 2026))->toBeFalse()
        ->and($expense->isActiveForMonth(6, 2026))->toBeTrue();
});

it('respects end date for recurring expenses', function () {
    $expense = Expense::factory()->create([
        'is_recurring' => true,
        'date' => '2026-01-01',
        'end_date' => '2026-06-30',
    ]);

    expect($expense->isActiveForMonth(1, 2026))->toBeTrue()
        ->and($expense->isActiveForMonth(6, 2026))->toBeTrue()
        ->and($expense->isActiveForMonth(7, 2026))->toBeFalse()
        ->and($expense->isActiveForMonth(12, 2026))->toBeFalse();
});
