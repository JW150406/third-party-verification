<?php

namespace App\Http\Controllers\AgentPanel\TPVAgent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Telesales;
use App\Traits\ScheduleCallTrait;

class ScheduleCallController extends Controller
{
    use ScheduleCallTrait;

    /**
     * This method is used for reschedule call for particular lead as per requested data
     */
    public function rescheduleTask(Request $request) {
      try {
        $rules = [
          "referenceId" => "required"
        ];

        $this->validateJsonResponse($request, $rules);
        $leadData = (new Telesales())->getLeadID($request->get('referenceId'));
        $rescheduleCall = $this->rescheduleCall($leadData->id);

        if ($rescheduleCall !== false) {
          
          \Log::info("Call has been reschedule for lead: " . $request->get('referenceId'));
          return response()->json([
            'status' => 'success',
            'message' => 'Your call has been resheduled for next attempt'
          ]);
        } else {
          \Log::error("Unable to reschedule a call for lead: " . $request->get('referenceId'));
          return response()->json([
            'status' => 'error',
            'message' => 'Unable to reschedule your call'
          ]);
        }
      } catch (\Exception $e) {
        \Log::error("Error while resheduling a call for lead with id " . $request->get('referenceId') . ": " . $e->getMessage());
        return response()->json([
          'status' => 'error',
          'message' => 'Unable to reschedule your call'
        ], 500);
      }
    }
}
