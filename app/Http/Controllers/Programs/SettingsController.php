<?php

namespace App\Http\Controllers\Programs;

use App\models\Settings;
use App\models\Programs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\models\SettingTPVnowRestrictedTimeZone;
use Exception;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-client-settings', ['only' => ['index']]);
        $this->middleware(['permission:edit-client-settings','isActiveClient'], ['only' => ['store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $customFields = Settings::where('client_id',$request->client_id)->first();
            $restrictionFields = SettingTPVnowRestrictedTimeZone::where('client_id',$request->client_id)->get();
            $data = [$customFields, $restrictionFields];
            // dd($data);
            return response()->json([ 'status' => 'success',  'data' => $data]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.']); 
        }
        
    }

    /**
     * Show in the form while creating program.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $field = Settings::where('client_id',$request->client_id)->first();
        $view = null;
        if ($field) {
            $view = view('client.utility_new.program.custom_field', compact('field'))->render();
        }
        return response()->json(['status' => 'success', 'view' => $view]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            
            $data = $request->except('_token');

            $data['tpv_now_max_no_of_call_attempt'] = $request->input('tpv_now_max_no_of_call_attempt', null);
            $callDelay = $request->input('textBoxVal');
            if (isset($callDelay)) {
                $delay = implode(",", $callDelay);
            } else {
                $delay = null;
            }
            $data['tpv_now_call_delay'] = $delay;

            // for self TPV welcome call
            $selfTpvDelay = $request->input('self_tpv_call_delay',null);
            if (!empty($selfTpvDelay)) {
                $selfTpvDelay = implode(",", $selfTpvDelay);
            }
            $data['self_tpv_call_delay'] = $selfTpvDelay;

            // for self TPV resetrict states tele
            $selfTpvStates = $request->input('restrict_states_self_tpv_tele',null);
            if (!empty($selfTpvStates)) {
                $selfTpvStates = implode(",", $selfTpvStates);
            }
            $data['restrict_states_self_tpv_tele'] = $selfTpvStates;

            // for self TPV resetrict states d2d
            $selfTpvStates = $request->input('restrict_states_self_tpv_d2d',null);
            if (!empty($selfTpvStates)) {
                $selfTpvStates = implode(",", $selfTpvStates);
            }
            $data['restrict_states_self_tpv_d2d'] = $selfTpvStates;


            //for outbound disconnect
            
            $outboundDelay = $request->input('outbound_disconnect_schedule_call_delay',null);
            if (!empty($outboundDelay)) {
                $outboundDelay = implode(",", $outboundDelay);
            }
            $data['outbound_disconnect_schedule_call_delay'] = $outboundDelay;
            
            // For create, update and delete operation of timezone restriction data
            $this->checkTimeZoneRestrictions($request);
            
            Settings::updateOrCreate(['client_id' => $request->client_id], $data);
            
            // for reset custom field in program table after disable custom field
            $this->resetCustomFieldsProgram($data);

            return response()->json([ 'status' => 'success',  'message'=>'Settings successfully updated.']);
        } catch (\Exception $e) {
            Log::error('Error while settings update:- '.$e);
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.']); 
        }
    }

    /**
     * reset custom field in program table when custom field disable from settings
     * @param $input
     */
    public function resetCustomFieldsProgram($input)
    {
        $colums = [];
        if (!$input['is_enable_field_1']) {
            $colums['custom_field_1'] = null;
        }
        if (!$input['is_enable_field_2']) {
            $colums['custom_field_2'] = null;
        }
        if (!$input['is_enable_field_3']) {
            $colums['custom_field_3'] = null;
        }
        if (!$input['is_enable_field_4']) {
            $colums['custom_field_4'] = null;
        }
        if (!$input['is_enable_field_5']) {
            $colums['custom_field_5'] = null;
        }

        if (!empty($colums)) {
            Programs::where('client_id',$input['client_id'])->update($colums);
        }
    }

    /**
     * This function is used for create, update and delete timezone restriction data
     * 
     * @param $request
     */
    public function checkTimeZoneRestrictions($request)
    {
        
        try {
            
            // dd($request->all());
            Log::info("In checkTimeZoneRestrictions method");
            $ids = $request->id;
            $deleteIds = $request->deleteId;
            $deleteIds = explode(',',$deleteIds[0]);
            $states = $request->state;
            $start_times = $request->start_time;
            $end_times = $request->end_time;
            $timezones = $request->timezone;
            
            // $check = SettingTPVnowRestrictedTimeZone::where('client_id', '=', $request->client_id)->pluck('id');

            // // For delete from database
            // $deleteIds = [];
            // foreach ($check as $k => $value) {
            //     if (!in_array($value, $ids)) {
            //         array_push($deleteIds, $value);
            //     } 
            // }
            if (isset($deleteIds)) {
                foreach ($deleteIds as $deleteId) {
                    $delete = SettingTPVnowRestrictedTimeZone::where('id', $deleteId)->delete();
                    Log::info("Timezone restriction deleted successfully of id : ".$deleteId);
                }
            }
            
            // For create and update in database 
            if(isset($states)){
                foreach ($states as $key => $value) {
                    $id = 0;
                    if(isset($ids[$key])){
                        $id = $ids[$key];
                    }
                    $array = [
                        'client_id' => $request->client_id,
                        'state' => $states[$key],
                        'start_time' => $start_times[$key],
                        'end_time' => $end_times[$key],
                        'timezone' => $timezones[$key]
                    ];
                    // echo $id; exit;

                    if ($id > 0) {
                        $setting = SettingTPVnowRestrictedTimeZone::find($id);
                        if (!empty($setting)) {
                            SettingTPVnowRestrictedTimeZone::where('id', $id)->update($array);
                        } else {
                            SettingTPVnowRestrictedTimeZone::create($array);
                        }
                    } else {
                        SettingTPVnowRestrictedTimeZone::create($array);
                    }

                    // $save = SettingTPVnowRestrictedTimeZone::updateOrCreate(['id'=> $id],$array);
                    Log::info("Timezone restriction created or updated successfully ");
                }
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    

    

    
    
}
