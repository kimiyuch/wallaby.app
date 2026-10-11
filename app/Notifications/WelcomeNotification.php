<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function uniqueId(): string
    {
        return 'welcome-'.$this->inviteCode;
    }

    public function __construct(
        public string $inviteCode,
        public bool $isSpaceOwner = true
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Wallaby! 🎉')
            ->markdown('emails.welcome', [
                'user' => $notifiable,
                'inviteCode' => $this->inviteCode,
                'isSpaceOwner' => $this->isSpaceOwner,
            ]);
    }
}
