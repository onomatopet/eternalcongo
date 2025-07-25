<?php

// app/Console/Kernel.php (ajouter dans la méthode schedule)

protected function schedule(Schedule $schedule)
{
    // Collecter les métriques toutes les 5 minutes
    $schedule->call(function () {
        $period = SystemPeriod::getCurrentPeriod()?->period;
        if ($period) {
            app(PerformanceMonitoringService::class)->collectMetrics($period);
        }
    })->everyFiveMinutes()->name('collect-metrics')->withoutOverlapping();

    // Préchauffer le cache toutes les heures
    $schedule->command('mlm:warm-cache')
             ->hourly()
             ->name('warm-cache')
             ->withoutOverlapping();

    // Agrégation batch quotidienne (à 2h du matin)
    $schedule->command('mlm:aggregate-batch')
             ->dailyAt('02:00')
             ->name('daily-aggregation')
             ->withoutOverlapping();

    // Nettoyage des métriques anciennes (hebdomadaire)
    $schedule->call(function () {
        // Garder seulement 30 jours d'historique
        $cutoff = now()->subDays(30);
        DB::table('performance_metrics')
          ->where('created_at', '<', $cutoff)
          ->delete();
    })->weekly()->name('cleanup-metrics');
}
