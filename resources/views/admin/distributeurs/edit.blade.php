{{-- resources/views/admin/distributeurs/edit.blade.php --}}

@extends('layouts.app') {{-- Adaptez --}}

@section('title', "Modifier Distributeur: " . $distributeur->full_name)

@section('content')
<div class="container">
    <h1>Modifier Distributeur : {{ $distributeur->full_name }} (#{{ $distributeur->distributeur_id }})</h1>

     {{-- Affichage des erreurs --}}
    @include('partials.alerts')

    {{-- Le formulaire pointe vers la route update avec la méthode PUT/PATCH --}}
    <form action="{{ route('admin.distributeurs.update', $distributeur) }}" method="POST">
        @csrf
        @method('PUT') {{-- Ou PATCH --}}

        <div class="row g-3">
            <div class="col-md-6">
                <label for="pnom_distributeur" class="form-label">Prénom *</label>
                {{-- old() prend la valeur soumise précédemment OU la valeur actuelle du modèle --}}
                <input type="text" class="form-control @error('pnom_distributeur') is-invalid @enderror" id="pnom_distributeur" name="pnom_distributeur" value="{{ old('pnom_distributeur', $distributeur->pnom_distributeur) }}" required>
                @error('pnom_distributeur') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label for="nom_distributeur" class="form-label">Nom *</label>
                <input type="text" class="form-control @error('nom_distributeur') is-invalid @enderror" id="nom_distributeur" name="nom_distributeur" value="{{ old('nom_distributeur', $distributeur->nom_distributeur) }}" required>
                 @error('nom_distributeur') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
             <div class="col-md-6">
                <label for="distributeur_id" class="form-label">Matricule *</label>
                <input type="text" class="form-control @error('distributeur_id') is-invalid @enderror" id="distributeur_id" name="distributeur_id" value="{{ old('distributeur_id', $distributeur->distributeur_id) }}" required>
                 @error('distributeur_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
             <div class="col-md-6">
                <label for="tel_distributeur" class="form-label">Téléphone</label>
                <input type="text" class="form-control @error('tel_distributeur') is-invalid @enderror" id="tel_distributeur" name="tel_distributeur" value="{{ old('tel_distributeur', $distributeur->tel_distributeur) }}">
                 @error('tel_distributeur') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
             <div class="col-12">
                <label for="adress_distributeur" class="form-label">Adresse</label>
                <textarea class="form-control @error('adress_distributeur') is-invalid @enderror" id="adress_distributeur" name="adress_distributeur" rows="3">{{ old('adress_distributeur', $distributeur->adress_distributeur) }}</textarea>
                 @error('adress_distributeur') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                 <label for="id_distrib_parent" class="form-label">Parent (Sponsor)</label>
                 <select class="form-select @error('id_distrib_parent') is-invalid @enderror" id="id_distrib_parent" name="id_distrib_parent">
                     <option value="">-- Aucun (Racine) --</option>
                     @foreach ($potentialParents as $id => $displayName)
                         {{-- Sélectionne le parent actuel --}}
                         <option value="{{ $id }}" {{ old('id_distrib_parent', $distributeur->id_distrib_parent) == $id ? 'selected' : '' }}>
                             {{ $displayName }}
                         </option>
                     @endforeach
                 </select>
                 @error('id_distrib_parent') <div class="invalid-feedback">{{ $message }}</div> @enderror
             </div>

             {{-- Champs pour valeurs actuelles - l'admin modifie-t-il directement ceci? --}}
             <div class="col-md-3">
                <label for="etoiles_id" class="form-label">Niveau (Etoiles) *</label>
                <input type="number" class="form-control @error('etoiles_id') is-invalid @enderror" id="etoiles_id" name="etoiles_id" value="{{ old('etoiles_id', $distributeur->etoiles_id) }}" required min="1">
                @error('etoiles_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
             </div>
             <div class="col-md-3">
                <label for="rang" class="form-label">Rang *</label>
                <input type="number" class="form-control @error('rang') is-invalid @enderror" id="rang" name="rang" value="{{ old('rang', $distributeur->rang) }}" required>
                 @error('rang') <div class="invalid-feedback">{{ $message }}</div> @enderror
             </div>

             {{-- Flag boolean --}}
              <div class="col-12">
                  <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="1" id="statut_validation_periode" name="statut_validation_periode" {{ old('statut_validation_periode', $distributeur->statut_validation_periode) ? 'checked' : '' }}>
                      <label class="form-check-label" for="statut_validation_periode">
                           Statut Validation Période (Renommer le label !)
                      </label>
                  </div>
                  @error('statut_validation_periode') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
              </div>

             {{-- Ajouter d'autres champs si nécessaire --}}

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary">Enregistrer Modifications</button>
                <a href="{{ route('admin.distributeurs.show', $distributeur) }}" class="btn btn-secondary">Annuler</a> {{-- Retourne à la vue show --}}
            </div>
        </div>
    </form>

</div>
@endsection

{{-- Optionnel : JS/CSS pour Select2 --}}
{{-- @push('scripts') ... @endpush --}}
