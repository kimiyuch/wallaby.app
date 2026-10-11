<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Settlement;
use App\Notifications\MonthSettledNotification;
use App\Services\BalanceCalculator;
use Illuminate\Database\UniqueConstraintViolationException;
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

    public bool $editing = false;

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
    public function hasExpenses(): bool
    {
        return $this->user->expenses()->exists();
    }

    /**
     * A month can be settled once it has started, by a space of two.
     */
    #[Computed]
    public function canSettle(): bool
    {
        return $this->partner !== null
            && $this->selectedMonthStart()->lte(now());
    }

    #[Computed]
    public function isSettled(): bool
    {
        if (! $this->user->space_id) {
            return false;
        }

        return Settlement::isSettled($this->user->space_id, $this->selectedMonth, $this->selectedYear);
    }

    /**
     * The first day of the selected month. Built from scratch rather than from now(),
     * which would overflow into the next month on the 29th to 31st.
     */
    protected function selectedMonthStart(): Carbon
    {
        return Carbon::create($this->selectedYear, $this->selectedMonth, 1);
    }

    public function previousMonth(): void
    {
        $this->goToMonth($this->selectedMonthStart()->subMonth());
    }

    public function nextMonth(): void
    {
        $this->goToMonth($this->selectedMonthStart()->addMonth());
    }

    protected function goToMonth(Carbon $date): void
    {
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

        // Start from an empty form, even if the panel was last closed while editing.
        $this->dispatch('resetExpenseForm')->to(ExpenseForm::class);
        $this->editing = false;
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

        if (! $expense) {
            $this->dispatch('toast', type: 'error', message: 'Expense not found.');

            return;
        }

        $this->dispatch('editExpense', expenseId: $id);
        $this->editing = true;
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

        if (! $expense) {
            $this->dispatch('toast', type: 'error', message: 'Expense not found.');

            return;
        }

        $from = $this->selectedMonthStart();

        if ($expense->coversSettledMonth($this->user->space_id, $from)) {
            $this->dispatch('toast', type: 'error', message: 'A later month with this expense is already settled.');

            return;
        }

        // A recurring expense that started earlier stops after last month, so the months
        // before stay as they were. Deleting it outright would rewrite them.
        if ($expense->is_recurring && $expense->date->lt($from)) {
            $expense->update(['end_date' => $from->copy()->subDay()]);
            $this->dispatch('toast', type: 'success', message: "Recurring expense stopped from {$from->format('F')} on");

            return;
        }

        $expense->delete();
        $this->dispatch('toast', type: 'success', message: 'Expense deleted');
    }

    public function settleMonth(): void
    {
        if (! $this->user->space_id) {
            $this->dispatch('toast', type: 'error', message: 'No space found.');

            return;
        }

        if ($this->isSettled) {
            $this->dispatch('toast', type: 'error', message: 'This month is already settled.');

            return;
        }

        if (! $this->canSettle) {
            $this->dispatch('toast', type: 'error', message: 'This month can\'t be settled yet.');

            return;
        }

        // Calculate final balance
        $myExpenses = $this->getExpensesForMonth($this->user->id);
        $partnerExpenses = $this->partner ? $this->getExpensesForMonth($this->partner->id) : collect();

        $calculator = new BalanceCalculator;
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

        // Create settlement. The unique index catches the partner settling at the same moment.
        try {
            $settlement = Settlement::create([
                'space_id' => $this->user->space_id,
                'settled_by_user_id' => $this->user->id,
                'month' => $this->selectedMonth,
                'year' => $this->selectedYear,
                'balance_amount' => $balanceAmount,
                'balance_owed_by' => $balanceOwedBy,
            ]);
        } catch (UniqueConstraintViolationException) {
            unset($this->isSettled);
            $this->dispatch('toast', type: 'error', message: 'This month is already settled.');

            return;
        }

        // Notify both users
        $this->user->notify(new MonthSettledNotification($settlement, $this->user->name));

        if ($this->partner && ! $this->partner->hasDeparted()) {
            $this->partner->notify(new MonthSettledNotification($settlement, $this->user->name));
        }

        unset($this->isSettled); // Clear computed cache
        $this->dispatch('toast', type: 'success', message: 'Month settled! Emails sent.');
    }

    protected function getExpensesForMonth(int $userId): Collection
    {
        return Expense::forUserInMonth($userId, $this->selectedMonthStart());
    }

    public function render()
    {
        $myExpenses = $this->getExpensesForMonth($this->user->id);
        $partnerExpenses = $this->partner ? $this->getExpensesForMonth($this->partner->id) : collect();

        $myTotal = $myExpenses->sum('amount');
        $partnerTotal = $partnerExpenses->sum('amount');

        $calculator = new BalanceCalculator;

        return view('livewire.expenses', [
            'myExpenses' => $myExpenses,
            'partnerExpenses' => $partnerExpenses,
            'balance' => $calculator->calculate($myExpenses, $partnerExpenses, $this->user, $this->partner),
            'myTotal' => $myTotal,
            'partnerTotal' => $partnerTotal,
            'monthName' => $this->selectedMonthStart()->format('F'),
            'monthStart' => $this->selectedMonthStart(),
        ]);
    }
}
