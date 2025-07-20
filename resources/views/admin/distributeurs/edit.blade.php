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
                <span class="text-gray-700 font-medium">Modifier</span>
            </nav>
        </div>

        {{-- Titre principal avec actions --}}
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Modifier le distributeur</h1>
                <p class="mt-2 text-gray-600">Matricule : <span class="font-semibold">{{ $distributeur->distributeur_id }}</span></p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.distributeurs.show', $distributeur) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Voir la fiche
                </a>
            </div>
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

            {{-- Informations personnelles --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Informations personnelles</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Matricule --}}
                    <div>
                        <label for="distributeur_id" class="block text-sm font-medium text-gray-700">
                            Matricule <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="distributeur_id"
                               id="distributeur_id"
                               value="{{ old('distributeur_id', $distributeur->distributeur_id) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('distributeur_id') border-red-300 @enderror"
                               placeholder="Ex: DIST001">
                        @error('distributeur_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Statut de validation --}}
                    <div>
                        <label for="statut_validation_periode" class="block text-sm font-medium text-gray-700">
                            Statut de validation
                        </label>
                        <select name="statut_validation_periode"
                                id="statut_validation_periode"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="1" {{ old('statut_validation_periode', $distributeur->statut_validation_periode) == '1' ? 'selected' : '' }}>Validé</option>
                            <option value="0" {{ old('statut_validation_periode', $distributeur->statut_validation_periode) == '0' ? 'selected' : '' }}>Non validé</option>
                        </select>
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
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('nom_distributeur') border-red-300 @enderror"
                               placeholder="Nom de famille">
                        @error('nom_distributeur')
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
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('pnom_distributeur') border-red-300 @enderror"
                               placeholder="Prénom">
                        @error('pnom_distributeur')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label for="tel_distributeur" class="block text-sm font-medium text-gray-700">
                            Téléphone
                        </label>
                        <input type="tel"
                               name="tel_distributeur"
                               id="tel_distributeur"
                               value="{{ old('tel_distributeur', $distributeur->tel_distributeur) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('tel_distributeur') border-red-300 @enderror"
                               placeholder="+237 6XX XXX XXX">
                        @error('tel_distributeur')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Adresse --}}
                    <div>
                        <label for="adress_distributeur" class="block text-sm font-medium text-gray-700">
                            Adresse
                        </label>
                        <input type="text"
                               name="adress_distributeur"
                               id="adress_distributeur"
                               value="{{ old('adress_distributeur', $distributeur->adress_distributeur) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('adress_distributeur') border-red-300 @enderror"
                               placeholder="Adresse complète">
                        @error('adress_distributeur')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Informations de parrainage et niveau --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Parrainage et niveau</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Distributeur parent --}}
                    <div>
                        <label for="id_distrib_parent" class="block text-sm font-medium text-gray-700">
                            Distributeur parent (Parrain)
                        </label>
                        <select name="id_distrib_parent"
                                id="id_distrib_parent"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('id_distrib_parent') border-red-300 @enderror">
                            <option value="">-- Aucun parent --</option>
                            @foreach($potentialParents as $parentId => $parentName)
                                <option value="{{ $parentId }}" {{ old('id_distrib_parent', $distributeur->id_distrib_parent) == $parentId ? 'selected' : '' }}>
                                    {{ $parentName }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_distrib_parent')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($distributeur->children()->exists())
                            <p class="mt-1 text-sm text-amber-600">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Ce distributeur a {{ $distributeur->children()->count() }} filleul(s) direct(s)
                            </p>
                        @endif
                    </div>

                    {{-- Niveau étoiles --}}
                    <div>
                        <label for="etoiles_id" class="block text-sm font-medium text-gray-700">
                            Niveau (étoiles)
                        </label>
                        <select name="etoiles_id"
                                id="etoiles_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ old('etoiles_id', $distributeur->etoiles_id) == $i ? 'selected' : '' }}>
                                    {{ $i }} étoile{{ $i > 1 ? 's' : '' }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Rang --}}
                    <div>
                        <label for="rang" class="block text-sm font-medium text-gray-700">
                            Rang
                        </label>
                        <input type="number"
                               name="rang"
                               id="rang"
                               value="{{ old('rang', $distributeur->rang) }}"
                               min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               placeholder="0">
                    </div>

                    {{-- Date de création --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Date d'inscription
                        </label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md px-3 py-2">
                            {{ $distributeur->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Statistiques --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Statistiques</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-500">Nombre de filleuls directs</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $distributeur->children()->count() }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-500">Nombre d'achats</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $distributeur->achats()->count() }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-500">Nombre de bonus</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $distributeur->bonuses()->count() }}</p>
                    </div>
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="flex justify-between">
                <button type="button"
                        @click="$dispatch('delete-modal-show')"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>

                <div class="flex space-x-3">
                    <a href="{{ route('admin.distributeurs.index') }}"
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
            </div>
        </form>

        {{-- Formulaire de suppression caché --}}
        <form id="delete-form" action="{{ route('admin.distributeurs.destroy', $distributeur) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        {{-- Modal de confirmation de suppression --}}
        @include('components.modal-confirmation', [
            'id' => 'delete-modal',
            'title' => 'Confirmer la suppression',
            'message' => 'Êtes-vous sûr de vouloir supprimer ce distributeur ? Cette action est irréversible. Tous les achats et bonus associés seront également supprimés.',
            'confirmText' => 'Oui, supprimer',
            'confirmClass' => 'bg-red-600 hover:bg-red-700',
            'formId' => 'delete-form'
        ])
    </div>
</div>

@push('scripts')
<script>
    // Auto-formatage du numéro de téléphone
    document.getElementById('tel_distributeur').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '');
        let formattedValue = '';

        if (value.startsWith('+237')) {
            value = value.substring(4);
        }

        if (value.length > 0) {
            formattedValue = '+237 ';
            for (let i = 0; i < value.length && i < 9; i++) {
                if (i === 3 || i === 6) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
        }

        e.target.value = formattedValue;
    });
</script>
@endpush
@endsection
