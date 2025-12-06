<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // For agent_profiles that have a user_id, set agent cash totals to match the user's wallet fields.
        // We treat the user's wallet_balance as the source of truth and copy it into agent_profiles.
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE agent_profiles ap JOIN users u ON ap.user_id = u.id SET ap.cash_balance = u.wallet_balance, ap.total_cash_in = u.total_deposits, ap.total_cash_out = u.total_withdrawals"
        );

        // Optionally write a log entry for audit
        \Illuminate\Support\Facades\Log::info('sync_agent_user_balances migration applied: agent_profiles updated from users.wallet_balance');
    }

    public function down()
    {
        // On rollback, copy agent_profiles.cash_balance back to users.wallet_balance for linked users.
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE users u JOIN agent_profiles ap ON ap.user_id = u.id SET u.wallet_balance = ap.cash_balance, u.total_deposits = ap.total_cash_in, u.total_withdrawals = ap.total_cash_out"
        );

        \Illuminate\Support\Facades\Log::info('sync_agent_user_balances migration rolled back: users updated from agent_profiles');
    }
};
