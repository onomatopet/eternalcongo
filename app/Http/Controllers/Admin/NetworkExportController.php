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
     * Récupère les données du réseau de manière optimisée
     */
    private function getNetworkData($distributeurId, $period)
    {
        // Initialiser avec le distributeur principal
        $network = [];
        $processedIds = [];
        $queue = [['id' => $distributeurId, 'level' => 0]];
        $limit = 5000; // Limite de sécurité

        while (!empty($queue) && count($network) < $limit) {
            $current = array_shift($queue);
            $currentId = $current['id'];
            $currentLevel = $current['level'];

            // Éviter les doublons
            if (in_array($currentId, $processedIds)) {
                continue;
            }
            $processedIds[] = $currentId;

            // Récupérer les données du distributeur avec jointures optimisées
            $data = DB::table('distributeurs as d')
                ->leftJoin('level_currents as lc', function($join) use ($period) {
                    $join->on('d.distributeur_id', '=', 'lc.distributeur_id')
                         ->where('lc.period', '=', $period);
                })
                ->leftJoin('distributeurs as parent', 'd.id_distrib_parent', '=', 'parent.distributeur_id')
                ->where('d.distributeur_id', $currentId)
                ->select([
                    'd.distributeur_id',
                    'd.nom_distributeur',
                    'd.pnom_distributeur',
                    'd.id_distrib_parent',
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
                // Ajouter au réseau
                $network[] = [
                    'rang' => $currentLevel,
                    'distributeur_id' => $data->distributeur_id,
                    'nom_distributeur' => $data->nom_distributeur ?? 'N/A',
                    'pnom_distributeur' => $data->pnom_distributeur ?? 'N/A',
                    'etoiles' => $data->etoiles ?? 0,
                    'new_cumul' => $data->new_cumul ?? 0,
                    'cumul_total' => $data->cumul_total ?? 0,
                    'cumul_collectif' => $data->cumul_collectif ?? 0,
                    'cumul_individuel' => $data->cumul_individuel ?? 0,
                    'id_distrib_parent' => $data->id_distrib_parent ?? '',
                    'nom_parent' => $data->nom_parent ?? 'N/A',
                    'pnom_parent' => $data->pnom_parent ?? 'N/A',
                ];

                // Ajouter les enfants à la queue
                $children = DB::table('distributeurs')
                    ->where('id_distrib_parent', $currentId)
                    ->pluck('distributeur_id')
                    ->toArray();

                foreach ($children as $childId) {
                    $queue[] = ['id' => $childId, 'level' => $currentLevel + 1];
                }
            }
        }

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
}
