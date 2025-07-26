{{-- resources/views/admin/achats/session/summary.blade.php --}}
@extends('layouts.admin')

@section('title', 'Session d\'achats en cours')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        <span class="text-gray-500 ml-1 md:ml-2">Session en cours</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- En-tête --}}
        <div class="sm:flex sm:items-center mb-8">
            <div class="sm:flex-auto">
                <h1 class="text-3xl font-bold text-gray-900">Session d'achats en cours</h1>
                <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6">
                    <div class="mt-2 flex items-center text-gray-600">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ $session['distributeur_info'] }}
                    </div>
                    <div class="mt-2 flex items-center text-gray-600">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::parse($session['date'])->format('d/m/Y') }}
                    </div>
                    <div class="mt-2 flex items-center text-gray-600">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Démarrée à {{ \Carbon\Carbon::parse($session['created_at'])->format('H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Colonne principale - Liste des produits --}}
            <div class="lg:col-span-2">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Produits dans la session
                        </h2>
                    </div>

                    @if(empty($session['items']))
                        <div class="px-4 py-12 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Panier vide</h3>
                            <p class="mt-1 text-gray-500">Commencez par ajouter des produits à votre session.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qté</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Prix U.</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                                        <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($session['items'] as $index => $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $item['product_name'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                {{ $item['quantity'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                                {{ number_format($item['prix_unitaire'], 0, ',', ' ') }} F
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                                {{ number_format($item['montant_total'], 0, ',', ' ') }} F
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 text-right font-medium">
                                                {{ $item['points_total'] }} PV
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button class="text-red-600 hover:text-red-900 transition-colors duration-200 remove-item"
                                                        data-index="{{ $index }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <th colspan="3" class="px-6 py-4 text-left text-sm font-bold text-gray-900 uppercase">
                                            Total général
                                        </th>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                            {{ number_format($session['totaux']['montant'], 0, ',', ' ') }} F
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600 text-right">
                                            {{ $session['totaux']['points'] }} PV
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <form action="{{ route('admin.achats.session.validate') }}" method="POST" class="inline-block" id="validate-form">
                                @csrf
                                <button type="submit"
                                        class="w-full sm:w-auto px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed {{ empty($session['items']) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ empty($session['items']) ? 'disabled' : '' }}>
                                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Valider tous les achats
                                </button>
                            </form>

                            <form action="{{ route('admin.achats.session.cancel') }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Êtes-vous sûr de vouloir annuler cette session ?')"
                                        class="w-full sm:w-auto px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Annuler la session
                                </button>
                            </form>

                            <a href="{{ route('admin.achats.index') }}"
                               class="w-full sm:w-auto px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 text-center transition-colors duration-200">
                                Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne latérale --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Formulaire d'ajout --}}
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ajouter un produit
                        </h3>
                    </div>
                    <div class="p-6">
                        <form id="add-item-form">
                            <div class="space-y-4">
                                <div>
                                    <label for="product_search" class="block text-sm font-medium text-gray-700 mb-2">
                                        Rechercher un produit
                                    </label>
                                    <div class="relative">
                                        <input type="text"
                                               id="product_search"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                               placeholder="Nom du produit..."
                                               autocomplete="off">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </div>

                                        {{-- Liste des produits --}}
                                        <div id="product-list" class="absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto z-50" style="display: none;">
                                            @foreach($products as $product)
                                                <div class="product-item px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                                                     data-id="{{ $product->id }}"
                                                     data-name="{{ $product->nom }}"
                                                     data-prix="{{ $product->prix_product }}"
                                                     data-points="{{ $product->pointValeur ? $product->pointValeur->numbers : 0 }}">
                                                    <div class="font-medium text-gray-900">{{ $product->nom }}</div>
                                                    <div class="text-sm text-gray-600">
                                                        {{ number_format($product->prix_product, 0, ',', ' ') }} F -
                                                        {{ $product->pointValeur ? $product->pointValeur->numbers : 0 }} PV
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <input type="hidden" name="product_id" id="product_id" required>
                                </div>

                                {{-- Produit sélectionné --}}
                                <div id="selected-product" class="hidden">
                                    <!-- Le produit sélectionné sera affiché ici -->
                                </div>

                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                        Quantité
                                    </label>
                                    <input type="number"
                                           name="quantity"
                                           id="quantity"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           min="1"
                                           value="1"
                                           required>
                                </div>

                                <div id="preview" class="hidden">
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <p class="text-sm text-blue-800 font-medium">Aperçu :</p>
                                        <p class="text-gray-900">Montant : <span class="font-bold" id="preview-montant">0</span> F</p>
                                        <p class="text-gray-900">Points : <span class="font-bold text-blue-600" id="preview-points">0</span> PV</p>
                                    </div>
                                </div>

                                <button type="submit"
                                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Ajouter au panier
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Résumé --}}
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">Résumé de la session</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-600">Articles</dt>
                                <dd class="text-lg font-semibold text-gray-900" id="summary-items">
                                    {{ $session['totaux']['nb_items'] }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-600">Montant total</dt>
                                <dd class="text-xl font-bold text-gray-900" id="summary-montant">
                                    {{ number_format($session['totaux']['montant'], 0, ',', ' ') }} F
                                </dd>
                            </div>
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <dt class="text-gray-900 font-medium">Points totaux</dt>
                                <dd class="text-xl font-bold text-blue-600" id="summary-points">
                                    {{ $session['totaux']['points'] }} PV
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let selectedProduct = null;
    const productSearch = document.getElementById('product_search');
    const productList = document.getElementById('product-list');
    const selectedProductDiv = document.getElementById('selected-product');
    const productIdInput = document.getElementById('product_id');

    // Recherche de produits
    productSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const items = productList.querySelectorAll('.product-item');
        let hasResults = false;

        items.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            if (name.includes(query)) {
                item.style.display = 'block';
                hasResults = true;
            } else {
                item.style.display = 'none';
            }
        });

        productList.style.display = query.length > 0 && hasResults ? 'block' : 'none';
    });

    // Sélection d'un produit
    document.querySelectorAll('.product-item').forEach(item => {
        item.addEventListener('click', function() {
            selectedProduct = {
                id: this.getAttribute('data-id'),
                name: this.getAttribute('data-name'),
                prix: parseFloat(this.getAttribute('data-prix')),
                points: parseInt(this.getAttribute('data-points'))
            };

            productIdInput.value = selectedProduct.id;
            productSearch.value = '';
            productList.style.display = 'none';

            selectedProductDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600 font-medium">Produit sélectionné</p>
                        <p class="text-gray-900 font-semibold">${selectedProduct.name}</p>
                        <p class="text-sm text-gray-600">${selectedProduct.prix.toLocaleString('fr-FR')} F - ${selectedProduct.points} PV</p>
                    </div>
                    <button type="button" onclick="resetProduct()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            selectedProductDiv.classList.remove('hidden');
            updatePreview();
        });
    });

    // Réinitialiser le produit
    window.resetProduct = function() {
        selectedProduct = null;
        productIdInput.value = '';
        selectedProductDiv.classList.add('hidden');
        productSearch.value = '';
        document.getElementById('preview').classList.add('hidden');
    };

    // Mise à jour de l'aperçu
    function updatePreview() {
        const quantity = parseInt(document.getElementById('quantity').value) || 0;

        if (selectedProduct && quantity > 0) {
            const montant = selectedProduct.prix * quantity;
            const points = selectedProduct.points * quantity;

            document.getElementById('preview-montant').textContent = montant.toLocaleString('fr-FR');
            document.getElementById('preview-points').textContent = points;
            document.getElementById('preview').classList.remove('hidden');
        } else {
            document.getElementById('preview').classList.add('hidden');
        }
    }

    document.getElementById('quantity').addEventListener('input', updatePreview);

    // Fermer la liste si on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!productSearch.contains(e.target) && !productList.contains(e.target)) {
            productList.style.display = 'none';
        }
    });

    // Ajouter un produit
    $('#add-item-form').on('submit', function(e) {
        e.preventDefault();

        if (!selectedProduct) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Veuillez sélectionner un produit'
            });
            return;
        }

        $.ajax({
            url: '{{ route("admin.achats.session.add-item") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: $('#product_id').val(),
                quantity: $('#quantity').val()
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Produit ajouté',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                const error = xhr.responseJSON.error || 'Erreur lors de l\'ajout';
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error
                });
            }
        });
    });

    // Retirer un produit
    $('.remove-item').on('click', function() {
        const index = $(this).data('index');

        Swal.fire({
            title: 'Confirmer la suppression ?',
            text: "Ce produit sera retiré de la session",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Oui, retirer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.achats.session.remove-item") }}',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        index: index
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Produit retiré',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Impossible de retirer le produit'
                        });
                    }
                });
            }
        });
    });

    // Confirmation avant validation
    $('#validate-form').on('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Valider tous les achats ?',
            html: `
                <div class="text-left">
                    <p class="mb-2">Vous allez créer <strong>${{ $session['totaux']['nb_items'] }}</strong> achats</p>
                    <p class="mb-2">Montant total : <strong>{{ number_format($session['totaux']['montant'], 0, ',', ' ') }} F</strong></p>
                    <p>Points totaux : <strong>{{ $session['totaux']['points'] }} PV</strong></p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Oui, valider',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>
@endpush
