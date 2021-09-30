<?php

namespace App\Http\Controllers\AgentPanel\TPVAgent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Telesales;
use App\models\TwilioLeadCallDetails;
use App\models\Dispositions;
use App\models\Client;
use App\Traits\LeadTrait;
use Log;

class CallController extends Controller
{
    use LeadTrait;

    public function __construct() {

    }

    
    /**
     * For Store Verified lead disposition for tpv agent panel once call end
     */
    public function storeReason(Request $request) {
        try {
            $rules = [
                'referenceId' => 'required',
                'dispositionId' => 'required'
            ];

            $this->validateJsonResponse($request, $rules);

            $lead = Telesales::where('refrence_id', $request->get('referenceId'))->first();

            if (empty($lead)) {
                $this->error('error', 'Lead not found', 400);
            }

            $client = Client::find(array_get($lead, 'client_id'));

            if (empty($client)) {
                $this->error('error', 'Client not found', 400);
            }

            $disposition = Dispositions::find($request->get('dispositionId'));

            if (empty($disposition)) {
                $this->error('error', 'Disposition not found', 400);
            }

            $lead->update([
                'disposition_id' => $request->get('dispositionId')
            ]);

            //check for child leads is exist or not
            $isChildExist = (new Telesales())->getChildLeads($lead->id);
            
            if(isset($isChildExist) && $isChildExist->count() > 0){
                //update child leads 
                foreach($isChildExist as $key => $val){
                    $val->update([
                        'disposition_id' => $request->get('dispositionId')
                    ]);
                    \Log::info('Child lead details are successfully updated with lead id '.$val->id);
                }
            }

            \Log::info("Task id ::".$request->taskId);
            //Store lead status and verified disposition id in twilio lead call details table
            $twilioCalls = TwilioLeadCallDetails::where('task_id',$request->taskId)->first();
            if(!empty($twilioCalls)){
                $twilioCalls->lead_status = $lead->status;
                $twilioCalls->disposition_id = $request->get('dispositionId');
                $twilioCalls->save();
            }

            \Log::info('lead status and disposition successfully updated for twilio call details table');
            
            if (array_get($disposition, 'email_alert') == 1) {
                $this->sendDispositionMail($disposition, $lead);
            }

            Log::info("Verification reason stored for lead with id: " . array_get($lead, 'id'));
            return $this->success('success', 'Verified disposition stored.');
     
        } catch (\Exception $e) {
            Log::error("Error while storing verified disposition: " . $e->getMessage());
            return $this->error('error', 'Unable to store verified disposition', 400);
        }
    }
}
