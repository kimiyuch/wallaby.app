<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BalanceCalculator
{
    /**
     * Calculate the balance between two users based on their expenses.
     *
     * @param  Collection  $userExpenses  Expenses paid by the primary user
     * @param  Collection  $partnerExpenses  Expenses paid by the partner
     * @param  User  $user  The primary user
     * @param  User|null  $partner  The partner user
     * @return array<int, float> Balance indexed by user ID (positive = owes, negative = is owed)
     */
    public function calculate(
        Collection $userExpenses,
        Collection $partnerExpenses,
        User $user,
        ?User $partner
    ): array {
        if (! $partner) {
            return [];
        }

        // Separate shared and personal expenses
        $userSharedExpenses = $userExpenses->where('is_personal', false)->sum('amount');
        $partnerSharedExpenses = $partnerExpenses->where('is_personal', false)->sum('amount');

        // Calculate shared expense split (50/50)
        $totalShared = $userSharedExpenses + $partnerSharedExpenses;
        $fairShare = $totalShared / 2;

        // Base balance from shared expenses
        $userOwes = $fairShare - $userSharedExpenses;
        $partnerOwes = $fairShare - $partnerSharedExpenses;

        // Add personal expenses (full amount owed by partner)
        $userPersonalExpenses = $userExpenses->where('is_personal', true)->sum('amount');
        $partnerPersonalExpenses = $partnerExpenses->where('is_personal', true)->sum('amount');

        // If user paid personal expenses for partner, partner owes user more
        $userOwes -= $userPersonalExpenses;
        $partnerOwes += $userPersonalExpenses;

        // If partner paid personal expenses for user, user owes partner more
        $userOwes += $partnerPersonalExpenses;
        $partnerOwes -= $partnerPersonalExpenses;

        return [
            $user->id => round($userOwes, 2),
            $partner->id => round($partnerOwes, 2),
        ];
    }

    /**
     * What the user owes the partner over every month up to now that isn't settled yet
     * (positive = owes, negative = is owed).
     */
    public function openBalance(User $user, User $partner): float
    {
        $first = Expense::whereIn('user_id', [$user->id, $partner->id])->min('date');

        if (! $first || ! $user->space_id) {
            return 0.0;
        }

        $total = 0.0;

        for ($month = Carbon::parse($first)->startOfMonth(); $month->lte(now()); $month = $month->copy()->addMonth()) {
            if (Settlement::isSettled($user->space_id, $month->month, $month->year)) {
                continue;
            }

            $balance = $this->calculate(
                Expense::forUserInMonth($user->id, $month),
                Expense::forUserInMonth($partner->id, $month),
                $user,
                $partner
            );

            $total += $balance[$user->id];
        }

        return round($total, 2);
    }
}
