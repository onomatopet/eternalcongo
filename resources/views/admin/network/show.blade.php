{{-- resources/views/layouts/network/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Structure du Réseau')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 print:py-0 print:bg-white">
    <div class="px-4 sm:px-6 lg:px-8 print:px-0">
        {{-- En-tête pour écran uniquement --}}
        <div class="bg-white rounded-lg shadow-sm px-6 py-4 mb-6 print:hidden">
            <nav class="flex items-center text-sm">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Tableau de Bord
                </a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="{{ route('admin.network.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    Export Réseau
                </a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-700 font-medium">Visualisation</span>
            </nav>
        </div>

        {{-- En-tête du rapport (visible à l'écran et à l'impression) --}}
        <div class="bg-white rounded-lg shadow-sm mb-6 print:shadow-none print:mb-4">
            <div class="px-6 py-4 border-b border-gray-200 print:border-b-2 print:border-black">
                {{-- En-tête pour impression uniquement --}}
                <div class="hidden print:block text-center mb-4">
                    <h1 class="text-2xl font-bold">ETERNAL</h1>
                    <p class="text-lg">Details Network Structure</p>
                    <p class="text-sm">eternalcongo.com - contact@eternalcongo.com</p>
                    <p class="text-sm mt-2">Print time: {{ now()->format('d-m-Y') }}</p>
                </div>

                {{-- En-tête pour écran --}}
                <div class="flex items-center justify-between print:hidden">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Structure du Réseau</h1>
                        <p class="text-sm text-gray-600 mt-1">Visualisation détaillée de la hiérarchie</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="window.print()"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Imprimer
                        </button>
                        <button onclick="exportToExcel()"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Excel
                        </button>
                        <a href="{{ route('admin.network.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour
                        </a>
                    </div>
                </div>

                {{-- Informations sur le distributeur principal --}}
                @if(isset($distributeurs) && count($distributeurs) > 0)
                    @php
                        $distributeurPrincipal = collect($distributeurs)->firstWhere('rang', 0);
                        $totalDistributeurs = count($distributeurs);
                        $totalPV = collect($distributeurs)->sum('cumul_collectif');
                    @endphp
                    @if($distributeurPrincipal)
                        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4 print:bg-gray-100 print:border-black">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 print:hidden">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3 print:ml-0">
                                        <h3 class="text-lg font-medium text-blue-900 print:text-black">
                                            {{ $distributeurPrincipal['nom_distributeur'] }} {{ $distributeurPrincipal['pnom_distributeur'] }}
                                            ({{ $distributeurPrincipal['distributeur_id'] }})
                                        </h3>
                                        <p class="text-sm text-blue-700 print:text-black">
                                            Période: {{ request('period', now()->format('Y-m')) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">Total réseau: {{ $totalDistributeurs }} distributeurs</p>
                                    <p class="text-sm text-gray-600">PV Total: ${{ number_format($totalPV, 0, '.', ' ') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Tableau principal --}}
        <div class="bg-white shadow-lg rounded-lg overflow-hidden print:shadow-none">
            <div class="overflow-x-auto">
                <table id="networkTable" class="min-w-full divide-y divide-gray-200 print:text-xs">
                    <thead class="bg-gray-50 print:bg-white">
                        <tr class="print:border-b-2 print:border-black">
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:px-2 print:py-1 print:text-black">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:px-2 print:py-1 print:text-black">
                                Nom & Prénom
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider print:px-2 print:py-1 print:text-black">
                                Rang
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider print:px-2 print:py-1 print:text-black">
                                New PV
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider print:px-2 print:py-1 print:text-black">
                                Total PV
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider print:px-2 print:py-1 print:text-black">
                                Cumulative PV
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:px-2 print:py-1 print:text-black">
                                ID references
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:px-2 print:py-1 print:text-black">
                                References Name
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 print:divide-gray-400">
                        @forelse($distributeurs as $index => $distributeur)
                            <tr class="{{ $distributeur['rang'] == 0 ? 'bg-blue-50 font-semibold print:font-bold' : ($distributeur['rang'] == 1 ? 'bg-gray-50 print:bg-white' : '') }}">
                                {{-- ID --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 print:px-2 print:py-1">
                                    {{ $distributeur['distributeur_id'] }}
                                </td>

                                {{-- Nom & Prénom avec indentation --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm print:px-2 print:py-1">
                                    <div style="padding-left: {{ $distributeur['rang'] * 20 }}px">
                                        <span class="{{ $distributeur['rang'] == 0 ? 'font-semibold text-blue-900 print:text-black print:font-bold' : 'text-gray-900' }}">
                                            {{ strtoupper($distributeur['nom_distributeur']) }} {{ $distributeur['pnom_distributeur'] }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Rang --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center print:px-2 print:py-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $distributeur['rang'] == 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} print:bg-transparent print:px-0 print:py-0">
                                        {{ $distributeur['rang'] }}
                                    </span>
                                </td>

                                {{-- New PV --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 print:px-2 print:py-1">
                                    ${{ number_format($distributeur['new_cumul'] ?? 0, 0, '.', '') }}
                                </td>

                                {{-- Total PV --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 print:px-2 print:py-1">
                                    ${{ number_format($distributeur['cumul_total'] ?? 0, 0, '.', '') }}
                                </td>

                                {{-- Cumulative PV --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900 print:px-2 print:py-1">
                                    ${{ number_format($distributeur['cumul_collectif'] ?? 0, 0, '.', '') }}
                                </td>

                                {{-- ID references --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 print:px-2 print:py-1">
                                    {{ $distributeur['id_distrib_parent'] ?: '-' }}
                                </td>

                                {{-- References Name --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 print:px-2 print:py-1">
                                    @if($distributeur['id_distrib_parent'])
                                        {{ strtoupper($distributeur['nom_parent']) }} {{ $distributeur['pnom_parent'] }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-gray-500">Aucune donnée disponible</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    {{-- Pied de tableau avec totaux --}}
                    @if(isset($distributeurs) && count($distributeurs) > 0)
                        <tfoot class="bg-gray-100 print:bg-white print:border-t-2 print:border-black">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 print:px-2 print:py-1">
                                    TOTAL
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-gray-900 print:px-2 print:py-1">
                                    ${{ number_format(collect($distributeurs)->sum('new_cumul'), 0, '.', ' ') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-gray-900 print:px-2 print:py-1">
                                    ${{ number_format(collect($distributeurs)->sum('cumul_total'), 0, '.', ' ') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-gray-900 print:px-2 print:py-1">
                                    ${{ number_format(collect($distributeurs)->sum('cumul_collectif'), 0, '.', ' ') }}
                                </td>
                                <td colspan="2" class="px-6 py-4 print:px-2 print:py-1"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Pied de page pour impression --}}
        <div class="hidden print:block mt-8 text-center text-xs text-gray-600">
            <p>{{ config('app.name') }} - Document confidentiel - Page 1</p>
        </div>
    </div>
</div>

{{-- Styles d'impression et scripts --}}
@push('styles')
<style>
/* Styles d'impression */
@media print {
    @page {
        size: A4 landscape;
        margin: 10mm;
    }

    body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Forcer les sauts de page appropriés */
    table { page-break-inside: auto; }
    tr { page-break-inside: avoid; page-break-after: auto; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }

    /* Masquer les éléments non nécessaires */
    .print\:hidden { display: none !important; }

    /* Ajuster les tailles */
    .print\:text-xs { font-size: 10px !important; }
    .print\:px-2 { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
    .print\:py-1 { padding-top: 0.25rem !important; padding-bottom: 0.25rem !important; }
}

/* Animation de chargement */
.loading {
    opacity: 0.5;
    pointer-events: none;
}
</style>
@endpush

@push('scripts')
<script>
// Fonction d'export Excel
function exportToExcel() {
    // Créer un élément de lien temporaire
    const link = document.createElement('a');
    link.href = '{{ route("admin.network.export.excel", request()->all()) }}';
    link.download = 'network_export_{{ now()->format("Y-m-d") }}.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Améliorer l'impression
window.addEventListener('beforeprint', function() {
    // Expansion de tous les éléments pour l'impression
    document.querySelectorAll('details').forEach(detail => {
        detail.setAttribute('open', true);
    });
});

window.addEventListener('afterprint', function() {
    // Restaurer l'état après impression si nécessaire
});

// Filtrage et recherche dans le tableau
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter une barre de recherche dynamique si nécessaire
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Rechercher dans le réseau...';
    searchInput.className = 'mb-4 px-4 py-2 border border-gray-300 rounded-lg w-full max-w-md print:hidden';

    // Insérer avant le tableau si souhaité
    // document.querySelector('#networkTable').parentElement.insertBefore(searchInput, document.querySelector('#networkTable'));

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#networkTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>
@endpush

@endsection
