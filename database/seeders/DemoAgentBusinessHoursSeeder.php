<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AgentProfile;
use App\Models\BusinessHours;
use App\Models\Country;
use App\Models\Currency;

class DemoAgentBusinessHoursSeeder extends Seeder
{
    public function run(): void
    {
        // Find or create demo agent
        $demoAgent = User::where('email', 'demo.agent@example.com')->first();
        
        if (!$demoAgent) {
            return; // Demo agent doesn't exist yet
        }

        $agentProfile = $demoAgent->agentProfile;
        
        if (!$agentProfile) {
            return; // Agent profile doesn't exist
        }

        // Get USA and USD as defaults
        $usa = Country::where('iso_code', 'USA')->first() ?? Country::first();
        $usd = Currency::where('code', 'USD')->first() ?? Currency::first();

        // Update agent profile with real business info
        if ($usa) $agentProfile->country_id = $usa->id;
        if ($usd) $agentProfile->currency_id = $usd->id;
        $agentProfile->timezone = 'America/New_York';
        $agentProfile->business_phone = '+1-555-123-4567';
        $agentProfile->business_email = 'business@demoagent.com';
        $agentProfile->website = 'https://demoagent.example.com';
        $agentProfile->service_description = 'Professional money transfer and financial services for individuals and businesses.';
        $agentProfile->latitude = 40.7128;
        $agentProfile->longitude = -74.0060;
        $agentProfile->save();

        // Set business hours (Mon-Fri 9AM-6PM, Sat 10AM-4PM, Sun Closed)
        $businessHours = [
            0 => ['day' => 'Sunday', 'open' => null, 'close' => null, 'closed' => true],
            1 => ['day' => 'Monday', 'open' => '09:00', 'close' => '18:00', 'closed' => false],
            2 => ['day' => 'Tuesday', 'open' => '09:00', 'close' => '18:00', 'closed' => false],
            3 => ['day' => 'Wednesday', 'open' => '09:00', 'close' => '18:00', 'closed' => false],
            4 => ['day' => 'Thursday', 'open' => '09:00', 'close' => '18:00', 'closed' => false],
            5 => ['day' => 'Friday', 'open' => '09:00', 'close' => '18:00', 'closed' => false],
            6 => ['day' => 'Saturday', 'open' => '10:00', 'close' => '16:00', 'closed' => false],
        ];

        foreach ($businessHours as $dayOfWeek => $hours) {
            BusinessHours::updateOrCreate(
                [
                    'agent_profile_id' => $agentProfile->id,
                    'day_of_week' => $dayOfWeek,
                ],
                [
                    'opening_time' => $hours['open'],
                    'closing_time' => $hours['close'],
                    'is_closed' => $hours['closed'],
                ]
            );
        }

        echo "✅ Demo agent business hours seeded successfully!\n";
    }
}
