<?php

namespace App\Listeners;

use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Verified;

class SendWelcomeEmail
{
    public function handle(Verified $event): void
    {
        $user = $event->user;

        $user->notify(new WelcomeNotification(
            inviteCode: $user->invite_code,
            isSpaceOwner: $user->isSpaceOwner()
        ));
    }
}
