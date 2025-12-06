<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Currency;
use App\Services\ExchangeRateService;

echo "Testing currency update functionality...\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "No users found in database.\n";
    exit;
}

echo "User: " . $user->email . "\n";
echo "Current currency_id: " . $user->currency_id . "\n";
echo "Current balance: " . $user->wallet_balance . "\n";

// Get available currencies
$currencies = Currency::all();
echo "Available currencies:\n";
foreach ($currencies as $currency) {
    echo "  {$currency->id}: {$currency->code} - {$currency->name}\n";
}

// Test updating to a different currency
if ($currencies->count() > 1) {
    $newCurrency = $currencies->firstWhere('id', '!=', $user->currency_id);
    if ($newCurrency) {
        echo "\nTesting update to currency: {$newCurrency->code}\n";
        
        // Get current currency
        $currentCurrency = $user->getPreferredCurrency();
        echo "Current currency: {$currentCurrency->code}\n";
        echo "New currency: {$newCurrency->code}\n";
        
        // Test exchange rate
        $exchangeService = new ExchangeRateService();
        $rate = $exchangeService->getRateByIds($currentCurrency->id, $newCurrency->id);
        echo "Exchange rate: " . ($rate ? $rate : 'Not available') . "\n";
        
        if ($rate) {
            $convertedBalance = $user->wallet_balance * $rate;
            echo "Converted balance: {$convertedBalance}\n";
            
            // Update user
            $user->wallet_balance = $convertedBalance;
            $user->currency_id = $newCurrency->id;
            $saved = $user->save();
            
            echo "Save successful: " . ($saved ? 'Yes' : 'No') . "\n";
            echo "New currency_id: " . $user->currency_id . "\n";
            echo "New balance: " . $user->wallet_balance . "\n";
        }
    }
}
?>