<?php

namespace App\Http\Controllers\Admin;

use App\models\SelfVerificationAllowedZipcode;
use App\models\Zipcodes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class SelfVerificationAllowedZipcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $zipcodes=SelfVerificationAllowedZipcode::all();
        return view('admin.self-verification-zipcode.edit',compact('zipcodes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* Start Validation rule */
        $request->validate([
            'zipcode' => 'required',
        ],['zipcode.required'=>'The zipcodes field is required.']);
        /* End Validation rule */
        try {
            $zipcodes = $request->zipcode;
            if(!empty($zipcodes)) { 
                SelfVerificationAllowedZipcode::where('id','>',0)->delete();                
                
                $zips = array();
                foreach ($zipcodes as $key => $value) {
                    $zipcode = Zipcodes::where('zipcode',$value)->first();
                    if(!empty($zipcode)) {
                        $zips[]['zipcode_id'] = $zipcode->id;
                    }
                }

                if(count($zips)>0)
                {
                    SelfVerificationAllowedZipcode::insert($zips);
                }
                return redirect()->back()->with('success','Self verification allowed zipcodes saved successfully.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error','Something went wrong!. Please try again.');
        }
    }

    
}
