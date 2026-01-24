<div class="max-w-md">
    @if($household)
        <!-- Already has household - show info -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl text-gray-900 font-bold">Household</h1>
        </div>
        
        <div class="space-y-6">
            <div>
                <label class="text-sm text-gray-500">Name</label>
                <p class="text-gray-900">{{ $household->name }}</p>
            </div>
            
            <div>
                <label class="text-sm text-gray-500">Invite Code</label>
                <div class="flex items-center gap-2 mt-1">
                    <code class="bg-gray-100 px-3 py-2 rounded-md text-lg font-mono">{{ $household->invite_code }}</code>
                    <button onclick="navigator.clipboard.writeText('{{ $household->invite_code }}')" 
                            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-sm transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Members</label>
                <ul class="mt-2 space-y-2">
                    <li class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span class="text-gray-900">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-gray-400">(you)</span>
                    </li>
                    @if($partner)
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            <span class="text-gray-900">{{ $partner->name }}</span>
                        </li>
                    @else
                        <li class="flex items-center gap-2 text-gray-400">
                            <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                            Waiting for partner to join...
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    @else
        <!-- No household yet -->
        @if($mode === 'choose')
            <div class="text-center py-8">
                <h1 class="text-xl text-gray-900 font-bold mb-2">Set Up Your Household</h1>
                <p class="text-gray-500 mb-8">Create a new household or join an existing one.</p>
                
                <div class="space-y-3 max-w-xs mx-auto">
                    <button wire:click="createHousehold" 
                            class="w-full px-4 py-3 bg-gray-900 text-white text-sm rounded-md hover:bg-gray-800 transition-colors">
                        Create New Household
                    </button>
                    <button wire:click="joinHousehold" 
                            class="w-full px-4 py-3 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200 transition-colors">
                        Join Existing Household
                    </button>
                </div>
            </div>
        @elseif($mode === 'create')
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl text-gray-900 font-bold">Create Household</h1>
            </div>
            
            <form wire:submit="submitCreate" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Household Name</label>
                    <input type="text" wire:model="householdName" 
                           class="w-full rounded-md border-gray-200 text-sm focus:border-gray-400 focus:ring-0"
                           placeholder="e.g. Kids Expenses">
                    @error('householdName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-gray-900 text-white text-sm rounded-md hover:bg-gray-800 transition-colors">
                        Create
                    </button>
                    <button type="button" wire:click="back" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200 transition-colors">
                        Back
                    </button>
                </div>
            </form>
        @elseif($mode === 'join')
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl text-gray-900 font-bold">Join Household</h1>
            </div>
            
            <form wire:submit="submitJoin" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-500 mb-1">Invite Code</label>
                    <input type="text" wire:model="inviteCode" 
                           class="w-full rounded-md border-gray-200 text-sm focus:border-gray-400 focus:ring-0 font-mono text-center tracking-wider"
                           placeholder="XXXXXXXX" maxlength="8">
                    @error('inviteCode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-gray-900 text-white text-sm rounded-md hover:bg-gray-800 transition-colors">
                        Join
                    </button>
                    <button type="button" wire:click="back" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200 transition-colors">
                        Back
                    </button>
                </div>
            </form>
        @endif
    @endif
</div>
