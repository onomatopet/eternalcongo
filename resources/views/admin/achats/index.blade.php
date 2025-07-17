{{-- resources/views/admin/achats/index.blade.php --}}

@extends('layouts.app') {{-- A CHANGER SELON VOTRE LAYOUT --}}

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Liste des Achats</h1>
        </div>
        <div class="col text-end">
            <a href="{{ route('admin.achats.create') }}" class="btn btn-success">Enregistrer un Achat</a>
        </div>
    </div>
     {{-- Formulaire de Filtre (Période ET Recherche) --}}
     <div class="card mb-4">
        <div class="card-header">Filtres</div>
        <div class="card-body">
            {{-- Le formulaire envoie toujours vers la même route index --}}
            <form method="GET" action="{{ route('admin.achats.index') }}" class="row gy-2 gx-3 align-items-center">
                {{-- Filtre Période --}}
                <div class="col-auto">
                    <label for="period_filter" class="visually-hidden">Période</label>
                    <select name="period_filter" id="period_filter" class="form-select">
                        <option value="">-- Toutes les périodes --</option>
                        @foreach ($availablePeriods as $period)
                            <option value="{{ $period }}" {{ $selectedPeriod == $period ? 'selected' : '' }}>
                                {{ $period }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Champ de Recherche --}}
                <div class="col-auto flex-grow-1"> {{-- flex-grow-1 pour prendre l'espace restant --}}
                    <label for="search" class="visually-hidden">Recherche</label>
                    <input type="text" class="form-control" placeholder="Rechercher (ID Achat, Nom/Matricule Distrib., Nom/Code Produit...)" name="search" id="search" value="{{ $searchTerm ?? '' }}">
                </div>

                {{-- Boutons --}}
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filtrer / Rechercher</button>
                    {{-- Lien pour réinitialiser TOUS les filtres --}}
                    <a href="{{ route('admin.achats.index') }}" class="btn btn-secondary ms-2">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau des Achats --}}
    <div class="card">
        <div class="card-body">
             @if($achats->isEmpty())
                <div class="alert alert-info text-center">
                    Aucun achat trouvé pour la période sélectionnée.
                </div>
             @else
                <table class="table table-striped table-hover table-sm"> {{-- table-sm pour compacité --}}
                    <thead>
                        <tr>
                            <th>ID Achat</th>
                            <th>Période</th>
                            <th>Date</th>
                            <th>Distributeur (ID)</th>
                            <th>Distributeur (Nom)</th>
                            <th>Produit (ID)</th>
                            <th>Produit (Nom)</th>
                            <th>Qté</th>
                            <th>PV Unit.</th>
                            <th>Prix Unit.</th>
                            <th>Montant Total</th>
                            <th>En Ligne</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Boucle sur les achats passés par le contrôleur --}}
                        @foreach ($achats as $achat)
                            <tr>
                                <td>{{ $achat->id }}</td>
                                <td>{{ $achat->period }}</td>
                                <td>{{ $achat->created_at->format('d/m/Y H:i') }}</td> {{-- Formatage date --}}
                                <td>{{ $achat->distributeur_id }}</td>
                                {{-- Affichage du nom via la relation chargée avec 'with()' --}}
                                <td>{{ $achat->distributeur->full_name ?? 'N/A' }}</td>
                                <td>{{ $achat->products_id }}</td>
                                {{-- Affichage du nom via la relation chargée avec 'with()' --}}
                                <td>{{ $achat->product->nom_produit ?? 'N/A' }}</td>
                                <td>{{ $achat->qt }}</td>
                                <td>{{ number_format($achat->points_unitaire_achat, 2, ',', ' ') }}</td> {{-- Formatage nombre --}}
                                <td>{{ number_format($achat->prix_unitaire_achat, 2, ',', ' ') }} €</td> {{-- Formatage monétaire --}}
                                <td>{{ number_format($achat->montant_total_ligne, 2, ',', ' ') }} €</td> {{-- Formatage monétaire --}}
                                <td>{{ $achat->online ? 'Oui' : 'Non' }}</td> {{-- Affichage booléen --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Liens de pagination --}}
                <div class="d-flex justify-content-center">
                    {{-- Important: ajouter les paramètres du filtre aux liens de pagination --}}
                    {{ $achats->appends(request()->query())->links() }}
                </div>
             @endif
        </div>
    </div>
</div>
@endsection
