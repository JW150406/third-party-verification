<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\TelesaleScheduleCall;
use App\models\Telesales;
use Carbon\Carbon;
use App\models\ClientTwilioNumbers;
use App\models\ClientWorkflow;
use App\Services\TwilioService;
use Mail;
use App\Mail\SendEmailNotVerify;
use App\models\ClientWorkspace;
use App\Traits\ScheduleCallTrait;
use App\Traits\LeadDataTrait;
use App\Traits\TwilioTrait;

class RescheduleTaskCommand extends Command
{
    use ScheduleCallTrait, LeadDataTrait, TwilioTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reschedule:tasks';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command create task of scheduled calls on twilio';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $current = Carbon::now();
        $twilioService = new TwilioService;
        $scheduledCall = TelesaleScheduleCall::select('telesale_schedule_call.*', 'telesales.client_id', 'telesales.refrence_id', 'telesales.status As lead_status')->join('telesales', 'telesales.id', 'telesale_schedule_call.telesale_id')
            ->where('telesale_schedule_call.call_time', '<', $current)
            ->where('telesale_schedule_call.schedule_status', 'pending')
            ->get();

        $clients = [];

        foreach ($scheduledCall as $key => $val) {
            // Check for lead status if lead is pending / disconnect or self verified then creating a task on twilio other wise skip an iteration
            if (!empty($val->lead_status) && in_array($val->lead_status, [config()->get('constants.LEAD_TYPE_PENDING'), config()->get('constants.LEAD_TYPE_DISCONNECTED'), config()->get('constants.LEAD_STATUS_SELF_VERIFIED')])) {
              if (!isset($val->client_id) && $val->client_id == "") {
                \Log::error("No Client available for lead with id: " . $scheduledCall->telesale_id);
              }

              $clientId = $val->client_id;
              $phoneNumber = "";
              $workflow = "";

              $currentClient = [];

              // Check if current client is stored in client's array
              if (!empty($clients) && in_array($clientId, array_keys($clients))) {
                  $phoneNumber = $clients[$clientId]['phone_number'];
                  $workflow = $clients[$clientId]['workflow_id'];
                  $workspace = $clients[$clientId]['workspace_id'];
              } else {
                  // Query to retrieve client inbound number data
                  $numberData = ClientTwilioNumbers::where('client_id', $clientId)->where('type', config()->get('constants.TWILIO_PHONE_NUMBER_TYPE.CUSTOMER_INBOUND_NUMBER'))->first();
                  if (empty($numberData) || array_get($numberData, 'phonenumber') == "") {
                      \Log::error("No customer inbound number found for client with id: " . array_get($val, 'client_id'));
                      continue;
                  } else {
                      $phoneNumber = array_get($numberData, 'phonenumber');
                      $currentClient['phone_number'] = array_get($numberData, 'phonenumber');
                  }

                  // Query to retrieve client workspace data
                  $workSpaceData = ClientWorkspace::where('client_id', $clientId)->first();
                  if (empty($workSpaceData) && array_get($workSpaceData, 'workspace_id') == "") {
                      \Log::error("No Workspace found for client with id: " . $clientId);
                      continue;
                  } else {
                      $workspace = array_get($workSpaceData, 'workspace_id');
                      $currentClient['workspace_id'] = array_get($workSpaceData, 'workspace_id');
                  }

                  // Query to retrieve client workflow data
                  $workflowData = ClientWorkflow::where('client_id', $clientId)->where('workspace_id', array_get($workSpaceData, 'workspace_id'))->first();
                  if (empty($workflowData) && array_get($workflowData, 'workflow_id') == "") {
                      \Log::error("No Workflow found for client with id: " . $clientId);
                      continue;
                  } else {
                      $workflow = array_get($workflowData, 'workflow_id');
                      $currentClient['workflow_id'] = array_get($workflowData, 'workflow_id');
                  }
              }

              // Check if required params for creating a task are empty or not
              if (empty($phoneNumber) || empty($workflow) || empty($workspace)) {
                  \Log::error("Phone number or workspace or workflow have null value for call with id: " . $val->id);
              } else {
                //   $fromNumber = config('services.twilio')['number'];

                $fromNumberData = $this->getClientNumberDetails($clientId);
                
                // Retrieve Customer's phone number
                  $custNum = $this->getPhoneNumber($val->telesale_id);
                
                  \Log::debug("Given phone number for lead: " . $custNum);
                  
                  if (empty($custNum)) {
                    //   \Log::error("Can not schedule a call as customer's number not found for lead with id: " . $this->lId);
                        continue;
                  }

                  if (!empty($fromNumberData)) {
                    $fromNumber = array_get($fromNumberData, 'phonenumber', config('services.twilio')['number']);
                  } else {
                    $fromNumber = config('services.twilio')['number'];
                  }

                  // Prepare an array for twilio outbound task
                  $toData = $this->outboundTwilioTaskData($workflow, array_get($val, 'call_lang'), $phoneNumber, $fromNumber, $val->telesale_id, $custNum, array_get($val, 'lead_status'));

                  if ($toData == false) {
                      \Log::error("Outbound call task data not available");
                      continue;
                  } else {
                      \Log::debug(print_r($toData, true));
                  }

                  // Make an service call to create task on Twilio
                  $task = $twilioService->createTask($workspace, $toData);

                  \Log::debug("Created Task: " . print_r($task, true));
                  
                  if (!empty($task)) {

                      // Updating task details & status for scheduled call
                      $callUpdated = TelesaleScheduleCall::where('id', $val->id)->update([
                          'task_id' => $task->sid,
                          'schedule_status' => config()->get('constants.SCHEDULE_TASK_CREATED_STATUS')
                      ]);

                      if ($callUpdated) {
                          \Log::info("Schedule call task created on twilio and details updated with id: " . $val->id);
                      } else {
                          \Log::error("Unable to update schedule call with id: " . $val->id);
                      }
                  } else {
                      \Log::error("Unable to create task on twilio for call with id: " . $val->id);
                  }
              }
            } else {
              \Log::error("Can not create task for this lead because lead doesn't have status pending, disconnected or self verified for call with id: " . $val->id);
              // Update current schedule call's status to skip
              $callUpdated = TelesaleScheduleCall::where('id', $val->id)->update([
                  'schedule_status' => config()->get('constants.SCHEDULE_TASK_SKIP_STATUS')
              ]);

              if ($callUpdated) {
                \Log::info("Schedule status updated for call with id: " . $val->id);
              } else {
                \Log::error("Unable to update schedule status for call with id: " . $val->id);
              }
            }
        }
      }
  }
