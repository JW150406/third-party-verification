<?php

namespace App\Http\Controllers\API;

use App\models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;
use App\models\Salescenter;


class SalesCenterController extends Controller
{
    public function __construct()
    {
    }

    /**
     * For Get Sales Centers List By Client Id
     */
    public function index(Request $request)
    {
        try {
            $clientId = $request->client_id;

            $client = Client::find($clientId);
            if(!$client){
                $salesCenters = Salescenter::where('status','=','active')->orderBy('name')->select('id','name')->get();
            }else {
                $salesCenters = Salescenter::where('client_id', $clientId)->where('status', '=', 'active')->orderBy('name')->select('id', 'name')->get();
            }
            Log::info("Get sales center data for client: " .$clientId );
            return $this->success("success", "Sales centers retrived successfully",$salesCenters);
        } catch (\Exception $e) {
            Log::error("Error while Get sales center data for client: " . $e->getMessage());
            return $this->error("error", "Something went wrong, Please try again later !!", 500);
        }
    }
}
