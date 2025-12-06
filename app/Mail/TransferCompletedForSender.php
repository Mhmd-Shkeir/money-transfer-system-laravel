<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransferCompletedForSender extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $transaction;
    public $agent;

    public function __construct($user, $transaction, $agent = null)
    {
        $this->user = $user;
        $this->transaction = $transaction;
        $this->agent = $agent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Transfer Completed - ' . ($this->transaction->reference_code ?? 'Ref Pending'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transfer-completed-for-sender',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
