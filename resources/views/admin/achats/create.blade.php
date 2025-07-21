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
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                            <select
                                name="period"
                                id="period"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('period') border-red-500 @enderror"
                                required>
                                <option value="">Sélectionner une période</option>
                                @foreach($periods as $periodValue => $periodLabel)
                                    <option value="{{ $periodValue }}" {{ old('period') == $periodValue ? 'selected' : '' }}>
                                        {{ $periodLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('period')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Format: YYYY-MM (ex: 2025-07)</p>
                        </div>

                        {{-- Recherche distributeur --}}
                        <div>
                            <label for="distributeur_search" class="block text-sm font-medium text-gray-700 mb-2">
                                Distributeur <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="distributeur_search"
                                    placeholder="Rechercher par nom, prénom ou matricule..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                    autocomplete="off">
                                <input type="hidden" name="distributeur_id" id="distributeur_id" value="{{ old('distributeur_id') }}">

                                {{-- Résultats de recherche --}}
                                <div id="search_results" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    {{-- Les résultats seront injectés ici via JavaScript --}}
                                </div>
                            </div>
                            @error('distributeur_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Achat en ligne --}}
                        <div>
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="online"
                                    id="online"
                                    value="1"
                                    {{ old('online') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="online" class="ml-2 block text-sm text-gray-900">
                                    Achat effectué en ligne
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Cochez si l'achat a été effectué via la plateforme en ligne</p>
                        </div>
                    </div>
                </div>

                {{-- Section Produit et calculs --}}
                <div class="bg-white shadow-lg rounded-lg overflow-hidden h-fit">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                            </svg>
                            Produit et quantité
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Sélection produit --}}
                        <div>
                            <label for="products_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Produit <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="products_id"
                                id="products_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('products_id') border-red-500 @enderror"
                                required>
                                <option value="">Sélectionner un produit</option>
                                @foreach($products as $product)
                                    <option value="{{ $product['id'] }}"
                                            data-price="{{ $product['price'] }}"
                                            data-points="{{ $product['points'] }}"
                                            {{ old('products_id') == $product['id'] ? 'selected' : '' }}>
                                        {{ $product['name'] }} - {{ number_format($product['price'], 0, ',', ' ') }} FCFA ({{ $product['points'] }} PV)
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
                            <input
                                type="number"
                                name="qt"
                                id="qt"
                                min="1"
                                value="{{ old('qt', 1) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('qt') border-red-500 @enderror"
                                required>
                            @error('qt')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Aperçu des calculs --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Aperçu des calculs</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Prix unitaire :</span>
                                    <span class="font-medium" id="preview_price">0 FCFA</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Points par unité :</span>
                                    <span class="font-medium text-blue-600" id="preview_points_unit">0 PV</span>
                                </div>
                                <div class="border-t pt-2 mt-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total points :</span>
                                        <span class="font-medium text-blue-600" id="preview_points">0 PV</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Montant total :</span>
                                        <span class="font-semibold text-green-600 text-lg" id="preview_total">0 FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.achats.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer l'achat
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts pour la recherche de distributeur et calculs --}}
@push('scripts')
<script>
    // Recherche de distributeur
    let searchTimeout;
    const searchInput = document.getElementById('distributeur_search');
    const searchResults = document.getElementById('search_results');
    const distributeurIdInput = document.getElementById('distributeur_id');

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

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('admin.distributeurs.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';
                if (data.length === 0) {
                    searchResults.innerHTML = '<div class="p-3 text-sm text-gray-500">Aucun distributeur trouvé</div>';
                } else {
                    data.forEach(distributeur => {
                        const div = document.createElement('div');
                        div.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0';
                        div.innerHTML = `
                            <div class="text-sm font-medium text-gray-900">${distributeur.distributeur_id} - ${distributeur.pnom_distributeur} ${distributeur.nom_distributeur}</div>
                            ${distributeur.tel_distributeur ? `<div class="text-xs text-gray-500">${distributeur.tel_distributeur}</div>` : ''}
                        `;
                        div.addEventListener('click', () => selectDistributeur(distributeur));
                        searchResults.appendChild(div);
                    });
                }
                searchResults.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Erreur lors de la recherche:', error);
                searchResults.innerHTML = '<div class="p-3 text-sm text-red-500">Erreur lors de la recherche</div>';
                searchResults.classList.remove('hidden');
            });
        }, 300);
    });

    function selectDistributeur(distributeur) {
        distributeurIdInput.value = distributeur.id;
        searchInput.value = `${distributeur.distributeur_id} - ${distributeur.pnom_distributeur} ${distributeur.nom_distributeur}`;
        searchResults.classList.add('hidden');
    }

    // Fermer les résultats quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });

    // Calcul automatique
    const productSelect = document.getElementById('products_id');
    const quantityInput = document.getElementById('qt');
    const previewPrice = document.getElementById('preview_price');
    const previewPointsUnit = document.getElementById('preview_points_unit');
    const previewTotal = document.getElementById('preview_total');
    const previewPoints = document.getElementById('preview_points');

    function updateCalculation() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const quantity = parseInt(quantityInput.value) || 0;

        if (selectedOption && selectedOption.value) {
            const price = parseFloat(selectedOption.dataset.price) || 0;
            const points = parseInt(selectedOption.dataset.points) || 0;
            const total = price * quantity;
            const totalPoints = points * quantity;

            previewPrice.textContent = new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
            previewPointsUnit.textContent = points + ' PV';
            previewTotal.textContent = new Intl.NumberFormat('fr-FR').format(total) + ' FCFA';
            previewPoints.textContent = totalPoints + ' PV';
        } else {
            previewPrice.textContent = '0 FCFA';
            previewPointsUnit.textContent = '0 PV';
            previewTotal.textContent = '0 FCFA';
            previewPoints.textContent = '0 PV';
        }
    }

    productSelect.addEventListener('change', updateCalculation);
    quantityInput.addEventListener('input', updateCalculation);

    // Calcul initial si des valeurs sont déjà sélectionnées
    updateCalculation();
</script>
@endpush
@endsection
