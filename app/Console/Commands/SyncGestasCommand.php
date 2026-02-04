<?php

namespace App\Console\Commands;

use App\Services\GestasScraperService;
use Illuminate\Console\Command;

class SyncGestasCommand extends Command
{
    protected $signature = 'gestas:sync';

    protected $description = 'Kabatepe - Gökçeada sefer ve duyuru verilerini Gestaş sitesinden çeker.';

    public function handle(GestasScraperService $service): int
    {
        $this->info('Gestaş verileri senkronize ediliyor...');

        $service->sync();

        $this->info('Tamamlandı.');

        return self::SUCCESS;
    }
}
