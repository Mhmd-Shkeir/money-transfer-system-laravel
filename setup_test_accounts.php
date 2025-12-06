<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\AgentProfile;

echo "====================================\n";
echo "  SETTING UP TEST ACCOUNTS\n";
echo "====================================\n\n";

// Create admin account
$admin = User::firstOrCreate(
    ['email' => 'admin@example.com'],
    [
        'first_name' => 'Admin',
        'last_name' => 'User',
        'password_hash' => bcrypt('password'),
        'role' => 'admin',
        'status' => 'active'
    ]
);

echo "✓ ADMIN ACCOUNT CREATED:\n";
echo "  Email:    admin@example.com\n";
echo "  Password: password\n";
echo "  Role:     admin\n";
echo "  Status:   active\n\n";

// Create test pending agents
$agents = [];
for ($i = 1; $i <= 3; $i++) {
    $user = User::create([
        'first_name' => "Test",
        'last_name' => "Agent {$i}",
        'email' => "testagent{$i}@example.com",
        'password_hash' => bcrypt('password'),
        'role' => 'user',
        'status' => 'active'
    ]);

    $profile = AgentProfile::create([
        'user_id' => $user->id,
        'is_approved' => 0,
        'store_name' => "Test Store {$i}",
        'address' => "{$i}00 Test Street, Test City",
        'commission_rate' => 2.50,
        'total_transactions' => 0
    ]);

    $agents[] = [
        'name' => "{$user->first_name} {$user->last_name}",
        'email' => $user->email,
        'store' => $profile->store_name
    ];
}

echo "✓ TEST PENDING AGENTS CREATED:\n";
foreach ($agents as $idx => $agent) {
    echo "  Agent " . ($idx + 1) . ":\n";
    echo "    Name:  {$agent['name']}\n";
    echo "    Email: {$agent['email']}\n";
    echo "    Store: {$agent['store']}\n";
}

echo "\n====================================\n";
echo "  NEXT STEPS:\n";
echo "====================================\n\n";
echo "1. Go to http://127.0.0.1:8000/login\n";
echo "2. Login with:\n";
echo "   Email:    admin@example.com\n";
echo "   Password: password\n";
echo "3. You'll see admin dashboard\n";
echo "4. Go to http://127.0.0.1:8000/admin/dashboard\n";
echo "5. Click APPROVE or REJECT on any pending agent\n";
echo "6. See the agent's status change and card disappear\n";
echo "\n✓ READY TO TEST!\n";
