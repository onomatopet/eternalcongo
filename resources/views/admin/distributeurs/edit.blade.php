{{-- resources/views/admin/distributeurs/edit.blade.php --}}

@extends('layouts.admin')

@section('title', 'Modifier un Distributeur')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="px-4 sm:px-6 lg:px-8">
        {{-- En-tête avec fil d'Ariane --}}
        <div class="bg-white rounded-lg shadow-sm px-6 py-4 mb-6">
            <nav class="flex items-center text-sm">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Tableau de Bord
                </a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="{{ route('admin.distributeurs.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    Distributeurs
                </a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-700 font-medium">Modifier {{ $distributeur->distributeur_id }}</span>
            </nav>
        </div>

        {{-- Titre principal --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Modifier le distributeur</h1>
            <p class="mt-2 text-gray-600">Modifiez les informations du distributeur #{{ $distributeur->distributeur_id }} - {{ $distributeur->full_name }}</p>
        </div>

        {{-- Affichage des erreurs --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Veuillez corriger les erreurs suivantes :</h3>
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
        @endif

        {{-- Formulaire principal --}}
        <form action="{{ route('admin.distributeurs.update', $distributeur) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Section Informations personnelles --}}
                <div class="bg-white shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Informations personnelles</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Matricule --}}
                        <div>
                            <label for="distributeur_id" class="block text-sm font-medium text-gray-700">
                                Matricule <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="distributeur_id"
                                   id="distributeur_id"
                                   value="{{ old('distributeur_id', $distributeur->distributeur_id) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('distributeur_id') border-red-300 @enderror"
                                   required>
                            @error('distributeur_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Prénom --}}
                        <div>
                            <label for="pnom_distributeur" class="block text-sm font-medium text-gray-700">
                                Prénom <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="pnom_distributeur"
                                   id="pnom_distributeur"
                                   value="{{ old('pnom_distributeur', $distributeur->pnom_distributeur) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('pnom_distributeur') border-red-300 @enderror"
                                   required>
                            @error('pnom_distributeur')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nom --}}
                        <div>
                            <label for="nom_distributeur" class="block text-sm font-medium text-gray-700">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="nom_distributeur"
                                   id="nom_distributeur"
                                   value="{{ old('nom_distributeur', $distributeur->nom_distributeur) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('nom_distributeur') border-red-300 @enderror"
                                   required>
                            @error('nom_distributeur')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section Contact --}}
                <div class="bg-white shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Informations de contact</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Téléphone --}}
                        <div>
                            <label for="tel_distributeur" class="block text-sm font-medium text-gray-700">
                                Téléphone
                            </label>
                            <input type="tel"
                                   name="tel_distributeur"
                                   id="tel_distributeur"
                                   value="{{ old('tel_distributeur', $distributeur->tel_distributeur) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('tel_distributeur') border-red-300 @enderror">
                            @error('tel_distributeur')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email_distributeur" class="block text-sm font-medium text-gray-700">
                                Email
                            </label>
                            <input type="email"
                                   name="email_distributeur"
                                   id="email_distributeur"
                                   value="{{ old('email_distributeur', $distributeur->email_distributeur) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email_distributeur') border-red-300 @enderror">
                            @error('email_distributeur')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Adresse --}}
                        <div>
                            <label for="adress_distributeur" class="block text-sm font-medium text-gray-700">
                                Adresse
                            </label>
                            <textarea name="adress_distributeur"
                                      id="adress_distributeur"
                                      rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('adress_distributeur') border-red-300 @enderror">{{ old('adress_distributeur', $distributeur->adress_distributeur) }}</textarea>
                            @error('adress_distributeur')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section Parrainage et Grade --}}
                <div class="bg-white shadow-sm rounded-lg lg:col-span-2">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Parrainage et Grade</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Colonne Parrainage --}}
                            <div>
                                <label for="parent_search" class="block text-sm font-medium text-gray-700 mb-2">
                                    Parent / Sponsor
                                </label>

                                {{-- Champ de recherche --}}
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="parent_search"
                                        placeholder="Rechercher par matricule, nom ou prénom..."
                                        class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        autocomplete="off"
                                    >
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Champ caché pour l'ID du parent --}}
                                <input
                                    type="hidden"
                                    name="id_distrib_parent"
                                    id="id_distrib_parent"
                                    value="{{ old('id_distrib_parent', $distributeur->id_distrib_parent) }}"
                                >

                                {{-- Affichage du parent sélectionné --}}
                                <div id="selected_parent" class="mt-2 p-3 bg-blue-50 rounded-md {{ $distributeur->parent ? '' : 'hidden' }}">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-blue-900">Parent sélectionné :</p>
                                            <p id="parent_display" class="text-sm text-blue-700">
                                                @if($distributeur->parent)
                                                    #{{ $distributeur->parent->distributeur_id }} - {{ $distributeur->parent->pnom_distributeur }} {{ $distributeur->parent->nom_distributeur }}
                                                @endif
                                            </p>
                                        </div>
                                        <button type="button" onclick="clearParent()" class="text-blue-600 hover:text-blue-800">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Liste des résultats de recherche --}}
                                <div id="search_results" class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <!-- Les résultats seront ajoutés ici via JavaScript -->
                                </div>

                                @error('id_distrib_parent')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">
                                    Laissez vide pour un distributeur racine.
                                </p>
                            </div>

                            {{-- Colonne Grade et Statistiques --}}
                            <div>
                                {{-- Grade actuel --}}
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Grade actuel
                                    </label>
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm text-gray-600">Niveau d'étoiles</p>
                                                <p class="text-2xl font-bold text-yellow-600">
                                                    @if($distributeur->etoiles_id)
                                                        @for($i = 0; $i < $distributeur->etoiles_id; $i++)
                                                            ⭐
                                                        @endfor
                                                        <span class="text-base ml-2">({{ $distributeur->etoiles_id }})</span>
                                                    @else
                                                        <span class="text-gray-400">Non défini</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">
                                            Le grade est mis à jour automatiquement selon les performances
                                        </p>
                                    </div>
                                </div>

                                {{-- Statistiques du réseau --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Statistiques du réseau
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-gray-50 rounded-md p-3">
                                            <p class="text-xs text-gray-500">Enfants directs</p>
                                            <p class="text-lg font-semibold text-gray-900">{{ $distributeur->children()->count() }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-md p-3">
                                            <p class="text-xs text-gray-500">Niveau hiérarchie</p>
                                            <p class="text-lg font-semibold text-gray-900">
                                                {{ $distributeur->id_distrib_parent ? 'Niveau ' . ($distributeur->parent ? '2+' : '1') : 'Racine' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">
                        Dernière modification : {{ $distributeur->updated_at->format('d/m/Y à H:i') }}
                    </p>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.distributeurs.show', $distributeur) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Annuler
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Script pour la recherche AJAX du parent --}}
@push('scripts')
<script>
    let searchTimeout;
    const searchInput = document.getElementById('parent_search');
    const searchResults = document.getElementById('search_results');
    const selectedParentDiv = document.getElementById('selected_parent');
    const parentDisplay = document.getElementById('parent_display');
    const parentIdInput = document.getElementById('id_distrib_parent');

    // Gestion de la recherche
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.classList.add('hidden');
            searchResults.innerHTML = '';
            return;
        }

        // Afficher un loader
        searchResults.innerHTML = `
            <div class="p-4 text-center">
                <svg class="animate-spin h-5 w-5 mx-auto text-gray-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500">Recherche en cours...</p>
            </div>
        `;
        searchResults.classList.remove('hidden');

        // Faire la recherche après un délai
        searchTimeout = setTimeout(() => performSearch(query), 300);
    });

    // Fonction de recherche AJAX
    function searchDistributeurs(query) {
        fetch(`{{ route('admin.distributeurs.search') }}?q=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            searchResults.innerHTML = '';

            if (!data.results || data.results.length === 0) {
                searchResults.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        Aucun distributeur trouvé
                    </div>
                `;
            } else {
                data.results.forEach(distributeur => {
                    // Exclure le distributeur actuel et ses descendants des résultats
                    if (distributeur.id === {{ $distributeur->id }}) {
                        return;
                    }

                    const item = document.createElement('div');
                    item.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0';
                    item.innerHTML = `
                        <div class="font-medium text-gray-900">
                            ${distributeur.text}
                        </div>
                        ${distributeur.tel_distributeur ? `<div class="text-sm text-gray-500">${distributeur.tel_distributeur}</div>` : ''}
                    `;
                    item.addEventListener('click', () => selectParent(distributeur));
                    searchResults.appendChild(item);
                });
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            searchResults.innerHTML = `
                <div class="p-4 text-center text-red-500">
                    Erreur lors de la recherche
                </div>
            `;
        });
    } throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            searchResults.innerHTML = '';

            if (!data.results || data.results.length === 0) {
                searchResults.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        Aucun distributeur trouvé
                    </div>
                `;
                return;
            }

            // Afficher les résultats
            data.results.forEach(distributeur => {
                // Exclure le distributeur actuel et ses descendants des résultats
                if (distributeur.id === {{ $distributeur->id }}) {
                    return;
                }

                const item = document.createElement('div');
                item.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors duration-150 border-b last:border-b-0';
                item.innerHTML = `
                    <div class="font-medium text-gray-900">${distributeur.text}</div>
                    ${distributeur.tel_distributeur ? `<div class="text-sm text-gray-500">${distributeur.tel_distributeur}</div>` : ''}
                `;
                item.addEventListener('click', () => selectParent(distributeur));
                searchResults.appendChild(item);
            });
        })
        .catch(error => {
            console.error('Search error:', error);
            searchResults.innerHTML = `
                <div class="p-4 text-center text-red-500">
                    Erreur lors de la recherche
                </div>
            `;
        });
    }

    // Sélectionner un parent
    function selectParent(distributeur) {
        parentIdInput.value = distributeur.id;
        parentDisplay.textContent = distributeur.text;
        selectedParentDiv.classList.remove('hidden');
        searchInput.value = '';
        searchResults.classList.add('hidden');
        searchResults.innerHTML = '';
    }

    // Effacer la sélection
    function clearParent() {
        parentIdInput.value = '';
        selectedParentDiv.classList.add('hidden');
        searchInput.value = '';
        parentDisplay.textContent = '';
    }

    // Fermer les résultats quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
</script>
@endpush

@endsection
