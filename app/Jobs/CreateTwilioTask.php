<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\models\TwilioOutboundCalls;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use App\Services\TwilioService;
use App\models\TelesaleScheduleCall;
use App\models\Telesales;
use App\models\ClientTwilioNumbers;
use Auth;
use App\models\ClientWorkflow;
use App\Traits\ScheduleCallTrait;
use App\Traits\LeadDataTrait;
use App\Traits\TwilioTrait;

class CreateTwilioTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ScheduleCallTrait, LeadDataTrait, TwilioTrait;

    public $scheduleCallId;
    public $client_id;
    public $language;
    private $twilio_client   = array();
    private $sid = "";
    private $token = "";
    private $fromNumber = "";
    public $twilioService;
    public $lead_id;
    public $lId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sid,$cid,$language,$leadId, $lId)
    {
        $this->scheduleCallId = $sid;
        $this->client_id = $cid;
        $this->language = $language;
        $this->sid = config('services.twilio')['accountSid'];
        $this->token = config('services.twilio')['authToken'];
        // $this->fromNumber = config('services.twilio')['number'];
        $this->fromNumber = $this->getClientNumberDetails($cid);
        $this->twilio_client = new Client($this->sid, $this->token);
        $this->twilioService = new TwilioService;
        $this->lead_id = $leadId;
        $this->lId = $lId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //create task api of twilio
        $toNumber = (new ClientTwilioNumbers)->getNumber($this->client_id);
        $toNumber = $toNumber->phonenumber;
        $clientWorkflowIds = (new ClientWorkflow )->getClientWorkflowIds($this->client_id);
        $workflowId= $clientWorkflowIds[0]->workflow_id;
        $workspaceId= $clientWorkflowIds[0]->workspace_id;

//        $workspaceId = "WS9521c0eb8afb27006db628d8e16145b5";
//        $workflowId = "WW156202e1ede3b241980f7060c38807fa";

        //Retrieve Customer's phone number
        $custNum = $this->getPhoneNumber($this->lId);

        //retrive lead status
        $leadStatus = Telesales::find($this->lId);
        if (empty($custNum)) {
            \Log::error("Can not schedule a call as customer's number not found for lead with id: " . $this->lId);
            return false;
        }

        $fromNumber = config('services.twilio')['number'];
        if (!empty($this->fromNumber)) {
            $fromNumber = array_get($this->fromNumber, 'phonenumber', config('services.twilio')['number']);
        }
        //Prepare an array for twilio outbound task
        $toData = $this->outboundTwilioTaskData($workflowId, $this->language, $toNumber, $fromNumber, $this->lId, $custNum,$leadStatus->status);

        if ($toData == false) {
            \Log::error("Outbound call task data not available");
            return false;
        } else {
            \Log::debug(print_r($toData, true));
        }

        //Make an service call to create task on Twilio
        $task = $this->twilioService->createTask($workspaceId, $toData);

        if($task != null)
        {
            \Log::info("Task created on twilio for lead with id: " . $this->lead_id);
            $task_queue_real_time_statistics = $this->twilio_client->taskrouter->v1->workspaces($workspaceId)
            ->taskQueues($task->taskQueueSid)
            ->statistics()
            ->fetch();

            $data['task_id'] = $task->sid;
            $data['schedule_status'] = config()->get('constants.SCHEDULE_TASK_CREATED_STATUS');
            (new TelesaleScheduleCall)->updateValue($data,$this->scheduleCallId);
        }
        else
        {
            \Log::error("Can not create task on twilio");
        }
    }


}
