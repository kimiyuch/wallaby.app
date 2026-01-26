<?php

namespace App\Notifications;

use App\Models\Settlement;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MonthSettledNotification extends Notification
{
    public function __construct(
        public Settlement $settlement,
        public string $settledByName
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $monthName = date('F', mktime(0, 0, 0, $this->settlement->month, 1));
        $year = $this->settlement->year;

        $message = (new MailMessage)
            ->subject("✓ {$monthName} {$year} settled – kimiyu")
            ->greeting("Hi {$notifiable->name}!")
            ->line("**{$this->settledByName}** has marked **{$monthName} {$year}** as settled.");

        if ($this->settlement->balance_amount > 0) {
            $message->line("Final balance: **{$this->settlement->balance_owed_by}** owed **CHF {$this->settlement->balance_amount}**.");
        } else {
            $message->line('You were all square – no balance owed.');
        }

        return $message
            ->line('This month is now closed and no more expenses can be added.')
            ->action('View History', url('/expenses'))
            ->salutation('— The kimiyu team');
    }
}
