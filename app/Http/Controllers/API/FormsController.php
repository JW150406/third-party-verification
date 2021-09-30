<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Client;
use App\models\Programs;
use App\models\Utilities;
use Log;
use App\models\Clientsforms;

class FormsController extends Controller
{
    /**
     * For get form as per client
     * @param $id
     */
    public function index($id) {
    	try {
    		$client = Client::find($id);
    		$forms = $client->forms()->with(['commodities' => function($query) {
            			$query->select('commodities.id', 'commodities.name');
            		}])->select('id', 'formname')->where('status', 'active')->whereIn('channel', ['BOTH', 'MOBILE'])->get();
    		Log::info("Client forms retrieved for client id: " . $id);
    		return $this->success("success", "Client forms retrieved !!", $forms);
    	} catch (\Exception $e) {
    		Log::error("Error while retrieving forms for client with id " . $id . " :" . $e->getMessage());
    		return $this->error("error", "Something went wrong, Please try again later !!", 500);
    	} 
    }

    /**
     * This method is used for get all the field details of form
     * @param $request
     */
    public function	details(Request	$request)
    {
        try {
    		$validator = \Validator::make($request->all(), [
	            'form_id' => 'required'
	        ]);

	        if ($validator->fails()) {
				return $this->error("error", implode(',',$validator->messages()->all()), 500); 
	        }

	        $form = Clientsforms::with('fields')->find($request->form_id);

            if (empty($form)) {
                return $this->error("error", 'Form not available', 400);
            }

	        $fields = $form->fields()->select('id', 'type', 'label', 'meta', 'is_required', 'is_primary', 'is_verify', 'is_allow_copy','regex','regex_message','is_auto_caps')->orderBy('position', 'asc')->get();

	        
            $response = [];

            foreach ($fields as $fiKey => $fiValue) {
                $resArr = [];
                $resArr['id'] = array_get($fiValue, 'id');
                $resArr['type'] = array_get($fiValue, 'type');
                if (array_get($fiValue, 'type') == 'textbox') {
                    $resArr['label'] = array_get($fiValue, 'label');//getAccountNumberLabel(array_get($fiValue, 'label'));
                } else {
                    $resArr['label'] = array_get($fiValue, 'label');
                }

                if ($fiValue['type'] == "phone_number" || $fiValue['type'] == "fullname" || $fiValue['type'] == "service_and_billing_address" || $fiValue['type'] == "address" || $fiValue['type'] == "email") {
                    $resArr['meta']['is_primary'] = $fiValue['is_primary'] ? true : false;
                    $resArr['meta']['is_allow_copy'] = $fiValue['is_allow_copy'] ? true : false;
                    $resArr['meta']['is_auto_caps'] = $fiValue['is_auto_caps'] ? true : false;
                } else {
                    $resArr['meta'] = array_get($fiValue, 'meta');
                }

                if ($fiValue['type'] == "textbox" || $fiValue['type'] == "textarea"){
                    $resArr['meta']['is_auto_caps'] = $fiValue['is_auto_caps'] ? true : false;
                }

                if ($fiValue['type'] == "selectbox" || $fiValue['type'] == "radio" || $fiValue['type'] == "checkbox") {
                    $resArr['meta']['style_as_a_button'] = false;
                }

                switch ($fiValue['type']) {
                    case 'fullname':
                        $resArr['values']["first_name"] = "";
                        $resArr['values']["middle_initial"] = "";
                        $resArr['values']["last_name"] = "";
                        break;
                    
                    case "service_and_billing_address":
                        $resArr['values']["billing_address_1"] = "";
                        $resArr['values']["billing_address_2"] = "";
                        $resArr['values']["billing_zipcode"] = "";
                        $resArr['values']["billing_city"] = "";
                        $resArr['values']["billing_county"] = "";
                        $resArr['values']["billing_state"] = "";
                        $resArr['values']["service_address_1"] = "";
                        $resArr['values']["service_address_2"] = "";
                        $resArr['values']["service_zipcode"] = "";
                        $resArr['values']["service_city"] = "";
                        $resArr['values']["service_county"] = "";
                        $resArr['values']["service_state"] = "";
                        $resArr['values']["billing_unit"] = "";
                        $resArr['values']["service_unit"] = "";
                        $resArr['values']["billing_country"] = "";
                        $resArr['values']["service_country"] = "";
                        $resArr['values']["billing_lat"] = "";
                        $resArr['values']["billing_lng"] = "";
                        $resArr['values']["service_lat"] = "";
                        $resArr['values']["service_lng"] = "";
                        $label = array_get($fiValue, 'label');
                        $labelArray = explode('-',$label);
                        is_array($labelArray) ? $resArr['label'] = trim($labelArray[0]) : $resArr['label'] = $label;
                        break;

                    case "address":
                        $resArr['values']["unit"] = "";
                        $resArr['values']["address_1"] = "";
                        $resArr['values']["address_2"] = "";
                        $resArr['values']["zipcode"] = "";
                        $resArr['values']["city"] = "";
                        $resArr['values']["county"] = "";
                        $resArr['values']["state"] = "";
                        $resArr['values']["country"] = "";
                        $resArr['values']["lat"] = "";
                        $resArr['values']["lng"] = "";
                        break;

                    case "radio":
                    case "checkbox":
                    case "selectbox":
                    case "separator":
                    case "heading":
                    case "label":
                        $resArr['values'] = NULL;
                        break;

                    default:
                        $resArr['values']["value"] = "";
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
                        $resArr['validations']["required"] = ($fiValue['is_required']) ? true : false;
                        $resArr['validations']["regex"] = ($fiValue['regex']) ? $fiValue['regex'] : '';
                        $resArr['validations']["regex_message"] = ($fiValue['regex_message']) ? $fiValue['regex_message'] : '';
                        $resArr['meta']['is_primary'] = (strtolower($fiValue['label']) == "account number") ? true : false;
                        $resArr['meta']['is_allow_copy'] = $fiValue['is_allow_copy'] ? true : false;
                      break;

                    case 'textarea':
                        $resArr['validations']["required"] = ($fiValue['is_required']) ? true : false;
                        $resArr['validations']["length"] = 0;
                        $resArr['validations']["required"] = ($fiValue['is_required']) ? true : false;
                        $resArr['validations']["regex"] = ($fiValue['regex']) ? $fiValue['regex'] : '';
                        $resArr['validations']["regex_message"] = ($fiValue['regex_message']) ? $fiValue['regex_message'] : '';
                        $resArr['meta']['is_allow_copy'] = $fiValue['is_allow_copy'] ? true : false;
                    break;

                    case "checkbox":
                    case "radio":
                    case "address":
                    case "service_and_billing_address":
                    case 'fullname':
                    case "selectbox":
                    case "email":
                        $resArr['validations']["required"] = ($fiValue['is_required']) ? true : false;
                        $resArr['validations']["verify"] = ($fiValue['is_verify']) ? true : false;
                        break;
                    
                }

                $response[] = $resArr;
            }

            // $data['form_data'] = $response;
            // $data['is_enable_enroll_by_state'] = isEnableEnrollByState($form->client_id);

	        Log::info("Forms retrieved for with id: " . $request->form_id);
    		return $this->success("success", "success", $response);
    	} catch (\Exception $e) {
    		Log::error("Error while retrieving form with id " . $request->form_id . " :" . $e->getMessage());
    		return $this->error("error", "Something went wrong, Please try again later !!", 500);
    	}
    }

    public function changeStatus()
    {

    }

    /**
     * get regEx pattern for account number validation  
     * @param Request $request
     * @param utility_id  pass coma separated multiple utility id for duel form
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegex(Request $request)
    {
        try{
            
            $validator = \Validator::make($request->all(), [
                'form_id' => 'required',
                'utility_id' => 'required'
            ]);
            info(print_r($request->all(),true));
            if ($validator->fails()) {
                return $this->error("error", implode(',',$validator->messages()->all()), 500); 
            }

            $form = Clientsforms::find($request->form_id);

            if (empty($form)) {
                return $this->error("error", 'Form not available', 400);
            }
            $accountNumberLabel = config('constants.ACCOUNT_NUMBER_LABEL').'%';
            $fields = $form->fields()->select('id','label')->where(\DB::raw('LOWER(label)'),'LIKE',$accountNumberLabel)->orderBy('position', 'asc')->get();
            
            if ($fields->isEmpty()) {
                //return $this->error("error", 'Account Number field not found', 400);
                Log::info("Account Number field not found for form id:".$request->form_id);
                // return response()->json([
                //     'status' => "success",
                //     'message' => "success",
                //     'data' => []
                // ], 200);
            }
            $data = [];
            $utilities = explode(',',$request->utility_id);
            foreach ($fields as $key => $field) {
                $labels = explode('-',$field->label);
                $utility = Utilities::whereIn('id',$utilities);
                
                if (count($labels) == 2 && isset($labels[1])) {
                    $commodity = trim($labels[1]);
                    $utility->whereHas('utilityCommodity', function ($query) use ($commodity) {
                        $query->where('name',$commodity);
                    });
                }
                $utility = $utility->first();
                
                $data[] = [
                    'field_id' => $field->id,
                    'regex' => isset($utility->regex) ? $utility->regex : "",
                    'regex_message' => isset($utility->regex_message) ? $utility->regex_message : "",
                    'placeholder' => isset($utility->act_num_verbiage) ? $utility->act_num_verbiage : "",
                ];
            }

            // for utility validation
            $utilityAll = Utilities::whereIn('id',$utilities)->with('validations')->get();
            foreach ($utilityAll as $utility) {
                if ($utility->validations->isEmpty()) {
                    continue;
                }
                foreach ($utility->validations as $validation) {                    
                    $field = $form->fields()->select('id','label')->whereIn('type',['textbox','textarea'])->where('label',$validation->label)->first();
                    if($field) {                                        
                        $data[] = [
                            'field_id' => $field->id,
                            'regex' => isset($validation->regex) ? $validation->regex : "",
                            'regex_message' => isset($validation->regex_message) ? $validation->regex_message : "",
                        ];
                    }
                }
            }


            // for update ecogold program field
            if($form->client_id == config('constants.CLIENT_RRH_CLIENT_ID')){
                $programLabel = config('constants.ECOGOLD_PROGRAM_LABEL').'%';
                $fields = $form->fields()->select('id','label','meta')->where('type','selectbox')->where(\DB::raw('LOWER(label)'),'LIKE',$programLabel)->get();
                $program = Programs::find($request->program_id);
                $brandName = '';
                if (isset($program->utility->brandContacts)) {
                    $brandName = $program->utility->brandContacts->name;
                }
                foreach ($fields as $key => $field) {
                    $options = isset($field->meta['options']) ? $field->meta['options'] : [];
                    if(strtolower($brandName) == 'kiwi energy') {
                        $otherOpts = ['spring guard', '5% ecogold rewards'];
                    } else {
                        $otherOpts = ['kiwi guard', 'ecogold base','5% rewards'];
                    }                    

                    if ($program && $program->code == config('constants.ECOGOLD_PROGRAM_CODE')) {
                        $removeOpts = ['3% cash back', '5% ecogold rewards'];
                        $otherOpts = array_merge($otherOpts, $removeOpts);
                    } else if ($program && in_array($program->code,config('constants.ECOGOLD_CODE_WITHOUT_SG'))) {
                        $otherOpts[] = "spring guard"; 
                    } else if($program && $program->code == 'KIWIEHGUARD') {
                        $otherOpts[] = "ecogold base";
                        $otherOpts[] = "5% rewards";
                        $otherOpts[] = "3% cash back";
                    } else if($program && $program->code == 'NY36MZG') {
                        $otherOpts[] = "kiwi guard";
                        $otherOpts[] = "5% rewards";
                        $otherOpts[] = "3% cash back";
                    } else if($program && $program->code == 'OHKIWICLN') {
                        $otherOpts[] = "kiwi guard";
                        $otherOpts[] = "ecogold base";
                    } 

                    foreach ($options as $k => $value) {
                        if (isset($value["option"]) && in_array(strtolower($value["option"]),$otherOpts)) {
                            unset($options[$k]);
                        }
                    }

                    $data[] = [
                        'field_id' => $field->id,
                        'label' => $field->label,
                        'options' => array_values($options)
                    ];
                }

                $promoCodeLabel = config('constants.PROMO_CODE_FIELD_LABEL').'%';
                $fields = $form->fields()->select('id','label','meta')->where('type','selectbox')->where(\DB::raw('LOWER(label)'),'LIKE',$promoCodeLabel)->get();
                foreach ($fields as  $field) {
                    $options = isset($field->meta['options']) ? $field->meta['options'] : [];
                    if(strtolower($brandName) == 'kiwi energy') {
                        $otherOpts = ['$25 gift card 3mo'];
                    } else {
                        $otherOpts = ['$25 gift card', '$500 energy efficiency'];
                    }
                    
                    if ($program && in_array($program->code,config('constants.PROMO_CODE_PROGRAM.GIFT'))) {
                        $otherOpts[] = '$200 energy efficiency';                        
                    } else if($program && in_array($program->code,config('constants.PROMO_CODE_PROGRAM.KIWI_ENERGY'))) {
                        $otherOpts[] = 'not applicable';
                    } else if($program && $program->code == 'KIWIEHGUARD') {
                        $otherOpts[] = '$25 gift card';
                        $otherOpts[] = '$200 energy efficiency';
                        $otherOpts[] = '$500 energy efficiency';
                    }
                    foreach ($options as $k => $value) {
                        if (isset($value["option"]) && in_array(strtolower($value["option"]),$otherOpts)) {
                            unset($options[$k]);
                        }
                    }

                    $data[] = [
                        'field_id' => $field->id,
                        'label' => $field->label,
                        'options' => array_values($options)
                    ];
                }
            }
            $response = $data;
            Log::info("Retrieved account number regEx for utility id: " . $request->utility_id);
            info(json_encode($response));
            return $this->success("success", "success", $response);
        } catch (\Exception $e) {
            Log::error("Error while getting regex for utility id " . $request->utility_id . " :" . $e);
            return $this->error("error", "Something went wrong, Please try again later !!", 500);
        }
    }

    /**
     * This method is used for change form settings as per auth user's client id 
     */
    public function formSettings(Request $request)
    {
        try{
            $clientId =  auth()->user()->client_id;
            $data['is_enable_enroll_by_state'] = isEnableEnrollByState($clientId);
            $data['is_enable_recording'] = isOnSettings($clientId,'is_enable_recording');
            return $this->success("success", "success", $data);
        }catch(\Exception $e) {
            Log::error("Error while getting form settings: " . $e->getMessage());
            return $this->error("error", "Something went wrong, Please try again later !!", 500);
        }
    }
}
