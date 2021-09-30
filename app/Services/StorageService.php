<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Storage;
use Image;

class StorageService
{
    public function __construct() {
    }

    /**
     * This method is used to upload file to storage 
     * @param $file, $awsFolderPath, $filePath, $fileName
     */
    public static function uploadFileToStorage($file, $awsFolderPath, $filePath, $fileName)
    {
        try {
            if (is_string($file)) {
                $fileData = $file;
            } else {
                $fileData = file_get_contents($file);
            }
            $awsUrl = self::storeToAws($fileData, $awsFolderPath, $filePath, $fileName);
            
            if ($awsUrl === false) {
                return false;
            }
            
            return $awsUrl;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        	return false;
        }
	}

    /**
     * This method is used to store file in aws s3 disk
     * @param $file, $awsFolderPath, $filePath, $fileName
     */
    public static function storeToAws($file, $awsFolderPath, $filePath, $fileName)
    {
        try {
            $storage = Storage::disk('s3');
            if (!$storage->exists($filePath)) {
                $storage->makeDirectory($filePath);
            }

            $imagePath = $awsFolderPath . $filePath . $fileName;
            Storage::disk('s3')->put($imagePath, $file, 'public');
            return $filePath . $fileName;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * For upload any file to FTP 
     * @param $file, $filePath, $fileName, $diskFtp
     */
    public static function uploadFileToFTP($file, $filePath, $fileName,$diskFtp)
    {
        try {
            if (is_string($file)) {
                $fileData = $file;
            } else {
                $fileData = file_get_contents($file);
            }
            $ftpUrl = self::storeToFTP($fileData, $filePath, $fileName,$diskFtp);
            if ($ftpUrl === false) {
                return false;
            }
            
            return $ftpUrl;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * For store any file to FTP
     * @param $file, $filePath, $fileName, $diskFtp
     */
    public static function storeToFTP($file, $filePath, $fileName,$diskFtp)
    {
        try {
            $storage = Storage::disk($diskFtp);
            if (!$storage->exists($filePath)) {
                $storage->makeDirectory($filePath);
            }
            \Log::info("Ftp upload start ".$diskFtp);

            $imagePath = $filePath . $fileName;
            $status = $storage->put($imagePath, $file);
            \Log::info("Report upload status ".$status);
            return $imagePath;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * This method is used to get image content through curl request
     * @param $image
     */
    public static function getImageContent($image) {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $image);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'TPV360');
        $res = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $res;
    }
}
 	
