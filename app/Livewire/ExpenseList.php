<?php

namespace App\Livewire;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseList extends Component
{
    use WithPagination;

    public $selectedMonth;
    public $selectedYear;

    protected $listeners = ['expenseUpdated' => '$refresh'];

    public function mount($selectedMonth = null, $selectedYear = null)
    {
        $this->selectedMonth = $selectedMonth ?? now()->month;
        $this->selectedYear = $selectedYear ?? now()->year;
    }

    public function deleteExpense($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        
        if ($expense->user_id === Auth::id()) {
            $expense->delete();
            session()->flash('message', 'Expense deleted!');
        }
    }

    public function editExpense($expenseId)
    {
        $this->dispatch('editExpense', expenseId: $expenseId);
        $this->dispatch('openExpenseForm')->to('dashboard');
    }

    public function render()
    {
        $user = Auth::user();
        $expenses = collect();

        if ($user->household_id) {
            $expenses = Expense::where('household_id', $user->household_id)
                ->whereMonth('date', $this->selectedMonth)
                ->whereYear('date', $this->selectedYear)
                ->with('user')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return view('livewire.expense-list', [
            'expenses' => $expenses,
            'currentUserId' => $user->id,
        ]);
    }
}
