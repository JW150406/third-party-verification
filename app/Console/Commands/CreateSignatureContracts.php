<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StorageService;
use App\models\Telesales;
use App\models\Leadmedia;
use App\Traits\SelfverifyDetailTrait;
use App\Traits\LeadTrait;
use Carbon\Carbon;
use Storage;
use PDF;
use Log;

class CreateSignatureContracts extends Command
{
    use SelfverifyDetailTrait, LeadTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'contracts:generate {startDate} {endDate}';
    protected $signature = 'contracts:generate 
                            {--l=* : The ID of Lead} 
                            {--s= : Start date of Lead} 
                            {--e= : End date of Lead}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate signature contracts for bolt Energy';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $leadIds = $this->option('l');
            $startDate = $this->option('s');
            $endDate = $this->option('e');

            if (empty($leadIds) && (empty($startDate) || empty($endDate))) {
                Log::info("Required lead or date parameter");
                $this->error('Please enter lead or date parameter');
                return ;
            }
            if (!empty($startDate) && !empty($endDate)) {
                $startDate = Carbon::parse($startDate,getClientSpecificTimeZone())->setTimezone('UTC')->format('Y-m-d H:i:s');
                $endDate = Carbon::parse($endDate,getClientSpecificTimeZone())->setTimezone('UTC')->format('Y-m-d H:i:s');
            }
            Log::info("Contracts: Lead Ids: " . print_r($leadIds,true));
            Log::info("Contracts: Start Date: " . $startDate . " | End Date: " . $endDate);

            $diskFtp = "boltenegry_ftp";
            $ftpPath = config('constants.CLIENT_BOLT_ENEGRY_FTP_TRANSFER_CONTRACTS_FOLDER');
            $clientId = config('constants.CLIENT_BOLT_ENEGRY_CLIENT_ID');
            $awsFolderPath = config('constants.aws_folder');
            $objStorageService = new StorageService;
            $telesales = Telesales::where('client_id',$clientId);
            if (!empty($leadIds)) {
                $telesales->whereIn('id',$leadIds);
            }
            if (!empty($startDate) && !empty($endDate)) {
                $telesales->whereBetween('created_at',[$startDate,$endDate]);
            }
            $telesales = $telesales->get();
            Log::info("Contracts: Found " . $telesales->count() . " leads");

            // For generate contracts
            foreach($telesales as $telesale) {
                Log::info("Contracts: Contract for lead: " . $telesale->id);
                $signature = Leadmedia::getSignature($telesale->id)->first();
                if (empty($signature)) {
                    Log::info("Contracts: signature not found");
                    continue;
                }
                $data = [];
                $customerName = $this->getCustomerName($telesale);
                $data['signature'] = Storage::disk('s3')->url($signature->url); 
                $data['customerName'] = $customerName; 
                $data['isVisible'] = true;
                $data['date'] = Carbon::parse($telesale->created_at)->format(getDateFormat());
                $state = $this->getLeadState($telesale->id, $telesale->form_id); 
                if ($state == 'CA') {
                    Log::info("Contracts: state CA");
                    $language = $this->getLeadLanguage($telesale); 
                    if($language == config('constants.LANGUAGES.SPANISH')) {
                        Log::info("Contracts: Lang: Spanish");
                        $pdf = PDF::loadView('frontend.customer.ca_spanish_t_and_c', $data);
                    } else {
                        Log::info("Contracts: Lang: English");
                        $pdf = PDF::loadView('frontend.customer.ca_english_t_and_c', $data);
                    }
                    Log::info("Contracts: Generating Ack");
                    $this->generateAcknowledge($telesale, $language, $customerName);
                } else {
                    Log::info("Contracts: state else");
                    $pdf = PDF::loadView('frontend.customer.in_t_and_c', $data);
                }
                $filePath = 'clients_data/' . $telesale->client_id . '/' . config('constants.CLIENT_CONTRACTS_PATH');
                $fileName = $telesale->refrence_id."-".Carbon::parse($telesale->created_at)->format('Y-m-d-h-i-s').'.pdf';
                $path = $objStorageService->uploadFileToStorage($pdf->output(), $awsFolderPath, $filePath, $fileName);
                if ($path !== false) {
                    Log::info("Contracts: path: " . $path);
                   $telesale->update(['contract_pdf'=>$path]);

                    // For store contracts pdf in client's FTP
                    $objStorageService->uploadFileToFTP($pdf->output(),$ftpPath, $fileName,$diskFtp);
                    info("successfully save signature contracts of lead Id:".$telesale->id);
                }
            }
            $this->info('Successfully generate contracts.');
        } catch (\Exception $e) {
            Log::error("Getting error while generating signature contracts: ".$e);
        }
    }

    /**
     * This method is used to store acknowledgement
     * @param $telesale, $language, $customerNames
     */
    public function generateAcknowledge($telesale, $language, $customerName) {
        try {
            Log::info("Ack: start");

            $signature = Leadmedia::getAckSignature($telesale->id)->first();
            if($signature) {
                Log::info("Ack: signature found");
                $data = [
                    'customer_name' => $customerName,
                    'signature' => $signature->url,
                    'date' => Carbon::parse($telesale->created_at)->format(getDateFormat())
                ];
                if ($language == config()->get('constants.LANGUAGES.SPANISH')) {
                    Log::info("Ack: lang spanish");
                    $pdf = PDF::loadView('frontend/customer/acknowledge_es',$data);
                } else {
                    Log::info("Ack: lang english");
                    $pdf = PDF::loadView('frontend/customer/acknowledge',$data);   
                }
                $fileName = $telesale->refrence_id."-".Carbon::parse($telesale->created_at)->format('Y-m-d-h-i-s').'.pdf';
                $filePath = config('constants.CLIENT_BOLT_ENEGRY_FTP_TRANSFER_ACKNOWLEDGE_FOLDER');
                // "testFolder/acknowledge_new/"
                $objStorageService = new StorageService;
                $path = $objStorageService->uploadFileToFTP($pdf->output(),$filePath, $fileName,"boltenegry_ftp");
                Log::info("Ack: acknowledgement is transferd on this path: ".$path);
            }
        } catch (\Exception $e) {
            \Log::error('Getting error while generating acknowledge: '.$e);
        }
    }
}
