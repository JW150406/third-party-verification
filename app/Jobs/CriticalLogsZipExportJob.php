<?php

namespace App\Jobs;

use App\models\TextEmailStatistics;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use App\Services\CriticalLogsZipExportService;
use Mail;

class CriticalLogsZipExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $userEmail;
    public $firstName;
    public $timeZone;

    /**
    * The number of seconds the job can run before timing out.
    *
    * @var int
     */
    public $timeout = 7200; // 2 hours

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $userEmail,$firstName,$timeZone)
    {
        $this->data = $data;
        $this->userEmail = $userEmail;
        $this->firstName = $firstName;
        $this->timeZone = $timeZone;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $objCriticalLogsZipExportService = new CriticalLogsZipExportService;
        $tmpFile = $objCriticalLogsZipExportService->exportReport($this->data);
        $subject = "Critical Alert Report";
        $greeting = "Hello ".$this->firstName.",";
        $mainMessage = "Please find the requested export of the critical alert report attached.";
        $toEmail = $this->userEmail;
        if ($this->userEmail != "") {
            $mail = Mail::send('emails.common', ['greeting' => $greeting, 'msg' => $mainMessage], function ($message) use ($subject, $toEmail, $tmpFile) {
                $message->attach($tmpFile, [
                    'as' => 'Critical_alert_report_'.date('d_M_Y_H_i_A') . '.zip',
                    'mime' => 'application/zip',
                ]);
                $message->to($toEmail)
                    ->subject($subject);
            });

            $textEmailStatistics = new TextEmailStatistics();
            $textEmailStatistics->type = 1;
            $textEmailStatistics->save();
            \Log::info("Critical log mail sent to email: " . $toEmail);
            unlink($tmpFile);
        } else {
            \Log::error("To mail isnot defined !!");
        }
    }
}
