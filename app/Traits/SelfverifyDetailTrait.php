<?php
 
namespace App\Traits;

use Log;
use Browser;
use App\models\FormField;
use App\models\Telesales;
use App\models\Telesalesdata;
use App\models\SelfverifyDetail;
trait SelfverifyDetailTrait {
 
    /**
     * This trait method is used to store/update self verification details in db
     * @param $telesale, $user_latitude=null, $user_longitude
     */
    public function saveSelfverifyDetail($telesale,$user_latitude=null,$user_longitude=null)
    {
    	try{
    		if(!empty($telesale)) {
    			$data = [
	    			'telesale_id' =>$telesale->id,
	    			'ip' => \Request::ip(),
					'platform_name' => Browser::deviceFamily(),
					'plaform_model' => Browser::deviceModel(),
					'os' => Browser::platformName(),
					'os_version' => Browser::platformVersion(),
					'browser' => Browser::browserFamily(),
					'browser_version' => Browser::browserVersion(),
                    'user_latitude' => $user_latitude,
                    'user_longitude' => $user_longitude,
	    		];
	    		SelfverifyDetail::updateOrCreate(['telesale_id' =>$telesale->id],$data);
    		} else {
    			Log::error('self verify details creation failed.');
    		}   		
    		
    	}catch(\Exception $e){
    		Log::error($e);
    	}
    }

    /**
     * This method is used for get particular customer information
     * @param $telesale
     */
    public function getCustomerInfo($telesale)
    {
        try {
            if (!empty($telesale)) {

                $phoneNumber = $clientLogo = $serviceAddress = $latLng =  null;

                $customerName = $this->getCustomerName($telesale);

                $fullAddress = $this->getCustomerPrimaryAddress($telesale);

                if (!empty($fullAddress)) {
                    $serviceAddress = $fullAddress['address'];
                    $latLng = $fullAddress['latLng'];
                }

                $phoneField = FormField::where('form_id', $telesale->form_id)->where('is_primary', 1)->where('type', 'phone_number')->first();

                if(!empty($phoneField)) {
                    $telesaleData = Telesalesdata::where('field_id', $phoneField->id)->where('meta_key','value')->where('telesale_id', $telesale->id)->first();
                    if(!empty($telesaleData)) {
                        $phoneNumber = $telesaleData->meta_value;
                    }
                }



                $customer = [
                    'name' => $customerName,
                    'phoneNumber' => $phoneNumber,
                    'serviceAddress' => $serviceAddress,
                    'latLng' => $latLng,
                ];
                return $customer;
            } 
            
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * This trait method is used to get customer name from the given parameter values
     * @param $telesale
     */
    public function getCustomerName($telesale)
    {
        try {
            $customerName = null; 
            if (!empty($telesale)) {

                $nameField = FormField::where('form_id', $telesale->form_id)->where('is_primary', 1)->where('type','fullname')->first();
                if(!empty($nameField)) {
                    $telesaleData = Telesalesdata::select('meta_key','meta_value')->where('field_id', $nameField->id)->whereIn('meta_key',['first_name','middle_initial','last_name'])->where('telesale_id', $telesale->id)->whereNotNull('meta_value')->get();
                    $fullName = [];
                    foreach ($telesaleData as $value) {
                        switch ($value['meta_key']) {
                            case 'first_name':
                                $fullName[0] = $value['meta_value'];
                                break;
                            case 'middle_initial':
                                $fullName[1] = $value['meta_value'];
                                break;
                            default:
                                $fullName[2] = $value['meta_value'];
                                break;
                        }
                    }
                    if(!empty($fullName)) {
                        // sort by key
                        ksort($fullName);
                        $customerName = ucwords(implode(' ', $fullName));
                    }
                }
            }
            Log::info('customer full name: '. $customerName);
            return $customerName;

        } catch (\Exception $e) {
            Log::error($e);
            return null;
        }        
    }

    /**
     * This method is used to get customer primary address from the given parameter
     * @param $telesale
     */
    public function getCustomerPrimaryAddress($telesale)
    {
        try {
            $address = $latLng = null; 
            if (!empty($telesale)) {                
                $addressField = FormField::where('form_id', $telesale->form_id)->where('is_primary', 1)->whereIn('type',['address','service_and_billing_address'])->orderBy('id')->first();
                if(!empty($addressField)) {
                    $telesaleData = Telesalesdata::where('field_id', $addressField->id)->where('telesale_id', $telesale->id)->whereNotNull('meta_value')->get();

                    if ($addressField->type == 'address') {
                        $fullAddress = $this->getAddress($telesaleData);
                    } else {
                        $fullAddress = $this->getServiceAddress($telesaleData);  
                    }

                    if(!empty($fullAddress)) {
                        // sort by key
                        ksort($fullAddress['address']);
                        $address = ucwords(implode(', ',$fullAddress['address']));

                        ksort($fullAddress['latLng']);
                        $latLng = ucwords(implode(',',$fullAddress['latLng']));
                    }
                }
            }

            $data = [
                'address' => $address,
                'latLng' => $latLng
            ];

            Log::info('customer primary address: '. print_r($data, true));

            return $data;

        } catch (\Exception $e) {
            Log::error($e);
            return null;
        }        
    }

    /**
     * For get formatted address from passing parameter
     * @param $address
     */
    public function getAddress($address)
    {
        $fullAddress = $latLng = [];
        foreach ($address as $key => $value) {
            switch ($value->meta_key) {
                case 'unit':
                    $fullAddress[0] = $value->meta_value;
                    break;
                case 'address_1':
                    $fullAddress[1] = $value->meta_value;
                    break;
                case 'address_2':
                    $fullAddress[2] = $value->meta_value;
                    break;
                case 'city':
                    $fullAddress[3] = $value->meta_value;
                    break;
                case 'state':
                    $fullAddress[4] = $value->meta_value;
                    break;
                case 'zipcode':
                    $fullAddress[5] = $value->meta_value;
                    break;
                case 'country':
                    $fullAddress[6] = $value->meta_value;
                    break;
                case 'lat':
                    $latLng[0] = $value->meta_value;
                    break;
                case 'lng':
                    $latLng[1] = $value->meta_value;
                    break;                
                default:
                    break;
            }
        }

        $data = [
            'address' => $fullAddress,
            'latLng' => $latLng
        ];

        return $data;
    }

    /**
     * For get service address from given parameter values
     * @param $address
     */
    public function getServiceAddress($address)
    {
        $fullAddress = $latLng = [];
        foreach ($address as $key => $value) {
            switch ($value->meta_key) {
                case 'service_unit':
                    $fullAddress[0] = $value->meta_value;
                    break;
                case 'service_address_1':
                    $fullAddress[1] = $value->meta_value;
                    break;
                case 'service_address_2':
                    $fullAddress[2] = $value->meta_value;
                    break;
                case 'service_city':
                    $fullAddress[3] = $value->meta_value;
                    break;
                case 'service_state':
                    $fullAddress[4] = $value->meta_value;
                    break;
                case 'service_zipcode':
                    $fullAddress[5] = $value->meta_value;
                    break;
                case 'service_country':
                    $fullAddress[6] = $value->meta_value;
                    break;
                case 'service_lat':
                    $latLng[0] = $value->meta_value;
                    break;
                case 'service_lng':
                    $latLng[1] = $value->meta_value;
                    break;                        
                default:
                    break;
            }
        }

        $data = [
            'address' => $fullAddress,
            'latLng' => $latLng
        ];

        return $data;
    }

}