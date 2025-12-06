<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use DB facade with fully-qualified name
$countries = \Illuminate\Support\Facades\DB::table('countries')->get();
$beneficiaries = \Illuminate\Support\Facades\DB::table('beneficiaries')->orderBy('id','desc')->limit(10)->get();
$users = \Illuminate\Support\Facades\DB::table('users')->orderBy('id','desc')->limit(10)->get();
$transactions = \Illuminate\Support\Facades\DB::table('transactions')->orderBy('id','desc')->limit(10)->get();

$output = [
    'countries' => $countries,
    'recent_beneficiaries' => $beneficiaries,
    'recent_users' => $users,
    'recent_transactions' => $transactions,
];

echo json_encode($output, JSON_PRETTY_PRINT);
