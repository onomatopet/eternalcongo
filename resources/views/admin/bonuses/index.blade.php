{{-- resources/views/admin/bonuses/index.blade.php --}}

@extends('layouts.app') {{-- A CHANGER SELON VOTRE LAYOUT --}}

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Liste des Bonus</h1>
        </div>
    </div>

    {{-- Formulaire de Filtre (Période ET Recherche) --}}
    <div class="card mb-4">
        <div class="card-header">Filtres</div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.bonuses.index') }}" class="row gy-2 gx-3 align-items-center">
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
                <div class="col-auto flex-grow-1">
                    <label for="search" class="visually-hidden">Recherche</label>
                    <input type="text" class="form-control" placeholder="Rechercher (N° Bonus, Nom/Matricule Distrib...)" name="search" id="search" value="{{ $searchTerm ?? '' }}">
                </div>

                {{-- Boutons --}}
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filtrer / Rechercher</button>
                    <a href="{{ route('admin.bonuses.index') }}" class="btn btn-secondary ms-2">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau des Bonus --}}
    <div class="card">
        <div class="card-body">
             @if($bonuses->isEmpty())
                <div class="alert alert-info text-center">
                    Aucun bonus trouvé pour les critères sélectionnés.
                </div>
             @else
                <table class="table table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>ID Bonus</th>
                            <th>Période</th>
                            <th>N° Bonus</th>
                            <th>Distributeur (ID)</th>
                            <th>Distributeur (Nom)</th>
                            <th class="text-end">B. Direct</th>
                            <th class="text-end">B. Indirect</th>
                            <th class="text-end">B. Leadership</th>
                            <th class="text-end">Total Bonus</th>
                            <th class="text-end">Épargne</th>
                            <th>Date Création</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bonuses as $bonus)
                            <tr>
                                <td>{{ $bonus->id }}</td>
                                <td>{{ $bonus->period }}</td>
                                <td>{{ $bonus->num }}</td>
                                <td>{{ $bonus->distributeur_id }}</td>
                                {{-- Affichage via relation --}}
                                <td>{{ $bonus->distributeur->full_name ?? 'N/A' }}</td>
                                <td class="text-end">{{ number_format($bonus->bonus_direct ?? 0, 2, ',', ' ') }} €</td>
                                <td class="text-end">{{ number_format($bonus->bonus_indirect ?? 0, 2, ',', ' ') }} €</td>
                                <td class="text-end">{{ number_format($bonus->bonus_leadership ?? 0, 2, ',', ' ') }} €</td>
                                <td class="text-end"><strong>{{ number_format($bonus->bonus, 2, ',', ' ') }} €</strong></td>
                                <td class="text-end">{{ number_format($bonus->epargne, 2, ',', ' ') }} €</td>
                                <td>{{ $bonus->created_at ? $bonus->created_at->format('d/m/Y') : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Liens de pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $bonuses->withQueryString()->links() }} {{-- withQueryString() est aussi disponible ici --}}
                </div>
             @endif
        </div>
    </div>
</div>
@endsection
