<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥'],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF'],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$'],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$'],
            
            // Arab Countries
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ'],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => '﷼'],
            ['code' => 'QAR', 'name' => 'Qatari Riyal', 'symbol' => '﷼'],
            ['code' => 'KWD', 'name' => 'Kuwaiti Dinar', 'symbol' => 'د.ك'],
            ['code' => 'BHD', 'name' => 'Bahraini Dinar', 'symbol' => '.د.ب'],
            ['code' => 'OMR', 'name' => 'Omani Rial', 'symbol' => '﷼'],
            ['code' => 'JOD', 'name' => 'Jordanian Dinar', 'symbol' => 'د.ا'],
            ['code' => 'TND', 'name' => 'Tunisian Dinar', 'symbol' => 'د.ت'],
            ['code' => 'LBP', 'name' => 'Lebanese Pound', 'symbol' => 'ل.ل'],
            ['code' => 'EGP', 'name' => 'Egyptian Pound', 'symbol' => '£'],
            ['code' => 'MAD', 'name' => 'Moroccan Dirham', 'symbol' => 'د.م.'],
            ['code' => 'DZD', 'name' => 'Algerian Dinar', 'symbol' => 'د.ج'],
            ['code' => 'SYP', 'name' => 'Syrian Pound', 'symbol' => '£S'],
            ['code' => 'IRR', 'name' => 'Iranian Rial', 'symbol' => '﷼'],

            // Asian & World Currencies
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹'],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥'],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$'],
            ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'symbol' => 'HK$'],
            ['code' => 'THB', 'name' => 'Thai Baht', 'symbol' => '฿'],
            ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'symbol' => 'RM'],
            ['code' => 'PHP', 'name' => 'Philippine Peso', 'symbol' => '₱'],
            ['code' => 'IDR', 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp'],
            ['code' => 'VND', 'name' => 'Vietnamese Dong', 'symbol' => '₫'],

            // Americas & Others
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$'],
            ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => '$'],
            ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R'],
            ['code' => 'RUB', 'name' => 'Russian Ruble', 'symbol' => '₽'],
            ['code' => 'TRY', 'name' => 'Turkish Lira', 'symbol' => '₺'],
            ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'symbol' => 'NZ$'],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['code' => $currency['code']],
                [
                    'name' => $currency['name'],
                    'symbol' => $currency['symbol'],
                ]
            );
        }
    }
}
