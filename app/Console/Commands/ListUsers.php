<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    protected $signature = 'app:users';

    protected $description = 'List all users';

    public function handle(): void
    {
        $users = User::with('space')->get();

        if ($users->isEmpty()) {
            $this->info('No users found.');

            return;
        }

        $rows = $users->map(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'verified' => $user->hasVerifiedEmail() ? '✓' : '✗',
            'space' => $user->space?->invite_code ?? '—',
            'partner' => $user->partner?->name ?? '—',
            'created' => $user->created_at->format('Y-m-d H:i'),
        ]);

        $this->table(['ID', 'Name', 'Email', 'Verified', 'Space', 'Partner', 'Created'], $rows);
        $this->newLine();
        $this->info("Total: {$users->count()} user(s)");
    }
}
