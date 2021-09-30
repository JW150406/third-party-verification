<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;
use App\models\TextEmailStatistics;

class SendDispositionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $emailArr, $disposition, $lead;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emailArr, $disposition, $lead)
    {
        $this->emailArr = $emailArr;
        $this->disposition = $disposition;
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $subject = 'Disposition Alert';
        $greeting ='Hello,';
        $status = '';
        switch ($this->lead->status) {
            case 'cancel':
                $status = 'cancelled';
                break;
            case 'decline':
                $status = 'declined';
                break;
            case 'hangup':
                $status = 'disconnected';
                break;
            default:
                $status = $this->lead->status;
                break;
        }
        $message = "A lead with ID ".$this->lead->refrence_id." has been $status with the disposition '" . array_get($this->disposition, 'description')."'.";
        $message .= "<br>To view more information regarding this lead, please click <a target='_blank' href='".route("telesales.show",$this->lead->id)."'>here</a>.";
        
        foreach ($this->emailArr as $toEmail) {
            Mail::send('emails.common', ['greeting' => $greeting, 'msg' => $message], function($mail) use ($toEmail, $subject) {
                $mail->to($toEmail);
                $mail->subject($subject);
            });

            if (!Mail::failures()) {
                $textEmailStatistics = new TextEmailStatistics();
                $textEmailStatistics->type = 1;
                $textEmailStatistics->save();
                \Log::info("Disposition mail sent on email address: " . $toEmail);
            } else {
                \Log::error("Unable to send disposition mail on email address: " . $toEmail);
            }
        }
        
    }
}
