{{-- resources/views/admin/achats/show.blade.php --}}

@extends('layouts.admin')

@section('title', 'Détails de l\'achat')

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
                <span class="text-gray-700 font-medium">Achat #{{ $achat->id }}</span>
            </nav>
        </div>

        {{-- Actions --}}
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Détails de l'achat #{{ $achat->id }}</h1>
            <div class="flex space-x-3">
                <a href="{{ route('admin.achats.edit', $achat) }}"
                   class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Modifier
                </a>
                <button type="button"
                        @click="$dispatch('delete-modal-show')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Informations principales --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Détails de l'achat --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations de l'achat</h2>

                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Période</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $achat->period }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date d'enregistrement</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $achat->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type d'achat</dt>
                            <dd class="mt-1">
                                @if($achat->online)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                        </svg>
                                        En ligne
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        Physique
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dernière modification</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $achat->updated_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Informations du distributeur --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Distributeur</h2>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $achat->distributeur->pnom_distributeur }} {{ $achat->distributeur->nom_distributeur }}
                            </p>
                            <p class="text-sm text-gray-500">Matricule: #{{ $achat->distributeur->distributeur_id }}</p>
                            @if($achat->distributeur->tel_distributeur)
                                <p class="text-sm text-gray-500">Tél: {{ $achat->distributeur->tel_distributeur }}</p>
                            @endif
                            <a href="{{ route('admin.distributeurs.show', $achat->distributeur) }}"
                               class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500">
                                Voir la fiche
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Informations du produit --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Produit acheté</h2>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-base font-medium text-gray-900">{{ $achat->product->nom_produit }}</h3>
                            <p class="text-sm text-gray-500">Code: {{ $achat->product->code_product }}</p>
                            @if($achat->product->category)
                                <p class="text-sm text-gray-500">Catégorie: {{ $achat->product->category->name }}</p>
                            @endif
                        </div>

                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Quantité</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $achat->qt }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Prix unitaire</dt>
                                <dd class="mt-1 text-lg font-medium text-gray-900">{{ number_format($achat->prix_unitaire_achat, 0, ',', ' ') }} XAF</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Points unitaires</dt>
                                <dd class="mt-1 text-lg font-medium text-green-600">{{ $achat->points_unitaire_achat }} PV</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Points totaux</dt>
                                <dd class="mt-1 text-lg font-medium text-green-600">{{ $achat->points_unitaire_achat * $achat->qt }} PV</dd>
                            </div>
                        </dl>

                        <a href="{{ route('admin.products.show', $achat->product) }}"
                           class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500">
                            Voir le produit
                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Résumé financier --}}
            <div class="lg:col-span-1">
                <div class="bg-white shadow-sm rounded-lg p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Résumé financier</h2>

                    <dl class="space-y-4">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Prix unitaire</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ number_format($achat->prix_unitaire_achat, 0, ',', ' ') }} XAF</dd>
                        </div>

                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Quantité</dt>
                            <dd class="text-sm font-medium text-gray-900">× {{ $achat->qt }}</dd>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-base font-medium text-gray-900">Montant total</dt>
                                <dd class="text-xl font-semibold text-gray-900">{{ number_format($achat->montant_total_ligne, 0, ',', ' ') }} XAF</dd>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-500">Points gagnés</dt>
                                <dd class="text-lg font-semibold text-green-600">{{ $achat->points_unitaire_achat * $achat->qt }} PV</dd>
                            </div>
                        </div>
                    </dl>

                    {{-- Statistiques du distributeur pour cette période --}}
                    @if(isset($distributeurStats))
                        <div class="mt-6 pt-6 border-t">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Cette période ({{ $achat->period }})</h3>
                            <dl class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-500">Total achats</dt>
                                    <dd class="text-xs font-medium text-gray-900">{{ $distributeurStats->total_achats }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-500">Montant total</dt>
                                    <dd class="text-xs font-medium text-gray-900">{{ number_format($distributeurStats->total_montant, 0, ',', ' ') }} XAF</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-xs text-gray-500">Points totaux</dt>
                                    <dd class="text-xs font-medium text-green-600">{{ $distributeurStats->total_points }} PV</dd>
                                </div>
                            </dl>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Formulaire de suppression caché --}}
        <form id="delete-form" action="{{ route('admin.achats.destroy', $achat) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        {{-- Modal de confirmation de suppression --}}
        @include('components.modal-confirmation', [
            'id' => 'delete-modal',
            'title' => 'Confirmer la suppression',
            'message' => 'Êtes-vous sûr de vouloir supprimer cet achat ? Cette action est irréversible.',
            'confirmText' => 'Oui, supprimer',
            'confirmClass' => 'bg-red-600 hover:bg-red-700',
            'formId' => 'delete-form'
        ])
    </div>
</div>
@endsection
