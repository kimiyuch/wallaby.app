<?php

namespace Database\Factories;

use App\Models\Space;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpaceFactory extends Factory
{
    protected $model = Space::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'invite_code' => Space::generateInviteCode(),
        ];
    }
}
