<x-mail::message>
# Hey {{ $user->name }}!

@if($isSpaceOwner)
You're all set! Your email is verified and your space is ready.

**Share this invite code with your partner:**

<x-mail::panel>
<div style="text-align: center; font-size: 20px; font-weight: 600; letter-spacing: 0.05em; color: #2a1d4d;">
{{ $inviteCode }}
</div>
</x-mail::panel>

Your partner can enter this code when they register to join your shared space.

Once they join, you'll both be able to track and split expenses together.

<x-mail::button :url="url('/expenses')">
Go to Dashboard
</x-mail::button>
@else
You've successfully joined your partner's space!

You can now track expenses together. Every expense either of you adds will be visible to both, and Wallaby will automatically calculate who owes whom.

<x-mail::button :url="url('/expenses')">
Start Tracking
</x-mail::button>
@endif

— The Wallaby team
</x-mail::message>
