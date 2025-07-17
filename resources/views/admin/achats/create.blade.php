@extends('layouts.app') {{-- Adaptez --}}

@section('content')
<div class="container">
    <h1>Enregistrer un Achat</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oups!</strong> Erreurs:<br><br>
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

    <form action="{{ route('admin.achats.store') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label for="period" class="form-label">Période (YYYY-MM) *</label>
                <input type="text" class="form-control @error('period') is-invalid @enderror" id="period" name="period" value="{{ old('period', date('Y-m')) }}" required pattern="\d{4}-\d{2}">
                @error('period') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="distributeur_id" class="form-label">Distributeur Acheteur *</label>
                <select class="form-select @error('distributeur_id') is-invalid @enderror" id="distributeur_id" name="distributeur_id" required>
                    <option value="">-- Sélectionner --</option>
                    {{-- Utiliser le format [id => "Nom (Matricule)"] --}}
                    @foreach ($distributeurs as $id => $displayName)
                        <option value="{{ $id }}" {{ old('distributeur_id') == $id ? 'selected' : '' }}>
                            {{ $displayName }}
                        </option>
                    @endforeach
                </select>
                @error('distributeur_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-8">
                <label for="products_id" class="form-label">Produit *</label>
                <select class="form-select @error('products_id') is-invalid @enderror" id="products_id" name="products_id" required>
                    <option value="">-- Sélectionner --</option>
                     {{-- Utiliser le format [id => "Nom (Code)"] --}}
                    @foreach ($products as $id => $displayName)
                         <option value="{{ $id }}" {{ old('products_id') == $id ? 'selected' : '' }}>
                            {{ $displayName }}
                        </option>
                    @endforeach
                </select>
                @error('products_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
               <label for="qt" class="form-label">Quantité *</label>
               <input type="number" class="form-control @error('qt') is-invalid @enderror" id="qt" name="qt" value="{{ old('qt', 1) }}" required min="1">
               @error('qt') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

           <div class="col-12">
               <div class="form-check">
                   <input class="form-check-input" type="checkbox" value="1" id="online" name="online" {{ old('online', 1) ? 'checked' : '' }}>
                   <label class="form-check-label" for="online">
                       Achat en ligne ?
                   </label>
               </div>
                @error('online') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
           </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary">Enregistrer l'Achat</button>
                <a href="{{ route('admin.achats.index') }}" class="btn btn-secondary">Annuler</a>
            </div>
        </div>
    </form>
</div>
@endsection

{{-- Optionnel: Ajouter JS/CSS pour Select2 si listes longues --}}
{{-- @push('scripts') ... @endpush --}}
