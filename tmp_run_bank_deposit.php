<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

// Ensure sender exists (id=1) and beneficiary exists
$sender = App\Models\User::find(1);
if (!$sender) { echo "Sender not found"; exit(1); }
$sender->is_email_verified = 1; $sender->is_phone_verified = 1; $sender->save();

// Create a beneficiary for sender if none
$beneficiary = DB::table('beneficiaries')->where('user_id', $sender->id)->first();
if (!$beneficiary) {
    $countryId = DB::table('countries')->value('id') ?: DB::table('countries')->insertGetId(['name'=>'Testland','iso_code'=>'TST','currency_id'=>null,'region'=>'Test']);
    $bId = DB::table('beneficiaries')->insertGetId(['user_id'=>$sender->id,'name'=>'Bank Benef','email'=>'recipient.test@example.local','phone'=>'+10000000002','country_id'=>$countryId,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
    $beneficiary = DB::table('beneficiaries')->where('id',$bId)->first();
}

// Build a request
$request = Request::create('/send-money/bank-deposit','POST',[
    'beneficiary_id' => $beneficiary->id,
    'amount_sent' => 75.00,
    'card_number' => '4111111111111111',
    'expiry_month' => date('n'),
    'expiry_year' => date('Y') + 1,
    'cvv' => '123',
    'bank_name' => 'Mock Bank',
    'account_holder' => $sender->first_name . ' ' . $sender->last_name,
    'account_number' => '00012345'
]);

// Authenticate as sender
Auth::loginUsingId($sender->id);

$controller = new App\Http\Controllers\SendMoneyController();
$response = $controller->processBankDeposit($request);

// Dump latest transaction
$tx = DB::table('transactions')->orderBy('id','desc')->first();
echo json_encode($tx, JSON_PRETTY_PRINT);
