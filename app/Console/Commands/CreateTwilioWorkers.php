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

class CreateTwilioWorkers extends Command
{
    use TwilioTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio-worker:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command create twilio worker for existing tpv agents.';

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

        $csvData = [];
        foreach ($users as $user) {
            $csvDataArr = [];
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
                    $isExist = true;
                    \Log::debug("1.2: Retrive worker api call succes");
                } catch (\Exception $e) {
                    $isExist = false;
                    \Log::debug("1.2: Retrive worker api call fail");
                }
            } else {
                \Log::debug("1.1: else blank twilio id");
                $isExist = false;
            }
            
            // If user available on twilio then updates its workflow to twilio
            $csvDataArr['prev_sel_workflows'] = NULL;
            if ($isExist) {
                \Log::debug("1.3: worker exists to twilio");
                \Log::debug("attrs: " . print_r($response->attributes, true));
                $attributes = json_decode($response->attributes, true);
                if (!empty($attributes)) {
                    \Log::debug("1.4: worker have attributes");
                    \Log::debug("1.5: Attributes: " . print_r($attributes, true));
                    if (array_get($attributes, 'selected_workflow')) {
                        $csvDataArr['prev_sel_workflows'] = array_get($attributes, 'selected_workflow');
                        if (array_diff(array_get($attributes, 'selected_workflow'), explode(",", $user->workflow_ids))) {
                            \Log::debug("1.7: set attributes true");
                            $setAttributes = true;
                        } else {
                            \Log::debug("1.7: set attributes false");
                            $setAttributes = false;
                        }
                    } else {
                        \Log::debug("1.6: selected workflow not found");
                        $setAttributes = true;
                    }
                } else {
                    \Log::debug("1.3: worker isn't have attributes");
                    $setAttributes = true;
                }
                
                if ($setAttributes) {
                    $attributes = [];
                    $attributes['attributes'] = json_encode(array('selected_workflow' => explode(",", $user->workflow_ids)));
                    $res = $this->twilioService->updateWorker($workspace->workspace_id, $user->twilio_id, $attributes);

                    if ($res) {
                        $csvDataArr['new_sel_workflows'] = json_encode(explode(",", array_get($user, 'workflow_ids')));
                        \Log::info("Updated workflow to twilio attributes for user id: " . $user->id);
                    } else {
                        $csvDataArr['new_sel_workflows'] = NULL;
                        \Log::error("Unable to Update workflow to twilio attributes for user id: " . $user->id);
                    }
                } else {
                    \Log::error("Set attributes flag is set to false for user id: " . $user->id);
                }
            } else {
                \Log::debug("1.3 New twilio worker created");
                // Prepare array for workers data
                $toWorker = [];
                $toWorker['friendlyName'] = $user->first_name . ' ' . $user->last_name . ' ('. $user->userid.')';
                        
                // Create worker on twilio
                $worker = $this->twilioService->createWorker($workspace->workspace_id, $toWorker);
                
                // Check response of worker resource is created or not on twilio
                if (!empty($worker)) {
                    $csvDataArr['new_twilio_id'] = $worker->sid;
                    $selWorkflows = explode(",", array_get($user, 'workflow_ids'));
                    //Update worker's attributes to twilio
                    $toUpdateWorker = [];
                    $toUpdateWorker['attributes'] = json_encode(array('selected_workflow' => $selWorkflows));
                    $res = $this->twilioService->updateWorker($workspace->workspace_id, $worker->sid, $toUpdateWorker);
                    if ($res) {
                        $csvDataArr['new_sel_workflows'] = json_encode(explode(",", array_get($user, 'workflow_ids')));
                        UserTwilioId::where('user_id', $user->id)
                            ->whereIn('workflow_id', $selWorkflows)
                            ->update([
                                'twilio_id' => $worker->sid
                            ]);
                    } else {
                        $csvDataArr['new_sel_workflows'] = NULL;
                        \Log::error("Unable to update worker attributes on twilio for user with id: " . $user->id);
                    }
                } else {
                    $csvDataArr['new_twilio_id'] = NULL;
                    \Log::error("Unable to create worker on twilio for user with id: " . $user->id);
                }
            }
            $csvData[] = $csvDataArr;
        }
        self::exportWorkers($csvData);
    }

    /**
     * This method is used to Export & Store updated workers data
     * @param $csvData
     */
    private function exportWorkers($csvData) {
        \Log::debug("Export workers: " . print_r($csvData, true));
        $fileName = 'twilio-workers-' . date('d_M_Y_H_i_A') . ".xls";
        $file = Excel::create($fileName, function($excel) use ($csvData) {
            $excel->sheet('sheet1', function($sheet) use ($csvData)
            {
                $sheet->fromArray($csvData);
            });
        })->string('xls');

        self::uploadWorkersFile($file, $fileName);
    }

    /**
     * This method is used for Upload file to local dir
     * @param $file, $fileName
     */
    private function uploadWorkersFile($file, $fileName) {
        try {
            $storage = Storage::disk('local');
            $imgPath = "/twilio-workers/";
            if (!$storage->exists($imgPath)) {
                $storage->makeDirectory($imgPath);
            }

            $filePath = $imgPath . $fileName;
            Storage::disk('local')->put($filePath, $file, 'public');
            \Log::info("Workers file uploaded. File path: /storage" . $filePath);
        } catch (\Exception $e) {
            \Log::error("Error while storing workers file: " . $e->getMessage());
        }
    }
}


