{{-- resources/views/admin/products/edit.blade.php --}}

@extends('layouts.admin')

@section('title', 'Modifier le produit')

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
                <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    Produits
                </a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="{{ route('admin.products.show', $product) }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    {{ $product->nom_produit }}
                </a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-700 font-medium">Modifier</span>
            </nav>
        </div>

        {{-- Titre principal --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Modifier le produit</h1>
            <p class="mt-2 text-gray-600">Modifiez les informations du produit ci-dessous.</p>
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
        <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Section Informations de base --}}
                <div class="bg-white shadow-lg rounded-lg overflow-hidden h-fit">
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Informations de base
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Code produit --}}
                        <div>
                            <label for="code_product" class="block text-sm font-medium text-gray-700 mb-2">
                                Code produit <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="code_product"
                                id="code_product"
                                value="{{ old('code_product', $product->code_product) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('code_product') border-red-500 @enderror"
                                placeholder="Ex: PROD001"
                                required
                                maxlength="50"
                            >
                            @error('code_product')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nom du produit --}}
                        <div>
                            <label for="nom_produit" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom du produit <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="nom_produit"
                                id="nom_produit"
                                value="{{ old('nom_produit', $product->nom_produit) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('nom_produit') border-red-500 @enderror"
                                placeholder="Entrez le nom du produit"
                                required
                                maxlength="255"
                            >
                            @error('nom_produit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Prix --}}
                        <div>
                            <label for="prix_product" class="block text-sm font-medium text-gray-700 mb-2">
                                Prix (XAF) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    name="prix_product"
                                    id="prix_product"
                                    value="{{ old('prix_product', $product->prix_product) }}"
                                    step="0.01"
                                    min="0"
                                    class="w-full px-4 py-2 pr-16 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('prix_product') border-red-500 @enderror"
                                    placeholder="0"
                                    required
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">XAF</span>
                                </div>
                            </div>
                            @error('prix_product')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea
                                name="description"
                                id="description"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('description') border-red-500 @enderror"
                                placeholder="Description détaillée du produit (optionnel)"
                                maxlength="1000"
                            >{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Maximum 1000 caractères</p>
                        </div>
                    </div>
                </div>

                {{-- Section Catégorie et Points --}}
                <div class="bg-white shadow-lg rounded-lg overflow-hidden h-fit">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Catégorie et Points
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Catégorie --}}
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Catégorie
                            </label>
                            <select
                                name="category_id"
                                id="category_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 @error('category_id') border-red-500 @enderror"
                            >
                                <option value="">-- Sélectionnez une catégorie --</option>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}" {{ old('category_id', $product->category_id) == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">
                                La catégorie est optionnelle. Elle permet de classifier les produits.
                            </p>
                        </div>

                        {{-- Points Valeur --}}
                        <div>
                            <label for="pointvaleur_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Points Valeur (PV) <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="pointvaleur_id"
                                id="pointvaleur_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 @error('pointvaleur_id') border-red-500 @enderror"
                                required
                            >
                                <option value="">-- Sélectionnez les points --</option>
                                @foreach($pointValeurs as $id => $points)
                                    <option value="{{ $id }}" {{ old('pointvaleur_id', $product->pointvaleur_id) == $id ? 'selected' : '' }}>
                                        {{ $points }} PV
                                    </option>
                                @endforeach
                            </select>
                            @error('pointvaleur_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">
                                Les points valeur sont utilisés pour calculer les bonus des distributeurs.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="{{ route('admin.products.show', $product) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
