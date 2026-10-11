<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'date',
        'end_date',
        'is_recurring',
        'is_personal',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'is_personal' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Expenses a user paid that count in the month starting at $monthStart: the one-offs dated
     * in it and every recurring expense running then, one-offs first.
     */
    public static function forUserInMonth(int $userId, Carbon $monthStart): Collection
    {
        $monthEnd = $monthStart->copy()->endOfMonth();

        $oneTimeExpenses = static::where('user_id', $userId)
            ->where('is_recurring', false)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get();

        $recurringExpenses = static::where('user_id', $userId)
            ->where('is_recurring', true)
            ->where('date', '<=', $monthEnd)
            ->where(function ($query) use ($monthStart) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $monthStart);
            })
            ->get();

        return $oneTimeExpenses->concat($recurringExpenses)->sortBy('is_recurring')->values();
    }

    /**
     * Check if a settled month of the space falls within the months this expense covers
     * (its own month, or every month a recurring expense runs), counting from $from on when given
     */
    public function coversSettledMonth(int $spaceId, ?Carbon $from = null): bool
    {
        $start = $this->date->copy()->startOfMonth();

        if ($from && $from->gt($start)) {
            $start = $from;
        }

        return Settlement::anyBetween($spaceId, $start, $this->is_recurring ? $this->end_date : $this->date);
    }

    /**
     * Check if this recurring expense is active for a given month/year
     */
    public function isActiveForMonth(int $month, int $year): bool
    {
        if (! $this->is_recurring) {
            return false;
        }

        $checkDate = Carbon::create($year, $month, 1);
        $startDate = $this->date->startOfMonth();

        // Must be on or after start date
        if ($checkDate->lt($startDate)) {
            return false;
        }

        // If end_date exists, must be on or before it
        if ($this->end_date && $checkDate->gt($this->end_date->endOfMonth())) {
            return false;
        }

        return true;
    }
}
