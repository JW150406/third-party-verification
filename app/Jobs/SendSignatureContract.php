<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use App\Services\StorageService;
use App\Traits\SelfverifyDetailTrait;
use App\Traits\LeadTrait;
use App\models\Telesales;
use App\models\Leadmedia;
use PDF;
use Carbon\Carbon;

class SendSignatureContract implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, LeadTrait, SelfverifyDetailTrait;

    public $leadId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($leadId)
    {
        $this->leadId = $leadId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $leadId = $this->leadId;
        $data = [];
        $lead = Telesales::findOrFail($leadId);
        $signature = Leadmedia::getSignature($lead->id)->first();
        $data['signature'] = ($signature) ? Storage::disk('s3')->url($signature->url) : ''; 
        $data['customerName'] = $this->getCustomerName($lead); 
        $data['isVisible'] = true;
        $data['date'] = Carbon::parse($lead->created_at)->format(getDateFormat());
        $state = $this->getLeadState($lead->id, $lead->form_id); 
        if ($state == 'CA') {
            $language = $this->getLeadLanguage($lead); 
            if($language == config('constants.LANGUAGES.SPANISH')) {
                $pdf = PDF::loadView('frontend.customer.ca_spanish_t_and_c', $data);
            } else {
                $pdf = PDF::loadView('frontend.customer.ca_english_t_and_c', $data);
            }
        } else {
            $pdf = PDF::loadView('frontend.customer.in_t_and_c', $data);
        }
        $awsFolderPath = config('constants.aws_folder');
        $filePath = 'clients_data/' . $lead->client_id . '/' . config('constants.CLIENT_CONTRACTS_PATH');
        $fileName = $leadId . '.pdf';
        $objStorageService = new StorageService;
        $path = $objStorageService->uploadFileToStorage($pdf->output(), $awsFolderPath, $filePath, $fileName);
        if ($path !== false) {
            $lead->update(['contract_pdf'=>$path]);
            info("successfully save signature contract");
        }
    }
}
