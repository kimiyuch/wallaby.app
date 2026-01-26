<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Check if this recurring expense is active for a given month/year
     */
    public function isActiveForMonth(int $month, int $year): bool
    {
        if (! $this->is_recurring) {
            return false;
        }

        $checkDate = now()->setYear($year)->setMonth($month)->startOfMonth();
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
