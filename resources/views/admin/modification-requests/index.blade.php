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
