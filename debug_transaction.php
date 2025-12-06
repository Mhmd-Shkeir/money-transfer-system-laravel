<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$latest = DB::table('transactions')
    ->latest('created_at')
    ->select('id', 'reference_code', 'amount_sent', 'fee', 'amount_received', 'total_paid', 'from_currency_id', 'to_currency_id', 'payout_method', 'status')
    ->first();

echo "Latest Transaction:\n";
echo json_encode($latest, JSON_PRETTY_PRINT);
