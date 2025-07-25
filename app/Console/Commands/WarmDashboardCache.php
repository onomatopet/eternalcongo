<?php
// app/Console/Commands/WarmDashboardCache.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;
use App\Models\SystemPeriod;

class WarmDashboardCache extends Command
{
    protected $signature = 'mlm:warm-cache {period?}';
    protected $description = 'Précharge le cache pour les tableaux de bord';

    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    public function handle()
    {
        $period = $this->argument('period') ?? SystemPeriod::getCurrentPeriod()?->period;

        if (!$period) {
            $this->error('Aucune période spécifiée');
            return 1;
        }

        $this->info("Préchauffage du cache pour la période: {$period}");

        $this->cacheService->warmCache($period);

        $this->info("Cache préchauffé avec succès!");

        return 0;
    }
}
