<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{

    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify your email – Wallaby')
            ->greeting("Hi {$notifiable->name}!")
            ->line("Welcome to Wallaby! We're excited to have you on board.")
            ->line('Please verify your email address by clicking the button below.')
            ->action('Verify Email', $verificationUrl)
            ->line('Once verified, you can start tracking and splitting expenses with your partner.')
            ->line('If you didn\'t create an account with Wallaby, you can safely ignore this email.')
            ->salutation('— The Wallaby team');
    }
}
