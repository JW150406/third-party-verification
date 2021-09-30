<?php

namespace App\Http\Controllers\API;

use App\Jobs\SendContractPDF;
use App\models\FormField;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Role;
use App\models\Client;
use App\models\Clientsforms;
use App\models\Telesales;
use App\models\Telesalesdata;
use App\models\ScriptQuestions;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\models\Salescenter;
use App\models\CriticalLogsHistory;
use Hash;
use Illuminate\Support\Str;
use Mail;
use Session;
use Illuminate\Routing\UrlGenerator;
use App\models\Utilities;
use App\models\Salescenterslocations;
use App\models\ClientWorkspace;
use App\models\ClientWorkflow;
use App\models\Programs;
use App\models\UserAssignedForms;
use App\models\ClientAgentNotFoundScripts;
use App\models\FormScripts;
use App\models\UtilityZipcodes;
use App\models\ClientTwilioNumbers;
use App\models\Zipcodes;
use App\models\Phonenumberverification;
use App\models\EmailVerification;
use App\models\TelesalesSelfVerifyExpTime;
use App\models\Leadmedia;
use App\models\Salesagentlocation;
use Carbon\Carbon;
use PDF;
use App\Services\SegmentService;
use App\Services\StorageService;

use App\models\TextEmailStatistics;
use Validator;
use App\Services\TwilioService;
use App\Traits\LeadTrait;

class ClientsController extends Controller
{
    use LeadTrait;

    public $ClientTwilioNumbers = array();

    function __construct(){
        $this->ClientTwilioNumbers = (new ClientTwilioNumbers);
        $this->segmentService = new SegmentService;
        $this->storageService = new StorageService;
        $this->twilioService = new TwilioService;
    }

    /**
     * This method is used for send json data of contact us form in json format
     */
    public function contactform(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'clientid' => 'required',
        //     'commodity' => 'required',
        //     'programid' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => implode(',',$validator->messages()->all())
        //     ], 400);
        // }else{
        //     $error = 0;
        //     $message = "";
        //     $program_data  =  (new Programs)::find($request->programid);
        //     if( !$program_data || empty($program_data) ){
        //         $error = 1;
        //         $message = "Please select a valid program";
        //     }
        //     if( $error == 1 ) {
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => $message
        //         ], 400);
        //     }
        //     $program_data  =  (new Programs)->getSingleProgramAPI($request->programid);


        //     $client_id = $request->clientid;
        //     $Commodity = $request->commodity;
        //     $state = $request->state;
        //     $Commodity = str_replace(' ','',$Commodity);
        //     $is_dual = 0;
        //     if( $Commodity != "" && ( $Commodity =="Gas" || $Commodity == "Electric" || $Commodity =="DualFuel"  ) ){
        //         if($Commodity =="Gas" || $Commodity == "Electric"){
        //             $Commodity  = "GasOrElectric";
        //         }

        //         if($Commodity =="DualFuel"){
        //             $is_dual = 1;
        //         }

        //         $ClientFields =  (new Clientsforms)->getClientFormByCommodityType($Commodity,$client_id);
        //          if( count($ClientFields) > 0 ){
        //                $fields = json_decode($ClientFields[0]->form_fields,TRUE);
        //                $form_fileds = $this->getFormatedFields($fields,$is_dual);
        //                return response()->json([
        //                 'status' => 'success',
        //                 'message' => 'success',
        //                 'program_data' => $program_data[0],
        //                 'data' =>  $form_fileds
        //             ]);
        //          }else{
        //             return response()->json([
        //                 'status' => 'error',
        //                 'message' => "No data found. Please contact to your admin"
        //             ], 400);
        //          }


        //     }else{
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => "Invalid request"
        //         ], 400);
        //     }


        // }

        $data = '[{"id":1,"type":"label","label":"Personal Details","values":null,"meta":null,"validations":null},{"id":2,"type":"full_name","label":"Authorized Name","values":{"first_name":"","middle_initial":"","last_name":""},"meta":{"is_primary":true},"validations":{"required":true}},{"id":3,"type":"phone_number","label":"Phone Number","values":{"value":""},"meta":{"is_primary":true},"validations":{"required":true,"verify":true}},{"id":4,"type":"email","label":"Email","values":{"value":""},"meta":null,"validations":{"required":true}},{"id":5,"type":"selectbox","label":"Auth Relationship","values":null,"meta":{"options":[{"option":"Account Holder","selected":true},{"option":"Example 2","selected":false},{"option":"Example 3","selected":false}],"style_as_a_button":false},"validations":{"required":true}},{"id":6,"type":"separator","label":null,"values":null,"meta":null,"validations":null},{"id":7,"type":"label","label":"Electric Details","values":null,"meta":null,"validations":null},{"id":8,"type":"service_and_billing_address","label":"","values":{"billing_address_1":"","billing_address_2":"","billing_zipcode":"","billing_city":"","billing_state":"","service_address_1":"","service_address_2":"","service_zipcode":"","service_city":"","service_state":"","billing_unit":"","service_unit":"","billing_country":"","service_country":"","billing_lat":"","billing_lng":"","service_lat":"","service_lng":""},"meta":{"is_primary":true},"validations":{"required":true}},{"id":9,"type":"full_name","label":"Billing Name","values":{"first_name":"","middle_initial":"","last_name":""},"meta":null,"validations":{"required":true}},{"id":10,"type":"separator","label":null,"values":null,"meta":null,"validations":null},{"id":11,"type":"label","label":"Gas Details","values":null,"meta":null,"validations":null},{"id":12,"type":"service_and_billing_address","label":"","values":{"billing_address_1":"","billing_address_2":"","billing_zipcode":"","billing_city":"","billing_state":"","service_address_1":"","service_address_2":"","service_zipcode":"","service_city":"","service_state":"","billing_unit":"","service_unit":"","billing_country":"","service_country":"","billing_lat":"","billing_lng":"","service_lat":"","service_lng":""},"meta":null,"validations":{"required":true}},{"id":13,"type":"full_name","label":"Billing Name","values":{"first_name":"","":"","last_name":""},"meta":null,"validations":{"required":true}},{"id":14,"type":"separator","label":null,"values":null,"meta":null,"validations":null},{"id":15,"type":"heading","label":"Other Widgets","values":null,"meta":null,"validations":null},{"id":16,"type":"textbox","label":"Textbox","values":{"value":""},"meta":{"placeholder":"this is a placeholder"},"validations":{"required":true,"length":10}},{"id":17,"type":"textarea","label":"Text area","values":{"value":""},"meta":{"placeholder":"Enter description...."},"validations":{"required":true}},{"id":18,"type":"radio","label":"Gender","values":null,"meta":{"options":[{"option":"Male","selected":true},{"option":"Female","selected":false}],"style_as_a_button":false},"validations":{"required":true}},{"id":19,"type":"checkbox","label":"Commodity","values":null,"meta":{"options":[{"option":"Gas","selected":true},{"option":"Electric","selected":false},{"option":"Power","selected":true}],"style_as_a_button":false},"validations":{"required":true}},{"id":20,"type":"address","label":"Address","values":{"unit":"","address_1":"","address_2":"","zipcode":"","city":"","state":"","country":"","lat":"","lng":""},"meta":null,"validations":{"required":true}},{"id":21,"type":"text","label":"Information","values":null,"meta":{"text":"Please make sure you entered the details correctly so that we can verify your account."},"validations":null}]';

        return $this->success("success", "success", json_decode($data));

    }

    /**
     * For get formated fields name
     */
    function getFormatedFields($fields, $is_dual)
    {
        $form_fields = array();
        $commondity_name = "";
        if (count($fields) > 0) {
            foreach ($fields as $single_field) {
                if ($single_field['type'] == 'name') {
                    $form_fields[] = $this->getNameFields($single_field, "Authorized", "Authorized");
                } else
                    if ($single_field['type'] == 'billingname') {
                        $form_fields[] = $this->getNameFields($single_field, "Billing", "Billing");
                    } else
                        if ($single_field['type'] == 'gasbillingname') {
                            $form_fields[] = $this->getNameFields($single_field, "Gas Billing", "Gas Billing");
                        } else
                            if ($single_field['type'] == 'electricbillingname') {
                                $form_fields[] = $this->getNameFields($single_field, "Electric Billing", "Electric Billing");
                            } else
                                if ($single_field['type'] == 'serviceaddress') {
                                    $form_fields[] = $this->getAddressFields($single_field, "Service", "");
                                } else
                                    if ($single_field['type'] == 'gasserviceaddress') {
                                        $form_fields[] = $this->getAddressFields($single_field, "GasService", "");
                                    } else
                                        if ($single_field['type'] == 'electricserviceaddress') {
                                            $form_fields[] = $this->getAddressFields($single_field, "electricService", "");
                                        } else
                                            if ($single_field['type'] == 'billingaddress') {
                                                $form_fields[] = $this->getAddressFields($single_field, "Billing", "");
                                            } else
                                                if ($single_field['type'] == 'gasbillingaddress') {
                                                    $form_fields[] = $this->getAddressFields($single_field, "GasBilling", "");
                                                } else
                                                    if ($single_field['type'] == 'electricbillingaddress') {
                                                        $form_fields[] = $this->getAddressFields($single_field, "ElectricBilling", "");
                                                    } else
                                                        if ($single_field['type'] == 'address') {
                                                            $form_fields[] = $this->getCollectiveAddressFields($single_field);
                                                        } else {

                                                            if ($single_field['type'] == 'heading') {
                                                                $commondity_name = strtolower($single_field['label_text']);
                                                            }
                                                            if ($is_dual == 1 && ($single_field['type'] == 'radio' || $single_field['type'] == 'heading')) {
                                                                $single_field['type'] = $commondity_name . $single_field['type'];
                                                            }
                                                            $single_field['required'] = (isset($single_field['required']) && $single_field['required'] == 'on') ? true : false;
                                                            $single_field['fields'] = (object)array();
                                                            if (isset($single_field['options'])) {

                                                                $single_field[$single_field['type'] . 'label'] = $single_field['options']['label'];
                                                                unset($single_field['options']);
                                                            }
                                                            $form_fields[] = $single_field;
                                                        }

            }
        }

        return $form_fields;
    }

    /**
     * For save form data
     */
    public function saveformdata(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'clientid' => 'required',
            'commodity' => 'required',
            'fields' => 'required',
            'zipcode' => 'required',


        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'message' => implode(',', $validator->messages()->all())
            ], 400);
        } else {
            // $myfile = fopen("/srv/users/spark/apps/spark/public/newfile.txt", "w") or die("Unable to open file!");
            // $txt = json_encode($request->all());

            // fwrite($myfile,  $txt );

            // fclose($myfile);

            $validate_utility = $this->validateCommodity($request->commodity, $request);
            if ($validate_utility['haserror'] == 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validate_utility['message']
                ], 400);

            } else {


                $leaddata_array = array();
                $error = 0;
                $message = "";
                $Zipcode = (new Zipcodes)::where('zipcode', '=', $request->zipcode)->first();
                if (!$Zipcode || empty($Zipcode)) {
                    $error = 1;
                    $message = "Please enter a valid zipcode";
                }

                if (strtolower($validate_utility['commodity_type']) == 'gasorelectric') {


                    if ($error == 0) {
                        $program_data = (new Programs)::find($request->programid);
                        if (!$program_data || empty($program_data)) {
                            $error = 1;
                            $message = "Please select a valid program";
                        }

                        $utility = (New Utilities)::find($request->utility_id);

                        if (!$utility || empty($utility)) {
                            $error = 1;
                            $message = "Please select a valid utility";
                        }

                    }


                }
                if (strtolower($validate_utility['commodity_type']) == 'dualfuel') {
                    if ($error == 0) {
                        $gasprogram = (new Programs)->find($request->gasprogramid);
                        if (!$gasprogram || empty($gasprogram)) {
                            $error = 1;
                            $message = "Please select a valid gas program";
                        }

                        $gasutility = (New Utilities)->find($request->gasutility_id);

                        if (!$gasutility || empty($gasutility)) {
                            $error = 1;
                            $message = "Please select a valid gas utility";
                        }

                        $electricprogram = (new Programs)->find($request->electricprogramid);
                        if (!$electricprogram || empty($electricprogram)) {
                            $error = 1;
                            $message = "Please select a valid electric program";
                        }

                        $electricutility = (New Utilities)->find($request->electricutility_id);

                        if (!$electricutility || empty($electricutility)) {
                            $error = 1;
                            $message = "Please select a valid electric utility";
                        }


                    }


                }

                if ($error == 1) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $message
                    ], 400);
                }


                $client_id = $request->clientid;
                $program_data = (new Programs)->find($request->programid);
                $utility = (New Utilities)->find($request->utility_id);

                $single_data = array();


                $ClientFields = (new Clientsforms)->getClientFormByCommodityType($validate_utility['commodity_type'], $client_id);
                if (count($ClientFields) > 0) {
                    $form_id = $ClientFields[0]->id;

                    $lead_data['client_id'] = $client_id;
                    $lead_data['form_id'] = $form_id;


                    $lead_data['user_id'] = Auth::user()->id;
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
                    $refrence_id = $this->get_client_salesceter_location_code($client_id, Auth::user()->salescenter_id, Auth::user()->location_id);
                    $lead_data['refrence_id'] = $refrence_id;
                    $lead_data['is_multiple'] = 0;
                    $lead_data['multiple_parent_id'] = 0;
                    // $lead_data['verification_number'] = $verification_number;
                    $lead_data['cloned_by'] = 0;
                    $lead_data['parent_id'] = 0;

                    $telesale_id = (new Telesales)->createLead($lead_data);
                    if ($this->isJSON($request->fields)) {
                        $fields = json_decode($request->fields, true);
                        $single_data = $fields[0];
                    } else {
                        $fields = $request->get('fields');
                        if (is_array($fields)) {
                            $single_data = $fields[0];
                        } else {
                            $single_data = $fields;
                        }

                    }

                    $single_data['sourceoflead'] = 'Tablet';
                    $single_data['Commodity'] = $request->commodity;
                    $single_data['zipcode'] = $Zipcode->zipcode;
                    $single_data['zipcodeState'] = $Zipcode->state;
                    $single_data['zipcodeCity'] = $Zipcode->city;
                    if (strtolower($validate_utility['commodity_type']) == 'gasorelectric') {
                        $single_data['etf'] = (empty($program_data->etf)) ? '0' : $program_data->etf;
                        $single_data['msf'] = (empty($program_data->msf)) ? '0' : $program_data->msf;
                        $single_data['term'] = (empty($program_data->term)) ? '0' : $program_data->term;
                        $single_data['rate'] = (empty($program_data->rate)) ? '0' : $program_data->rate;
                        $single_data['Program Code'] = $program_data->code;
                        $single_data['Program'] = $program_data->name;
                        $single_data['UDC account name'] = $program_data->name;
                        $single_data['ElectricUDCAccountCode'] = $program_data->code;
                        $single_data['GasUDCAccountCode'] = $program_data->code;
                        $single_data['UDCAccountCode'] = $program_data->code;
                        $single_data['_programID'] = $request->programid;
                        $single_data['accountnumbertypename'] = $program_data->accountnumbertype;
                        $single_data['Account Number Length'] = $program_data->accountnumberlength;
                        $single_data['Account Number Type'] = $program_data->accountnumbertype;

                        $single_data['utility'] = $utility->utilityname;
                        $single_data['_utilityID'] = $request->utility_id;
                        $single_data['MarketCode'] = $utility->market;


                    }
                    if (strtolower($validate_utility['commodity_type']) == 'dualfuel') {
                        /* Dual Gas */
                        $single_data['gas_etf'] = (empty($gasprogram->etf)) ? '0' : $gasprogram->etf;
                        $single_data['gas_msf'] = (empty($gasprogram->msf)) ? '0' : $gasprogram->msf;
                        $single_data['gas_term'] = (empty($gasprogram->term)) ? '0' : $gasprogram->term;
                        $single_data['gas_rate'] = (empty($gasprogram->rate)) ? '0' : $gasprogram->rate;
                        $single_data['GasUDCAccountCode'] = $gasprogram->code;
                        $single_data['GasProgram'] = $gasprogram->name;
                        $single_data['_gasprogramID'] = $request->gasprogramid;
                        $single_data['accountnumbertypename'] = $gasprogram->accountnumbertype;
                        $single_data['Account Number Length'] = $gasprogram->accountnumberlength;
                        $single_data['gasutility'] = $gasutility->utilityname;
                        $single_data['_gasutilityID'] = $request->gasutility_id;

                        /* Dual Electric */

                        $single_data['electric_etf'] = (empty($electricprogram->etf)) ? '0' : $electricprogram->etf;
                        $single_data['electric_msf'] = (empty($electricprogram->msf)) ? '0' : $electricprogram->msf;
                        $single_data['electric_term'] = (empty($electricprogram->term)) ? '0' : $electricprogram->term;
                        $single_data['electric_rate'] = (empty($electricprogram->rate)) ? '0' : $electricprogram->rate;
                        $single_data['ElectricUDCAccountCode'] = $electricprogram->code;

                        $single_data['ElectricProgram'] = $electricprogram->name;
                        $single_data['_electricprogramID'] = $request->electricprogramid;
                        $single_data['accountnumbertypename'] = $electricprogram->accountnumbertype;
                        $single_data['Account Number Length'] = $electricprogram->accountnumberlength;
                        $single_data['utility'] = $gasutility->utilityname;
                        $single_data['electricutility'] = $gasutility->utilityname;
                        $single_data['_electricutilityID'] = $request->electricutility_id;
                    }


                    $single_data['verification_number'] = $verification_number;
                    $single_data['Lead Verification ID'] = $verification_number;

                    foreach ($single_data as $meta_key => $meta_value) {
                        if (is_array($meta_value)) {
                            ksort($meta_value);
                            $val = implode(',', $meta_value);
                        } else {
                            $val = $meta_value;
                        }

                        $single_lead_Data = array(
                            'telesale_id' => $telesale_id,
                            'meta_key' => $meta_key,
                            'meta_value' => $val,
                        );


                        (new Telesalesdata)->createLeadDetail($single_lead_Data);

                        //   if( strtolower($validate_utility['commodity_type'])  == 'dualfuel'  ){
                        //        if($meta_key == 'zipcodeState'){
                        //             $single_lead_Data= array(
                        //                 'telesale_id' => $telesale_id,
                        //                 'meta_key' => 'ServiceState',
                        //                 'meta_value' => $val,
                        //             );
                        //             (new Telesalesdata)->createLeadDetail($single_lead_Data);
                        //             $single_lead_Data= array(
                        //                 'telesale_id' => $telesale_id,
                        //                 'meta_key' => 'GasServiceState',
                        //                 'meta_value' => $val,
                        //             );
                        //             (new Telesalesdata)->createLeadDetail($single_lead_Data);

                        //             $single_lead_Data= array(
                        //                 'telesale_id' => $telesale_id,
                        //                 'meta_key' => 'ElectricBillingState',
                        //                 'meta_value' => $val,
                        //             );
                        //             (new Telesalesdata)->createLeadDetail($single_lead_Data);

                        //        }

                        //        if($meta_key == 'zipcode'){
                        //         $single_lead_Data= array(
                        //             'telesale_id' => $telesale_id,
                        //             'meta_key' => 'ServiceZip',
                        //             'meta_value' => $val,
                        //         );
                        //         (new Telesalesdata)->createLeadDetail($single_lead_Data);
                        //         $single_lead_Data= array(
                        //             'telesale_id' => $telesale_id,
                        //             'meta_key' => 'GasServiceZip',
                        //             'meta_value' => $val,
                        //         );
                        //         (new Telesalesdata)->createLeadDetail($single_lead_Data);
                        //         $single_lead_Data= array(
                        //             'telesale_id' => $telesale_id,
                        //             'meta_key' => 'ElectricBillingZip',
                        //             'meta_value' => $val,
                        //         );
                        //         (new Telesalesdata)->createLeadDetail($single_lead_Data);


                        //    }

                        //   }
                    }


                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'success',
                    'data' => array(
                        'id' => $telesale_id,
                        'reference_id' => $refrence_id,

                    )
                ]);

            }


        }


    }

    /**
     * This is used for check commodity is valid or not
     */
    function validateCommodity($commodity, $request)
    {
        $haserror = 0;
        $error_message = "";
        $commodity_type = "GasOrElectric";
        if (strtolower(str_replace(' ', '', $commodity)) == 'dualfuel') {
            if (!isset($request->gasutility_id) || !isset($request->gasprogramid)) {
                $haserror = 1;
                $error_message = "Please select Gas utility and program";
            }
            if (!isset($request->electricutility_id) || !isset($request->electricprogramid)) {
                $haserror = 1;
                $error_message = "Please select Electric utility and program";
            }
            $commodity_type = "DualFuel";
        }
        if (strtolower($commodity) == 'gas' || strtolower($commodity) == 'naturalgas' || strtolower($commodity) == 'electric') {
            if (!isset($request->utility_id) || !isset($request->programid)) {
                $haserror = 1;
                $error_message = "Please select utility and program";
            }
        }
        return array(
            'haserror' => $haserror,
            'message' => $error_message,
            'commodity_type' => $commodity_type
        );

    }

    /**
     * For get name fields
     * @param $single_filed, $nameprefix, $placeholderprefix
     */
    function getNameFields($single_filed, $nameprefix = "", $placeholderprefix = "")
    {
        $nameprefix = ($nameprefix != "") ? $nameprefix . ' ' : '';
        $placeholderprefix = ($placeholderprefix != "") ? $placeholderprefix . ' ' : '';
        $name_fields = array(
            'type' => $single_filed['type'],
            'label_text' => $single_filed['label_text'],
            'required' => ($single_filed['required'] == 'on') ? true : false,
            'fields' => (object)array(

                'firstnameplaceholder' => $placeholderprefix . 'First name',
                'firstname_name' => $nameprefix . 'First name',
                'middlenameplaceholder' => $placeholderprefix . 'Middle initial',
                'middlename_name' => $nameprefix . 'Middle initial',
                'lastnameplaceholder' => $placeholderprefix . 'Last name',
                'lastname_name' => $nameprefix . 'Last name',


            )
        );

        return $name_fields;

    }

    /**
     * For get address fields
     * @param $single_filed, $nameprefix, $placeholderprefix
     */
    function getAddressFields($single_filed, $nameprefix = "", $placeholderprefix = "")
    {


        $address_fields = array(
            'type' => $single_filed['type'],
            'label_text' => $single_filed['label_text'],
            'required' => ($single_filed['required'] == 'on') ? true : false,
            'fields' => (object)array(

                'address1_placeholder' => $placeholderprefix . 'Address 1',
                'address1_name' => $nameprefix . 'Address',
                'address2_placeholder' => $placeholderprefix . 'Address 2',
                'address2_name' => $nameprefix . 'Address2',
                'city_placeholder' => $placeholderprefix . 'City',
                'city_name' => $nameprefix . 'City',
                'state_placeholder' => $placeholderprefix . 'State',
                'state_name' => $nameprefix . 'State',
                'zipcode_placeholder' => $placeholderprefix . 'Zipcode',
                'zipcode_name' => $nameprefix . 'Zip',
            )
        );

        return $address_fields;

    }

    /**
     * For get collective address fields
     * @param $single_filed 
     */
    function getCollectiveAddressFields($single_filed)
    {


        $address_fields = array(
            'type' => $single_filed['type'],
            'label_text' => $single_filed['label_text'],
            'required' => ($single_filed['required'] == 'on') ? true : false,
            'fields' => (object)array(
                array(
                    'placeholder' => 'Address 1',
                    'name' => $single_field['label_text'] . '[]',
                ),
                array(
                    'placeholder' => 'Address 2',
                    'name' => $single_field['label_text'] . '[]',
                ),
                array(
                    'placeholder' => 'City',
                    'name' => $single_field['label_text'] . '[]',
                ),
                array(
                    'placeholder' => 'State',
                    'name' => $single_field['label_text'] . '[]',
                ),
                array(
                    'placeholder' => 'Zipcode',
                    'name' => $single_field['label_text'] . '[]',
                ),
            )
        );

        return $address_fields;

    }

    /**
     * Get form id on the bases of commodity
     *
     */

    function getFormId($formtype)
    {


    }

    /**
     * This is used for generate code from client, salescenter and location
     * @param $client_id, $selescenter_id, $location_id
     */
    function get_client_salesceter_location_code($client_id, $selescenter_id, $location_id)
    {
        $client_code = (new Client)->getClientCode($client_id);
        $salescenter_code = (new Salescenter)->getSalescenterCode($selescenter_id);
        $salescenter_location_code = (new Salescenterslocations)->getSaleslocationCode($location_id);
        $new_id = (new Telesales)->nextAutoID();
        return $client_code . '-' . $salescenter_code . '-' . $salescenter_location_code . '-' . $new_id;
    }

    /**
     * For store lead media in database and store audio file in s3 bucket
     * @param $request
     */
    public function leadmedia(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'leadid' => 'required',
            'media' => 'required',
            // 'lat' => 'required',
            // 'lng' => 'required',

        ]);


        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'message' => implode(',', $validator->messages()->all())
            ], 400);
        } else {
            $file = $request->file('media')->getClientOriginalName();
            $allowed_audio = array(
                'mp3',
                'wav',
                'aief',
                'flac',
                'mp4'
            );
            $allowed_images = array(
                'jpg',
                'jpeg',
                'tif',
                'gif',
                'png'
            );


            $filename = pathinfo($file, PATHINFO_FILENAME);
            $file = $request->file('media')->getClientOriginalExtension();
            $extension = $request->file('media')->getClientOriginalExtension();

            $error = 0;
            $type = 'audio';
            $message = "";

            if (in_array(strtolower($extension), $allowed_audio)) {
                $type = 'audio';
            } else if (in_array(strtolower($extension), $allowed_images)) {
                $type = 'image';
            } else {
                $error = 1;
                $message = "Please upload valid file format";
            }

            $Telesales = (new Telesales)::find($request->leadid);
            if (!$Telesales || empty($Telesales)) {
                $error = 1;
                $message = "Please enter a valid lead ID";
            }

            if ($type == 'audio') {
                $filePath = 'clients_data/' . $Telesales->client_id . '/' . config()->get('constants.CLIENT_CONSENT_RECORDING_UPLOAD_PATH');
            } else {
                $filePath = config()->get('constants.CLIENT_LEAD_DATA_UPLOAD_PATH');
            }

            $file = $request->file('media');
            $awsFolderPath = config()->get('constants.aws_folder');
            
            $fileName = uniqid() . '.' . $extension;
            $imageUploaded = $this->storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $fileName);

            if ($imageUploaded === false) {
                return response()->json(['status' => 'error', 'message' => 'Unable to upload media !!'], 500);
            }
//            $path = Storage::disk('s3')->putFileAs('/client/leaddata', $request->file('media'), $filename);

            if ($error == 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => $message
                ], 400);
            }

            $leadmedia = new Leadmedia();
            $leadmedia->name = $filename;
            $leadmedia->type = $type;
            $leadmedia->media_type = $type;
            $leadmedia->url = $imageUploaded;
            $leadmedia->telesales_id = $request->leadid;
            $leadmedia->save();

            return response()->json([
                'status' => 'success',
                'message' => 'success'
            ]);


        }

    }

    /**
     * For check string is in json or not
     */
    function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    /**
     * This function is currently not in use
     */
    function sendcontract(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'leadid' => 'required'

            ]);


            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => implode(',', $validator->messages()->all())
                ], 400);
            } else {
                $leadid = $request->leadid;
                $data = (new Telesales)->getDetailsForPdf($leadid);

                $mstatus = 0;
                if (!empty($data->toArray())) {
                    $leadData = $data[0];
                    // dd($leadData);
                    if ($leadData->Email != "") {
                        $name = $leadData->FirstName;
                        $to=$email = $leadData->Email;
                        // $to = "mansi.inexture@gmail.com";
                        $subject = "TPV360 contract";
                        $mainMessage = " Dear Mr.".$name.",<br/>";
                        $mainMessage .= "Welcome to the ".$name."'s energy program, thank you for enrolling.  Attached you will find a signed copy of your contract including the full terms and conditions. <br/><br/>";
                        $mainMessage .= "If you have any further questions, please contact <client>'s Customer Service Team at 1800 785 733.<br/><br/>";
                        $mainMessage .= "Regards<br/><br/>";
                        $mainMessage .= $name;

                        Mail::send([],[], function ($message) use ($subject, $to, $mainMessage, $leadid){
                            $file = Storage::disk('public')->path('contract/pdf/'.$leadid.'.pdf');
                            $message->attach($file, [
                                'as' => 'contract.pdf',
                                'mime' => 'application/pdf',
                            ]);
                            $message->to($to)
                                ->subject($subject)
                                ->setBody($mainMessage, 'text/html');
                            });
                            $textEmailStatistics = new TextEmailStatistics();
                            $textEmailStatistics->type = 1;
                            $textEmailStatistics->save();

                        $mstatus = 1;

                    } else {
                      \Log::error("Lead Data not found !" . print_r($leadData,true));
                      return response()->json([
                            'status' => 'error',
                            'message' => 'Lead Data not found !'
                        ]);
                    }


                }

                if ($mstatus == 1) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Contract sent to email'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Unable to send contract"
                    ], 400);
                }

            }
        } catch (\Exception $e) {
            \Log::error($e);
            \Log::error("Error while sending contract: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => "Something went wrong while sending contract !!"
            ], 500);
        }
    }

    /**
     * For generate otp for phone number verification
     */
    public function generateotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phonenumber' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => implode(',', $validator->messages()->all())
            ], 400);
        } else {
            $to = $request->phonenumber;
            $six_digit_random_number = mt_rand(100000, 999999);
            $message = "Your TPV360 security code is: {$six_digit_random_number}";

            $user = Auth::user();
            $phones = $this->ClientTwilioNumbers->getNumber(array_get($user, 'client_id'));
            $tpvNumber = '';
            if(!empty($phones) && $phones != null) {
                $tpvNumber = $phones->phonenumber;
            }

            if ($verification = Phonenumberverification::where('phonenumber', $to)->where('status', 'pending')->first()) {
                $response = $verification->update(
                    ['otp' => $six_digit_random_number]);
            } else {
                $verification = Phonenumberverification::create(
                    ['phonenumber' => $to, 'otp' => $six_digit_random_number, 'status' => 'pending']);
            }

            if (!$request->has('otp_type') || $request->get('otp_type') == "" || ($request->has('otp_type') && in_array(strtolower($request->get('otp_type')), array_values(config()->get('constants.PHONE_NUM_VERIFICATION_OTP_TYPE'))))) {
                if (!$request->has('otp_type') || $request->get('otp_type') == "" || strtolower($request->get('otp_type')) == config()->get('constants.PHONE_NUM_VERIFICATION_OTP_TYPE.SMS')) {
                    $statisticsType = 2;
                    $message_response = app('App\Http\Controllers\Conference\ConferenceController')->sendmessage($to, $message);
                } else {
                    $statisticsType = 3;
                    $message_response = $this->twilioService->makeVoiceCall($tpvNumber, $to, $verification->id);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid OTP type.'
                ], 400);
            }


            if ($message_response == true) {
                $textEmailStatistics = new TextEmailStatistics();
                $textEmailStatistics->type = $statisticsType;
                $textEmailStatistics->save();
                return response()->json([
                    'status' => 'success',
                    'message' => "OTP successfully sent"
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Unable to send message. Please try again."
                ], 400);
            }


        }
    }

    /**
     * For verify phone number otp
     */
    public function verifyotp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phonenumber' => 'required',
            'otp' => 'required',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'message' => implode(',', $validator->messages()->all())
            ], 400);
        } else {

            $phonenumber = $request->phonenumber;
            $otp = $request->otp;

            $getresult = DB::table('phonenumberverification')
                ->where('phonenumber', $phonenumber)
                ->where('otp', $otp)
                ->where('status', 'pending')
                ->first();
            if ($getresult) {

                $Phonenumberverification = (new Phonenumberverification)::find($getresult->id);

                $Phonenumberverification->status = 'verified';
                $Phonenumberverification->verifiedby = Auth::user()->id;

                $Phonenumberverification->save();

                return response()->json([
                    'status' => 'success',
                    'message' => "OTP verified"
                ], 200);

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Invalid OTP"
                ], 400);
            }

        }
    }
    
    /**
     * For generate otp for verification of email address
     */
    public function generateotpEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'message' => implode(',', $validator->messages()->all())
            ], 400);
        } else {
            $to = $request->email;
            $six_digit_random_number = mt_rand(100000, 999999);
            $message = "Your TPV360 security code is: {$six_digit_random_number}";

            $message_response = $this->sendOtpEmail($to, $message);
            if ($message_response == true) {
                $emailVerification = EmailVerification::where('email', $to)
                    ->where('status', 'pending')
                    ->first();

                if (!$emailVerification) {
                    $emailVerification = new EmailVerification();
                    $emailVerification->email = $to;
                    $emailVerification->otp = $six_digit_random_number;
                    $emailVerification->status = 'pending';
                } else {
                    $emailVerification->otp = $six_digit_random_number;
                    $emailVerification->status = 'pending';
                }

                $emailVerification->save();

                return response()->json([
                    'status' => 'success',
                    'message' => "OTP successfully sent"
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Unable to send email. Please try again."
                ], 400);
            }


        }
    }

    /**
     * For verify otp of email
     */
    public function verifyotpEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'otp' => 'required',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'message' => implode(',', $validator->messages()->all())
            ], 400);
        } else {

            $email = $request->email;
            $otp = $request->otp;

            $emailVerification = EmailVerification::where('email', $email)
                ->where('otp', $otp)
                ->where('status', 'pending')
                ->first();
            if ($emailVerification) {

                $emailVerification->status = 'verified';
                $emailVerification->verifiedby = Auth::user()->id;
                $emailVerification->save();

                return response()->json([
                    'status' => 'success',
                    'message' => "OTP verified"
                ], 200);

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Invalid OTP"
                ], 400);
            }

        }
    }

    /**
     * This method is used for self verification
     */
    public function selfverify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'verification_mode' => 'required',
            'leadid' => 'required',
        ]);


        if ($validator->fails()) {

            return response()->json([
                'status' => 'error',
                'message' => implode(',', $validator->messages()->all())
            ], 400);
        } else {
            $convertToLower = str_replace("sms", "phone", strtolower($request->verification_mode));
            $verification_mode = explode(',', $convertToLower);

            $response = $this->storeSelfVerifyLink($request->leadid,$verification_mode);

            if (isset($response['status']) && $response['status']) {
                return response()->json([
                    'status' => 'success',
                    'message' => $response['message']
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $response['message']
                ], 400);
            }
        }

    }    

    /**
     * For send otp in email
     */
    function sendOtpEmail($to,$message_content)
    {

        $subject = 'Your verification code';

        // Sending email
        Mail::send('emails.common', ['msg' => $message_content], function($mail) use ($to, $subject) {
            $mail->to($to);
            $mail->subject($subject);
        });

        $textEmailStatistics = new TextEmailStatistics();
        $textEmailStatistics->type = 1;
        $textEmailStatistics->save();
        return true;
    }

    // public function proceedToSegment($lead) {
        
    //     $lead = Telesales::with('teleSalesData')->find($lead->id);
    //     $this->segmentService->createIdentity($lead);
    //     $trackCreated = $this->segmentService->createTrack($lead);
    //     if ($trackCreated) {
    //         \Log::info("Segment track of lead creation created for lead: " . array_get($lead, 'id'));
    //     } else {
    //         \Log::error("Unable to create track of lead creation for lead: " . array_get($lead, 'id'));
    //     }
    // }

    /**
     * For get id and name of all active clients
     * @param $request
     */
    public function getAllClients(Request $request)
    {
        try{
            if($request->header('mobile')!=null && isset($request->headers))
            {
                $mobile = $request->header('mobile');
                $roles = $this->getUserRoles($mobile);
                if(isset($roles) && !empty($roles))
                {
                    if($roles->name == 'admin')
                    {
                        $clients = Client::select('id', 'name')->where('status','active')->get();
                    }

                    else if($roles->name == 'client_admin')
                    {
                        $clients = Client::select('id', 'name')->where('status','active')->where('id',$roles->client_id)->get();
                    }
                    else
                    {
                        return $this->error(0, "Unauthorized Access",401);
                    }
                    $data = ['clients' => $clients];
                    return $this->success(1,"Clients list fetched successfully", $data);
                }
                else
                {
                    return $this->error(0, "Unknown Credentials",400);
                }
            }
            else
            {
                return $this->error(0, "Phone number Not Found",400);
            }

        }catch (\Exception $e) {
    		Log::error("Error while retrieving client list :" . $e->getMessage());
    		return $this->error("error", "Something went wrong, Please try again later !!", 500);
    	}
    }

    /**
     * For get details of all salescenters
     * @param $request
     */
    public function getAllSalesCenters(Request $request)
    {
        try{


            if($request->header('mobile')!=null && isset($request))
            {
                $mobile = $request->header('mobile');
                $roles = $this->getUserRoles($mobile);

                $salesCenters = Salescenter::leftjoin('clients', 'clients.id', '=', 'salescenters.client_id')->select('salescenters.id', 'salescenters.name', 'clients.id as client_id', 'clients.name as client_name')
                ->where('clients.status','active')
                ->where('salescenters.status','active');


                if (isset($request->client_id) && isset($request->client_name) && (!empty($request->client_id)) && (!empty($request->client_name))) {
                    $salesCenters->where('clients.name', $request->client_name)->where('salescenters.client_id', $request->client_id);
                } else if (isset($request->client_id) && !empty($request->client_id)) {
                    $salesCenters->where('salescenters.client_id', $request->client_id);
                } else if (isset($request->client_name) && !empty($request->client_name)) {
                    $salesCenters->where('clients.name', $request->client_name);
                }
                if(isset($roles) && !empty($roles))
                {
                    if($roles->name == 'admin')
                    {
                        $salesCenters = $salesCenters->get();
                    }
                    else if($roles->name == 'client_admin')
                    {
                        $salesCenters = $salesCenters->where('clients.id',$roles->client_id)->get();
                    }
                    else if($roles->name == 'sales_center_admin')
                    {
                        $salesCenters = $salesCenters->where('salescenters.id',$roles->salescenter_id)->get();
                    }
                    else
                    {
                        return $this->error(0, "Unauthorized Access",401);
                    }

                    $data = ['sales_centers' => $salesCenters];
                    return $this->success(1,"Sales centers list fetched sucessfully",$data);
                }
                else
                {
                    return $this->error(0, "Unknown Credentials",400);
                }
            }
            else
            {
                return $this->error(0, "Phone number Not Found",400);
            }
        } catch (\Exception $e) {
    		Log::error("Error while retrieving Salescenter list :" . $e->getMessage());
    		return $this->error("error", "Something went wrong, Please try again later", 500);
    	}
    }

    /**
     * For get sales center location
     * @param $request
     */
    public function getSalesCentersLocations(Request $request)
    {
        try{

            if($request->header('mobile')!=null && isset($request))
            {
                $mobile = $request->header('mobile');
                $roles = $this->getUserRoles($mobile);

                $salescenterLocations = Salescenterslocations::leftjoin('clients','clients.id','salescenterslocations.client_id')
                    ->leftjoin('salescenters','salescenters.id','salescenterslocations.salescenter_id')
                    ->where('clients.status','active');
                if(isset($request->client_id) && (!empty($request->client_id)))
                {
                    $salescenterLocations = $salescenterLocations->where('clients.id',$request->client_id);
                }
                if(isset($request->client_name) && (!empty($request->client_name)))
                {
                    $salescenterLocations = $salescenterLocations->where('clients.name',$request->client_name);
                }
                if(isset($request->salescenter_id) && (!empty($request->salescenter_id)))
                {
                    $salescenterLocations = $salescenterLocations->where('salescenters.status','active')->where('salescenters.id',$request->salescenter_id);
                }
                if(isset($request->salescenter_name) && (!empty($request->salescenter_name)))
                {
                    $salescenterLocations = $salescenterLocations->where('salescenters.status','active')->where('salescenters.name',$request->salescenter_name);
                }
                $salescenterLocations = $salescenterLocations->select('salescenterslocations.id', 'salescenterslocations.name', 'clients.name as client_name', 'clients.id as client_id', 'salescenters.name as sales_center_name', 'salescenters.id as sales_center_id');
                if(isset($roles) && !empty($roles))
                {
                    if($roles->name == 'admin')
                    {
                        $salescenterLocations = $salescenterLocations->get();
                    }
                    else if($roles->name == 'client_admin')
                    {
                        $salescenterLocations = $salescenterLocations->where('clients.id',$roles->client_id)->get();
                    }
                   else if($roles->name == 'sales_center_admin')
                    {
                        $salescenterLocations = $salescenterLocations->where('salescenters.id',$roles->salescenter_id)->get();
                    }
                    else
                    {
                        return $this->error(0, "Unauthorized Access",401);
                    }

                    $data = ['locations' => $salescenterLocations];
                    return $this->success(1,"Locations list fetched sucessfully",$data);

                }
                else
                {
                    return $this->error(0, "Unknown Credentials",400);
                }

            }
            else
            {
                return $this->error(0, "Phone number Not Found",400);
            }


        }catch (\Exception $e) {
    		Log::error("Error while retrieving SalescenterLocation list :" . $e->getMessage());
    		return $this->error("error", "Something went wrong, Please try again later !!", 500);
    	}

    }

    /**
     * For get status of lead
     */
    public function getLeadStatus(Request $request)
    {
        try{
            $message = "";
            $leadArray = [];
            $statusArr = [];

            if($request->header('mobile')!=null && isset($request))
            {

                $mobile = $request->header('mobile');
                $roles = $this->getUserRoles($mobile);
                $leadStatusCount = Telesales::leftJoin('users', 'users.id', '=', 'telesales.user_id')
                    ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
                    ->leftJoin('salescenterslocations', 'salescenters.id', '=', 'salescenterslocations.salescenter_id')
                    ->leftJoin('clients','clients.id','salescenterslocations.client_id')
                    ->select(DB::raw('count(telesales.status) as count' ),'telesales.status');

                if(isset($request->client_id) && (!empty($request->client_id)))
                {
                    $leadStatusCount = $leadStatusCount->where('telesales.client_id', $request->client_id);
                }

                if(isset($request->client_name) && (!empty($request->client_name)))
                {
                    $leadStatusCount = $leadStatusCount->where('clients.name', $request->client_name);
                }

                if(isset($request->salescenter_id) && (!empty($request->salescenter_id)))
                {
                    $leadStatusCount = $leadStatusCount->where('salescenters.status','active')->where('salescenters.id',$request->salescenter_id);
                }

                if(isset($request->salescenter_name) && (!empty($request->salescenter_name)))
                {
                    $leadStatusCount = $leadStatusCount->where('salescenters.status','active')->where('salescenters.name',$request->salescenter_name);
                }

                if(isset($request->location_id) && (!empty($request->location_id)))
                {
                    $leadStatusCount = $leadStatusCount->where('salescenterslocations.id',$request->location_id);
                }
                if(isset($request->location_name) && (!empty($request->location_name)))
                {
                    $leadStatusCount = $leadStatusCount->where('salescenterslocations.name',$request->location_name);
                }
                if(isset($roles) && !empty($roles))
                {
                    if($roles->name == 'admin')
                    {
                        $leadStatusCount = $leadStatusCount;
                    }
                    else if($roles->name == 'client_admin')
                    {
                        $leadStatusCount = $leadStatusCount->where('clients.id',$roles->client_id);
                    }
                    else if($roles->name == 'sales_center_admin')
                    {
                        $leadStatusCount = $leadStatusCount->where('salescenters.id',$roles->salescenter_id);
                    }
                    else
                    {
                        return $this->error(0, "Unauthorized Access",401);

                    }

                    if(!isset($request->status) && (empty($request->status)))
                    {

                        $leadStatusCount = $leadStatusCount->groupBy('telesales.status');

                    }
                    else
                    {

                        $statusArr = explode(',',$request->status);

                        foreach($statusArr as  $k => $status)
                        {
                            $statusArr[$k] = strtolower(config('constants.VERIFICATION_STATUS_CHART_LEADS.'.ucfirst(strtolower($status))));
                        }

                        $leadStatusCount = $leadStatusCount->whereIn('telesales.status',$statusArr)->groupBy('telesales.status');

                    }
                    $leadStatusCount = $leadStatusCount->get();

                    if(empty($statusArr))
                    {

                        // $getStatuList = Telesales::getLeadsStatusList();
                        // $statusArr = $getStatuList->pluck('status')->toArray();
                        $statusArr = ['pending','verified','decline','cancel','hangup'];
                    }
                    foreach($statusArr as $k => $v)
                    {

                        $leadArray[$k] = [
                            'status' => config('constants.VERIFICATION_STATUS_CHART.'.ucfirst($v)),
                            'value' => 0
                        ];

                    }
                    foreach($leadStatusCount as $k => $v)
                    {
                        if(isset($statusArr) && count($statusArr) >0)
                        {
                            $key = array_search($v->status,$statusArr);
                            $leadArray[$key]['value'] = $v->count;
                        }
                        else
                        {
                            $leadArray[] = [
                                'status' => config('constants.VERIFICATION_STATUS_CHART.'.ucfirst($v->status)),
                                'value' => $v->count
                            ];
                        }

                    }

                    if(empty($leadArray))
                    {
                        return $this->success(1,"No leads found");
                    }
                    $data = $leadArray;
                    return $this->success(1,"Leads count fetched successfully", $data);
                }
                else
                {
                    return $this->error(0, "Unknown Credentials",400);
                }
            }
            else
            {
                return $this->error(0, "Phone number Not Found",400);
            }


        } catch (\Exception $e) {
    		Log::error("Error while retrieving Lead count :" . $e->getMessage());
    		return $this->error("error", "Something went wrong, Please try again later !!", 500);
    	}
    }

    /**
     * Query for get user roles
     */
    function getUserRoles($mobile)
    {
        $mobile = substr($mobile,2);

        $roles = User::leftjoin('role_user','users.id','=','role_user.user_id')
            ->leftjoin('roles','roles.id','=','role_user.role_id')
            ->select('role_id','roles.name','users.client_id','salescenter_id')
            ->where('users.phone_no',$mobile)
            ->get()->first();

        return $roles;
    }

    /**
     * For get list of all active client
     */
    public function index(Request $request){
        try{
            $q = Client::where('status','=','active')->orderBy('name')->select('id','name');

            if ($request->has('client_id')) {
                $q->where('id', $request->client_id);
            }

            $clients = $q->get();

            Log::info("Get all clients successfully.");
            return $this->success('success','success', $clients);
        }catch (\Exception $e){
            Log::error("Error while retrieving all clients:" . $e->getMessage());
            return $this->error("error", "Something went wrong, Please try again later !!", 500);
        }

    }

}
