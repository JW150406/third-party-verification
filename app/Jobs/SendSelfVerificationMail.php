<?php

namespace App\Jobs;

use App\models\TextEmailStatistics;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendSelfVerificationMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to;
    protected $subject;
    protected $greeting;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $subject, $message, $greeting)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->greeting = $greeting;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $to = $this->to;
        $subject = $this->subject;
        $greeting = $this->greeting;
        $message = $this->message;

        Mail::send('emails.common', ['greeting' => $greeting, 'msg' => $message], function($mail) use ($to, $subject) {
            $mail->to($to);
            $mail->subject($subject);
        });
        $textEmailStatistics = new TextEmailStatistics();
        $textEmailStatistics->type = 1;
        $textEmailStatistics->save();
    }
}
