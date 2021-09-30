<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\TwilioTrait;
use App\Services\TwilioService;
use App\models\ClientWorkflow;
use App\models\UserTwilioId;
use App\User;
use Storage;
use Maatwebsite\Excel\Facades\Excel;

class UpdateTwilioWorkersLastCallTime extends Command
{
    use TwilioTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio-worker:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command update twilio worker for existing tpv agents last call time. Command is build to run once';

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
        $workspace = $this->getWorkspaceDetails();

        // Stop command execution if unable to find workspace
        if (empty($workspace)) {
            \Log::error('No twilio workspace is available for this system.');
            return false;
        }
       
        $users = User::select('users.*', 'user_twilio_id.twilio_id', \DB::raw('GROUP_CONCAT(user_twilio_id.workflow_id) As workflow_ids'))->where('users.access_level', config()->get('constants.TPVAGENT_ACCESS_LEVEL'))
                ->join('user_twilio_id', 'users.id', '=', 'user_twilio_id.user_id')
                ->groupBy('users.id')
                ->get();

        foreach ($users as $user) {
            $csvDataArr['user_id'] = $user->id;
            $csvDataArr['prev_twilio_id'] = array_get($user, 'twilio_id');
            
            if (empty($user->workflow_ids)) {
                \Log::info("No workflow assigned to user id: " . $user->id);
                continue;
            }

            if (isset($user->twilio_id) && !empty($user->twilio_id)) { 
                \Log::debug("1.1: Have twilio id");
                // Make twilio api call to check whether user available on twilio or not
                try {
                    $response = $this->twilioService->getWorker($workspace->workspace_id, $user->twilio_id);

                    \Log::info("Worker Sid: ".$response->sid);

                    $attributes = json_decode($response->attributes, true);
                    \Log::info("Previous worker attributes: ".print_r($attributes,true));
                    $attributes['last_call_time'] = now()->timestamp;
                    $toUpdateWorker['attributes'] = json_encode($attributes);
                    \Log::info("New worker attributes: ".print_r($attributes,true));
                    $updateworker = $this->twilioService->updateWorker($workspace->workspace_id,$response->sid,$toUpdateWorker);
                    \Log::debug("1.2: Retrive worker api call succes");
                } catch (\Exception $e) {
                    $isExist = false;
                    \Log::debug("1.2: Retrive worker api call fail");
                }
            } else {
                \Log::debug("1.1: else blank twilio id");
                $isExist = false;
            }
        }
    }
}


