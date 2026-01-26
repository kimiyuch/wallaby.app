<x-app-layout>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-xl text-gray-900 font-bold">Profile</h1>
    </div>

    <div class="space-y-12 max-w-lg">
        <livewire:profile.space-settings />
        <livewire:profile.update-profile-information-form />
        <livewire:profile.update-password-form />
        <livewire:profile.delete-user-form />
    </div>
</x-app-layout>
