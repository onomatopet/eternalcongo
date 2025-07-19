{{-- resources/views/admin/distributeurs/edit.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Modifier le Distributeur #{{ $distributeur->distributeur_id }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.distributeurs.show', $distributeur) }}" class="btn btn-info">
                <i class="fas fa-eye me-1"></i>Consulter
            </a>
            <a href="{{ route('admin.distributeurs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
    </div>
</div>

{{-- Affichage des erreurs de validation --}}
@if($errors->any())
    <div class="alert alert-danger">
        <h6><i class="fas fa-exclamation-triangle me-2"></i>Veuillez corriger les erreurs suivantes :</h6>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Affichage des messages flash --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <i class="fas fa-user-edit me-2"></i>Modifier les informations du distributeur
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.distributeurs.update', $distributeur) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                {{-- Informations personnelles --}}
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2 mb-3">Informations personnelles</h5>
                    
                    <div class="mb-3">
                        <label for="distributeur_id" class="form-label">Matricule <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('distributeur_id') is-invalid @enderror" 
                               id="distributeur_id" name="distributeur_id" value="{{ old('distributeur_id', $distributeur->distributeur_id) }}" required>
                        @error('distributeur_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pnom_distributeur" class="form-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('pnom_distributeur') is-invalid @enderror" 
                               id="pnom_distributeur" name="pnom_distributeur" value="{{ old('pnom_distributeur', $distributeur->pnom_distributeur) }}" required>
                        @error('pnom_distributeur')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nom_distributeur" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom_distributeur') is-invalid @enderror" 
                               id="nom_distributeur" name="nom_distributeur" value="{{ old('nom_distributeur', $distributeur->nom_distributeur) }}" required>
                        @error('nom_distributeur')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tel_distributeur" class="form-label">Téléphone</label>
                        <input type="text" class="form-control @error('tel_distributeur') is-invalid @enderror" 
                               id="tel_distributeur" name="tel_distributeur" value="{{ old('tel_distributeur', $distributeur->tel_distributeur) }}">
                        @error('tel_distributeur')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="adress_distributeur" class="form-label">Adresse</label>
                        <textarea class="form-control @error('adress_distributeur') is-invalid @enderror" 
                                  id="adress_distributeur" name="adress_distributeur" rows="3">{{ old('adress_distributeur', $distributeur->adress_distributeur) }}</textarea>
                        @error('adress_distributeur')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Informations MLM --}}
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2 mb-3">Informations MLM</h5>
                    
                    <div class="mb-3">
                        <label for="id_distrib_parent" class="form-label">Distributeur Parent</label>
                        <select class="form-select @error('id_distrib_parent') is-invalid @enderror" 
                                id="id_distrib_parent" name="id_distrib_parent">
                            <option value="">-- Aucun parent (distributeur racine) --</option>
                            @foreach($potentialParents as $id => $displayName)
                                <option value="{{ $id }}" {{ old('id_distrib_parent', $distributeur->id_distrib_parent) == $id ? 'selected' : '' }}>
                                    {{ $displayName }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_distrib_parent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Sélectionner le distributeur qui parraine cette personne</div>
                    </div>

                    <div class="mb-3">
                        <label for="etoiles_id" class="form-label">Niveau d'étoiles <span class="text-danger">*</span></label>
                        <select class="form-select @error('etoiles_id') is-invalid @enderror" 
                                id="etoiles_id" name="etoiles_id" required>
                            <option value="1" {{ old('etoiles_id', $distributeur->etoiles_id) == 1 ? 'selected' : '' }}>1 étoile</option>
                            <option value="2" {{ old('etoiles_id', $distributeur->etoiles_id) == 2 ? 'selected' : '' }}>2 étoiles</option>
                            <option value="3" {{ old('etoiles_id', $distributeur->etoiles_id) == 3 ? 'selected' : '' }}>3 étoiles</option>
                            <option value="4" {{ old('etoiles_id', $distributeur->etoiles_id) == 4 ? 'selected' : '' }}>4 étoiles</option>
                            <option value="5" {{ old('etoiles_id', $distributeur->etoiles_id) == 5 ? 'selected' : '' }}>5 étoiles</option>
                        </select>
                        @error('etoiles_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="rang" class="form-label">Rang <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('rang') is-invalid @enderror" 
                               id="rang" name="rang" value="{{ old('rang', $distributeur->rang) }}" min="0" required>
                        @error('rang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" 
                                   id="statut_validation_periode" name="statut_validation_periode" 
                                   {{ old('statut_validation_periode', $distributeur->statut_validation_periode) ? 'checked' : '' }}>
                            <label class="form-check-label" for="statut_validation_periode">
                                Période validée
                            </label>
                        </div>
                        <div class="form-text">Cocher si la période courante est validée pour ce distributeur</div>
                    </div>

                    {{-- Informations en lecture seule --}}
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Informations</h6>
                        <p class="mb-1"><strong>Inscription :</strong> {{ $distributeur->created_at->format('d/m/Y à H:i') }}</p>
                        <p class="mb-1"><strong>Enfants directs :</strong> {{ $distributeur->children()->count() }}</p>
                        <p class="mb-0"><strong>Achats total :</strong> {{ $distributeur->achats()->count() }}</p>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="{{ route('admin.distributeurs.show', $distributeur) }}" class="btn btn-secondary me-md-2">
                    <i class="fas fa-times me-1"></i>Annuler
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection