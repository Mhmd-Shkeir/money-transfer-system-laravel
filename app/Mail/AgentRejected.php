<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $profile;

    public function __construct($user, $profile = null)
    {
        $this->user = $user;
        $this->profile = $profile;
    }

    public function build()
    {
        return $this->subject('Your agent application has been rejected')
                    ->view('emails.agent_rejected');
    }
}
