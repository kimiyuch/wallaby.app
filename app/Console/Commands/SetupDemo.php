<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupDemo extends Command
{
    protected $signature = 'app:setup-demo';
    protected $description = 'Set up demo users and expenses';

    private array $expenseDescriptions = [
        'School supplies',
        'Soccer practice',
        'Swimming lessons',
        'Winter jacket',
        'New shoes',
        'Birthday party',
        'School trip',
        'Piano lessons',
        'Dentist visit',
        'Doctor checkup',
        'Bus tickets',
        'Lunch money',
        'Art supplies',
        'Books',
        'Haircut',
        'Cinema tickets',
        'Zoo visit',
        'Sports equipment',
        'School photos',
        'Vitamins',
    ];

    public function handle(): void
    {
        $this->info('Setting up demo data...');

        // Clear existing data
        Expense::truncate();
        User::truncate();

        // Create users
        $peter = User::create([
            'name' => 'Peter',
            'email' => 'peter@demo.com',
            'password' => Hash::make('password'),
        ]);

        $maria = User::create([
            'name' => 'Maria',
            'email' => 'maria@demo.com',
            'password' => Hash::make('password'),
        ]);

        $this->info('Created users: Peter and Maria');

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
    }
}
