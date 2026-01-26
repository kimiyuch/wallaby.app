<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ExpenseFormData extends Form
{
    #[Validate('required|numeric|min:0.01')]
    public string $amount = '';

    #[Validate('required|string|max:255')]
    public string $description = '';

    #[Validate('required|date')]
    public string $date = '';

    #[Validate('nullable|date|after_or_equal:date')]
    public ?string $end_date = '';

    #[Validate('boolean')]
    public bool $is_recurring = false;

    #[Validate('boolean')]
    public bool $is_personal = false;

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function resetForm(string $defaultDate): void
    {
        $this->reset(['amount', 'description', 'end_date', 'is_recurring', 'is_personal']);
        $this->date = $defaultDate;
    }

    public function fillFromExpense(object $expense): void
    {
        $this->amount = (string) $expense->amount;
        $this->description = $expense->description;
        $this->date = $expense->date->format('Y-m-d');
        $this->end_date = $expense->end_date ? $expense->end_date->format('Y-m-d') : '';
        $this->is_recurring = $expense->is_recurring;
        $this->is_personal = $expense->is_personal;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'description' => $this->description,
            'date' => $this->date,
            'end_date' => $this->is_recurring && $this->end_date ? $this->end_date : null,
            'is_recurring' => $this->is_recurring,
            'is_personal' => $this->is_personal,
        ];
    }
}
