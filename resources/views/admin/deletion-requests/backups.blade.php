@extends('layouts.admin')

@section('title', 'Gestion des Backups')

@section('content')
<div class="container-fluid">
    {{-- En-tête --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Gestion des Backups</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Backups créés lors des suppressions avec possibilité de restauration
                </p>
            </div>
            <a href="{{ route('admin.deletion-requests.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour aux demandes
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('admin.deletion-requests.backups') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="entity_type" class="block text-sm font-medium text-gray-700">Type d'entité</label>
                <select id="entity_type" name="entity_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Tous les types</option>
                    <option value="distributeur" {{ request('entity_type') == 'distributeur' ? 'selected' : '' }}>Distributeur</option>
                    <option value="achat" {{ request('entity_type') == 'achat' ? 'selected' : '' }}>Achat</option>
                    <option value="product" {{ request('entity_type') == 'product' ? 'selected' : '' }}>Produit</option>
                    <option value="bonus" {{ request('entity_type') == 'bonus' ? 'selected' : '' }}>Bonus</option>
                </select>
            </div>

            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">Date début</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">Date fin</label>
                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="flex items-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    {{-- Liste des backups --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Backups disponibles</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID Backup
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Entité
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Créé par
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date création
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($backups as $backup)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $backup->backup_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($backup->entity_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $backup->entity_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $backup->creator->name ?? 'Système' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $backup->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($backup->restored_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Restauré
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Disponible
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if(!$backup->restored_at && Auth::user()->hasPermission('restore_backups'))
                                    <form action="{{ route('admin.deletion-requests.restore-backup') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="backup_id" value="{{ $backup->backup_id }}">
                                        <button type="submit"
                                                class="text-indigo-600 hover:text-indigo-900"
                                                onclick="return confirm('Êtes-vous sûr de vouloir restaurer ce backup ? Cette action va recréer l\'entité supprimée.')">
                                            Restaurer
                                        </button>
                                    </form>
                                @endif

                                <button onclick="showBackupDetails('{{ $backup->backup_id }}')"
                                        class="text-gray-600 hover:text-gray-900 ml-3">
                                    Détails
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Aucun backup disponible
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($backups->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $backups->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal de détails du backup --}}
<div id="backupDetailsModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Détails du backup</h3>
            <div class="mt-2 px-7 py-3">
                <pre id="backupContent" class="bg-gray-100 p-4 rounded overflow-auto max-h-96 text-xs"></pre>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Stockage des données de backup pour l'affichage
    const backupData = {
        @foreach($backups as $backup)
            '{{ $backup->backup_id }}': @json($backup->backup_data){{ !$loop->last ? ',' : '' }}
        @endforeach
    };

    function showBackupDetails(backupId) {
        const modal = document.getElementById('backupDetailsModal');
        const content = document.getElementById('backupContent');

        if (backupData[backupId]) {
            content.textContent = JSON.stringify(backupData[backupId], null, 2);
            modal.classList.remove('hidden');
        }
    }

    function closeModal() {
        document.getElementById('backupDetailsModal').classList.add('hidden');
    }

    // Fermer le modal en cliquant en dehors
    document.getElementById('backupDetailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush
@endsection
