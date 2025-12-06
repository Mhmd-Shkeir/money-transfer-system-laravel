<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        // Deterministic fixed rates using USD as base
        $currencyIds = DB::table('currencies')->pluck('id', 'code')->all();

        if (empty($currencyIds) || !isset($currencyIds['USD'])) {
            $this->command->error('Currencies not seeded; run CurrencySeeder first.');
            return;
        }

        $rates = [
            // from => [to => rate]
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
                // insert both directions for convenience
                DB::table('exchange_rates')->updateOrInsert(
                    ['from_currency_id' => $fromId, 'to_currency_id' => $toId],
                    ['rate' => $rate, 'updated_at' => $now]
                );

                // store reciprocal
                $recip = $rate > 0 ? round(1.0 / $rate, 6) : null;
                if ($recip) {
                    DB::table('exchange_rates')->updateOrInsert(
                        ['from_currency_id' => $toId, 'to_currency_id' => $fromId],
                        ['rate' => $recip, 'updated_at' => $now]
                    );
                }
            }
        }
    }
}
