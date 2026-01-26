<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'space_id',
        'settled_by_user_id',
        'month',
        'year',
        'balance_amount',
        'balance_owed_by',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'balance_amount' => 'decimal:2',
    ];

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function settledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'settled_by_user_id');
    }

    /**
     * Check if a month is settled for a space
     */
    public static function isSettled(int $spaceId, int $month, int $year): bool
    {
        return static::where('space_id', $spaceId)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();
    }

    /**
     * Get settlement for a specific month
     */
    public static function forMonth(int $spaceId, int $month, int $year): ?static
    {
        return static::where('space_id', $spaceId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
    }
}
