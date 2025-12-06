<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransferAssignedToAgent extends Mailable
{
    use Queueable, SerializesModels;

    public $agent;
    public $transaction;

    public function __construct($agent, $transaction)
    {
        $this->agent = $agent;
        $this->transaction = $transaction;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Transfer Request Assigned - ' . ($this->transaction->reference_code ?? 'Ref Pending'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transfer-assigned-to-agent',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
