<?php

namespace App\Services;

use App\Models\ExchangeRate;
use App\Models\Currency;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ExchangeRateService
{
    // TTL in seconds for cached DB rates
    protected $ttl = 3600; // 1 hour

    // Public API base (no API key required)
    protected $apiBase = 'https://api.exchangerate.host/convert';

    /**
     * Get conversion rate from currency id to currency id.
     * Returns float rate or null if not available.
     */
    public function getRateByIds(?int $fromCurrencyId, ?int $toCurrencyId): ?float
    {
        if (!$fromCurrencyId || !$toCurrencyId) return null;

        // Try DB cache first
        $cached = ExchangeRate::where('from_currency_id', $fromCurrencyId)
            ->where('to_currency_id', $toCurrencyId)
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($cached) {
            $age = Carbon::now()->diffInSeconds(Carbon::parse($cached->updated_at));
            if ($age <= $this->ttl) {
                return (float) $cached->rate;
            }
        }

        // Need currency codes to call external API
        $from = Currency::find($fromCurrencyId);
        $to = Currency::find($toCurrencyId);
        if (!$from || !$to || !$from->code || !$to->code) {
            return $cached ? (float)$cached->rate : null;
        }

        // Query external API
        try {
            $resp = Http::get($this->apiBase, ['from' => $from->code, 'to' => $to->code, 'amount' => 1]);
            if ($resp->ok()) {
                $json = $resp->json();
                if (isset($json['result'])) {
                    $rate = (float) $json['result'];
                    // Save to DB (upsert)
                    ExchangeRate::updateOrCreate(
                        ['from_currency_id' => $fromCurrencyId, 'to_currency_id' => $toCurrencyId],
                        ['rate' => $rate, 'updated_at' => Carbon::now()]
                    );
                    return $rate;
                }
            }
        } catch (\Exception $e) {
            // swallow network errors; fall back to cached if available
        }

        return $cached ? (float)$cached->rate : null;
    }
}
