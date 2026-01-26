# Livewire Components Review
**Date:** 2026-01-26  
**Reviewer:** Jarvis 🤖  
**Status:** ✅ ALL FIXES APPLIED

---

## Summary

Overall the codebase is solid and follows most Livewire 3 best practices. Found **3 critical issues**, **5 improvements**, and **4 suggestions**.

### Fixes Applied:
- ✅ Added settlement check to `ExpenseForm::save()`
- ✅ Fixed authorization with proper query in `editExpense()`
- ✅ Used `#[Computed]` to cache `Auth::user()` calls
- ✅ Added type hints throughout
- ✅ Created `ExpenseFormData` Form Object
- ✅ Extracted balance calculation to `BalanceCalculator` service
- ✅ Optimized recurring expenses query (filter in DB, not PHP)
- ✅ Added `wire:model.blur` to form inputs
- ✅ Added loading states to buttons and navigation
- ✅ Used `#[Locked]` for `editingExpenseId`
- ✅ Standardized error handling with toast messages

---

## 🔴 Critical Issues

### 1. Missing Authorization Check in `ExpenseForm`
**File:** `app/Livewire/ExpenseForm.php`  
**Line:** 66-68

```php
public function editExpense($expenseId)
{
    $expense = Expense::findOrFail($expenseId);
    if ($expense->user_id !== Auth::id()) {
        return; // Silent fail - no feedback to user
    }
```

**Problem:** When editing, the code checks ownership but silently returns. However, the bigger issue is there's no check if the expense belongs to the user's **space**. A malicious user could potentially guess expense IDs.

**Fix:**
```php
public function editExpense($expenseId)
{
    $expense = Expense::where('id', $expenseId)
        ->where('user_id', Auth::id())
        ->firstOrFail(); // Throws 404 if not found/owned
```

---

### 2. Missing Settlement Check in `ExpenseForm::save()`
**File:** `app/Livewire/ExpenseForm.php`

**Problem:** The `Expenses` component checks if a month is settled before opening the form, but `ExpenseForm::save()` doesn't verify this. A user could manipulate the DOM or make direct requests to create expenses in settled months.

**Fix:** Add settlement check in `save()`:
```php
public function save()
{
    $this->validate();
    
    $user = Auth::user();
    
    // Check if month is settled
    $expenseDate = Carbon::parse($this->date);
    if ($user->space_id && Settlement::isSettled($user->space_id, $expenseDate->month, $expenseDate->year)) {
        $this->dispatch('toast', type: 'error', message: 'This month is settled.');
        return;
    }
    
    // ... rest of save logic
}
```

---

### 3. Duplicate Auth::user() Calls
**File:** `app/Livewire/Expenses.php`

**Problem:** `Auth::user()` is called multiple times in `render()` and throughout the component. This creates unnecessary database queries.

**Fix:** Cache the user in a property:
```php
public function render()
{
    $user = Auth::user();
    // Use $user throughout instead of calling Auth::user() repeatedly
}
```

Or better, use Livewire's `#[Computed]` attribute:
```php
use Livewire\Attributes\Computed;

#[Computed]
public function user()
{
    return Auth::user();
}
```

---

## 🟡 Improvements

### 1. Add Type Hints Throughout
**Files:** `Expenses.php`, `ExpenseForm.php`

Properties lack type hints:
```php
// Current
public $selectedMonth;
public $selectedYear;
public $showExpenseForm = false;

// Better
public int $selectedMonth;
public int $selectedYear;
public bool $showExpenseForm = false;
```

---

### 2. Use Form Objects in ExpenseForm
**File:** `app/Livewire/ExpenseForm.php`

The component has many public properties for form data. Consider using Livewire's Form Objects pattern (like `LoginForm`):

```php
// app/Livewire/Forms/ExpenseFormData.php
class ExpenseFormData extends Form
{
    #[Validate('required|numeric|min:0.01')]
    public string $amount = '';
    
    #[Validate('required|string|max:255')]
    public string $description = '';
    
    // ... etc
}
```

---

### 3. Extract Balance Calculation to Service/Model
**File:** `app/Livewire/Expenses.php`

The `calculateBalance()` method is 30+ lines of business logic. This should live in a dedicated service or on the Space model:

```php
// app/Services/BalanceCalculator.php
class BalanceCalculator
{
    public function calculate(Collection $myExpenses, Collection $partnerExpenses, User $user): array
    {
        // ...
    }
}
```

Or on the Space model:
```php
// In Space.php
public function calculateBalanceForMonth(int $month, int $year): array
{
    // ...
}
```

---

### 4. Recurring Expenses Query is Inefficient
**File:** `app/Livewire/Expenses.php`, Line 145-155

```php
$recurringExpenses = Expense::where('user_id', $userId)
    ->where('is_recurring', true)
    ->get() // Gets ALL recurring expenses
    ->filter(function ($expense) {
        return $expense->isActiveForMonth($this->selectedMonth, $this->selectedYear);
    });
```

**Problem:** Fetches all recurring expenses, then filters in PHP. For users with many recurring expenses, this is slow.

**Fix:** Filter in the query:
```php
$recurringExpenses = Expense::where('user_id', $userId)
    ->where('is_recurring', true)
    ->where('date', '<=', Carbon::create($this->selectedYear, $this->selectedMonth)->endOfMonth())
    ->where(function ($q) {
        $q->whereNull('end_date')
          ->orWhere('end_date', '>=', Carbon::create($this->selectedYear, $this->selectedMonth)->startOfMonth());
    })
    ->get();
```

---

### 5. Use `wire:model.blur` for Better Performance
**File:** `resources/views/livewire/expense-form.blade.php`

```php
// Current - sends request on every keystroke
<x-text-input wire:model="description" ... />

// Better - sends request when field loses focus
<x-text-input wire:model.blur="description" ... />
```

For forms, `.blur` or `.lazy` reduces server requests significantly.

---

## 🟢 Suggestions

### 1. Add Loading States
The components work but don't show loading indicators. Add `wire:loading` states:

```blade
<x-primary-button wire:loading.attr="disabled">
    <span wire:loading.remove>Save</span>
    <span wire:loading>Saving...</span>
</x-primary-button>
```

---

### 2. Consider Pagination for Expenses
If users have many expenses per month, the list could get long. Consider using Livewire's pagination.

---

### 3. Add `#[Locked]` to Sensitive Properties
Livewire 3 has `#[Locked]` to prevent client-side tampering:

```php
use Livewire\Attributes\Locked;

#[Locked]
public int $editingExpenseId;
```

---

### 4. Consistent Error Handling
Some methods dispatch toasts, others silently return. Standardize:

```php
// Bad: silent return
if ($expense->user_id !== Auth::id()) {
    return;
}

// Good: user feedback
if ($expense->user_id !== Auth::id()) {
    $this->dispatch('toast', type: 'error', message: 'Unauthorized');
    return;
}
```

---

## ✅ What's Good

1. **Volt components** for auth pages - clean single-file approach
2. **Event-based communication** between components (`dispatch`, `listeners`)
3. **Proper validation rules** in ExpenseForm
4. **Settlement checks** in Expenses component (though needs to be in ExpenseForm too)
5. **Clean separation** between components
6. **Good use of Blade components** (`x-primary-button`, etc.)

---

## Action Items

| Priority | Item | File |
|----------|------|------|
| 🔴 High | Add settlement check to ExpenseForm::save() | ExpenseForm.php |
| 🔴 High | Fix authorization in editExpense() | ExpenseForm.php |
| 🟡 Medium | Add type hints to all properties | All components |
| 🟡 Medium | Optimize recurring expenses query | Expenses.php |
| 🟡 Medium | Extract balance calculation | Expenses.php |
| 🟢 Low | Add loading states | All views |
| 🟢 Low | Use wire:model.blur in forms | expense-form.blade.php |

---

*Review complete. Questions? Just ask.* 🤖
