{{-- resources/views/layouts/network/pdf.blade.php --}}
@extends('layouts.admin')

@section('title', 'Export Réseau - Structure Détaillée')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="px-4 sm:px-6 lg:px-8">
        {{-- En-tête du rapport --}}
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">ETERNAL Details Network Structure</h1>
                        <p class="text-sm text-gray-600 mt-1">eternalcongo.com - contact@eternalcongo.com</p>
                        <p class="text-sm text-gray-500">Print time: {{ now()->format('d-m-Y') }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="window.print()"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Imprimer
                        </button>
                        <a href="{{ route('admin.network.export.pdf', request()->all()) }}"
                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export PDF
                        </a>
                    </div>
                </div>

                {{-- Informations sur le distributeur principal et la période --}}
                @if(isset($distributeurs) && count($distributeurs) > 0)
                    @php
                        $distributeurPrincipal = collect($distributeurs)->firstWhere('rang', 0);
                    @endphp
                    @if($distributeurPrincipal)
                        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-medium text-blue-900">
                                        {{ $distributeurPrincipal['nom_distributeur'] }} {{ $distributeurPrincipal['pnom_distributeur'] }}
                                        ({{ $distributeurPrincipal['distributeur_id'] }})
                                    </h3>
                                    <p class="text-sm text-blue-700">
                                        Période: {{ request('period', now()->format('Y-m')) }} |
                                        Total réseau: {{ count($distributeurs) }} distributeurs
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Tableau de la structure du réseau --}}
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom & Prénom
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rang
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                New PV
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total PV
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cumulative PV
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID references
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                References Name
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($distributeurs as $distributeur)
                            <tr class="{{ $distributeur['rang'] == 0 ? 'bg-blue-50 font-semibold' : ($distributeur['rang'] == 1 ? 'bg-gray-50' : '') }}">
                                {{-- ID (Matricule) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $distributeur['distributeur_id'] }}
                                </td>

                                {{-- Nom & Prénom avec indentation selon le rang --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div style="padding-left: {{ $distributeur['rang'] * 20 }}px">
                                        <span class="{{ $distributeur['rang'] == 0 ? 'font-semibold text-blue-900' : 'text-gray-900' }}">
                                            {{ $distributeur['nom_distributeur'] }} {{ $distributeur['pnom_distributeur'] }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Rang --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $distributeur['rang'] == 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $distributeur['rang'] }}
                                    </span>
                                </td>

                                {{-- New PV --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                    ${{ number_format($distributeur['new_cumul'] ?? 0, 0, '.', '') }}
                                </td>

                                {{-- Total PV --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                    ${{ number_format($distributeur['cumul_total'] ?? 0, 0, '.', '') }}
                                </td>

                                {{-- Cumulative PV --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                    ${{ number_format($distributeur['cumul_collectif'] ?? 0, 0, '.', '') }}
                                </td>

                                {{-- ID references (Matricule parrain) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $distributeur['id_distrib_parent'] ?: '-' }}
                                </td>

                                {{-- References Name (Nom parrain) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($distributeur['id_distrib_parent'])
                                        {{ $distributeur['nom_parent'] }} {{ $distributeur['pnom_parent'] }}
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
                                        <p class="text-gray-500">Aucune donnée disponible pour cette période</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Résumé en bas de tableau --}}
            @if(isset($distributeurs) && count($distributeurs) > 0)
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Total distributeurs:</span> {{ count($distributeurs) }}
                        </div>
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Total PV cumulés:</span>
                            ${{ number_format(collect($distributeurs)->sum('cumul_collectif'), 0, '.', ' ') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Styles d'impression --}}
@push('styles')
<style media="print">
    @page {
        size: A4 landscape;
        margin: 10mm;
    }

    body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .no-print {
        display: none !important;
    }

    table {
        font-size: 10pt;
    }

    th, td {
        padding: 4px 8px !important;
    }
</style>
@endpush

@endsection
