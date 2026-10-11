<?php

namespace App\Livewire;

use App\Livewire\Forms\ExpenseFormData;
use App\Models\Expense;
use App\Models\Settlement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ExpenseForm extends Component
{
    public ExpenseFormData $form;

    #[Locked]
    public ?int $editingExpenseId = null;

    public int $selectedMonth;

    public int $selectedYear;

    protected $listeners = ['editExpense', 'monthChanged', 'resetExpenseForm' => 'cancelEdit'];

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    public function mount(?int $selectedMonth = null, ?int $selectedYear = null): void
    {
        $this->selectedMonth = $selectedMonth ?? now()->month;
        $this->selectedYear = $selectedYear ?? now()->year;
        $this->form->setDate($this->getDefaultDate());
    }

    protected function getDefaultDate(): string
    {
        $now = now();
        $isCurrentMonth = $this->selectedMonth == $now->month && $this->selectedYear == $now->year;

        if ($isCurrentMonth) {
            return $now->format('Y-m-d');
        }

        return Carbon::create($this->selectedYear, $this->selectedMonth, 1)->format('Y-m-d');
    }

    public function monthChanged(int $month, int $year): void
    {
        $this->selectedMonth = $month;
        $this->selectedYear = $year;

        // Only update date if not editing an expense
        if (! $this->editingExpenseId) {
            $this->form->setDate($this->getDefaultDate());
        }
    }

    public function editExpense(int $expenseId): void
    {
        // Secure query: only fetch if owned by current user
        $expense = Expense::where('id', $expenseId)
            ->where('user_id', $this->user->id)
            ->first();

        if (! $expense) {
            $this->dispatch('toast', type: 'error', message: 'Expense not found.');

            return;
        }

        // Check if the expense's month is settled
        if ($this->isMonthSettled($expense->date->month, $expense->date->year)) {
            $this->dispatch('toast', type: 'error', message: 'This month is settled. No changes allowed.');

            return;
        }

        $this->editingExpenseId = $expense->id;
        $this->form->fillFromExpense($expense);
    }

    protected function isMonthSettled(int $month, int $year): bool
    {
        return $this->user->space_id
            && Settlement::isSettled($this->user->space_id, $month, $year);
    }

    public function save(): void
    {
        $this->form->validate();

        // Check if the expense's month is settled
        $expenseDate = Carbon::parse($this->form->date);
        if ($this->isMonthSettled($expenseDate->month, $expenseDate->year)) {
            $this->dispatch('toast', type: 'error', message: 'This month is settled. No changes allowed.');

            return;
        }

        $data = array_merge(
            ['user_id' => $this->user->id],
            $this->form->toArray()
        );

        if ($this->editingExpenseId) {
            // Double-check ownership when updating
            $expense = Expense::where('id', $this->editingExpenseId)
                ->where('user_id', $this->user->id)
                ->first();

            if (! $expense) {
                $this->dispatch('toast', type: 'error', message: 'Expense not found.');

                return;
            }

            $expense->update($data);
            $this->dispatch('toast', type: 'success', message: 'Expense updated');
        } else {
            Expense::create($data);
            $this->dispatch('toast', type: 'success', message: 'Expense created');
        }

        $this->editingExpenseId = null;
        $this->form->resetForm($this->getDefaultDate());
        $this->dispatch('expenseUpdated');
    }

    public function cancelEdit(): void
    {
        $this->editingExpenseId = null;
        $this->form->resetForm($this->getDefaultDate());
    }

    public function render()
    {
        return view('livewire.expense-form');
    }
}
