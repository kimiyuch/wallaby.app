<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\Space;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupDemo extends Command
{
    protected $signature = 'app:setup-demo';

    protected $description = 'Set up demo users and expenses';

    private array $expenseDescriptions = [
        'Groceries',
        'Dinner out',
        'Electricity bill',
        'Internet',
        'Cleaning supplies',
        'Takeaway pizza',
        'Cinema tickets',
        'Concert tickets',
        'Weekend trip',
        'Train tickets',
        'Furniture',
        'Plants',
        'Pharmacy',
        'Birthday gift',
        'Coffee beans',
        'Toilet paper',
        'Drinks for the party',
        'Laundry detergent',
        'Kitchen tools',
        'Brunch',
    ];

    public function handle(): void
    {
        $this->info('Setting up demo data...');

        // Clear existing data
        Expense::truncate();
        User::truncate();
        Space::truncate();

        // Create shared space
        $space = Space::create([
            'name' => 'Demo Household',
            'invite_code' => Space::generateInviteCode(),
        ]);

        $this->info("Created space with invite code: {$space->invite_code}");

        // Create users in the same space
        $peter = User::create([
            'name' => 'Peter',
            'email' => 'peter@demo.com',
            'password' => Hash::make('password'),
            'space_id' => $space->id,
        ]);
        $peter->email_verified_at = now();
        $peter->save();

        $maria = User::create([
            'name' => 'Maria',
            'email' => 'maria@demo.com',
            'password' => Hash::make('password'),
            'space_id' => $space->id,
        ]);
        $maria->email_verified_at = now();
        $maria->save();

        $this->info('Created users: Peter and Maria (verified, sharing space)');

        // Create recurring expenses
        Expense::create([
            'user_id' => $peter->id,
            'amount' => 29.00,
            'description' => 'Mobile Subscription',
            'date' => now()->subMonths(6)->startOfMonth(),
            'is_recurring' => true,
            'is_personal' => false,
            'end_date' => null,
        ]);

        Expense::create([
            'user_id' => $maria->id,
            'amount' => 15.90,
            'description' => 'Netflix',
            'date' => now()->subMonths(6)->startOfMonth(),
            'is_recurring' => true,
            'is_personal' => false,
            'end_date' => null,
        ]);

        $this->info('Created recurring expenses (Mobile Subscription, Netflix)');

        // Create random expenses for the last 6 months
        $users = [$peter, $maria];

        for ($monthsAgo = 5; $monthsAgo >= 0; $monthsAgo--) {
            $monthDate = now()->subMonths($monthsAgo);

            foreach ($users as $user) {
                $expenseCount = rand(5, 10);

                for ($i = 0; $i < $expenseCount; $i++) {
                    $day = rand(1, $monthDate->daysInMonth);
                    $date = $monthDate->copy()->setDay($day);

                    // Don't create expenses in the future
                    if ($date->isFuture()) {
                        $date = now();
                    }

                    $isPersonal = rand(1, 10) <= 2; // 20% chance of personal expense

                    Expense::create([
                        'user_id' => $user->id,
                        'amount' => rand(10, 150) + (rand(0, 1) ? 0.50 : 0),
                        'description' => $this->expenseDescriptions[array_rand($this->expenseDescriptions)],
                        'date' => $date,
                        'is_recurring' => false,
                        'is_personal' => $isPersonal,
                    ]);
                }
            }

            $this->info("Created expenses for {$monthDate->format('F Y')}");
        }

        $this->newLine();
        $this->info('✓ Demo setup complete!');
        $this->newLine();
        $this->line('Login credentials:');
        $this->line('  Peter: peter@demo.com / password');
        $this->line('  Maria: maria@demo.com / password');
        $this->newLine();
        $this->line("Invite code: {$space->invite_code}");
    }
}
