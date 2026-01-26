<?php

namespace App\Console\Commands;

use App\Models\Expense;
use Illuminate\Console\Command;

class ListExpenses extends Command
{
    protected $signature = 'app:expenses {--user= : Filter by user ID} {--limit=50 : Number of entries to show}';

    protected $description = 'List all expenses';

    public function handle(): void
    {
        $query = Expense::with('user')->latest('date');

        if ($userId = $this->option('user')) {
            $query->where('user_id', $userId);
        }

        $expenses = $query->limit($this->option('limit'))->get();

        if ($expenses->isEmpty()) {
            $this->info('No expenses found.');

            return;
        }

        $rows = $expenses->map(fn ($expense) => [
            'id' => $expense->id,
            'user' => $expense->user->name,
            'description' => mb_strimwidth($expense->description, 0, 25, '...'),
            'amount' => number_format($expense->amount, 2),
            'date' => $expense->date->format('Y-m-d'),
            'recurring' => $expense->is_recurring ? '✓' : '',
            'personal' => $expense->is_personal ? '✓' : '',
        ]);

        $this->table(['ID', 'User', 'Description', 'Amount', 'Date', 'Recurring', 'Personal'], $rows);
        $this->newLine();

        $total = $expenses->sum('amount');
        $this->info("Showing: {$expenses->count()} expense(s) | Total: ".number_format($total, 2));
    }
}
