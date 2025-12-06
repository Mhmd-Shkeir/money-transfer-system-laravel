<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\Transaction;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing demo users with specific emails if present
        User::whereIn('email', ['demo.admin@example.com','demo.user@example.com','demo.agent@example.com'])->delete();

        // Create admin
        $admin = User::create([
            'first_name' => 'Demo',
            'last_name' => 'Admin',
            'email' => 'demo.admin@example.com',
            'password_hash' => Hash::make('Password1!'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create normal user
        $user = User::create([
            'first_name' => 'Demo',
            'last_name' => 'User',
            'email' => 'demo.user@example.com',
            'password_hash' => Hash::make('Password1!'),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Create agent user and agent profile
        $agent = User::create([
            'first_name' => 'Demo',
            'last_name' => 'Agent',
            'email' => 'demo.agent@example.com',
            'password_hash' => Hash::make('Password1!'),
            'role' => 'agent',
            'status' => 'active',
        ]);

        // Create agent profile required by transactions.agent_id foreign key
        $agentProfile = \App\Models\AgentProfile::create([
            'user_id' => $agent->id,
            'store_name' => 'Demo Agent Store',
            'address' => '123 Demo Street',
            'is_approved' => true,
            'commission_rate' => 1.5,
        ]);

        // Ensure at least one country exists (beneficiaries require country_id)
        $country = \DB::table('countries')->first();
        if (!$country) {
            $countryId = \DB::table('countries')->insertGetId([
                'name' => 'United States',
                'iso_code' => 'USA',
                'currency_id' => null,
                'region' => 'Americas'
            ]);
        } else {
            $countryId = $country->id;
        }

        // Create a beneficiary for demo user
        $beneficiary = Beneficiary::create([
            'user_id' => $user->id,
            'name' => 'Maria Garcia',
            'email' => 'maria.garcia@example.com',
            'phone' => '+1234567890',
            'country_id' => $countryId,
        ]);

        // Create a few transactions for demo user with different dates/statuses
        $now = Carbon::now();

        Transaction::create([
            'sender_id' => $user->id,
            'beneficiary_id' => $beneficiary->id,
            'agent_id' => $agentProfile->id,
            'amount_sent' => 500.00,
            'exchange_rate' => 1.0,
            'fee' => 5.00,
            'total_paid' => 505.00,
            'amount_received' => 500.00,
            'payout_method' => 'bank_deposit',
            'status' => 'completed',
            'reference_code' => Str::upper(Str::random(10)),
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(2),
        ]);

        Transaction::create([
            'sender_id' => $user->id,
            'beneficiary_id' => $beneficiary->id,
            'agent_id' => $agentProfile->id,
            'amount_sent' => 250.00,
            'exchange_rate' => 1.0,
            'fee' => 2.50,
            'total_paid' => 252.50,
            'amount_received' => 250.00,
            'payout_method' => 'cash_pickup',
            'status' => 'processing',
            'reference_code' => Str::upper(Str::random(10)),
            'created_at' => $now->copy()->subDays(4),
            'updated_at' => $now->copy()->subDays(4),
        ]);

        // Older transaction last month
        Transaction::create([
            'sender_id' => $user->id,
            'beneficiary_id' => $beneficiary->id,
            'agent_id' => $agentProfile->id,
            'amount_sent' => 1200.00,
            'exchange_rate' => 1.0,
            'fee' => 12.00,
            'total_paid' => 1212.00,
            'amount_received' => 1200.00,
            'payout_method' => 'mobile_wallet',
            'status' => 'completed',
            'reference_code' => Str::upper(Str::random(10)),
            'created_at' => $now->copy()->subDays(35),
            'updated_at' => $now->copy()->subDays(35),
        ]);

        $this->command->info('Demo users, beneficiary and transactions created.');
    }
}
