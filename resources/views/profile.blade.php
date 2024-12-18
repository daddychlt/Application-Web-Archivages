<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profile') }}
            </h2>
        </div>
    </x-slot>

    @livewire('profile', ['user_profile' => $user_profile])

</x-app-layout>
