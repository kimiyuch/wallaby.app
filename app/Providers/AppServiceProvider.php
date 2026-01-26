<?php

namespace App\Providers;

use App\Listeners\SendWelcomeEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Verified::class, SendWelcomeEmail::class);

        // Redirect all emails to test address in non-production environments
        if (!app()->isProduction()) {
            Mail::alwaysTo('m@marceli.to');
        }
    }
}
