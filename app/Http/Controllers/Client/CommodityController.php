<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\models\Commodity;
use App\models\CommodityUnit;
use App\models\Utilities;
use Illuminate\Http\Request;
use DataTables;

class CommodityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $commodities = Commodity::select('commodities.*')->where('client_id',$request->client_id)->with('units');

            return DataTables::of($commodities)
                ->addColumn('units', function($commodity){
                    $units='';
                    if(!$commodity->units->isEmpty()) {
                        $units = $commodity->units->pluck('unit')->implode(', ');
                        if (strlen($units) > 160) {
                            $stringCut = substr($units, 0, 160);
                            $units = $stringCut.'...';
                        }

                    }
                    return $units;
                })
                ->addColumn('action', function($commodity){
                    $editBtn = $deleteBtn = '';
                    if (auth()->user()->hasPermissionTo('edit-commodity')) {
                        $editBtn = '<a 
                        href="javascript:void(0)"  
                        data-toggle="tooltip" 
                        data-placement="top" data-container="body" 
                        title="Edit Commodity" 
                        data-original-title="Edit Commodity" 
                        role="button" 
                        data-type="edit"
                        data-id="' . $commodity->id . '" 
                        class="btn commodity-create-modal">'
                            . getimage("images/edit.png") . '</a>';
                    }
                    if (auth()->user()->hasPermissionTo('delete-commodity')) {
                        $deleteBtn = '<a 
                        href="javascript:void(0)"  
                        data-toggle="tooltip" 
                        data-placement="top" data-container="body" 
                        title="Delete Commodity" 
                        data-original-title="Delete Commodity" 
                        role="button" 
                        data-id="' . $commodity->id . '" 
                        data-url="' . route("commodity.destroy", $commodity->id) . '" 
                        class="btn delete-commodity">'
                            . getimage("images/cancel.png") . '</a>';
                    }

                    if(empty($editBtn) && empty($deleteBtn)) {
                        return '';
                    } else {

                        return '<div class="btn-group">'.$editBtn.$deleteBtn.'<div>';
                    }
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
            'name' => 'required|max:255|unique:commodities,name,'.$request->id.',id,client_id,'.$request->client_id,
            'units' => 'required'
        ],['name.unique'=>'This name is taken','units.required'=>'Please add at least one unit']);
        /* End Validation rule */
        
        try {
            $commodity=Commodity::updateOrCreate(['id'=>$request->id],[
                'client_id' => $request->client_id,
                'name' => $request->name
            ]);
            if(!empty($commodity)) {
                $this->storeUnit($commodity,$request->units);
            }
            if (empty($request->id)) {
                return response()->json([ 'status' => 'success',  'message'=>'Commodity successfully created.']);
            } else {
                return response()->json([ 'status' => 'success',  'message'=>'Commodity successfully updated.']);
            }
        } catch(\Exception $e) {
            return response()->json([ 'status' => 'error',  'message'=> $e->getMessage()]);
        }
        
    }

    /**
     * This function is used to store commodity unit
     */
    public function storeUnit($commodity,$units)
    {
        CommodityUnit::where('commodity_id',$commodity->id)->delete();
        $data=[];
        foreach ($units as $key => $unit) {
            $data[$key]['commodity_id'] =$commodity->id;
            $data[$key]['unit'] =$unit;
        }

        if(!empty($data)) {
            CommodityUnit::insert($data);
        }
    }

    /**
     * This method is used to update commodity unit
     */
    public function edit(Request $request)
    {
        $units=CommodityUnit::where('commodity_id',$request->commodity_id)->get();
        if(!empty($units)) {
            return response()->json([ 'status' => 'success',  'data'=>$units ]);
        } else {
            return response()->json([ 'status' => 'error',  'message'=>'Units not found.']);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\models\Commodity  $commodity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Commodity $commodity)
    {
        $utilityCount= Utilities::where('commodity_id',$commodity->id)->count();
        if ($utilityCount > 0) {
            return response()->json([ 'status' => 'error',  'message'=>'You cannot delete this commodity.']);
        }
        $commodity->delete();
        return response()->json([ 'status' => 'success',  'message'=>'Commodity successfully deleted.']);
    }

    /**
     * This method is used to get commodities with units
     */
    public function getCommodities(Request $request)
    {
        $commodities=Commodity::where('client_id',$request->client_id)->with('units')->get();
        
        return response()->json(['status'=>'success','data'=>$commodities]); 
    }

    /**
     * This method is used to get commodity unit
     */
    public function getCommodityUnit(Request $request)
    {
        $units=CommodityUnit::where('commodity_id',$request->commodity_id)->get();
        
        return response()->json(['status'=>'success','data'=>$units]); 
    }
}
