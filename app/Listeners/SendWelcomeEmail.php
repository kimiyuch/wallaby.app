<?php

namespace App\Listeners;

use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Cache;

class SendWelcomeEmail implements ShouldHandleEventsAfterCommit
{
    public function handle(Verified $event): void
    {
        $user = $event->user;

        // Prevent duplicate welcome emails
        $cacheKey = 'welcome-sent-'.$user->id;
        if (Cache::has($cacheKey)) {
            return;
        }
        Cache::put($cacheKey, true, now()->addHour());

        $user->notify(new WelcomeNotification(
            inviteCode: $user->invite_code,
            isSpaceOwner: $user->isSpaceOwner()
        ));
    }
}
