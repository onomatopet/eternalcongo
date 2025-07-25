<?php
// app/Services/DashboardService.php

namespace App\Services;

use App\Models\LevelCurrent;
use App\Models\Achat;
use App\Models\Distributeur;
use App\Models\Bonus;
use App\Models\SystemPeriod;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    protected CacheService $cache;
    protected PerformanceMonitoringService $monitoring;

    public function __construct(
        CacheService $cache,
        PerformanceMonitoringService $monitoring
    ) {
        $this->cache = $cache;
        $this->monitoring = $monitoring;
    }

    /**
     * Récupère toutes les données du dashboard principal
     */
    public function getDashboardData(string $period = null): array
    {
        $period = $period ?? SystemPeriod::getCurrentPeriod()?->period ?? date('Y-m');

        return $this->cache->remember(
            CacheService::PREFIX_DASHBOARD . "main:{$period}",
            CacheService::TTL_SHORT,
            function() use ($period) {
                return [
                    'period' => $period,
                    'kpis' => $this->getKPIs($period),
                    'charts' => $this->getChartsData($period),
                    'recent_activity' => $this->getRecentActivity($period),
                    'alerts' => $this->getAlerts($period),
                    'comparisons' => $this->getPeriodComparisons($period)
                ];
            },
            ['dashboard', "period:{$period}"]
        );
    }

    /**
     * KPIs principaux
     */
    public function getKPIs(string $period): array
    {
        $currentStats = $this->getGlobalStats($period);
        $previousPeriod = Carbon::createFromFormat('Y-m', $period)->subMonth()->format('Y-m');
        $previousStats = $this->getGlobalStats($previousPeriod);

        return [
            'total_revenue' => [
                'value' => $currentStats['total_revenue'],
                'change' => $this->calculatePercentageChange(
                    $previousStats['total_revenue'],
                    $currentStats['total_revenue']
                ),
                'formatted' => number_format($currentStats['total_revenue'], 2) . ' €'
            ],
            'active_distributors' => [
                'value' => $currentStats['active_distributors'],
                'change' => $this->calculatePercentageChange(
                    $previousStats['active_distributors'],
                    $currentStats['active_distributors']
                ),
                'formatted' => number_format($currentStats['active_distributors'])
            ],
            'average_basket' => [
                'value' => $currentStats['average_basket'],
                'change' => $this->calculatePercentageChange(
                    $previousStats['average_basket'],
                    $currentStats['average_basket']
                ),
                'formatted' => number_format($currentStats['average_basket'], 2) . ' €'
            ],
            'total_points' => [
                'value' => $currentStats['total_points'],
                'change' => $this->calculatePercentageChange(
                    $previousStats['total_points'],
                    $currentStats['total_points']
                ),
                'formatted' => number_format($currentStats['total_points'])
            ]
        ];
    }

    /**
     * Statistiques globales
     */
    public function getGlobalStats(string $period): array
    {
        return $this->cache->remember(
            CacheService::PREFIX_STATS . "global:{$period}",
            CacheService::TTL_MEDIUM,
            function() use ($period) {
                $achats = Achat::where('period', $period);

                return [
                    'total_revenue' => $achats->sum('montant_total_ligne'),
                    'total_points' => $achats->sum('pointvaleur'),
                    'total_orders' => $achats->count(),
                    'active_distributors' => $achats->distinct('distributeur_id')->count(),
                    'average_basket' => $achats->avg('montant_total_ligne') ?? 0,
                    'new_distributors' => Distributeur::whereYear('created_at', substr($period, 0, 4))
                                                    ->whereMonth('created_at', substr($period, 5, 2))
                                                    ->count()
                ];
            },
            ['stats', "period:{$period}"]
        );
    }

    /**
     * Données pour les graphiques
     */
    protected function getChartsData(string $period): array
    {
        return [
            'sales_evolution' => $this->getSalesEvolution($period),
            'grade_distribution' => $this->getGradeDistribution($period),
            'top_products' => $this->getTopProducts($period),
            'geographic_distribution' => $this->getGeographicDistribution($period),
            'hourly_activity' => $this->monitoring->getBusinessMetrics($period)['purchase_velocity'] ?? []
        ];
    }

    /**
     * Evolution des ventes sur 12 mois
     */
    protected function getSalesEvolution(string $period): array
    {
        $data = [];
        $current = Carbon::createFromFormat('Y-m', $period);

        for ($i = 11; $i >= 0; $i--) {
            $monthPeriod = $current->copy()->subMonths($i)->format('Y-m');

            $stats = Achat::where('period', $monthPeriod)
                        ->selectRaw('COUNT(*) as orders, SUM(montant_total_ligne) as revenue, SUM(pointvaleur) as points')
                        ->first();

            $data[] = [
                'period' => $monthPeriod,
                'orders' => $stats->orders ?? 0,
                'revenue' => $stats->revenue ?? 0,
                'points' => $stats->points ?? 0
            ];
        }

        return $data;
    }

    /**
     * Distribution des grades
     */
    protected function getGradeDistribution(string $period): array
    {
        return LevelCurrent::where('period', $period)
                         ->join('distributeurs', 'level_currents.distributeur_id', '=', 'distributeurs.id')
                         ->groupBy('level_currents.etoiles')
                         ->selectRaw('level_currents.etoiles as grade, COUNT(*) as count, SUM(level_currents.new_cumul) as total_points')
                         ->orderBy('grade')
                         ->get()
                         ->map(function($item) {
                             return [
                                 'grade' => $item->grade,
                                 'label' => "Grade {$item->grade} ⭐",
                                 'count' => $item->count,
                                 'total_points' => $item->total_points
                             ];
                         })
                         ->toArray();
    }

    /**
     * Top produits
     */
    protected function getTopProducts(string $period): array
    {
        return Achat::where('period', $period)
                   ->join('products', 'achats.products_id', '=', 'products.id')
                   ->groupBy('products.id', 'products.title')
                   ->selectRaw('products.id, products.title, COUNT(*) as count, SUM(achats.qt) as quantity, SUM(achats.montant_total_ligne) as revenue')
                   ->orderByDesc('revenue')
                   ->limit(10)
                   ->get()
                   ->toArray();
    }

    /**
     * Distribution géographique
     */
    protected function getGeographicDistribution(string $period): array
    {
        // Adapter selon votre structure de données géographiques
        return [];
    }

    /**
     * Activité récente
     */
    protected function getRecentActivity(string $period): array
    {
        return [
            'recent_orders' => $this->getRecentOrders($period, 10),
            'recent_advancements' => $this->getRecentAdvancements($period, 10),
            'recent_registrations' => $this->getRecentRegistrations(10)
        ];
    }

    protected function getRecentOrders(string $period, int $limit): array
    {
        return Achat::where('period', $period)
                   ->with(['distributeur', 'product'])
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get()
                   ->map(function($achat) {
                       return [
                           'id' => $achat->id,
                           'distributeur' => $achat->distributeur->nom_distributeur . ' ' . $achat->distributeur->pnom_distributeur,
                           'product' => $achat->product->title ?? 'N/A',
                           'amount' => $achat->montant_total_ligne,
                           'points' => $achat->pointvaleur,
                           'created_at' => $achat->created_at->format('d/m/Y H:i')
                       ];
                   })
                   ->toArray();
    }

    protected function getRecentAdvancements(string $period, int $limit): array
    {
        return DB::table('avancement_histories')
                ->where('period', $period)
                ->join('distributeurs', 'avancement_histories.distributeur_id', '=', 'distributeurs.id')
                ->orderBy('avancement_histories.created_at', 'desc')
                ->limit($limit)
                ->select([
                    'distributeurs.distributeur_id',
                    'distributeurs.nom_distributeur',
                    'distributeurs.pnom_distributeur',
                    'avancement_histories.ancien_grade',
                    'avancement_histories.nouveau_grade',
                    'avancement_histories.created_at'
                ])
                ->get()
                ->map(function($item) {
                    return [
                        'distributeur' => $item->nom_distributeur . ' ' . $item->pnom_distributeur,
                        'matricule' => $item->distributeur_id,
                        'from_grade' => $item->ancien_grade,
                        'to_grade' => $item->nouveau_grade,
                        'date' => Carbon::parse($item->created_at)->format('d/m/Y')
                    ];
                })
                ->toArray();
    }

    protected function getRecentRegistrations(int $limit): array
    {
        return Distributeur::with('parent')
                         ->orderBy('created_at', 'desc')
                         ->limit($limit)
                         ->get()
                         ->map(function($dist) {
                             return [
                                 'id' => $dist->id,
                                 'matricule' => $dist->distributeur_id,
                                 'name' => $dist->nom_distributeur . ' ' . $dist->pnom_distributeur,
                                 'sponsor' => $dist->parent ? $dist->parent->nom_distributeur . ' ' . $dist->parent->pnom_distributeur : 'N/A',
                                 'date' => $dist->created_at->format('d/m/Y')
                             ];
                         })
                         ->toArray();
    }

    /**
     * Alertes système
     */
    protected function getAlerts(string $period): array
    {
        $alerts = [];

        // Alerte : Baisse d'activité
        $currentActive = $this->getGlobalStats($period)['active_distributors'];
        $previousPeriod = Carbon::createFromFormat('Y-m', $period)->subMonth()->format('Y-m');
        $previousActive = $this->getGlobalStats($previousPeriod)['active_distributors'];

        if ($previousActive > 0 && ($currentActive / $previousActive) < 0.8) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Baisse d\'activité',
                'message' => 'Le nombre de distributeurs actifs a baissé de plus de 20%'
            ];
        }

        // Alerte : Bonus non validés
        $pendingBonuses = Bonus::where('period', $period)
                             ->where('status', 'calculé')
                             ->count();
        if ($pendingBonuses > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Bonus en attente',
                'message' => "{$pendingBonuses} bonus sont en attente de validation"
            ];
        }

        // Alerte : Performance système
        $metrics = $this->monitoring->collectMetrics($period);
        if (($metrics['system']['memory']['current'] ?? 0) > 1024) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Utilisation mémoire élevée',
                'message' => 'La consommation mémoire dépasse 1GB'
            ];
        }

        return $alerts;
    }

    /**
     * Comparaisons entre périodes
     */
    protected function getPeriodComparisons(string $period): array
    {
        $periods = [];
        $current = Carbon::createFromFormat('Y-m', $period);

        // Comparer avec les 3 derniers mois
        for ($i = 0; $i <= 3; $i++) {
            $comparePeriod = $current->copy()->subMonths($i)->format('Y-m');
            $stats = $this->getGlobalStats($comparePeriod);

            $periods[] = [
                'period' => $comparePeriod,
                'is_current' => $i === 0,
                'stats' => $stats
            ];
        }

        return $periods;
    }

    /**
     * Top performers
     */
    public function getTopPerformers(string $period, int $limit = 10): array
    {
        return $this->cache->remember(
            CacheService::PREFIX_STATS . "top_performers:{$period}:{$limit}",
            CacheService::TTL_MEDIUM,
            function() use ($period, $limit) {
                return LevelCurrent::where('period', $period)
                                 ->with('distributeur')
                                 ->orderBy('new_cumul', 'desc')
                                 ->limit($limit)
                                 ->get()
                                 ->map(function($level) {
                                     return [
                                         'rank' => 0, // Will be set after
                                         'matricule' => $level->distributeur->distributeur_id,
                                         'name' => $level->distributeur->nom_distributeur . ' ' . $level->distributeur->pnom_distributeur,
                                         'grade' => $level->etoiles,
                                         'points' => $level->new_cumul,
                                         'team_points' => $level->cumul_collectif
                                     ];
                                 })
                                 ->values()
                                 ->map(function($item, $index) {
                                     $item['rank'] = $index + 1;
                                     return $item;
                                 })
                                 ->toArray();
            },
            ['stats', "period:{$period}"]
        );
    }

    /**
     * Calcule le pourcentage de changement
     */
    protected function calculatePercentageChange($oldValue, $newValue): float
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
    }
}
