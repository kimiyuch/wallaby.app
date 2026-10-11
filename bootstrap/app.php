<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Signed-in users who open a guest page go to their expenses. An invite link
        // (/register?code=…) would otherwise bounce them without a word, so say why.
        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->routeIs('register') && filled($code = $request->query('code'))) {
                $user = $request->user();

                $request->session()->flash('toast', [
                    'type' => 'warning',
                    'message' => $code === $user->invite_code
                        ? "That's your own invite link. Send it to your person, it opens the sign-up with your code filled in."
                        : "You're signed in as {$user->email}. Log out first to join with this invite.",
                ]);
            }

            return route('expenses');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
