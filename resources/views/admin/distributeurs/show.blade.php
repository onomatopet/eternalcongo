{{-- resources/views/admin/distributeurs/show.blade.php --}}

@extends('layouts.app') {{-- Adaptez --}}

@section('title', "Détails Distributeur: " . $distributeur->full_name)

@section('content')
<div class="container">
    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            {{-- Fil d'Ariane --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de Bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.distributeurs.index') }}">Distributeurs</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $distributeur->full_name }} (#{{ $distributeur->distributeur_id }})</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            {{-- Boutons d'action --}}
            <a href="{{ route('admin.distributeurs.edit', $distributeur) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <form action="{{ route('admin.distributeurs.destroy', $distributeur) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce distributeur ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer
                </button>
            </form>
            <a href="{{ route('admin.distributeurs.index') }}" class="btn btn-secondary btn-sm ms-2">
                <i class="fas fa-arrow-left me-1"></i> Retour Liste
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Informations Détaillées
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID Primaire</dt>
                <dd class="col-sm-9">{{ $distributeur->id }}</dd>

                <dt class="col-sm-3">Matricule</dt>
                <dd class="col-sm-9">{{ $distributeur->distributeur_id }}</dd>

                <dt class="col-sm-3">Nom Complet</dt>
                <dd class="col-sm-9">{{ $distributeur->full_name }}</dd>

                <dt class="col-sm-3">Prénom</dt>
                <dd class="col-sm-9">{{ $distributeur->pnom_distributeur }}</dd>

                <dt class="col-sm-3">Nom</dt>
                <dd class="col-sm-9">{{ $distributeur->nom_distributeur }}</dd>

                <dt class="col-sm-3">Niveau (Étoiles)</dt>
                <dd class="col-sm-9">{{ $distributeur->etoiles_id }}</dd>

                <dt class="col-sm-3">Rang</dt>
                <dd class="col-sm-9">{{ $distributeur->rang }}</dd>

                <dt class="col-sm-3">Téléphone</dt>
                <dd class="col-sm-9">{{ $distributeur->tel_distributeur ?? '---' }}</dd>

                <dt class="col-sm-3">Adresse</dt>
                <dd class="col-sm-9">{{ $distributeur->adress_distributeur ?? '---' }}</dd>

                <dt class="col-sm-3">Parent (Sponsor)</dt>
                <dd class="col-sm-9">
                    @if($distributeur->parent)
                        <a href="{{ route('admin.distributeurs.show', $distributeur->parent) }}">
                            #{{ $distributeur->parent->distributeur_id }} - {{ $distributeur->parent->full_name }}
                        </a>
                    @else
                        Aucun (Racine)
                    @endif
                </dd>

                <dt class="col-sm-3">Statut Validation Période</dt>
                <dd class="col-sm-9">{{ $distributeur->statut_validation_periode ? 'Oui' : 'Non' }}</dd>

                <dt class="col-sm-3">Date Création</dt>
                <dd class="col-sm-9">{{ $distributeur->created_at ? $distributeur->created_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>

                <dt class="col-sm-3">Dernière MàJ</dt>
                <dd class="col-sm-9">{{ $distributeur->updated_at ? $distributeur->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</dd>

            </dl>
        </div>
    </div>

    {{-- Section Enfants Directs (Optionnel) --}}
    <div class="card mt-4">
         <div class="card-header">
             Enfants Directs ({{ $distributeur->children->count() }})
         </div>
         <div class="card-body p-0">
             @if($distributeur->children->isNotEmpty())
             <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Matricule</th>
                            <th>Nom Complet</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($distributeur->children as $child)
                        <tr>
                            <td>{{ $child->id }}</td>
                            <td>{{ $child->distributeur_id }}</td>
                            <td>{{ $child->full_name }}</td>
                            <td>
                                <a href="{{ route('admin.distributeurs.show', $child) }}" class="btn btn-secondary btn-sm" title="Voir Enfant">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
             </div>
             @else
                 <p class="p-3 text-muted">Ce distributeur n'a pas d'enfants directs.</p>
             @endif
         </div>
    </div>

     {{-- Ajouter d'autres sections si besoin: Achats récents, Bonus récents, etc. --}}

</div>
@endsection
