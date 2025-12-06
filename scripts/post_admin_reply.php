<?php
// Post an admin reply to a support ticket (scriptable helper).
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\SupportTicket;
use App\Models\SupportMessage;

$ticketId = isset($argv[1]) ? $argv[1] : 5; // default to ticket 5
$admin = User::where('role','admin')->first();
$adminId = $admin ? $admin->id : null;

$ticket = SupportTicket::find($ticketId);
if (!$ticket) {
    echo "TICKET_NOT_FOUND\n";
    exit(1);
}

// Build message row
$messageText = isset($argv[2]) ? $argv[2] : "Admin test reply: we received your request and will follow up shortly.";

// create support_messages row
$msgId = DB::table('support_messages')->insertGetId([
    'support_ticket_id' => $ticket->id,
    'user_id' => $adminId,
    'message' => $messageText,
    'sender_role' => 'admin',
    'created_at' => now(),
    'updated_at' => now(),
]);

// update ticket fields as appropriate to your existing schema
$update = [];
if (Schema::hasColumn('support_tickets','status')) $update['status'] = 'answered';
if (Schema::hasColumn('support_tickets','answered_by') && $adminId) $update['answered_by'] = $adminId;

if (!empty($update)) {
    DB::table('support_tickets')->where('id', $ticket->id)->update($update + ['updated_at' => now()]);
}

echo "REPLY_CREATED|ticket_id={$ticket->id}|message_id={$msgId}\n";
