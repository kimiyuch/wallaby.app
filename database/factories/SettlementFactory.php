<?php

namespace Database\Factories;

use App\Models\Settlement;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettlementFactory extends Factory
{
    protected $model = Settlement::class;

    public function definition(): array
    {
        return [
            'space_id' => Space::factory(),
            'settled_by_user_id' => User::factory(),
            'month' => now()->month,
            'year' => now()->year,
            'balance_amount' => fake()->randomFloat(2, 0, 200),
            'balance_owed_by' => fake()->name(),
        ];
    }

    public function forMonth(int $month, int $year): static
    {
        return $this->state(fn (array $attributes) => [
            'month' => $month,
            'year' => $year,
        ]);
    }
}
