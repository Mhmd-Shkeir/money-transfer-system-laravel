<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportFixedRates extends Command
{
    protected $signature = 'rates:import-fixed';
    protected $description = 'Import fixed, deterministic exchange rates into the database';

    public function handle(): int
    {
        $this->info('Importing fixed currencies (idempotent)...');

        // Ensure currencies seeded
        $this->callSilent('db:seed', ['--class' => '\\Database\\Seeders\\CurrencySeeder']);

        $currencyIds = DB::table('currencies')->pluck('id', 'code')->all();
        if (empty($currencyIds) || !isset($currencyIds['USD'])) {
            $this->error('Currencies not available; aborting.');
            return 1;
        }

        $rates = [
            'USD' => [
                'EUR' => 0.92,
                'GBP' => 0.78,
                'GHS' => 12.00,
                'NGN' => 770.00,
            ],
        ];

        $now = Carbon::now();
        foreach ($rates as $from => $targets) {
            if (!isset($currencyIds[$from])) continue;
            $fromId = $currencyIds[$from];
            foreach ($targets as $to => $rate) {
                if (!isset($currencyIds[$to])) continue;
                $toId = $currencyIds[$to];
                DB::table('exchange_rates')->updateOrInsert(
                    ['from_currency_id' => $fromId, 'to_currency_id' => $toId],
                    ['rate' => $rate, 'updated_at' => $now]
                );

                $recip = $rate > 0 ? round(1.0 / $rate, 6) : null;
                if ($recip) {
                    DB::table('exchange_rates')->updateOrInsert(
                        ['from_currency_id' => $toId, 'to_currency_id' => $fromId],
                        ['rate' => $recip, 'updated_at' => $now]
                    );
                }
            }
        }

        $this->info('Fixed exchange rates imported.');
        return 0;
    }
}
