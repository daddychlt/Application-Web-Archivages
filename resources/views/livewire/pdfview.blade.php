<div>
    <div class="bg-gray-100 dark:bg-gray-900 flex flex-col items-center justify-center">
        <!-- Conteneur principal -->
        <div
            class="relative w-full bg-white border border-gray-300 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700">
            <!-- En-tête -->
            <header class="flex items-center justify-between px-6 py-3 bg-blue-600 rounded-t-lg">
                <h1 class="text-lg font-semibold text-white">Aperçu du document</h1>
                <button onclick="window.history.back()" class="text-white hover:text-gray-300 focus:outline-none">
                    <div class="inline-flex space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>
                            Retour
                        </span>
                    </div>
                </button>
            </header>

            <!-- Contenu principal -->
            <div class="grid grid-cols-1 md:grid-cols-4">
                <!-- Aperçu du document (colonne principale) -->
                <div class="col-span-3">
                    @if (in_array($document->type, ['pdf', 'txt', 'png', 'jpeg']))
                        <iframe src="{{ asset('storage/' . $document->filename) }}"
                            class="w-full h-[500px] border-none rounded-bl-lg"></iframe>
                    @else
                        <iframe src="{{ asset('storage/preview_' . $document->id . '.pdf') }}"
                            class="w-full h-[500px] border-none rounded-bl-lg">Affichage indisponible</iframe>
                    @endif
                </div>

                <!-- Informations supplémentaires (colonne secondaire) -->
                <aside
                    class="bg-gray-50 p-6 dark:bg-gray-700 rounded-br-lg border-l border-gray-200 dark:border-gray-600 space-y-3">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Informations sur le document</h2>
                    <ul class="mt-4 space-y-2 text-gray-600 dark:text-gray-400">
                        <li><strong>Titre :</strong> {{ $document->nom }}</li>
                        <li><strong>Type du fichier :</strong> {{ $document->type }}</li>
                        <li><strong>Auteur :</strong> {{ $document->user->name }}</li>
                        @php
                            $lines = explode("\n", $document->content); // Divise le contenu en lignes
                            $lastLine = end($lines); // Récupère la dernière ligne
                        @endphp
                        <li><strong>Mot clé de recherche :</strong> {{ $lastLine }}</li>
                        <li><strong>Date de création :</strong> {{ $document->created_at->format('d/m/Y') }}</li>
                    </ul>

                    <div class="inline-flex space-x-2">
                        <a href="{{ route('tag', $document->id) }}">
                            <button type="button"
                                class="px-3 py-2 text-xs font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                <svg class="w-3 h-3 text-white me-2" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                                    <path
                                        d="m10.036 8.278 9.258-7.79A1.979 1.979 0 0 0 18 0H2A1.987 1.987 0 0 0 .641.541l9.395 7.737Z" />
                                    <path
                                        d="M11.241 9.817c-.36.275-.801.425-1.255.427-.428 0-.845-.138-1.187-.395L0 2.6V14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2.5l-8.759 7.317Z" />
                                </svg>
                                Laisser un message
                            </button>
                        </a>
                        <a href="{{ asset('storage/' . $document->filename) }}" target="_blank">
                            <button type="button"
                                class="px-3 py-2 text-xs font-medium text-center inline-flex items-center text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                <svg class="w-3 h-3 text-white me-2" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                                    <path fill-rule="evenodd"
                                        d="M11.403 5H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-6.403a3.01 3.01 0 0 1-1.743-1.612l-3.025 3.025A3 3 0 1 1 9.99 9.768l3.025-3.025A3.01 3.01 0 0 1 11.403 5Z"
                                        clip-rule="evenodd" />
                                    <path fill-rule="evenodd"
                                        d="M13.232 4a1 1 0 0 1 1-1H20a1 1 0 0 1 1 1v5.768a1 1 0 1 1-2 0V6.414l-6.182 6.182a1 1 0 0 1-1.414-1.414L17.586 5h-3.354a1 1 0 0 1-1-1Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Ouvrir hors de l'application
                            </button>
                        </a>
                    </div>

                </aside>
            </div>
        </div>
    </div>

</div>
