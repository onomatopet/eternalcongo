<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributeur;
use App\Models\LevelCurrent;
use App\Models\Achat;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NetworkExport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NetworkExportController extends Controller
{
    /**
     * Affiche le formulaire de sélection
     */
    public function index()
    {
        $distributeurs = Distributeur::orderBy('nom_distributeur', 'ASC')
            ->get(['id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur']);

        $periods = LevelCurrent::select('period')
            ->distinct()
            ->orderBy('period', 'DESC')
            ->pluck('period');

        return view('admin.network.index', compact('distributeurs', 'periods'));
    }

    /**
     * Affiche l'aperçu du réseau avant export
     */
    public function export(Request $request)
    {
        $request->validate([
            'distributeur_id' => 'required|exists:distributeurs,distributeur_id',
            'period' => 'required|string|size:7'
        ]);

        $distributeurId = $request->distributeur_id;
        $period = $request->period;

        // Récupérer les données du réseau
        $networkData = $this->getNetworkData($distributeurId, $period);

        if (empty($networkData)) {
            return back()->with('error', 'Aucune donnée trouvée pour ce distributeur et cette période.');
        }

        // Informations du distributeur principal
        $mainDistributor = Distributeur::where('distributeur_id', $distributeurId)->first();

        return view('admin.network.show', [
            'distributeurs' => $networkData,
            'mainDistributor' => $mainDistributor,
            'period' => $period,
            'totalCount' => count($networkData)
        ]);
    }

    /**
     * Affiche l'aperçu du réseau avant impression
     */
    public function exportHtml(Request $request)
    {
        $request->validate([
            'distributeur_id' => 'required|exists:distributeurs,distributeur_id',
            'period' => 'required|string|size:7'
        ]);

        $distributeurId = $request->distributeur_id;
        $period = $request->period;

        // Récupérer les données du réseau
        $networkData = $this->getNetworkData($distributeurId, $period);

        if (empty($networkData)) {
            return back()->with('error', 'Aucune donnée trouvée pour ce distributeur et cette période.');
        }

        // Informations du distributeur principal
        $mainDistributor = Distributeur::where('distributeur_id', $distributeurId)->first();

        return view('admin.network.imprimable', [
            'distributeurs' => $networkData,
            'mainDistributor' => $mainDistributor,
            'period' => $period,
            'totalCount' => count($networkData)
        ]);
    }

    /**
     * Export en PDF
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'distributeur_id' => 'required|exists:distributeurs,distributeur_id',
            'period' => 'required|string|size:7'
        ]);

        $networkData = $this->getNetworkData($request->distributeur_id, $request->period);
        $mainDistributor = Distributeur::where('distributeur_id', $request->distributeur_id)->first();

        $pdf = PDF::loadView('admin.network.pdf', [
            'distributeurs' => $networkData,
            'mainDistributor' => $mainDistributor,
            'period' => $request->period,
            'totalCount' => count($networkData),
            'printDate' => Carbon::now()->format('d/m/Y H:i')
        ]);

        $filename = "reseau_{$request->distributeur_id}_{$request->period}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Export en Excel
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'distributeur_id' => 'required|exists:distributeurs,distributeur_id',
            'period' => 'required|string|size:7'
        ]);

        $filename = "reseau_{$request->distributeur_id}_{$request->period}.xlsx";

        return Excel::download(
            new NetworkExport($request->distributeur_id, $request->period),
            $filename
        );
    }

    /**
     * Récupère les données du réseau de manière optimisée - VERSION CORRIGÉE
     */
    private function getNetworkData($distributeurMatricule, $period)
    {
        \Log::info("=== Début getNetworkData ===");
        \Log::info("Distributeur Matricule: {$distributeurMatricule}, Période: {$period}");

        // D'abord, obtenir l'ID primaire du distributeur principal
        $distributeurPrincipal = DB::table('distributeurs')
            ->where('distributeur_id', $distributeurMatricule)
            ->first();

        if (!$distributeurPrincipal) {
            \Log::error("Distributeur avec matricule {$distributeurMatricule} non trouvé");
            return [];
        }

        \Log::info("Distributeur principal trouvé - ID: {$distributeurPrincipal->id}, Nom: {$distributeurPrincipal->nom_distributeur}");

        // Initialiser
        $network = [];
        $processedIds = []; // IDs primaires traités
        $queue = [['id' => $distributeurPrincipal->id, 'level' => 0]];
        $limit = 5000;

        while (!empty($queue) && count($network) < $limit) {
            $current = array_shift($queue);
            $currentId = $current['id']; // ID primaire
            $currentLevel = $current['level'];

            \Log::info("Traitement ID: {$currentId}, Niveau: {$currentLevel}");

            // Éviter les doublons
            if (in_array($currentId, $processedIds)) {
                \Log::info("ID {$currentId} déjà traité");
                continue;
            }
            $processedIds[] = $currentId;

            // Récupérer les données avec les bonnes jointures
            $data = DB::table('distributeurs as d')
                ->leftJoin('level_currents as lc', function($join) use ($period) {
                    $join->on('d.id', '=', 'lc.distributeur_id') // level_currents.distributeur_id = distributeurs.id
                        ->where('lc.period', '=', $period);
                })
                ->leftJoin('distributeurs as parent', 'd.id_distrib_parent', '=', 'parent.id') // id_distrib_parent contient l'ID du parent
                ->where('d.id', $currentId)
                ->select([
                    'd.id',
                    'd.distributeur_id', // Le matricule
                    'd.nom_distributeur',
                    'd.pnom_distributeur',
                    'd.id_distrib_parent', // ID du parent
                    'parent.distributeur_id as parent_matricule', // Matricule du parent pour l'affichage
                    'parent.nom_distributeur as nom_parent',
                    'parent.pnom_distributeur as pnom_parent',
                    'lc.etoiles',
                    'lc.new_cumul',
                    'lc.cumul_total',
                    'lc.cumul_collectif',
                    'lc.cumul_individuel'
                ])
                ->first();

            if ($data) {
                \Log::info("Données trouvées: {$data->nom_distributeur} {$data->pnom_distributeur}, Matricule: {$data->distributeur_id}");

                // Ajouter au réseau
                $network[] = [
                    'rang' => $currentLevel,
                    'distributeur_id' => $data->distributeur_id, // Matricule pour l'affichage
                    'nom_distributeur' => $data->nom_distributeur ?? 'N/A',
                    'pnom_distributeur' => $data->pnom_distributeur ?? 'N/A',
                    'etoiles' => $data->etoiles ?? 0,
                    'new_cumul' => $data->new_cumul ?? 0,
                    'cumul_total' => $data->cumul_total ?? 0,
                    'cumul_collectif' => $data->cumul_collectif ?? 0,
                    'cumul_individuel' => $data->cumul_individuel ?? 0,
                    'id_distrib_parent' => $data->parent_matricule ?? '', // Matricule du parent pour l'affichage
                    'nom_parent' => $data->nom_parent ?? 'N/A',
                    'pnom_parent' => $data->pnom_parent ?? 'N/A',
                ];

                // CORRECTION ICI : Chercher les enfants avec l'ID primaire, pas le matricule
                $children = DB::table('distributeurs')
                    ->where('id_distrib_parent', $currentId) // id_distrib_parent = ID du parent actuel
                    ->get(['id', 'distributeur_id', 'nom_distributeur']);

                \Log::info("Recherche enfants où id_distrib_parent = {$currentId}: " . $children->count() . " trouvés");

                foreach ($children as $child) {
                    \Log::info("- Enfant trouvé: ID={$child->id}, Matricule={$child->distributeur_id}, Nom={$child->nom_distributeur}");
                    $queue[] = [
                        'id' => $child->id,
                        'level' => $currentLevel + 1
                    ];
                }
            } else {
                \Log::warning("Aucune donnée pour ID {$currentId}");
            }
        }

        \Log::info("=== Fin getNetworkData ===");
        \Log::info("Total: " . count($network) . " distributeurs");
        \Log::info("IDs traités: " . implode(', ', $processedIds));

        return $network;
    }

    /**
     * Recherche AJAX de distributeurs
     */
    public function searchDistributeurs(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $distributeurs = Distributeur::where(function($q) use ($query) {
                $q->where('distributeur_id', 'LIKE', "%{$query}%")
                ->orWhere('nom_distributeur', 'LIKE', "%{$query}%")
                ->orWhere('pnom_distributeur', 'LIKE', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(nom_distributeur, ' ', pnom_distributeur)"), 'LIKE', "%{$query}%")
                ->orWhere(DB::raw("CONCAT(pnom_distributeur, ' ', nom_distributeur)"), 'LIKE', "%{$query}%");
            })
            ->orderBy('nom_distributeur')
            ->limit(30)
            ->get(['id', 'distributeur_id', 'nom_distributeur', 'pnom_distributeur', 'etoiles_id', 'id_distrib_parent']);

        // Formater les résultats pour l'affichage
        $results = $distributeurs->map(function($dist) {
            return [
                'id' => $dist->id,
                'distributeur_id' => $dist->distributeur_id,
                'nom_distributeur' => $dist->nom_distributeur,
                'pnom_distributeur' => $dist->pnom_distributeur,
                'etoiles_id' => $dist->etoiles_id ?? 0,
                'id_distrib_parent' => $dist->id_distrib_parent,
                'display_name' => $dist->distributeur_id . ' - ' . $dist->nom_distributeur . ' ' . $dist->pnom_distributeur,
                'grade_display' => str_repeat('★', $dist->etoiles_id ?? 0)
            ];
        });

        return response()->json($results);
    }

    /**
     * Recherche AJAX des périodes disponibles dans la table achats
     */
    public function searchPeriods(Request $request)
    {
        $query = $request->get('q', '');

        // Si la recherche est vide, retourner les 12 dernières périodes
        if (empty($query)) {
            $periods = Achat::select('period')
                ->whereNotNull('period')
                ->where('period', '!=', '')
                ->groupBy('period')
                ->orderBy('period', 'desc')
                ->limit(12)
                ->pluck('period');
        } else {
            // Recherche avec le terme saisi
            $periods = Achat::select('period')
                ->whereNotNull('period')
                ->where('period', '!=', '')
                ->where('period', 'LIKE', "%{$query}%")
                ->groupBy('period')
                ->orderBy('period', 'desc')
                ->limit(20)
                ->pluck('period');
        }

        // Formater les périodes pour l'affichage
        $formattedPeriods = $periods->map(function($period) {
            try {
                $date = Carbon::createFromFormat('Y-m', $period);
                return [
                    'value' => $period,
                    'label' => ucfirst($date->locale('fr')->isoFormat('MMMM YYYY')),
                    'year' => $date->year
                ];
            } catch (\Exception $e) {
                // Si le format n'est pas valide, ignorer cette période
                return null;
            }
        })->filter()->values();

        // Grouper par année
        $groupedPeriods = $formattedPeriods->groupBy('year')->sortKeysDesc();

        return response()->json($groupedPeriods);
    }
}
