<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
     * Check if any month from $from up to $to (open-ended without $to) is settled for a space
     */
    public static function anyBetween(int $spaceId, Carbon $from, ?Carbon $to = null): bool
    {
        return static::where('space_id', $spaceId)
            ->whereRaw('year * 12 + month >= ?', [$from->year * 12 + $from->month])
            ->when($to, fn ($query) => $query->whereRaw('year * 12 + month <= ?', [$to->year * 12 + $to->month]))
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
