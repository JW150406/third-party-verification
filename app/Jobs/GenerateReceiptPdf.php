<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\SelfverifyDetailTrait;
use App\Services\StorageService;
use App\models\Telesales;
use Storage;
use Log;
use PDF;

class GenerateReceiptPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SelfverifyDetailTrait;

    public $leadId;
    public $timeZone;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($leadId,$timeZone)
    {
        $this->leadId = $leadId;
        $this->timeZone = $timeZone;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $telesale = Telesales::with('client','selfverifyDetails')->find($this->leadId);

            if(!empty($telesale)) {
                $awsFolderPath = config()->get('constants.aws_folder');
                $filePath = config()->get('constants.TPV_RECEIPT_PDF_UPLOAD_PATH');
                $storageService = new StorageService;

                $customerInfo = $this->getCustomerInfo($telesale);
                $selfverifyInfo = $telesale->selfverifyDetails;
                $signature = $telesale->leadMedia->where('type','image')->first();
                $clientLogo = $gpsImage = null;

                if(!empty($signature)) {
                    $signature = Storage::disk('s3')->url($signature->url);
                }
                //$signature = Storage::disk('s3')->url($telesale->client->logo);
                if(!empty($telesale->client->logo)) {
                    $clientLogo = Storage::disk('s3')->url($telesale->client->logo);
                }

                if(!empty($customerInfo)) { 
                    
                    $key = config()->get('constants.GOOGLE_MAP_API_KEY');

                    $url= 'https://maps.googleapis.com/maps/api/staticmap?&key='.$key.'&size=370x270&maptype=roadmap&markers=size:small%7Ccolor:0xFE9200%7C'.$customerInfo['latLng'];

                    if(!empty($selfverifyInfo) && !empty($selfverifyInfo->user_latitude)) {
                       $url .= '&markers=size:mid%7Ccolor:0x0062B1%7C'.$selfverifyInfo->user_latitude.','.$selfverifyInfo->user_longitude;
                    }

                    if(!empty($telesale->salesagent_lat) && !empty($telesale->salesagent_lng)) {
                        $url .= '&markers=color:0xF542B3%7C'.$telesale->salesagent_lat.','.$telesale->salesagent_lng;
                    }
                    Log::info('map:'.$url);
                    $fileName = uniqid() . '_' . $telesale->refrence_id.'.png';
                    $imageUploaded = $storageService->uploadFileToStorage(file_get_contents($url), $awsFolderPath, $filePath, $fileName);
                    
                    if (isset($telesale->selfverifyDetails) && !empty($telesale->selfverifyDetails)) {
                        $telesale->selfverifyDetails->update(['gps_location_image'=>$imageUploaded]);
                    }
                    $gpsImage = Storage::disk('s3')->url($imageUploaded);
                }
                $questionAnswers = $telesale->questionAnswers;
                $firstAns = $questionAnswers->first();
                $answerDate = null ;
                if(!empty($firstAns)) {
                    $answerDate = $firstAns->created_at;
                }
                $events = $telesale->criticalLogs->where('error_type',0)->sortBy('created_at');
                $eventBeforeAnswer = array();
                $eventAfterAnswer = array();
                if(!empty($events)) {
                    $eventBeforeAnswer = $events->where('created_at','<=',$answerDate);
                    $eventAfterAnswer = $events->where('created_at','>',$answerDate);
                }
                Log::info('event before ans: '.print_r($eventBeforeAnswer,true));
                Log::info('event after ans: '.print_r($eventAfterAnswer,true));
                $data = [
                        'client_logo' => $clientLogo,
                        'telesale' => $telesale,
                        'customer' => $customerInfo,
                        'signature' => $signature,
                        'gpsImage' => $gpsImage,
                        'selfverifyInfo' => $selfverifyInfo,
                        //'events' => $events,
                        'eventBeforeAnswer' => $eventBeforeAnswer,
                        'eventAfterAnswer' => $eventAfterAnswer,
                        'questionAnswers' => $questionAnswers,
                        'timeZone' => $this->timeZone,
                    ];

                $pdf = PDF::loadView('admin/leads/tpv-receipt-pdf', $data);

                $fileName = 'TPVReceipt_' . $telesale->refrence_id.'.pdf';

                //$pdf->save($fileName);

                $filePath = 'clients_data/' . $telesale->client_id . '/'. config()->get('constants.CLIENT_TPV_RECEIPT_PATH');
                $path = $storageService->uploadFileToStorage($pdf->output(), $awsFolderPath, $filePath, $fileName);
                $telesale->update(['tpv_receipt_pdf'=>$path]);
            }
            
        }catch(\Exception $e) {
            Log::error('Error while generate tpv receipt pdf:-');
            Log::error($e);
        }
    }
}
