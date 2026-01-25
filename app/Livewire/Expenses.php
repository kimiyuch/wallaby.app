<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Expenses extends Component
{
    public $selectedMonth;
    public $selectedYear;
    public $showExpenseForm = false;

    protected $listeners = ['expenseUpdated' => 'onExpenseUpdated', 'closeExpenseForm', 'openExpenseForm'];

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    public function previousMonth()
    {
        $date = now()->setYear($this->selectedYear)->setMonth($this->selectedMonth)->subMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        $this->dispatch('monthChanged', month: $this->selectedMonth, year: $this->selectedYear);
    }

    public function nextMonth()
    {
        $date = now()->setYear($this->selectedYear)->setMonth($this->selectedMonth)->addMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
        $this->dispatch('monthChanged', month: $this->selectedMonth, year: $this->selectedYear);
    }

    public function openExpenseForm()
    {
        $this->showExpenseForm = true;
    }

    public function closeExpenseForm()
    {
        $this->showExpenseForm = false;
    }

    public function onExpenseUpdated()
    {
        $this->showExpenseForm = false;
    }

    public function editExpense($id)
    {
        $this->dispatch('editExpense', expenseId: $id);
        $this->showExpenseForm = true;
    }

    public function deleteExpense($id)
    {
        $expense = Expense::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($expense) {
            $expense->delete();
            $this->dispatch('toast', type: 'success', message: 'Expense deleted');
        }
    }

    protected function getExpensesForMonth($userId)
    {
        // Get one-time expenses for this month
        $oneTimeExpenses = Expense::where('user_id', $userId)
            ->where('is_recurring', false)
            ->whereMonth('date', $this->selectedMonth)
            ->whereYear('date', $this->selectedYear)
            ->get();

        // Get recurring expenses that are active for this month
        $recurringExpenses = Expense::where('user_id', $userId)
            ->where('is_recurring', true)
            ->get()
            ->filter(function ($expense) {
                return $expense->isActiveForMonth($this->selectedMonth, $this->selectedYear);
            });

        // Combine and sort (non-recurring first, then recurring)
        return $oneTimeExpenses->concat($recurringExpenses)->sortBy('is_recurring')->values();
    }

    protected function calculateBalance($myExpenses, $partnerExpenses): array
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            return [];
        }

        // Separate shared and personal expenses
        $mySharedExpenses = $myExpenses->where('is_personal', false)->sum('amount');
        $partnerSharedExpenses = $partnerExpenses->where('is_personal', false)->sum('amount');

        // Calculate shared expense split (50/50)
        $totalShared = $mySharedExpenses + $partnerSharedExpenses;
        $fairShare = $totalShared / 2;

        // Base balance from shared expenses
        $userOwes = $fairShare - $mySharedExpenses;
        $partnerOwes = $fairShare - $partnerSharedExpenses;

        // Add personal expenses (full amount owed by partner)
        $myPersonalExpenses = $myExpenses->where('is_personal', true)->sum('amount');
        $partnerPersonalExpenses = $partnerExpenses->where('is_personal', true)->sum('amount');

        // If I paid personal expenses, partner owes me more
        $userOwes -= $myPersonalExpenses;
        $partnerOwes += $myPersonalExpenses;

        // If partner paid personal expenses, I owe partner more
        $userOwes += $partnerPersonalExpenses;
        $partnerOwes -= $partnerPersonalExpenses;

        return [
            $user->id => round($userOwes, 2),
            $partner->id => round($partnerOwes, 2),
        ];
    }

    public function render()
    {
        $user = Auth::user();
        $partner = $user->partner;

        $myExpenses = $this->getExpensesForMonth($user->id);
        $partnerExpenses = $partner ? $this->getExpensesForMonth($partner->id) : collect();

        $myTotal = $myExpenses->sum('amount');
        $partnerTotal = $partnerExpenses->sum('amount');

        return view('livewire.expenses', [
            'myExpenses' => $myExpenses,
            'partnerExpenses' => $partnerExpenses,
            'balance' => $this->calculateBalance($myExpenses, $partnerExpenses),
            'partner' => $partner,
            'myTotal' => $myTotal,
            'partnerTotal' => $partnerTotal,
            'monthName' => now()->setMonth($this->selectedMonth)->format('F'),
        ]);
    }
}
