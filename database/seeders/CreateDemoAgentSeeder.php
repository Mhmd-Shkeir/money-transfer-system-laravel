<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AgentProfile;
use App\Models\Country;
use App\Models\Currency;
use App\Models\BusinessHours;
use Illuminate\Database\Seeder;

class CreateDemoAgentSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo agent user
        $agent = User::firstOrCreate(
            ['email' => 'demo.agent@example.com'],
            [
                'first_name' => 'Demo',
                'last_name' => 'Agent',
                'phone' => '+1-555-0001',
                'password_hash' => bcrypt('Password1!'),
                'role' => 'agent',
                'is_email_verified' => true,
            ]
        );
        echo "✅ Demo Agent User: {$agent->email}\n";

        // Create agent profile
        $usa = Country::where('iso_code', 'US')->first() ?? Country::first();
        $usd = Currency::where('code', 'USD')->first() ?? Currency::first();

        $profile = AgentProfile::firstOrCreate(
            ['user_id' => $agent->id],
            [
                'store_name' => 'Demo Money Transfer Store',
                'address' => '123 Main St, New York, NY 10001',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'business_name' => 'Demo Money Transfer Store',
                'business_registration_number' => 'REG-123456',
                'business_description' => 'Test agent for transaction processing',
                'office_address' => '123 Main St, New York, NY 10001',
                'country_id' => $usa?->id ?? 1,
                'currency_id' => $usd?->id ?? 1,
                'timezone' => 'America/New_York',
                'business_phone' => '+1-555-0001',
                'business_email' => 'demo.agent@example.com',
                'commission_rate' => 2.5,
                'cash_balance' => 10000,
                'total_cash_in' => 10000,
                'total_cash_out' => 0,
                'total_transactions' => 0,
                'is_approved' => true,
            ]
        );
        echo "✅ Agent Profile: {$profile->business_name}\n";
        echo "   Approved: YES\n";
        echo "   Cash Balance: {$profile->cash_balance}\n";

        // Set business hours (Mon-Fri 9-6, Sat 10-4, Sun closed)
        $hours = [
            0 => ['day' => 'Sunday', 'open' => null, 'close' => null, 'closed' => true],
            1 => ['day' => 'Monday', 'open' => '09:00', 'close' => '18:00', 'closed' => false],
            2 => ['day' => 'Tuesday', 'open' => '09:00', 'close' => '18:00', 'closed' => false],
            3 => ['day' => 'Wednesday', 'open' => '09:00', 'close' => '18:00', 'closed' => false],
            4 => ['day' => 'Thursday', 'open' => '09:00', 'close' => '18:00', 'closed' => false],
            5 => ['day' => 'Friday', 'open' => '09:00', 'close' => '18:00', 'closed' => false],
            6 => ['day' => 'Saturday', 'open' => '10:00', 'close' => '16:00', 'closed' => false],
        ];

        foreach ($hours as $dayOfWeek => $h) {
            BusinessHours::updateOrCreate(
                ['agent_profile_id' => $profile->id, 'day_of_week' => $dayOfWeek],
                [
                    'opening_time' => $h['open'],
                    'closing_time' => $h['close'],
                    'is_closed' => $h['closed'],
                ]
            );
        }
        echo "✅ Business Hours Set: Mon-Fri 9-6, Sat 10-4, Sun Closed\n";
        echo "\n✅ Demo agent ready! Login: demo.agent@example.com / Password1!\n";
    }
}
