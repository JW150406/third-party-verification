<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TwilioService;
use App\models\ClientWorkspace;
use App\User;
use App\Traits\TwilioTrait;

class MakeAgentOfflineCommand extends Command
{
    use TwilioTrait;
    public $twilioService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:offline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command retrieves all the available twilio worker & check it there last activity is null or not set then make its related agnet offline';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->twilioService = new TwilioService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Retrieve workspace details
        $workspaceDetails = $this->getWorkspaceDetails();

        // Check if workspace object is empty then end a command to execute
        if (empty($workspaceDetails)) {
            \Log::error("No twilio workspace available for system.");
            return false;
        }

        // Check if workspace id column id null or not
        if (empty($workspaceDetails->workspace_id)) {
            \Log::error("No twilio workspace id found for workspace with id: " . array_get($workspaceDetails, 'id'));
            return false;
        }

        // Call function to retrieve offline activity id
        $activitySid = $this->retrieveOfflineActivityId($workspaceDetails);

        // Check if activity id is null then stop command execution
        if (empty($activitySid)) {
            \Log::error("No activity sid found for available activity from workspace with id: " . array_get($workspaceDetails, 'id'));
            return false;
        }

        // Call function retrieve available workers
        $availableWorkers = $this->retrieveAvailableWorkers($workspaceDetails);

        // Check if any workers found with available activity then continue otherwise stop command execution
        if (count($availableWorkers) > 0) {
            foreach ($availableWorkers as $worker) {

                // Retrieve user with passed twilio id & where last activity is not updates
                $user = User::select('users.id')->join('user_twilio_id', 'users.id', 'user_twilio_id.user_id')->where('user_twilio_id.twilio_id', $worker->sid)
                            ->where(function($q) {
                                $q->whereNull('users.last_activity')
                                    ->orWhere('users.last_activity', "")
                                    ->orWhere('users.last_activity', "0000-00-00 00:00:00");
                            })
                            ->first();

                // Check if user array is empty then continue iterate loop other wise update worker to offline state on twilio
                if (empty($user)) {
                    continue;
                } else {
                    // Call function to make twilio worker offline
                    $updateWorker = $this->updateWorkerToTwilio($workspaceDetails, $activitySid, $worker->sid);
                    if (!empty($updateWorker)) {
                        \Log::info("Worker updated to offline with user id: " . $user->id);
                    } else {
                        \Log::error("Unable to update worker on twilio with user id: " . $user->id);
                    }
                }
            }
        } else {
            \Log::error("Any online agent is not found for workspace with id: " . array_get($workspaceDetails, 'id'));
            return false;
        }
    }

    /**
     * This function is used to retrieve offline activity id
     * @param $workspaceDetails
     */
    public function retrieveOfflineActivityId($workspaceDetails) {
        // Prepare activity details array for Available activity
        $toActivitiesData = [];
        $toActivitiesData['friendlyName'] = "Offline";

        // Retrieve activities from twilio by making an API call to twilio
        $activities = $this->twilioService->retrieveActivities(array_get($workspaceDetails, 'workspace_id'), $toActivitiesData);

        // Retrieve activity id from twilio response
        $activitySid = "";
        foreach ($activities as $activity) {
            $activitySid = $activity->sid;
        }
        return $activitySid;
    }

    /**
     * This Funtion is used to retrieve available workers from twilio
     */
    public function retrieveAvailableWorkers($workspaceDetails) {
        // Prepare workers array to retrieve
        $toWorkersData = [];
        $toWorkersData['activityName'] = "Available";

        // Retrieve available workers by making an API call to twilio
        return $this->twilioService->workers(array_get($workspaceDetails, 'workspace_id'), $toWorkersData);
    }

    /**
     * This Function is used to update worker on twilio
     */
    public function updateWorkerToTwilio($workspaceDetails, $activitySid, $workerId) {
        // Prepare array to make agent offline
        $updateWorkerArr = [];
        $updateWorkerArr['activitySid'] = $activitySid;

        // Make an API call to twilio and update worker state
        return $this->twilioService->updateWorker(array_get($workspaceDetails, 'workspace_id'), $workerId, $updateWorkerArr);
    }
}
