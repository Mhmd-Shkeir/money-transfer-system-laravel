<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\AgentProfile;

// Create test user
$user = User::firstOrCreate(
    ['email' => 'testagent@example.com'],
    [
        'first_name' => 'Test',
        'last_name' => 'Agent',
        'password_hash' => bcrypt('password'),
        'role' => 'user',
        'status' => 'active'
    ]
);

// Create pending agent profile
$profile = AgentProfile::firstOrCreate(
    ['user_id' => $user->id],
    [
        'is_approved' => 0,  // 0 = pending, 1 = approved, -1 = rejected
        'store_name' => 'Test Agent Store',
        'address' => '123 Test Street, Test City',
        'commission_rate' => 2.50,
        'total_transactions' => 0
    ]
);

echo "✓ Created/verified test agent:\n";
echo "  User ID: {$user->id}\n";
echo "  User Email: {$user->email}\n";
echo "  Agent Profile ID: {$profile->id}\n";
echo "  Is Approved: " . ($profile->is_approved === 0 ? 'pending (0)' : $profile->is_approved) . "\n";
echo "\nNow visit http://127.0.0.1:8000/admin/dashboard and click Approve/Reject\n";
