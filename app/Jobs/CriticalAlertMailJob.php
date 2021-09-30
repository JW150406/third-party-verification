<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\CriticalLogsZipExportService;
use App\Notifications\CriticalAlert;
use Storage;
use App\User;
use Auth;
use App\models\FraudAlert;
use PDF;
use Mail;

class CriticalAlertMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $agent;
    public $lead;
    public $alerts;
    public $timeZone;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($agent=null,$lead=null,$alerts=[],$timeZone="America/Toronto")
    {
        $this->agent = $agent;
        $this->lead = $lead;
        $this->alerts = $alerts;
        $this->timeZone = $timeZone;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {        
        try{
            
            if(config('constants.IS_ENABLE_CRITICAL_ALERT_MAIL')) {
                
                // For get pdf from s3
                $objCriticalLogsZipExportService = new CriticalLogsZipExportService;
                \Log::info($this->timeZone." timezone");
                $criticalPath = $objCriticalLogsZipExportService->criticalLogsPdf($this->lead->refrence_id,$this->timeZone);
                $criticalPath = Storage::disk('s3')->url($criticalPath);

                $clients = config()->get('constants.RESTRICT_CRITICAL_EMAIL_CLIENTS');
                $clientsArr = explode(",", $clients);
                if (in_array($this->agent->client_id, $clientsArr)) {
                    \Log::info("This client is restricted for sending critical emails");
                } else {
                    \Log::info("This client is allowed to sending critical emails");

                    $clientId = $this->agent->client_id;
                    $salescenterId = $this->agent->salescenter_id;
                    $locationId = $this->agent->location_id;
                    $getEmails = FraudAlert::
                                    orWhere(function($q) use($clientId) {
                                        $q->where('alert_level', '=', 'client')
                                        ->whereRaw('FIND_IN_SET("' . $clientId . '", client_id)');
                                    })
                                    ->orWhere(function($q) use($salescenterId) {
                                        $q->where('alert_level', '=', 'salescenter')
                                        ->whereRaw('FIND_IN_SET("' . $salescenterId . '", salescenter_id)');
                                    })
                                    ->orWhere(function($q) use($locationId) {
                                        $q->where('alert_level', '=', 'sclocation')
                                        ->whereRaw('FIND_IN_SET("' . $locationId . '", location_id)');
                                    })
                                    ->whereRaw('FIND_IN_SET("fraudalert", alert_for)')
                                    ->where('added_for_client', '=', $clientId)
                                    ->whereNotNull('email')
                                    ->pluck('email');
                    \Log::info($getEmails);
                    
                    // Old Code :-
                        // $clientAdmins = User::where('client_id',$this->agent->client_id)
                        //     ->where('status','active')
                        //     ->whereHas('roles',function($query) {
                        //         $query->where('name','client_admin');
                        //     })->get();
                        // $salesCenterAdmins = User::where('salescenter_id',$this->agent->salescenter_id)
                        //     ->where('status','active')
                        //     ->whereHas('roles',function($query) {
                        //         $query->where('name','sales_center_admin');
                        //     })->get();
                        // $users = $clientAdmins->merge($salesCenterAdmins);

                    if(!empty($this->alerts) && !empty($this->lead)) {
                        
                        // Old :-
                            // \Notification::send($users, new CriticalAlert($this->agent,$this->lead,$this->alerts,$criticalPath));
                        
                        $lead = $this->lead;
                        $agent = $this->agent;
                        $status = '(current status - '.config()->get('constants.VERIFICATION_STATUS_CHART.'.ucfirst($lead->status)).')';
                        // if($lead->status =='cancel') {
                        //     $status = '(current status - cancelled):';
                        // } else {
                        //     $status = '(current status - '.$lead->status.'):';
                        // }
                        
                        $msg = "We’ve received the following alert(s) for the sales agent <b>{$agent->full_name}</b> (ID: {$agent->userid}) in regards to lead ID {$lead->refrence_id} {$status}";
                        $alerts = '<ol>';
                        foreach ($this->alerts as $key => $alert) {
                            $alerts .="<li>$alert</li>";
                        }
                        $alerts .= '</ol>';
                        $url = route('critical-logs.show', array_get($lead, 'id'));
                        $msgEnd = "To view more information regarding this lead, please click <a href='{$url}'>here</a>.";

                        $msg .= $alerts.$msgEnd;
                        \Log::info($msg);
                        
                        // For send email alert
                        foreach ($getEmails as $email) {
                            $toEmail = $email;
                            $greeting = "Hello, ";
                            Mail::send('emails.common', ['greeting' => $greeting, 'msg' => $msg], function($mail) use ($toEmail, $criticalPath) {
                                $mail->to($toEmail);
                                $mail->subject("TPV360  Fraud Alerts");
                                $mail->attach($criticalPath);  // DOM pdf 
                            });
                        }
                    }
                }
            }
        }catch(\Exception $e) {
            \Log::error($e);
        }
    }
}
