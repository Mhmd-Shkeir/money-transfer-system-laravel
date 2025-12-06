<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;

class ReassignProcessingToAgent3Seeder extends Seeder
{
    public function run(): void
    {
        $count = Transaction::where('agent_id', 2)->where('status', 'processing')->update(['agent_id' => 3]);
        $this->command->info("Reassigned {$count} processing transaction(s) from agent_id=2 to agent_id=3");
    }
}
