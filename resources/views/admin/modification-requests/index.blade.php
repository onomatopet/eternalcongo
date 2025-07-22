{{-- resources/views/admin/modification-requests/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Demandes de Modification')

@section('content')
<div class="container-fluid">
    {{-- En-tête avec statistiques --}}
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Demandes de Modification</h1>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-yellow-800">En attente</h3>
                        <p class="text-2xl font-semibold text-yellow-900">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-red-800">Risque élevé</h3>
                        <p class="text-2xl font-semibold text-red-900">{{ $stats['high_risk'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-orange-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-orange-800">Expiration proche</h3>
                        <p class="text-2xl font-semibold text-orange-900">{{ $stats['expiring_soon'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white shadow rounded-lg mb-6 p-6">
        <form method="GET" action="{{ route('admin.modification-requests.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select name="status" class="form-select w-full">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvée</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejetée</option>
                    <option value="executed" {{ request('status') === 'executed' ? 'selected' : '' }}>Exécutée</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select name="type" class="form-select w-full">
                    <option value="">Tous les types</option>
                    <option value="change_parent" {{ request('type') === 'change_parent' ? 'selected' : '' }}>Changement de parent</option>
                    <option value="manual_grade" {{ request('type') === 'manual_grade' ? 'selected' : '' }}>Modification de grade</option>
                    <option value="adjust_cumul" {{ request('type') === 'adjust_cumul' ? 'selected' : '' }}>Ajustement de cumuls</option>
                    <option value="reassign_children" {{ request('type') === 'reassign_children' ? 'selected' : '' }}>Réassignation d'enfants</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Niveau de risque</label>
                <select name="risk_level" class="form-select w-full">
                    <option value="">Tous les niveaux</option>
                    <option value="low" {{ request('risk_level') === 'low' ? 'selected' : '' }}>Faible</option>
                    <option value="medium" {{ request('risk_level') === 'medium' ? 'selected' : '' }}>Moyen</option>
                    <option value="high" {{ request('risk_level') === 'high' ? 'selected' : '' }}>Élevé</option>
                    <option value="critical" {{ request('risk_level') === 'critical' ? 'selected' : '' }}>Critique</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="btn btn-primary w-full">Filtrer</button>
            </div>
        </form>
    </div>

    {{-- Liste des demandes --}}
    <div class="bg-white shadow rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risque</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandé par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($modifications as $modification)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $modification->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $modification->getModificationTypeLabel() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $modification->entity_type }} #{{ $modification->entity_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $modification->getRiskLevelColor() }}-100 text-{{ $modification->getRiskLevelColor() }}-800">
                                {{ $modification->getRiskLevelLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $modification->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $modification->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $modification->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $modification->status === 'executed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($modification->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $modification->requestedBy->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $modification->created_at->format('d/m/Y H:i') }}
                            @if($modification->isExpired())
                                <span class="text-red-600 text-xs">(Expirée)</span>
                            @elseif($modification->expires_at && $modification->expires_at->diffInHours(now()) < 24)
                                <span class="text-orange-600 text-xs">(Expire bientôt)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.modification-requests.show', $modification) }}" class="text-indigo-600 hover:text-indigo-900">
                                Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Aucune demande de modification trouvée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $modifications->links() }}
        </div>
    </div>
</div>
@endsection

{{-- resources/views/admin/modification-requests/create-parent-change.blade.php --}}
@extends('layouts.admin')

@section('title', 'Demande de changement de parent')

@section('content')
<div class="container-fluid max-w-4xl">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Demande de changement de parent</h1>
        </div>

        {{-- Informations du distributeur --}}
        <div class="p-6 bg-gray-50">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Distributeur concerné</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Nom</p>
                    <p class="font-medium">{{ $distributeur->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Matricule</p>
                    <p class="font-medium">{{ $distributeur->distributeur_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Parent actuel</p>
                    <p class="font-medium">
                        @if($distributeur->parent)
                            {{ $distributeur->parent->full_name }} (#{{ $distributeur->parent->distributeur_id }})
                        @else
                            <span class="text-gray-500">Aucun parent</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Nombre d'enfants</p>
                    <p class="font-medium">{{ $distributeur->children()->count() }}</p>
                </div>
            </div>
        </div>

        {{-- Formulaire --}}
        <form method="POST" action="{{ route('admin.modification-requests.store.parent-change', $distributeur) }}" class="p-6">
            @csrf

            {{-- Nouveau parent --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nouveau parent <span class="text-red-500">*</span>
                </label>
                <select name="new_parent_id" id="new_parent_id" class="form-select w-full" required>
                    <option value="">Sélectionnez un distributeur</option>
                    @foreach($potentialParents as $parent)
                        @if($parent->id !== $distributeur->id_distrib_parent)
                            <option value="{{ $parent->id }}" {{ old('new_parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->full_name }} (#{{ $parent->distributeur_id }}) - Grade {{ $parent->etoiles_id }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('new_parent_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Raison --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Raison du changement <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" rows="3" class="form-textarea w-full" required
                          placeholder="Expliquez pourquoi ce changement est nécessaire...">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Validation en temps réel --}}
            <div id="validation-result" class="mb-6" style="display: none;">
                <div class="border rounded-lg p-4">
                    <h3 class="font-medium mb-2">Résultat de validation</h3>
                    <div id="validation-content"></div>
                </div>
            </div>

            {{-- Avertissements --}}
            <div class="mb-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-yellow-800 mb-2">Points importants</h3>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Ce changement affectera toute la descendance du distributeur</li>
                        <li>• Les cumuls collectifs seront recalculés</li>
                        <li>• Une approbation sera nécessaire avant l'exécution</li>
                        <li>• Les bonus de la période en cours pourraient être impactés</li>
                    </ul>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.distributeurs.show', $distributeur) }}" class="btn btn-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    Soumettre la demande
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const parentSelect = document.getElementById('new_parent_id');
    const validationResult = document.getElementById('validation-result');
    const validationContent = document.getElementById('validation-content');

    parentSelect.addEventListener('change', function() {
        if (!this.value) {
            validationResult.style.display = 'none';
            return;
        }

        // Validation en temps réel
        fetch('{{ route('admin.modification-requests.validate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                type: 'parent_change',
                entity_id: {{ $distributeur->id }},
                new_value: this.value
            })
        })
        .then(response => response.json())
        .then(data => {
            validationResult.style.display = 'block';

            let html = '';

            if (!data.is_valid) {
                html += '<div class="text-red-600 mb-2">';
                html += '<strong>Blocages:</strong><ul class="list-disc list-inside">';
                data.blockers.forEach(blocker => {
                    html += `<li>${blocker}</li>`;
                });
                html += '</ul></div>';
            }

            if (data.warnings && data.warnings.length > 0) {
                html += '<div class="text-orange-600 mb-2">';
                html += '<strong>Avertissements:</strong><ul class="list-disc list-inside">';
                data.warnings.forEach(warning => {
                    html += `<li>${warning}</li>`;
                });
                html += '</ul></div>';
            }

            if (data.impact) {
                html += '<div class="text-blue-600">';
                html += '<strong>Impact:</strong><ul class="list-disc list-inside">';
                if (data.impact.children_count) {
                    html += `<li>${data.impact.children_count} enfant(s) seront déplacés</li>`;
                }
                if (data.impact.affected_levels) {
                    html += `<li>${data.impact.affected_levels} niveaux affectés dans la hiérarchie</li>`;
                }
                html += '</ul></div>';
            }

            validationContent.innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur validation:', error);
            validationResult.style.display = 'none';
        });
    });
});
</script>
@endpush
@endsection

{{-- resources/views/admin/modification-requests/create-grade-change.blade.php --}}
@extends('layouts.admin')

@section('title', 'Demande de changement de grade')

@section('content')
<div class="container-fluid max-w-4xl">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Demande de changement de grade</h1>
        </div>

        {{-- Informations du distributeur --}}
        <div class="p-6 bg-gray-50">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Distributeur concerné</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Nom</p>
                    <p class="font-medium">{{ $distributeur->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Matricule</p>
                    <p class="font-medium">{{ $distributeur->distributeur_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Grade actuel</p>
                    <p class="font-medium">Grade {{ $distributeur->etoiles_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Cumul individuel</p>
                    <p class="font-medium">
                        @php
                            $currentLevel = \App\Models\LevelCurrent::where('distributeur_id', $distributeur->id)
                                                                   ->where('period', date('Y-m'))
                                                                   ->first();
                        @endphp
                        {{ $currentLevel ? number_format($currentLevel->cumul_individuel) : 'N/A' }} points
                    </p>
                </div>
            </div>
        </div>

        {{-- Formulaire --}}
        <form method="POST" action="{{ route('admin.modification-requests.store.grade-change', $distributeur) }}" class="p-6">
            @csrf

            {{-- Nouveau grade --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nouveau grade <span class="text-red-500">*</span>
                </label>
                <select name="new_grade" id="new_grade" class="form-select w-full" required>
                    <option value="">Sélectionnez un grade</option>
                    @foreach($grades as $grade)
                        @if($grade !== $distributeur->etoiles_id)
                            <option value="{{ $grade }}" {{ old('new_grade') == $grade ? 'selected' : '' }}>
                                Grade {{ $grade }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('new_grade')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Raison --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Raison du changement <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" rows="3" class="form-textarea w-full" required
                          placeholder="Expliquez pourquoi ce changement de grade est nécessaire...">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Justification (conditionnelle) --}}
            <div id="justification-field" class="mb-6" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Justification détaillée <span class="text-red-500">*</span>
                </label>
                <textarea name="justification" rows="5" class="form-textarea w-full"
                          placeholder="Ce changement de grade important nécessite une justification détaillée...">{{ old('justification') }}</textarea>
                <p class="mt-1 text-sm text-gray-600">
                    Un changement de plus de 2 grades nécessite une justification approfondie.
                </p>
                @error('justification')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Validation en temps réel --}}
            <div id="validation-result" class="mb-6" style="display: none;">
                <div class="border rounded-lg p-4">
                    <h3 class="font-medium mb-2">Analyse du changement</h3>
                    <div id="validation-content"></div>
                </div>
            </div>

            {{-- Avertissements --}}
            <div class="mb-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-yellow-800 mb-2">Impact du changement</h3>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Le grade sera immédiatement mis à jour</li>
                        <li>• Les bonus futurs seront calculés avec le nouveau grade</li>
                        <li>• L'historique du changement sera conservé</li>
                        <li>• Une approbation de niveau supérieur peut être nécessaire</li>
                    </ul>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.distributeurs.show', $distributeur) }}" class="btn btn-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    Soumettre la demande
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeSelect = document.getElementById('new_grade');
    const justificationField = document.getElementById('justification-field');
    const validationResult = document.getElementById('validation-result');
    const validationContent = document.getElementById('validation-content');
    const currentGrade = {{ $distributeur->etoiles_id }};

    gradeSelect.addEventListener('change', function() {
        if (!this.value) {
            validationResult.style.display = 'none';
            justificationField.style.display = 'none';
            return;
        }

        const newGrade = parseInt(this.value);
        const gradeDiff = Math.abs(newGrade - currentGrade);

        // Afficher le champ justification si nécessaire
        if (gradeDiff > 2) {
            justificationField.style.display = 'block';
            justificationField.querySelector('textarea').required = true;
        } else {
            justificationField.style.display = 'none';
            justificationField.querySelector('textarea').required = false;
        }

        // Validation en temps réel
        fetch('{{ route('admin.modification-requests.validate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                type: 'grade_change',
                entity_id: {{ $distributeur->id }},
                new_value: newGrade
            })
        })
        .then(response => response.json())
        .then(data => {
            validationResult.style.display = 'block';

            let html = '';

            if (data.warnings && data.warnings.length > 0) {
                html += '<div class="text-orange-600 mb-2">';
                html += '<strong>Avertissements:</strong><ul class="list-disc list-inside">';
                data.warnings.forEach(warning => {
                    html += `<li>${warning}</li>`;
                });
                html += '</ul></div>';
            }

            if (data.impact) {
                html += '<div class="text-blue-600">';
                html += '<strong>Impact:</strong><ul class="list-disc list-inside">';
                if (data.impact.children_with_higher_grade) {
                    html += `<li>${data.impact.children_with_higher_grade} enfant(s) ont un grade supérieur</li>`;
                }
                if (data.impact.bonus_recalculation) {
                    html += '<li>Les bonus devront être recalculés</li>';
                }
                html += '</ul></div>';
            }

            if (data.justification_required) {
                html += '<div class="text-red-600 mt-2">';
                html += '<strong>Justification requise pour ce changement important</strong>';
                html += '</div>';
            }

            validationContent.innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur validation:', error);
            validationResult.style.display = 'none';
        });
    });
});
</script>
@endpush
@endsection
