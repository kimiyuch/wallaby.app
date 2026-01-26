<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Settlement;
use App\Notifications\MonthSettledNotification;
use App\Services\BalanceCalculator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class Expenses extends Component
{
    #[Url(as: 'period', history: true)]
    public string $period = '';
    
    public int $selectedMonth;
    public int $selectedYear;
    public bool $showExpenseForm = false;

    protected $listeners = ['expenseUpdated' => 'onExpenseUpdated', 'closeExpenseForm', 'openExpenseForm'];

    public function mount(): void
    {
        if ($this->period && preg_match('/^(\d{4})-(\d{2})$/', $this->period, $matches)) {
            $this->selectedYear = (int) $matches[1];
            $this->selectedMonth = (int) $matches[2];
        } else {
            $this->selectedMonth = now()->month;
            $this->selectedYear = now()->year;
            $this->period = $this->formatPeriod();
        }
    }
    
    protected function formatPeriod(): string
    {
        return sprintf('%d-%02d', $this->selectedYear, $this->selectedMonth);
    }
    
    protected function updatePeriod(): void
    {
        $this->period = $this->formatPeriod();
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function partner()
    {
        return $this->user->partner;
    }

    #[Computed]
    public function isSettled(): bool
    {
        if (!$this->user->space_id) {
            return false;
        }

        return Settlement::isSettled($this->user->space_id, $this->selectedMonth, $this->selectedYear);
    }

    public function previousMonth(): void
    {
        $date = now()->setYear($this->selectedYear)->setMonth($this->selectedMonth)->subMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        $this->updatePeriod();
        $this->dispatch('monthChanged', month: $this->selectedMonth, year: $this->selectedYear);
        unset($this->isSettled); // Clear computed cache
    }

    public function nextMonth(): void
    {
        $date = now()->setYear($this->selectedYear)->setMonth($this->selectedMonth)->addMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        $this->updatePeriod();
        $this->dispatch('monthChanged', month: $this->selectedMonth, year: $this->selectedYear);
        unset($this->isSettled); // Clear computed cache
    }

    public function openExpenseForm(): void
    {
        if ($this->isSettled) {
            $this->dispatch('toast', type: 'error', message: 'This month is settled. No changes allowed.');
            return;
        }
        
        $this->showExpenseForm = true;
    }

    public function closeExpenseForm(): void
    {
        $this->showExpenseForm = false;
    }

    public function onExpenseUpdated(): void
    {
        $this->showExpenseForm = false;
    }

    public function editExpense(int $id): void
    {
        if ($this->isSettled) {
            $this->dispatch('toast', type: 'error', message: 'This month is settled. No changes allowed.');
            return;
        }
        
        // Verify expense exists and belongs to current user
        $expense = Expense::where('id', $id)
            ->where('user_id', $this->user->id)
            ->first();
            
        if (!$expense) {
            $this->dispatch('toast', type: 'error', message: 'Expense not found.');
            return;
        }
        
        $this->dispatch('editExpense', expenseId: $id);
        $this->showExpenseForm = true;
    }

    public function deleteExpense(int $id): void
    {
        if ($this->isSettled) {
            $this->dispatch('toast', type: 'error', message: 'This month is settled. No changes allowed.');
            return;
        }
        
        $expense = Expense::where('id', $id)
            ->where('user_id', $this->user->id)
            ->first();

        if (!$expense) {
            $this->dispatch('toast', type: 'error', message: 'Expense not found.');
            return;
        }

        $expense->delete();
        $this->dispatch('toast', type: 'success', message: 'Expense deleted');
    }

    public function settleMonth(): void
    {
        if (!$this->user->space_id) {
            $this->dispatch('toast', type: 'error', message: 'No space found.');
            return;
        }

        if ($this->isSettled) {
            $this->dispatch('toast', type: 'error', message: 'This month is already settled.');
            return;
        }

        // Calculate final balance
        $myExpenses = $this->getExpensesForMonth($this->user->id);
        $partnerExpenses = $this->partner ? $this->getExpensesForMonth($this->partner->id) : collect();
        
        $calculator = new BalanceCalculator();
        $balance = $calculator->calculate($myExpenses, $partnerExpenses, $this->user, $this->partner);

        // Determine who owes whom
        $balanceAmount = 0;
        $balanceOwedBy = null;
        
        if (isset($balance[$this->user->id]) && $balance[$this->user->id] > 0) {
            $balanceAmount = $balance[$this->user->id];
            $balanceOwedBy = $this->user->name;
        } elseif ($this->partner && isset($balance[$this->partner->id]) && $balance[$this->partner->id] > 0) {
            $balanceAmount = $balance[$this->partner->id];
            $balanceOwedBy = $this->partner->name;
        }

        // Create settlement
        $settlement = Settlement::create([
            'space_id' => $this->user->space_id,
            'settled_by_user_id' => $this->user->id,
            'month' => $this->selectedMonth,
            'year' => $this->selectedYear,
            'balance_amount' => $balanceAmount,
            'balance_owed_by' => $balanceOwedBy,
        ]);

        // Notify both users
        $this->user->notify(new MonthSettledNotification($settlement, $this->user->name));
        
        if ($this->partner) {
            $this->partner->notify(new MonthSettledNotification($settlement, $this->user->name));
        }

        unset($this->isSettled); // Clear computed cache
        $this->dispatch('toast', type: 'success', message: 'Month settled! Emails sent.');
    }

    protected function getExpensesForMonth(int $userId): Collection
    {
        $monthStart = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Get one-time expenses for this month
        $oneTimeExpenses = Expense::where('user_id', $userId)
            ->where('is_recurring', false)
            ->whereMonth('date', $this->selectedMonth)
            ->whereYear('date', $this->selectedYear)
            ->get();

        // Get recurring expenses active for this month (optimized query)
        $recurringExpenses = Expense::where('user_id', $userId)
            ->where('is_recurring', true)
            ->where('date', '<=', $monthEnd)
            ->where(function ($query) use ($monthStart) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $monthStart);
            })
            ->get();

        // Combine and sort (non-recurring first, then recurring)
        return $oneTimeExpenses->concat($recurringExpenses)->sortBy('is_recurring')->values();
    }

    public function render()
    {
        $myExpenses = $this->getExpensesForMonth($this->user->id);
        $partnerExpenses = $this->partner ? $this->getExpensesForMonth($this->partner->id) : collect();

        $myTotal = $myExpenses->sum('amount');
        $partnerTotal = $partnerExpenses->sum('amount');

        $calculator = new BalanceCalculator();

        return view('livewire.expenses', [
            'myExpenses' => $myExpenses,
            'partnerExpenses' => $partnerExpenses,
            'balance' => $calculator->calculate($myExpenses, $partnerExpenses, $this->user, $this->partner),
            'myTotal' => $myTotal,
            'partnerTotal' => $partnerTotal,
            'monthName' => now()->setMonth($this->selectedMonth)->format('F'),
        ]);
    }
}
