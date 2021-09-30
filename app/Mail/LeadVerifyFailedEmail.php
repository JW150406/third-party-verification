<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeadVerifyFailedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $greeting;
    public $msg;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($greeting, $msg)
    {
        $this->greeting = $greeting;
        $this->msg = $msg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Unable to Click Verification")
                ->view('emails.common')->with(['greeting' => $this->greeting,'msg' => $this->msg]);
    }
}
