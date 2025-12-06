<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\SupportMessage;

$agent = User::where('role', 'agent')->first();
if (!$agent) {
    echo "NO_AGENT\n";
    exit(0);
}

$data = [];
if (Schema::hasColumn('support_tickets','user_id')) $data['user_id'] = $agent->id;
if (Schema::hasColumn('support_tickets','role')) $data['role'] = 'agent';
if (Schema::hasColumn('support_tickets','question')) $data['question'] = 'This is a demo support request submitted by an agent.';
if (Schema::hasColumn('support_tickets','status')) $data['status'] = 'open';
if (Schema::hasColumn('support_tickets','answered_by')) $data['answered_by'] = null;

$id = DB::table('support_tickets')->insertGetId($data);

DB::table('support_messages')->insert([
    'support_ticket_id' => $id,
    'user_id' => $agent->id,
    'message' => 'This is a demo support request submitted by an agent.',
    'sender_role' => 'agent',
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "CREATED|" . $id . "\n";
