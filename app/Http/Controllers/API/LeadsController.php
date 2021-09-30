<?php

namespace App\Http\Controllers\API;

use App\Jobs\SendContractPDF;
use App\Jobs\SendSignatureContract;
use Illuminate\Http\Request;
use App\Services\StorageService;
use App\models\Settings;
use App\Services\SegmentService;
use App\Http\Controllers\Controller;
use App\models\Telesales;
use Log;
use App\models\Zipcodes;
use App\models\Programs;
use App\models\Clientsforms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\models\Salescenter;
use App\models\Salescenterslocations;
use App\models\Telesalesdata;
use App\models\TelesalesTmp;
use App\models\Leadmedia;
use App\models\LeadmediaTemp;
use App\models\TelesalesdataTmp;
use App\models\Client;
use App\models\FormField;
use App\models\Dispositions;
use App\models\DoNotEnroll;
use App\User;
use App\models\ClientTwilioNumbers;
use App\models\Salesagentdetail;
use App\models\Salesagentlocation;
use App\models\CriticalLogsHistory;
use App\Traits\LeadTrait;
use App\Http\Controllers\User\UserController;
use App\models\TelesalesZipcode;

class LeadsController extends Controller
{
    use LeadTrait;

    /**
     * This method is used for fetch and show the details of particular lead
     * @param $id
     */
    public function show($id)
    {
    	try {
   			$lead = Telesales::with('teleSalesData')->find($id);

   			if (empty($lead)) {
                return $this->error("error", 'Lead not found with this id !!', 400);
            }

            $leadForm = Clientsforms::withTrashed()->find($lead->form_id);

            if (empty($leadForm)) {
                return $this->error("error", 'Form not found !!', 400);
            }

			$fields = $leadForm->fields()->with('telesalesData')->select('id', 'type', 'label', 'meta', 'is_required', 'is_primary', 'is_verify', 'position')->orderBy('position', 'asc')->get();

			$teleSalesData = array_get($lead, 'teleSalesData');
			
            $response = [];
            $dispositions = [];
			$dispositions = (new UserController)->getDispositions($id,$lead->status);
            foreach ($fields as $fiKey => $fiValue) {
				
				if($fiValue['is_primary'] == 1)
				{
            	// echo "<pre>"; print_r($fiValue); exit;
                $resArr = [];
                $resArr['id'] = array_get($fiValue, 'id');
                $resArr['type'] = array_get($fiValue, 'type');
                if (array_get($fiValue, 'type') == 'textbox') {
                    $resArr['label'] = array_get($fiValue, 'label');
                } else if (array_get($fiValue, 'type') == 'service_and_billing_address') {
                    $resArr['label'] = getLabel(array_get($fiValue, 'label'));
                } else {
                    $resArr['label'] = array_get($fiValue, 'label');
                }

                if ($fiValue['type'] == "phone_number" || $fiValue['type'] == "fullname" || $fiValue['type'] == "service_and_billing_address" || $fiValue['type'] == "address" || $fiValue['type'] == "email") {
                    $resArr['meta']['is_primary'] = $fiValue['is_required'] ? true : false;
                } else {
                    $resArr['meta'] = array_get($fiValue, 'meta');
                }

                if ($fiValue['type'] == "selectbox" || $fiValue['type'] == "radio" || $fiValue['type'] == "checkbox") {
                    $resArr['meta']['style_as_a_button'] = false;
				}
				
                switch ($fiValue['type']) {
					case 'fullname':
						
						$resArr['values']["first_name"] = '';
						$resArr['values']["middle_initial"] = '';
						$resArr['values']["last_name"] = '';
						if($fiValue['is_primary'] == 1)
						{
							$fName = Telesalesdata::where('meta_key', 'first_name')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
							$resArr['values']["first_name"] = array_get($fName, 'meta_value', "");
							$mName = Telesalesdata::where('meta_key', 'middle_initial')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
							$resArr['values']["middle_initial"] = array_get($mName, 'meta_value', "");
							$lName = Telesalesdata::where('meta_key', 'last_name')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
							$resArr['values']["last_name"] = array_get($lName, 'meta_value', "");
						}
	                    break;

					case "service_and_billing_address":
						
                    	// $bAdd1 = '';//Telesalesdata::where('meta_key', 'billing_address_1')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    	// $resArr['values']["billing_address_1"] = array_get($bAdd1, 'meta_value');

                    	// $bAdd2 ='';// Telesalesdata::where('meta_key', 'billing_address_2')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        // $resArr['values']["billing_address_2"] = array_get($bAdd2, 'meta_value');

                    	// $bZip = '';//Telesalesdata::where('meta_key', 'billing_zipcode')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        // $resArr['values']["billing_zipcode"] = array_get($bZip, 'meta_value');

                    	// $bCity = '';//Telesalesdata::where('meta_key', 'billing_city')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        // $resArr['values']["billing_city"] = array_get($bCity, 'meta_value');

                    	// $bState = '';//Telesalesdata::where('meta_key', 'billing_state')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        // $resArr['values']["billing_state"] = array_get($bState, 'meta_value');

                    	$sAdd1 = '';//Telesalesdata::where('meta_key', 'service_address_1')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["address_1"] = array_get($sAdd1, 'meta_value');

                    	$sAdd2 = '';//Telesalesdata::where('meta_key', 'service_address_2')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
						$resArr['values']["address_2"] = array_get($sAdd2, 'meta_value');
						
						$resArr['values']["zipcode"] = '';
            $resArr['values']["city"] = '';
            $resArr['values']["county"] = '';
						$resArr['values']["state"] = '';
						if($fiValue['is_primary'] == 1)
						{
							$resArr['type'] = 'address';//array_get($fiValue, 'type');
                			$resArr['label'] ='Address';// array_get($fiValue, 'label');
							$sZip = Telesalesdata::where('meta_key', 'service_zipcode')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
							$resArr['values']["zipcode"] = array_get($sZip, 'meta_value');

							$sCity = Telesalesdata::where('meta_key', 'service_city')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
              $resArr['values']["city"] = array_get($sCity, 'meta_value');
              
              $sCity = Telesalesdata::where('meta_key', 'service_county')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
							$resArr['values']["county"] = array_get($sCity, 'meta_value');

							$sState = Telesalesdata::where('meta_key', 'service_state')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
							$resArr['values']["state"] = array_get($sState, 'meta_value');
						}

                    	// $bUnit = '';//Telesalesdata::where('meta_key', 'billing_unit')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        // $resArr['values']["billing_unit"] = array_get($bUnit, 'meta_value');

                    	$sUnit ='';// Telesalesdata::where('meta_key', 'service_unit')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["unit"] = array_get($sUnit, 'meta_value');

                    	// $bCountry ='';// Telesalesdata::where('meta_key', 'billing_country')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        // $resArr['values']["billing_country"] = array_get($bCountry, 'meta_value');

                    	$sCountry ='';// Telesalesdata::where('meta_key', 'service_country')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["country"] = array_get($sCountry, 'meta_value');

                    	// $bLat = '';//Telesalesdata::where('meta_key', 'billing_lat')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        // $resArr['values']["billing_lat"] = array_get($bLat, 'meta_value');

                    	// $bLng = '';//Telesalesdata::where('meta_key', 'billing_lng')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        // $resArr['values']["billing_lng"] = array_get($bLng, 'meta_value');

                    	$sLat = '';//Telesalesdata::where('meta_key', 'service_lat')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["lat"] = array_get($sLat, 'meta_value');

                    	$serviceLng = '';//Telesalesdata::where('meta_key', 'service_lng')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["lng"] = array_get($serviceLng, 'meta_value');
                        break;

                    case "address":
                    	$unit = '';//Telesalesdata::where('meta_key', 'unit')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    	$resArr['values']["unit"] = array_get($unit, 'meta_value');

                    	$address_1 ='';// Telesalesdata::where('meta_key', 'address_1')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["address_1"] = array_get($address_1, "meta_value");

                    	$address_2 = '';//Telesalesdata::where('meta_key', 'address_2')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                      $resArr['values']["address_2"] = array_get($address_2, "meta_value");
                      $resArr['values']["zipcode"] = '';
                      $resArr['values']["city"] = '';
                      $resArr['values']["county"] = '';
                      $resArr['values']["state"] = '';
                      if($fiValue['is_primary'] == 1)
                      {
                        $zipcode = Telesalesdata::where('meta_key', 'zipcode')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["zipcode"] = array_get($zipcode, "meta_value");
            
                        $city = Telesalesdata::where('meta_key', 'city')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["city"] = array_get($city, "meta_value");

                        // For address county
                        $city = Telesalesdata::where('meta_key', 'county')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["county"] = array_get($city, "meta_value");
                        // End

                        $state = Telesalesdata::where('meta_key', 'state')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["state"] = array_get($state, "meta_value");
                      }

                    	$country = '';//Telesalesdata::where('meta_key', 'country')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["country"] = array_get($country, "meta_value");

                    	$lat = '';//Telesalesdata::where('meta_key', 'lat')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["lat"] = array_get($lat, "meta_value");

                    	$lng = '';//Telesalesdata::where('meta_key', 'lng')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                        $resArr['values']["lng"] = array_get($lng, "meta_value");
                        break;

					case "selectbox":
                    case "radio":
                    	$radioOptions = $fiValue['meta']['options'];
                    	$radio = Telesalesdata::where('meta_key', 'value')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();

                    	foreach ($radioOptions as $rKey => $rValue) {
                    		if ( array_get($radio, 'meta_value') == $rValue['option']) {
                    			$radioOptions[$rKey]['selected'] = true;
                    		}
                    	}
                    	$resArr['values']["options"] = $radioOptions;
                    	break;

                    case "checkbox":
                    	$radioOptions = $fiValue['meta']['options'];
                    	$radio = Telesalesdata::where('meta_key', 'value')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
						$split = explode(",", array_get($radio, 'meta_value'));
                    	foreach ($radioOptions as $rKey => $rValue) {
                    		if ( in_array($rValue['option'], $split )) {
                    			$radioOptions[$rKey]['selected'] = true;
                    		}
                    	}
                    	$resArr['values']["options"] = $radioOptions;
                    	break;
                    	break;

					case "phone_number":
						$resArr['values']["value"] = '';
						if($fiValue['is_primary'] == 1)
						{
							$phoneNumData = Telesalesdata::where('meta_key', 'value')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
							$phoneNum = array_get($phoneNumData, "meta_value");
							$phoneNum = (strlen($phoneNum) == 11) ? $phoneNum : "1" . $phoneNum;
							$resArr['values']["value"] = preg_replace(config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT'), config('constants.PHONE_NUMBER_REPLACEMENT'), $phoneNum) ;
						}
                        break;

                    case "separator":
                    case "heading":
                    case "label":
                        $resArr['values'] = NULL;
                        break;

					default:
						$resArr['values']["value"] = '';
						if($fiValue['is_primary'] == 1)
						{
							$default = Telesalesdata::where('meta_key', 'value')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
							$resArr['values']["value"] = array_get($default, "meta_value", "");
						}
			            break;
                }



                switch ($fiValue['type']) {
                    case 'phone_number':
                        $resArr['validations']["required"] = ($fiValue['is_required']) ? true : false;
                        $resArr['validations']["verify"] = ($fiValue['is_verify']) ? true : false;
                        break;

                    case 'textbox':
                        $resArr['validations']["required"] = ($fiValue['is_required']) ? true : false;
                        $resArr['validations']["length"] = 0;
                        break;

                    case "checkbox":
                    case "radio":
                    case "textarea":
                    case "address":
                    case "service_and_billing_address":
                    case 'fullname':
                    case "selectbox":
                    case "email":
                        $resArr['validations']["required"] = ($fiValue['is_required']) ? true : false;
                        break;

                }

				$response[] = $resArr;
				}
			}
			
			// $response[]['disposition'] = $dispositions;
      Log::info("lead details retrieved with id: " . $id);
      
      $programs = $lead->programs()->withTrashed()->with('utility')->get();
        
      $allPrograms = [];
      foreach($programs as $program) {
          $programArr = [];
          $programArr['utility'] = $program->utility->commodity . " Utility";
          $programArr['id'] = $program->id;
          $programArr['name'] = $program->name;
          $programArr['rate'] = '$'.$program->rate .' per '.$program->unit_of_measure;
          $programArr['utilityName'] = $program->utility->fullname .' ('.$program->utility->market.')';
          $allPrograms[] = $programArr;
      }
      $veirificationCode = '';
      if($lead->status == 'verified')
        $veirificationCode = $lead->verification_number;
        
      $data = array();
      $data['leadDeatils'] = $response;
      $data['programsDeatils'] = $allPrograms;
      $data['dispositions'] = $dispositions;
      
      $data['verificationCode'] = $veirificationCode;

      return response()->json(
        [
          'status' => 'success',
          'message' => "Lead details retrieved !!",
          'data' => (object) $data
        ]
      );
			// return $this->success("success", "Lead details retrieved !!", $response);
    	} catch (\Exception $e) {
    		Log::error("Error while retrieving lead details with id " . $id . " :" . $e->getMessage());
    		return $this->error("error", $e->getMessage(), 500);
    	}
    }

    /**
     * For store the value of lead
     */
    public function store(Request $request)
    {
        Log::info($request->all());
        
        Log::info(\Auth::user()->id);
        $validator = \Validator::make($request->all(), [
            'form_id' => 'required',
            'fields' => 'required',
            'lead_tmp_id' => 'required',
        ]);
             
        if ($validator->fails()) {
            return $this->error("error", implode(',',$validator->messages()->all()), 422);
        }

        try {

            $form = Clientsforms::with('client')->find($request->form_id);
            if (empty($form)) {
                return $this->error("error", 'Form not available', 400);
            }

            $otherElements = $request->get('other');

            if (!isset($otherElements['zipcode']) || $otherElements['zipcode'] == NULL) {
                return $this->error("error", 'Zipcode is required field', 400);
            }

            if (!isset($otherElements['program_id']) || $otherElements['program_id'] == NULL) {
                return $this->error("error", 'Program id is required field', 400);
            }

            $zipcode = Zipcodes::where('zipcode', $otherElements['zipcode'])->first();

            if (empty($zipcode)) {
                return $this->error("error", 'Please enter a valid zipcode', 400);
            }

            $programIds = explode(",", $otherElements['program_id']);

            if (is_array($programIds)) {
                foreach ($programIds as $pId) {
                    $program = Programs::find($pId);
                    if (empty($program)) {
                        return $this->error("error", 'Program not found with id: ' . $pId, 400);
                    }
                }
            } else {
                $program = Programs::find($otherElements['program_id']);
                if (empty($program)) {
                    return $this->error("error", 'Please select a valid program' , 400);
                }
            }

            // $requestFields = $request->get('fields');
            $requestedFields = $request->fields;
            $emailIndices = array_keys(array_column($requestedFields, 'type'),'email');
            $requestEmail = '';
            \Log::info("Email indices: ");
            \Log::info(print_r($emailIndices, true));
            foreach($emailIndices as $index){
                \Log::info("Index: ");
                \Log::info(print_r($index, true));
                if(isset($requestedFields[$index]['meta']['is_primary']) && $requestedFields[$index]['meta']['is_primary'] == true){
                        $requestEmail = (isset($requestedFields[$index]['values']['value'])) ? $requestedFields[$index]['values']['value'] : '';
                }
            }

            \Log::info("Request Email: " . $requestEmail);
            \Log::info("Auth Email: " . Auth::user()->email);

            if ($requestEmail != "" && $requestEmail == Auth::user()->email) {
                Log::error("Request email is match with logged in sales agent: " . $requestEmail);
                return $this->error("error", "You cannot submit a lead using your personal phone number or email address.", 500);
            }

            // $requestFields = $request->get('fields');
            $phoneIndices = array_keys(array_column($requestedFields, 'type'),'phone_number');
            $requestPhone = '';
            \Log::info("Phone indices: ");
            \Log::info(print_r($phoneIndices, true));
            foreach($phoneIndices as $pIndex){
                \Log::info("Index: ");
                \Log::info(print_r($pIndex, true));
                if(isset($requestedFields[$pIndex]['meta']['is_primary']) && $requestedFields[$pIndex]['meta']['is_primary'] == true){
                        $requestPhone = (isset($requestedFields[$pIndex]['values']['value'])) ? $requestedFields[$pIndex]['values']['value'] : '';
                }
            }

            $salesAgentDetail = Salesagentdetail::where('user_id', Auth::user()->id)->first();

            \Log::info("Request phone: " . $requestPhone);
            \Log::info("Auth phone: " . array_get($salesAgentDetail, 'phone_number'));
            
            if ($requestPhone != "" && $requestPhone == array_get($salesAgentDetail, 'phone_number')) {
                Log::error("Request phone is match with logged in sales agent: " . $requestPhone);
                return $this->error("error", "You cannot submit a lead using your personal phone number or email address.", 500);
            }
        
            $leadData = [];
         $leadData['client_id'] = array_get($form, 'client_id');
         $leadData['form_id'] = array_get($form, 'id');
         $leadData['user_id'] = Auth::user()->id;
         $clientId = array_get($form, 'client_id');
           $leadData['alert_status'] = config('constants.TELESALES_ALERT_PROCEED_STATUS');
           $clientPrefix = (new Client())->getClientPrefix($clientId);
        //  $referenceId = (new Telesales)->generateReferenceId();
         $referenceId = (new Telesales)->generateNewReferenceId($clientId,$clientPrefix);
         
    //      $check_verification_number = 2;
    //  $validate_num = $verification_number = "";
    //  while ($check_verification_number > 1){
    //     $verification_number = rand(1000000,9999999);
    //     $validate_num =   (new Telesales)->validateConfirmationNumber($verification_number);
    //     if( !$validate_num ){
    //         $check_verification_number = 0;
    //     }else{
    //         $check_verification_number ++;
    //     }
    //  }

     $leadData['refrence_id'] = $referenceId;
     $leadData['is_multiple'] = 0;
     $leadData['multiple_parent_id'] = 0;
    //  $leadData['verification_number'] = $verification_number;
     $leadData['cloned_by'] = 0;
     $leadData['parent_id'] = 0;
     $leadData['status'] = "pending";

//    if($request->has('agent_lat') && $request->has('agent_lng')) {
//        $leadData['salesagent_lat'] = $request->agent_lat;
//        $leadData['salesagent_lng'] = $request->agent_lng;
//    }

   if($request->lead_tmp_id != "")
   {
       $tempLead = TelesalesTmp::find($request->lead_tmp_id);
       $leadData['salesagent_lat'] = array_get($tempLead, 'salesagent_lat');
       $leadData['salesagent_lng'] = array_get($tempLead, 'salesagent_lng');
       $leadData['is_enrollment_by_state'] = array_get($tempLead, 'is_enrollment_by_state');
   }
	$telesale = Telesales::create($leadData);


    $message = __('critical_logs.messages.Event_Type_11');
	$event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_11');
    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
	(new CriticalLogsHistory)->createCriticalLogs(\Auth::user()->id,$message,$telesale->id,null,null,$lead_status,$event_type);
	 if($request->lead_tmp_id != "")
	 {
		$updaateLeadInLogs = (new CriticalLogsHistory)->updateId($request->lead_tmp_id,$telesale->id,$telesale->status);
        TelesalesTmp::where('id', $request->lead_tmp_id)->update(['is_proceed'=>1]);
	 }
     $formFields = $request->get('fields');
     
     foreach ($formFields as $k => $field) {
      
       if ($field['type'] != "separator" || $field['type'] != "heading") {
         $values = $field['values'];

         if ($values != NULL) {
           if (array_get($values, 'options')) {
             $metaValues = [];
             foreach (array_get($values, 'options') as $key => $value) {
               if (array_get($value, 'selected') == 1) {
                 $metaValues[] = $value['option'];
               }
             }

             if (!empty($metaValues)) {
               \Log::info($field['id'] . " <br/> ");
               $telesale->teleSalesData()->create([
                     'meta_key' => "value",
                     'meta_value' => implode(",", $metaValues),
                     'field_id' => $field['id']
                   ]);
                 }

            } else {

                if ($field['type'] == 'service_and_billing_address') {
                    if (!array_key_exists("service_county",$values)) {
                        $values['service_county'] = '';
                    }

                    if (!array_key_exists("billing_county",$values)) {
                        $values['billing_county'] = '';
                    }
                }

                if ($field['type'] == 'address') {
                    if (!array_key_exists("county",$values)) {
                        $values['county'] = '';
                    }
                }
            
                foreach ($values as $key => $value) {
                    \Log::info($field['id'] . " <br/> ");
                    \Log::info("values : ".$value);

                    $counyKey = ['county','service_county','billing_county'];
                    if (in_array($key, $counyKey) && empty($value)) {
                        $zipcodeKey = str_replace('county', 'zipcode', $key);
                        $addressZipcode = Zipcodes::where('zipcode',array_get($values,$zipcodeKey))->first();
                        $value = array_get($addressZipcode,'county');
                        //Append 'County' in service/billing county value
                        if(strpos($value,'County') === false){
                          $value = $value . ' County';
                        }
                    }

                    $telesale->teleSalesData()->create([
                        'meta_key' => $key,
                        'meta_value' => $value,
                        'field_id' => $field['id']
                   ]);
                   
                }
            }
           
            }
             
         } else {
          
           continue;
         }

         }
                 
         // $telesale->teleSalesData()->create([
         //   'zipcode' => $otherElements['zipcode'],
         // ]);

         $telesale->zipcodes()->sync($zipcode->id);
         
        if (is_array(explode(",", $otherElements['program_id']))) {
            $telesale->programs()->sync(explode(",", $otherElements['program_id']));
        } else {
            $telesale->programs()->sync($otherElements['program_id']);
        }

        $this->storeSalesAgentLocation($telesale);

        // move signature temprary to parmanent location
        $isMovedSignature = $this->tempToParmanentSignature($telesale->id, $request->lead_tmp_id);

        if ($isMovedSignature) {
            if($telesale->client_id == config('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID')) {
              SendSignatureContract::dispatch($telesale->id);
            } else {
              $this->sendContract($telesale->id);
            }
        }

        $twilioNumber = ClientTwilioNumbers::where('client_id',$telesale->client_id)->where('type','customer_call_in_verification')->first();
        if(!empty($twilioNumber)) {
            $phone_number = $twilioNumber->phonenumber;
            $phone_number = str_replace("+", "", $phone_number);
            $phone_number = preg_replace(config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT'), config()->get('constants.PHONE_NUMBER_REPLACEMENT'), $phone_number);
        } else {
            $phone_number = '';
        }
        $this->sendCriticalAlertMail($telesale);
        $lead_state = TelesalesZipcode::join('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id')->where('telesales_zipcodes.telesale_id',$telesale->id)->select('state')->first();
        if(isOnSettings($clientId,'is_enable_self_tpv_d2d')){
          $states = getSettingValue($clientId,'restrict_states_self_tpv_d2d',null);
          $client_states = !empty($states) ? explode(',',$states) : '';
          if(!empty($client_states)){
              $restrict_state = in_array($lead_state['state'],$client_states);
          }else{
              $restrict_state = true;
          }
          
        }else{
              $restrict_state = false;
        }
        $clientId = $telesale->client_id;
        $data = [];
        $data['id'] = $telesale->id;
        $data['reference_id'] = $telesale->refrence_id;
        $data['phone_number'] = $phone_number;
        $data['is_on_customer_call_number'] = isOnSettings($clientId,'is_enable_cust_call_num');
        $data['is_on_self_tpv'] = $restrict_state;
        $data['is_on_outbound_tpv'] = isOnSettings($clientId,'is_enable_outbound_tpv');

        Log::info("Lead created !!");
        return $this->success("success", "Lead created !!", $data);

    } catch (\Exception $e) {
       Log::error("Error while creating lead: " . $e->getMessage());
       return $this->error("error", "Something went wrong while creating lead !!", 500);
    }
}

    /**
     * This method is used to validate lead details
     * @param $request
     */
    public function checkLeadValidation(Request $request)
    {
        Log::info($request->all());
        $validator = \Validator::make($request->all(), [
            'form_id' => 'required',
            'fields' => 'required',
        ]);
          
        
        if ($validator->fails()) {
          return $this->error("error", implode(',',$validator->messages()->all()), 422);
        }

        try {
        $requestedFields = $request->fields;
        $form = Clientsforms::with('client')->find($request->form_id);

        if (empty($form)) {
            return $this->error("error", 'Form not available', 400);
        }
        $client = Client::find($form->client_id);
        if (empty($client)) {
            return $this->error("error", 'Client not available', 400);
        }

        /* Form Backend Validation */
        $formValidationMsg = array();
        $fullnameIndices = array_keys(array_column($requestedFields, 'type'), 'fullname');
        $serviceDetails = array_keys(array_column($requestedFields, 'type'), 'service_and_billing_address');
        $addressDetails = array_keys(array_column($requestedFields, 'type'), 'address');
        $addressListDetails = array_merge((array)$serviceDetails, (array)$addressDetails);
        
        /* Lable needs to Match */
        $name_array = array_map('strtolower',["Customer Name","Billing Name"]);
        $isMDState = [];
        $primeryServiceZipcode = [];
        foreach ($serviceDetails as $key => $serviceFieldID) {

            if (isset($requestedFields[$serviceFieldID]['meta']['is_primary']) && $requestedFields[$serviceFieldID]['meta']['is_primary'] == 1) {
                if(isset($requestedFields[$serviceFieldID]['values']['service_zipcode'])){
                    $primeryServiceZipcode = $requestedFields[$serviceFieldID]['values']['service_zipcode'];
                }else{
                    $primeryServiceZipcode = $requestedFields[$serviceFieldID]['values']['zipcode'];
                }
            }
        }

        if(!empty($primeryServiceZipcode)){
            $isMDState = Zipcodes::whereIn('zipcode', [$primeryServiceZipcode])->where('state', 'MD')->first();
        }

        /* Check Client Id is RRH and State is MD */
        if ($form->client_id == config('constants.CLIENT_RRH_CLIENT_ID') && $isMDState) {
            
            /* Fetch active fields from Form */
            $check_field_ids = FormField::where('form_id',$request->form_id)
                ->where('type',"fullname")
                ->whereNull('deleted_at')
                ->whereIn(DB::raw('LOWER(label)'),$name_array)
                ->pluck('id')
                ->toArray();

            $validFirstName = array();
            $validMiddleInitial = array();
            $validLastName = array();
            foreach ($fullnameIndices as $key => $fieldID) {
                if (in_array($requestedFields[$fieldID]["id"], $check_field_ids)) {
                    if (isset($requestedFields[$fieldID]['values']['first_name'])) {
                        $validFirstName[] = $requestedFields[$fieldID]['values']['first_name'];
                    }
                    if (isset($requestedFields[$fieldID]['values']['middle_initial'])) {
                      $validMiddleInitial[] = $requestedFields[$fieldID]['values']['middle_initial'];
                    }
                    else
                    {
                        $validMiddleInitial[] = "";
                    }
                    if (isset($requestedFields[$fieldID]['values']['last_name'])) {
                        $validLastName[] = $requestedFields[$fieldID]['values']['last_name'];
                    }
                }
            }

            /* Compare Billing and Customer Name */
            if ((count($validFirstName) > 1 && count(array_unique($validFirstName)) > 1) || (count($validMiddleInitial) > 1 && count(array_unique($validMiddleInitial)) > 1) || (count($validLastName) > 1 && count(array_unique($validLastName)) > 1)) {
                $formValidationMsg[] = 'Billing Name must match Customer Name';
            }

            if (!empty($formValidationMsg)) {
                $validationData['validations_errors'] = $formValidationMsg;
                return $this->success("success", "Validation error message found !!", $validationData);
            }
        }

        $otherElements = $request->get('other');

        if (!isset($otherElements['zipcode']) || $otherElements['zipcode'] == NULL) {
            return $this->error("error", 'Zipcode is required field', 400);
        }

        if (!isset($otherElements['program_id']) || $otherElements['program_id'] == NULL) {
            return $this->error("error", 'Program id is required field', 400);
        }

        $zipcode = Zipcodes::where('zipcode', $otherElements['zipcode'])->first();

        if (empty($zipcode)) {
            return $this->error("error", 'Please enter a valid zipcode', 400);
        }

        $programIds = explode(",", $otherElements['program_id']);

        if (is_array($programIds)) {
            foreach ($programIds as $pId) {
                $program = Programs::find($pId);
                if (empty($program)) {
                    return $this->error("error", 'Program not found with id: ' . $pId, 400);
                }
            }
        } else {
            $program = Programs::find($otherElements['program_id']);
            if (empty($program)) {
              return $this->error("error", 'Please select a valid program', 400);
            }
        }

        $isEnrollByState = 0;
        if (isset($otherElements['enrollment_using']) && $otherElements['enrollment_using'] == 'state') {
            $isEnrollByState = 1;
        }

        $clientId = array_get($form, 'client_id');
        $leadData = [];
        $leadData['client_id'] = $clientId;
        $leadData['form_id'] = array_get($form, 'id');
        $leadData['user_id'] = Auth::user()->id;
        $leadData['program'] = isset($programIds) ? implode(',',$programIds) : NULL;
        $leadData['is_enrollment_by_state'] = $isEnrollByState; 
        // $referenceId = (new TelesalesTmp)->generateReferenceId();
        $clientPrefix = (new Client())->getClientPrefix($clientId);
        $referenceId = (new TelesalesTmp)->generateNewReferenceId($clientId,$clientPrefix);

        $isOnAlert = isOnSettings($clientId,'is_enable_alert_d2d');

        // $check_verification_number = 2;
        // $validate_num = $verification_number = "";
        // while ($check_verification_number > 1){
        //     $verification_number = rand(1000000,9999999);
        //     $validate_num = (new TelesalesTmp)->validateConfirmationNumber($verification_number);
        //     if( !$validate_num ){
        //         $check_verification_number = 0;
        //     }else{
        //         $check_verification_number ++;
        //     }
        // }

        $leadData['refrence_id'] = $referenceId;
        $leadData['is_multiple'] = 0;
        $leadData['multiple_parent_id'] = 0;
        // $leadData['verification_number'] = $verification_number;
        $leadData['cloned_by'] = 0;
        $leadData['parent_id'] = 0;
        $leadData['zipcode'] = $otherElements['zipcode'];
        $leadData['salesagent_lat'] = $request->has('agent_lat') ? $request->agent_lat : NULL;
        $leadData['salesagent_lng'] = $request->has('agent_lng') ? $request->agent_lng : NULL;


        \Log::info('$leadData'.print_r($request->all(),true));
        // \Log::info('$leadData'.print_r($leadData,true));

        $telesaleTmp = TelesalesTmp::create($leadData);

        $formFields = $request->get('fields');

        foreach ($formFields as $k => $field) {

            if ($field['type'] != "separator" || $field['type'] != "heading") {

                $values = $field['values'];
                if ($values != NULL) {

                    if (array_get($values, 'options')) {

                        $metaValues = [];
                        foreach (array_get($values, 'options') as $key => $value) {
                            if (array_get($value, 'selected') == 1) {
                                $metaValues[] = $value['option'];
                            }
                        }
                        if (!empty($metaValues)) {
                            \Log::info($field['id'] . " <br/> ");
                            $telesaleTmp->teleSalesData()->create([
                                'meta_key' => "value",
                                'meta_value' => implode(",", $metaValues),
                                'field_id' => $field['id']
                            ]);

                        }

                    } else {

                        foreach ($values as $key => $value) {

                            \Log::info($field['id'] . " <br/> ");
                            $telesaleTmp->teleSalesData()->create([
                                'meta_key' => $key,
                                'meta_value' => $value,
                                'field_id' => $field['id']
                            ]);

                        }

                    }
                }
            } else {

                continue;
            }
        }

		// for check account number exists or not in do not enroll list
		$exists = $this->isExistsInDNE($clientId, $request->form_id, $requestedFields, true);
		if($exists) {
			// Account number is exists in do not enrollment list.
			$message = 'This customer cannot be enrolled with '.$client->name.'. Please contact '.$client->name.' for more details.';
			\Log::info("Account number is exists in do not enrollment list.So ".$message);

			$disposition = Dispositions::where("client_id",$clientId)->where('type','do_not_enroll')->first();
			$dispositionId = array_get($disposition,'id');
			$cancelLead = $this->funCancelLead($telesaleTmp->id, $dispositionId);

			if($cancelLead){
				return $this->error("error", $message, 500);
			}else{
				return $this->error("error", "Something went wrong !", 500);
			}
		}

        /** Geomapping validation check for event type 1 */
        $validationData = array();
        // for check settings is on or off
        if ($isOnAlert && isOnSettings($clientId,'is_enable_alert8_d2d')) {
			if($request->has('agent_lat') && $request->has('agent_lng')) {
				$geolocation = $request->agent_lat.','.$request->agent_lng;
				$agentLng = $request->agent_lng;
				$agentLat = $request->agent_lat;
				$request  = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$geolocation.'&sensor=false&key=AIzaSyCwDYH6F8nAVlguYbLk87ORm1zkfALTZ8c'; 
				$file_contents = file_get_contents($request);
				$json_decode = json_decode($file_contents);
				
				if(isset($json_decode->results[0])) {
					$response = array();
					foreach($json_decode->results[0]->address_components as $addressComponet) {
						if(in_array('political', $addressComponet->types)) {
								$response[] = $addressComponet->long_name; 
						}
						if(in_array('postal_code', $addressComponet->types)) {
							$response[] = $addressComponet->long_name; 
						}
					}
					$formatted_address = "";
					foreach($response as $res)
					{
						$formatted_address .= $res.", ";
					}
					$formatted_address = rtrim($formatted_address,", ");
				}
					
       			$sAndBAddressIndices = array_keys(array_column($requestedFields, 'type'), 'service_and_billing_address');
				$requestedsAndBAddress = '';
				$serviceAddress = '';
                foreach ($sAndBAddressIndices as $index) {
                    if (isset($requestedFields[$index]['meta']['is_primary']) && $requestedFields[$index]['meta']['is_primary'] == true) {
                        $requestedServiceLat = (isset($requestedFields[$index]['values']['service_lat'])) ? $requestedFields[$index]['values']['service_lat'] : '';
                        $requestedServiceLng = (isset($requestedFields[$index]['values']['service_lng'])) ? $requestedFields[$index]['values']['service_lng'] : '';
						$serviceAddress .= (isset($requestedFields[$index]['values']['service_address_1'])) ? $requestedFields[$index]['values']['service_address_1'].", " : '';
						$serviceAddress .= (isset($requestedFields[$index]['values']['service_address_2'])) ? $requestedFields[$index]['values']['service_address_2'].", " : '';
						$serviceAddress .= (isset($requestedFields[$index]['values']['service_city'])) ? $requestedFields[$index]['values']['service_city'].", " : '';
						$serviceAddress .= (isset($requestedFields[$index]['values']['service_state'])) ? $requestedFields[$index]['values']['service_state'].", " : '';
						$serviceAddress .= (isset($requestedFields[$index]['values']['service_country'])) ? $requestedFields[$index]['values']['service_country'].", " : '';
						
						$serviceAddress .= (isset($requestedFields[$index]['values']['service_zipcode'])) ? $requestedFields[$index]['values']['service_zipcode'] : '';
						
                        $distance = "";
                        if ($requestedServiceLat != "" && $requestedServiceLng != "" && $agentLat != "" && $agentLng != "") {
                            $distance = distance($requestedServiceLat, $requestedServiceLng, $agentLat, $agentLng, "M");
                            $radius = getSettingValue($clientId,'geomapping_radius',0);
                            if($radius == 0){
                              $radius = config()->get('constants.GEOMAPPING_VALIDATION_DISTANCE_IN_METER');
                            }
                            
                            if ($distance != "" && $distance > $radius) {
                                $d2dAlert8Critical = isOnSettings($clientId, 'is_critical_alert8_d2d');
                                $alert_msg = "Geolocation: the sales agent's location did not match the service location.";
								$criticalLogMessage = __('critical_logs.messages.Event_Type_1')."Estimated address of agent: ".$formatted_address."\nService address: ".$serviceAddress ."\nAcceptable Radius: ".$radius." m\nActual Radius: ".ceil($distance) ." m";
                                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_1');
                                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                                if ($d2dAlert8Critical) {
                                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                                } else {
                                    $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                                }
                                
								$criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$criticalLogMessage,null,$telesaleTmp->id,null,$lead_status,$event_type,$error_type,null,null,$alert_msg);
                                if ($d2dAlert8Critical) {
                                  $cancelLead = $this->funCancelLead($telesaleTmp->id);
                                  if ($cancelLead) {
                                    return $this->error("error", "Your current location does not match with your service location.", 500);
                                  } else {
                                    return $this->error("error", "Something went wrong !", 500);
                                  }
                                } else if(isOnSettings($clientId, 'is_show_agent_alert8_d2d')) {
                                  $validationData['geomapping']['title'] = "Geo mapping alert";
                                  $validationData['geomapping']['msg'] = "Your current location does not match with your service location.";
                                }
                                
                                
                            }
						}
						
                    }
				}
			}
			else
			{
				\Log::info("Agent lat and long not found");
			}
        } else {
            \Log::info("Geomapping location validation check flag is not true, or alert settings is off.");
        }
 
        //  for check setting is on or off
        if ($isOnAlert && isOnSettings($clientId,'is_enable_alert7_d2d')) {

            /** Email Validation Check Case #0*/       
               
            $emailIndices = array_keys(array_column($requestedFields, 'type'),'email');
            $requestedEmail = '';
            
            foreach($emailIndices as $index){

                if(isset($requestedFields[$index]['meta']['is_primary']) && $requestedFields[$index]['meta']['is_primary'] == true){
                        $requestedEmail = (isset($requestedFields[$index]['values']['value'])) ? $requestedFields[$index]['values']['value'] : '';
                }
    		}

    		$phoneIndices = array_keys(array_column($requestedFields, 'type'),'phone_number');
            $requestedPhone = '';

            foreach($phoneIndices as $pIndex){
                \Log::info("Index: ");
                // \Log::info(print_r($pIndex, true));
                if(isset($requestedFields[$pIndex]['meta']['is_primary']) && $requestedFields[$pIndex]['meta']['is_primary'] == true){
                        $requestedPhone = (isset($requestedFields[$pIndex]['values']['value'])) ? $requestedFields[$pIndex]['values']['value'] : '';
                }
            }

        $salesAgentDetail = Salesagentdetail::where('user_id', Auth::user()->id)->first();
        
        $d2dAlert7Critical = isOnSettings($clientId, 'is_critical_alert7_d2d');
        $isShowAgent = isOnSettings($clientId, 'is_show_agent_alert7_d2d');

    		if (($requestedPhone != "" && $requestedPhone == array_get($salesAgentDetail, 'phone_number')) && ($requestedEmail != "" && $requestedEmail == Auth::user()->email))
    		{
                $alert_msg = "Sales agent used their own email address and phone number during enrollment.";
    			      $message = __('critical_logs.messages.Event_Type_4');
                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
    			      $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_4');
                if ($d2dAlert7Critical) {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                } else {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                }
    			      $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,null,$lead_status,$event_type,$error_type,null,null,$alert_msg);
                
                if ($d2dAlert7Critical) {
                  $cancelLead = $this->funCancelLead($telesaleTmp->id);

                  if($cancelLead){
                    return $this->error("error", "You cannot submit a lead using your personal phone number and email address.", 500);
                  }else{
                    return $this->error("error", "Something went wrong !", 500);
                  }
                } else if($isShowAgent){
                  $validationData['personalDetails']['title'] = "Sales agent used their personal details.";
                  $validationData['personalDetails']['msg'] = $alert_msg;
                }
    		}
              
            if ($requestedEmail != "" && $requestedEmail == Auth::user()->email) {
                Log::error("Request email is match with logged in sales agent: " . $requestedEmail);
                $alert_msg = "Sales agent used their own email during enrollment.";
                $message = __('critical_logs.messages.Event_Type_2');
                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
    			      $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_2');
                if ($d2dAlert7Critical) {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                } else {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                }
                $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,null,$lead_status,$event_type,$error_type,null,null,$alert_msg);
                
                //Check for alert if auto cancel is on
                if ($d2dAlert7Critical) {
                  $cancelLead = $this->funCancelLead($telesaleTmp->id);
                  if($cancelLead){
                    return $this->error("error", "You cannot submit a lead using your personal phone number or email address.", 500);
                  }else{
                    return $this->error("error", "Something went wrong !", 500);
                  }
                } else if($isShowAgent) {
                  $validationData['personalDetails']['title'] = "Sales agent used their personal details.";
                  $validationData['personalDetails']['msg'] = $alert_msg;
                }
            }
            /**End email validation*/

            /** Phone Number Validaton case #02 */
            $phoneIndices = array_keys(array_column($requestedFields, 'type'),'phone_number');
            $requestedPhone = '';

            foreach($phoneIndices as $pIndex){
                \Log::info("Index: ");
                // \Log::info(print_r($pIndex, true));
                if(isset($requestedFields[$pIndex]['meta']['is_primary']) && $requestedFields[$pIndex]['meta']['is_primary'] == true){
                        $requestedPhone = (isset($requestedFields[$pIndex]['values']['value'])) ? $requestedFields[$pIndex]['values']['value'] : '';
                }
            }

            $salesAgentDetail = Salesagentdetail::where('user_id', Auth::user()->id)->first();

            if ($requestedPhone != "" && $requestedPhone == array_get($salesAgentDetail, 'phone_number')) {
                Log::error("Request phone is match with logged in sales agent: " . $requestedPhone);
                $alert_msg = "Sales agent used their own phone number during enrollment.";
    			      $message = __('critical_logs.messages.Event_Type_3');
    			      $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
    			      $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_3');
                if ($d2dAlert7Critical) {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                } else {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                }
                $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,null,$lead_status,$event_type,$error_type,null,null,$alert_msg);
                
                //Check for alert if auto cancel is on
                if ($d2dAlert7Critical) {
                  $cancelLead = $this->funCancelLead($telesaleTmp->id);
                  if($cancelLead){
                    return $this->error("error", "You cannot submit a lead using your personal phone number or email address.", 500);
                  }else{
                      return $this->error("error", "Something went wrong !", 500);
                  }
                } else if($isShowAgent) {
                  $validationData['personalDetails']['title'] = "Sales agent used their personal details.";
                  $validationData['personalDetails']['msg'] = $alert_msg;
                }
            }
            /** End Phone Number */
        } else {
            info('Alert 7 settings is switch off.(d2d)');
        }


        /** Validation Code start*/
        $form = Clientsforms::with('fields')->find($form->id);

        $accountNumField = $form->fields()->where('label','LIKE', 'Account Number%')->where('type', 'textbox')->first();
        // \Log::info('$accountNumField'.print_r($accountNumField,true));
        
        $teleSalesData = [];
        $aLeadStatus = array();
        $aLeadStatus['pending'] = 0;
        $aLeadStatus['verified'] = 0;
        $aLeadStatus['decline'] = 0;
        $aLeadStatus['hangup'] = 0;
        $aLeadStatus['cancel'] = 0;
        $aLeadStatus['expired'] = 0;
        // $aLeadStatus['msg'] = '';
        $totCount = 0;
        $aVerifiedLeadData = array();
        if (!empty($accountNumField)) {
          
          //Check for primary first name anddd last name

          $requestFields = $requestedFields;
          $accountIndices = array_keys(array_column($requestFields, 'id'),$accountNumField->id);
          // \Log::info('$accountIndices'.print_r($accountIndices,true));
          $requestAccountNumber = '';
          foreach($accountIndices as $index){
            \Log::info('$requestFields[$index][value]'.print_r($requestFields[$index]['values'],true));
              if(isset($requestFields[$index]['values']['value']) && $requestFields[$index]['values']['value']){
                      $requestAccountNumber = $requestFields[$index]['values']['value'];
              }
          }
            \Log::info('$requestAccountNumber'.$requestAccountNumber);

            
          //Duplicate Account number Validation
          $fieldIds = FormField::where('label','LIKE','Account Number%')->where('type','textbox')->pluck('id')->toArray();
          $teleSalesData = Telesalesdata::where('meta_key','value')->where('meta_value',$requestAccountNumber)->whereIn('field_id',$fieldIds)->pluck('telesale_id')->toArray();

            $intervalDays = getSettingValue($clientId,'interval_days_alert1_d2d',null);
            $teleSales = Telesales::whereIn('id',$teleSalesData);
            if (!empty($intervalDays) && $intervalDays > 0) {
                $intervalDate = today()->subDays($intervalDays);
                $teleSales->whereDate('created_at','>=',$intervalDate);
            }
            $teleSales = $teleSales->get();

          $lead_ids = "";
          foreach($teleSales AS $teleSale){
            $lead_ids .= $teleSale->id .",";
            if($teleSale->status == 'verified'){
              $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
            }

            if($teleSale->status == 'pending'){
              $aLeadStatus['pending'] = isset($aLeadStatus['pending']) ?  $aLeadStatus['pending'] + 1 : 1;
            }

            if($teleSale->status == 'decline'){
              $aLeadStatus['decline'] = isset($aLeadStatus['decline']) ? $aLeadStatus['decline'] + 1 : 1;
            }

            if($teleSale->status == 'hangup'){
              $aLeadStatus['hangup'] = isset($aLeadStatus['hangup']) ? $aLeadStatus['hangup'] + 1 : 1;
            }

            if($teleSale->status == 'cancel'){
              $aLeadStatus['cancel'] = isset($aLeadStatus['cancel']) ? $aLeadStatus['cancel'] + 1 : 1;
            }

            if($teleSale->status == 'expired'){
              $aLeadStatus['expired'] = isset($aLeadStatus['expired']) ? $aLeadStatus['expired'] + 1 : 1;
            }

            $totCount = $totCount + 1;
          }
          $lead_ids = rtrim($lead_ids,",");

          $d2dAlert1Critical = isOnSettings($clientId, 'is_critical_alert1_d2d');
          if($totCount >= getSettingValue($clientId,'max_times_alert1_d2d') && $isOnAlert && isOnSettings($clientId,'is_enable_alert1_d2d')){
            if($totCount >1 ) {
                $time = ' times.';
            } else {
                $time = ' time.';
            }
            $alert_msg = "Sales agent used an account number that has been used ".$totCount.$time;
            $message = __('critical_logs.messages.Event_Type_5',['count'=>$totCount]);
            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_5');
            
            if ($d2dAlert1Critical) {
              $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
            } else {
              $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
            }
            $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alert_msg);
            
            //Check for alert if auto cancel is on
            if ($d2dAlert1Critical) {
              $cancelLead = $this->funCancelLead($telesaleTmp->id);
              if($cancelLead){
                return $this->error("error", $alert_msg, 500);
              }else{
                  return $this->error("error", "Something went wrong !", 500);
              }
            } else if(isOnSettings($clientId, 'is_show_agent_alert1_d2d')) {
                $validationData['acc']['title'] = 'This Account number has been used '.$totCount.' times.';
                $validationData['acc']['msg'] = 'Good Sales: '.$aLeadStatus['verified'] .' Pending Leads: '.($aLeadStatus['pending'] + $aLeadStatus['hangup']).' Bad Sales: '.$aLeadStatus['decline'].' Cancelled Leads: '.($aLeadStatus['cancel']+ $aLeadStatus['expired']);
            }
          }

           // for check settings is on or off
          if ($isOnAlert && isOnSettings($clientId,'is_enable_alert9_d2d',false)) {
            \Log::info('Account number critical logs');
            $intervalDays = getSettingValue($clientId,'interval_days_alert9_d2d',null);
              $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
              if (!empty($intervalDays) && $intervalDays > 0) {
                  $intervalDate = today()->subDays($intervalDays);
                  $teleSales->whereDate('created_at','>=',$intervalDate);
              }
              $teleSales = $teleSales->get();
              
              $aLeadStatus = array();
              $aLeadStatus['verified'] = 0;
              $aVerifiedLeadData = array();
              $lead_ids = "";

              if(!empty($teleSales) && $teleSales->count() > 0){
                foreach($teleSales AS $teleSale){
                  $lead_ids .= $teleSale->id .",";
                  if($teleSale->status == 'verified'){
                    $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                }
                }
                $lead_ids = rtrim($lead_ids,",");
                $d2dAlert9Critical = isOnSettings($clientId, 'is_critical_alert9_d2d');
                $alert_msg = "Sales agent used an account number that has been used in previous verified leads";
                $message = __('critical_logs.messages.Event_Type_44');
                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_44');
                
                if ($d2dAlert9Critical) {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                } else {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                }
                $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alert_msg);
                
                //Check for alert if auto cancel is on
                if ($d2dAlert9Critical) {
                  $cancelLead = $this->funCancelLead($telesaleTmp->id);
                  if($cancelLead){
                    return $this->error("error", $alert_msg, 500);
                  }else{
                      return $this->error("error", "Something went wrong !", 500);
                  }
                } else if(isOnSettings($clientId, 'is_show_agent_alert9_d2d')) {
                    $validationData['acc_verified']['title'] = 'This account number has been used in previous verified leads.';
                    $validationData['acc_verified']['msg'] = 'Good Sales: '.$aLeadStatus['verified'];
                }
              }
          }

        }

        //  for check setting is on or off
        if ($isOnAlert && isOnSettings($clientId,'is_enable_alert2_d2d')) {

            $intervalDays = getSettingValue($clientId,'interval_days_alert2_d2d',null);
            $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status','verified');
            if (!empty($intervalDays) && $intervalDays > 0) {
                $intervalDate = today()->subDays($intervalDays);
                $teleSales->whereDate('created_at','>=',$intervalDate);
            }
            $aVerifiedLeadData = $teleSales->pluck('id')->toArray(); 
            $verifiedTelesalesData = Telesalesdata::where(function($query) use ($aVerifiedLeadData){
                                                        $query->where('meta_key','first_name')
                                                        ->orWhere('meta_key','last_name');
                                                    })
                                                    ->whereIn('telesale_id',$aVerifiedLeadData)
                                                    ->whereHas('formFieldsData',function($query) {
                                                          $query->where('is_primary',1);
                                                      })
                                                    ->get();
                                                    
            // \Log::info('$verifiedTelesalesData'.print_r($verifiedTelesalesData,true));
            $firstName = '';
            $lastName = '';
              
            $d2dAlert2Critical = isOnSettings($clientId, 'is_critical_alert2_d2d');
            //Check for primary first name anddd last name
            $requestFields = $requestedFields;
            $fullnameIndices = array_keys(array_column($requestFields, 'type'),'fullname');
            $requestFullName = '';
            foreach($fullnameIndices as $index){
                if(isset($requestFields[$index]['meta']['is_primary']) && $requestFields[$index]['meta']['is_primary'] == 1){
                  $requestFullName = (isset($requestFields[$index]['values']['first_name']) && isset($requestFields[$index]['values']['last_name'])) ? $requestFields[$index]['values']['first_name'].' '.$requestFields[$index]['values']['last_name'] : '';
                }
            }
            $lead_ids = "";
            foreach($verifiedTelesalesData AS $verifiedTelesale){
              $verifiedTelesalesFirstName = Telesalesdata::where('meta_key','first_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();
              $verifiedTelesalesLastName = Telesalesdata::where('meta_key','last_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();

              $firstName = $verifiedTelesalesFirstName->meta_value;
              $lastName = $verifiedTelesalesLastName->meta_value;
              $fullName = $firstName .' '.$lastName;
              if($requestFullName == $fullName){
                $lead_ids .= $verifiedTelesale->telesale_id .",";
              }
            }
            $lead_ids = implode(',',array_unique(explode(',',$lead_ids)));
            foreach($verifiedTelesalesData AS $verifiedTelesale){
              
              $verifiedTelesalesFirstName = Telesalesdata::where('meta_key','first_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();
              $verifiedTelesalesLastName = Telesalesdata::where('meta_key','last_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();

              $firstName = $verifiedTelesalesFirstName->meta_value;
              $lastName = $verifiedTelesalesLastName->meta_value;
              $fullName = $firstName .' '.$lastName;
              if($requestFullName == $fullName){
                $alert_msg = "Sales agent submitted an enrollment for an existing customer.";
                // $validationData['name']['title'] = 'This Customer is already enrolled with  '.$fullName;
                $message = __('critical_logs.messages.Event_Type_6');
                
                $lead_ids = $lead_ids;
                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_6');
          
                if ($d2dAlert2Critical) {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                } else {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                }
                $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alert_msg);

                //Check for alert if auto cancel is on
                if ($d2dAlert2Critical) {
                  $cancelLead = $this->funCancelLead($telesaleTmp->id);
                  if($cancelLead){
                    return $this->error("error", $alert_msg, 500);
                  }else{
                    return $this->error("error", "Something went wrong !", 500);
                  }
                } else if(isOnSettings($clientId, 'is_show_agent_alert2_d2d')) {
                  $validationData['name']['title'] = 'This Customer is already enrolled with '.$client->name;
                  $validationData['name']['msg'] = 'There is a verified enrollment associated with this customer and account number.';
                }
                break;
              }else{
                continue;
              }
            }
            \Log::info('$verifiedTelesalesData$validationData'.print_r($validationData,true));
        } else {
            info('Alert 2 settings is switch off.(d2d)');
        }


        // Check validation for #3 Duplicate Email check
        //Check for is primary email

        //  for check setting is on or off
        if ($isOnAlert && isOnSettings($clientId,'is_enable_alert3_d2d')) {

            $d2dAlert3Critical = isOnSettings($clientId, 'is_critical_alert3_d2d');
            $requestFields = $requestedFields;
            $emailIndices = array_keys(array_column($requestFields, 'type'),'email');
            $requestEmail = '';
            // \Log::info('$emailIndices'.print_r($emailIndices,true));
            foreach($emailIndices as $index){
                if(isset($requestFields[$index]['meta']['is_primary']) && $requestFields[$index]['meta']['is_primary'] == 1){
                        $requestEmail = (isset($requestFields[$index]['values']['value'])) ? $requestFields[$index]['values']['value'] : '';
                }
            }
            \Log::info('$requestEmail1'.$requestEmail);
            if($requestEmail != ''){
              $forms = Clientsforms::where('client_id',$client->id)->pluck('id');
              $fieldIds = FormField::where('type','email')->whereIn('form_id',$forms)->pluck('id');
              $teleSalesData = Telesalesdata::where('meta_value', $requestEmail)->whereIn('field_id',$fieldIds)->pluck('telesale_id');

                $intervalDays = getSettingValue($clientId,'interval_days_alert3_d2d',null);
                $teleSales = Telesales::whereIn('id',$teleSalesData);
                if (!empty($intervalDays) && $intervalDays > 0) {
                    $intervalDate = today()->subDays($intervalDays);
                    $teleSales->whereDate('created_at','>=',$intervalDate);
                }
                $teleSales = $teleSales->get();

              $aLeadStatus = array();
              $aLeadStatus['pending'] = 0;
              $aLeadStatus['verified'] = 0;
              $aLeadStatus['decline'] = 0;
              $aLeadStatus['hangup'] = 0;
              $aLeadStatus['cancel'] = 0;
              $aLeadStatus['expired'] = 0;
              // $aLeadStatus['msg'] = '';

              $aVerifiedLeadData = array();
              $totCount = 0;
              $lead_ids = "";
              foreach($teleSales AS $teleSale){
                $lead_ids .= $teleSale->id .",";
                if($teleSale->status == 'verified'){
                  $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                }

                if($teleSale->status == 'pending'){
                  $aLeadStatus['pending'] = isset($aLeadStatus['pending']) ?  $aLeadStatus['pending'] + 1 : 1;
                }

                if($teleSale->status == 'decline'){
                  $aLeadStatus['decline'] = isset($aLeadStatus['decline']) ? $aLeadStatus['decline'] + 1 : 1;
                }

                if($teleSale->status == 'hangup'){
                  $aLeadStatus['hangup'] = isset($aLeadStatus['hangup']) ? $aLeadStatus['hangup'] + 1 : 1;
                }

                if($teleSale->status == 'cancel'){
                  $aLeadStatus['cancel'] = isset($aLeadStatus['cancel']) ? $aLeadStatus['cancel'] + 1 : 1;
                }

                if($teleSale->status == 'expired'){
                  $aLeadStatus['expired'] = isset($aLeadStatus['expired']) ? $aLeadStatus['expired'] + 1 : 1;
                }

                $totCount = $totCount + 1;
              }
              $lead_ids = rtrim($lead_ids,",");
              // \Log::info('$validationData'.print_r($validationData,true));
              if($totCount >= getSettingValue($clientId,'max_times_alert3_d2d')){
                $alert_msg = "Sales agent used an email address that has been used ".$totCount." times.";
                $message = __('critical_logs.messages.Event_Type_7',['count'=>$totCount]);
                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
    			      $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_7');
                
                if ($d2dAlert3Critical) {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                } else {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                }

                $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alert_msg);
                
                //Check for alert if auto cancel is on
                if ($d2dAlert3Critical) {
                  $cancelLead = $this->funCancelLead($telesaleTmp->id);
                  if($cancelLead){
                    return $this->error("error", $alert_msg, 500);
                  }else{
                      return $this->error("error", "Something went wrong !", 500);
                  }
                } else if(isOnSettings($clientId, 'is_show_agent_alert3_d2d')) {
                  $validationData['emailCheck']['title'] = 'This email address has been used '.$totCount.' times.';
                  $validationData['emailCheck']['msg'] = 'Good Sales: '.$aLeadStatus['verified'] .' Pending Leads: '.($aLeadStatus['pending']+$aLeadStatus['hangup']).' Bad Sales: '.$aLeadStatus['decline'].' Cancelled Leads: '.($aLeadStatus['cancel']+$aLeadStatus['expired']);
                }
              }
            }
        } else {
            info('Alert 3 settings is switch off.(d2d)');
        }

        // Check validation for #5 Duplicate Phone check against ALL LEADS in database

        // Check for is primary phone_number

        //  for check setting is on or off
        if ($isOnAlert && isOnSettings($clientId,'is_enable_alert4_d2d')) {
          $d2dAlert4Critical = isOnSettings($clientId, 'is_critical_alert4_d2d');
          $requestFields = $requestedFields;
          $phoneIndices = array_keys(array_column($requestFields, 'type'),'phone_number');
          $requestPhone = '';
          // \Log::info('$phoneIndices'.print_r($phoneIndices,true));
          foreach($phoneIndices as $index){
              if(isset($requestFields[$index]['meta']['is_primary']) && $requestFields[$index]['meta']['is_primary'] == 1){
                      $requestPhone = (isset($requestFields[$index]['values']['value'])) ? $requestFields[$index]['values']['value'] : '';
              }
          }
          // \Log::info('$requestPhone'.print_r($requestPhone,true));
          if($requestPhone != ''){
            $forms = Clientsforms::where('client_id',$client->id)->pluck('id');
            $fieldIds = FormField::where('type','phone_number')->whereIn('form_id',$forms)->pluck('id');
            $teleSalesData = Telesalesdata::where('meta_value', $requestPhone)->whereIn('field_id',$fieldIds)->pluck('telesale_id');

            $intervalDays = getSettingValue($clientId,'interval_days_alert4_d2d',null);
            $teleSales = Telesales::whereIn('id',$teleSalesData);
            if (!empty($intervalDays) && $intervalDays > 0) {
                $intervalDate = today()->subDays($intervalDays);
                $teleSales->whereDate('created_at','>=',$intervalDate);
            }
            $teleSales = $teleSales->get();

            $aLeadStatus = array();
            $aLeadStatus['pending'] = 0;
            $aLeadStatus['verified'] = 0;
            $aLeadStatus['decline'] = 0;
            $aLeadStatus['hangup'] = 0;
            $aLeadStatus['cancel'] = 0;
            $aLeadStatus['expired'] = 0;
            // $aLeadStatus['msg'] = '';

            $aVerifiedLeadData = array();
            $totCount = 0;
            $lead_ids = "";
            foreach($teleSales AS $teleSale){
              $lead_ids .= $teleSale->id .",";
              if($teleSale->status == 'verified'){
                $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
              }

              if($teleSale->status == 'pending'){
                $aLeadStatus['pending'] = isset($aLeadStatus['pending']) ?  $aLeadStatus['pending'] + 1 : 1;
              }

              if($teleSale->status == 'decline'){
                $aLeadStatus['decline'] = isset($aLeadStatus['decline']) ? $aLeadStatus['decline'] + 1 : 1;
              }

              if($teleSale->status == 'hangup'){
                $aLeadStatus['hangup'] = isset($aLeadStatus['hangup']) ? $aLeadStatus['hangup'] + 1 : 1;
              }

              if($teleSale->status == 'cancel'){
                $aLeadStatus['cancel'] = isset($aLeadStatus['cancel']) ? $aLeadStatus['cancel'] + 1 : 1;
              }

              if($teleSale->status == 'expired'){
                $aLeadStatus['expired'] = isset($aLeadStatus['expired']) ? $aLeadStatus['expired'] + 1 : 1;
              }

              $totCount = $totCount + 1;
            }
            // dd($aLeadStatus);
            $lead_ids = rtrim($lead_ids ,",");
            if($totCount >= getSettingValue($clientId,'max_times_alert4_d2d')){
              $alert_msg = "Sales agent used a phone number that has been used ".$totCount." times.";
              $message =  __('critical_logs.messages.Event_Type_9',['count'=>$totCount]);
              $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
              $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_9');
              
              if ($d2dAlert4Critical) {
                $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
              } else {
                $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
              }
              $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alert_msg);
              
              //Check for alert if auto cancel is on
              if ($d2dAlert4Critical) {
                $cancelLead = $this->funCancelLead($telesaleTmp->id);
                if($cancelLead){
                  return $this->error("error", $alert_msg, 500);
                }else{
                  return $this->error("error", "Something went wrong !", 500);
                }
              } else if(isOnSettings($clientId, 'is_show_agent_alert4_d2d')) {
                $validationData['phoneCheck']['title'] = 'This phone number has been used '.$totCount.' times.';
                $validationData['phoneCheck']['msg'] = 'Good Sales: '.$aLeadStatus['verified'] .' Pending Leads: '.($aLeadStatus['pending']+$aLeadStatus['hangup']).' Bad Sales: '.$aLeadStatus['decline'].' Cancelled Leads: '.($aLeadStatus['cancel']+$aLeadStatus['expired']);
              }
            }
          }
      } else {
          info('Alert 4 settings is switch off.(d2d)');
      }

        // Checkpoint #4: Fraudulent Email check against that client’s sales agents (Tele and D2D both) Emails

        //Check for is primary email

        //  for check setting is on or off
        if ($isOnAlert && isOnSettings($clientId,'is_enable_alert5_d2d')) {

            $requestFields = $requestedFields;
            $emailIndices = array_keys(array_column($requestFields, 'type'),'email');
            $requestEmail = '';

            $d2dAlert5Critical = isOnSettings($clientId, 'is_critical_alert5_d2d');

            foreach($emailIndices as $index){
                if(isset($requestFields[$index]['meta']['is_primary']) && $requestFields[$index]['meta']['is_primary'] == 1){
                    $requestEmail = (isset($requestFields[$index]['values']['value'])) ? $requestFields[$index]['values']['value'] : '';
                }
            }
            \Log::info('$emailIndices'.print_r($emailIndices,true));
            if($requestEmail != ''){
              $salesAgents = User::where('client_id',$client->id)
              ->where('email', $requestEmail)
              ->where('access_level','salesagent')
              ->first();
              
              // \Log::info('$salesAgents'.print_r($salesAgents,true));
              // \Log::info('emailssss'.print_r($requestEmail,true));
              if($salesAgents){
                $alert_msg = "Sales agent used an email address belonging to another sales agent.";
                $name = array_get($salesAgents,'full_name').'('.array_get($salesAgents,'userid').')';
                $message =  __('critical_logs.messages.Event_Type_8',['name'=>$name]);
    			$error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
    			$event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_8');
                
                if ($d2dAlert5Critical) {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                } else {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                }
                $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,null,$lead_status,$event_type,$error_type,null,null,$alert_msg);
                
                //Check for alert if auto cancel is on
                if ($d2dAlert5Critical) {
                  $cancelLead = $this->funCancelLead($telesaleTmp->id);
                  if($cancelLead){
                    return $this->error("error", $alert_msg, 500);
                  }else{
                      return $this->error("error", "Something went wrong !", 500);
                  }
                } else if(isOnSettings($clientId, 'is_show_agent_alert5_d2d')) {
                    $validationData['salesEmailCheck']['title'] = 'This email address is associated with an existing '. array_get($client, 'name') . ' sales agent ('. array_get($salesAgents,'full_name') . ' - ID: ' . array_get($salesAgents,'userid') . ')';
                    $validationData['salesEmailCheck']['msg'] = 'There is an active '.array_get($client, 'name') .' sales agent associated with this email address.';
                }
              }

            }
            \Log::info('$validationData'.print_r($validationData,true));
        } else {
            info('Alert 5 settings is switch off.(d2d)');
        }

        

        // Checkpoint #6: Fraudulent Phone check against that client’s sales agents (Tele and D2D both) Phone
        //Check for is primary phone

        //  for check setting is on or off
        if ($isOnAlert && isOnSettings($clientId,'is_enable_alert6_d2d')) {

            $d2dAlert6Critical = isOnSettings($clientId, 'is_critical_alert6_d2d');
            $requestFields = $requestedFields;
            $phoneIndices = array_keys(array_column($requestedFields, 'type'),'phone_number');
            $requestphone = '';

            foreach($phoneIndices as $index){
                if(isset($requestFields[$index]['meta']['is_primary']) && $requestFields[$index]['meta']['is_primary'] == 1){
                    $requestphone = (isset($requestFields[$index]['values']['value'])) ? $requestFields[$index]['values']['value'] : '';
                }
            }
            // \Log::info('$phoneIndices6'.print_r($phoneIndices,true));
            if($requestphone != ''){

              $salesAgents = User::where('client_id',$client->id)
                                      ->where('access_level','salesagent')
                                      ->whereHas('salesAgentDetails', function($q) use($requestphone) {
                                          $q->where('phone_number', $requestphone);
                                      })->first();


              // \Log::info('$salesAgents6'.print_r($salesAgents,true));
              // \Log::info('Phone6'.print_r($requestphone,true));
              if($salesAgents){
                $alert_msg = "Sales agent used a phone number belonging to another sales agent.";
                $name = array_get($salesAgents,'full_name').'('.array_get($salesAgents,'userid').')';
                $message =  __('critical_logs.messages.Event_Type_10',['name'=>$name]);
                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_10');
          
                if ($d2dAlert6Critical) {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                } else {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                }
                $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,null,$lead_status,$event_type,$error_type,null,null,$alert_msg);
              
                //Check for alert if auto cancel is on
                if ($d2dAlert6Critical) {
                  $cancelLead = $this->funCancelLead($telesaleTmp->id);
                  if($cancelLead){
                    return $this->error("error", $alert_msg, 500);
                  }else{
                    return $this->error("error", "Something went wrong !", 500);
                  }
                } else if(isOnSettings($clientId, 'is_show_agent_alert6_d2d')) {
                    $validationData['salesPhoneCheck']['title'] = 'This phone number is associated with an existing '. array_get($client, 'name') . ' sales agent ('. array_get($salesAgents,'full_name') . ' - ID: ' . array_get($salesAgents,'userid') . ').';
                    $validationData['salesPhoneCheck']['msg'] = 'There is an active '.array_get($client, 'name') .' sales agent associated with this phone number.';
                }
              }

            }
        } else {
            info('Alert 6 settings is switch off.(d2d)');
        }
       
        //  for check setting is on or off
        if ($isOnAlert && isOnSettings($clientId,'is_enable_alert10_d2d',false)) {
          $d2dAlert10Critical = isOnSettings($clientId, 'is_critical_alert10_d2d');
          $requestFields = $requestedFields;
          $phoneIndices = array_keys(array_column($requestFields, 'type'),'phone_number');
          $requestPhone = '';
          // \Log::info('$phoneIndices'.print_r($phoneIndices,true));
          foreach($phoneIndices as $index){
              if(isset($requestFields[$index]['meta']['is_primary']) && $requestFields[$index]['meta']['is_primary'] == 1){
                      $requestPhone = (isset($requestFields[$index]['values']['value'])) ? $requestFields[$index]['values']['value'] : '';
              }
          }
          // \Log::info('$requestPhone'.print_r($requestPhone,true));
          if($requestPhone != ''){
            $forms = Clientsforms::where('client_id',$client->id)->pluck('id');
            $fieldIds = FormField::where('type','phone_number')->whereIn('form_id',$forms)->pluck('id');
            $teleSalesData = Telesalesdata::where('meta_value', $requestPhone)->whereIn('field_id',$fieldIds)->pluck('telesale_id');

            $intervalDays = getSettingValue($clientId,'interval_days_alert10_d2d',null);
            $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
            if (!empty($intervalDays) && $intervalDays > 0) {
                $intervalDate = today()->subDays($intervalDays);
                $teleSales->whereDate('created_at','>=',$intervalDate);
            }
            $teleSales = $teleSales->get();
            $aLeadStatus = array();
              $aLeadStatus['verified'] = 0;
              $aVerifiedLeadData = array();
              $lead_ids = "";
            if(isset($teleSales) && $teleSales->count() > 0){
              foreach($teleSales AS $teleSale){
                $lead_ids .= $teleSale->id .",";
                if($teleSale->status == 'verified'){
                  $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                }
              }
                $lead_ids = rtrim($lead_ids ,",");
                $alert_msg = "Sales agent used a phone number that has been used in previous verified leads.";
                $message =  __('critical_logs.messages.Event_Type_45',['count'=>$totCount]);
                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_45');
                
                if ($d2dAlert10Critical) {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                } else {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                }
                $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alert_msg);
                
                //Check for alert if auto cancel is on
                if ($d2dAlert10Critical) {
                  $cancelLead = $this->funCancelLead($telesaleTmp->id);
                  if($cancelLead){
                    return $this->error("error", $alert_msg, 500);
                  }else{
                    return $this->error("error", "Something went wrong !", 500);
                  }
                } else if(isOnSettings($clientId, 'is_show_agent_alert10_d2d')) {
                  $validationData['phoneCheck_verified']['title'] = 'This phone number has been used in previous verified leads.';
                  $validationData['phoneCheck_verified']['msg'] = 'Good Sales: '.$aLeadStatus['verified'];
                }
            }
            
          }
      } else {
          info('Alert 10 settings is switch off.(d2d)');
      }

       //  for check setting is on or off
       if ($isOnAlert && isOnSettings($clientId,'is_enable_alert12_d2d',false)) {
        $d2dAlert12Critical = isOnSettings($clientId, 'is_critical_alert12_d2d');
        $requestFields = $requestedFields;
        $phoneIndices = array_keys(array_column($requestFields, 'type'),'phone_number');
        $requestPhone = '';
        // \Log::info('$phoneIndices'.print_r($phoneIndices,true));
        foreach($phoneIndices as $index){
            if(isset($requestFields[$index]['meta']['is_primary']) && $requestFields[$index]['meta']['is_primary'] == 1){
                    $requestPhone = (isset($requestFields[$index]['values']['value'])) ? $requestFields[$index]['values']['value'] : '';
            }
        }
        // \Log::info('$requestPhone'.print_r($requestPhone,true));
        if($requestPhone != ''){
          $forms = Clientsforms::where('client_id',$clientId)->pluck('id');
          $fieldIds = FormField::where('type','phone_number')->whereIn('form_id',$forms)->pluck('id');
          $teleSalesData = Telesalesdata::where('meta_value', $requestPhone)->whereIn('field_id',$fieldIds)->pluck('telesale_id');

          $intervalDays = getSettingValue($clientId,'interval_days_alert12_d2d',null);
          $teleSales = Telesales::whereIn('id',$teleSalesData)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
          if (!empty($intervalDays) && $intervalDays > 0) {
              $intervalDate = today()->subDays($intervalDays);
              $teleSales->whereDate('created_at','>=',$intervalDate);
          }
          $aVerifiedLeadData = $teleSales->pluck('id');                                        
                        
          $verifiedTelesalesData = Telesalesdata::where(function($query) use ($aVerifiedLeadData){
                                      $query->where('meta_key','first_name')
                                      ->orWhere('meta_key','last_name');
                                  })
                                  ->whereIn('telesale_id',$aVerifiedLeadData)
                                  ->whereHas('formFieldsData',function($query) {
                                      $query->where('is_primary',1);
                                  })
                                  ->get();
          $firstName = '';
          $lastName = '';

          //Check for primary first name anddd last name
         // $requestFields = $request->fields[0];
          $fullnameIndices = array_keys(array_column($requestFields, 'type'),'fullname');
          
          $requestFullName = '';
          foreach($fullnameIndices as $index){
            
              if(isset($requestFields[$index]['meta']['is_primary']) && $requestFields[$index]['meta']['is_primary'] == 1){
                      $requestFullName = (isset($requestFields[$index]['values']['first_name']) && isset($requestFields[$index]['values']['last_name'])) ? $requestFields[$index]['values']['first_name'].' '.$requestFields[$index]['values']['last_name'] : '';
              }
          }
          $lead_ids = "";
          $critical_message = "";

          foreach($verifiedTelesalesData AS $verifiedTelesale)
          {
            $verifiedTelesalesFirstName = Telesalesdata::where('meta_key','first_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();
            $verifiedTelesalesLastName = Telesalesdata::where('meta_key','last_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();

            $firstName = $verifiedTelesalesFirstName->meta_value;
            $lastName = $verifiedTelesalesLastName->meta_value;
            $fullName = $firstName .' '.$lastName;
            if($requestFullName == $fullName){
              $lead_ids .= $verifiedTelesale->telesale_id .",";              
            }
          }
          $lead_ids = implode(',',array_unique(explode(',',$lead_ids)));
          foreach($verifiedTelesalesData AS $verifiedTelesale){
          
            $verifiedTelesalesFirstName = Telesalesdata::where('meta_key','first_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();
            $verifiedTelesalesLastName = Telesalesdata::where('meta_key','last_name')->where('telesale_id',$verifiedTelesale->telesale_id)->first();

            $firstName = $verifiedTelesalesFirstName->meta_value;
            $lastName = $verifiedTelesalesLastName->meta_value;
            $fullName = $firstName .' '.$lastName;
            if($requestFullName == $fullName){
                $alert_msg = 'Sales agent submitted an enrollment for an existing customer in previous verified leads.';
                $message =  __('critical_logs.messages.Event_Type_46');
                
    			$lead_ids = $lead_ids;
    			$error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
          $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_46');
          
                if ($d2dAlert12Critical) {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                } else {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                }
                $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alert_msg);
              
                //Check for alert if auto cancel is on
                if ($d2dAlert12Critical) {
                  $cancelLead = $this->funCancelLead($telesaleTmp->id);
                  if($cancelLead){
                    return $this->error("error", $alert_msg, 500);
                  }else{
                    return $this->error("error", "Something went wrong !", 500);
                  }
                }

                if(isOnSettings($clientId,'is_show_agent_alert12_d2d')) {
                    $validationData['name_phone_verified']['title'] = 'This Customer is already enrolled with '.$client->name;
                    $validationData['name_phone_verified']['msg'] = 'There is a verified enrollment associated with this customer and phone number.';
                }
                break;
            }else{
                continue;
            }
          }
          // \Log::info('$verifiedTelesalesData$validationData'.print_r($validationData,true));
      }
    } else {
        info('Alert 12 settings is switch off.(d2d)');
    }

    if($isOnAlert && isOnSettings($clientId,'is_enable_alert13_d2d',false)){
                            
      $requestFields = $requestedFields;
    
      $addressIndices = array_keys(array_column($requestedFields, 'type'),'address');
      $field_type = 'address';
      if(empty($addressIndices)){
      $addressIndices = array_keys(array_column($requestedFields, 'type'),'service_and_billing_address');
      $field_type = 'service_and_billing_address';
      }
      // \Log::info('$addressIndices'.print_r($field_type,true));
      $serviceAddress1 = '';
      $serviceAddress2 = '';
      $serviceCity = '';
      $serviceCounty = '';
      $serviceState = '';
      $serviceZipcode = '';
      $serviceCountry = '';
      // \Log::info('$addressIndices'.print_r($addressIndices,true));
      foreach($addressIndices as $index){
          if($field_type == 'service_and_billing_address'){
                  $serviceAddress1 = strtolower($requestFields[$index]['values']['service_address_1']);
                  $serviceAddress2 = strtolower($requestFields[$index]['values']['service_address_2']);
                  $serviceCity = strtolower($requestFields[$index]['values']['service_city']);
                  $serviceCounty = strtolower($requestFields[$index]['values']['service_county']);
                  $serviceState = strtolower($requestFields[$index]['values']['service_state']);
                  $serviceZipcode = strtolower($requestFields[$index]['values']['service_zipcode']);
                  $serviceCountry = strtolower($requestFields[$index]['values']['service_country']);
          }
          if($field_type == 'address'){
              $serviceAddress1 = strtolower($requestFields[$index]['values']['address_1']);
              $serviceAddress2 = strtolower($requestFields[$index]['values']['address_2']);
              $serviceCity = strtolower($requestFields[$index]['values']['city']);
              $serviceCounty = strtolower($requestFields[$index]['values']['county']);
              $serviceState = strtolower($requestFields[$index]['values']['state']);
              $serviceZipcode = strtolower($requestFields[$index]['values']['zipcode']);
              $serviceCountry = strtolower($requestFields[$index]['values']['country']);
          }
          }
      
      $aLeadStatus = array();
      $aLeadStatus['verified'] = 0;
      $aVerifiedLeadData = array();
      $lead_ids = "";
      if($serviceAddress1 != ''){
        $forms = Clientsforms::where('client_id',$clientId)->pluck('id');
        $fieldIds = FormField::whereIn('form_id',$forms)->where(function ($q) {
            $q->where('type', '=', 'address')
            ->orWhere('type', '=', 'service_and_billing_address');
        })
        ->where('is_primary', '=', '1')->pluck('id');
        $telesalesData = Telesalesdata::whereIn('field_id',$fieldIds)->where(function ($query) {
            $query->whereIn('meta_key',['service_address_1','service_address_2','service_city','service_county','service_state','service_zipcode','service_country'])
            ->orWhereIn('meta_key',['address_1','address_2','city','county','state','zipcode','country']);
        })
       ->get();
     
        $lead_ids = "";
        $critical_message = "";
       

        foreach($telesalesData AS $teleData)
        {
            if($teleData->meta_key == "service_address_1")
            $lead_ids .= $teleData->telesale_id .",";   
            if($teleData->meta_key == "address_1")
            $lead_ids .= $teleData->telesale_id .",";   
               
        }
        $teleSalesIds = explode(",",$lead_ids);
       
        $telesales_id = [];
       
        foreach($teleSalesIds AS $teleId){
        if(!empty($teleId)){
        $verifiedTelesalesServiceAddress1 = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
            $query->where('meta_key','service_address_1')->orWhere('meta_key','address_1');
        })->first();
        $verifiedTelesalesServiceAddress2 = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
            $query->where('meta_key','service_address_2')->orWhere('meta_key','address_2');
        })->first();
        $verifiedTelesalesServiceCity = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
            $query->where('meta_key','service_city')->orWhere('meta_key','city');
        })->first();
        $verifiedTelesalesServiceCounty = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
            $query->where('meta_key','service_county')->orWhere('meta_key','county');
        })->first();
        $verifiedTelesalesServiceState = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
            $query->where('meta_key','service_state')->orWhere('meta_key','state');
        })->first();
        $verifiedTelesalesServiceZipcode = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
            $query->where('meta_key','service_zipcode')->orWhere('meta_key','zipcode');
        })->first();
        $verifiedTelesalesServiceCountry = Telesalesdata::where('telesale_id',$teleId)->where(function($query){
            $query->where('meta_key','service_country')->orWhere('meta_key','country');
        })->first();
       
        if((strtolower(trim($verifiedTelesalesServiceAddress1->meta_value)) == $serviceAddress1) && (strtolower(trim($verifiedTelesalesServiceAddress2->meta_value)) == $serviceAddress2) && (strtolower(trim($verifiedTelesalesServiceCity['meta_value'])) == $serviceCity)
        && (strtolower(trim($verifiedTelesalesServiceCounty['meta_value'])) == $serviceCounty) && (strtolower(trim($verifiedTelesalesServiceState->meta_value)) == $serviceState) && (strtolower(trim($verifiedTelesalesServiceZipcode->meta_value)) == $serviceZipcode) && (strtolower(trim($verifiedTelesalesServiceCountry->meta_value)) == $serviceCountry)){
           
            $telesales_id[] = $teleId;
        }
        else{
            
            continue;
        }
    }
    }
    // \Log::info('$teleSalesIds Array'.print_r($telesales_id,true));  
    if(!empty($telesales_id)){
      $lead_ids = '';
            $intervalDays = getSettingValue($clientId,'interval_days_alert13_d2d',null);
            $teleSales = Telesales::whereIn('id',$telesales_id)->where('status',config('constants.LEAD_TYPE_VERIFIED'));
            if (!empty($intervalDays) && $intervalDays > 0) {
                $intervalDate = today()->subDays($intervalDays);
                $teleSales->whereDate('created_at','>=',$intervalDate);
            }
            $teleSales = $teleSales->get(); 
            // \Log::info('$teleSalesin'.print_r($teleSales,true));     
            if(!empty($teleSales->toArray()) && $teleSales->count() > 0){
                foreach($teleSales->toArray() as $teleSale){
                    // \Log::info('$teleSalesFor'.print_r($teleSales,true));   
                    
                    if($teleSale['status'] == 'verified'){
                      $lead_ids .= $teleSale['id'] . ",";
                        $aLeadStatus['verified'] = 'Verified Leads: '.isset($aLeadStatus['verified']) ? $aLeadStatus['verified'] + 1 : 1;
                    }
                } 
                // \Log::info(' $aLeadStatus total'.print_r($aLeadStatus['verified'],true));                  
                $d2dAlert13Critical = isOnSettings($clientId, 'is_critical_alert13_d2d');
                $alerts = 'Sales agent used service address that has been used in previous verified leads.';
                $message = __('critical_logs.messages.Event_Type_47');
                $lead_ids = rtrim($lead_ids,","); 
                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_47');

                if ($d2dAlert13Critical) {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
                } else {
                  $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
                }
                $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(Auth::user()->id,$message,null,$telesaleTmp->id,$lead_ids,$lead_status,$event_type,$error_type,null,null,$alerts);

                //Check for alert if auto cancel is on
                if ($d2dAlert13Critical) {
                  $cancelLead = $this->funCancelLead($telesaleTmp->id);
                  if($cancelLead){
                    return $this->error("error", $alerts, 500);
                  }else{
                  \Log::info("Error");
                    return $this->error("error", "Something went wrong !", 500);
                  }
                }
                else if(isOnSettings($clientId,'is_show_agent_alert13_d2d')) {
                  $validationData['address_verified']['title'] = 'This service address has been used in previous verified leads';
                  $validationData['address_verified']['msg'] = 'Verified Leads: '.$aLeadStatus['verified'];
                }
            }
        }
      }
      
	}else {
		info('Alert 13 settings is switch off.(d2d)');
	}
        /** Validation Code end*/
        // dd($validationData);
        $data = [];
        $data['lead_temp_id'] = optional($telesaleTmp)->id;

        if(!empty($validationData)){
          $data['errors'] = array_values($validationData);

          return $this->success("success", "Validation error message found !!", $data);
        } else{
          return $this->success("success", "Validation not found", $data);
        }

     } catch (\Exception $e) {
       Log::error("Error while creating lead: " . $e);
       return $this->error("error", "Something went wrong while creating lead !!", 500);
     }
    }

    /**
     * For cancel particular lead
     * @param $id, $disposition
     */
    public function cancelLead($id,$disposition = null)
    {
      try {
        if($id){
          $teleSaleTmp = TelesalesTmp::find($id);
          $teleSaleDataTmp = TelesalesdataTmp::where('telesaletmp_id',$id)->get();

          // Check for temp data is available or not
          if($teleSaleTmp && $teleSaleDataTmp){

            $zipcode = isset($teleSaleTmp->zipcode) ? $teleSaleTmp->zipcode : null;
            $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();

            if (empty($zipcodeData)) {
              return $this->error("error", 'Please enter a valid zipcode', 400);
            }

            $reqPrograms = explode(',',$teleSaleTmp->program);
            \Log::info('$reqPrograms'.print_r($reqPrograms,true));
            if (is_array($reqPrograms)) {
                foreach ($reqPrograms as $pId) {
                    $program = Programs::find($pId);
                    if (empty($program)) {
                       return $this->error("error", 'This program was not found.', 400);
                    }
                }
            } else {
              return $this->error("error", 'Please select a valid program.', 400);
            }
            $reason = '';
            if (!empty($disposition)) {
                $reason = $disposition->description;
            }
            $telesale = $this->createLead($teleSaleTmp,$zipcodeData,$reqPrograms,'cancel', true, $reason);
            $referenceId = $telesaleId ='';
            if(!empty($telesale)) {
                $telesaleId = $telesale->id;
                $referenceId = $telesale->refrence_id;
                if (!empty($disposition)) {
                    $telesale->disposition_id = $disposition->id;
                    $telesale->cancel_reason = $reason;
                    $telesale->save();
                }
            }

            $this->sendCriticalAlertMail($telesale);
            $data = [];
            $data['id'] = $telesaleId;
            $data['reference_id'] = $referenceId;
            return $this->success("success", "Lead Cancelled successfully !!", $data, []);
          }else{
              return $this->error("error", 'Telesales Data not found.', 400);
          }

        }else{
            return $this->error("error", 'Something went wrong.', 400);
        }
      } catch (\Exception $e) {
        Log::error("Error while creating lead: " . $e->getMessage());
    		return $this->error("error", "Something went wrong while creating lead !!", 500);
      	}
    }

    /**
     * For cancel particular lead with e-signature
     * @param $request, $id
     */
    public function cancelLeadPost(Request $request,$id)
    {
		try {
			if($request->has('source'))
			{
				if($request->source == 'e-signature')
				{
                    $teleSaleTmp = TelesalesTmp::find($id);
                    $disposition = Dispositions::where('client_id', array_get($teleSaleTmp,'client_id'))->where('type','esignature_cancel')->first();
                    $this->cancelLead($id, $disposition);
					return $this->success("success", "Lead Cancelled successfully.");
				}
				elseif($request->source == 'alert')
				{
					$this->cancelLead($id);
					return $this->success("success", "Lead Cancelled successfully.");
				}
			}
		  } catch (\Exception $e) {
			Log::error("Error while creating lead: " . $e->getMessage());
				return $this->error("error", "Something went wrong while creating lead !!", 500);
			  }
    }

    /**
     * protected fun for cancel lead
     */
    protected function funCancelLead($id, $dispositionId=null)
    {
      try {
        if($id){

          $teleSaleTmp = TelesalesTmp::find($id);
          // Check for temp data is available or not
          if($teleSaleTmp){
            $zipcode = isset($teleSaleTmp->zipcode) ? $teleSaleTmp->zipcode : null;
            $zipcodeData = Zipcodes::where('zipcode', $zipcode)->first();

            if (empty($zipcodeData)) {
              return $this->error("error", 'Please enter a valid zipcode', 400);
            }

            $reqPrograms = explode(',',$teleSaleTmp->program);
            \Log::info('$reqPrograms'.print_r($reqPrograms,true));
            if (is_array($reqPrograms)) {
                foreach ($reqPrograms as $pId) {
                    $program = Programs::find($pId);
                    if (empty($program)) {
                       return $this->error("error", 'This program was not found.', 400);
                    }
                }
            } else {
              return $this->error("error", 'Please select a valid program.', 400);
            }
            
            $telesale = $this->createLead($teleSaleTmp,$zipcodeData,$reqPrograms,'cancel',true,'',0,$dispositionId);

            $this->sendCriticalAlertMail($telesale); 
            return true;
          }else{

              return false;
          }

        }else{
            return false;
        }
      } catch (\Exception $e) {
        Log::error("Error while creating lead: " . $e->getMessage());
        return false;
      }
      return false;

	}

    /**
     * For proceedToSegment
     * @param $leadId
     */
    public function proceedToSegment($leadId) {
        
        $lead = Telesales::with('teleSalesData')->find($leadId);
        $segmentService = new SegmentService;
        $segmentService->createIdentity($lead);
        $trackCreated = $segmentService->createTrack($lead);
        if ($trackCreated) {
            \Log::info("Segment track of lead creation created for lead: " . array_get($lead, 'id'));
        } else {
            \Log::error("Unable to create track of lead creation for lead: " . array_get($lead, 'id'));
        }
    }

    /**
     * store sales agent location when lead created
     * @param $lead
     */
    public function storeSalesAgentLocation($lead)
    {
        $salesagent_location = new Salesagentlocation();
        $salesagent_location->lat = array_get($lead, 'salesagent_lat');
        $salesagent_location->lng = array_get($lead, 'salesagent_lng');
        $salesagent_location->salesagent_id = \Auth::id();
        $salesagent_location->save();
    }

    /**
     * for move signature leadmedia_temp to leadmedia
     * @param $leadId
     * @param $tmpLeadId
     * @return bool
     */
    public function tempToParmanentSignature($leadId, $tmpLeadId)
    {
        try {
            $tmpLeadMedia = LeadmediaTemp::where('telesales_tmp_id',$tmpLeadId)->where('type','image')->first();
            if(!empty($tmpLeadMedia)) {
                $leadMedia = new Leadmedia();
                $leadMedia->name = $tmpLeadMedia->name;
                $leadMedia->type = $tmpLeadMedia->type;
                $leadMedia->media_type = $tmpLeadMedia->type;
                $leadMedia->url = $tmpLeadMedia->url;
                $leadMedia->telesales_id = $leadId;
                $leadMedia->save();
                $this->tempToParmanentSignature2($leadId, $tmpLeadId);
                return true;
            } else {
                Log::info('signature not found.');  
                return false;
            }            
        } catch (\Exception $e) {
            Log::error('Error while move temp to parmanent signature: '.$e);
            return false;
        }
    }

    /**
     * for move signature and acknowledgement leadmedia_temp to leadmedia
     * @param $leadId
     * @param $tmpLeadId
     * @return bool
     */
    public function tempToParmanentSignature2($leadId, $tmpLeadId)
    {
        try {
            $tmpLeadMedias = LeadmediaTemp::where('telesales_tmp_id',$tmpLeadId)->whereIn('type',['signature2','acknowledgement'])->get();
            if(!$tmpLeadMedias->isEmpty()) {
                foreach ($tmpLeadMedias as $key => $tmpLeadMedia) {
                    $fileName = explode(".", $tmpLeadMedia->name);                    
                    $leadMedia = new Leadmedia();
                    $leadMedia->name = $leadId."_".date('Y_m_d_H_i').'.'.end($fileName);
                    $leadMedia->type = $tmpLeadMedia->type;
                    $leadMedia->media_type = ($tmpLeadMedia->type == 'acknowledgement') ? 'pdf':'image';
                    $leadMedia->url = $tmpLeadMedia->url;
                    $leadMedia->telesales_id = $leadId;
                    $leadMedia->save();
                }

                return true;
            } else {
                Log::info('signature 2 not found.');  
                return false;
            }            
        } catch (\Exception $e) {
            Log::error('Error while move temp to parmanent signature 2: '.$e);
            return false;
        }
    }

    /**
     * for send contract pdf
     * @param $leadId
     */
    public function sendContract($leadId)
    {
        try {
            $telesales = Telesales::find($leadId);
            $serviceLatLng = DB::table('telesales')
                ->select(
                    DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_lat' and telesale_id =telesales.id LIMIT 1) as ServiceLat"),
                    DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'service_and_billing_address' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'service_lng' and telesale_id =telesales.id LIMIT 1) as ServiceLng"))
                ->where('telesales.id',array_get($telesales, 'id'))->get();

            if (!$serviceLatLng->isEmpty() && !empty($telesales)) {
                $key = config()->get('constants.GOOGLE_MAP_API_KEY');

                // Get google location image                
                $url= 'https://maps.googleapis.com/maps/api/staticmap?'.'&key='.$key.'&size=250x170&maptype=roadmap&markers=color:blue%7Clabel:Serviceaddress%7C'.$serviceLatLng[0]->ServiceLat.','.$serviceLatLng[0]->ServiceLng;

                if (!empty($telesales->salesagent_lat) && !empty($telesales->salesagent_lng)) {
                    $url .='&markers=color:red%7Clabel:Salesagentlocation%7C'.$telesales->salesagent_lat.','.$telesales->salesagent_lng;
                }
                Log::info("contract pdf map url:- ".$url);

                $awsFolderPath = config()->get('constants.aws_folder');
                $filePath = config()->get('constants.GPS_LOCATION_IMAGE_UPLOAD_PATH');
                $fileName = uniqid() . '_' . $telesales->refrence_id.'.png';
                $storageService = new StorageService;
                $imageUploaded = $storageService->uploadFileToStorage(file_get_contents($url), $awsFolderPath, $filePath, $fileName);

                if($imageUploaded)
                {
                    $telesales->gps_location_image = $imageUploaded;
                    $telesales->save();
                }

                if (!isOnSettings(array_get($telesales, 'client_id'), 'is_enable_send_contract_after_lead_verify_d2d',false)) {
                    Log::info('Save Lead Contract PDF');
                   

                    //check whether this lead has child leads or not
                    // $isChildExist = $telesales->childLeads()->get();
                    // //if there are child leads then generate child leads contract 
                    // if(isset($isChildExist) && $isChildExist->count() > 0){
                    //     foreach ($isChildExist as $key => $val) {
                    //         SendContractPDF::dispatch($val->id,$telesales->salesagent_lat, $telesales->salesagent_lng,'child');
                    //     }
                    // }
                    //send parent lead contract
                    SendContractPDF::dispatch($telesales->id, $telesales->salesagent_lat, $telesales->salesagent_lng);
                }
            }           

        } catch (\Exception $e) {
            Log::error('Error while send contract pdf: '.$e);
        }
    }

    /**
     * for send signature link on email or phone
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSignatureLink(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'tmp_lead_id' => 'required',
                'mode' => 'required',
            ]);

            if ($validator->fails()) {                
                return $this->error("error", implode(',', $validator->messages()->all()), 400);                
            } else {
                $tmpLeadId = $request->tmp_lead_id;
                $convertToLower = str_replace("sms", "phone", strtolower($request->mode));
                $mode = explode(',', $convertToLower);

                Log::info(print_r($mode,true));

                if (!in_array('email', $mode) && !in_array('phone', $mode)) {
                    return $this->error("error", "The selected mode is invalid.", 400);
                }

                $tmpLead = TelesalesTmp::find($tmpLeadId);
                
                if (!empty($tmpLead)) {
                    $email = "";
                    $fullName="";
                    $phoneNumber = "";
                    $formId = $tmpLead->form_id;

                    $fullNameField = FormField::whereHas('form', function ($query) use ($formId) {
                        $query->where('id', $formId);
                    })->where('type', 'fullname')->where('is_primary', 1)->first();

                    if (!empty($fullNameField)) {
                        $field = TelesalesdataTmp::where('field_id', $fullNameField->id)->where('meta_key', 'first_name')->where('telesaletmp_id', $tmpLeadId)->first();
                        if ($field) {
                            $fullName = ucfirst($field->meta_value);
                        }
                    }

                    $encodedTmpLeadId = encode($tmpLeadId);
                    $url= route('signature.create',$encodedTmpLeadId);

                    // for send signature link in email
                    if (in_array('email', $mode)) {
                        $emailField = FormField::whereHas('form', function ($query) use ($formId) {
                            $query->where('id', $formId);
                        })->where('type', 'email')->where('is_primary', 1)->first();

                        if (!empty($emailField)) {
                            $field = TelesalesdataTmp::where('field_id', $emailField->id)->where('telesaletmp_id', $tmpLeadId)->first();
                            if ($field) {
                                $email = $field->meta_value;
                            }
                        }

                        if (!empty($email)) {
                            $this->sendSignatureLinkEmail($email, $url, $fullName);
                            Log::info("Signature email sent");
                        } else {
                          Log::info("Email address not found");
                        }
                    }

                    // for send signature link in phone
                    if (in_array('phone', $mode)) {

                        $phoneNumberField = FormField::whereHas('form', function ($query) use ($formId) {
                                $query->where('id', $formId);
                            })->where('type', 'phone_number')->where('is_primary', 1)->first();

                        if (!empty($phoneNumberField)) {
                            $field = TelesalesdataTmp::where('field_id', $phoneNumberField->id)->where('telesaletmp_id', $tmpLeadId)->first();
                            if ($field){
                                $phoneNumber = $field->meta_value;
                                $phoneNumber = '+1' . preg_replace("/[^0-9\.]/", '', $phoneNumber);
                            }

                        }

                        if (!empty($phoneNumber)) {
                            $this->sendSignatureLinkSMS($phoneNumber, $url, $fullName);
                            Log::info("Signature SMS sent");
                        } else {
                          Log::info("Phone number not found");
                        }
                    }
                    Log::info("Signature link successfully sent.");
                    return $this->success("success", "Signature link successfully sent.");
                } else {
                    return $this->error("error", 'Invalid tmp lead id.', 400);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error while send signature link: '.$e);
            return $this->error("error", 'Something went wrong.', 400);
        }
    }

    /**
     * for verify signature is uploaded or not
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifySignature(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'tmp_lead_id' => 'required',
        ]);

        if ($validator->fails()) {                
            return $this->error("error", implode(',', $validator->messages()->all()), 400);                
        } else {
            $isExists = LeadmediaTemp::where('telesales_tmp_id',$request->tmp_lead_id)->where('type','image')->exists();
            if ($isExists) {
                $message = "Signature has been uploaded.";
            } else {
                $message = "Signature has been not uploaded.";
            }
            return response()->json([
                'status' => "success",
                'message' => $message,
                'data' => [
                    'is_signature_captured' => $isExists
                ]
            ], 200);
        }
    }
}