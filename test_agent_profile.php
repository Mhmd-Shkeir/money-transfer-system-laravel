<?php

require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();

try {
    $agent = \App\Models\User::where('email', 'demo.agent@example.com')->with('agentProfile', 'agentProfile.businessHours')->first();
    
    echo "=== AGENT PROFILE TEST ===\n";
    echo "Agent exists: " . ($agent ? "YES" : "NO") . "\n";
    
    if ($agent) {
        echo "Agent Name: " . $agent->first_name . " " . $agent->last_name . "\n";
        echo "Agent Email: " . $agent->email . "\n";
        
        if ($agent->agentProfile) {
            echo "\n--- Business Profile ---\n";
            echo "Business Name: " . ($agent->agentProfile->business_name ?? 'Not set') . "\n";
            echo "Registration: " . ($agent->agentProfile->business_registration_number ?? 'Not set') . "\n";
            echo "Country ID: " . ($agent->agentProfile->country_id ?? 'Not set') . "\n";
            echo "Currency ID: " . ($agent->agentProfile->currency_id ?? 'Not set') . "\n";
            echo "Timezone: " . ($agent->agentProfile->timezone ?? 'Not set') . "\n";
            echo "Business Hours Count: " . $agent->agentProfile->businessHours()->count() . "\n";
            
            echo "\n--- Business Hours ---\n";
            foreach ($agent->agentProfile->businessHours()->orderBy('day_of_week')->get() as $hours) {
                echo $hours->getDayName() . ": ";
                if ($hours->is_closed) {
                    echo "CLOSED\n";
                } else {
                    echo $hours->opening_time . " - " . $hours->closing_time . "\n";
                }
            }
        }
    }
    
    echo "\n=== COUNTRIES TEST ===\n";
    $arabCountries = \App\Models\Country::where('region', 'like', '%Middle East%')->orWhere('region', 'Africa')->count();
    echo "Arab/African Countries: " . $arabCountries . "\n";
    
    $israel = \App\Models\Country::where('iso_code', 'ISR')->first();
    echo "Israel still in DB: " . ($israel ? "YES (ERROR)" : "NO (Good)") . "\n";
    
    echo "\n✅ All tests completed!\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
