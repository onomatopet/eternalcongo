@extends('layouts.admin')

@section('title', 'Réseau - ' . $mainDistributor->nom_distributeur)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="px-4 sm:px-6 lg:px-8">
        {{-- En-tête avec statistiques --}}
        <div class="mb-8">
            {{-- Breadcrumb et actions --}}
            <div class="bg-white rounded-lg shadow-sm px-6 py-4 mb-6">
                <div class="flex items-center justify-between">
                    <nav class="flex items-center text-sm">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                            Tableau de bord
                        </a>
                        <span class="mx-2 text-gray-400">/</span>
                        <a href="{{ route('admin.network.index') }}" class="text-gray-500 hover:text-gray-700">
                            Export Réseau
                        </a>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-700 font-medium">Aperçu</span>
                    </nav>
                    <div class="flex space-x-3 no-print">
                        <button onclick="window.print()"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-sm hover:bg-gray-700 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/>
                            </svg>
                            Imprimer
                        </button>
                        <form action="{{ route('admin.network.export.pdf') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="distributeur_id" value="{{ $mainDistributor->distributeur_id }}">
                            <input type="hidden" name="period" value="{{ $period }}">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-sm hover:bg-red-700 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Export PDF
                            </button>
                        </form>
                        <form action="{{ route('admin.network.export.excel') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="distributeur_id" value="{{ $mainDistributor->distributeur_id }}">
                            <input type="hidden" name="period" value="{{ $period }}">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-sm hover:bg-green-700 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export Excel
                            </button>
                        </form>
                        <a href="{{ route('admin.network.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Nouvelle recherche
                        </a>
                    </div>
                </div>
            </div>

            {{-- Informations du distributeur principal --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">
                                    {{ substr($mainDistributor->nom_distributeur, 0, 1) }}{{ substr($mainDistributor->pnom_distributeur, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold text-gray-900">
                                {{ $mainDistributor->nom_distributeur }} {{ $mainDistributor->pnom_distributeur }}
                            </h1>
                            <div class="flex items-center mt-1 space-x-4 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                    Matricule: <strong>{{ $mainDistributor->distributeur_id }}</strong>
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Période: <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $period)->locale('fr')->isoFormat('MMMM YYYY') }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-blue-600">{{ $totalCount }}</div>
                        <div class="text-sm text-gray-600">Distributeurs dans le réseau</div>
                    </div>
                </div>
            </div>

            {{-- Statistiques rapides --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @php
                    $distributeursCollection = collect($distributeurs);
                    $stats = [
                        'total_pv' => $distributeursCollection->sum('cumul_collectif'),
                        'avg_grade' => round($distributeursCollection->avg('etoiles'), 1),
                        'max_level' => $distributeursCollection->max('rang'),
                        'active_count' => $distributeursCollection->where('new_cumul', '>', 0)->count()
                    ];
                @endphp

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total PV Collectif</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_pv'], 0, ',', ' ') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Grade moyen</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['avg_grade'] }} ★</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Profondeur max</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['max_level'] }} niveaux</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Actifs ce mois</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_count'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau des données --}}
        <div class="bg-white shadow-lg rounded-lg overflow-hidden print-area">
            {{-- En-tête d'impression (visible uniquement à l'impression) --}}
            <div class="hidden print:block p-6 border-b">
                <h2 class="text-xl font-bold text-center">
                    Réseau de {{ $mainDistributor->nom_distributeur }} {{ $mainDistributor->pnom_distributeur }}
                    ({{ $mainDistributor->distributeur_id }})
                </h2>
                <p class="text-center text-sm text-gray-600 mt-1">
                    Période : {{ \Carbon\Carbon::createFromFormat('Y-m', $period)->locale('fr')->isoFormat('MMMM YYYY') }} |
                    Total : {{ $totalCount }} distributeur(s) |
                    Imprimé le : {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Matricule
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Niveau
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom & Prénom
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grade
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                New PV
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total PV
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cumul Collectif
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Parent
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom Parent
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($distributeurs as $index => $distributeur)
                            <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $distributeur['distributeur_id'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center text-sm text-gray-500">
                                        @for($i = 0; $i < $distributeur['rang']; $i++)
                                            <span class="text-gray-300 mr-1">—</span>
                                        @endfor
                                        <span class="ml-1 font-medium">{{ $distributeur['rang'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center">
                                                <span class="text-xs font-medium text-white">
                                                    {{ substr($distributeur['nom_distributeur'], 0, 1) }}{{ substr($distributeur['pnom_distributeur'], 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $distributeur['nom_distributeur'] }} {{ $distributeur['pnom_distributeur'] }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $distributeur['etoiles'] }} ★
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">
                                    {{ number_format($distributeur['new_cumul'], 0, ',', ' ') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">
                                    {{ number_format($distributeur['cumul_total'], 0, ',', ' ') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ number_format($distributeur['cumul_collectif'], 0, ',', ' ') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                    @if($distributeur['id_distrib_parent'])
                                        <span class="font-mono text-xs">{{ $distributeur['id_distrib_parent'] }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    @if($distributeur['id_distrib_parent'])
                                        {{ $distributeur['nom_parent'] }} {{ $distributeur['pnom_parent'] }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 print:hidden">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900">
                                Total du réseau
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">
                                {{ number_format($distributeursCollection->sum('new_cumul'), 0, ',', ' ') }}
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">
                                {{ number_format($distributeursCollection->sum('cumul_total'), 0, ',', ' ') }}
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">
                                {{ number_format($distributeursCollection->sum('cumul_collectif'), 0, ',', ' ') }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Pied de page d'impression --}}
            <div class="hidden print:block p-6 border-t text-center">
                <p class="text-sm text-gray-600">
                    {{ config('app.name') }} - eternalcongo.com - contact@eternalcongo.com
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Styles d'impression --}}
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .print-area, .print-area * {
            visibility: visible;
        }

        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-print {
            display: none !important;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        table {
            font-size: 9pt;
        }

        .bg-gray-50 {
            background-color: #f9fafb !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        tr {
            page-break-inside: avoid;
        }
    }
</style>
@endsection
