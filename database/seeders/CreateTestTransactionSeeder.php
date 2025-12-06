<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Beneficiary;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CreateTestTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test client user
        $client = User::firstOrCreate(
            ['email' => 'testclient@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'Client',
                'phone' => '+1234567890',
                'password_hash' => bcrypt('Password1!'),
                'role' => 'user',
                'is_email_verified' => true,
            ]
        );
        echo "✅ Test Client: {$client->email} (ID: {$client->id})\n";

        // Create a test beneficiary
        $beneficiary = Beneficiary::firstOrCreate(
            ['user_id' => $client->id, 'name' => 'John Doe'],
            [
                'phone' => '+9876543210',
                'bank_name' => 'Test Bank',
                'bank_account' => '1234567890',
                'country_id' => 1,
            ]
        );
        echo "✅ Test Beneficiary: {$beneficiary->name}\n";

        // Create a pending transfer
        $transaction = Transaction::create([
            'sender_id' => $client->id,
            'beneficiary_id' => $beneficiary->id,
            'agent_id' => null,
            'amount_sent' => 500,
            'amount_received' => 500,
            'total_paid' => 500,
            'transaction_type' => 'transfer',
            'status' => 'pending',
            'payout_method' => 'cash_pickup',
            'reference_code' => 'TXN-' . Str::upper(Str::random(10)),
        ]);
        echo "✅ Pending Transaction Created: {$transaction->reference_code}\n";
        echo "   Amount: 500 | From: {$client->first_name} | To: {$beneficiary->name}\n";
    }
}
