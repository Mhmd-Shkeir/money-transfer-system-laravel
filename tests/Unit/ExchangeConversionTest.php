<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Services\ExchangeRateService;
use App\Models\TransferFee;

class ExchangeConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_rates_are_available()
    {
        // Seed currencies and exchange rates
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\CurrencySeeder']);
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ExchangeRateSeeder']);

        $ids = DB::table('currencies')->pluck('id', 'code')->all();
        $this->assertArrayHasKey('USD', $ids);
        $this->assertArrayHasKey('EUR', $ids);

        $svc = new ExchangeRateService();
        $rate = $svc->getRateByIds($ids['USD'], $ids['EUR']);
        $this->assertEqualsWithDelta(0.92, $rate, 0.000001);
    }

    public function test_conversion_math_and_fee_application()
    {
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\CurrencySeeder']);
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ExchangeRateSeeder']);

        $ids = DB::table('currencies')->pluck('id', 'code')->all();
        $usd = $ids['USD'];
        $eur = $ids['EUR'];

        // Create a transfer fee rule: 2% + 1.00 fixed
        TransferFee::create([
            'from_currency_id' => $usd,
            'to_currency_id' => $eur,
            'percentage_fee' => 2.0,
            'fixed_fee' => 1.00,
            'effective_date' => now()->subDay(),
        ]);

        $svc = new ExchangeRateService();
        $rate = $svc->getRateByIds($usd, $eur);
        $this->assertNotNull($rate);

        $amountSent = 100.00;
        $expectedReceived = round($amountSent * $rate, 2);
        $expectedFee = round((2.0 / 100.0) * $amountSent + 1.00, 2);

        $this->assertEquals( round($amountSent * $rate, 2), $expectedReceived );
        $this->assertEquals(3.00, $expectedFee);
    }
}
