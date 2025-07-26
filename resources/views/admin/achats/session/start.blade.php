{{-- resources/views/admin/achats/session/start.blade.php --}}
@extends('layouts.admin')

@section('title', 'Nouvelle session d\'achats')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Fil d'Ariane --}}
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('admin.achats.index') }}" class="text-gray-700 hover:text-blue-600 ml-1 md:ml-2">
                            Achats
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2">Session d'achats</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- En-tête --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle session d'achats groupés</h1>
            <p class="mt-2 text-gray-600">Ajoutez plusieurs produits pour un même distributeur et validez tout en une fois</p>
        </div>

        {{-- Messages d'erreur --}}
        @if ($errors->any())
            <div class="mb-6">
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Il y a {{ $errors->count() }} erreur(s) dans le formulaire</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulaire principal --}}
        <form action="{{ route('admin.achats.session.init') }}" method="POST">
            @csrf

            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Configuration de la session
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Alert info --}}
                    <div class="rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-blue-800">Mode session d'achats</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Cette fonctionnalité vous permet d'enregistrer plusieurs achats pour un même distributeur avant de tout valider en une seule opération.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Recherche distributeur --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Distributeur <span class="text-red-500">*</span>
                            </label>

                            {{-- Champ de recherche --}}
                            <div class="relative">
                                <input
                                    type="text"
                                    id="distributeur-search"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    placeholder="Rechercher par nom, prénom, matricule ou téléphone..."
                                    autocomplete="off"
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>

                                {{-- Résultats de recherche --}}
                                <div id="search-results" class="absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-80 overflow-y-auto z-50" style="display: none;">
                                    <!-- Les résultats seront affichés ici -->
                                </div>
                            </div>

                            {{-- Distributeur sélectionné --}}
                            <div id="selected-distributeur" class="mt-3" style="display: none;">
                                <!-- Le distributeur sélectionné sera affiché ici -->
                            </div>

                            {{-- Input caché pour l'ID du distributeur --}}
                            <input type="hidden" name="distributeur_id" id="distributeur_id" value="{{ old('distributeur_id', session('last_distributeur_id')) }}" required>

                            @error('distributeur_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date des achats <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="date"
                                       name="date"
                                       id="date"
                                       class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('date') border-red-500 @enderror"
                                       value="{{ old('date', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Période en cours : <span class="font-medium text-gray-700">{{ $currentPeriod->period }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                    <a href="{{ route('admin.achats.index') }}"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Démarrer la session
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script de recherche autocomplete (même que dans create.blade.php)
    const searchInput = document.getElementById('distributeur-search');
    const searchResults = document.getElementById('search-results');
    const selectedDistributeur = document.getElementById('selected-distributeur');
    const distributeurIdInput = document.getElementById('distributeur_id');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                searchResults.innerHTML = `
                    <div class="p-4 text-center">
                        <svg class="animate-spin h-8 w-8 mx-auto text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                `;
                searchResults.style.display = 'block';

                fetch(`{{ route('admin.api.distributeurs.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';

                    if (data.length === 0) {
                        searchResults.innerHTML = `
                            <div class="p-4 text-center text-gray-500">
                                Aucun distributeur trouvé
                            </div>
                        `;
                    } else {
                        data.forEach(distributeur => {
                            const div = document.createElement('div');
                            div.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0';
                            div.innerHTML = `
                                <div class="font-medium text-gray-900">#${distributeur.distributeur_id} - ${distributeur.pnom_distributeur} ${distributeur.nom_distributeur}</div>
                                ${distributeur.tel_distributeur ? `<div class="text-sm text-gray-600">Tél: ${distributeur.tel_distributeur}</div>` : ''}
                            `;

                            div.addEventListener('click', function() {
                                selectDistributeur(distributeur);
                            });

                            searchResults.appendChild(div);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    searchResults.innerHTML = `
                        <div class="p-4 text-center">
                            <div class="text-red-600">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="mt-2 text-sm">Erreur lors de la recherche</p>
                            </div>
                        </div>
                    `;
                });
            }, 300);
        });
    }

    // Fonction pour sélectionner un distributeur
    function selectDistributeur(distributeur) {
        distributeurIdInput.value = distributeur.id;
        searchInput.value = '';
        searchResults.style.display = 'none';

        selectedDistributeur.innerHTML = `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-10 w-10 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <div>
                        <p class="text-sm text-blue-600 font-medium">Distributeur sélectionné</p>
                        <p class="text-gray-900 font-semibold">${distributeur.text}</p>
                        ${distributeur.tel_distributeur ? `<p class="text-sm text-gray-600">Tél: ${distributeur.tel_distributeur}</p>` : ''}
                    </div>
                </div>
                <button type="button" onclick="resetDistributeur()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;
        selectedDistributeur.style.display = 'block';
    }

    // Fonction pour réinitialiser la sélection
    function resetDistributeur() {
        distributeurIdInput.value = '';
        selectedDistributeur.style.display = 'none';
        searchInput.value = '';
        searchInput.focus();
    }

    // Fermer les résultats si on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Si un distributeur était déjà sélectionné (last_distributeur_id)
    @if(old('distributeur_id', session('last_distributeur_id')))
        // Charger les infos du distributeur pré-sélectionné
        fetch(`{{ route('admin.api.distributeurs.search') }}?id={{ old('distributeur_id', session('last_distributeur_id')) }}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    selectDistributeur(data[0]);
                }
            });
    @endif
</script>
@endpush
