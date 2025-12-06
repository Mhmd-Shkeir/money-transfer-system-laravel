<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Currency;

// Get first user
$user = User::first();
if (!$user) {
    echo "No users found\n";
    exit;
}

echo "=== BEFORE UPDATE ===\n";
echo "User: {$user->email}\n";
echo "Currency ID: " . ($user->currency_id ?? 'NULL') . "\n";
echo "Balance: {$user->wallet_balance}\n";

// Get EUR currency
$eur = Currency::where('code', 'EUR')->first();
if (!$eur) {
    echo "EUR currency not found\n";
    exit;
}

// Update currency and balance
$user->currency_id = $eur->id;
$user->wallet_balance = 50.00; // Set a test balance
$saved = $user->save();

echo "\n=== AFTER UPDATE ===\n";
echo "Save successful: " . ($saved ? 'YES' : 'NO') . "\n";
echo "Currency ID: " . ($user->currency_id ?? 'NULL') . "\n";
echo "Balance: {$user->wallet_balance}\n";

// Verify the changes persisted
$refreshedUser = User::find($user->id);
echo "\n=== VERIFICATION ===\n";
echo "Currency ID: " . ($refreshedUser->currency_id ?? 'NULL') . "\n";
echo "Balance: {$refreshedUser->wallet_balance}\n";
?>