@extends('layouts.app') {{-- Adaptez --}}

@section('content')
<div class="container">
    <h1>Ajouter un Distributeur</h1>

    {{-- Affichage des erreurs de validation générales --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oups !</strong> Quelques erreurs se sont produites :<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.distributeurs.store') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label for="pnom_distributeur" class="form-label">Prénom *</label>
                <input type="text" class="form-control @error('pnom_distributeur') is-invalid @enderror" id="pnom_distributeur" name="pnom_distributeur" value="{{ old('pnom_distributeur') }}" required>
                @error('pnom_distributeur') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label for="nom_distributeur" class="form-label">Nom *</label>
                <input type="text" class="form-control @error('nom_distributeur') is-invalid @enderror" id="nom_distributeur" name="nom_distributeur" value="{{ old('nom_distributeur') }}" required>
                 @error('nom_distributeur') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
             <div class="col-md-6">
                <label for="distributeur_id" class="form-label">Matricule *</label>
                <input type="text" class="form-control @error('distributeur_id') is-invalid @enderror" id="distributeur_id" name="distributeur_id" value="{{ old('distributeur_id') }}" required>
                 @error('distributeur_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
             <div class="col-md-6">
                <label for="tel_distributeur" class="form-label">Téléphone</label>
                <input type="text" class="form-control @error('tel_distributeur') is-invalid @enderror" id="tel_distributeur" name="tel_distributeur" value="{{ old('tel_distributeur') }}">
                 @error('tel_distributeur') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
             <div class="col-12">
                <label for="adress_distributeur" class="form-label">Adresse</label>
                <textarea class="form-control @error('adress_distributeur') is-invalid @enderror" id="adress_distributeur" name="adress_distributeur" rows="3">{{ old('adress_distributeur') }}</textarea>
                 @error('adress_distributeur') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                 <label for="id_distrib_parent" class="form-label">Parent (Sponsor)</label>
                 {{-- Un select simple est ok pour peu de parents, sinon utiliser Select2/Ajax --}}
                 <select class="form-select @error('id_distrib_parent') is-invalid @enderror" id="id_distrib_parent" name="id_distrib_parent">
                     <option value="">-- Aucun (Racine) --</option>
                     @foreach ($potentialParents as $id => $displayName)
                         <option value="{{ $id }}" {{ old('id_distrib_parent') == $id ? 'selected' : '' }}>
                             {{ $displayName }}
                         </option>
                     @endforeach
                 </select>
                 @error('id_distrib_parent') <div class="invalid-feedback">{{ $message }}</div> @enderror
             </div>

             {{-- Champs pour valeurs initiales - à ajuster si calculées automatiquement --}}
             <div class="col-md-3">
                <label for="etoiles_id" class="form-label">Niveau Initial (Etoiles) *</label>
                <input type="number" class="form-control @error('etoiles_id') is-invalid @enderror" id="etoiles_id" name="etoiles_id" value="{{ old('etoiles_id', 1) }}" required min="1">
                @error('etoiles_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
             </div>
             <div class="col-md-3">
                <label for="rang" class="form-label">Rang Initial *</label>
                <input type="number" class="form-control @error('rang') is-invalid @enderror" id="rang" name="rang" value="{{ old('rang', 0) }}" required>
                 @error('rang') <div class="invalid-feedback">{{ $message }}</div> @enderror
             </div>

             {{-- Ajouter d'autres champs si nécessaire (ex: email, password si Authenticatable) --}}

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary">Enregistrer Distributeur</button>
                <a href="{{ route('admin.distributeurs.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </div>
    </form>
</div>
@endsection

{{-- Optionnel : Ajouter JS pour Select2 si beaucoup de parents --}}
{{-- @push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#id_distrib_parent').select2({
            placeholder: "-- Aucun (Racine) --",
            allowClear: true
        });
    });
</script>
@endpush --}}
