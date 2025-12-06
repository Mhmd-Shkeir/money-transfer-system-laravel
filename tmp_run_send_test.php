<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Models
$User = App\Models\User::class;
$Beneficiary = App\Models\Beneficiary::class;
$Transaction = App\Models\Transaction::class;

try {
    // Ensure a country exists
    $countryId = DB::table('countries')->value('id');
    if (!$countryId) {
        $countryId = DB::table('countries')->insertGetId([
            'name' => 'Testland',
            'iso_code' => 'TST',
            'currency_id' => null,
            'region' => 'Test'
        ]);
    }

    // Create verified recipient user
    $recipientEmail = 'recipient.test@example.local';
    $recipientPhone = '+10000000002';
    $recipient = App\Models\User::where('email', $recipientEmail)->first();
    if (!$recipient) {
        $recipient = App\Models\User::create([
            'first_name' => 'Recipient',
            'last_name' => 'Test',
            'email' => $recipientEmail,
            'password_hash' => Hash::make('Secret1!'),
            'role' => 'user',
            'status' => 'active',
            'is_email_verified' => 1,
            'is_phone_verified' => 1,
            'phone' => $recipientPhone,
        ]);
    } else {
        $recipient->is_email_verified = 1;
        $recipient->is_phone_verified = 1;
        $recipient->phone = $recipientPhone;
        $recipient->save();
    }

    // Fetch sender (assume id=1 exists)
    $sender = App\Models\User::find(1);
    if (!$sender) {
        echo json_encode(['error' => 'Sender user with id=1 not found']);
        exit(1);
    }

    // Mark sender verified so mobile_wallet allowed
    $sender->is_email_verified = 1;
    $sender->is_phone_verified = 1;
    $sender->save();

    // Create beneficiary for sender pointing to recipient
    $beneficiary = App\Models\Beneficiary::create([
        'user_id' => $sender->id,
        'name' => 'Recipient Benef',
        'email' => $recipient->email,
        'phone' => $recipient->phone,
        'country_id' => $countryId,
    ]);

    // Prepare a Request to simulate form submission
    $request = Request::create('/send-money', 'POST', [
        'amount_sent' => 150.00,
        'beneficiary_id' => $beneficiary->id,
        'payout_method' => 'mobile_wallet',
    ]);

    // Authenticate as sender
    Auth::loginUsingId($sender->id);

    // Call controller
    $controller = new App\Http\Controllers\SendMoneyController();
    $response = $controller->store($request);

    // Fetch recent transactions and beneficiaries
    $recentTx = DB::table('transactions')->orderBy('id', 'desc')->limit(10)->get();
    $allBeneficiaries = DB::table('beneficiaries')->orderBy('id','desc')->limit(10)->get();

    // Compute wallet balances per dashboard logic
    // For sender
    $incomingSender = DB::table('transactions')
        ->join('beneficiaries','transactions.beneficiary_id','=','beneficiaries.id')
        ->where('transactions.status','completed')
        ->where(function($q) use ($sender) {
            $q->where('beneficiaries.email', $sender->email)
              ->orWhere('beneficiaries.phone', $sender->phone);
        })->sum('transactions.amount_received');
    $outgoingSender = DB::table('transactions')
        ->where('transactions.status','completed')
        ->where('transactions.sender_id', $sender->id)
        ->sum('transactions.total_paid');
    $walletSender = $incomingSender - $outgoingSender;

    // For recipient (by matching beneficiary email/phone)
    $incomingRecipient = DB::table('transactions')
        ->join('beneficiaries','transactions.beneficiary_id','=','beneficiaries.id')
        ->where('transactions.status','completed')
        ->where(function($q) use ($recipient) {
            $q->where('beneficiaries.email', $recipient->email)
              ->orWhere('beneficiaries.phone', $recipient->phone);
        })->sum('transactions.amount_received');
    $outgoingRecipient = DB::table('transactions')
        ->where('transactions.status','completed')
        ->where('transactions.sender_id', $recipient->id)
        ->sum('transactions.total_paid');
    $walletRecipient = $incomingRecipient - $outgoingRecipient;

    $output = [
        'recipient' => $recipient,
        'sender' => $sender,
        'beneficiary' => $beneficiary,
        'recent_transactions' => $recentTx,
        'recent_beneficiaries' => $allBeneficiaries,
        'wallet_sender' => $walletSender,
        'wallet_recipient' => $walletRecipient,
    ];

    echo json_encode($output, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
}
