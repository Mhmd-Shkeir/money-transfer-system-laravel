<?php

namespace App\Http\ViewModels;

class BankDepositViewModel
{
    public $beneficiary_id;
    public $amount_sent;
    public $bank_name;
    public $account_holder;
    public $account_number;

    // Card details
    public $card_number;
    public $expiry_month;
    public $expiry_year;
    public $cvv;
}
