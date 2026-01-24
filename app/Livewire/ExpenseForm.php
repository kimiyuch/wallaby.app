<?php

namespace App\Livewire;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ExpenseForm extends Component
{
    public $amount = '';
    public $description = '';
    public $date;
    public $end_date = '';
    public $is_recurring = false;

    public $editingExpenseId = null;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|max:255',
        'date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:date',
        'is_recurring' => 'boolean',
    ];

    protected $listeners = ['editExpense'];

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
    }

    public function editExpense($expenseId)
    {
        $expense = Expense::findOrFail($expenseId);
        
        if ($expense->user_id !== Auth::id()) {
            return;
        }

        $this->editingExpenseId = $expense->id;
        $this->amount = $expense->amount;
        $this->description = $expense->description;
        $this->date = $expense->date->format('Y-m-d');
        $this->end_date = $expense->end_date?->format('Y-m-d') ?? '';
        $this->is_recurring = $expense->is_recurring;
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        $data = [
            'user_id' => $user->id,
            'amount' => $this->amount,
            'description' => $this->description,
            'date' => $this->date,
            'end_date' => $this->is_recurring && $this->end_date ? $this->end_date : null,
            'is_recurring' => $this->is_recurring,
        ];

        if ($this->editingExpenseId) {
            $expense = Expense::findOrFail($this->editingExpenseId);
            if ($expense->user_id === $user->id) {
                $expense->update($data);
                $this->dispatch('toast', type: 'success', message: 'Expense updated');
            }
        } else {
            Expense::create($data);
            $this->dispatch('toast', type: 'success', message: 'Expense created');
        }

        $this->reset(['amount', 'description', 'end_date', 'is_recurring', 'editingExpenseId']);
        $this->date = now()->format('Y-m-d');
        
        $this->dispatch('expenseUpdated');
    }

    public function cancelEdit()
    {
        $this->reset(['amount', 'description', 'end_date', 'is_recurring', 'editingExpenseId']);
        $this->date = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.expense-form');
    }
}
