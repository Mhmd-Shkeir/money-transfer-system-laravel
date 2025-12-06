<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\ImportFixedRates::class,
    ];

    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
    {
        // no scheduled tasks for now
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
