<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\PerformanceMonitoringService;
use App\Models\SystemPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected PerformanceMonitoringService $monitoringService;

    public function __construct(
        DashboardService $dashboardService,
        PerformanceMonitoringService $monitoringService
    ) {
        $this->dashboardService = $dashboardService;
        $this->monitoringService = $monitoringService;
    }

    /**
     * Dashboard principal
     */
    public function index(Request $request)
    {
        $period = $request->get('period', SystemPeriod::getCurrentPeriod()?->period);

        if (!$period) {
            return redirect()->route('admin.periods.index')
                           ->with('error', 'Aucune période active. Veuillez configurer une période.');
        }

        $dashboardData = $this->dashboardService->getDashboardData($period);
        $availablePeriods = SystemPeriod::orderBy('period', 'desc')->pluck('period');

        return view('admin.dashboard.index', compact('dashboardData', 'availablePeriods', 'period'));
    }

    /**
     * Dashboard de performance
     */
    public function performance(Request $request)
    {
        $period = $request->get('period', SystemPeriod::getCurrentPeriod()?->period);

        $metrics = $this->monitoringService->collectMetrics($period);
        $history = $this->monitoringService->getMetricsHistory(24);

        return view('admin.dashboard.performance', compact('metrics', 'history', 'period'));
    }

    /**
     * API pour les données temps réel
     */
    public function realtime(Request $request)
    {
        $period = $request->get('period', SystemPeriod::getCurrentPeriod()?->period);

        // Données légères pour mise à jour temps réel
        $realtimeData = [
            'timestamp' => now()->toISOString(),
            'active_users' => $this->getActiveUsersCount(),
            'recent_sales' => $this->getRecentSalesCount($period, 5), // Dernières 5 minutes
            'system_health' => $this->getSystemHealth()
        ];

        return response()->json($realtimeData);
    }

    /**
     * Export du dashboard en PDF
     */
    public function export(Request $request)
    {
        $period = $request->get('period', SystemPeriod::getCurrentPeriod()?->period);
        $dashboardData = $this->dashboardService->getDashboardData($period);

        // Utiliser DomPDF
        $pdf = Pdf::loadView('admin.dashboard.export', compact('dashboardData', 'period'));

        return $pdf->download("dashboard_{$period}.pdf");
    }

    protected function getActiveUsersCount(): int
    {
        // Compter les utilisateurs actifs dans les 15 dernières minutes
        return \App\Models\User::where('last_activity', '>=', now()->subMinutes(15))->count();
    }

    protected function getRecentSalesCount(string $period, int $minutes): int
    {
        return \App\Models\Achat::where('period', $period)
                               ->where('created_at', '>=', now()->subMinutes($minutes))
                               ->count();
    }

    protected function getSystemHealth(): array
    {
        $health = ['status' => 'healthy', 'checks' => []];

        // Vérifier la base de données
        try {
            \DB::connection()->getPdo();
            $health['checks']['database'] = 'ok';
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['checks']['database'] = 'error';
        }

        // Vérifier Redis
        try {
            Redis::ping();  // Utilisation correcte de la façade Redis
            $health['checks']['redis'] = 'ok';
        } catch (\Exception $e) {
            $health['status'] = 'degraded';
            $health['checks']['redis'] = 'error';
        }

        // Vérifier l'espace disque
        $freeSpace = disk_free_space('/');
        $totalSpace = disk_total_space('/');
        $usagePercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        if ($usagePercent > 90) {
            $health['status'] = 'degraded';
            $health['checks']['disk'] = 'warning';
        } else {
            $health['checks']['disk'] = 'ok';
        }

        return $health;
    }
}
