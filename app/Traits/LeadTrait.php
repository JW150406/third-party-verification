<?php

namespace App\Traits;

use App\models\Clientsforms;
use App\models\Telesalesdata;
use App\models\Client;
use Log;
use Mail;
use App\User;
use App\models\Zipcodes;
use App\models\FormField;
use App\models\Telesales;
use App\models\TelesalesTmp;
use App\models\TelesalesdataTmp;
use App\models\TextEmailStatistics;
use App\models\TelesalesSelfVerifyExpTime;
use App\models\TwilioLeadCallDetails;
use App\models\Salesagentdetail;
use App\models\CriticalLogsHistory;
use App\models\ClientTwilioNumbers;
use App\Jobs\CriticalAlertMailJob;
use App\Traits\CriticalLogsTrait;
use App\Services\SegmentService;
use App\Jobs\SendSelfVerificationMail;
use Carbon\Carbon;
use App\Jobs\SendDispositionEmail;
use App\models\DoNotEnroll;
use App\models\FraudAlert;

trait LeadTrait {

    use CriticalLogsTrait;

	/**
	 * This trait method is used to create new lead as per parameter values 
	 * @param $tmpLead, $zipcodeData, $reqPrograms, $status, $isCancelStatus, $reason, $parentLeadId 
	 */
    public function createLead($tmpLead,$zipcodeData,$reqPrograms,$status='pending', $isCancelStatus = false, $reason = '',$parentLeadId = 0, $dispositionId = null)
    {
    	try{

			
	    	if(!empty($tmpLead)) {
		    	$teleSaleDataTmp = TelesalesdataTmp::where('telesaletmp_id',$tmpLead->id)->get();
		 		$leadData = [];
		        $leadData['client_id'] = $tmpLead->client_id;
		        $leadData['form_id'] = $tmpLead->form_id;
		        $leadData['user_id'] = $tmpLead->user_id;
		        $leadData['is_enrollment_by_state'] = $tmpLead->is_enrollment_by_state;

		        if ($isCancelStatus == true) {
                    $leadData['alert_status'] = config('constants.TELESALES_ALERT_CANCELLED_STATUS');
                } else {
                    $leadData['alert_status'] = config('constants.TELESALES_ALERT_PROCEED_STATUS');
                }

				// $referenceId = (new Telesales)->generateReferenceId();
				$clientPrefix = (new Client())->getClientPrefix($tmpLead->client_id);
				$referenceId = (new Telesales)->generateNewReferenceId($tmpLead->client_id,$clientPrefix);
				
				
		        // $check_verification_number = 2;
		        // $validate_num = $verification_number = "";
		        // while ($check_verification_number > 1) {
		        //     $verification_number = rand(1000000, 9999999);
		        //     $validate_num = (new Telesales)->validateConfirmationNumber($verification_number);
		        //     if (!$validate_num) {
		        //         $check_verification_number = 0;
		        //     } else {
		        //         $check_verification_number++;
		        //     }
		        // }

		        $leadData['refrence_id'] = $referenceId;
		        $leadData['is_multiple'] = $tmpLead->is_multiple;
		        $leadData['multiple_parent_id'] = $parentLeadId;
		        $leadData['disposition_id'] = $dispositionId;
		        // $leadData['verification_number'] = $verification_number;

		        $leadData['parent_id'] = $tmpLead->parent_id;
		        $leadData['cloned_by'] = $tmpLead->cloned_by;
				$leadData['status'] = $status;
				
				$telesale = Telesales::create($leadData);
				
		        foreach($teleSaleDataTmp AS $data){

		            $telesale->teleSalesData()->create([
		                'meta_key' => $data->meta_key,
		                'meta_value' => $data->meta_value,
		                'field_id' => $data->field_id
		            ]);
		        }
		        $telesale->zipcodes()->sync($zipcodeData->id);
		        $telesale->programs()->sync($reqPrograms);

		        TelesalesTmp::where('id',$tmpLead->id)->update(['is_proceed'=>1]);

		        $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
		        if($status == 'cancel') {
		        	$lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');

		        	//Register logs when lead cancelled
                    $this->registerCancelledLeadsLogs($telesale,$reason);
		        } else {
		        	$message = __('critical_logs.messages.Event_Type_11');
		        	$event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_11');
		        	(new CriticalLogsHistory)->createCriticalLogs($tmpLead->user_id,$message,$telesale->id,null,null,$lead_status,$event_type);
		        }
		        (new CriticalLogsHistory)->updateId($tmpLead->id,$telesale->id,$lead_status);
		        return $telesale;
		    }
	    }catch(\Exception $e) {
            Log::error($e);
            return null;
        }
	}

	/**
	 * This trait method is used to create lead for multiple (For parent - child lead relation)
	 * @param $tmpLead, $zipcodeData, $reqPrograms, $parent_lead_id, $status, $isCancelStatus, $reason
	 */
	public function createLeadMultiple($tmpLead,$zipcodeData,$reqPrograms,$parent_lead_id=0,$status='pending', $isCancelStatus = false, $reason = '')
    {
    	try{

			
	    	if(!empty($tmpLead)) {
		    	$teleSaleDataTmp = TelesalesdataTmp::where('telesaletmp_id',$tmpLead->id)->get();
		 		$leadData = [];
		        $leadData['client_id'] = $tmpLead->client_id;
		        $leadData['form_id'] = $tmpLead->form_id;
				$leadData['user_id'] = $tmpLead->user_id;
				$leadData['is_enrollment_by_state'] = $tmpLead->is_enrollment_by_state;

		        if ($isCancelStatus == true) {
                    $leadData['alert_status'] = config('constants.TELESALES_ALERT_CANCELLED_STATUS');
                } else {
                    $leadData['alert_status'] = config('constants.TELESALES_ALERT_PROCEED_STATUS');
                }

				// $referenceId = (new Telesales)->generateReferenceId();
				$clientPrefix = (new Client())->getClientPrefix($tmpLead->client_id);
				$referenceId = (new Telesales)->generateNewReferenceId($tmpLead->client_id,$clientPrefix);
				
				
		        // $check_verification_number = 2;
		        // $validate_num = $verification_number = "";
		        // while ($check_verification_number > 1) {
		        //     $verification_number = rand(1000000, 9999999);
		        //     $validate_num = (new Telesales)->validateConfirmationNumber($verification_number);
		        //     if (!$validate_num) {
		        //         $check_verification_number = 0;
		        //     } else {
		        //         $check_verification_number++;
		        //     }
		        // }

		        $leadData['refrence_id'] = $referenceId;
		        $leadData['is_multiple'] = $tmpLead->is_multiple;
		        $leadData['multiple_parent_id'] = $parent_lead_id;
		        // $leadData['verification_number'] = $verification_number;

		        $leadData['parent_id'] = $tmpLead->parent_id;
		        $leadData['cloned_by'] = $tmpLead->cloned_by;
				$leadData['status'] = $status;
				
				$telesale = Telesales::create($leadData);
				
		        foreach($teleSaleDataTmp AS $data){

		            $telesale->teleSalesData()->create([
		                'meta_key' => $data->meta_key,
		                'meta_value' => $data->meta_value,
		                'field_id' => $data->field_id
		            ]);
		        }
		        $telesale->zipcodes()->sync($zipcodeData->id);
		        $telesale->programs()->sync($reqPrograms);

		        TelesalesTmp::where('id',$tmpLead->id)->update(['is_proceed'=>1]);

		        $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
		        if($status == 'cancel') {
		        	$lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');

		        	//Register logs when lead cancelled
                    $this->registerCancelledLeadsLogs($telesale,$reason);
		        } else {
		        	$message = __('critical_logs.messages.Event_Type_11');
		        	$event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_11');
		        	(new CriticalLogsHistory)->createCriticalLogs($tmpLead->user_id,$message,$telesale->id,null,null,$lead_status,$event_type);
		        }
		        (new CriticalLogsHistory)->updateId($tmpLead->id,$telesale->id,$lead_status);
		        return $telesale;
		    }
	    }catch(\Exception $e) {
            Log::error($e);
            return null;
        }
	}
	
	/**
	 * This method is used to validate sales agent's email and phone number
	 * @param $telesale, $tmpLead
	 */
    public function validateSalesAgentEmailAndPhone($telesale,$tmpLead)
    {
    	try{
	    	if(!empty($telesale) && !empty($tmpLead)) {
		    	$emailPhoneFlag = 0;
		        $emailFlag = 0;
		        $phoneFlag  = 0;
		        $response = ['status' => true,'message'=>''];
		        $salesAgent = User::find($tmpLead->user_id);
		        $salesAgentDetail = Salesagentdetail::where('user_id', $tmpLead->user_id)->first();
		        $phoneField = FormField::where('form_id', $tmpLead->form_id)->where('is_primary', 1)->where('type', 'phone_number')->first();
		        $emailField = FormField::where('form_id', $tmpLead->form_id)->where('is_primary', 1)->where('type', 'email')->first();

		        if(!empty($phoneField) && !empty($salesAgentDetail)) {
		            $customerPhone = TelesalesdataTmp::where('field_id', $phoneField->id)->where('telesaletmp_id', $tmpLead->id)->first();

		            if(!empty($customerPhone) && $customerPhone->meta_value == $salesAgentDetail->phone_number) {
		                $phoneFlag = 1;
		            }
		        }

		        if(!empty($emailField) && !empty($salesAgent)) {
		            $customerEmail = TelesalesdataTmp::where('field_id', $emailField->id)->where('telesaletmp_id', $tmpLead->id)->first();
		            if(!empty($customerEmail) && $customerEmail->meta_value == $salesAgent->email) {
		                $emailFlag = 1;
		            }
		        }

		        if($phoneFlag && $emailFlag) {
		            $emailPhoneFlag = 1;
		        }

		        $message = $log_message = "";
		        $event_type = $alerts = null;

		        if($emailPhoneFlag == 1)
		        {
		            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_4');
		            $message = "You can not create lead with your own Email address and Your own Phone number";
		            $alerts = "Sales agent used their own email and phone number during enrollment.";
		            $log_message = __('critical_logs.messages.Event_Type_4');
		        }
		        else if($emailFlag == 1)
		        {
		            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_2');
		            $message = "You can not create lead with your own Email address";
		            $alerts = "Sales agent used their own email during enrollment.";
		            $log_message = __('critical_logs.messages.Event_Type_2');
		        }
		        else if($phoneFlag == 1)
		        {
		            $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_3');
		            $message = "You cannot create lead with your own Phone Number ";
		            $alerts = "Sales agent used their own phone number during enrollment.";
		            $log_message = __('critical_logs.messages.Event_Type_3');
		        }

		        if($message != "")
		        {
		            $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Critical');
		            $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Cancelled');
		            $critical_logs_report = (new CriticalLogsHistory)->createCriticalLogs($tmpLead->user_id,$log_message,$telesale->id,null,null,$lead_status,$event_type,$error_type,null,null,$alerts);
		            $response = ['status' => false,'message'=>$message];
		            return $response;
		        }

		        return $response;
		    }
		}catch(\Exception $e) {
            Log::error($e);
        }
    }

	/**
	 * This method is used for send email for critial alert
	 * @param $lead
	 */
 	public function sendCriticalAlertMail($lead,$tpvAlert = null)
    {
        try{
            $alerts = $salesAgent = null;
            if(!empty($lead)) {
				if($tpvAlert != null){
					$alerts = $tpvAlert;
				}
				else{
					$alerts = CriticalLogsHistory::getEmailAlertMessage($lead->id);
				}
                $salesAgent = User::find($lead->user_id);
            }
            if(!empty($alerts) && !empty($salesAgent)) {
                CriticalAlertMailJob::dispatch($salesAgent,$lead,$alerts);
            }
        }catch(\Exception $e) {
            Log::error($e);
        }
    }

    /**
	 * This method is used to register lead as a expired lead and store its non-critical logs
	 * @param $leadId
	 */
    public function expiredLeadWithoutSendingMails($leadId) {
      $lead = Telesales::with('selfVerifyModes')->find($leadId);

      if (empty($lead)) {
        \Log::error("expireLeadWithoutSendingMails: Lead not found with id: " . $leadId);
        return false;
      }

	  $lead->update(['status' => config()->get('constants.LEAD_TYPE_EXPIRED')]);
	  
	//Store lead status in twilio call details for tpv now request
	$twilioCalls = TwilioLeadCallDetails::where('lead_id',$leadId)->orderBy('id','desc')->first();
	if(!empty($twilioCalls)){
		$twilioCalls->lead_status = config()->get('constants.LEAD_TYPE_EXPIRED');
		$twilioCalls->save();
	}

      //Register non-critical logs when lead expires
      $this->registerLeadExpiredLogs($lead);

      //Check if any self verification is available or not
      if(count($lead->selfVerifyModes) > 0) {
        //If self verification link is available then register self verification expired link logs
        return $this->registerLogsForSelfVerificationExpire($lead);
      } else {
        \Log::info("Lead: " . $lead->id . " has no self verification link. So, Can not register its logs.");
        return true;
      }
    }

    /**
     * @param $leadId
     * @return bool
     * @description store self verification mode and send link via mail and text
     */
    public function storeSelfVerifyLink($leadId,$verification_mode=[])
    {
    	try {

    		$lead = Telesales::find($leadId);
    		$response = [
    			'status' => false,
    			'message' => 'Invalid lead ID'
    		];

    		if (!empty($lead)) {
    			if($lead->status != config()->get('constants.LEAD_TYPE_PENDING')) {
    				$response['message'] = "Lead is not in pending state. Can't generate self verification links.";
	    			return $response;
	    		}

	    		// for check self TPV Tele is enable or not
	    		if ($lead->type == 'tele' && !isOnSettings($lead->client_id, 'is_enable_self_tpv_tele')) {
	    			$response['message'] = "Tele self verify switched off by administrator. Can't generate self verification links.";
	    			return $response;
	    		}

	    		// for check self TPV d2d is enable or not
	    		if ($lead->type == 'd2d' && !isOnSettings($lead->client_id, 'is_enable_self_tpv_d2d')) {
	    			$response['message'] = "D2D self verify switched off by administrator. Can't generate self verification links.";
	    			return $response;
	    		}

	    		$clientTwilioNumbers = new ClientTwilioNumbers;
	    		$segmentService = new SegmentService;
	            $phones = $clientTwilioNumbers->getNumber($lead->client_id);
	            $emailField = FormField::whereHas('form', function ($query) use ($lead) {
	                $query->where('id', $lead->form_id);
	            })->where('type', 'email')->where('is_primary', 1)->first();

	            $fullName = FormField::whereHas('form', function ($query) use ($lead) {
	                $query->where('id', $lead->form_id);
	            })->where('type', 'fullname')->where('is_primary', 1)->first();

	            $phoneNumberField = FormField::whereHas('form', function ($query) use ($lead) {
	                $query->where('id', $lead->form_id);
	            })->where('type', 'phone_number')->where('is_primary', 1)->first();

	            // for client name
	            $clientName=$lead->client->name;

	            $tpvNumber = '';
	            if(!empty($phones) && $phones != null) {
	                $tpvNumber = $phones->phonenumber;
	            }

	            if (!empty($emailField) || !empty($phoneNumberField) || !empty($fullName)) {
	                $phone_number = "";
	                $Email = "";
	                $full_name="";

	                if (isset($phoneNumberField)) {
	                    $field = Telesalesdata::where('field_id', $phoneNumberField->id)->where('telesale_id', $leadId)->first();
	                    if ($field){
	                        $phone_number = $field->meta_value;
	                        $phone_number = '+1' . preg_replace("/[^0-9\.]/", '', $phone_number);
	                    }

	                }

	                if (isset($emailField)) {
	                    $field = Telesalesdata::where('field_id', $emailField->id)->where('telesale_id', $leadId)->first();
	                    if ($field) {
	                        $Email = $field->meta_value;
	                    }
	                }

	                if (isset($fullName)) {
	                    $field = Telesalesdata::where('field_id', $fullName->id)->where('meta_key', 'first_name')->where('telesale_id', $leadId)->first();
	                    if ($field) {
	                        $full_name = $field->meta_value;
	                    }
	                }


	                $encoded_leadid = base64_encode($leadId);
	                // $url = \URL::temporarySignedRoute(
	                //     'sendverificationlink', now()->addHours(48), ['verificationid' => $encoded_leadid]
	                // );
	                $expireMin = config('constants.self_verification_link_expire_time');
	                $data = [
	                    'telesale_id' => $leadId,
	                    'expire_time' => now()->addHours($expireMin),
	                ];
	                $link = "";
	                $emailLink = "";
	                $phoneLink = "";

	                if (in_array('email', $verification_mode) && $Email != "") {

	                    $data['verification_mode'] ='email';
	                    TelesalesSelfVerifyExpTime::updateOrCreate(['telesale_id' => $leadId,'verification_mode'=>'email'],$data);
	                    $url= route('sendverificationlink',[$encoded_leadid,'email']);
	                    $emailLink = $url;
	                    $this->sendSelfverificationLinkEmail($Email, $url,$full_name,$clientName,$tpvNumber);
	                    $segmentService->updateLeadTrackForEmailLink($lead);
	                }
	                if (in_array('phone', $verification_mode) && $phone_number != "") {

	                    $data['verification_mode'] ='phone';
	                    TelesalesSelfVerifyExpTime::updateOrCreate(['telesale_id' => $leadId,'verification_mode'=>'phone'],$data);
	                    $url= route('sendverificationlink',[$encoded_leadid,'phone']);
	                    $phoneLink = $url;
	                    $this->sendSelfverificationLinkSMS($phone_number, $url,$full_name,$clientName,$tpvNumber);
	                }

	                if((in_array('email', $verification_mode)) && (in_array('phone', $verification_mode)))
	                {
	                    $link = "email and text.";
	                }
	                else if(in_array('email', $verification_mode))
	                {
	                    $link = "email.";
	                }
	                else if(in_array('phone', $verification_mode))
	                {
	                    $link = "text.";
	                }
	                $message = __('critical_logs.messages.Event_Type_12',['link'=>$link]);
	                $user_type = config('constants.USER_TYPE_CRITICAL_LOGS.2');
	                $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical');
	                $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_Type_12');
	                $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Pending');
	                $criticalLogs = (new CriticalLogsHistory)->createCriticalLogs(null,$message,$leadId,null,null,$lead_status,$event_type,$error_type,$user_type);
	                $response = [
		    			'status' => true,
		    			'message' => 'Verification link successfully sent.'
		    		];
	                return $response;
	            } else {
		        	return $response;
		        }
	        } else {
	        	return $response;
	        }

    	} catch (\Exception $e) {
    		Log::error($e);
    		$response = [
    			'status' => false,
    			'message' => $e->getMessage()
    		];
    		return $response;
    	}
    }

    /**
     * @param $phonenumber
     * @param $url
     * @param $full_name
     * @param $client_name
     * @param $tpvNumber
     * @return mixed
     * @description send self verification link in sms
     */
    public function sendSelfverificationLinkSMS($phonenumber, $url, $full_name, $client_name,$tpvNumber)
    {
    	try {
			$message = addslashes("Hello ".$full_name.", to verify your enrollment with ".$client_name.", please visit ".$url." or call ".$tpvNumber.".");
	        $message_response = app('App\Http\Controllers\Conference\ConferenceController')->sendmessage($phonenumber, $message);

	        $textEmailStatistics = new TextEmailStatistics();
	        $textEmailStatistics->type = 2;
	        $textEmailStatistics->save();

	        return $message_response;

        } catch (\Exception $e) {
        	Log::error($e);
        }



    }

    /**
     * @param $email
     * @param $url
     * @param $full_name
     * @param $client_name
     * @param $phone_number
     * @return bool
     * @description send self verification link in email
     */
    public function sendSelfverificationLinkEmail($email, $url, $full_name, $client_name,$phone_number)
    {
    	try{
			$arrPhoneNum = preg_replace(config()->get('constants.DISPLAY_PHONE_NUMBER_FORMAT'), config()->get('constants.PHONE_NUMBER_REPLACEMENT'), str_replace("+", "", $phone_number));
	        /*$message_content ='Hello '.$full_name.',<br/><br/>
	        To verify your enrollment in '.$client_name.'’s services, please <a href="' . $url . '">click here</a>.<br/><br/>If you would like to speak with an agent, please call '.$arrPhoneNum.'<br/><br/>Regards,<br/><br/>The TPV360 Team';


	        $to = $email;
	        $subject = 'Verify your energy services';
	        $from = 'no-reply@spark.tpv.plus';

	        // To send HTML mail, the Content-type header must be set
	        $headers = 'MIME-Version: 1.0' . "\r\n";
	        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	        // Create email headers
	        $headers .= 'From: ' . $from . "\r\n" .
	            'Reply-To: ' . $from . "\r\n" .
	            'X-Mailer: PHP/' . phpversion();

	        // Compose a simple HTML email message
	        $message = '<html><body>';
	        $message .= '<p>' . $message_content . '</p>';
	        $message .= '</body></html>';*/

	        $to = $email;
	        $subject = 'Verify your energy services';
	        $greeting ='Hello '.$full_name.',';
	        $message = 'To verify your enrollment in '.$client_name.'’s services, please <a href="' . $url . '">click here</a>.<br/><br/>If you would like to speak with an agent, please call '.$arrPhoneNum;
	        // Sending email
            SendSelfVerificationMail::dispatch($to, $subject, $message, $greeting)->delay(Carbon::now()->addSeconds(config('constants.DELAY_TIME_FOR_SELF_VERIFICATION_MAIL')));
            return true;
	    } catch (\Exception $e) {
        	Log::error($e);
        }
    }

    /**
	 * This method is used to Prepare array for leads details API
	 * @param $lead
	 */
    public function leadsDetailsFormatting($lead) {
        $leadForm = Clientsforms::withTrashed()->find($lead->form_id);

        if (empty($leadForm)) {
            return $this->error("error", 'Form not found !!', 400);
        }

        $fields = $leadForm->fields()->with('telesalesData')->select('id', 'type', 'label', 'meta', 'is_required', 'is_primary', 'is_verify', 'position')->orderBy('position', 'asc')->get();
        $response = [];
        foreach ($fields as $fiKey => $fiValue) {
            // echo "<pre>"; print_r($fiValue); exit;
            $resArr = [];
            $resArr['id'] = array_get($fiValue, 'id');
            $resArr['type'] = array_get($fiValue, 'type');
            if (array_get($fiValue, 'type') == 'textbox') {
                $resArr['label'] = array_get($fiValue, 'label');//getAccountNumberLabel(array_get($fiValue, 'label'));
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
                    $fName = Telesalesdata::where('meta_key', 'first_name')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["first_name"] = array_get($fName, 'meta_value', "");
                    $mName = Telesalesdata::where('meta_key', 'middle_initial')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["middle_initial"] = array_get($mName, 'meta_value', "");
                    $lName = Telesalesdata::where('meta_key', 'last_name')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["last_name"] = array_get($lName, 'meta_value', "");
                    break;

                case "service_and_billing_address":
                    $bAdd1 = Telesalesdata::where('meta_key', 'billing_address_1')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["billing_address_1"] = array_get($bAdd1, 'meta_value');

                    $bAdd2 = Telesalesdata::where('meta_key', 'billing_address_2')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["billing_address_2"] = array_get($bAdd2, 'meta_value');

                    $bZip = Telesalesdata::where('meta_key', 'billing_zipcode')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["billing_zipcode"] = array_get($bZip, 'meta_value');

                    $bCity = Telesalesdata::where('meta_key', 'billing_city')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
					$resArr['values']["billing_city"] = array_get($bCity, 'meta_value');
					
					// For billing_county
					$bCounty = Telesalesdata::where('meta_key', 'billing_county')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["billing_county"] = array_get($bCounty, 'meta_value');
					// End

                    $bState = Telesalesdata::where('meta_key', 'billing_state')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["billing_state"] = array_get($bState, 'meta_value');

                    $sAdd1 = Telesalesdata::where('meta_key', 'service_address_1')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["service_address_1"] = array_get($sAdd1, 'meta_value');

                    $sAdd2 = Telesalesdata::where('meta_key', 'service_address_2')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["service_address_2"] = array_get($sAdd2, 'meta_value');

                    $sZip = Telesalesdata::where('meta_key', 'service_zipcode')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["service_zipcode"] = array_get($sZip, 'meta_value');

                    $sCity = Telesalesdata::where('meta_key', 'service_city')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
					$resArr['values']["service_city"] = array_get($sCity, 'meta_value');
					
					// For service_county
					$sCounty = Telesalesdata::where('meta_key', 'service_county')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["service_county"] = array_get($sCounty, 'meta_value');
					// End

                    $sState = Telesalesdata::where('meta_key', 'service_state')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["service_state"] = array_get($sState, 'meta_value');

                    $bUnit = Telesalesdata::where('meta_key', 'billing_unit')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["billing_unit"] = array_get($bUnit, 'meta_value');

                    $sUnit = Telesalesdata::where('meta_key', 'service_unit')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["service_unit"] = array_get($sUnit, 'meta_value');

                    $bCountry = Telesalesdata::where('meta_key', 'billing_country')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["billing_country"] = array_get($bCountry, 'meta_value');

                    $sCountry = Telesalesdata::where('meta_key', 'service_country')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["service_country"] = array_get($sCountry, 'meta_value');

                    $bLat = Telesalesdata::where('meta_key', 'billing_lat')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["billing_lat"] = array_get($bLat, 'meta_value');

                    $bLng = Telesalesdata::where('meta_key', 'billing_lng')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["billing_lng"] = array_get($bLng, 'meta_value');

                    $sLat = Telesalesdata::where('meta_key', 'service_lat')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["service_lat"] = array_get($sLat, 'meta_value');

                    $serviceLng = Telesalesdata::where('meta_key', 'service_lng')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["service_lng"] = array_get($serviceLng, 'meta_value');
                    break;

                case "address":
                    $unit = Telesalesdata::where('meta_key', 'unit')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["unit"] = array_get($unit, 'meta_value');

                    $address_1 = Telesalesdata::where('meta_key', 'address_1')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["address_1"] = array_get($address_1, "meta_value");

                    $address_2 = Telesalesdata::where('meta_key', 'address_2')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["address_2"] = array_get($address_2, "meta_value");

                    $zipcode = Telesalesdata::where('meta_key', 'zipcode')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["zipcode"] = array_get($zipcode, "meta_value");

                    $city = Telesalesdata::where('meta_key', 'city')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
					$resArr['values']["city"] = array_get($city, "meta_value");
					
					// For address county
					$county = Telesalesdata::where('meta_key', 'county')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["county"] = array_get($county, "meta_value");
					// End

                    $state = Telesalesdata::where('meta_key', 'state')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["state"] = array_get($state, "meta_value");

                    $country = Telesalesdata::where('meta_key', 'country')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["country"] = array_get($country, "meta_value");

                    $lat = Telesalesdata::where('meta_key', 'lat')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["lat"] = array_get($lat, "meta_value");

                    $lng = Telesalesdata::where('meta_key', 'lng')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
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

                case "separator":
                case "heading":
                case "label":
                    $resArr['values'] = NULL;
                    break;

                default:
                    $default = Telesalesdata::where('meta_key', 'value')->where('field_id', $fiValue->id)->where('telesale_id', $lead->id)->first();
                    $resArr['values']["value"] = array_get($default, "meta_value", "");
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

        return $response;
    }

    /**
     * For send signature link in sms
     * @param $to
     * @param $url
     * @param $fullName
     * @return mixed
     */
    public function sendSignatureLinkSMS($to, $url, $fullName)
    {
    	try {
			$message = addslashes("Hello ".$fullName.", to upload your signature, please visit ".$url);
	        $response = app('App\Http\Controllers\Conference\ConferenceController')->sendmessage($to, $message);

	        TextEmailStatistics::create(['type'=>2]);
	        return $response;
        } catch (\Exception $e) {
        	Log::error($e);
        }
    }

    /**
     * For send signature link in email
     * @param $to
     * @param $url
     * @param $fullName
     * @return mixed
     */
    public function sendSignatureLinkEmail($to, $url, $fullName)
    {
        try {
			$greeting ='Hello '.$fullName.',';
	        $subject = 'Upload your signature';
            $message='To upload your signature, please <a href="' . $url . '">click here</a>';

	        // Mail::send([], [], function($mail) use ($to, $subject, $message) {
	        //     $mail->to($to);
	        //     $mail->subject($subject);
	        //     $mail->setBody($message, 'text/html');
			// });
			
			Mail::send('emails.common', ['greeting' => $greeting, 'msg' => $message], function($mail) use ($to, $subject) {
                $mail->to($to);
                $mail->subject($subject);
            });

	        TextEmailStatistics::create(['type'=>1]);
        } catch (\Exception $e) {
        	Log::error($e);
        }

    }
	
	
	/**
	 * This method is used to Send alerts to all registered email adderss for disposition
	 * @param $disposition, $lead
	 */
	public function sendDispositionMail($disposition, $lead) {
		// $emailArr = array('mansi.inexture@gmail.com', 'kirtan.inexture@gmail.com');
		$alert = new FraudAlert;
		$agentDetails = User::where('id', $lead->user_id)->first();
		$scId = $locId = "";
		if (!empty($agentDetails)) {
			$scId = array_get($agentDetails, 'salescenter_id');
			$locId = array_get($agentDetails, 'location_id');
		}
		$emails = $alert->getDispositionEmailList($lead->client_id, $scId, $locId);
		$emailArr = $emails->pluck('email')->toArray();
		\Log::debug("emailArr: " . print_r($emailArr, true));
		SendDispositionEmail::dispatch($emailArr, $disposition, $lead);
	}

	/**
	 * This method is used to Prepare an response of lead details for webhook API
	 * @param $leadId
	 */
	public function getWebhookAPIRequest($leadId) {
		$lead = Telesales::find($leadId);
		
		if (empty($lead)) {
            return $this->error("error", 'Lead not found !!', 400);
		}
		
		$leadForm = Clientsforms::withTrashed()->find($lead->form_id);

        if (empty($leadForm)) {
            return $this->error("error", 'Form not found !!', 400);
        }

        $fields = $leadForm->fields()->get();
		$response = [];
		
		$response['RepID'] = "";
		$salesAgent = User::find(array_get($lead, 'user_id'));
		if (!empty($salesAgent->salesAgentDetails)) {
			$response['RepID'] = $salesAgent->salesAgentDetails->external_id;
		}

		$response['SalesCenter'] = $salesAgent->salescenter->name;
		$response['SalesLocation'] = $salesAgent->location->name;
		$response['LeadID#'] = $lead->refrence_id;
		// $response['SOID'] = "";
		// $response['TPVConfirmation'] = array_get($lead, 'verification_number');
        $response['BTN'] = "";
        
        $response['Email'] = "";
        $emailField = $fields->where('type', 'email')->first();
        if (!empty($emailField)) {
            $emailData = Telesalesdata::fieldVal($leadId, array_get($emailField, 'id'))->first();
            $response['Email'] = array_get($emailData, 'meta_value');
            
        }
        
        $response['TPVRecording'] = "";
        if (array_get($lead, 's3_recording_url')) {
			$response['TPVRecording'] = \Storage::disk('s3')->url(array_get($lead, 's3_recording_url'));
		}
		
        $response['FirstName'] = "";
		$response['LastName'] = "";
        $fullNameField = $fields->where('type', 'fullname')->where('is_primary', true)->first();
        if (!empty($fullNameField)) {
            $fNameData = Telesalesdata::fieldVal($leadId, array_get($fullNameField, 'id'), 'first_name')->first();
            $response['FirstName'] = array_get($fNameData, 'meta_value');
            $lNameData = Telesalesdata::fieldVal($leadId, array_get($fullNameField, 'id'), 'last_name')->first();
            $response['LastName'] = array_get($lNameData, 'meta_value');
        }
        
        // $response['ContactTitle'] = "";

        $response['SvcStreet'] = "";
		$response['SvcCity'] = "";
		$response['SvcState'] = "";
		$response['SvcZIP'] = "";
		$response['BillStreet'] = "";
		$response['BillCity'] = "";
		$response['BillState'] = "";
		$response['BillZIP'] = "";
		$response['SvcCounty'] = "";
		$response['State'] = "";
		$addField = $fields->where('type', 'service_and_billing_address')->where('is_primary', true)->first();
        if (!empty($addField)) {
			$addData = Telesalesdata::where('telesale_id', $leadId)->where('field_id', $addField->id)->get();
			$svcStreet1 = $addData->where('meta_key', 'service_address_1')->first();
			$svcStreet2 = $addData->where('meta_key', 'service_address_2')->first();
			$svcCity = $addData->where('meta_key', 'service_city')->first();
			$svcCounty = $addData->where('meta_key', 'service_county')->first();
			$SvcState = $addData->where('meta_key', 'service_state')->first();
			$svcZIP = $addData->where('meta_key', 'service_zipcode')->first();
			$billStreet = $addData->where('meta_key', 'billing_address_1')->first();
			$billCity = $addData->where('meta_key', 'billing_city')->first();
			$billState = $addData->where('meta_key', 'billing_state')->first();
			$billZIP = $addData->where('meta_key', 'billing_zipcode')->first();
			
			$svcStreet = array_get($svcStreet1, 'meta_value', "")." ".array_get($svcStreet2, 'meta_value', "");

            $response['SvcStreet'] = $svcStreet;
			$response['SvcCity'] = array_get($svcCity, 'meta_value', "");
			$response['SvcCounty'] = array_get($svcCounty, 'meta_value', "");
			$response['SvcState'] = array_get($SvcState, 'meta_value', "");
			$response['SvcZIP'] = array_get($svcZIP, 'meta_value');
			$response['BillStreet'] = array_get($billStreet, 'meta_value', "");
			$response['BillCity'] = array_get($billCity, 'meta_value', "");
			$response['BillState'] = array_get($billState, 'meta_value', "");
			$response['BillZIP'] = array_get($billZIP, 'meta_value');
			$response['State'] = array_get($SvcState, 'meta_value',"");
		}
		
		

		$response['RepPhone'] = "";
		if (!empty($salesAgent)) {
			$response['RepPhone'] = array_get($salesAgent, 'phone_no', "");
		}


		// $response['Rescom'] = "";

		$response['BillFirstName'] = "";
		$response['BillLastName'] = "";
		$billNameField = $fields->where('type', 'fullname')->where('label', "Billing Name")->first();
        if (!empty($billNameField)) {
            $bfNameData = Telesalesdata::fieldVal($leadId, array_get($billNameField, 'id'), 'first_name')->first();
            $response['BillFirstName'] = array_get($bfNameData, 'meta_value');
            $blNameData = Telesalesdata::fieldVal($leadId, array_get($billNameField, 'id'), 'last_name')->first();
            $response['BillLastName'] = array_get($lNameData, 'meta_value');
        }
		
		$response['Relationship'] = "";

		$response['Phone'] = "";
		$phnNumField = $fields->where('type', 'phone_number')->first();
		if (!empty($phnNumField)) {
			$phnNumData = Telesalesdata::fieldVal($leadId, array_get($phnNumField, 'id'))->first();
			$response['Phone'] = array_get($phnNumData, 'meta_value', "");
		}

		$response['SubmissionDate'] = date("Y-m-d H:i:s", strtotime(array_get($lead, 'created_at')));
		
		$response['ContractURL'] = "";
		if (array_get($lead, 'contract_pdf')) {
			$response['ContractURL'] = \Storage::disk('s3')->url($lead->contract_pdf);
		}

		$response['accounts'] = [];
		
        $formCommodities = $leadForm->commodities()->count();
		$response['ResidentialOrCommercial'] = "";
        if ($formCommodities > 1) {
            $leadPrograms = $lead->programs()->with('utility', 'utility.utilityCommodity')->get();
            foreach ($leadPrograms as $program) {
				$commodityName = array_get($program->utility->utilityCommodity, 'name');
                $field = $leadForm->fields()->where('type', 'textbox')->where('label', 'like', 'Account Number%' . $commodityName)->first();
                $accountArr = [];
				$accountArr['Util'] = array_get($program->utility, 'fullname');
				
				$accountArr['AccountNumber'] = "";
				$accNum = Telesalesdata::fieldVal($leadId, array_get($field, 'id'))->first();
				if (!empty($accNum)) {
					$accountArr['AccountNumber'] = array_get($accNum, 'meta_value');
				}

                $accountArr['ProductCode'] = array_get($program, 'code');
                $accountArr['Commodity'] = $commodityName;
                $response['accounts']['items'][] = $accountArr;
			}
			$customerType = $program->customerType()->first();
			if (!empty($customerType)) {
				$response['ResidentialOrCommercial'] = array_get($customerType, 'name', "");
			}
        } else {
            $leadProgram = $lead->programs()->with('utility', 'utility.utilityCommodity')->first();
			$commodityName = array_get($leadProgram->utility->utilityCommodity, 'name');
			
            $field = $leadForm->fields()->where('type', 'textbox')->where('label','like', 'Account Number%')->first();
            $accountArr = [];
			$accountArr['Util'] = array_get($leadProgram->utility, 'fullname');
			$accountArr['AccountNumber'] = "";
			$accNum = Telesalesdata::fieldVal($leadId, array_get($field, 'id'))->first();
			if (!empty($accNum)) {
				$accountArr['AccountNumber'] = array_get($accNum, 'meta_value');
			}
            $accountArr['ProductCode'] = array_get($leadProgram, 'code');
            $accountArr['Commodity'] = $commodityName;
			$response['accounts']['items'][] = $accountArr;
			$customerType = $leadProgram->customerType()->first();
			if (!empty($customerType)) {
				$response['ResidentialOrCommercial'] = array_get($customerType, 'name', "");
			}
        }

		return $response;
	}

	/**
	 * For get primary state of lead
	 * @param $telesaleId, $formId
	 */
	public function getLeadState($telesaleId, $formId) {
		try {
			$state = '';
			$stateField = FormField::where('form_id',$formId)
				->whereIn('type',['address','service_and_billing_address'])
				->where('is_primary','1')
				->with(['telesalesData' => function ($query) use ($telesaleId) {
                    $query->where('telesale_id', $telesaleId)
                    ->whereIn('meta_key',['state','service_state']);
                }])
                ->first();
            if ($stateField) {
            	$state = isset($stateField->telesalesData[0]['meta_value']) ? $stateField->telesalesData[0]['meta_value'] : '';
            	info($state);
            	$state = array_search($state, config('constants.USA_STATE_ABBR'));
            }
            return $state;
		} catch (\Exception $e) {
			Log::error('Error while getting state:'.$e);
			return '';
		}
	}

	/**
	 * For get primary e-signature language of lead
	 * @param $telesale
	 */
	public function getLeadLanguage($telesale) {
		try {
			$language = '';
			$languageField = FormField::where('form_id',$telesale->form_id)
			->where(function ($q) {
                $q->where('label','LIKE','E-Signature Language')
                ->orWhere('label','LIKE','Language');
            })
				->whereIn('type',['radio','selectbox'])
				->with(['telesalesData' => function ($query) use ($telesale) {
                    $query->where('telesale_id', $telesale->id);
                }])
				->first();
            if ($languageField) {
            	$language = isset($languageField->telesalesData[0]['meta_value']) ? $languageField->telesalesData[0]['meta_value'] : '';
            	info($language);
            }
            return $language;
		} catch (\Exception $e) {
			Log::error('Error while getting language:'.$e);
			return '';
		}
	}
	
	/**
	 * for account number checking in do not enroll list exist or not
	 *
	 * @param  mixed $var
	 * @return void
	 */
	public function isExistsInDNE($clientId, $formId, $requestFields, $fromApi = false)
	{
		try {
			$exists = false;
			$fieldKey = "value";
			$fieldIdKey = "field_id";
			$accountNumberLabel = config('constants.ACCOUNT_NUMBER_LABEL').'%';
			$accountNumFields = FormField::where("form_id",$formId)->where('label','LIKE',$accountNumberLabel)->where('type', 'textbox')->get();

			Log::info("Account Number fields: ".print_r($accountNumFields,true));
			if($fromApi) {
				$fieldKey = "values";
				$fieldIdKey = "id";
			}

			foreach($accountNumFields as $accountNumField) {

				// getting account number index in request field
				$index = array_search($accountNumField->id,array_column($requestFields, $fieldIdKey));
				if($index !== false && isset($requestFields[$index][$fieldKey]['value']) && $requestFields[$index][$fieldKey]['value']){
					$requestAccountNumber = $requestFields[$index][$fieldKey]['value'];

					// checking account number in do not enrollment listing
					$exists = DoNotEnroll::where('client_id',$clientId)->where('account_number',$requestAccountNumber)->exists();
					if($exists) {
						Log::info("Account number is found in do not enrollment list. account number: ".$requestAccountNumber);
						break;
					} else {
						Log::info("Account number is not found in do not enrollment list. account number: ".$requestAccountNumber);
					}
				} else {
					Log::info("Account number fields is empty");
				}
			}
			return $exists;
		} catch (\Exception $e) {
			Log::error('Error while account number checking in do not enroll list: '.$e);
			return $exists;
		}
	}
}
