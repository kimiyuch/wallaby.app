<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'space_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the partner (other user in the same space)
     */
    public function getPartnerAttribute(): ?User
    {
        if (! $this->space_id) {
            return null;
        }

        return User::where('space_id', $this->space_id)
            ->where('id', '!=', $this->id)
            ->first();
    }

    /**
     * Check if user has a partner in their space
     */
    public function hasPartner(): bool
    {
        return $this->partner !== null;
    }

    /**
     * Check if user is the space owner (first user)
     */
    public function isSpaceOwner(): bool
    {
        if (! $this->space) {
            return false;
        }

        return $this->space->users()->oldest()->first()?->id === $this->id;
    }

    /**
     * Get the invite code for this user's space
     */
    public function getInviteCodeAttribute(): ?string
    {
        return $this->space?->invite_code;
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }
}
