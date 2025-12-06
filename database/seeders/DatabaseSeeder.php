<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed currencies first
        $this->call(CurrencySeeder::class);

        // User::factory(10)->create();
        // Create a simple test user without using model factories (not available in this repo setup)
        \App\Models\User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password_hash' => bcrypt('Password1!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Seed fixed currencies and exchange rates for deterministic conversions
        $this->call([
            \Database\Seeders\CurrencySeeder::class,
            \Database\Seeders\ExchangeRateSeeder::class,
            \Database\Seeders\DemoSeeder::class,
        ]);
    }
}
