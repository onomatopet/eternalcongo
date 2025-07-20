{{-- resources/views/admin/achats/edit.blade.php --}}

@extends('layouts.admin')

@section('title', 'Modifier un Achat')

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
                <a href="{{ route('admin.achats.show', $achat) }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    Achat #{{ $achat->id }}
                </a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-700 font-medium">Modifier</span>
            </nav>
        </div>

        {{-- Titre principal --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Modifier l'achat #{{ $achat->id }}</h1>
            <p class="mt-2 text-gray-600">Modifiez les informations de l'achat ci-dessous.</p>
        </div>

        {{-- Messages d'erreur --}}
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
        <form action="{{ route('admin.achats.update', $achat) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Informations de base --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Informations de l'achat</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Période --}}
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700">
                            Période <span class="text-red-500">*</span>
                        </label>
                        <select name="period"
                                id="period"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('period') border-red-300 @enderror">
                            @foreach($periods as $value => $label)
                                <option value="{{ $value }}" {{ old('period', $achat->period) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('period')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type d'achat --}}
                    <div>
                        <label for="online" class="block text-sm font-medium text-gray-700">
                            Type d'achat
                        </label>
                        <select name="online"
                                id="online"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="0" {{ old('online', $achat->online) == '0' ? 'selected' : '' }}>Achat physique</option>
                            <option value="1" {{ old('online', $achat->online) == '1' ? 'selected' : '' }}>Achat en ligne</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Sélection du distributeur --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Distributeur</h2>

                <div class="relative">
                    <label for="distributeur_search" class="block text-sm font-medium text-gray-700 mb-2">
                        Rechercher un distributeur <span class="text-red-500">*</span>
                    </label>

                    {{-- Distributeur actuel --}}
                    <div class="mb-3 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <strong>Distributeur actuel :</strong>
                            #{{ $achat->distributeur->distributeur_id }} -
                            {{ $achat->distributeur->pnom_distributeur }} {{ $achat->distributeur->nom_distributeur }}
                        </p>
                    </div>

                    {{-- Champ caché pour l'ID --}}
                    <input type="hidden" name="distributeur_id" id="distributeur_id" value="{{ old('distributeur_id', $achat->distributeur_id) }}" required>

                    {{-- Champ de recherche --}}
                    <div class="relative">
                        <input
                            type="text"
                            id="distributeur_search"
                            placeholder="Tapez le nom, prénom ou matricule..."
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('distributeur_id') border-red-500 @enderror"
                            autocomplete="off"
                            value="{{ $achat->distributeur->distributeur_id }} - {{ $achat->distributeur->pnom_distributeur }} {{ $achat->distributeur->nom_distributeur }}"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Zone de résultats --}}
                    <div id="distributeur_search_results" class="absolute z-10 w-full mt-1 bg-white rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                        <!-- Les résultats seront ajoutés ici via JavaScript -->
                    </div>

                    @error('distributeur_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Sélection du produit et quantité --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Produit et quantité</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Produit --}}
                    <div>
                        <label for="products_id" class="block text-sm font-medium text-gray-700">
                            Produit <span class="text-red-500">*</span>
                        </label>
                        <select name="products_id"
                                id="products_id"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('products_id') border-red-300 @enderror">
                            <option value="">-- Sélectionnez un produit --</option>
                            @foreach($products as $product)
                                <option value="{{ $product['id'] }}"
                                        data-price="{{ $product['price'] }}"
                                        data-points="{{ $product['points'] }}"
                                        {{ old('products_id', $achat->products_id) == $product['id'] ? 'selected' : '' }}>
                                    {{ $product['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('products_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Quantité --}}
                    <div>
                        <label for="qt" class="block text-sm font-medium text-gray-700">
                            Quantité <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="qt"
                               id="qt"
                               value="{{ old('qt', $achat->qt) }}"
                               min="1"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('qt') border-red-300 @enderror"
                               placeholder="1">
                        @error('qt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Aperçu du calcul --}}
                <div id="calculation_preview" class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Aperçu du calcul</h3>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs text-gray-500">Prix unitaire</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900" id="preview_price">
                                {{ number_format($achat->prix_unitaire_achat, 0, ',', ' ') }} XAF
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Montant total</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900" id="preview_total">
                                {{ number_format($achat->montant_total_ligne, 0, ',', ' ') }} XAF
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Points totaux</dt>
                            <dd class="mt-1 text-sm font-medium text-green-600" id="preview_points">
                                {{ $achat->points_unitaire_achat * $achat->qt }} PV
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.achats.show', $achat) }}"
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Recherche de distributeur
    let searchTimeout;
    const searchInput = document.getElementById('distributeur_search');
    const searchResults = document.getElementById('distributeur_search_results');
    const distributeurIdInput = document.getElementById('distributeur_id');

    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();

        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('admin.distributeurs.search') }}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';

                if (data.length === 0) {
                    searchResults.innerHTML = '<div class="p-3 text-sm text-gray-500">Aucun résultat trouvé</div>';
                } else {
                    data.forEach(distributeur => {
                        const div = document.createElement('div');
                        div.className = 'p-3 hover:bg-gray-50 cursor-pointer transition-colors duration-150';
                        div.innerHTML = `
                            <div class="font-medium text-sm text-gray-900">#${distributeur.distributeur_id} - ${distributeur.pnom_distributeur} ${distributeur.nom_distributeur}</div>
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

            previewPrice.textContent = new Intl.NumberFormat('fr-FR').format(price) + ' XAF';
            previewTotal.textContent = new Intl.NumberFormat('fr-FR').format(total) + ' XAF';
            previewPoints.textContent = totalPoints + ' PV';
        }
    }

    productSelect.addEventListener('change', updateCalculation);
    quantityInput.addEventListener('input', updateCalculation);
</script>
@endpush
@endsection
