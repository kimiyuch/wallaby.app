<?php

namespace App\Actions;

use App\Models\Expense;
use App\Models\Settlement;
use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LogicException;

class StartFresh
{
    /**
     * Whether the user can leave their space: only once their partner deleted their account
     */
    public function isOffered(User $user): bool
    {
        return (bool) $user->partner?->hasDeparted();
    }

    /**
     * Months up to now with expenses that aren't settled yet; they must be settled first
     *
     * @return Collection<int, Carbon>
     */
    public function openMonths(User $user): Collection
    {
        $ids = [$user->id, $user->partner?->id];
        $first = Expense::whereIn('user_id', array_filter($ids))->where('date', '<=', now())->min('date');
        $months = collect();

        if (! $first) {
            return $months;
        }

        for ($month = Carbon::parse($first)->startOfMonth(); $month->lte(now()); $month = $month->copy()->addMonth()) {
            $hasExpenses = collect($ids)->filter()->contains(
                fn ($id) => Expense::forUserInMonth($id, $month)->isNotEmpty()
            );

            if ($hasExpenses && ! Settlement::isSettled($user->space_id, $month->month, $month->year)) {
                $months->push($month);
            }
        }

        return $months;
    }

    /**
     * The user's expenses that still count after this month and move to the new space
     *
     * @return Collection<int, Expense>
     */
    public function carriedOver(User $user): Collection
    {
        $next = $this->nextMonth();

        return Expense::where('user_id', $user->id)
            ->where(function ($query) use ($next) {
                $query->where(fn ($query) => $query->where('is_recurring', true)
                    ->where(fn ($query) => $query->whereNull('end_date')->orWhere('end_date', '>=', $next)))
                    ->orWhere(fn ($query) => $query->where('is_recurring', false)->where('date', '>=', $next));
            })
            ->orderBy('date')
            ->get();
    }

    public function nextMonth(): Carbon
    {
        return now()->startOfMonth()->addMonth();
    }

    /**
     * Give the user a new, empty space. Expenses that still run continue from next month;
     * everything else in the old space (both people's history, the settled months and the
     * former partner's account) is deleted, as nobody uses it any more.
     */
    public function __invoke(User $user): Space
    {
        if (! $this->isOffered($user) || $this->openMonths($user)->isNotEmpty()) {
            throw new LogicException('Starting fresh needs a departed partner and all months settled.');
        }

        return DB::transaction(function () use ($user) {
            $oldSpace = $user->space;
            $former = $user->partner;
            $carried = $this->carriedOver($user);

            foreach ($carried->where('is_recurring', true) as $expense) {
                if ($expense->date->lt($this->nextMonth())) {
                    $expense->update(['date' => $this->nextMonth()]);
                }
            }

            Expense::where('user_id', $user->id)->whereNotIn('id', $carried->modelKeys())->delete();

            $space = Space::create(['invite_code' => Space::generateInviteCode()]);
            $user->update(['space_id' => $space->id]);

            $former->delete();
            $oldSpace->delete();

            return $space;
        });
    }
}
