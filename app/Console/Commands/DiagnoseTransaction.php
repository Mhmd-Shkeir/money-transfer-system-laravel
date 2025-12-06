<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\AgentProfile;
use App\Models\Transaction;

class DiagnoseTransaction extends Command
{
    protected $signature = 'diagnose:transaction';
    protected $description = 'Diagnose transaction and agent issues';

    public function handle()
    {
        $this->info('=== TRANSACTION DIAGNOSIS ===');

        // 1. Check pending transaction
        $this->line('\n📦 PENDING TRANSACTION:');
        $txn = Transaction::where('reference_code', 'like', 'TXN-%')->first();
        if ($txn) {
            $this->line("✅ Found: {$txn->reference_code}");
            $this->line("   Status: {$txn->status}");
            $this->line("   Type: {$txn->transaction_type}");
            $this->line("   Amount: {$txn->amount_sent}");
            $this->line("   Payout: {$txn->payout_method}");
            $this->line("   Agent ID: " . ($txn->agent_id ?? 'NULL (not assigned)'));
        } else {
            $this->error('❌ No pending transaction found');
        }

        // 2. Check agent
        $this->line('\n🤝 AGENT PROFILE:');
        $agent = User::where('email', 'demo.agent@example.com')->first();
        if ($agent) {
            $this->line("✅ Agent user found: {$agent->email}");
            $profile = $agent->agentProfile;
            if ($profile) {
                $this->line("   Profile ID: {$profile->id}");
                $this->line("   Is Approved: " . ($profile->is_approved ? 'YES ✅' : 'NO ❌'));
                $this->line("   Cash Balance: {$profile->cash_balance}");
                $this->line("   Business Name: {$profile->business_name}");
            } else {
                $this->error("   ❌ No agent profile found!");
            }
        } else {
            $this->error('❌ Demo agent not found');
        }

        // 3. Check sender/beneficiary
        $this->line('\n👤 SENDER & BENEFICIARY:');
        if ($txn) {
            if ($txn->sender) {
                $this->line("✅ Sender: {$txn->sender->first_name} {$txn->sender->last_name}");
            } else {
                $this->error('❌ Sender not found');
            }
            if ($txn->beneficiary) {
                $this->line("✅ Beneficiary: {$txn->beneficiary->name}");
            } else {
                $this->error('❌ Beneficiary not found');
            }
        }

        // 4. Check routes
        $this->line('\n🛣️  ROUTES:');
        $this->line("Accept route: /agent/incoming-requests/{id}/accept");
        $this->line("Reject route: /agent/incoming-requests/{id}/reject");

        // 5. Check incoming requests query
        $this->line('\n📋 INCOMING REQUESTS QUERY:');
        $requests = Transaction::where('transaction_type', 'transfer')
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->where('payout_method', 'cash_pickup')
                      ->orWhereNull('payout_method');
            })
            ->with(['beneficiary', 'sender'])
            ->orderByDesc('created_at')
            ->get();
        $this->line("Found: " . $requests->count() . " pending transfer(s)");
        foreach ($requests as $req) {
            $this->line("  - {$req->reference_code}: {$req->amount_sent} from {$req->sender->first_name}");
        }

        // Extra diagnostics: assigned transactions for demo.agenttink and all processing transactions
        $this->line('\n📌 ASSIGNED TRANSACTIONS FOR DEMO AGENT (demo.agenttink@example.com):');
        $demo = User::where('email', 'demo.agenttink@example.com')->first();
        if ($demo && $demo->agentProfile) {
            $assigned = Transaction::where('agent_id', $demo->agentProfile->id)->orderByDesc('created_at')->get();
            $this->line('Agent Profile ID: ' . $demo->agentProfile->id . ' (user: ' . $demo->email . ')');
            $this->line('Assigned count: ' . $assigned->count());
            foreach ($assigned as $a) {
                $this->line("  - {$a->reference_code}: {$a->amount_sent} | status: {$a->status}");
            }
        } else {
            $this->line('Demo agent (tink) not found or no profile.');
        }

        $this->line('\n🔎 ALL PROCESSING TRANSACTIONS:');
        $processing = Transaction::where('status', 'processing')->orderByDesc('created_at')->get();
        $this->line('Processing count: ' . $processing->count());
        foreach ($processing as $p) {
            $this->line("  - {$p->reference_code}: amt {$p->amount_sent} | agent_id: " . ($p->agent_id ?? 'NULL'));
        }

        $this->info('\n=== END DIAGNOSIS ===');
    }
}
