@extends('layouts.admin')

@section('title', 'Nouvel Achat')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Créer un nouvel achat</h3>
                </div>

                <form action="{{ route('admin.achats.store') }}" method="POST" id="achat-form">
                    @csrf

                    <div class="card-body">
                        {{-- Messages d'erreur globaux --}}
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Section 1: Sélection du distributeur --}}
                        <div class="mb-4">
                            <h5 class="mb-3">1. Sélection du distributeur</h5>

                            <div class="form-group">
                                <label class="form-label">
                                    Distributeur <span class="text-danger">*</span>
                                </label>

                                {{-- Champ de recherche --}}
                                <div class="position-relative">
                                    <input
                                        type="text"
                                        id="distributeur-search"
                                        class="form-control"
                                        placeholder="Rechercher par nom, prénom, matricule ou téléphone..."
                                        autocomplete="off"
                                    >

                                    {{-- Résultats de recherche --}}
                                    <div id="search-results" class="hidden position-absolute w-100 mt-1 bg-white rounded shadow-lg" style="max-height: 300px; overflow-y: auto; z-index: 1000; display: none;">
                                        <!-- Les résultats seront affichés ici -->
                                    </div>
                                </div>

                                {{-- Distributeur sélectionné --}}
                                <div id="selected-distributeur" class="mt-2" style="display: none;">
                                    <!-- Le distributeur sélectionné sera affiché ici -->
                                </div>

                                {{-- Input caché pour l'ID du distributeur --}}
                                <input type="hidden" name="distributeur_id" id="distributeur_id" value="{{ old('distributeur_id') }}" required>

                                @error('distributeur_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Section 2: Détails du produit --}}
                        <div class="mb-4">
                            <h5 class="mb-3">2. Détails du produit</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="products_id" class="form-label">
                                            Produit <span class="text-danger">*</span>
                                        </label>
                                        <select name="products_id" id="products_id" class="form-control @error('products_id') is-invalid @enderror" required>
                                            <option value="">-- Sélectionner un produit --</option>
                                            @if(isset($products))
                                                @foreach($products as $product)
                                                    @php
                                                        // Initialiser les variables
                                                        $id = null;
                                                        $nom = 'Produit';
                                                        $prix = 0;
                                                        $pv = 0;

                                                        // Si c'est un objet
                                                        if(is_object($product)) {
                                                            $id = $product->id ?? null;
                                                            $nom = $product->nom_produit ?? 'Produit';
                                                            $prix = $product->prix_product ?? 0;
                                                            // Gérer la relation pointvaleur
                                                            if(isset($product->pointvaleur) && is_object($product->pointvaleur)) {
                                                                $pv = $product->pointvaleur->numbers ?? 0;
                                                            }
                                                        }
                                                        // Si c'est un tableau
                                                        elseif(is_array($product)) {
                                                            $id = $product['id'] ?? null;
                                                            $nom = $product['nom_produit'] ?? 'Produit';
                                                            $prix = $product['prix_product'] ?? 0;
                                                            // Gérer pointvaleur dans un tableau
                                                            if(isset($product['pointvaleur'])) {
                                                                if(is_array($product['pointvaleur'])) {
                                                                    $pv = $product['pointvaleur']['numbers'] ?? 0;
                                                                } elseif(is_numeric($product['pointvaleur'])) {
                                                                    $pv = $product['pointvaleur'];
                                                                }
                                                            }
                                                        }
                                                    @endphp

                                                    @if($id)
                                                        <option value="{{ $id }}"
                                                            data-price="{{ $prix }}"
                                                            data-pv="{{ $pv }}"
                                                            {{ old('products_id') == $id ? 'selected' : '' }}>
                                                            {{ $nom }} - {{ number_format($prix, 0, ',', ' ') }} FCFA
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('products_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="qt" class="form-label">
                                            Quantité <span class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                               name="qt"
                                               id="qt"
                                               class="form-control @error('qt') is-invalid @enderror"
                                               value="{{ old('qt', 1) }}"
                                               min="1"
                                               required>
                                        @error('qt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="period" class="form-label">
                                            Période <span class="text-danger">*</span>
                                        </label>
                                        <input type="month"
                                               name="period"
                                               id="period"
                                               class="form-control @error('period') is-invalid @enderror"
                                               value="{{ old('period', date('Y-m')) }}"
                                               required>
                                        @error('period')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Champs cachés pour compatibilité avec l'ancien système --}}
                            <input type="hidden" name="online" value="off">

                            {{-- Si votre ancien système utilise ces champs, décommentez-les --}}
                            {{--
                            <input type="hidden" name="value_pv" id="value_pv" value="">
                            <input type="hidden" name="idproduit" id="idproduit" value="">
                            <input type="hidden" name="value" id="value" value="">
                            <input type="hidden" name="prix_product" id="prix_product" value="">
                            <input type="hidden" name="pointvaleur_id" id="pointvaleur_id" value="">
                            <input type="hidden" name="created_at" id="created_at" value="{{ date('d/m/Y') }}">
                            --}}
                        </div>

                        <hr class="my-4">

                        {{-- Section 3: Récapitulatif --}}
                        <div class="mb-4">
                            <h5 class="mb-3">3. Récapitulatif</h5>

                            <div class="bg-light p-3 rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Prix unitaire:</strong></p>
                                        <p class="h5 mb-0"><span id="prix-unitaire">0</span> FCFA</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Montant total:</strong></p>
                                        <p class="h5 mb-0 text-primary"><span id="montant-total">0</span> FCFA</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Points valeur (PV):</strong></p>
                                        <p class="h5 mb-0 text-success"><span id="pv-total">0</span> PV</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 4: Informations complémentaires (si nécessaire) --}}
                        <div class="mb-4">
                            <div class="form-group">
                                <label for="notes" class="form-label">Notes (optionnel)</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.achats.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer l'achat
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Meta CSRF Token --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const searchInput = document.getElementById('distributeur-search');
    const searchResults = document.getElementById('search-results');
    const selectedDistributeur = document.getElementById('selected-distributeur');
    const distributeurIdInput = document.getElementById('distributeur_id');
    const productSelect = document.getElementById('products_id');
    const quantiteInput = document.getElementById('qt');
    let searchTimeout;

    // === GESTION DE LA RECHERCHE DE DISTRIBUTEUR ===
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            // Afficher un loader
            searchResults.innerHTML = `
                <div class="p-3 text-center">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="sr-only">Recherche...</span>
                    </div>
                    <p class="mb-0 mt-2 small text-muted">Recherche en cours...</p>
                </div>
            `;
            searchResults.style.display = 'block';

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('admin.distributeurs.search') }}?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Erreur réseau');
                    return response.json();
                })
                .then(data => {
                    searchResults.innerHTML = '';

                    const results = data.results || data;

                    if (!results || results.length === 0) {
                        searchResults.innerHTML = '<div class="p-3 text-muted">Aucun distributeur trouvé</div>';
                    } else {
                        results.forEach(distributeur => {
                            const div = document.createElement('div');
                            div.className = 'p-3 border-bottom cursor-pointer hover-bg-light';
                            div.style.cursor = 'pointer';

                            const displayText = distributeur.text || `#${distributeur.distributeur_id} - ${distributeur.pnom_distributeur} ${distributeur.nom_distributeur}`;

                            div.innerHTML = `
                                <div class="font-weight-bold">${displayText}</div>
                                ${distributeur.tel_distributeur ? `<small class="text-muted">Tél: ${distributeur.tel_distributeur}</small>` : ''}
                            `;

                            div.addEventListener('click', function() {
                                selectDistributeur(distributeur);
                            });

                            div.addEventListener('mouseenter', function() {
                                this.style.backgroundColor = '#f8f9fa';
                            });

                            div.addEventListener('mouseleave', function() {
                                this.style.backgroundColor = '';
                            });

                            searchResults.appendChild(div);
                        });
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    searchResults.innerHTML = `
                        <div class="p-3 text-danger">
                            <i class="fas fa-exclamation-triangle"></i> Erreur lors de la recherche
                        </div>
                    `;
                });
            }, 300);
        });
    }

    // Fonction pour sélectionner un distributeur
    function selectDistributeur(distributeur) {
        // Utiliser distributeur_id au lieu de id pour le champ hidden
        distributeurIdInput.value = distributeur.distributeur_id || distributeur.id;
        searchInput.value = '';
        searchResults.style.display = 'none';

        const displayText = distributeur.text || `#${distributeur.distributeur_id} - ${distributeur.pnom_distributeur} ${distributeur.nom_distributeur}`;

        selectedDistributeur.innerHTML = `
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <div>
                    <strong>Distributeur sélectionné:</strong><br>
                    ${displayText}
                    ${distributeur.tel_distributeur ? `<br><small>Tél: ${distributeur.tel_distributeur}</small>` : ''}
                </div>
                <button type="button" class="btn btn-sm btn-outline-info" onclick="clearDistributeur()">
                    <i class="fas fa-times"></i> Changer
                </button>
            </div>
        `;
        selectedDistributeur.style.display = 'block';
    }

    // Fonction pour effacer la sélection
    window.clearDistributeur = function() {
        distributeurIdInput.value = '';
        selectedDistributeur.innerHTML = '';
        selectedDistributeur.style.display = 'none';
        searchInput.value = '';
    };

    // Fermer les résultats si on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // === CALCUL DU MONTANT TOTAL ET PV ===
    function updateTotals() {
        if (!productSelect || !quantiteInput) return;

        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (!selectedOption || selectedOption.value === '') {
            document.getElementById('prix-unitaire').textContent = '0';
            document.getElementById('montant-total').textContent = '0';
            document.getElementById('pv-total').textContent = '0';
            return;
        }

        const prix = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const pv = parseFloat(selectedOption.getAttribute('data-pv')) || 0;
        const quantite = parseInt(quantiteInput.value) || 0;

        const montantTotal = prix * quantite;
        const pvTotal = pv * quantite;

        document.getElementById('prix-unitaire').textContent = prix.toLocaleString('fr-FR');
        document.getElementById('montant-total').textContent = montantTotal.toLocaleString('fr-FR');
        document.getElementById('pv-total').textContent = pvTotal.toLocaleString('fr-FR');

        // Mettre à jour les champs cachés si ils existent (pour compatibilité)
        const valuePvInput = document.getElementById('value_pv');
        const idproduitInput = document.getElementById('idproduit');
        const valueInput = document.getElementById('value');
        const prixProductInput = document.getElementById('prix_product');

        if (valuePvInput) valuePvInput.value = pvTotal;
        if (idproduitInput) idproduitInput.value = selectedOption.value;
        if (valueInput) valueInput.value = montantTotal;
        if (prixProductInput) prixProductInput.value = prix;
    }

    // Écouteurs d'événements pour le calcul
    if (productSelect) {
        productSelect.addEventListener('change', updateTotals);
    }

    if (quantiteInput) {
        quantiteInput.addEventListener('input', updateTotals);
    }

    // Calcul initial
    updateTotals();

    // === VALIDATION DU FORMULAIRE ===
    const form = document.getElementById('achat-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!distributeurIdInput.value) {
                e.preventDefault();
                alert('Veuillez sélectionner un distributeur');
                searchInput.focus();
                return false;
            }

            if (!productSelect.value) {
                e.preventDefault();
                alert('Veuillez sélectionner un produit');
                productSelect.focus();
                return false;
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .hover-bg-light:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush
