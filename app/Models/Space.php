<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Space extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'invite_code',
    ];

    /**
     * 4-letter words for invite code generation
     */
    protected static array $words = [
        'blue', 'moon', 'snow', 'star', 'fire', 'wind', 'wave', 'leaf',
        'rain', 'gold', 'pink', 'mint', 'lime', 'ruby', 'jade', 'opal',
        'rose', 'dawn', 'dusk', 'glow', 'beam', 'mist', 'haze', 'fern',
        'pine', 'palm', 'sage', 'iris', 'lily', 'dove', 'hawk', 'bear',
        'wolf', 'deer', 'fawn', 'fish', 'crab', 'seal', 'kiwi', 'puma',
        'lynx', 'frog', 'moth', 'wasp', 'wren', 'lark', 'swan', 'crow',
        'teal', 'plum', 'pear', 'kale', 'corn', 'rice', 'cake', 'wine',
        'brew', 'salt', 'zinc', 'iron', 'silk', 'wool', 'lace', 'felt',
        'cozy', 'warm', 'cool', 'calm', 'bold', 'soft', 'loud', 'deep',
        'high', 'wide', 'fast', 'slow', 'easy', 'safe', 'pure', 'true',
        'free', 'wild', 'tame', 'kind', 'wise', 'fair', 'keen', 'neat',
        'coil', 'knot', 'loop', 'curl', 'bend', 'arch', 'edge', 'peak',
        'hill', 'dale', 'glen', 'cove', 'bay', 'reef', 'pond', 'lake',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function owner(): ?User
    {
        return $this->users()->oldest()->first();
    }

    /**
     * Generate a unique invite code (3 words, 4 chars each)
     */
    public static function generateInviteCode(): string
    {
        do {
            $words = collect(static::$words)->random(3);
            $code = $words->implode('-');
        } while (static::where('invite_code', $code)->exists());

        return $code;
    }

    /**
     * Regenerate invite code for this space
     */
    public function regenerateInviteCode(): string
    {
        $this->invite_code = static::generateInviteCode();
        $this->save();

        return $this->invite_code;
    }

    /**
     * Find a space by invite code
     */
    public static function findByInviteCode(string $code): ?static
    {
        return static::where('invite_code', strtolower($code))->first();
    }

    /**
     * Check if space is full (2 members max)
     */
    public function isFull(): bool
    {
        return $this->users()->count() >= 2;
    }
}
