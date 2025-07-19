@extends('layouts.admin')

@section('content')
<!-- Header -->
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Enregistrer un achat
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
                Nouvelle transaction
            </div>
        </div>
    </div>
    <div class="mt-4 flex md:ml-4 md:mt-0">
        <a href="{{ route('admin.achats.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8 7.72 4.47a.75.75 0 011.06-1.06l4 4a.75.75 0 010 1.06l-4 4a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
            </svg>
            Retour à la liste
        </a>
    </div>
</div>

<!-- Messages d'erreur -->
@if($errors->any())
    <div class="mt-6 rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Veuillez corriger les erreurs suivantes :</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul role="list" class="list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mt-6 rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
@endif

<!-- Formulaire -->
<div class="mt-8 bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
    <form method="POST" action="{{ route('admin.achats.store') }}" class="px-4 py-6 sm:p-8">
        @csrf
    
    <div class="space-y-12">
        <!-- Informations de base -->
        <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Informations de l'achat</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Détails de la transaction effectuée par le distributeur.</p>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <!-- Période -->
                <div class="sm:col-span-2">
                    <label for="period" class="block text-sm font-medium leading-6 text-gray-900">
                        Période <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input type="month" name="period" id="period" value="{{ old('period', date('Y-m')) }}" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('period') ring-red-500 @enderror">
                        @error('period')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Format: YYYY-MM (ex: 2024-12)</p>
                    </div>
                </div>

                <!-- Distributeur -->
                <div class="sm:col-span-4">
                    <label for="distributeur_id" class="block text-sm font-medium leading-6 text-gray-900">
                        Distributeur <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <select name="distributeur_id" id="distributeur_id" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('distributeur_id') ring-red-500 @enderror">
                            <option value="">Sélectionner un distributeur</option>
                            @foreach($distributeurs as $id => $displayName)
                                <option value="{{ $id }}" {{ old('distributeur_id') == $id ? 'selected' : '' }}>
                                    {{ $displayName }}
                                </option>
                            @endforeach
                        </select>
                        @error('distributeur_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Produit -->
                <div class="sm:col-span-4">
                    <label for="products_id" class="block text-sm font-medium leading-6 text-gray-900">
                        Produit <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <select name="products_id" id="products_id" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('products_id') ring-red-500 @enderror">
                            <option value="">Sélectionner un produit</option>
                            @foreach($products as $id => $displayName)
                                <option value="{{ $id }}" {{ old('products_id') == $id ? 'selected' : '' }}>
                                    {{ $displayName }}
                                </option>
                            @endforeach
                        </select>
                        @error('products_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Quantité -->
                <div class="sm:col-span-2">
                    <label for="qt" class="block text-sm font-medium leading-6 text-gray-900">
                        Quantité <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input type="number" name="qt" id="qt" value="{{ old('qt', 1) }}" min="1" max="9999" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-green-600 sm:text-sm sm:leading-6 @error('qt') ring-red-500 @enderror">
                        @error('qt')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Type d'achat -->
                <div class="sm:col-span-6">
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="online" name="online" type="checkbox" value="1" {{ old('online', 1) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-600">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="online" class="font-medium text-gray-900">
                                <svg class="inline h-4 w-4 mr-1 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3s-4.5 4.03-4.5 9 2.015 9 4.5 9z" />
                                </svg>
                                Achat en ligne
                            </label>
                            <p class="text-gray-500">Cocher si l'achat a été effectué via la plateforme en ligne</p>
                        </div>
                    </div>
                    @error('online')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Informations automatiques -->
        <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Calculs automatiques</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Ces valeurs seront calculées automatiquement lors de l'enregistrement.</p>

            <div class="mt-10">
                <div class="rounded-md bg-blue-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Valeurs calculées automatiquement</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc space-y-1 pl-5">
                                    <li><strong>Points unitaires :</strong> Récupérés depuis la configuration du produit sélectionné</li>
                                    <li><strong>Prix unitaire :</strong> Prix du produit au moment de l'achat</li>
                                    <li><strong>Montant total :</strong> Prix unitaire × Quantité</li>
                                    <li><strong>Points totaux :</strong> Points unitaires × Quantité</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aperçu (pour plus tard avec JavaScript) -->
        <div class="hidden" id="preview-section">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Aperçu de l'achat</h2>
            <div class="mt-6 border-t border-gray-100">
                <dl class="divide-y divide-gray-100">
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-medium leading-6 text-gray-900">Distributeur</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" id="preview-distributeur">—</dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-medium leading-6 text-gray-900">Produit</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" id="preview-produit">—</dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-medium leading-6 text-gray-900">Quantité</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0" id="preview-quantite">—</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
        <a href="{{ route('admin.achats.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Annuler</a>
        <button type="submit" class="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
            </svg>
            Enregistrer l'achat
        </button>
    </div>
    </form>
</div>

<!-- Script pour aperçu en temps réel (optionnel) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-set period to current month if empty
    const periodInput = document.getElementById('period');
    if (!periodInput.value) {
        const now = new Date();
        const currentMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
        periodInput.value = currentMonth;
    }
    
    // Preview functionality could be added here
});
</script>
@endsection