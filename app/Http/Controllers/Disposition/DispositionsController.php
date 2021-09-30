<?php

namespace App\Http\Controllers\Disposition;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\models\Dispositions;
use App\models\Telesales;
use App\models\Client;

use DataTables;

class DispositionsController extends Controller
{

    public function indexold(Request $request){
        $all_dispositions = (new Dispositions)->getList();
        return view('dispositions.index',compact('all_dispositions'))
        ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This method is used for listing of dispositions as per client id
     * @param $id, $request
     */
    public function index($id,Request $request)
    {
        $client_id = $id;
        $this->CheckClientUser($client_id);
        if ($request->ajax()) {
            $all_dispositions = Dispositions::select('id', 'description', 'type', 'allow_cloning', 'disposition_group', 'status','email_alert')
                ->where([
                    ['client_id', '=' ,$client_id]
                ]);
            if ( $request->has('status') && $request->get('status') != "" ) {
                $all_dispositions->where('status', $request->get('status'));
            }
            return DataTables::of($all_dispositions)
                ->editColumn('disposition_group', function($disposition){
                    $group='';
                    if($disposition->disposition_group == 'lead_detail' ) {
                        $group = 'Lead Detail';
                    } else if($disposition->disposition_group == 'sales_agent' ) {
                        $group = 'Sales Agent';
                    } else if($disposition->disposition_group == 'customer' ){
                        $group = 'Customer';    
                    } else {
                        $group = ucfirst($disposition->disposition_group);   
                    }
                    return $group;
                })
                ->addColumn('email_alert',function($desposition){
                    if($desposition->email_alert == 1)
                        return 'Yes';
                    else
                    return '';
                })
                ->addColumn('action', function($user){
                    $editBtn =  $statusBtn = $deleteBtn = '';
                    if (\auth()->user()->hasPermissionTo('edit-dispositions')) {
                        $editBtn = '<a 
                        class="disposition-modal btn"  
                        href="javascript:void(0)" 
                        data-toggle="tooltip" 
                        data-placement="top" data-container="body"
                        data-type="edit" 
                        data-original-title="Edit Disposition" 
                        data-id="' . $user->id . '" 
                        >' . getimage("images/edit.png") . '</a>';
                    }
                    if (\auth()->user()->hasPermissionTo('delete-dispositions')) {
                        if ($user->status == config('constants.STATUS_ACTIVE')) {
                            $statusBtn = '<a 
                                            class="deactive-disposition btn"  
                                            href="javascript:void(0)" 
                                            data-toggle="tooltip" 
                                            data-placement="top" data-container="body"  
                                            data-original-title="Deactivate Disposition" 
                                            id="delete-desposition-' . $user->id . '"
                                            data-did="' . $user->id . '" 
                                            >' . getimage("images/activate_new.png") . '</a>';
                        } else {
                            $statusBtn = '<a 
                                            class="active-disposition btn"  
                                            href="javascript:void(0)" 
                                            data-toggle="tooltip" 
                                            data-placement="top" data-container="body"  
                                            data-original-title="Activate Disposition" 
                                            id="delete-desposition-' . $user->id . '"
                                            data-did="' . $user->id . '" 
                                            >' . getimage("images/deactivate_new.png") . '</a>';
                        }
                    }
                    if (Auth::user()->hasRole(config()->get('constants.ROLE_GLOBAL_ADMIN'))) {
                        $class = 'delete-desposition-data';
                        $attributes = [
                            "data-original-title" => "Delete Disposition",
                            "data-did" => $user->id,
                            "id" => "delete-desposition-data-{$user->id}"
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    }
                    if(empty($editBtn) && empty($statusBtn) && empty($deleteBtn)) {
                        return '';
                    } else {
                        return '<div class="btn-group">'.$editBtn.$statusBtn.$deleteBtn.'<div>';
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * For check auth user is client user or not
     * @param $client_id
     */
    function CheckClientUser($client_id){
        if(Auth::user()->access_level == 'client' ){
            if(Auth::user()->client_id != $client_id ){
                abort(403);
            }
        }
    }

    /**
     * For return view page of create dispositions
     */
    public function create(){
        return view('dispositions.create');
    }

    /**
     * For store data of dispositions in db as per request data
     */
    public function save(Request $request){

        $validator = \Validator::make($request->all(), [
            'description' => "required",
            'type' => "required",           
        ]);
        
        if ($validator->fails())
        {
            return response()->json([ 'status' => 'error',  'errors'=>$validator->errors()->all()]);
        }
        $dispositions = Dispositions::where([['type','=',$request->type],['disposition_group','=',$request->disposition_group],['client_id',$client_id]])->get();
        if($dispositions->count()>0)
        {
            return response()->json([ 'status' => 'error',  'message'=> "You cannot create Dispositions with same category name and same disposition group name "]);
        }
        try{
                $allow_cloning = 'false';
                if(isset($request->allow_cloning)){
                $allow_cloning = 'true';
                }
                
                $insert_data = array(
                    'description' => $request->description,
                    'type' => $request->type,
                    'disposition_group' => $request->disposition_group,
                    'created_by' => Auth::user()->id,
                    'allow_cloning' => $allow_cloning,
                    'created_at' => date('m-d-Y H:i:s'),
                );
                 

                (new Dispositions)->saveDisposition($insert_data);
                return response()->json([ 'status' => 'success',  'message'=>'Disposition created successfully. ','url' => route('admin.dispositionslist')]);
        } catch(Exception $e) {
            // echo 'Message: ' .$e->getMessage();
             return response()->json([ 'status' => 'error',  'errors'=> ["Something went wrong!. Please try again."]]);
           }  

        // return redirect()->route('admin.dispositionslist')
        // ->with('success','Disposition created successfully');

    }

    /**
     * This method is used for create and update dispositions details as per client id
     * @param $client_id, $request
     */
    public function createOrUpdate($client_id,Request $request)
    {
        $id = $request->id;
        
        $request->validate([
            'description' => "required",
            'type' => "required|in:decline,customerhangup,verified,esignature_cancel,do_not_enroll",
        ],[
            'description.required' => 'This field is required',
            'type.required' => 'This field is required'
        ]);
        if($request->type == 'esignature_cancel' || $request->type == 'do_not_enroll')
        {
            $disposition = Dispositions::where('client_id',$client_id)->where('type','=',$request->type);
            if ($id > 0) {
                $disposition->where('id','!=',$id);
            }
            if($disposition->count() > 0)
            {
                return response()->json([ 'status' => 'error',  'message'=> "You cannot create dispositions with this category as this is already created."]);
            }
        }
        $email_alert = 0;
        if(isset($request->email_alert_disposition))
        {
            $email_alert = 1;
        }
        $disposition = Dispositions::where([['type','=',$request->type],['disposition_group','=',$request->disposition_group],['description','=',$request->description],['client_id',$client_id]]);

        if ($id > 0) {
            $disposition->where('id','!=',$id);
        }
        if($disposition->exists())
        {
            return response()->json([ 'status' => 'error',  'message'=> "You cannot create dispositions with same category name and same disposition group name"]);
        }

        try{
            $allow_cloning = 'false';
            if(isset($request->allow_cloning)){
                $allow_cloning = 'true';
            }
            
            $insert_data = array(
                'description' => $request->description,
                'client_id' => $client_id,
                'type' => $request->type,
                'disposition_group' => $request->disposition_group,
                'created_by' => Auth::user()->id,
                'allow_cloning' => $allow_cloning,
                'email_alert' => $email_alert,
                'created_at' => date('m-d-Y H:i:s'),
            );
            if (empty($id)) {
                (new Dispositions)->saveDisposition($insert_data);
                return response()->json([ 'status' => 'success',  'message'=>'Disposition successfully created.']);
            } else {
                (new Dispositions)->updateDisposition($id,$insert_data);
                return response()->json([ 'status' => 'success',  'message'=>'Disposition successfully updated.']);
            }
        } catch(\Exception $e) {
            return response()->json([ 'status' => 'error',  'errors'=> "Something went wrong, please try again."]);
        }

    }

    /**
     * For return view page of edit or update particular disposition
     */
    public function edit(Request $request){
         $desposition = (new Dispositions)->getDisposition($request->disposition_id);
         return view('dispositions.edit',compact('desposition'));
    }

    /**
     * For get all the details of disposition as per client
     * @param $client_id, $request
     */
    public function getDispositions($client_id,Request $request)
    {
        $desposition = (new Dispositions)->getDisposition($client_id,$request->disposition_id);
        return response()->json([ 'status' => 'success',  'data'=>$desposition]);
    }

    /**
     * For update particular disposition details
     */
    public function update(Request $request){
        
        $this->validate($request,[
            'description' => "required",
            'type' => "required",
        ]);
        $dispositions = Dispositions::where([['type','=',$request->type],['disposition_group','=',$request->disposition_group],['client_id',$client_id]])->get();
        if($dispositions->count() > 0)
        {
            return response()->json([ 'status' => 'error',  'message'=> "You cannot edit Dispositions with same category name and same disposition group name"]);
        }
        $allow_cloning = 'false';
        if(isset($request->allow_cloning)){
          $allow_cloning = 'true';
        }
        $update_data = array(
            'type' => $request->type,
            'description' => $request->description,
            'allow_cloning' => $allow_cloning,
            'updated_at' => date('m-d-Y H:i:s'),
        );
        $desposition = (new Dispositions)->updateDisposition($request->disposition_id,$update_data );
        return redirect()->back()
        ->with('success','Disposition successfully updated.');
   }

    /**
     * This method is used for delete particular dispostion
     */
    public function delete(Request $request){

        $data = $request->all();
        if(isset($request->disposition_id) && !empty($request->disposition_id)){
            $check_disposition_adde_to_lead =   (new Telesales)->validate_disposition($request->disposition_id);
            if($check_disposition_adde_to_lead > 0 ){
                return response()->json([ 'status' => 'error',  'message'=>'This disposition cannot be deleted as it is assigned to a lead.']);
            }else{
                (new Dispositions)->deleteDisposition($request->disposition_id);
                return response()->json([ 'status' => 'success',  'message'=>'Disposition successfully deleted.']);
            }

        }else{
                return redirect()->back()
            ->withErrors('Invalid request');
        }

    }

    /**
     * This method is used for make active or inactive disposition
     */
    public function activeInactiveDisposition(Request $request){

        $data = $request->all();
        if($request->status == 'delete')
        {
            $delete =  Dispositions::where('id',$request->disposition_id)->delete();
            if($delete == 1)
            {
                $message = 'Disposition successfully deleted.';
                return response()->json([ 'status' => 'success',  'message'=> $message]);
            }    
            else
                return response()->json([ 'status' => 'error',  'message'=> "Something went wrong, please try again later."]);
        }
        else{
            if(isset($request->disposition_id) && !empty($request->disposition_id)){
                    (new Dispositions)->activeAndInactiveDisposition($request->disposition_id, $request->status);
                    if($request->status == 'inactive')
                        $message = 'Disposition successfully deactivated.';
                    else
                        $message = 'Disposition successfully activated.';
                    return response()->json([ 'status' => 'success',  'message'=>$message]);
            }else{
                return redirect()->back()
                    ->withErrors('Invalid request');
            }
        }

    }

    /**
     * For bulk upload dispositions
     * @param $clientId
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View|mixed
     */
    public function bulkupload($clientId, Request $request){
        $this->CheckClientUser($clientId);
        $client = Client::active()->findOrFail($clientId);
        return view('dispositions.bulkupload',compact('client'));
    }

    /**
     * For import disposition
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request) {
        $validator = \Validator::make(
            [
                'upload_file' => $request->hasFile('upload_file')? strtolower($request->file('upload_file')->getClientOriginalExtension()) : null,
            ],
            [
                'upload_file'      => 'required|in:csv,xlsx,xls',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
        }

        try {
            $clientId=$request->client_id;
            $path = $request->file('upload_file')->getRealPath();
            $data = Excel::load($path, function($reader) {
                $columns= [
                    "category",
                    "description",
                    "disposition_group",
                ];
                $reader->select($columns);
            })
                ->ignoreEmpty()
                ->get()
                ->toArray();
            $errors = $validData = array();
            if (empty($data)) {
                $errors[1][]='This file does not fit the correct format.';
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }
            $isSignatureCategory = $isNotEnrollCategory = false;
            foreach ($data as $key => $disposition) {
                
                $dataValidator = \Validator::make($disposition,
                    [
                        'category'          => 'required|in:Declined,Call Disconnected,Verified,E-signature Cancel,Do Not Enroll',
                        'description'          => 'required|max:255',
                        'disposition_group'          => 'required|in:Customer,Sales Agent,Lead Detail,Other',
                    ]
                );
                if ($dataValidator->fails()) {
                    foreach ($dataValidator->messages()->all() as  $value) {
                        $errors[$key+1][]=$value;
                    }
                } else {

                    $disposition['category'] = $this->convertDispCategoryValueToKey($disposition['category']);

                    if($disposition['category'] == 'esignature_cancel') {
                        $isExist = Dispositions::where('client_id',$clientId)->where('type',$disposition['category'])->exists();
                        if($isExist || $isSignatureCategory) {
                            $errors[$key+1][] = "This Dispositions already exists with e-signature cancel category name.";
                        }
                        $isSignatureCategory = true;
                    } else if($disposition['category'] == 'do_not_enroll') {
                        $isExist = Dispositions::where('client_id',$clientId)->where('type',$disposition['category'])->exists();
                        if($isExist || $isNotEnrollCategory) {
                            $errors[$key+1][] = "This Dispositions already exists with do not enroll category name.";
                        }
                        $isNotEnrollCategory = true;
                    }

                    $disposition['disposition_group'] = $this->convertDispGroupValueToKey($disposition['disposition_group']);

                    $disp = Dispositions::where('type',$disposition['category'])->where('description',$disposition['description'])->where('disposition_group',$disposition['disposition_group'])->where('client_id',$clientId)->first();
                    if (!empty($disp)) {
                        $errors[$key+1][]="This Dispositions already exists with same category name, description and same disposition group name";
                    } else {
                        $validData[] = $disposition;
                    }
                }
            }

            if (!empty($errors)) {
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }
            if (!empty($validData)) {
                //Update or create utility records
                \DB::transaction(function() use ($validData, $clientId){
                    foreach ($validData as $key => $data) {
                        $data['type'] = $data['category'];
                        $data['client_id'] = $clientId;
                        unset( $data['category'] );
                        $query = $data;
                        $data['created_by'] = Auth::id();
                        $data['status'] = "active";
                        Dispositions::updateOrCreate($query,$data);                        
                    }
                });
                $url = \URL::route('client.show',['id' => $clientId]). '#Dispositions';
                session()->put('success', 'Dispositions successfully imported.');
                return response()->json(['status' => 'success',  'message' =>'Dispositions successfully imported.', 'url' =>$url], 200);
            } else {
                return response()->json(['status' => 'error',  'message' =>'Data not found to import.'], 500);
            }
        } catch(\Exception $e) {
            \Log::error('Error while dispositions bulk upload:-'.$e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
    }

    /**
     * For convert value to key disposition category
     * @param $disposition
     * @return mixed
     */
    public function convertDispCategoryValueToKey($category) {

        switch ($category) {
            case 'Call Disconnected':
                $key = 'customerhangup';
                break;
            case 'Verified':
                $key = 'verified';
                break;
            case 'Declined':
                $key = 'decline';
                break;
            case 'E-signature Cancel':
                $key = 'esignature_cancel';
                break;
            case 'Do Not Enroll':
                $key = 'do_not_enroll';
                break;
            default:
                $key = 'customerhangup';
                break;
        }
        return $key;
    }

    /**
     * For convert value to key disposition group
     * @param $disposition
     * @return mixed
     */
    public function convertDispGroupValueToKey($disposition) {

        switch ($disposition) {
            case 'Customer':
                $key = 'customer';
                break;
            case 'Sales Agent':
                $key = 'sales_agent';
                break;
            case 'Lead Detail':
                $key = 'lead_detail';
                break;
            case 'Other':
                $key = 'other';
                break;
            default:
                $key = 'other';
                break;
        }
        return $key;
    }

    /**
     * For download sample file
     * @param Request $request
     * @return mixed
     */
    public function downloadSample(Request $request) {
        $data =[[
            "Category" => "Declined",
            "Description" => "This is sample",
            "Disposition Group" => "Customer",
        ],[
            "Category" => "Call Disconnected",
            "Description" => "This is sample",
            "Disposition Group" => "Sales Agent",

        ]];

        return Excel::create('disposition_sample', function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download("csv");
    }

    /**
     * For export dispositions
     * @param $clientId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function export($clientId, Request $request) {
        $this->CheckClientUser($clientId);
        try {
            $client = Client::active()->findOrFail($clientId);

            $clientName = str_replace(" ", "-", array_get($client, 'name'));

            if ($clientName != "") {
                $fileName = $clientName . "-" . "dispotions";
            } else {
                $fileName = "dispotions";
            }

            $fileName .= "-" . date('d_M_Y_H_i_A');

            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=". $fileName .".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );

            $dispotions = Dispositions::where('client_id', $clientId)->where('status','active')->get();

            $columns = array('Category', 'Description', 'Disposition Group');
            $callback = function() use ($dispotions, $columns) {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, $columns);

                        foreach($dispotions as $dispotion) {
                            fputcsv($file, array($dispotion->category, $dispotion->description, $dispotion->group));
                        }
                        fclose($file);
                    };

            return \Response::stream($callback, 200, $headers);
        } catch(\Exception $e) {
            \Log::error('Error while dispositions export:-'.$e);
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

}
