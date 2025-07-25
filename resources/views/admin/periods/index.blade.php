{{-- resources/views/admin/periods/index.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Gestion des Périodes</h1>

    {{-- Période Courante --}}
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Période Courante</h2>

        @if($currentPeriod)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded">
                    <p class="text-sm text-gray-600">Période</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $currentPeriod->period }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded">
                    <p class="text-sm text-gray-600">Statut</p>
                    <p class="text-xl font-semibold text-green-600">
                        @if($currentPeriod->status === 'open')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Ouverte
                            </span>
                        @elseif($currentPeriod->status === 'validation')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                En Validation
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                Clôturée
                            </span>
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-sm text-gray-600">Ouverte depuis</p>
                    <p class="text-lg">{{ $currentPeriod->opened_at->format('d/m/Y') }}</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex space-x-4">
                @if($currentPeriod->status === 'open')
                    <form action="{{ route('admin.periods.start-validation') }}" method="POST">
                        @csrf
                        <input type="hidden" name="period" value="{{ $currentPeriod->period }}">
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                            Passer en Validation
                        </button>
                    </form>
                @elseif($currentPeriod->status === 'validation')
                    <button onclick="showClosureModal()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                        Clôturer le Mois
                    </button>
                @endif
            </div>
        @else
            <p class="text-gray-500">Aucune période courante définie</p>
        @endif
    </div>

    {{-- Seuils de Bonus par Grade --}}
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Seuils Minimum de PV pour Bonus</h2>

        <form action="{{ route('admin.periods.update-thresholds') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($bonusThresholds as $threshold)
                    <div class="border rounded p-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Grade {{ $threshold->grade }} ⭐
                        </label>
                        <input type="hidden" name="thresholds[{{ $loop->index }}][grade]" value="{{ $threshold->grade }}">
                        <input type="number"
                               name="thresholds[{{ $loop->index }}][minimum_pv]"
                               value="{{ $threshold->minimum_pv }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                               min="0">
                    </div>
                @endforeach
            </div>
            <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Mettre à jour les seuils
            </button>
        </form>
    </div>

    {{-- Historique des Périodes --}}
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Historique des Périodes</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ouverture</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clôture</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clôturé par</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentPeriods as $period)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $period->period }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($period->status === 'closed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Clôturée
                                    </span>
                                @elseif($period->status === 'validation')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Validation
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Ouverte
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $period->opened_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $period->closed_at ? $period->closed_at->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $period->closedBy ? $period->closedBy->name : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal de Confirmation de Clôture --}}
<div id="closureModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.periods.close') }}" method="POST">
                @csrf
                <input type="hidden" name="period" value="{{ $currentPeriod->period ?? '' }}">

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Confirmer la clôture de période
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Êtes-vous sûr de vouloir clôturer la période {{ $currentPeriod->period ?? '' }} ?
                                    Cette action est irréversible et va :
                                </p>
                                <ul class="mt-2 text-sm text-gray-500 list-disc list-inside">
                                    <li>Finaliser tous les calculs de cumuls</li>
                                    <li>Reporter les cumuls vers la période suivante</li>
                                    <li>Empêcher toute modification des achats de cette période</li>
                                </ul>
                            </div>
                            <div class="mt-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="confirm" value="1" required class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Je confirme vouloir clôturer cette période</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Clôturer la période
                    </button>
                    <button type="button" onclick="hideClosureModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showClosureModal() {
    document.getElementById('closureModal').classList.remove('hidden');
}

function hideClosureModal() {
    document.getElementById('closureModal').classList.add('hidden');
}
</script>
@endsection
