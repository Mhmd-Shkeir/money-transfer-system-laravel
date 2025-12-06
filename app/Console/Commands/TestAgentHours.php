<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AgentProfile;
use App\Models\BusinessHours;
use Carbon\Carbon;

class TestAgentHours extends Command
{
    protected $signature = 'test:agent-hours';
    protected $description = 'Test if agent is open now';

    public function handle()
    {
        $profile = AgentProfile::find(2);
        $this->line("Agent: {$profile->business_name}");
        $this->line("Is Approved: " . ($profile->is_approved ? 'YES' : 'NO'));

        $now = now();
        $this->line("Current Time: {$now->format('l H:i:s')}");
        $this->line("Day of Week: {$now->dayOfWeek} (0=Sun, 1=Mon...)");

        $businessHours = BusinessHours::where('agent_profile_id', $profile->id)
            ->where('day_of_week', $now->dayOfWeek)
            ->first();

        if ($businessHours) {
            $this->line("Hours for today:");
            $this->line("  Closed: " . ($businessHours->is_closed ? 'YES' : 'NO'));
            $this->line("  Open: {$businessHours->opening_time}");
            $this->line("  Close: {$businessHours->closing_time}");
        } else {
            $this->line("❌ No business hours found for today!");
        }

        $isOpen = $profile->isOpen();
        $this->line("Is Agent Open Now: " . ($isOpen ? '✅ YES' : '❌ NO'));
    }
}
