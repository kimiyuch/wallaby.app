<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DeleteAccount
{
    /**
     * Delete a user's account. While a partner still uses the space, the expenses and settled
     * months stay so the partner's history keeps adding up; only the personal data goes and
     * the account can't be used again. Without one, nobody needs the history: the user, a
     * former partner and the space are deleted with everything in them.
     */
    public function __invoke(User $user): void
    {
        $partner = $user->partner;

        DB::transaction(function () use ($user, $partner) {
            if ($partner && ! $partner->hasDeparted()) {
                $user->forceFill([
                    'name' => 'Former partner',
                    'email' => "former-{$user->id}@wallaby.invalid",
                    'password' => Str::random(64),
                    'email_verified_at' => null,
                    'remember_token' => null,
                    'departed_at' => now(),
                ])->save();

                // Sign out other devices too; the session above is already gone.
                if (Schema::hasTable('sessions')) {
                    DB::table('sessions')->where('user_id', $user->id)->delete();
                }

                return;
            }

            $space = $user->space;

            $partner?->delete();
            $user->delete();
            $space?->delete();
        });
    }
}
