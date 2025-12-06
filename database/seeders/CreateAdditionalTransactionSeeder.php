<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\Transaction;
use Illuminate\Support\Str;

class CreateAdditionalTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Locate test client and beneficiary by known emails/ids
        $client = User::where('email', 'test.clienttink@example.com')->first();
        if (! $client) {
            $this->command->error('Client user test.clienttink@example.com not found.');
            return;
        }

        $benef = Beneficiary::where('user_id', $client->id)->first();
        if (! $benef) {
            $this->command->error('Beneficiary for client not found.');
            return;
        }

        $txn = Transaction::create([
            'sender_id' => $client->id,
            'beneficiary_id' => $benef->id,
            'agent_id' => null,
            'payment_method_id' => null,
            'from_currency_id' => null,
            'to_currency_id' => null,
            'amount_sent' => 250.00,
            'exchange_rate' => 1.0,
            'fee' => 5.00,
            'total_paid' => 255.00,
            'amount_received' => 250.00,
            'payout_method' => 'cash_pickup',
            'status' => 'pending',
            'transaction_type' => 'transfer',
            'reference_code' => 'TXN-'.Str::upper(substr(str_replace('-','', Str::uuid()),0,10)),
        ]);

        $this->command->info("Created transaction {$txn->reference_code} (id: {$txn->id}) for client id {$client->id} and beneficiary id {$benef->id}");
    }
}
