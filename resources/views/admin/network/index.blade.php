@extends('layouts.admin')

@section('title', 'Export Réseau Distributeur')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- En-tête --}}
        <div class="bg-white rounded-lg shadow-sm px-6 py-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Export Réseau Distributeur</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Générez un rapport complet du réseau d'un distributeur pour une période donnée
                    </p>
                </div>
                <a href="{{ route('admin.distributeurs.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        {{-- Formulaire moderne --}}
        <form action="{{ route('admin.network.export') }}" method="GET" id="networkExportForm">
            <div class="space-y-6">
                {{-- Étape 1: Sélection du distributeur --}}
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 rounded-t-lg">
                        <div class="flex items-center">
                            <span class="flex-shrink-0 w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-white font-semibold mr-3">1</span>
                            <h2 class="text-lg font-semibold text-white">Sélectionner un distributeur</h2>
                        </div>
                    </div>

                    <div class="p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Recherchez par matricule, nom ou prénom
                        </label>

                        {{-- Conteneur de recherche avec position relative --}}
                        <div class="relative">
                            {{-- Barre de recherche --}}
                            <div class="relative">
                                <input type="text"
                                       id="distributeur_search"
                                       placeholder="Ex: 1234567, Dupont, Jean..."
                                       class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       autocomplete="off">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                {{-- Spinner de chargement --}}
                                <div id="search_spinner" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>

                            {{-- Résultats de recherche - Positionnement absolu dans le conteneur relatif --}}
                            <div id="search_results"
                                 class="hidden absolute mt-1 w-full bg-white rounded-lg shadow-xl border border-gray-200 max-h-80 overflow-y-auto z-50">
                            </div>

                            {{-- Champ caché pour l'ID --}}
                            <input type="hidden" name="distributeur_id" id="distributeur_id" required>
                        </div>

                        {{-- Message d'aide --}}
                        <p class="mt-2 text-sm text-gray-500">
                            Commencez à taper pour rechercher parmi les {{ number_format($totalDistributeurs ?? 5000) }} distributeurs
                        </p>

                        {{-- Distributeur sélectionné avec style carte --}}
                        <div id="selected_distributeur" class="hidden mt-4">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm text-gray-600">Distributeur sélectionné</div>
                                            <div id="selected_text" class="text-lg font-semibold text-gray-900"></div>
                                        </div>
                                    </div>
                                    <button type="button" onclick="clearSelection()"
                                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        @error('distributeur_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Étape 2: Sélection de la période --}}
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 rounded-t-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-white/20 p-3 rounded-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-xl font-semibold text-white">Étape 2 : Choisir la période</h2>
                                <p class="text-green-100 text-sm">Saisissez la période au format AAAA-MM</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- Champ de recherche de période avec autocomplétion --}}
                        <div class="relative" x-data="periodSearch()">
                            <label for="period-input" class="block text-sm font-medium text-gray-700 mb-2">
                                Période (format: AAAA-MM) :
                            </label>

                            <div class="relative">
                                <input type="text"
                                       id="period-input"
                                       name="period"
                                       x-model="periodValue"
                                       @input="searchPeriods"
                                       @focus="showSuggestions = true"
                                       @keydown.escape="showSuggestions = false"
                                       class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Ex: 2025-06"
                                       pattern="\d{4}-\d{2}"
                                       maxlength="7"
                                       autocomplete="off">

                                {{-- Icône calendrier --}}
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- Liste des suggestions --}}
                            <div x-show="showSuggestions && suggestions.length > 0"
                                 x-transition
                                 @click.away="showSuggestions = false"
                                 class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-auto">

                                <div x-show="loading" class="p-3 text-center text-gray-500">
                                    <svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </div>

                                <template x-for="period in suggestions" :key="period.value">
                                    <button type="button"
                                            @click="selectPeriod(period)"
                                            class="w-full px-4 py-2 text-left hover:bg-blue-50 focus:bg-blue-50 focus:outline-none transition-colors duration-150">
                                        <div class="font-medium text-gray-900" x-text="period.value"></div>
                                        <div class="text-sm text-gray-500" x-text="period.label"></div>
                                    </button>
                                </template>
                            </div>

                            {{-- Message d'aide --}}
                            <p class="mt-2 text-sm text-gray-500">
                                Saisissez une période existante ou commencez à taper pour voir les suggestions
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Options et soumission --}}
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="p-6">
                        {{-- Options d'export --}}
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Options d'export</h3>
                            <div class="space-y-3">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" name="include_inactive" value="1" checked
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                                        Inclure les distributeurs inactifs
                                    </span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" name="include_summary" value="1" checked
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                                        Inclure le résumé statistique
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Bouton de soumission moderne --}}
                        <button type="submit"
                                id="submit_button"
                                disabled
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span id="button_text">Générer l'aperçu du réseau</span>
                        </button>

                        {{-- Info sur la limite --}}
                        <p class="mt-3 text-center text-xs text-gray-500">
                            Maximum 5 000 distributeurs par export • Temps de traitement : 5-30 secondes
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    /* Style pour les options de période sélectionnées */
    .period-option input:checked + div {
        border-color: rgb(59 130 246);
        background-color: rgb(239 246 255);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Animation pour les résultats de recherche */
    #search_results > div {
        animation: slideIn 0.2s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let searchTimeout;
    let selectedDistributeur = null;

    // Éléments DOM
    const searchInput = document.getElementById('distributeur_search');
    const searchResults = document.getElementById('search_results');
    const searchSpinner = document.getElementById('search_spinner');
    const selectedDiv = document.getElementById('selected_distributeur');
    const selectedText = document.getElementById('selected_text');
    const distributeurIdInput = document.getElementById('distributeur_id');
    const submitButton = document.getElementById('submit_button');
    const buttonText = document.getElementById('button_text');

    // Recherche de distributeurs avec debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.classList.add('hidden');
            searchResults.innerHTML = '';
            searchSpinner.classList.add('hidden');
            return;
        }

        // Afficher le spinner
        searchSpinner.classList.remove('hidden');

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('admin.network.search.distributeurs') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchSpinner.classList.add('hidden');

                    if (data.length === 0) {
                        searchResults.innerHTML = `
                            <div class="p-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Aucun distributeur trouvé pour "${query}"</p>
                            </div>
                        `;
                    } else {
                        searchResults.innerHTML = data.map(dist => `
                            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors duration-150 border-b border-gray-100 last:border-b-0"
                                 onclick="selectDistributeur('${dist.distributeur_id}', '${dist.display_name}')">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                            <span class="text-sm font-bold text-white">
                                                ${dist.nom_distributeur.charAt(0)}${dist.pnom_distributeur.charAt(0)}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    ${dist.distributeur_id}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    ${dist.nom_distributeur} ${dist.pnom_distributeur}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-yellow-600">
                                                    ${dist.grade_display}
                                                </div>
                                                ${dist.id_distrib_parent ? `
                                                    <div class="text-xs text-gray-400">
                                                        Parent: ${dist.id_distrib_parent}
                                                    </div>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    }
                    searchResults.classList.remove('hidden');
                })
                .catch(error => {
                    searchSpinner.classList.add('hidden');
                    console.error('Erreur:', error);
                    searchResults.innerHTML = `
                        <div class="p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-2 text-sm text-red-600">Erreur lors de la recherche</p>
                            <p class="text-xs text-gray-500">Veuillez réessayer</p>
                        </div>
                    `;
                    searchResults.classList.remove('hidden');
                });
        }, 300);
    });

    // Sélection d'un distributeur
    function selectDistributeur(id, text) {
        selectedDistributeur = id;
        distributeurIdInput.value = id;
        selectedText.textContent = text;

        searchInput.value = '';
        searchResults.classList.add('hidden');
        selectedDiv.classList.remove('hidden');

        checkFormValidity();
    }

    // Effacer la sélection
    function clearSelection() {
        selectedDistributeur = null;
        distributeurIdInput.value = '';
        selectedDiv.classList.add('hidden');
        searchInput.value = '';

        checkFormValidity();
    }

    // Fonction globale pour vérifier la validité du formulaire
    window.checkFormValidity = function() {
        const hasDistributeur = distributeurIdInput.value !== '';
        const periodInput = document.getElementById('period-input');
        const hasPeriod = periodInput && periodInput.value.match(/^\d{4}-\d{2}$/);

        console.log('Validation:', { hasDistributeur, hasPeriod, periodValue: periodInput?.value });

        if (hasDistributeur && hasPeriod) {
            submitButton.disabled = false;
            submitButton.classList.remove('from-gray-400', 'to-gray-500', 'cursor-not-allowed');
            submitButton.classList.add('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
            buttonText.textContent = 'Générer l\'aperçu du réseau';
        } else {
            submitButton.disabled = true;
            submitButton.classList.add('from-gray-400', 'to-gray-500', 'cursor-not-allowed');
            submitButton.classList.remove('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');

            if (!hasDistributeur) {
                buttonText.textContent = 'Sélectionnez d\'abord un distributeur';
            } else {
                buttonText.textContent = 'Sélectionnez une période valide (AAAA-MM)';
            }
        }
    }

    // Fonction Alpine.js pour la recherche de période
    function periodSearch() {
        return {
            periodValue: '',
            suggestions: [],
            showSuggestions: false,
            loading: false,
            searchTimeout: null,

            init() {
                // Écouter les changements pour la validation du formulaire
                this.$watch('periodValue', (value) => {
                    console.log('Period value changed:', value);
                    // Appeler la fonction globale
                    window.checkFormValidity();
                });
            },

            searchPeriods() {
                clearTimeout(this.searchTimeout);

                // Ne chercher que si on a au moins 3 caractères
                if (this.periodValue.length < 3) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }

                this.searchTimeout = setTimeout(() => {
                    this.loading = true;
                    this.showSuggestions = true;

                    console.log('Searching periods for:', this.periodValue);

                    fetch(`{{ route("admin.network.search.periods") }}?q=${encodeURIComponent(this.periodValue)}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Periods found:', data);
                            this.suggestions = [];

                            // Aplatir les résultats groupés par année
                            Object.entries(data).forEach(([year, periods]) => {
                                periods.forEach(period => {
                                    this.suggestions.push(period);
                                });
                            });

                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Erreur lors de la recherche des périodes:', error);
                            this.loading = false;
                            this.suggestions = [];
                        });
                }, 300);
            },

            selectPeriod(period) {
                console.log('Period selected:', period);
                this.periodValue = period.value;
                this.showSuggestions = false;
                // Forcer la mise à jour de la validation
                this.$nextTick(() => {
                    window.checkFormValidity();
                });
            }
        }
    }

    // Cacher les résultats quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });

    // Empêcher la soumission si le formulaire n'est pas valide
    document.getElementById('networkExportForm').addEventListener('submit', function(e) {
        if (submitButton.disabled) {
            e.preventDefault();
            return false;
        }

        // Afficher un loader sur le bouton
        submitButton.disabled = true;
        buttonText.textContent = 'Génération en cours...';
        submitButton.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Génération en cours...</span>
        `;
    });

    // Vérifier la validité au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        checkFormValidity();
    });
</script>
@endpush

@endsection
