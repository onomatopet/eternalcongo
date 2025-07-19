@extends('layouts.admin')

@section('content')
<!-- Header -->
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Ajouter un distributeur
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                </svg>
                Nouveau membre du réseau
            </div>
        </div>
    </div>
    <div class="mt-4 flex md:ml-4 md:mt-0">
        <a href="{{ route('admin.distributeurs.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
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
    <form method="POST" action="{{ route('admin.distributeurs.store') }}" class="px-4 py-6 sm:p-8">
        @csrf
    
    <div class="space-y-12">
        <!-- Informations personnelles -->
        <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Informations personnelles</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Ces informations seront visibles dans le système.</p>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <!-- Matricule -->
                <div class="sm:col-span-2">
                    <label for="distributeur_id" class="block text-sm font-medium leading-6 text-gray-900">
                        Matricule <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input type="number" name="distributeur_id" id="distributeur_id" value="{{ old('distributeur_id') }}" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('distributeur_id') ring-red-500 @enderror">
                        @error('distributeur_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Prénom -->
                <div class="sm:col-span-2">
                    <label for="pnom_distributeur" class="block text-sm font-medium leading-6 text-gray-900">
                        Prénom <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input type="text" name="pnom_distributeur" id="pnom_distributeur" value="{{ old('pnom_distributeur') }}" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('pnom_distributeur') ring-red-500 @enderror">
                        @error('pnom_distributeur')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Nom -->
                <div class="sm:col-span-2">
                    <label for="nom_distributeur" class="block text-sm font-medium leading-6 text-gray-900">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <input type="text" name="nom_distributeur" id="nom_distributeur" value="{{ old('nom_distributeur') }}" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('nom_distributeur') ring-red-500 @enderror">
                        @error('nom_distributeur')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Téléphone -->
                <div class="sm:col-span-3">
                    <label for="tel_distributeur" class="block text-sm font-medium leading-6 text-gray-900">Téléphone</label>
                    <div class="mt-2">
                        <input type="tel" name="tel_distributeur" id="tel_distributeur" value="{{ old('tel_distributeur') }}"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('tel_distributeur') ring-red-500 @enderror"
                               placeholder="06 12 34 56 78">
                        @error('tel_distributeur')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Adresse -->
                <div class="sm:col-span-6">
                    <label for="adress_distributeur" class="block text-sm font-medium leading-6 text-gray-900">Adresse</label>
                    <div class="mt-2">
                        <textarea name="adress_distributeur" id="adress_distributeur" rows="3"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('adress_distributeur') ring-red-500 @enderror"
                                  placeholder="Adresse complète du distributeur">{{ old('adress_distributeur') }}</textarea>
                        @error('adress_distributeur')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations MLM -->
        <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Configuration MLM</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Paramètres du réseau de distribution et hiérarchie.</p>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <!-- Parent -->
                <div class="sm:col-span-4">
                    <label for="id_distrib_parent" class="block text-sm font-medium leading-6 text-gray-900">Distributeur parent</label>
                    <div class="mt-2">
                        <select name="id_distrib_parent" id="id_distrib_parent" 
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('id_distrib_parent') ring-red-500 @enderror">
                            <option value="">Aucun parent (distributeur racine)</option>
                            @foreach($potentialParents as $id => $displayName)
                                <option value="{{ $id }}" {{ old('id_distrib_parent') == $id ? 'selected' : '' }}>
                                    {{ $displayName }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_distrib_parent')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Sélectionnez le distributeur qui parraine cette personne</p>
                    </div>
                </div>

                <!-- Niveau étoiles -->
                <div class="sm:col-span-2">
                    <label for="etoiles_id" class="block text-sm font-medium leading-6 text-gray-900">
                        Niveau d'étoiles <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-2">
                        <select name="etoiles_id" id="etoiles_id" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('etoiles_id') ring-red-500 @enderror">
                            <option value="1" {{ old('etoiles_id', 1) == 1 ? 'selected' : '' }}>⭐ 1 étoile</option>
                            <option value="2" {{ old('etoiles_id') == 2 ? 'selected' : '' }}>⭐⭐ 2 étoiles</option>
                            <option value="3" {{ old('etoiles_id') == 3 ? 'selected' : '' }}>⭐⭐⭐ 3 étoiles</option>
                            <option value="4" {{ old('etoiles_id') == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ 4 étoiles</option>
                            <option value="5" {{ old('etoiles_id') == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5 étoiles</option>
                        </select>
                        @error('etoiles_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Rang -->
                <div class="sm:col-span-2">
                    <label for="rang" class="block text-sm font-medium leading-6 text-gray-900">
                        Rang initial
                    </label>
                    <div class="mt-2">
                        <input type="number" name="rang" id="rang" value="{{ old('rang', 0) }}" min="0"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('rang') ring-red-500 @enderror">
                        @error('rang')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Par défaut : 0 pour un nouveau distributeur (optionnel)</p>
                    </div>
                </div>

                <!-- Statut période -->
                <div class="sm:col-span-6">
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="statut_validation_periode" name="statut_validation_periode" type="checkbox" value="1" {{ old('statut_validation_periode') ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="statut_validation_periode" class="font-medium text-gray-900">Période validée</label>
                            <p class="text-gray-500">Cocher si la période courante est déjà validée pour ce distributeur</p>
                        </div>
                    </div>
                    @error('statut_validation_periode')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
        <a href="{{ route('admin.distributeurs.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Annuler</a>
        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
            </svg>
            Enregistrer le distributeur
        </button>
    </div>
    </form>
</div>
@endsection