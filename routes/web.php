<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::view('/privacy', 'privacy')->name('privacy');

Route::view('expenses', 'expenses')
    ->middleware(['auth', 'verified'])
    ->name('expenses');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('logout', function (Logout $logout) {
    $logout();

    return redirect('/');
})->middleware('auth')->name('logout');

require __DIR__.'/auth.php';
