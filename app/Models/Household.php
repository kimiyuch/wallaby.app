<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Household extends Model
{
    protected $fillable = ['name', 'invite_code'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($household) {
            $household->invite_code = Str::random(8);
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    public function getBalanceAttribute(): array
    {
        $users = $this->users;
        if ($users->count() !== 2) {
            return [];
        }

        $user1 = $users->first();
        $user2 = $users->last();

        $user1Paid = $this->expenses()->where('user_id', $user1->id)->sum('amount');
        $user2Paid = $this->expenses()->where('user_id', $user2->id)->sum('amount');

        $total = $user1Paid + $user2Paid;
        $fairShare = $total / 2;

        $user1Owes = $fairShare - $user1Paid;
        $user2Owes = $fairShare - $user2Paid;

        // Account for settlements
        $settlementsTo1 = $this->settlements()->where('to_user_id', $user1->id)->sum('amount');
        $settlementsTo2 = $this->settlements()->where('to_user_id', $user2->id)->sum('amount');
        $settlementsFrom1 = $this->settlements()->where('from_user_id', $user1->id)->sum('amount');
        $settlementsFrom2 = $this->settlements()->where('from_user_id', $user2->id)->sum('amount');

        $user1Balance = $user1Owes - $settlementsFrom1 + $settlementsTo1;
        $user2Balance = $user2Owes - $settlementsFrom2 + $settlementsTo2;

        return [
            $user1->id => round($user1Balance, 2),
            $user2->id => round($user2Balance, 2),
        ];
    }
}
