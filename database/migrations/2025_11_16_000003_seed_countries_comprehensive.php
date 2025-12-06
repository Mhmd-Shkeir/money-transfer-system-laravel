<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Insert comprehensive countries list if not already populated
        $countriesData = [
            ['name' => 'United States', 'iso_code' => 'USA', 'region' => 'North America'],
            ['name' => 'Canada', 'iso_code' => 'CAN', 'region' => 'North America'],
            ['name' => 'Mexico', 'iso_code' => 'MEX', 'region' => 'North America'],
            ['name' => 'United Kingdom', 'iso_code' => 'GBR', 'region' => 'Europe'],
            ['name' => 'France', 'iso_code' => 'FRA', 'region' => 'Europe'],
            ['name' => 'Germany', 'iso_code' => 'DEU', 'region' => 'Europe'],
            ['name' => 'Spain', 'iso_code' => 'ESP', 'region' => 'Europe'],
            ['name' => 'Italy', 'iso_code' => 'ITA', 'region' => 'Europe'],
            ['name' => 'Netherlands', 'iso_code' => 'NLD', 'region' => 'Europe'],
            ['name' => 'Belgium', 'iso_code' => 'BEL', 'region' => 'Europe'],
            ['name' => 'Switzerland', 'iso_code' => 'CHE', 'region' => 'Europe'],
            ['name' => 'Sweden', 'iso_code' => 'SWE', 'region' => 'Europe'],
            ['name' => 'Norway', 'iso_code' => 'NOR', 'region' => 'Europe'],
            ['name' => 'Denmark', 'iso_code' => 'DNK', 'region' => 'Europe'],
            ['name' => 'Finland', 'iso_code' => 'FIN', 'region' => 'Europe'],
            ['name' => 'Poland', 'iso_code' => 'POL', 'region' => 'Europe'],
            ['name' => 'Russia', 'iso_code' => 'RUS', 'region' => 'Europe/Asia'],
            ['name' => 'Japan', 'iso_code' => 'JPN', 'region' => 'Asia'],
            ['name' => 'China', 'iso_code' => 'CHN', 'region' => 'Asia'],
            ['name' => 'India', 'iso_code' => 'IND', 'region' => 'Asia'],
            ['name' => 'South Korea', 'iso_code' => 'KOR', 'region' => 'Asia'],
            ['name' => 'Singapore', 'iso_code' => 'SGP', 'region' => 'Asia'],
            ['name' => 'Thailand', 'iso_code' => 'THA', 'region' => 'Asia'],
            ['name' => 'Vietnam', 'iso_code' => 'VNM', 'region' => 'Asia'],
            ['name' => 'Philippines', 'iso_code' => 'PHL', 'region' => 'Asia'],
            ['name' => 'Indonesia', 'iso_code' => 'IDN', 'region' => 'Asia'],
            ['name' => 'Malaysia', 'iso_code' => 'MYS', 'region' => 'Asia'],
            ['name' => 'Hong Kong', 'iso_code' => 'HKG', 'region' => 'Asia'],
            ['name' => 'Taiwan', 'iso_code' => 'TWN', 'region' => 'Asia'],
            ['name' => 'Australia', 'iso_code' => 'AUS', 'region' => 'Oceania'],
            ['name' => 'New Zealand', 'iso_code' => 'NZL', 'region' => 'Oceania'],
            ['name' => 'Brazil', 'iso_code' => 'BRA', 'region' => 'South America'],
            ['name' => 'Argentina', 'iso_code' => 'ARG', 'region' => 'South America'],
            ['name' => 'Chile', 'iso_code' => 'CHL', 'region' => 'South America'],
            ['name' => 'Colombia', 'iso_code' => 'COL', 'region' => 'South America'],
            ['name' => 'Peru', 'iso_code' => 'PER', 'region' => 'South America'],
            ['name' => 'Venezuela', 'iso_code' => 'VEN', 'region' => 'South America'],
            ['name' => 'South Africa', 'iso_code' => 'ZAF', 'region' => 'Africa'],
            ['name' => 'Egypt', 'iso_code' => 'EGY', 'region' => 'Africa'],
            ['name' => 'Nigeria', 'iso_code' => 'NGA', 'region' => 'Africa'],
            ['name' => 'Kenya', 'iso_code' => 'KEN', 'region' => 'Africa'],
            ['name' => 'Ethiopia', 'iso_code' => 'ETH', 'region' => 'Africa'],
            ['name' => 'Ghana', 'iso_code' => 'GHA', 'region' => 'Africa'],
            ['name' => 'Morocco', 'iso_code' => 'MAR', 'region' => 'Africa'],
            ['name' => 'Tunisia', 'iso_code' => 'TUN', 'region' => 'Africa'],
            ['name' => 'United Arab Emirates', 'iso_code' => 'ARE', 'region' => 'Middle East'],
            ['name' => 'Saudi Arabia', 'iso_code' => 'SAU', 'region' => 'Middle East'],
            ['name' => 'Israel', 'iso_code' => 'ISR', 'region' => 'Middle East'],
            ['name' => 'Turkey', 'iso_code' => 'TUR', 'region' => 'Middle East'],
            ['name' => 'Pakistan', 'iso_code' => 'PAK', 'region' => 'Asia'],
            ['name' => 'Bangladesh', 'iso_code' => 'BGD', 'region' => 'Asia'],
            ['name' => 'Sri Lanka', 'iso_code' => 'LKA', 'region' => 'Asia'],
        ];

        DB::table('countries')->insertOrIgnore($countriesData);
    }

    public function down(): void {
        // Don't delete countries on rollback as they might be in use
    }
};
