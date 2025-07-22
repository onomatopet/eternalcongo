@extends('layouts.admin')

@section('title', 'Détails de la demande #' . $deletionRequest->id)

@section('content')
<div class="container-fluid">
    {{-- En-tête --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Demande de suppression #{{ $deletionRequest->id }}</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Créée le {{ $deletionRequest->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <a href="{{ route('admin.deletion-requests.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Informations principales --}}
        <div class="lg:col-span-2">
            {{-- Détails de la demande --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informations de la demande</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type d'entité</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($deletionRequest->entity_type) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($deletionRequest->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($deletionRequest->status == 'approved') bg-blue-100 text-blue-800
                                    @elseif($deletionRequest->status == 'completed') bg-green-100 text-green-800
                                    @elseif($deletionRequest->status == 'rejected') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $deletionRequest->getStatusLabel() }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Demandeur</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $deletionRequest->requestedBy->name ?? 'N/A' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de demande</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $deletionRequest->created_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>

                        @if($deletionRequest->approved_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">
                                {{ $deletionRequest->status == 'rejected' ? 'Rejetée par' : 'Approuvée par' }}
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $deletionRequest->approvedBy->name ?? 'N/A' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">
                                Date {{ $deletionRequest->status == 'rejected' ? 'de rejet' : 'd\'approbation' }}
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $deletionRequest->approved_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                        @endif

                        @if($deletionRequest->completed_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date d'exécution</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $deletionRequest->completed_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                        @endif
                    </dl>

                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">Raison de la demande</dt>
                        <dd class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md p-3">
                            {{ $deletionRequest->reason }}
                        </dd>
                    </div>

                    @if($deletionRequest->rejection_reason)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">Raison du rejet</dt>
                        <dd class="mt-1 text-sm text-gray-900 bg-red-50 rounded-md p-3 text-red-700">
                            {{ $deletionRequest->rejection_reason }}
                        </dd>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Informations sur l'entité --}}
            <div class="bg-white shadow rounded-lg mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Entité concernée</h3>
                </div>
                <div class="px-6 py-4">
                    @if($entity)
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        {{ ucfirst($deletionRequest->entity_type) }} : {{ $deletionRequest->getEntityName() }}
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        @if($deletionRequest->entity_type == 'distributeur' && $entity)
                                            <p>Matricule : {{ $entity->distributeur_id }}</p>
                                            <p>Nom : {{ $entity->full_name }}</p>
                                            <p>Téléphone : {{ $entity->tel_distributeur ?? 'N/A' }}</p>
                                            <p>Grade actuel : {{ $entity->currentLevel->grade ?? 'N/A' }} étoiles</p>
                                        @elseif($deletionRequest->entity_type == 'achat' && $entity)
                                            <p>Distributeur : {{ $entity->distributeur->full_name ?? 'N/A' }}</p>
                                            <p>Produit : {{ $entity->produit->nom_produit ?? 'N/A' }}</p>
                                            <p>Quantité : {{ $entity->quantite }}</p>
                                            <p>Montant : {{ number_format($entity->montant_total_ligne, 0, ',', ' ') }} F CFA</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-600">
                                        L'entité a déjà été supprimée ou n'existe plus.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Données de validation --}}
            @if($deletionRequest->validation_data)
            <div class="bg-white shadow rounded-lg mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Analyse d'impact</h3>
                </div>
                <div class="px-6 py-4">
                    @php
                        $validationData = $deletionRequest->validation_data;
                    @endphp

                    @if(isset($validationData['blockers']) && count($validationData['blockers']) > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-red-700 mb-2">Blockers</h4>
                        <ul class="space-y-1">
                            @foreach($validationData['blockers'] as $blocker)
                            <li class="text-sm text-red-600 flex items-start">
                                <svg class="h-4 w-4 text-red-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                {{ $blocker }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($validationData['warnings']) && count($validationData['warnings']) > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-yellow-700 mb-2">Avertissements</h4>
                        <ul class="space-y-1">
                            @foreach($validationData['warnings'] as $warning)
                            <li class="text-sm text-yellow-600 flex items-start">
                                <svg class="h-4 w-4 text-yellow-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                {{ $warning }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(isset($validationData['impact_level']))
                    <div class="mt-4">
                        <span class="text-sm font-medium text-gray-700">Niveau d'impact : </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($validationData['impact_level'] == 'low') bg-green-100 text-green-800
                            @elseif($validationData['impact_level'] == 'medium') bg-yellow-100 text-yellow-800
                            @elseif($validationData['impact_level'] == 'high') bg-orange-100 text-orange-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($validationData['impact_level']) }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Actions et timeline --}}
        <div class="lg:col-span-1">
            {{-- Actions disponibles --}}
            @if($deletionRequest->isPending() && Auth::user()->hasPermission('approve_deletions'))
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <form action="{{ route('admin.deletion-requests.approve', $deletionRequest) }}" method="POST">
                        @csrf
                        <input type="hidden" name="execute_immediately" value="0">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Approuver
                        </button>
                    </form>

                    <form action="{{ route('admin.deletion-requests.approve', $deletionRequest) }}" method="POST">
                        @csrf
                        <input type="hidden" name="execute_immediately" value="1">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                onclick="return confirm('Approuver et exécuter immédiatement la suppression ?')">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Approuver et exécuter
                        </button>
                    </form>

                    <button onclick="openRejectModal()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Rejeter
                    </button>
                </div>
            </div>
            @endif

            @if($deletionRequest->isApproved() && !$deletionRequest->isCompleted() && Auth::user()->hasPermission('execute_deletions'))
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>
                <div class="px-6 py-4">
                    <form action="{{ route('admin.deletion-requests.execute', $deletionRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                onclick="return confirm('Exécuter la suppression maintenant ? Cette action est irréversible.')">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Exécuter la suppression
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Timeline --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Historique</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            {{-- Création --}}
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    Demande créée par <span class="font-medium text-gray-900">{{ $deletionRequest->requestedBy->name ?? 'N/A' }}</span>
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $deletionRequest->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            {{-- Approbation/Rejet --}}
                            @if($deletionRequest->approved_at)
                            <li>
                                <div class="relative pb-8">
                                    @if($deletionRequest->completed_at)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full {{ $deletionRequest->status == 'rejected' ? 'bg-red-500' : 'bg-green-500' }} flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($deletionRequest->status == 'rejected')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    @endif
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    {{ $deletionRequest->status == 'rejected' ? 'Rejetée' : 'Approuvée' }} par <span class="font-medium text-gray-900">{{ $deletionRequest->approvedBy->name ?? 'N/A' }}</span>
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $deletionRequest->approved_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif

                            {{-- Exécution --}}
                            @if($deletionRequest->completed_at)
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    Suppression exécutée
                                                </p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $deletionRequest->completed_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de rejet --}}
@if($deletionRequest->isPending())
<div id="rejectModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <form action="{{ route('admin.deletion-requests.reject', $deletionRequest) }}" method="POST">
                @csrf
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Rejeter la demande
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Veuillez indiquer la raison du rejet de cette demande.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">
                        Raison du rejet
                    </label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                              placeholder="Expliquez pourquoi cette demande est rejetée..."></textarea>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
                        Rejeter
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-1 sm:mt-0 sm:text-sm">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}
</script>
@endpush
@endif
@endsection
