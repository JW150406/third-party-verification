<?php

namespace App\Console\Commands;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Console\Command;
use App\models\Telesales;
use Storage;

class FileMoveClientWise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:move-client-wise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command move file client wise';

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
     * Move existing file to client wise
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $telesales = Telesales::all();
            $csvData = [];
            $awsFolder = config()->get('constants.aws_folder');
            foreach ($telesales as $key => $telesale) {
                $data = [];

                // For move file of tpv receipt
                if (!empty($telesale->tpv_receipt_pdf)) {
                    $oldPath = $awsFolder . $telesale->tpv_receipt_pdf;
                    $exists = Storage::disk('s3')->exists($oldPath);
                    if($exists) {
                        $newfilePath = 'clients_data/' . $telesale->client_id . '/'. config()->get('constants.CLIENT_TPV_RECEIPT_PATH').basename($oldPath);
                        $newPath = $awsFolder. $newfilePath;
                        if(!Storage::disk('s3')->exists($newPath)) {
                            $data['table'] = "telesales";
                            $data['column'] = "tpv_receipt_pdf";
                            $data['id'] = $telesale->id;
                            $data['old_path'] = $oldPath;
                            $data['new_path'] = $newPath;

                            $isMoved = Storage::disk('s3')->move($oldPath, $newPath);
                            if ($isMoved) {
                                $telesale->tpv_receipt_pdf = $newfilePath;
                                $telesale->save();
                                $data['is_moved'] = "Yes";
                            } else {
                                $data['is_moved'] = "No";
                            }

                            $csvData[] = $data;
                        }
                    }
                }

                // For move file of tpv recordings
                if (!empty($telesale->s3_recording_url)) {
                    $oldPath = $awsFolder . $telesale->s3_recording_url;
                    $exists = Storage::disk('s3')->exists($oldPath);
                    if($exists) {
                        $newfilePath = 'clients_data/' . $telesale->client_id . '/'. config()->get('constants.TPV_RECORDING_UPLOAD_PATH').basename($oldPath);
                        $newPath = $awsFolder. $newfilePath;
                        if(!Storage::disk('s3')->exists($newPath)) {
                            $data['table'] = "telesales";
                            $data['column'] = "s3_recording_url";
                            $data['id'] = $telesale->id;
                            $data['old_path'] = $oldPath;
                            $data['new_path'] = $newPath;
                            $isMoved = Storage::disk('s3')->move($oldPath, $newPath);
                            if ($isMoved) {
                                $telesale->s3_recording_url = $newfilePath;
                                $telesale->save();
                                $data['is_moved'] = "Yes";
                            } else {
                                $data['is_moved'] = "No";
                            }
                            $csvData[] = $data;
                        }
                    }
                }

                // For move file of contracts pdf
                if (!empty($telesale->contract_pdf)) {
                    $oldPath = $awsFolder . $telesale->contract_pdf;
                    $exists = Storage::disk('s3')->exists($oldPath);
                    if($exists) {
                        $newfilePath = 'clients_data/' . $telesale->client_id . '/'. config()->get('constants.CLIENT_CONTRACTS_PATH').basename($oldPath);
                        $newPath = $awsFolder. $newfilePath;
                        if(!Storage::disk('s3')->exists($newPath)) {
                            $data['table'] = "telesales";
                            $data['column'] = "contract_pdf";
                            $data['id'] = $telesale->id;
                            $data['old_path'] = $oldPath;
                            $data['new_path'] = $newPath;
                            $isMoved = Storage::disk('s3')->move($oldPath, $newPath);
                            if ($isMoved) {
                                $telesale->contract_pdf = $newfilePath;
                                $telesale->save();
                                $data['is_moved'] = "Yes";
                            } else {
                                $data['is_moved'] = "No";
                            }
                            $csvData[] = $data;
                        }
                    }
                }

                // For move file of consent recordings
                if(!$telesale->leadMedia->isEmpty()) {
                    $audio = $telesale->leadMedia->where('type','audio')->first();
                    if(!empty($audio) && !empty(array_get($audio,'url'))) {
                        $oldPath = $awsFolder . $audio->url;
                        $exists = Storage::disk('s3')->exists($oldPath);
                        if($exists) {
                            $newfilePath = 'clients_data/' . $telesale->client_id . '/'. config()->get('constants.CLIENT_CONSENT_RECORDING_UPLOAD_PATH').basename($oldPath);
                            $newPath = $awsFolder. $newfilePath;
                            if(!Storage::disk('s3')->exists($newPath)) {
                                $data['table'] = "leadmedia";
                                $data['column'] = "url";
                                $data['id'] = $audio->id;
                                $data['old_path'] = $oldPath;
                                $data['new_path'] = $newPath;
                                $isMoved = Storage::disk('s3')->move($oldPath, $newPath);
                                if ($isMoved) {
                                    $audio->url = $newfilePath;
                                    $audio->save();
                                    $data['is_moved'] = "Yes";
                                } else {
                                    $data['is_moved'] = "No";
                                }
                                $csvData[] = $data;
                            }
                        }
                    }
                }
            }
            if (!empty($csvData)) {
                self::export($csvData);
            } else {
                info("File not found for move client wise.");
            }
            $this->info('File successfully moved.');
            
        } catch (\Exception $e) {
            \Log::error('Error while moving file client wise: '.$e->getMessage());
            $this->error('Something went wrong!');
        }
    }

    /**
     * This method is used to Export & Store ftp data
     * @param $csvData
     */
    private function export($csvData) {
        \Log::info("Export ftp data: " . print_r($csvData, true));
        $fileName = 'ftp-setup-' . date('d_M_Y_H_i_A') . ".csv";
        $file = Excel::create($fileName, function($excel) use ($csvData) {
            $excel->sheet('sheet1', function($sheet) use ($csvData)
            {
                $sheet->fromArray($csvData);
            });
        })->string('csv');

        self::upload($file, $fileName);
    }
    
    /**
     * This method is used for Upload file to local directory
     * @param $file, $fileName
     */
    private function upload($file, $fileName) {
        try {
            $storage = Storage::disk('local');
            $path = "/ftp-setup/";
            if (!$storage->exists($path)) {
                $storage->makeDirectory($path);
            }

            $filePath = $path . $fileName;
            $storage->put($filePath, $file, 'public');
            \Log::info("FTP file uploaded. File path: /storage" . $filePath);
        } catch (\Exception $e) {
            \Log::error("Error while storing ftp file: " . $e->getMessage());
        }
    }
}
