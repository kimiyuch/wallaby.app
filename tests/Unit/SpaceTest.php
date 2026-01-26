<?php

use App\Models\Space;
use App\Models\User;

it('generates unique invite codes', function () {
    $code1 = Space::generateInviteCode();
    $code2 = Space::generateInviteCode();

    expect($code1)->not->toBe($code2)
        ->and($code1)->toMatch('/^[a-z]+-[a-z]+-[a-z]+$/');
});

it('generates invite codes with three words', function () {
    $code = Space::generateInviteCode();
    $words = explode('-', $code);

    expect($words)->toHaveCount(3);
});

it('can find space by invite code', function () {
    $space = Space::factory()->create(['invite_code' => 'blue-moon-star']);

    $found = Space::findByInviteCode('blue-moon-star');

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($space->id);
});

it('finds space by invite code case-insensitively', function () {
    $space = Space::factory()->create(['invite_code' => 'blue-moon-star']);

    $found = Space::findByInviteCode('BLUE-MOON-STAR');

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($space->id);
});

it('has many users', function () {
    $space = Space::factory()->create();
    User::factory()->count(2)->create(['space_id' => $space->id]);

    expect($space->users)->toHaveCount(2);
});

it('returns first user as owner', function () {
    $space = Space::factory()->create();
    $firstUser = User::factory()->create(['space_id' => $space->id]);
    User::factory()->create(['space_id' => $space->id]);

    expect($space->owner()->id)->toBe($firstUser->id);
});

it('is full with two members', function () {
    $space = Space::factory()->create();

    expect($space->isFull())->toBeFalse();

    User::factory()->create(['space_id' => $space->id]);
    expect($space->isFull())->toBeFalse();

    User::factory()->create(['space_id' => $space->id]);
    expect($space->isFull())->toBeTrue();
});

it('can regenerate invite code', function () {
    $space = Space::factory()->create(['invite_code' => 'old-code-here']);
    $oldCode = $space->invite_code;

    $newCode = $space->regenerateInviteCode();

    expect($newCode)->not->toBe($oldCode)
        ->and($space->fresh()->invite_code)->toBe($newCode);
});
