<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributeur;
use App\Models\Achat;
use App\Models\ModificationRequest;
use App\Models\DeletionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_distributeurs' => Distributeur::count(),
            'active_distributeurs' => Distributeur::where('created_at', '>=', now()->subDays(30))->count(),
            'total_achats' => Achat::whereMonth('created_at', now()->month)->count(),
            // CORRECTION ICI : Utiliser points_unitaire_achat au lieu de point_achat
            'revenue_month' => Achat::whereMonth('created_at', now()->month)
                ->sum(DB::raw('points_unitaire_achat * qt')),
            'pending_modifications' => ModificationRequest::pending()->count(),
            'pending_deletions' => DeletionRequest::pending()->count(),
        ];

        // Graphiques et données pour le dashboard
        $monthlyRevenue = $this->getMonthlyRevenue();
        $topDistributeurs = $this->getTopDistributeurs();
        $recentActivities = $this->getRecentActivities();

        return view('admin.dashboard.index', compact('stats', 'monthlyRevenue', 'topDistributeurs', 'recentActivities'));
    }

    public function performance()
    {
        // Données de performance
        $performanceData = [
            'grade_distribution' => $this->getGradeDistribution(),
            'network_growth' => $this->getNetworkGrowth(),
            'bonus_statistics' => $this->getBonusStatistics(),
        ];

        return view('admin.dashboard.performance', compact('performanceData'));
    }

    private function getMonthlyRevenue()
    {
        // CORRECTION ICI : Utiliser points_unitaire_achat * qt au lieu de point_achat
        return Achat::selectRaw('MONTH(created_at) as month, SUM(points_unitaire_achat * qt) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getTopDistributeurs()
    {
        // CORRECTION ICI : Utiliser une sous-requête avec les bonnes colonnes
        return Distributeur::select('distributeurs.*')
            ->selectRaw('(SELECT SUM(points_unitaire_achat * qt) FROM achats WHERE achats.distributeur_id = distributeurs.id) as total_points')
            ->orderByDesc('total_points')
            ->limit(10)
            ->get();
    }

    private function getRecentActivities()
    {
        // Récupérer les activités récentes
        return collect();
    }

    private function getGradeDistribution()
    {
        return Distributeur::selectRaw('grade, COUNT(*) as count')
            ->groupBy('grade')
            ->orderBy('grade')
            ->get();
    }

    private function getNetworkGrowth()
    {
        return Distributeur::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getBonusStatistics()
    {
        // Statistiques des bonus
        return [];
    }

    // Méthodes API pour les routes AJAX
    public function apiStats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_distributeurs' => Distributeur::count(),
                'active_distributeurs' => Distributeur::where('created_at', '>=', now()->subDays(30))->count(),
                'total_achats' => Achat::whereMonth('created_at', now()->month)->count(),
                // CORRECTION ICI AUSSI
                'revenue_month' => Achat::whereMonth('created_at', now()->month)
                    ->sum(DB::raw('points_unitaire_achat * qt')),
            ]
        ]);
    }

    public function apiNotifications()
    {
        return response()->json([
            'success' => true,
            'notifications' => []
        ]);
    }
}
