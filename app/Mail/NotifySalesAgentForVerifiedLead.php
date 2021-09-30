<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifySalesAgentForVerifiedLead extends Mailable
{
    use Queueable, SerializesModels;

    public $leadId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($leadId)
    {
        $this->leadId = $leadId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $msg = "TPV Now verified for the lead: ".$this->leadId;
        return $this
                ->subject("TPV Now - Success for the Lead ".$this->leadId)
                ->view('emails.common')->with(['msg' => $msg, 'greeting' => 'Hi,']);
    }
}
