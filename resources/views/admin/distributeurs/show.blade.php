{{-- resources/views/admin/distributeurs/show.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Détails du Distributeur #{{ $distributeur->distributeur_id }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.distributeurs.edit', $distributeur) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('admin.distributeurs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
    </div>
</div>

<div class="row">
    {{-- Informations personnelles --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user me-2"></i>Informations personnelles
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Matricule :</th>
                        <td><span class="badge bg-primary fs-6">#{{ $distributeur->distributeur_id }}</span></td>
                    </tr>
                    <tr>
                        <th>Nom complet :</th>
                        <td><strong>{{ $distributeur->pnom_distributeur }} {{ $distributeur->nom_distributeur }}</strong></td>
                    </tr>
                    <tr>
                        <th>Téléphone :</th>
                        <td>{{ $distributeur->tel_distributeur ?? 'Non renseigné' }}</td>
                    </tr>
                    <tr>
                        <th>Adresse :</th>
                        <td>{{ $distributeur->adress_distributeur ?? 'Non renseignée' }}</td>
                    </tr>
                    <tr>
                        <th>Inscription :</th>
                        <td>{{ $distributeur->created_at->format('d/m/Y à H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Informations MLM --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-network-wired me-2"></i>Informations MLM
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Parent :</th>
                        <td>
                            @if($distributeur->parent)
                                <a href="{{ route('admin.distributeurs.show', $distributeur->parent) }}" class="text-decoration-none">
                                    #{{ $distributeur->parent->distributeur_id }} - {{ $distributeur->parent->pnom_distributeur }} {{ $distributeur->parent->nom_distributeur }}
                                </a>
                            @else
                                <span class="text-muted">Distributeur racine</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Niveau :</th>
                        <td><span class="badge bg-info">{{ $distributeur->etoiles_id }} étoile(s)</span></td>
                    </tr>
                    <tr>
                        <th>Rang :</th>
                        <td><strong>{{ $distributeur->rang }}</strong></td>
                    </tr>
                    <tr>
                        <th>Période validée :</th>
                        <td>
                            @if($distributeur->statut_validation_periode)
                                <span class="badge bg-success">Oui</span>
                            @else
                                <span class="badge bg-warning">Non</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Enfants directs :</th>
                        <td><strong>{{ $distributeur->children->count() }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Enfants directs --}}
@if($distributeur->children->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-users me-2"></i>Enfants directs ({{ $distributeur->children->count() }})
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom</th>
                                <th>Niveau</th>
                                <th>Inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($distributeur->children as $enfant)
                            <tr>
                                <td><span class="badge bg-primary">#{{ $enfant->distributeur_id }}</span></td>
                                <td>{{ $enfant->pnom_distributeur }} {{ $enfant->nom_distributeur }}</td>
                                <td><span class="badge bg-info">{{ $enfant->etoiles_id }} étoile(s)</span></td>
                                <td>{{ $enfant->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.distributeurs.show', $enfant) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Derniers achats --}}
@if($distributeur->achats->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-shopping-cart me-2"></i>Derniers achats ({{ $distributeur->achats->count() }} au total)
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Période</th>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($distributeur->achats->take(10) as $achat)
                            <tr>
                                <td>{{ $achat->created_at->format('d/m/Y') }}</td>
                                <td><span class="badge bg-info">{{ $achat->period }}</span></td>
                                <td>
                                    @if($achat->product)
                                        {{ $achat->product->nom_produit }}
                                    @else
                                        <span class="text-muted">Produit supprimé</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $achat->qt }}</span></td>
                                <td><strong>{{ number_format($achat->montant_total_ligne, 2) }}€</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($distributeur->achats->count() > 10)
                    <div class="text-center">
                        <a href="{{ route('admin.achats.index', ['search' => $distributeur->distributeur_id]) }}" class="btn btn-sm btn-outline-primary">
                            Voir tous les achats
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection