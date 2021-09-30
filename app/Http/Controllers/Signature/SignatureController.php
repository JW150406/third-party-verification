<?php

namespace App\Http\Controllers\Signature;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\LeadmediaTemp;
use App\models\TelesalesTmp;
use App\models\Clientsforms;
use App\models\Programs;
use App\models\TelesalesdataTmp;
use App\models\FormField;
use App\Services\StorageService;
use Carbon\Carbon;
use PDF;
use Log;
use Storage;
use Illuminate\Http\File;

class SignatureController extends Controller
{
    /**
     * For create view page of e-signature upload
     * @param $telesaleTmpId
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|mixed
     */
	public function create($telesaleTmpId)
    {
    	$telesaleTmpId = decode($telesaleTmpId);
    	
		if ($this->isExistsSignature($telesaleTmpId)) {
			return redirect()->route('signature.success')->withErrors('Signature already submitted.');
		}
        $telesaleTmp = TelesalesTmp::findOrFail($telesaleTmpId);
        $formId = $telesaleTmp->form_id;
        
        // For language field 
        $languageField = FormField::whereHas('form', function ($query) use ($formId) {
                $query->where('id', $formId);
            })->where(function ($q) {
                $q->where('type', '=', 'radio')
                ->orWhere('type', '=', 'selectbox');
            })
            ->where(function ($q) {
                $q->where('label','LIKE','E-Signature Language')
                ->orWhere('label','LIKE','Language');
            })->first();
        if (!empty($languageField)) {
            $languageData = TelesalesdataTmp::where('telesaletmp_id', '=', $telesaleTmp->id)
                    ->where('field_id', '=', $languageField->id)
                    ->first();
            $language = $languageData->meta_value;
        } else {
            $language = "English";
        }
        
        $form = Clientsforms::findorFail($telesaleTmp->form_id);
        $programs = Programs::whereIn('id',explode(',', $telesaleTmp->program))->with('utility')->get();

        $telesaleTmpId = $telesaleTmp->id;        
        $telesaleTmpClientId = $telesaleTmp->client_id;    
        
        $allFields = $form->fields()->orderBy('position')->with(['telesalesDataTmp' => function ($query) use ($telesaleTmpId) {
            $query->where('telesaletmp_id', $telesaleTmpId);
        }]);
        
        // For get state from address
        $address = $allFields->where(function ($q) {
                                $q->where('type', '=', 'address')
                                ->orWhere('type', '=', 'service_and_billing_address');
                            })
                            ->where('is_primary', '=', '1')
                            ->with(['telesalesDataTmp' => function ($query) use ($telesaleTmpId) {
                                $query->where(function ($qu) {
                                        $qu->where('meta_key', '=', 'state')
                                        ->orWhere('meta_key', '=', 'service_state');
                                    })
                                ->where('telesaletmp_id', $telesaleTmpId);
                            }])
                            ->first(); 
        $state = isset($address->telesalesDataTmp[0]['meta_value']) ? $address->telesalesDataTmp[0]['meta_value'] : ''; 

        $fields = $form->fields()->orderBy('position')->with(['telesalesDataTmp' => function ($query) use ($telesaleTmpId) {
                    $query->where('telesaletmp_id', $telesaleTmpId);
                }])->get()->toArray();
    	return view('frontend.customer.signature',compact('fields','programs','telesaleTmpId','telesaleTmpClientId','state','language'));
    }

    /**
     * This method is used for upload and store signature in database
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            //print_r($request->all());
            
    		$request->validate([
	            'signature' => 'required',
	        ]); 
    		$telesaleTmpId = $request->tmp_lead_id;
            $webip = $request->webip;
            
    		if ($this->isExistsSignature($telesaleTmpId)) {
    			return redirect()->route('signature.success')->withErrors('Signature already submitted.');
    		}

    		$imageParts = explode(";base64,", $request->signature);
	    	$imageTypeAux = explode("image/", $imageParts[0]);
	    	$extension = $imageTypeAux[1];
	    	$image = base64_decode($imageParts[1]);

    		$awsFolderPath = config()->get('constants.aws_folder');
            $filePath = config()->get('constants.CLIENT_LEAD_DATA_UPLOAD_PATH');
            $fileName = uniqid() . '.' . $extension;
            $storageService = new StorageService;
            $url = $storageService->uploadFileToStorage($image, $awsFolderPath, $filePath, $fileName);

            
            if ($url === false) {
            	return redirect()->route('signature.success')->withErrors('Unable to upload signature !!');
            }
            $data = [
            	'telesales_tmp_id' =>$telesaleTmpId,
            	'name' => $fileName,
            	'type' => 'image',
            	'url' => $url,
                'ip_address' => $webip
            ];
            
            LeadmediaTemp::create($data);

            // For store Acknowledgement
            if($request->has('ack_signature')) {
                $this->storeAcknowledge($request, $data);
            }

	      	return redirect()->route('signature.success')->with('success', 'Signature successfully uploaded.');
    	} catch (\Exception $e) {
    		\Log::error('Error while uploading e-signature: '.$e);
    		return redirect()->route('signature.success')->withErrors($e->getMessage());
    	}
    }

    /**
     * This method is used to check signature is exists or not
     * @param $telesaleTmpId
     * @return mixed
     */
    public function isExistsSignature($telesaleTmpId)
    {
    	return LeadmediaTemp::where('telesales_tmp_id',$telesaleTmpId)->where('type','image')->exists();
    }

    public function successOrError() {
        return view('frontend.customer.signature_success');
    }

    /**
     * for store acknowledgement
     */
    public function storeAcknowledge($request, $data) {
        try {
            if($request->ack_signature) {
                $awsFolderPath = config()->get('constants.aws_folder');
                $storageService = new StorageService;

                /*$imageParts = explode(";base64,", $request->ack_signature);
                $imageTypeAux = explode("image/", $imageParts[0]);
                $extension = $imageTypeAux[1];
                $image = base64_decode($imageParts[1]);

                $filePath = config()->get('constants.CLIENT_BOLT_ENERGY_E_SIGNATURE_UPLOAD_PATH');
                $fileName = time() . '.' . $extension;
                $signatureUrl = $storageService->uploadFileToStorage($image, $awsFolderPath, $filePath, $fileName);

                // for store signature of acknowledge
                $signatureData = [
                    'telesales_tmp_id' =>$request->tmp_lead_id,
                    'name' => $fileName,
                    'type' => 'signature2',
                    'url' => $signatureUrl,
                ];*/

                $data['type'] = 'signature2';
                $signatureUrl = isset($data['url']) ? $data['url'] : '';

                LeadmediaTemp::create($data);

                $data = [
                    'customer_name' => $request->customer_name,
                    'signature' => $signatureUrl,
                    'date' => Carbon::now()->format(getDateFormat())
                ];
                if ($request->language == config()->get('constants.LANGUAGES.SPANISH')) {
                    $pdf = PDF::loadView('frontend/customer/acknowledge_es',$data);
                } else {
                    $pdf = PDF::loadView('frontend/customer/acknowledge',$data);   
                }
                $ackFileName = 'acknowledge_'.time().'.pdf';
                $filePathForAcknowledge = config()->get('constants.CLIENT_BOLT_ENERGY_ACKNOWLEDGE_UPLOAD_PATH');
                $path = $storageService->uploadFileToStorage($pdf->output(), $awsFolderPath, $filePathForAcknowledge, $ackFileName);
                info("acknowledgement uploaded on this path: ".$path);
                // for store pdf of acknowledge
                $acknowledgeData = [
                    'telesales_tmp_id' =>$request->tmp_lead_id,
                    'name' => $ackFileName,
                    'type' => 'acknowledgement',
                    'url' => $path,
                    'ip_address' =>$request->webip
                ];
                LeadmediaTemp::create($acknowledgeData);

            }
        } catch (\Exception $e) {
            \Log::error('Getting error while uploading acknowledge: '.$e);
        }
    }
}
