{{-- resources/views/admin/achats/create.blade.php --}}

@extends('layouts.admin')

@section('title', 'Enregistrer un Achat')

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
                <a href="{{ route('admin.achats.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    Achats
                </a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-700 font-medium">Nouvel Achat</span>
            </nav>
        </div>

        {{-- Titre principal --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Enregistrer un nouvel achat</h1>
            <p class="mt-2 text-gray-600">Complétez le formulaire pour enregistrer un achat effectué par un distributeur.</p>
        </div>

        {{-- Messages de session --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Affichage des erreurs de validation --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Des erreurs ont été détectées :</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
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
        <form method="POST" action="{{ route('admin.achats.store') }}" class="space-y-8">
            @csrf

            {{-- Sections côte à côte sur grand écran --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Section Informations de base --}}
                <div class="bg-white shadow-lg rounded-lg overflow-hidden h-fit">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Informations de base
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Période --}}
                        <div>
                            <label for="period" class="block text-sm font-medium text-gray-700 mb-2">
                                Période <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="month"
                                name="period"
                                id="period"
                                value="{{ old('period', date('Y-m')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 @error('period') border-red-500 @enderror"
                                required
                            >
                            @error('period')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">Format: YYYY-MM (ex: 2024-12)</p>
                        </div>

                        {{-- Type d'achat (Online/Offline) --}}
                        <div>
                            <label for="online" class="block text-sm font-medium text-gray-700 mb-2">
                                Type d'achat <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="online"
                                id="online"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 @error('online') border-red-500 @enderror"
                                required
                            >
                                <option value="0" {{ old('online', '0') == '0' ? 'selected' : '' }}>
                                    Hors ligne (Magasin)
                                </option>
                                <option value="1" {{ old('online') == '1' ? 'selected' : '' }}>
                                    En ligne
                                </option>
                            </select>
                            @error('online')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Section Informations calculées --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg mt-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Information</h3>
                                    <p class="mt-2 text-sm text-blue-700">
                                        Les points unitaires, le prix unitaire et le montant total seront calculés automatiquement
                                        en fonction du produit sélectionné et de la quantité saisie.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Distributeur et Produit --}}
                <div class="bg-white shadow-lg rounded-lg overflow-hidden h-fit">
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Sélection du distributeur et du produit
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                    {{-- Distributeur avec recherche AJAX --}}
                    <div>
                        <label for="distributeur_search" class="block text-sm font-medium text-gray-700 mb-2">
                            Distributeur <span class="text-red-500">*</span>
                        </label>

                        {{-- Champ de recherche --}}
                        <div class="relative">
                            <input
                                type="text"
                                id="distributeur_search"
                                placeholder="Rechercher par matricule, nom ou prénom..."
                                class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                                autocomplete="off"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Champ caché pour l'ID du distributeur --}}
                        <input
                            type="hidden"
                            name="distributeur_id"
                            id="distributeur_id"
                            value="{{ old('distributeur_id') }}"
                            required
                        >

                        {{-- Affichage du distributeur sélectionné --}}
                        <div id="selected_distributeur" class="mt-2 p-3 bg-indigo-50 rounded-lg hidden">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-indigo-900">Distributeur sélectionné :</p>
                                    <p id="distributeur_display" class="text-sm text-indigo-700"></p>
                                </div>
                                <button type="button" onclick="clearDistributeur()" class="text-indigo-600 hover:text-indigo-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Liste des résultats de recherche --}}
                        <div id="distributeur_search_results" class="absolute z-10 w-full mt-1 bg-white rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                            <!-- Les résultats seront ajoutés ici via JavaScript -->
                        </div>

                        @error('distributeur_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Produit --}}
                    <div>
                        <label for="products_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Produit <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="products_id"
                            id="products_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('products_id') border-red-500 @enderror"
                            required
                        >
                            <option value="">-- Sélectionnez un produit --</option>
                            @foreach($products as $id => $displayName)
                                <option value="{{ $id }}" {{ old('products_id') == $id ? 'selected' : '' }}>
                                    {{ $displayName }}
                                </option>
                            @endforeach
                        </select>
                        @error('products_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Quantité --}}
                    <div>
                        <label for="qt" class="block text-sm font-medium text-gray-700 mb-2">
                            Quantité <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                name="qt"
                                id="qt"
                                value="{{ old('qt', 1) }}"
                                min="1"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('qt') border-red-500 @enderror"
                                required
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">unité(s)</span>
                            </div>
                        </div>
                        @error('qt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    </div>
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="{{ route('admin.achats.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Enregistrer l'achat
                </button>
            </div>
        </form>

        {{-- Note supplémentaire --}}
        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600">
                <strong>Note :</strong> Une fois l'achat enregistré, vous pourrez le consulter dans la liste des achats.
                Les calculs de bonus et de points seront automatiquement mis à jour selon les règles définies dans le système.
            </p>
        </div>
    </div>
</div>

{{-- Script pour améliorer l'UX (optionnel) --}}
@push('scripts')
<script>
    // Script pour la recherche AJAX du distributeur
    let distributeurSearchTimeout;
    const distributeurSearchInput = document.getElementById('distributeur_search');
    const distributeurSearchResults = document.getElementById('distributeur_search_results');
    const selectedDistributeurDiv = document.getElementById('selected_distributeur');
    const distributeurDisplay = document.getElementById('distributeur_display');
    const distributeurIdInput = document.getElementById('distributeur_id');

    // Gestion de la recherche distributeur
    distributeurSearchInput.addEventListener('input', function() {
        clearTimeout(distributeurSearchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            distributeurSearchResults.classList.add('hidden');
            distributeurSearchResults.innerHTML = '';
            return;
        }

        // Afficher un loader
        distributeurSearchResults.innerHTML = `
            <div class="p-4 text-center">
                <svg class="animate-spin h-5 w-5 mx-auto text-gray-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500">Recherche en cours...</p>
            </div>
        `;
        distributeurSearchResults.classList.remove('hidden');

        // Délai avant la recherche
        distributeurSearchTimeout = setTimeout(() => {
            searchDistributeurs(query);
        }, 300);
    });

    // Fonction de recherche AJAX pour distributeurs
    function searchDistributeurs(query) {
        fetch(`{{ route('admin.distributeurs.search') }}?q=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            distributeurSearchResults.innerHTML = '';

            if (data.length === 0) {
                distributeurSearchResults.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        Aucun distributeur trouvé
                    </div>
                `;
            } else {
                data.forEach(distributeur => {
                    const item = document.createElement('div');
                    item.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0';
                    item.innerHTML = `
                        <div class="font-medium text-gray-900">
                            #${distributeur.distributeur_id} - ${distributeur.pnom_distributeur} ${distributeur.nom_distributeur}
                        </div>
                        ${distributeur.tel_distributeur ? `<div class="text-sm text-gray-500">${distributeur.tel_distributeur}</div>` : ''}
                    `;
                    item.addEventListener('click', () => selectDistributeur(distributeur));
                    distributeurSearchResults.appendChild(item);
                });
            }
        })
        .catch(error => {
            distributeurSearchResults.innerHTML = `
                <div class="p-4 text-center text-red-500">
                    Erreur lors de la recherche
                </div>
            `;
        });
    }

    // Sélectionner un distributeur
    function selectDistributeur(distributeur) {
        distributeurIdInput.value = distributeur.id;
        distributeurDisplay.textContent = `#${distributeur.distributeur_id} - ${distributeur.pnom_distributeur} ${distributeur.nom_distributeur}`;
        selectedDistributeurDiv.classList.remove('hidden');
        distributeurSearchInput.value = '';
        distributeurSearchResults.classList.add('hidden');
        distributeurSearchResults.innerHTML = '';
    }

    // Effacer la sélection
    function clearDistributeur() {
        distributeurIdInput.value = '';
        selectedDistributeurDiv.classList.add('hidden');
        distributeurSearchInput.value = '';
    }

    // Fermer les résultats quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!distributeurSearchInput.contains(e.target) && !distributeurSearchResults.contains(e.target)) {
            distributeurSearchResults.classList.add('hidden');
        }
    });

    // Si un distributeur était sélectionné (old value), l'afficher
    @if(old('distributeur_id'))
        fetch(`{{ route('admin.distributeurs.show', old('distributeur_id')) }}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(distributeur => {
            selectDistributeur(distributeur);
        });
    @endif
</script>
@endpush

@endsection
