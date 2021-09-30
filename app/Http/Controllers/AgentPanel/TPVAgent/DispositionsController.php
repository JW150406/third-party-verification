<?php

namespace App\Http\Controllers\AgentPanel\TPVAgent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Telesales;
use App\models\Dispositions;
use App\models\Client;

class DispositionsController extends Controller
{
    public function __construct() {

    }

    /**
     * For get dispositions details of particular lead as per requested data
     */
    public function dispositions(Request $request) {
      $rules = [
        'disType' => 'required',
        'referenceId' => 'required' ];

      $this->validateJsonResponse($request, $rules);

      $lead = Telesales::where('refrence_id', $request->get('referenceId'))->first();
      if (empty($lead)) {
        $this->error('error', 'Lead not found', 400);
      }

      $client = Client::find(array_get($lead, 'client_id'));

      if (empty($client)) {
        $this->error('error', 'Client not found', 400);
      }

      if ($request->has('disType') && $request->get('disType') != "" && $request->has('referenceId') && $request->get('referenceId') != "") {
        $dispositions = Dispositions::where('client_id', array_get($client, 'id'))->where('type', $request->get('disType'))->where('status', 'active')->get();
        $data = [];
        $data['totalReasons'] = count($dispositions);
        if ($request->get('disType') == "customerhangup") {
            $data['view'] = view('frontend.tpvagent.disconnected-reasons', compact('dispositions'))->render();
        } else if ($request->get('disType') == "decline") {
            $data['view'] = view('frontend.tpvagent.declined-reasons', compact('dispositions'))->render();
        } else {
            $data['view'] = view('frontend.tpvagent.verified-reasons', compact('dispositions'))->render();
        }
        \Log::info("Dispositions retrieved success for type: " . $request->get('disType') . " and for lead with id: " . array_get($lead, 'id'));
        return $this->success('success', 'success', $data);
      } else {
        \Log::error("Retrieve dispositions: All required params are not found");
        return $this->error('error', 'Pass all the required params in request', 400);
      }

    }

    public function dispositionsForAdmin(Request $request) {
      $rules = [
        'disType' => 'required',
        'referenceId' => 'required' ];

      $this->validateJsonResponse($request, $rules);

      $lead = Telesales::where('refrence_id', $request->get('referenceId'))->first();
      if (empty($lead)) {
        $this->error('error', 'Lead not found', 400);
      }

      $client = Client::find(array_get($lead, 'client_id'));

      if (empty($client)) {
        $this->error('error', 'Client not found', 400);
      }

      if ($request->has('disType') && $request->get('disType') != "" && $request->has('referenceId') && $request->get('referenceId') != "") {
        $dispositions = Dispositions::where('client_id', array_get($client, 'id'))->where('status', 'active')->get();
        $data = [];
        $data['totalReasons'] = count($dispositions);
        if ($request->get('disType') == "customerhangup") {
            $data['view'] = view('frontend.tpvagent.disconnected-reasons-admin', compact('dispositions'))->render();
        } else if ($request->get('disType') == "decline") {
            $data['view'] = view('frontend.tpvagent.disconnected-reasons-admin', compact('dispositions'))->render();
        } else {
            $data['view'] = view('frontend.tpvagent.disconnected-reasons-admin', compact('dispositions'))->render();
        }
        \Log::info("Dispositions retrieved success for type: " . $request->get('disType') . " and for lead with id: " . array_get($lead, 'id'));
        return $this->success('success', 'success', $data);
      } else {
        \Log::error("Retrieve dispositions: All required params are not found");
        return $this->error('error', 'Pass all the required params in request', 400);
      }

    }
}
