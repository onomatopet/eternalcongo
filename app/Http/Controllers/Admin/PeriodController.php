<?php
// app/Http/Controllers/Admin/PeriodController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PeriodManagementService;
use App\Models\SystemPeriod;
use App\Models\BonusThreshold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class PeriodController extends Controller
{
    protected PeriodManagementService $periodService;

    public function __construct(PeriodManagementService $periodService)
    {
        $this->periodService = $periodService;
    }

    /**
     * Affiche la page de gestion des périodes
     */
    public function index()
    {
        $currentPeriod = SystemPeriod::getCurrentPeriod();
        $recentPeriods = SystemPeriod::orderBy('period', 'desc')->take(12)->get();
        $bonusThresholds = BonusThreshold::where('is_active', true)->orderBy('grade')->get();

        return view('admin.periods.index', compact('currentPeriod', 'recentPeriods', 'bonusThresholds'));
    }

    /**
     * Démarre la phase de validation
     */
    public function startValidation(Request $request)
    {
        $period = $request->input('period');
        $result = $this->periodService->startValidationPhase($period);

        if ($result['success']) {
            return redirect()->route('admin.periods.index')
                           ->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Clôture la période courante
     */
    public function closePeriod(Request $request)
    {
        $request->validate([
            'period' => 'required|string',
            'confirm' => 'required|accepted'
        ]);

        $result = $this->periodService->closePeriod(
            $request->input('period'),
            Auth::id()
        );

        if ($result['success']) {
            return redirect()->route('admin.periods.index')
                           ->with('success', $result['message'])
                           ->with('closure_summary', $result['summary']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Met à jour les seuils de bonus
     */
    public function updateThresholds(Request $request)
    {
        $request->validate([
            'thresholds' => 'required|array',
            'thresholds.*.grade' => 'required|integer|min:1|max:10',
            'thresholds.*.minimum_pv' => 'required|integer|min:0'
        ]);

        foreach ($request->input('thresholds') as $threshold) {
            BonusThreshold::where('grade', $threshold['grade'])
                         ->update(['minimum_pv' => $threshold['minimum_pv']]);
        }

        return redirect()->route('admin.periods.index')
                       ->with('success', 'Seuils de bonus mis à jour avec succès');
    }

    /**
     * Lance l'agrégation batch manuellement
     */
    public function runAggregation(Request $request)
    {
        $period = $request->input('period', SystemPeriod::getCurrentPeriod()?->period);

        if (!$period) {
            return redirect()->back()->with('error', 'Aucune période spécifiée');
        }

        // Exécuter la commande Artisan
        Artisan::call('mlm:aggregate-batch', [
            'period' => $period,
            '--batch-size' => 100
        ]);

        $output = Artisan::output();

        return redirect()->route('admin.periods.index')
                    ->with('success', 'Agrégation batch exécutée')
                    ->with('command_output', $output);
    }
}
