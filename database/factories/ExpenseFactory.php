<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 5, 500),
            'description' => fake()->sentence(3),
            'date' => fake()->dateTimeBetween('-1 month', 'now'),
            'is_recurring' => false,
            'is_personal' => false,
        ];
    }

    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
        ]);
    }

    public function personal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_personal' => true,
        ]);
    }

    public function forMonth(int $month, int $year): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now()->setYear($year)->setMonth($month)->setDay(15),
        ]);
    }
}
