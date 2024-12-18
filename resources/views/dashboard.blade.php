<x-app-layout>
    <!-- Header -->
    <x-slot name="header" class="pt-24 pb-12 bg-gray-50 shadow-md">
        <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-indigo-600 via-blue-500 to-blue-400 text-white">
        <div class="px-6 mx-auto max-w-screen-xl text-center py-24 lg:py-32">
            <h1 class="text-5xl font-extrabold mb-6">Bienvenue sur <span class="text-yellow-400">DocArch</span></h1>
            <p class="text-lg font-light mb-8 sm:px-16 lg:px-32">
                Votre espace dédié pour une gestion efficace et sécurisée de vos documents numériques !
            </p>
            <div class="flex flex-col sm:flex-row sm:justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#"
                    class="px-8 py-3 bg-white text-indigo-600 font-medium rounded-lg shadow-md hover:bg-gray-100 hover:shadow-lg transition">
                    Commencer
                </a>
                <a href="#"
                    class="px-8 py-3 border border-white text-white font-medium rounded-lg hover:bg-white hover:text-indigo-600 transition">
                    En savoir plus
                </a>
            </div>
        </div>
    </section>

    <!-- Recherche Rapide -->
    <div class="max-w-screen-xl mx-auto mt-12 px-6">
        <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Recherche Rapide</h2>
            @livewire('fast-search')
        </div>
    </div>

    <!-- Statistiques -->
    <div class="bg-gray-100 py-8">
        <div class="max-w-screen-xl mx-auto px-6">
            @livewire('stat-general')
        </div>
    </div>

    <!-- Sections principales -->
    <div class="bg-gray-100 py-12">
        <div class="max-w-screen-xl mx-auto px-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Accessibilités</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Actions Rapides -->
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <h2 class="text-xl font-bold mb-4 text-gray-700">Actions Rapides</h2>
                    <ul class="space-y-4">
                        @if (Auth::user()->role->nom === "SuperAdministrateur" | Auth::user()->role->nom === "Administrateur")
                        <li>
                            <a href="{{ route('document') }}"
                                class="flex items-center text-blue-600 hover:text-blue-800 transition">
                                ➕ <span class="ml-2">Ajouter un document</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user') }}"
                                class="flex items-center text-blue-600 hover:text-blue-800 transition">
                                🛠 <span class="ml-2">Gérer les utilisateurs</span>
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ route('service') }}"
                                class="flex items-center text-blue-600 hover:text-blue-800 transition">
                                🔍 <span class="ml-2">Consulter les services</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Activités Récentes -->
                <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <h2 class="text-xl font-bold mb-4 text-gray-700">Activités Récentes</h2>
                    <ul class="divide-y divide-gray-200">
                        @if (count($activities) > 0)
                            @foreach ($activities as $activity)
                                <li class="py-3 text-gray-600">
                                    {{ $activity->action }} :
                                    <span class="font-semibold">"{{ $activity->description }}"</span> -
                                    {{ $activity->created_at->diffForHumans() }}
                                </li>
                            @endforeach
                        @else
                            <li class="py-3 text-gray-600">Aucune activité récente</li>
                        @endif
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-300 text-center py-6">
        <p>&copy; 2024 DocArch - Tous droits réservés.</p>
    </footer>
</x-app-layout>