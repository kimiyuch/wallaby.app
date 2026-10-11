<?php

namespace App\Livewire;

use App\Livewire\Forms\ExpenseFormData;
use App\Models\Expense;
use App\Models\Settlement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Changes to a recurring expense apply from the viewed month on, so that's the
        // month that must still be open; for a one-off it's the month it's in.
        $month = $expense->is_recurring ? $this->selectedMonthStart() : $expense->date;

        if ($this->isMonthSettled($month->month, $month->year)) {
            $this->dispatch('toast', type: 'error', message: 'This month is settled. No changes allowed.');

            return;
        }

        $this->editingExpenseId = $expense->id;
        $this->form->fillFromExpense($expense);

        if ($this->splitsAtSelectedMonth($expense)) {
            $this->form->setDate($this->selectedMonthStart()->format('Y-m-d'));
        }
    }

    /**
     * The month from which an edit applies, when it doesn't change the expense as a whole
     */
    #[Computed]
    public function appliesFrom(): ?string
    {
        $expense = $this->editingExpenseId ? Expense::find($this->editingExpenseId) : null;

        return $expense && $this->splitsAtSelectedMonth($expense)
            ? $this->selectedMonthStart()->format('F Y')
            : null;
    }

    protected function selectedMonthStart(): Carbon
    {
        return Carbon::create($this->selectedYear, $this->selectedMonth, 1);
    }

    /**
     * A recurring expense that started before the viewed month is changed from that month on:
     * the original ends the month before, and the changed copy starts in the viewed month.
     */
    protected function splitsAtSelectedMonth(Expense $expense): bool
    {
        return $expense->is_recurring && $expense->date->lt($this->selectedMonthStart());
    }

    protected function isUnchanged(Expense $expense, Expense $changed): bool
    {
        return $changed->date->equalTo($this->selectedMonthStart())
            && $changed->description === $expense->description
            && (float) $changed->amount === (float) $expense->amount
            && $changed->is_recurring === $expense->is_recurring
            && $changed->is_personal === $expense->is_personal
            && $changed->end_date?->toDateString() === $expense->end_date?->toDateString();
    }

    protected function isMonthSettled(int $month, int $year): bool
    {
        return $this->user->space_id
            && Settlement::isSettled($this->user->space_id, $month, $year);
    }

    public function save(): void
    {
        $this->form->validate();

        $expense = null;

        if ($this->editingExpenseId) {
            // Double-check ownership when updating
            $expense = Expense::where('id', $this->editingExpenseId)
                ->where('user_id', $this->user->id)
                ->first();

            if (! $expense) {
                $this->dispatch('toast', type: 'error', message: 'Expense not found.');

                return;
            }
        }

        $data = array_merge(
            ['user_id' => $this->user->id],
            $this->form->toArray()
        );
        $changed = new Expense($data);
        $from = $this->selectedMonthStart();
        $split = $expense && $this->splitsAtSelectedMonth($expense);

        if ($split && $changed->date->lt($from)) {
            $this->addError('form.date', "Changes apply from {$from->format('F Y')} on. Pick a date in or after it.");

            return;
        }

        if ($split && $this->isUnchanged($expense, $changed)) {
            $this->finish();

            return;
        }

        // Settled months stay as they are: neither the new version nor the part of the old
        // one that changes may cover one.
        $spaceId = (int) $this->user->space_id;

        if ($changed->coversSettledMonth($spaceId) || $expense?->coversSettledMonth($spaceId, $split ? $from : null)) {
            $this->dispatch('toast', type: 'error', message: 'This would change a month that is already settled.');

            return;
        }

        if ($split) {
            DB::transaction(function () use ($expense, $data, $from) {
                $expense->update(['end_date' => $from->copy()->subDay()]);
                Expense::create($data);
            });
            $this->dispatch('toast', type: 'success', message: "Expense updated from {$from->format('F')} on");
        } elseif ($expense) {
            $expense->update($data);
            $this->dispatch('toast', type: 'success', message: 'Expense updated');
        } else {
            Expense::create($data);
            $this->dispatch('toast', type: 'success', message: 'Expense created');
        }

        $this->finish();
    }

    protected function finish(): void
    {
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
