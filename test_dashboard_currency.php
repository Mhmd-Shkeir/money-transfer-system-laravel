<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Currency;

// Simulate dashboard currency update
$user = User::first();
if (!$user) {
    echo "No users found\n";
    exit;
}

echo "=== INITIAL STATE ===\n";
echo "User: {$user->email}\n";
echo "Currency ID: " . ($user->currency_id ?? 'NULL') . "\n";
echo "Balance: {$user->wallet_balance}\n";

// Simulate selecting EUR currency (ID: 2)
$eur = Currency::find(2);
if (!$eur) {
    echo "EUR currency not found\n";
    exit;
}

// Update as the controller would
$oldBalance = $user->wallet_balance;
$user->currency_id = $eur->id;

// For testing, let's simulate a conversion (like the controller does)
// USD to EUR conversion rate (example: 0.92)
$conversionRate = 0.92;
$user->wallet_balance = round($oldBalance * $conversionRate, 2);

$saved = $user->save();

echo "\n=== AFTER CURRENCY CHANGE TO EUR ===\n";
echo "Save successful: " . ($saved ? 'YES' : 'NO') . "\n";
echo "Currency ID: " . ($user->currency_id ?? 'NULL') . "\n";
echo "Old balance (USD): {$oldBalance}\n";
echo "New balance (EUR): {$user->wallet_balance}\n";
echo "Conversion rate used: {$conversionRate}\n";

// Verify persistence
$refreshed = User::find($user->id);
echo "\n=== PERSISTENCE CHECK ===\n";
echo "Currency ID: " . ($refreshed->currency_id ?? 'NULL') . "\n";
echo "Balance: {$refreshed->wallet_balance}\n";

// Now simulate changing back to USD
$usd = Currency::find(1);
$oldBalanceEur = $refreshed->wallet_balance;
$refreshed->currency_id = $usd->id;

// EUR to USD conversion (1 / 0.92)
$conversionRateBack = 1 / $conversionRate;
$refreshed->wallet_balance = round($oldBalanceEur * $conversionRateBack, 2);

$savedBack = $refreshed->save();

echo "\n=== AFTER CURRENCY CHANGE BACK TO USD ===\n";
echo "Save successful: " . ($savedBack ? 'YES' : 'NO') . "\n";
echo "Currency ID: " . ($refreshed->currency_id ?? 'NULL') . "\n";
echo "Old balance (EUR): {$oldBalanceEur}\n";
echo "New balance (USD): {$refreshed->wallet_balance}\n";
echo "Conversion rate used: {$conversionRateBack}\n";
?>