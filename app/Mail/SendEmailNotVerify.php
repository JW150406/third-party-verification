<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailNotVerify extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $leadId;
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
        \Log::debug("mail block");
        $msg = "TPV Now not verified for the lead: ".$this->leadId;
        return $this
                ->subject("TPV Now - Failed for the Lead ".$this->leadId)
                ->view('emails.common')->with(['msg' => $msg,'greeting' => 'Hi,']);

    }
}
