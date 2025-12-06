<?php

require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\Transaction;
use Illuminate\Support\Str;

try {
    // 1. Create a test client user
    $client = User::firstOrCreate(
        ['email' => 'testclient@example.com'],
        [
            'first_name' => 'Test',
            'last_name' => 'Client',
            'phone' => '+1234567890',
            'password' => bcrypt('Password1!'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]
    );
    echo "✅ Test Client Created/Found: {$client->email} (ID: {$client->id})\n";

    // 2. Create a test beneficiary
    $beneficiary = Beneficiary::firstOrCreate(
        ['user_id' => $client->id, 'name' => 'John Doe'],
        [
            'account_number' => '1234567890',
            'bank_name' => 'Test Bank',
            'country_id' => 1,
        ]
    );
    echo "✅ Test Beneficiary Created/Found: {$beneficiary->name} (ID: {$beneficiary->id})\n";

    // 3. Create a pending transfer transaction
    $transaction = Transaction::create([
        'sender_id' => $client->id,
        'beneficiary_id' => $beneficiary->id,
        'agent_id' => null, // No agent assigned yet
        'amount_sent' => 500,
        'amount_received' => 500,
        'total_paid' => 500,
        'transaction_type' => 'transfer',
        'status' => 'pending',
        'payout_method' => 'cash_pickup',
        'reference_code' => 'TXN-' . Str::upper(Str::random(10)),
        'created_at' => now(),
    ]);
    echo "✅ Test Transaction Created: {$transaction->reference_code}\n";
    echo "   Amount: 500\n";
    echo "   From: {$client->first_name} {$client->last_name}\n";
    echo "   To: {$beneficiary->name}\n";
    echo "   Status: PENDING\n";

    echo "\n🎯 Next Steps:\n";
    echo "1. Log in as agent (email: demo.agent@example.com, password: Password1!)\n";
    echo "2. Go to: http://127.0.0.1:8000/agent/requests\n";
    echo "3. You should see the pending transfer: {$transaction->reference_code}\n";
    echo "4. Click 'Accept & Process'\n";
    echo "5. Click 'Cash Out' and complete the payout\n";
    echo "6. Check notifications and emails\n";

} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo $e->getTraceAsString();
}
?>
