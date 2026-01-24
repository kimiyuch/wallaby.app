<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
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
    }

    public function nextMonth()
    {
        $date = now()->setYear($this->selectedYear)->setMonth($this->selectedMonth)->addMonth();
        $this->selectedMonth = $date->month;
        $this->selectedYear = $date->year;
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

    protected function calculateBalance($myTotal, $partnerTotal): array
    {
        $user = Auth::user();
        $partner = $user->partner;
        
        if (!$partner) {
            return [];
        }

        $total = $myTotal + $partnerTotal;
        $fairShare = $total / 2;

        $userOwes = $fairShare - $myTotal;
        $partnerOwes = $fairShare - $partnerTotal;

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

        return view('livewire.dashboard', [
            'myExpenses' => $myExpenses,
            'partnerExpenses' => $partnerExpenses,
            'balance' => $this->calculateBalance($myTotal, $partnerTotal),
            'partner' => $partner,
            'myTotal' => $myTotal,
            'partnerTotal' => $partnerTotal,
            'monthName' => now()->setMonth($this->selectedMonth)->format('F'),
        ]);
    }
}
