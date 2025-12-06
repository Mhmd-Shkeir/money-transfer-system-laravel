<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class SupportDemoSeeder extends Seeder
{
    public function run()
    {
        // Try to attach the demo ticket to an actual agent user if present
        $agentUser = User::where('role', 'agent')->first();

        // Build ticket data only with columns that exist in the current DB schema
        $ticketData = [];
        if (Schema::hasColumn('support_tickets', 'user_id')) {
            $ticketData['user_id'] = $agentUser ? $agentUser->id : null;
        }
        if (Schema::hasColumn('support_tickets', 'subject')) {
            $ticketData['subject'] = 'Agent: Test support request';
        }
        if (Schema::hasColumn('support_tickets', 'message')) {
            $ticketData['message'] = 'This is a demo support request submitted by an agent for testing.';
        }
        if (Schema::hasColumn('support_tickets', 'status')) {
            $ticketData['status'] = 'open';
        }
        if (Schema::hasColumn('support_tickets', 'source')) {
            $ticketData['source'] = 'agent';
        }

        // If no columns matched (very unlikely), create a minimal record to avoid error
        if (empty($ticketData)) {
            // fallback: use insert via DB so we at least create an ID if possible
            $id = \DB::table('support_tickets')->insertGetId([]);
            $ticket = SupportTicket::find($id);
        } else {
            $ticket = SupportTicket::create($ticketData);
        }

        // Create the threaded message (messages table exists)
        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $agentUser ? $agentUser->id : null,
            'message' => 'This is a demo support request submitted by an agent for testing.',
            'sender_role' => 'agent',
        ]);
    }
}
