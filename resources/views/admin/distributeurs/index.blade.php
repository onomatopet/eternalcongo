{{-- resources/views/admin/distributeurs/index.blade.php --}}

{{-- Assurez-vous d'étendre votre layout principal --}}
{{-- Exemple: @extends('layouts.admin') ou @extends('layouts.app') --}}
@extends('layouts.app') {{-- A CHANGER SELON VOTRE LAYOUT --}}

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Liste des Distributeurs</h1>
        </div>
        <div class="col text-end">
            {{-- Lien pour ajouter un nouveau distributeur (pointe vers la route create) --}}
            {{-- Vous définirez cette route plus tard avec Route::resource ou Route::get(...) --}}
            <a href="{{ route('admin.distributeurs.create') }}" class="btn btn-success">Ajouter un Distributeur</a>
        </div>
    </div

    {{-- Affichage des messages flash (succès, erreur) --}}
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Formulaire de Recherche --}}
    <div class="card mb-4">
        <div class="card-header">Rechercher</div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.distributeurs.index') }}">
                <div class="input-group">
                    {{-- Champ de recherche. 'value' affiche le terme précédent --}}
                    <input type="text" class="form-control" placeholder="Rechercher par nom, prénom, matricule..." name="search" value="{{ $searchTerm ?? '' }}">
                    <button class="btn btn-primary" type="submit">Rechercher</button>
                    {{-- Lien pour effacer la recherche --}}
                    <a href="{{ route('admin.distributeurs.index') }}" class="btn btn-secondary" title="Réinitialiser la recherche"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Matricule</th>
                        <th>Nom Complet</th>
                        <th>Niveau (Etoiles)</th>
                        <th>Rang</th>
                        <th>Parent ID</th>
                        <th>Téléphone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Boucle sur les distributeurs passés par le contrôleur --}}
                    @forelse ($distributeurs as $distributeur)
                        <tr>
                            <td>{{ $distributeur->id }}</td>
                            <td>{{ $distributeur->distributeur_id }}</td>
                            {{-- Utilisation de l'accesseur getFullNameAttribute() défini dans le modèle --}}
                            <td>{{ $distributeur->full_name }}</td>
                            <td>{{ $distributeur->etoiles_id }}</td>
                            <td>{{ $distributeur->rang }}</td>
                            {{-- Afficher le matricule du parent si existant --}}
                            <td>
                                {{ $distributeur->parent ? $distributeur->parent->distributeur_id : 'N/A' }}
                                {{-- Alternative: juste l'ID parent: {{ $distributeur->id_distrib_parent ?? 'N/A' }} --}}
                            </td>
                            <td>{{ $distributeur->tel_distributeur ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.distributeurs.show', $distributeur) }}" class="btn btn-info btn-sm" title="Consulter"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.distributeurs.edit', $distributeur) }}" class="btn btn-warning btn-sm" title="Modifier"><i class="fas fa-edit"></i></a>
                                {{-- <form action="{{ route('admin.distributeurs.destroy', $distributeur) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce distributeur ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Supprimer"><i class="fas fa-trash-alt"></i></button>
                                </form> --}}
                                <button type="button" class="btn btn-danger btn-sm" title="Supprimer (TODO)" onclick="alert('Suppression non implémentée');"><i class="fas fa-trash-alt"></i></button>

                            </td>
                        </tr>
                    @empty
                        {{-- Message si aucun distributeur n'est trouvé --}}
                        <tr>
                            <td colspan="8" class="text-center">Aucun distributeur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Liens de pagination --}}
            <div class="d-flex justify-content-center">
                {{-- withQueryString() dans le contrôleur gère l'ajout des paramètres --}}
               {{ $distributeurs->links() }}
           </div>

        </div>
    </div>
</div>
@endsection

{{-- Optionnel: Ajouter Font Awesome si vous utilisez les icônes --}}
@push('styles')
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> --}}
@endpush
