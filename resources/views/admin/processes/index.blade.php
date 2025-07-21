@extends('layouts.admin')

@section('content')
<!-- Header -->
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Processus Métier
        </h2>
        <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.23 10.661a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                </svg>
                Calculs automatisés et régularisation des grades
            </div>
        </div>
    </div>
</div>

<!-- Messages flash -->
@if(session('success'))
    <div class="mt-4 rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.23 10.661a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mt-4 rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
@endif

<!-- Sortie de commande -->
@if(session('command_output'))
    <div class="mt-4 rounded-md bg-blue-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Résultat de l'exécution :</h3>
                <div class="mt-2">
                    <pre class="text-xs text-blue-700 bg-blue-100 p-3 rounded-md overflow-x-auto whitespace-pre-wrap max-h-96">{{ session('command_output') }}</pre>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Grille des processus -->
<div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

    <!-- 1. Calcul des Avancements -->
    <div class="bg-white overflow-hidden shadow-lg rounded-lg">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h3 class="text-lg font-medium text-white flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Calcul des Avancements
            </h3>
            <p class="text-blue-100 text-sm mt-1">F-CALC-01</p>
        </div>
        <div class="px-6 py-4">
            <p class="text-sm text-gray-600 mb-4">
                Calcule et applique les promotions de grade pour tous les distributeurs selon leurs performances pour une période donnée.
                <strong>Par défaut, seuls les distributeurs ayant effectué des achats dans la période sont traités.</strong>
            </p>

            <form method="POST" action="{{ route('admin.processes.advancements') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Période (YYYY-MM) <span class="text-red-500">*</span>
                    </label>
                    <select name="period" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="{{ $currentPeriod }}">{{ $currentPeriod }} (Actuelle)</option>
                        @foreach($availablePeriods as $period)
                            @if($period !== $currentPeriod)
                                <option value="{{ $period }}">{{ $period }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="dry_run" value="1" id="dry_run_advancements"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="dry_run_advancements" class="ml-2 block text-sm text-gray-700">
                        <span class="font-medium">Mode simulation</span> - Aucune modification ne sera apportée
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="validated_only" value="1" id="validated_only_advancements"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                    <label for="validated_only_advancements" class="ml-2 block text-sm text-gray-700">
                        <span class="font-medium">Achats validés uniquement</span> - Calculer seulement les distributeurs ayant effectué des achats dans la période
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 flex items-center justify-center"
                        onclick="return confirm('Confirmer le calcul des avancements pour cette période ?')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    Lancer le Calcul
                </button>
            </form>
        </div>
    </div>

    <!-- 2. Régularisation des Grades -->
    <div class="bg-white overflow-hidden shadow-lg rounded-lg">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4">
            <h3 class="text-lg font-medium text-white flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Régularisation des Grades
            </h3>
            <p class="text-amber-100 text-sm mt-1">F-CALC-02</p>
        </div>
        <div class="px-6 py-4">
            <p class="text-sm text-gray-600 mb-4">
                Audite et synchronise tous les grades en recalculant depuis zéro pour corriger les incohérences. Présente les changements avant application.
            </p>

            <form method="POST" action="{{ route('admin.processes.regularization') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Période (YYYY-MM) <span class="text-red-500">*</span>
                    </label>
                    <select name="period" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="{{ $currentPeriod }}">{{ $currentPeriod }} (Actuelle)</option>
                        @foreach($availablePeriods as $period)
                            @if($period !== $currentPeriod)
                                <option value="{{ $period }}">{{ $period }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="dry_run" value="1" id="dry_run_regularization"
                           class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <label for="dry_run_regularization" class="ml-2 block text-sm text-gray-700">
                        <span class="font-medium">Mode simulation</span> - Aucune modification, analyse uniquement
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 flex items-center justify-center"
                        onclick="return confirm('Confirmer la régularisation des grades pour cette période ?')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Lancer l'Audit
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Statistiques du système -->
<div class="mt-12 bg-white shadow-lg rounded-lg overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
        <h3 class="text-lg font-medium text-white flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Statistiques Système
        </h3>
    </div>
    <div class="px-6 py-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_distributeurs']) }}</div>
                <div class="text-sm text-gray-500">Total Distributeurs</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ number_format($stats['distributeurs_actifs_mois']) }}</div>
                <div class="text-sm text-gray-500">Actifs ce mois</div>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_achats_mois']) }}</div>
                <div class="text-sm text-gray-500">Achats ce mois</div>
            </div>
            <div class="text-center p-4 bg-orange-50 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">{{ number_format($stats['total_bonus_mois']) }}</div>
                <div class="text-sm text-gray-500">Bonus ce mois</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ \App\Models\AvancementHistory::where('period', $currentPeriod)->count() }}</div>
                <div class="text-sm text-gray-500">Avancements ce mois</div>
            </div>
        </div>

        @if($stats['dernier_traitement'])
            <div class="mt-4 text-center text-sm text-gray-500">
                <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Dernier traitement : {{ \Carbon\Carbon::parse($stats['dernier_traitement'])->format('d/m/Y à H:i') }}
                </span>
            </div>
        @endif
    </div>
</div>

<!-- Aide -->
<div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-md p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Important</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <ul class="list-disc pl-5 space-y-1">
                    <li>Utilisez toujours le <strong>mode simulation/audit</strong> en premier pour vérifier les résultats</li>
                    <li>Les processus peuvent prendre du temps selon le nombre de distributeurs</li>
                    <li>Assurez-vous qu'aucun autre processus n'est en cours avant de lancer un traitement</li>
                    <li>Les résultats sont affichés et loggés pour traçabilité</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
