<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use FarhanWazir\GoogleMaps\GMaps;
use App\models\Zipcodes;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\AgentPanel\TPVAgent\TwilioController;
use App\models\ClientWorkflow;
use App\models\TwilioStatisticsWorkers;
use App\models\TwilioStatisticsWorkersActivityduration;
use App\models\TwilioStatisticsWorkflow;
use App\models\TwilioStatisticsTaskqueue;
use App\models\TwilioStatisticsWorkspace;
use App\models\TwilioWorkspaceActivityStatistics;
use App\models\TwilioStatisticsSpecificWorker;
use App\models\TwilioStatisticsSpecificWorkerActivity;
use App\models\TwilioStatisticsCallLogs;
use App\models\TwilioStatisticsUsageRecords;
use App\models\UserTwilioId;
use Auth;
use Twilio\Exceptions\TwilioException;
use App\Services\TwilioService;
use Twilio\Rest\Client as TwilioClient;
use Twilio\Rest\Client;
use Log;


class DemoMapController extends Controller
{
    /**
     * This method is used to get twilio statistics
     * For save twilio call logs  this is for testing purposes for fetch twilio statistics there is a command donot change here for saving any twilio api records 
     */
    public function twilioStatistics()
    {
        $sDate = date_create(Carbon::yesterday(), timezone_open(getClientSpecificTimeZone()));
        $eDate = $sDate;
        $toronto_timezone = date_format($sDate, 'P');
        $sDate = $sDate->format('Y-m-d');
        $eDate = $eDate->modify('+1 day')->format('Y-m-d');
        $startDate = date($sDate.'\T00:00:00'.$toronto_timezone);
        $endDate = date($eDate.'\T00:00:00'.$toronto_timezone);
        try{
            Log::info("Store Twilio previous record APi Command started");
        
                $sId  = config('services.twilio')['accountSid'];
                $token  = config('services.twilio')['authToken'];
                $twilioClient  = new Client($sId, $token);
                $workspace = DB::table('client_twilio_workspace')->pluck('workspace_id')->unique('workspace_id');
                $workspaceId = $workspace[0];
            
            //Call function that stores twilio statistics api record into database
                (new TwilioController)->fetchTwilioStatisticsApiRecord($twilioClient,$startDate,$endDate,$workspaceId);
            
            Log::info("Store Twilio previous record APi Command end");
        }
        catch(TwilioException $e)
        {
            Log::error($e->getMessage());
        }
    }
    
    // public function map()
    // {
    //     $lat_lng = DB::table('zip_codes')->offset(9155)->take(1000)->get(['zipcode']);
    //     // dd($lat_lng); // 367
    //     foreach($lat_lng as $key => $value)
    //     {
    //         //  echo "<pre>";
    //         //  print_r($value);
    //          $address = $value->zipcode;
    //         // // google map geocode api url
    //          $url = "https://maps.googleapis.com/maps/api/geocode/json?address={{$address}}&key=AIzaSyCwDYH6F8nAVlguYbLk87ORm1zkfALTZ8c";
            
    //         // // get the json response
    //         $resp_json = file_get_contents($url);
            
    //         // // decode the json
    //          $resp = json_decode($resp_json, true);
        
    //         // // response status will be 'OK', if able to geocode given address 
    //         if($resp['status']=='OK'){
        
    //             // get the important data
    //             $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
    //             $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
    //             $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";

    //             $add_lat_lng =  Zipcodes::where('zipcode',$address)->first();
    //             $add_lat_lng->lat = $lati;
    //             $add_lat_lng->lng = $longi;
    //             $add_lat_lng->save();
                
    //        }
           
    //    }
    // }

    public function map(Request $request)
    {

        //     $lat_lng = DB::table('zip_codes')->offset($request->get('offset'))->take(20)->get(['zipcode', 'id'])->toArray();
        //     $zipcodes = [];
        //     $urlArray = [];
        //     $i = 0;
        //     foreach ($lat_lng as $k => $v) {
        //         $address = $v->zipcode;
        //         $zipcodes[$i] = $address;
        //         $urlArray[$i++] = "https://maps.googleapis.com/maps/api/geocode/json?address={{$address}}&key=AIzaSyCwDYH6F8nAVlguYbLk87ORm1zkfALTZ8c";

        //     }

        //     $this->fetchAndProcessUrls($urlArray, $zipcodes, function ($requestData, $zipcode) {

        //     // e.g.
        //         $jsonData = json_decode($requestData, 1);
        //         if ($jsonData['status'] == 'OK') {

        //         // get the important data
        //             $lati = isset($jsonData['results'][0]['geometry']['location']['lat']) ? $jsonData['results'][0]['geometry']['location']['lat'] : "";
        //             $longi = isset($jsonData['results'][0]['geometry']['location']['lng']) ? $jsonData['results'][0]['geometry']['location']['lng'] : "";
        //             $formatted_address = isset($jsonData['results'][0]['formatted_address']) ? $jsonData['results'][0]['formatted_address'] : "";

        //             $add_lat_lng = Zipcodes::where('zipcode', $zipcode)->first();
        //             $add_lat_lng->lat = $lati;
        //             $add_lat_lng->lng = $longi;
        //             $add_lat_lng->save();
        //         }

        //     });

        //     return redirect()->route('google.map', ['offset' => $v->id]);
    }

    // function fetchAndProcessUrls(array $urls, array $zipcodes, callable $f)
    // {

    //     $multi = curl_multi_init();
    //     $reqs = [];

    //     foreach ($urls as $url) {
    //         $req = curl_init();
    //         curl_setopt($req, CURLOPT_URL, $url);
    //         curl_setopt($req, CURLOPT_HEADER, 0);
    //         curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
    //         curl_multi_add_handle($multi, $req);
    //         array_push($reqs, $req);
    //     }

    //     $active = null;

    //     // Execute the handles
    //     do {
    //         $mrc = curl_multi_exec($multi, $active);
    //     } while ($mrc == CURLM_CALL_MULTI_PERFORM);

    //     while ($active && $mrc == CURLM_OK) {
    //         if (curl_multi_select($multi) != -1) {
    //             do {
    //                 $mrc = curl_multi_exec($multi, $active);
    //             } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    //         }
    //     }

    //     // Close the handles
    //     foreach ($reqs as $i => $req) {
    //         $curl = curl_multi_getcontent($req);
    //         $f($curl, $zipcodes[$i]);
    //         curl_multi_remove_handle($multi, $req);
    //     }
    //     curl_multi_close($multi);
    // }
}
