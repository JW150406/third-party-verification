<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\models\CustomerType;
use Illuminate\Http\Request;
use DataTables;

class CustomerTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-customer-type', ['only' => ['index']]);
        $this->middleware(['permission:add-customer-type|edit-customer-type','isActiveClient'],   ['only' => ['store']]);
        $this->middleware('permission:delete-customer-type',   ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customerTypes = CustomerType::where('client_id',$request->client_id);

            return DataTables::of($customerTypes)                
                ->addColumn('action', function($customerType){
                    $editBtn = $deleteBtn = '';
                    if(auth()->user()->hasPermissionTo('edit-customer-type')) {
                        $editBtn = '<a 
                            href="javascript:void(0)"  
                            data-toggle="tooltip" 
                            data-placement="top" data-container="body" 
                            title="Edit Customer Type" 
                            data-original-title="Edit Customer Type" 
                            role="button" 
                            data-type="edit" 
                            data-id="' . $customerType->id . '" 
                            class="btn customer-type-create-modal">'
                                . getimage("images/edit.png") . '</a>';
                    } else {
                        $editBtn = '<a title="Edit Customer Type" class="btn cursor-none">' . getimage("images/edit-no.png") . '</a>';
                    }
                    if(auth()->user()->hasPermissionTo('delete-customer-type')) {
                        $deleteBtn = '<a 
                            href="javascript:void(0)"  
                            data-toggle="tooltip" 
                            data-placement="top" data-container="body" 
                            title="Delete Customer Type" 
                            data-original-title="Delete Customer Type" 
                            role="button" 
                            data-id="' . $customerType->id . '" 
                            data-url="' . route("customerType.destroy", $customerType->id) . '" 
                            class="btn delete-customer-type">'
                                . getimage("images/cancel.png") . '</a>';
                    } else {
                        $deleteBtn = '<a title="Delete Customer Type" class="btn cursor-none">' . getimage("images/delete-no.png") . '</a>';
                    }

                    return '<div class="btn-group">'.$editBtn.$deleteBtn.'<div>';
                })
                ->make(true);
        }
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
            'name' => 'required|max:255|unique:customer_types,name,'.$request->id.',id,client_id,'.$request->client_id
        ],['name.unique'=>'This name is taken']);
        /* End Validation rule */

        try {
            $commodity=CustomerType::updateOrCreate(['id'=>$request->id],[
                'client_id' => $request->client_id,
                'name' => $request->name
            ]);
            
            return response()->json([ 'status' => 'success',  'message'=>'Customer type successfully saved.']);
        } catch(Exception $e) {
            return response()->json([ 'status' => 'error',  'message'=> 'Something went wrong, please try again.']);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\models\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerType $customerType)
    {
        if(!empty($customerType)) {
            $customerType->delete();
            return response()->json([ 'status' => 'success',  'message'=>'Customer type successfully deleted.']);
        } else {
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.']);
        }
    }

    public function getCustomerType(Request $request){
       $customerTypes = CustomerType::where('client_id',$request->client_id)->get();
        return response()->json(['status'=>'success','data'=>$customerTypes]);
    }
}
