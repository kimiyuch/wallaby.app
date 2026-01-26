# Future: Multi-Space Support

**Date:** 2026-01-26  
**Status:** Planning  
**Priority:** Future consideration

---

## Current Architecture

- User belongs to **one** space (`users.space_id`)
- Space has **max 2** users
- Partner = other user in same space

```
User (1) → (1) Space (1) ← (1) User
```

---

## Proposed Architecture

User can belong to **multiple** spaces via pivot table.

```
User (M) ↔ (M) Space
```

### Database Changes

**New pivot table: `space_user`**
```php
Schema::create('space_user', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('space_id')->constrained()->cascadeOnDelete();
    $table->string('role')->default('member'); // owner, member
    $table->timestamp('joined_at');
    $table->timestamps();
    
    $table->unique(['user_id', 'space_id']);
});
```

**Migration steps:**
1. Create `space_user` table
2. Migrate existing `users.space_id` → `space_user`
3. Remove `users.space_id` column

### Model Changes

**User.php**
```php
// Before
public function space(): BelongsTo

// After
public function spaces(): BelongsToMany
{
    return $this->belongsToMany(Space::class)
        ->withPivot('role', 'joined_at')
        ->withTimestamps();
}

public function currentSpace(): ?Space
{
    // Active/selected space (stored in session or user preference)
}
```

**Space.php**
```php
// Before
public function users(): HasMany

// After  
public function users(): BelongsToMany
{
    return $this->belongsToMany(User::class)
        ->withPivot('role', 'joined_at')
        ->withTimestamps();
}
```

---

## UI/UX Considerations

### Space Switcher
- Dropdown in sidebar/header to switch active space
- Show space name or partner name
- Quick access to all spaces

### Space Management (Profile)
- List all spaces user belongs to
- Create new space
- Join space via invite code
- Leave space (if not owner, or transfer ownership first)

### Naming Spaces
- Currently spaces have optional `name` field
- Could auto-name as "Space with {partner_name}"
- Or let users customize

---

## Use Cases

1. **Co-parents with new partners**
   - User A shares space with Ex (for kids)
   - User A shares space with New Partner (household)

2. **Roommates + Partner**
   - Space with roommates (utilities)
   - Space with partner (personal)

3. **Multiple children with different co-parents**
   - Space per child/co-parent combination

---

## Implementation Phases

### Phase 1: Foundation
- [ ] Create migration for `space_user` pivot
- [ ] Update models (User, Space)
- [ ] Migrate existing data
- [ ] Add `current_space_id` to session

### Phase 2: Space Switching
- [ ] Space switcher component
- [ ] Update all queries to use `currentSpace()`
- [ ] Update Livewire components

### Phase 3: Space Management
- [ ] Join existing space (invite code in profile)
- [ ] Create new space
- [ ] Leave space
- [ ] Space settings per space

### Phase 4: Polish
- [ ] Space naming/customization
- [ ] Notifications per space
- [ ] Email preferences per space

---

## Breaking Changes

- `$user->space` → `$user->currentSpace()`
- `$user->partner` → `$user->currentSpace()->partner($user)`
- `$user->space_id` → removed
- All expense/settlement queries need space context

---

## Migration Strategy

1. Deploy pivot table (additive)
2. Backfill pivot from `users.space_id`
3. Update code to use pivot (keep `space_id` as fallback)
4. Remove `space_id` column
5. Remove fallback code

---

## Questions to Resolve

- [ ] Should expenses be tied to space or user?
  - Current: User (but displayed per space)
  - Better: Expense belongs to space + user
  
- [ ] Settlement per space (already correct)

- [ ] Invite code: per space (current) or per user?

- [ ] Max users per space: keep at 2 or make configurable?

---

*This is a significant refactor. Recommend completing current beta first, then revisiting based on user feedback.*
