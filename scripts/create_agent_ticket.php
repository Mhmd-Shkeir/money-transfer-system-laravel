<?php
// Bootstrap Laravel application and create a demo support ticket for the first agent user (if any).
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\SupportTicket;
use App\Models\SupportMessage;

$agent = User::where('role', 'agent')->first();
if (!$agent) {
    echo "NO_AGENT\n";
    exit(0);
}

$ticketData = [];
if (Schema::hasColumn('support_tickets', 'user_id')) {
    $ticketData['user_id'] = $agent->id;
}
if (Schema::hasColumn('support_tickets', 'subject')) {
    $ticketData['subject'] = 'Agent: Demo support request';
}
if (Schema::hasColumn('support_tickets', 'message')) {
    $ticketData['message'] = 'This is a demo support request submitted by an agent.';
}
if (Schema::hasColumn('support_tickets', 'status')) {
    $ticketData['status'] = 'open';
}
if (Schema::hasColumn('support_tickets', 'source')) {
    $ticketData['source'] = 'agent';
}

if (empty($ticketData)) {
    // Last-resort: try to insert minimally and fetch id
    $id = \DB::table('support_tickets')->insertGetId([]);
    $ticket = SupportTicket::find($id);
} else {
    $ticket = SupportTicket::create($ticketData);
}

SupportMessage::create([
    'support_ticket_id' => $ticket->id,
    'user_id' => $agent->id,
    'message' => 'This is a demo support request submitted by an agent.',
    'sender_role' => 'agent',
]);

echo "CREATED|" . $ticket->id . "\n";
