<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AgentProfile;
use App\Models\BusinessHours;
use App\Models\Beneficiary;
use App\Models\Transaction;
use Illuminate\Support\Str;

class CreateTempAgentAndTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // create or get demo agent user
        $agent = User::firstOrCreate(
            ['email' => 'demo.agenttink@example.com'],
            [
                'first_name' => 'Demo',
                'last_name' => 'AgentTink',
                'phone' => '+100000001',
                'password_hash' => bcrypt('Password1!'),
                'role' => 'agent',
                'is_email_verified' => true,
            ]
        );
        $this->command->info("Agent user: {$agent->email} (id: {$agent->id})");

        // create or get agent profile
        $profile = AgentProfile::firstOrCreate(
            ['user_id' => $agent->id],
            [
                'business_name' => 'Demo Tinker Agent',
                'store_name' => 'Tinker Store',
                'address' => '123 Demo St',
                'latitude' => 0.0,
                'longitude' => 0.0,
                'working_hours' => null,
                'is_approved' => true,
                'commission_rate' => 2.5,
                'cash_balance' => 10000.00,
                'total_cash_in' => 0,
                'total_cash_out' => 0,
                'total_transactions' => 0,
                'country_id' => 1,
                'currency_id' => 1,
            ]
        );
        $this->command->info("Agent profile id: {$profile->id}");

        // business hours (one day sample)
        BusinessHours::updateOrCreate(
            ['agent_profile_id' => $profile->id, 'day_of_week' => 1],
            ['opening_time' => '09:00:00', 'closing_time' => '18:00:00', 'is_closed' => false]
        );

        // create client user
        $client = User::firstOrCreate(
            ['email' => 'test.clienttink@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'ClientTink',
                'phone' => '+199999999',
                'password_hash' => bcrypt('Password1!'),
                'role' => 'user',
                'is_email_verified' => true,
            ]
        );
        $this->command->info("Client user: {$client->email} (id: {$client->id})");

        // create beneficiary for client
        $benef = Beneficiary::firstOrCreate(
            ['user_id' => $client->id, 'name' => 'Tinker Beneficiary'],
            [
                'phone' => '+188888888',
                'email' => '',
                'country_id' => 1,
                'bank_name' => 'Test Bank',
                'bank_account' => '1234567890',
            ]
        );
        $this->command->info("Beneficiary id: {$benef->id}");

        // create pending transfer
        $txn = Transaction::create([
            'sender_id' => $client->id,
            'beneficiary_id' => $benef->id,
            'agent_id' => null,
            'payment_method_id' => null,
            'from_currency_id' => null,
            'to_currency_id' => null,
            'amount_sent' => 500.00,
            'exchange_rate' => 1.0,
            'fee' => 10.00,
            'total_paid' => 510.00,
            'amount_received' => 500.00,
            'payout_method' => 'cash_pickup',
            'status' => 'pending',
            'transaction_type' => 'transfer',
            'reference_code' => 'TXN-'.Str::upper(substr(str_replace('-','', Str::uuid()),0,10)),
        ]);

        $this->command->info("Created transaction {$txn->reference_code} (id: {$txn->id})");
    }
}
