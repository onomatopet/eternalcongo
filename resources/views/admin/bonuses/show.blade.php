{{-- resources/views/admin/bonuses/show.blade.php --}}

@extends('layouts.admin')

@section('title', 'Détails du bonus')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="px-4 sm:px-6 lg:px-8">
        {{-- En-tête avec fil d'Ariane --}}
        <div class="bg-white rounded-lg shadow-sm px-6 py-4 mb-6">
            <nav class="flex items-center text-sm">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Tableau de Bord
                </a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="{{ route('admin.bonuses.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    Bonus
                </a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-700 font-medium">{{ $bonus->num }}</span>
            </nav>
        </div>

        {{-- Actions --}}
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Reçu de bonus</h1>
                <p class="mt-1 text-gray-600">Période : {{ $bonus->period }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.bonuses.pdf', $bonus) }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger PDF
                </a>
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimer
                </button>
            </div>
        </div>

        {{-- Contenu principal --}}
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            {{-- En-tête du reçu --}}
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold">REÇU DE BONUS</h2>
                        <p class="mt-1 text-green-100">N° {{ $bonus->num }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-green-100">Date d'émission</p>
                        <p class="text-lg font-semibold">{{ $bonus->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                {{-- Informations du bénéficiaire --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bénéficiaire</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Nom complet</p>
                                <p class="font-medium text-gray-900">{{ $bonus->distributeur->pnom_distributeur }} {{ $bonus->distributeur->nom_distributeur }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Matricule</p>
                                <p class="font-medium text-gray-900">#{{ $bonus->distributeur->distributeur_id }}</p>
                            </div>
                            @if($bonus->distributeur->tel_distributeur)
                                <div>
                                    <p class="text-sm text-gray-500">Téléphone</p>
                                    <p class="font-medium text-gray-900">{{ $bonus->distributeur->tel_distributeur }}</p>
                                </div>
                            @endif
                            @if($bonus->distributeur->adress_distributeur)
                                <div>
                                    <p class="text-sm text-gray-500">Adresse</p>
                                    <p class="font-medium text-gray-900">{{ $bonus->distributeur->adress_distributeur }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Détail des bonus --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Détail des bonus</h3>
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type de bonus
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Montant
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Bonus Direct
                                        <p class="text-xs text-gray-500">Achats personnels</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                        {{ number_format($bonus->bonus_direct ?? 0, 0, ',', ' ') }} XAF
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Bonus Indirect
                                        <p class="text-xs text-gray-500">Achats des filleuls</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                        {{ number_format($bonus->bonus_indirect ?? 0, 0, ',', ' ') }} XAF
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Bonus Leadership
                                        <p class="text-xs text-gray-500">Performance du réseau</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                        {{ number_format($bonus->bonus_leadership ?? 0, 0, ',', ' ') }} XAF
                                    </td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        TOTAL BRUT
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-green-600">
                                        {{ number_format($bonus->bonus, 0, ',', ' ') }} XAF
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Déductions et montant net --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Déductions et montant net</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dl class="space-y-3">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-500">Total brut</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($bonus->bonus, 0, ',', ' ') }} XAF</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-500">Épargne (10%)</dt>
                                <dd class="text-sm font-medium text-red-600">- {{ number_format($bonus->epargne, 0, ',', ' ') }} XAF</dd>
                            </div>
                            <div class="border-t pt-3">
                                <div class="flex items-center justify-between">
                                    <dt class="text-base font-semibold text-gray-900">NET À PAYER</dt>
                                    <dd class="text-xl font-bold text-green-600">{{ number_format($bonus->bonus - $bonus->epargne, 0, ',', ' ') }} XAF</dd>
                                </div>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Achats associés --}}
                @if($bonus->distributeur->achats->where('period', $bonus->period)->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Achats de la période</h3>
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Produit
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Qté
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Prix unit.
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Points
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bonus->distributeur->achats->where('period', $bonus->period) as $achat)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $achat->product->nom_produit }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                                {{ $achat->qt }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                                {{ number_format($achat->prix_unitaire_achat, 0, ',', ' ') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                                {{ $achat->points_unitaire_achat * $achat->qt }} PV
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                                {{ number_format($achat->montant_total_ligne, 0, ',', ' ') }} XAF
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <th scope="row" colspan="4" class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                                            Total achats
                                        </th>
                                        <td class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                                            {{ number_format($bonus->distributeur->achats->where('period', $bonus->period)->sum('montant_total_ligne'), 0, ',', ' ') }} XAF
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Signature et mentions --}}
                <div class="border-t pt-8 mt-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Le bénéficiaire</p>
                            <div class="h-20 border-b-2 border-gray-300"></div>
                            <p class="mt-2 text-sm text-gray-700">{{ $bonus->distributeur->pnom_distributeur }} {{ $bonus->distributeur->nom_distributeur }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-2">La direction</p>
                            <div class="h-20 border-b-2 border-gray-300"></div>
                            <p class="mt-2 text-sm text-gray-700">{{ config('app.name') }}</p>
                        </div>
                    </div>

                    <div class="mt-8 text-center text-xs text-gray-500">
                        <p>Ce document est généré automatiquement et ne nécessite pas de signature manuscrite.</p>
                        <p class="mt-1">{{ config('app.name') }} - Tous droits réservés {{ date('Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Styles d'impression --}}
@push('styles')
<style media="print">
    @page {
        size: A4;
        margin: 1cm;
    }

    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    .no-print {
        display: none !important;
    }

    nav, .mb-6.flex {
        display: none !important;
    }

    .shadow-sm {
        box-shadow: none !important;
    }
</style>
@endpush
@endsection
