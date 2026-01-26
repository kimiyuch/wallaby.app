# Performance Review - kimiyu.ch

**Date:** 2026-01-26  
**Reviewer:** Jarvis  
**Stack:** Laravel 11 + Livewire 3 + SQLite

---

## Executive Summary

The codebase is clean and well-structured for a small app. However, there are several opportunities to optimize database queries and add caching before scaling. Current state is fine for ~100 users but will need attention for growth.

---

## 🔴 Critical Issues

### 1. N+1 Query: Partner Lookup
**File:** `app/Models/User.php:47-55`

```php
public function getPartnerAttribute(): ?User
{
    if (!$this->space_id) {
        return null;
    }
    return User::where('space_id', $this->space_id)
        ->where('id', '!=', $this->id)
        ->first();
}
```

**Problem:** Every access to `$user->partner` triggers a new database query. In `Expenses.php`, this is accessed multiple times per request.

**Fix:** Cache the result or eager load:
```php
public function getPartnerAttribute(): ?User
{
    return once(fn () => $this->space_id 
        ? User::where('space_id', $this->space_id)
            ->where('id', '!=', $this->id)
            ->first()
        : null
    );
}
```

---

### 2. Duplicate Expense Queries
**File:** `app/Livewire/Expenses.php`

`getExpensesForMonth()` is called:
1. Once for `$myExpenses` in `render()`
2. Once for `$partnerExpenses` in `render()`
3. Again in `settleMonth()` for both users

**Problem:** 4-6 queries for the same data per render + settlement.

**Fix:** Memoize or use computed properties:
```php
#[Computed]
public function myExpenses(): Collection
{
    return $this->getExpensesForMonth($this->user->id);
}

#[Computed]
public function partnerExpenses(): Collection
{
    return $this->partner 
        ? $this->getExpensesForMonth($this->partner->id) 
        : collect();
}
```

---

## 🟡 Medium Priority

### 3. Missing Database Indexes

**settlements table:**
```php
// Current: no composite index
// Recommended:
$table->unique(['space_id', 'month', 'year']);
```

**expenses table:**
```php
// Add for recurring expense queries:
$table->index(['user_id', 'is_recurring', 'date']);
```

### 4. Settlement Status Check
**File:** `app/Models/Settlement.php:38-43`

```php
public static function isSettled(int $spaceId, int $month, int $year): bool
{
    return static::where('space_id', $spaceId)
        ->where('month', $month)
        ->where('year', $year)
        ->exists();
}
```

**Problem:** Called multiple times per request. No caching.

**Fix:** Add simple cache (1 hour TTL):
```php
public static function isSettled(int $spaceId, int $month, int $year): bool
{
    return cache()->remember(
        "settlement:{$spaceId}:{$year}-{$month}",
        3600,
        fn () => static::where('space_id', $spaceId)
            ->where('month', $month)
            ->where('year', $year)
            ->exists()
    );
}
```

Remember to invalidate when settling:
```php
cache()->forget("settlement:{$spaceId}:{$year}-{$month}");
```

### 5. Recurring Expense Logic in PHP
**File:** `app/Livewire/Expenses.php:166-177`

Two separate queries + PHP merge for expenses. Could be optimized with a single query using UNION or conditional logic.

---

## 🟢 Good Practices Found

### Security ✅
- Expense ownership verified before edit/delete
- Rate limiting on login attempts
- CSRF protection via Livewire
- Locked properties for sensitive IDs (`#[Locked]`)
- Input validation with Livewire Form objects

### Code Quality ✅
- Clean separation: `BalanceCalculator` service
- Proper use of computed properties
- Form validation in dedicated class
- Consistent authorization checks

### Frontend ✅
- Assets bundled via Vite
- Tailwind CSS purged
- Alpine.js for lightweight interactivity
- Proper loading states

---

## 📊 Query Analysis

**Typical page load (Expenses page with partner):**

| Query | Count | Source |
|-------|-------|--------|
| Auth user | 1 | Middleware |
| Partner lookup | 1-3 | `$user->partner` |
| Settlement check | 1-2 | `isSettled` |
| My expenses (one-time) | 1 | `getExpensesForMonth` |
| My expenses (recurring) | 1 | `getExpensesForMonth` |
| Partner expenses (one-time) | 1 | `getExpensesForMonth` |
| Partner expenses (recurring) | 1 | `getExpensesForMonth` |
| **Total** | **7-10** | |

**Target after optimization:** 3-4 queries

---

## 🛠 Recommended Actions

### Phase 1: Quick Wins (1-2 hours)
1. Add `once()` wrapper to partner attribute
2. Convert expense queries to computed properties
3. Add composite index on settlements

### Phase 2: Caching (2-3 hours)
1. Cache settlement status with proper invalidation
2. Consider caching monthly expense totals

### Phase 3: Schema (requires migration)
1. Add composite index on expenses
2. Consider denormalizing monthly totals if needed

---

## 📁 Files to Modify

1. `app/Models/User.php` - Partner caching
2. `app/Models/Settlement.php` - Query caching
3. `app/Livewire/Expenses.php` - Computed properties
4. `database/migrations/` - New indexes

---

## Monitoring Recommendations

1. Enable query logging in development:
```php
// AppServiceProvider
DB::listen(fn ($query) => logger($query->sql, $query->bindings));
```

2. Consider Laravel Debugbar for development
3. Add basic query count assertions in tests

---

*Review complete. Overall: solid foundation, minor optimizations needed before scaling.*
