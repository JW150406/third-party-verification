<?php

namespace App\Http\Controllers\Salescenter;

use App\models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\User;
use DB;
use Hash;
use Mail;
use Session;
use App\models\Telesales;
use App\models\Salescenter;
use App\models\Salescenterslocations;
use App\models\LocationChannel;
use App\models\Client;
use App\models\ClientWorkspace;
use App\models\ClientWorkflow;
use App\models\ClientTwilioNumbers;
use App\models\Commodity;
use App\models\Programs;
use DataTables;
use App\Http\Requests\CsvImportRequest;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use Validator;
use App\models\Salesagentdetail;
use App\models\UserLocation;
use App\models\TextEmailStatistics;
use App\Services\StorageService;
use App\models\Brandcontacts;
use App\models\SalescentersBrands;
use App\models\SalescenterBrandPrograms;
use App\Http\Controllers\Utility\BrandContactsController;

class SalescenterController extends Controller
{
     private $client = array();
     private $salescenter = array();

    public function __construct(Request $request)
    {

        $this->middleware('auth');
         if($request->client_id){
            $this->client = (new Client )->getClientinfo($request->client_id);
         }

         if($request->salescenter_id){

            $this->salescenter = (new Salescenter)->getSalescenterinfo($request->salescenter_id);
            if( empty($this->salescenter) ){
               abort('404');
            }

        }

        $this->storageService = new StorageService;

    }

    /**
     * Show the client form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function create($client_id)
    {

        $client = Client::active()->findOrFail($client_id);
        $brands = Brandcontacts::where('client_id',$client_id)->get();
        return view('client.salescenter.create-form',compact('client_id','client','brands'));
    }

    // This function is currently not in use
    public function store($client_id, Request $request)
    {
        /* Start Validation rule */
          $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            // 'street' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            // 'country' => 'required',
            // 'zip' => 'required',
            'code' => 'required|unique:salescenters,code, client_id',
            'first_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',

        ]);

        if ($validator->fails())
        {
            return response()->json([ 'status' => 'error',  'errors'=>$validator->errors()->all()]);
        }
        /* End Validation rule */

      try{


       $Salescenter = new Salescenter();
       $Salescenter->name = $request->name;
       $Salescenter->street = $request->street;
       $Salescenter->city = $request->city;
       $Salescenter->state = $request->state;
       $Salescenter->country = $request->country;
       $Salescenter->zip = $request->zip;
       $Salescenter->client_id = $client_id;
       $Salescenter->code = $request->code;
       $Salescenter->created_by = Auth::user()->id;
       $Salescenter->save();

       $next_user_id = (new User)->nextAutoID();

       $newuser = $request->only('first_name','last_name', 'email');

        $newuser['parent_id'] = Auth::user()->id;
        $newuser['client_id'] = $client_id;
        $newuser['salescenter_id'] = $Salescenter->id;
        $newuser['access_level'] = 'salescenter';
        $newuser['status'] = 'inactive';
        $newuser['userid'] = strtolower($request->first_name[0]).$next_user_id;
        $newuser['verification_code'] = str_random(20);
        $newuser['password'] = Hash::make(rand());
        $next_user_id = (new User)->createuser($newuser);
        //$this->NewUserEmail($newuser);

        return response()->json([ 'status' => 'success',  'message'=>'Sales Center created successfully. Add location under salescenter.','url' => route('client.salescenter.addlocation',['client_id'=>$client_id,'salescenter_id' => $Salescenter->id])]);
    } catch(Exception $e) {
     // echo 'Message: ' .$e->getMessage();
      return response()->json([ 'status' => 'error',  'errors'=> ["something went wrong!. Please try again."]]);
    }

       /*client.salescenters*/
    //    return redirect()->route('client.salescenter.addlocation',['client_id'=>$client_id,'salescenter_id' => $Salescenter->id])
    //        ->with('success','Sales Center created successfully. Add location under salescenter.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $client_id
     * @return \Illuminate\Http\Response
     */
    public function storeNew($client_id, Request $request)
    {
        /* start Validation rule */
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            // 'street' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            // 'country' => 'required',
            // 'zip' => 'required|numeric|digits:5',
            'zip' => 'nullable|numeric|digits:5',
            'contact' => 'nullable|numeric|digits:10',
            'code' => 'required|unique:salescenters,code, client_id',
            'logo' => 'image',

        ],[
            'name.required' => ' The Sales center name field is required.',
            'code.required' => ' The Sales center code field is required.',
            // 'street.required' => ' The Address field is required.',
            // 'zip.required' => ' The zipcode field is required. ',
            'zip.numeric' => ' The zipcode must be a number.',
            'zip.digits' => ' The zipcode must be 5 digits.',
            'logo.image' => 'Sales center logo must be an image.',
        ]);

        if ($validator->fails())
        {
            return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
        }
        /* End Validation rule */

        try{
            
            $salescenter = new Salescenter();
            $salescenter->name = $request->name;
            if($request->street){
                $salescenter->street = $request->street;
            }else{
                $salescenter->street = "";    
            }

            if($request->city){
                $salescenter->city = $request->city;
            }else{
                $salescenter->city = "";    
            }

            if($request->state){
                $salescenter->state = $request->state;
            }else{
                $salescenter->state = "";    
            }

            if($request->country){
                $salescenter->country = $request->country;
            }else{
                $salescenter->country = "";    
            }

            if($request->zip){
                $salescenter->zip = $request->zip;
            }else{
                $salescenter->zip = "";    
            }
            
            
            $salescenter->client_id = $client_id;
            $salescenter->code = $request->code;
            $salescenter->contact = $request->contact;
            $salescenter->created_by = Auth::user()->id;
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $awsFolderPath = config()->get('constants.aws_folder');
                $filePath = config()->get('constants.SALESCENTER_LOGO_UPLOAD_PATH');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $this->storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $fileName);
                if ($path !== false) {
                    $salescenter->logo = $path;
                }
            }
            $salescenter->save();

            // default assigned all brands to sales center
            $this->assignBrands($client_id, $salescenter->id);
            session()->put('success', 'Sales Center successfully created.');
            return response()->json(['status' => 'success', 'message' => 'Sales Center successfully created.'], 200);
        } catch(\Exception $e) {
            Log::error("Error while creating salescenter: " . $e);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }


    }

    /**
     * for assigned all brands to sales center
     */
    public function assignBrands($clientId, $salecenterId)
    {
        try {
            $brands = Brandcontacts::where('client_id', $clientId)->get();
            $data = [];
            foreach ($brands as $key => $brand) {
                $data[$key]['salescenter_id'] = $salecenterId;
                $data[$key]['brand_id'] = $brand->id;
            }
            if (empty($data)) {
                Log::info("Brands not found.");
            } else {
                SalescentersBrands::insert($data);
                Log::info("Brands assigned to sales center.");
            }
        } catch (\Exception $e) {
            Log::error("Error while assign all brands: " . $e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $clientId, $salecenterId
     * @return \Illuminate\Http\Response
     */
    public function edit($clientId,$salecenterId)
    {
        $salecenter_id=$salecenterId;
        $client_id=$clientId;
        HelperCheckClientUser($clientId);
        $salescenter = Salescenter::active()->findOrFail($salecenterId);
        $client = Client::active()->findOrFail($clientId);
        $roles = (new Role)->getRolesForSalesCenterUser();
        $brands =(new BrandContactsController)->getBrands($client_id);
        $brands = $brands->original['data'];
        $salesCenterBrands = SalescentersBrands::where('salescenter_id',$salecenterId)->with('restrictProg')->get();
        $restrictedPrograms = $salesCenterBrands->groupBy('brand_id');
        $salesCenterBrands = $salesCenterBrands->toArray();
        $roles = (new Role)->getRolesForSalesCenterUser();
        $brandPrograms = Programs::where('client_id',$client_id)->with('utility')->get()->groupBy('utility.brand_id');
        return view('client.salescenter_new.show-edit',compact('salescenter','client','client_id','salecenter_id','roles','brands','salesCenterBrands','brandPrograms','restrictedPrograms'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $clientid, $salecenterid
     * @return \Illuminate\Http\Response
     */
    public function update($clientid,$salescenterid,Request $request)
    {
        /* Start Validation rule */
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            // 'street' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            // 'country' => 'required',
            'zip' => 'nullable|numeric|digits:5',
            'contact' => 'nullable|numeric|digits:10',
            'logo' => 'image',
        ],
        [
            // 'zip.required' => ' The zipcode field is required. ',
            'zip.numeric' => ' The zipcode must be a number.',
            'zip.digits' => ' The zipcode must be 5 digits.',
            'logo.image' => 'Sales center logo must be an image.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
        }
        /* End Validation rule */

        try {
            $salescenter = Salescenter::find($salescenterid);
            $salescenter->name = $request->name;
            $salescenter->street = $request->street;
            $salescenter->city = $request->city;
            $salescenter->state = $request->state;
            $salescenter->country = $request->country;
            $salescenter->zip = $request->zip;
            $salescenter->contact = $request->contact;
            if ($request->hasFile('salescenter_logo')) {
                $file = $request->file('salescenter_logo');
                $awsFolderPath = config()->get('constants.aws_folder');
                $filePath = config()->get('constants.SALESCENTER_LOGO_UPLOAD_PATH');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $this->storageService->uploadFileToStorage($file, $awsFolderPath, $filePath, $fileName);
                if ($path !== false) {
                    $salescenter->logo = $path;
                }
            }
            $salescenter->save();
            session()->put('success', 'Sales Center successfully updated.');
            \Log::info("Sales center update with id: " . $salescenterid);
            return response()->json(['status' => 'success', 'message' => 'Sales Center successfully updated.'], 200);
        } catch (\Exception $e) {
            session()->put('error', 'Something went wrong, please try again later.');
            \Log::error("Error while updating salescenter with id: " . $salescenterid . " : " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id, $client_id
     * @return \Illuminate\Http\Response
     */
    public function show($client_id, $id)
    {
        $salecenter_id=$id;
        HelperCheckClientUser($client_id);
        $salescenter = Salescenter::where('client_id', '=', $client_id)->where('id', '=', $id)->firstOrFail();
        $brands =(new BrandContactsController)->getBrands($client_id);
        $brands = $brands->original['data'];
        $client = (new Client )->getClientinfo($client_id) ;
        $salesCenterBrands = SalescentersBrands::where('salescenter_id',$id)->with('restrictProg')->get();
        $restrictedPrograms = $salesCenterBrands->groupBy('brand_id');
        $salesCenterBrands = $salesCenterBrands->toArray();
        $roles = (new Role)->getRolesForSalesCenterUser();
        $brandPrograms = Programs::where('client_id',$client_id)->where('status','active')->with('utility')->get()->groupBy('utility.brand_id');
        
        return view('client.salescenter_new.show-edit',compact('salescenter','client','client_id','salecenter_id','roles','brands','salesCenterBrands','brandPrograms','restrictedPrograms'));
    }

    /**
     * Display a listing of the resource.
     * @param $client_id
     * @return \Illuminate\Http\Response
     */
    public function index($client_id,Request $request)
    {
        if ($request->ajax()) {
            $salesCenters = Salescenter::with('client')->where('client_id', $client_id);
            if(Auth::user()->hasAccessLevels('salescenter')) {
                $salesCenters->where('id',Auth::user()->salescenter_id);
            }
            return DataTables::of($salesCenters)
                ->editColumn('logo', function($salesCenter){
                    if (array_get($salesCenter, 'logo') && Storage::disk('s3')->exists( config()->get('constants.aws_folder') . $salesCenter->logo)) {
                        $logo = '<a href="'.route("client.salescenter.show", array($salesCenter->client_id, $salesCenter->id)) .'" ><img src="'.Storage::disk('s3')->url($salesCenter->logo).'" class="list-logo" alt="'.$salesCenter->name.'"></a>';
                    } else {
                      $logo = '<a href="javascript:void(0);" ><img src="'.asset('images/PlaceholderLogo.png').'" class="list-logo" alt="'.$salesCenter->name.'"></a>';
                    }

                    return $logo;
                })
                ->editColumn('street', function($salesCenter){
                        $address = implode(', ',array_filter(array($salesCenter->street,$salesCenter->city,$salesCenter->state,$salesCenter->country,$salesCenter->zip)));
                        return $address;
                    })
                ->addColumn('action', function($salesCenter){

                    if (auth()->user()->hasPermissionTo('view-sales-center')) {
                        $viewBtn = '<a href="' . route("client.salescenter.show", array($salesCenter->client_id, $salesCenter->id)) . '" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="View Sales Center" class="btn">' . getimage("images/view.png") . '</a>';
                    }else{
                        $viewBtn = getDisabledBtn();
                    }
                    $editBtn ='';
                    if (auth()->user()->hasPermissionTo('edit-sales-center') && $salesCenter->isActive() && $salesCenter->isActiveClient()) {
                        $editBtn = '<a href="' . route("client.salescenters.edit", array($salesCenter->client_id, $salesCenter->id)) . '" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit Sales Center" class="btn">' . getimage("images/edit.png") . '</a>';
                    }else{
                        $editBtn = getDisabledBtn('edit');
                    }
                    $statusBtn ='';
                    if(Auth::user()->hasPermissionTo('deactivate-sales-center') && $salesCenter->isActiveClient()) {

                        if($salesCenter->status == 'active') {
                            $statusBtn = '<button
                                                class="deactivate-clientuser btn"   data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Deactivate Sales Center"
                                                data-id="'.$salesCenter->id.'"
                                                data-clientsalescenter="'.$salesCenter->name.'"  >'.getimage("images/activate_new.png").'</button>';
                        } else {
                            
                            $statusBtn = '<button
                                class="activate-clientuser btn"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Activate Sales Center"
                                    data-id="'.$salesCenter->id.'"
                                    data-clientsalescenter="'.$salesCenter->name.'"  >'.getimage("images/deactivate_new.png").'</button>';
                        }
                    }else{
                        $statusBtn = getDisabledBtn('status');
                    }
                    if (Auth::user()->hasPermissionTo('delete-sales-center')) {
                        $class = 'delete-sales-center';

                        $attributes = [
                            "data-original-title" => "Delete Sales Center",
                            "data-id" => $salesCenter->id,
                            "data-clientsalescenter" => $salesCenter->name
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    } else {
                        $deleteBtn = '';
                    }
                    return '<div class="btn-group">'.$viewBtn.$editBtn.$statusBtn.$deleteBtn.'<div>';
                })
                ->rawColumns(['logo','action'])
                ->make(true);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $client_id
     * @return \Illuminate\Http\Response
     */
    public function delete($client_id,Request $request)
    {
        try {
            $center = Salescenter::find($request->salescenterid);
            if (empty($center)) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong, please try again later.']);
            }

            DB::beginTransaction();
            if ($request->status == 'delete' && auth()->user()->hasPermissionTo('delete-sales-center')) {
                $userIds = User::where('salescenter_id',$center->id)->pluck('id')->toArray();

                // Will be deleted with related data
                $telesales = Telesales::whereIn('user_id',$userIds)->get();
                foreach ($telesales as $key => $telesale) {
                    $telesale->delete();
                }
                // Will be deleted with related data
                User::destroy($userIds);

                $center->delete();
                $message = "Sales Center successfully deleted.";
            } else {
                $center->status = $request->status;
                $center->save();
                if ($request->status == 'active') {
                    $message = 'Sales Center successfully activated.';
                } else {
                    $message = 'Sales Center successfully de-activated.';
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => $message]);            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json(['status' => 'error', 'message' => 'Something went wrong, please try again later.']);
        }
    }

    /**
     * This method is used to salescenter userlist
     */
    public function salescenterusers($client_id, $salescenter_id,Request $request)
    {
        HelperCheckClientUser($client_id);
        $center_users = User::where([
            ['client_id', '=' ,$client_id],
            ['salescenter_id', '=' ,$salescenter_id],
            ['access_level', '=' ,'salescenter']
           ])->orderBy('id','DESC')->paginate(20);
           $client = $this->client ;
           $salescenter = $this->salescenter ;

        return view('client.salescenter.user.userslist',compact('center_users','client_id','salescenter_id','client','salescenter'))
             ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This method is used to add salescenter user
     */
    public function adduser($client_id, $salescenter_id)
    {
        $client = $this->client ;
        $salescenter = $this->salescenter ;
        return view('client.salescenter.user.usercreate',compact('client_id','salescenter_id','client','salescenter'));
    }

    // This function is currently not in use
    public function saveuser($client_id,$salescenter_id,Request $request)
    {
        /* Start Validation rule */
         $validator = \Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',

        ]);

        if ($validator->fails())
        {
            return response()->json([ 'status' => 'error',  'errors'=>$validator->errors()->all()]);
        }
        /* End Validation rule */

      try{

            $verification_code  = str_random(20);
            $newuser = $request->only('first_name','last_name', 'email');
            $next_user_id = (new User)->nextAutoID();
            $newuser['parent_id'] = Auth::user()->id;
            $newuser['client_id'] = $client_id;
            $newuser['salescenter_id'] = $salescenter_id;
            $newuser['access_level'] = 'salescenter';
            $newuser['status'] = 'inactive';
            $newuser['userid'] = strtolower($request->first_name[0]).$next_user_id;
            $newuser['verification_code'] = $verification_code;
            $newuser['password'] = Hash::make(rand());
            $next_user_id = (new User)->createuser($newuser);
            //$this->NewUserEmail($newuser);
            return response()->json([ 'status' => 'success',  'message'=>'User created successfully.','url' => route('client.salescenter.users',['client_id' => $client_id,'salescenter_id' => $salescenter_id ] )]);
        } catch(Exception $e) {
        // echo 'Message: ' .$e->getMessage();
        return response()->json([ 'status' => 'error',  'errors'=> ["Something went wrong , please try again."]]);
        }

        //  return redirect()->route('client.salescenter.users',['client_id' => $client_id,'salescenter_id' =>$salescenter_id ])
        //     ->with('success','User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $client_id, $salescenter_id, $userid
     * @return \Illuminate\Http\Response
     */
    public function edituser($client_id,$salescenter_id, $userid)
    {

        HelperCheckClientUser($client_id);
         $user = 	User::where([
            ['client_id', '=', $client_id],
            ['salescenter_id', '=', $salescenter_id],
            ['id', '=', $userid],
          ])->firstOrFail();
          $client = $this->client ;
          $salescenter = $this->salescenter ;
         return view('client.salescenter.user.useredit',compact('user','client_id','salescenter_id','salescenter','client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $client_id, $salescenter_id, $userid
     * @return \Illuminate\Http\Response
     */
    public function updateuser($client_id,$salescenter_id,$userid,Request $request)
    {
        /* Start Validation rule */
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$userid,
            'password' => 'confirmed',
           ]);
           /* End Validation rule */
           $user = User::find($userid);
           $user->first_name = $request->first_name;
           $user->last_name = $request->last_name;
           $user->email = $request->email;
           $user->status = $request->status;
           if(!empty($request->password)){
            $user->password = Hash::make($request->password); //update the password
           }
           $user->save();
           return redirect()->to(route('client.salescenter.user.edit',['client_id' => $client_id,'salescenter_id'=>$salescenter_id, 'userid' => $userid]))
           ->with('success','User successfully updated.');
    }

    /**
     * Display the specified resource.
     *
     * @param $client_id, $salescenter_id, $userid
     * @return \Illuminate\Http\Response
     */
    public function showuser($client_id,$salescenter_id,$userid)
    {
        HelperCheckClientUser($client_id);

         $user = 	User::where([
            ['client_id', '=', $client_id],
            ['salescenter_id', '=', $salescenter_id],
            ['id', '=', $userid],
          ])->firstOrFail();
          $client = $this->client ;
          $salescenter = $this->salescenter ;
         return view('client.salescenter.user.usershow',compact('user','client_id','salescenter_id','client','salescenter' ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $client_id, $salescenter_id
     * @return \Illuminate\Http\Response
     */
    public function updatestatus($client_id,$salescenter_id,Request $request)
    {

           $user = User::find($request->userid);
           $user->status = $request->status;
           $user->save();
           return redirect()->route('client.salescenter.users',['client_id' => $client_id,'salescenter_id' =>$salescenter_id ])
           ->with('success','User successfully updated.');
    }

    /**
     * This method is used to show salescenter locationlist
     */
    public  function locations($client_id,$salescenter_id,Request $request)
    {
        HelperCheckClientUser($client_id);

        $locations = (new Salescenterslocations)->getLocations($client_id,$salescenter_id);
        $client = $this->client ;
        $salescenter = $this->salescenter ;
        return view('client.salescenter.locations.locationslist',compact('locations','client_id','salescenter_id','salescenter','client'))
             ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This method is used to add salescenter location
     */
    public function addlocation($client_id, $salescenter_id)
    {
        HelperCheckClientUser($client_id);
        $client = $this->client ;
        $salescenter = $this->salescenter;
        return view('client.salescenter.locations.createlocation',compact('client_id','salescenter_id','client','salescenter'));
    }

    /**
     * This method is used to store salescenter location
     */
    public function savelocation($client_id,$salescenter_id, Request $request)
    {

        /* Start Validation rule */
          $validator = \Validator::make($request->all(), [
            'name' => 'required',
            // 'street' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            // 'country' => 'required',
            // 'zip' => 'required',
            'zip' => 'nullable|numeric|digits:5',
            'code' => 'required|unique:salescenterslocations'

        ]);

        if ($validator->fails())
        {
            return response()->json([ 'status' => 'error',  'errors'=>$validator->errors()->all()]);
        }
        /* End Validation rule */

      try{
            $input = $request->only('name','street', 'city','state','country','zip','code');
            $input['created_by'] = Auth::user()->id;
            $input['client_id'] = $client_id;
            $input['salescenter_id'] = $salescenter_id;
            (new Salescenterslocations)->createLocation($input);
            return response()->json([ 'status' => 'success',  'message'=>'Location created successfully.','url' => route('client.salescenter.locations',['client_id' => $client_id, 'salescenter_id' =>$salescenter_id ])]);
        } catch(Exception $e) {
        // echo 'Message: ' .$e->getMessage();
        return response()->json([ 'status' => 'error',  'errors'=> ["Something went wrong, please try again."]]);
        }

    //    return redirect()->route('client.salescenter.locations',['client_id' => $client_id, 'salescenter_id' =>$salescenter_id ])
    //        ->with('success','Location created successfully');
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param $client_id, $salescenter_id, $location_id
     * @return \Illuminate\Http\Response
     */
    public function editlocation($client_id,$salescenter_id, $location_id)
    {
        HelperCheckClientUser($client_id);
         $client = $this->client ;
         $salescenter = $this->salescenter;
         $location =  (new Salescenterslocations)->getLocationDetail($location_id);
         return view('client.salescenter.locations.editlocation',compact('location','client_id','salescenter_id','client','salescenter'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $client_id, $salescenter_id, $location_id
     * @return \Illuminate\Http\Response
     */
    public function updatelocation($client_id,$salescenter_id, $location_id, Request $request)
    {
        /* Start Validation rule */
        $this->validate($request, [
            'name' => 'required',
            // 'street' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            // 'country' => 'required',
            // 'zip' => 'required',
            'zip' => 'nullable|numeric|digits:5',
          ]);
          /* End Validation rule */

        $input = $request->only('name','street', 'city','state','country','zip');
             (new Salescenterslocations)->updateLocation($location_id,$input);

       return redirect()->route('client.salescenter.locations',['client_id' => $client_id, 'salescenter_id' =>$salescenter_id ])
           ->with('success','Location successfully updated');
    }

    public function ajaxgetsalescenters(Request $request)
    {
        $client_id = $request->client_id;
        $selected = '';
        if(Auth::user()->hasAccessLevels('salescenter')) {
            $sale_centers =  getAllSalesCenter();
            $selected = 'selected';
        } else {
            $sale_centers = (New Salescenter)->getSalesCentersListByClientID($client_id);
        }
        $res_options = "";
        foreach($sale_centers as $salecenter){
            $statusClass = $salecenter->status.'-salescenters';
            $res_options.="<option value='$salecenter->id' class='all-salescenters $statusClass' $selected >".$salecenter->name."</option>";
        }
        $response = array(
            'status' => 'success',
            'options' =>  $res_options,
        );
        return \Response::json($response);
    }
    public  function ajaxgetlocation(Request $request)
    {
        $clientId = $request->client_id;
        $salescenterId = $request->salescenter_id;
        $locations = Salescenterslocations::where('status','active')->orderBy('name','asc');
        $selected = '';
        if(Auth::user()->isLocationRestriction()) {
            $locationId = Auth::user()->location_id;
            $locations->where('id',$locationId);
            $selected = "selected";
        }

        if (auth()->user()->hasMultiLocations()) {
            $locationIds = auth()->user()->locations->pluck('id');
            $locations->whereIn('id', $locationIds);
        }

        if (!empty($clientId)) {
            $locations->where('client_id',$clientId);
        }
        if (!empty($salescenterId)) {
            $locations->where('salescenter_id',$salescenterId);
        }
        $locations = $locations->get();
        $resOptions = "";
        foreach($locations as $location){
            $salescenterClass = 'salescenter-'.$location->salescenter_id;
            $resOptions.="<option class='locations-opt $salescenterClass' value='$location->id' client=$location->client_id salescenter=$location->salescenter_id $selected>$location->name</option>";
        }
        $response = array(
            'status' => 'success',
            'options' =>  $resOptions,
        );
        return \Response::json($response);
    }

    /**
     * This method is used to get salescenter location by client
     */
    public  function ajaxgetlocationbyclient(Request $request)
    {
        $client_id = $request->client_id;

        $locations = (new Salescenterslocations)->getclientLocationsInfo($client_id);
        $res_options = "";
        foreach($locations as $location){
            $res_options.="<option value=\"{$location->id}\" >".$location->name."</option>";
        }
        $response = array(
            'status' => 'success',
            'options' =>  $res_options,
        );
        return \Response::json($response);
    }

    public  function getLocationChannels(Request $request)
    {
        try{
            $locationId = $request->location_id;
            $location = Salescenterslocations::find($locationId);
            $channels = $location->channels;
            //$channels = LocationChannel::where('location_id',$locationId)->get();
            $clientId = $location->client_id;
            $res_options = "";
            foreach($channels as $channel){
                $disabled = '';
                $name = ($channel->channel == 'd2d') ? 'D2D' : ucfirst($channel->channel);

                if($name == "D2D" && !isOnSettings($clientId,'is_enable_d2d_app')) {
                    $disabled = 'disabled';
                }
                $res_options.="<option value='$channel->channel' $disabled>$name</option>";
            }
            $response = array(
                'status' => 'success',
                'options' =>  $res_options,
            );
            return \Response::json($response);
        }catch(\Exception $e) {
            Log::error($e);
            $response = array(
                'status' => 'error',
                'message' =>  $e->getMessage()
            );
            return \Response::json($response);
        }
    }
    

    /**
     * This method used to get userlist
     */
    public function userList(Request $request) {
        if ($request->ajax()) {
            $center_users = User::select(['users.*','roles.id as role','roles.display_name as role_name','roles.name as user_role','salescenterslocations.name as location_name'])
                ->leftJoin('salescenterslocations','users.location_id','=', 'salescenterslocations.id')
                ->leftJoin('role_user','users.id','=', 'role_user.user_id')
                ->leftjoin('roles','role_user.role_id','=', 'roles.id')
                ->where('users.id','!=',Auth::id())
                ->with('locations','salescenter')
                ->where([
                    ['users.client_id', '=' ,$request->client_id],
                    ['users.salescenter_id', '=' ,$request->salescenter_id],
                    ['users.access_level', '=' ,'salescenter']
                ]);
            if(Auth::user()->hasRole(['sales_center_location_admin'])) {
                $center_users->whereIn('roles.name',['sales_center_location_admin', 'sales_center_qa']);
            }
            if(Auth::user()->hasRole(['sales_center_qa'])) {
                $center_users->where('roles.name', 'sales_center_qa');
            }
            if (auth()->user()->hasMultiLocations()) {
                $locationIds = auth()->user()->locations->pluck('id');
                $center_users->whereHas('locations', function ($query) use ($locationIds) {
                    $query->whereIn('location_id', $locationIds);
                });
            }
            if(Auth::user()->isLocationRestriction()) {
                $locationId = Auth::user()->location_id;
                $center_users->where('users.location_id',$locationId);
            }
            return DataTables::of($center_users)
                ->editColumn('profile_picture', function($user){
                    $icon = getProfileIcon($user);
                    return $icon;
                })
                ->editColumn('location_name', function($user){
                    if ($user->user_role == 'sales_center_qa') {
                        $locations = $user->locations->implode('name',', ');
                    } else {
                        $locations = $user->location_name;    
                    }
                    return $locations;
                })
                ->addColumn('action', function($user){
                    $viewBtn = $editBtn =  $statusBtn ='';

                    if ($user->user_role == 'sales_center_qa') {
                        $locations = $user->locations->implode('id',',');
                    } else {
                        $locations = $user->location_id;    
                    }
                    if (\auth()->user()->hasPermissionTo('view-sales-users')) {
                        $viewBtn = '<a
                        href="javascript:void(0)"
                        data-toggle="tooltip"
                        data-placement="top" data-container="body"
                        data-original-title="View Sales Center User"

                        role="button"
                        data-type="view"
                        data-id="' . $user->id . '" 
                        data-client-id="' . $user->client_id . '" 
                        data-salescenter-id="' . $user->salescenter_id . '" 
                        data-salescenter-name="' . $user->salescenter->name . '"  
                        data-first-name="' . $user->first_name . '" 
                        data-last-name="' . $user->last_name . '" 
                        data-email="' . $user->email . '" 
                        data-role="' . $user->role . '" 
                        data-role-name="' . $user->user_role . '" 
                        data-location="' . $locations . '" 
                        data-status="' . $user->status . '" 
                        data-reason="' . $user->deactivationreason . '" 
                        class="btn salescenter-user-modal">'
                            . getimage("images/view.png") . '</a>';
                    } else {
                        $viewBtn = getDisabledBtn();
                    }

                    if ($user->salescenter->isActive() && $user->salescenter->isActiveClient() && auth()->user()->hasPermissionTo('edit-sales-users')  && $user->is_block != 1) {

                        $editBtn = '<a
                        href="javascript:void(0)"
                        data-toggle="tooltip"
                        data-placement="top" data-container="body"
                        data-original-title="Edit Sales Center User"

                        role="button"
                        data-type="edit"
                        data-id="' . $user->id . '" 
                        data-client-id="' . $user->client_id . '" 
                        data-salescenter-id="' . $user->salescenter_id . '" 
                        data-salescenter-name="' . $user->salescenter->name . '"  
                        data-first-name="' . $user->first_name . '" 
                        data-last-name="' . $user->last_name . '" 
                        data-email="' . $user->email . '"
                        data-role="' . $user->role . '" 
                        data-role-name="' . $user->user_role . '" 
                        data-location="' . $locations . '" 
                        class="btn salescenter-user-modal">'
                            . getimage("images/edit.png") . '</a>';
                    } else {
                        $editBtn = getDisabledBtn('edit');
                    }

                    if ($user->salescenter->isActive() && $user->salescenter->isActiveClient() && (auth()->user()->hasPermissionTo('deactivate-sc-admin') && $user->hasRole(['sales_center_admin']) || auth()->user()->hasPermissionTo('deactivate-sc-qa') && $user->hasRole(['sales_center_qa']) || auth()->user()->hasPermissionTo('deactivate-sc-location-admin') && $user->hasRole(['sales_center_location_admin']))) {

                        if ($user->status == 'active') {
                            $statusBtn = '<a
                            class="deactivate-salescenter-user btn"
                            href="javascript:void(0)"
                            data-toggle="tooltip"
                            data-placement="top" data-container="body"
                            title=""

                            data-original-title="Deactivate Sales Center User"
                            data-id="' . $user->id . '"
                            data-name="' . $user->full_name . '">'
                                . getimage("images/activate_new.png") . '</a>';
                        } else {
                            $editBtn = getDisabledBtn('edit');
                            $statusBtn = '<a
                            class="activate-salescenter-user btn"
                            href="javascript:void(0)"
                            data-toggle="tooltip"
                            data-placement="top" data-container="body"
                            title=""

                            data-original-title="Activate Sales Center User"
                            data-id="' . $user->id . '"
                            data-is-block="' . $user->is_block . '"
                            data-name="' . $user->full_name . '">'
                                . getimage("images/deactivate_new.png") . '</a>';
                        }
                    } else {
                        $statusBtn = getDisabledBtn('status');
                    }
                    return '<div class="btn-group">'.$viewBtn.$editBtn.$statusBtn.'<div>';
                })
                ->rawColumns(['profile_picture','action'])
                ->make(true);
        }
    }

    /**
     * This method is used to create or update user
     */
    public function createOrUpdateUser(Request $request)
    {
        
        $id= $request->id;     
        $client_id= $request->client;     
        $salescenter_id= $request->sales_center;   

        /* Start Validation rule */
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'client' => 'required',
            'sales_center' => 'required',
            //'location_id' => 'required',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'role' => 'required',
        ]);

        $validator->validate();
        /* End Validation rule */

        try{

            $data = $request->only('first_name','last_name', 'email');
            $data['location_id'] = 0;
            $locationIds = $request->location_id;
            
            if (empty($id)) {
                $verification_code  = str_random(20);
                $next_user_id = (new User)->nextAutoID();
                $data['parent_id'] = Auth::user()->id;
                $data['client_id'] = $client_id;
                $data['salescenter_id'] = $salescenter_id;
                $data['access_level'] = 'salescenter';
                $data['status'] = 'active';
                $data['userid'] = strtolower($request->first_name[0]).$next_user_id;
                $data['verification_code'] = $verification_code;
                $data['password'] = Hash::make(str_random(8));
                $user = User::create($data);
                $user->attachRole($request->role);

                $this->storeUserLocations($user,$locationIds);
                // for send verification email
                $this->sendVerificationEmail($user); 
                
                return response()->json([ 'status' => 'success',  'message'=>'Sales center user successfully created.']);
            } else {

                User::where('id',$id)->update($data);
                $user=User::find($id);
                $user->roles()->sync([$request->role]);
                $user->locations()->sync([]);
                $this->storeUserLocations($user,$locationIds);
                return response()->json([ 'status' => 'success',  'message'=>'Sales center user successfully updated.']);
            }


        } catch(\Exception $e) {
            Log::error($e);
            return response()->json([ 'status' => 'error',  'message'=> $e->getMessage()]);
        }
    }

    /**
     * This method is used to store userlocation
     */
    public function storeUserLocations($user, $locationIds ) {
        if ($user->hasRole("sales_center_qa")) {
            $user->locations()->sync($locationIds);
        } else if($user->hasRole("sales_center_location_admin")) {
            $user->location_id = isset($locationIds[0]) ? $locationIds[0] : 0 ;
            $user->save();
        }
    }

    /**
     * This method is used to change salescenter user status
     */
    public function changeUserStatus(Request $request) {
        /* Start Validation rule */
        $request->validate(
            [
                'comment'=>'required_if:status,==,inactive',
            ],
            [
                'comment.required_if' => 'The reason for deactivation  field is required.'
            ]
        );
        /* End Validation rule */
        if($request->status == 'delete')
        {
            User::where('id',$request->id)->delete();
            $message='Sales center user successfully deleted.';
        }
        else
        {
            $data = [
                'status' => $request->status,
                'deactivationreason' => $request->comment,
                'is_block' => $request->input('is_block', 0)
            ];
    
            User::where('id',$request->id)->update($data);
            if ($request->status =='active') {
                $message='Sales center user successfully activated';
            } else {
                $message='Sales center user successfully deactivated.';
            }
        }
        return response()->json([ 'status' => 'success',  'message'=>$message]);

    }

    /**
     * This method is used to get salescenter location
     */
    public function getLocations(Request $request) {
        if ($request->ajax()) {
            $locations = Salescenterslocations::with('salescenter')->where([
                ['client_id', '=' ,$request->client_id],
                ['salescenter_id', '=' ,$request->salescenter_id],
            ]);
            /* check user has multiple locations */
            if (auth()->user()->hasMultiLocations()) {
                $locationIds = auth()->user()->locations->pluck('id');
                $locations->whereIn('id', $locationIds);
            }
            /* check location level restriction */
            if(Auth::user()->isLocationRestriction()) {
                $locationId = Auth::user()->location_id;
                $locations->where('id',$locationId);
            }
            return DataTables::of($locations)
                ->editColumn('street', function($client){
                    /*$address = $client->street.', '.$client->city.', '.$client->state.', '.$client->country.', '.$client->zip;*/
                    $address = ($client->street != null ? $client->street.', ' : '') . ($client->city!= null ? $client->city.', ' : '') . ($client->state != null? $client->state.', ' : '') .($client->country != null? $client->country.', ' : '') .($client->zip != null ? $client->zip : '') ;
                    return $address;
                })
                ->addColumn('action', function($location){
                    $editBtn =  $statusBtn = $deleteBtn = '';
                    $viewBtn = '<a
                        href="javascript:void(0)"
                        data-toggle="tooltip"
                        data-placement="top" data-container="body"
                        data-original-title="View Sales Center Location"
                        role="button"
                        data-type="view"
                        data-id="'.$location->id.'"
                        class="btn salescenter-location-modal">'
                        .getimage("images/view.png").'</a>';

                    if ($location->salescenter->isActive() && $location->salescenter->isActiveClient()) {
                        $editBtn = '<a
                            href="javascript:void(0)"
                            data-toggle="tooltip"
                            data-placement="top" data-container="body"
                            data-original-title="Edit Sales Center Location"
                            role="button"
                            data-type="edit"
                            data-id="'.$location->id.'"
                            class="btn salescenter-location-modal">'
                            .getimage("images/edit.png").'</a>';

                        if($location->status == config('constants.STATUS_ACTIVE')) {
                            $statusBtn = '<button 
                                class="deactivate-location btn" 
                                role="button" 
                                data-toggle="tooltip" 
                                data-placement="top" 
                                data-container="body" 
                                title="" 
                                data-original-title="Deactivate Location" 
                                data-cid="'.$location->id.'" 
                                data-locationname="'.$location->name.'"  >'
                                .getimage("images/activate_new.png").'</button>';
                        } else {
                            $statusBtn = '<button
                                class="activate-location btn" 
                                role="button"  
                                data-toggle="tooltip" 
                                data-placement="top" 
                                data-container="body" 
                                title="" 
                                data-original-title="Activate Location" 
                                data-cid="'.$location->id.'" 
                                data-locationname="'.$location->name.'"  >'
                                .getimage("images/deactivate_new.png").'</button>';
                        }
                    } else {
                        $editBtn = getDisabledBtn('edit');
                        $statusBtn = getDisabledBtn('status');
                    }

                    if (Auth::user()->hasPermissionTo('delete-sales-center-location')) {
                        $class = 'delete-location';

                        $attributes = [
                            "data-original-title" => "Delete Location",
                            "data-id" => $location->id,
                            "data-locationname" => $location->name
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    }

                    return '<div class="btn-group">'.$viewBtn.$editBtn.$statusBtn.$deleteBtn.'<div>';
                })
                ->make(true);
        }
    }

    //Update status for sales center locations
    public function locationChangeStatus(Request $request) {
        $data = [
            'status' => $request->status,
        ];

        $userCount = User::where('location_id',$request->locationid)->count();
        $userLocationCount = UserLocation::where('location_id',$request->locationid)->count();
        $salesAgentCount = Salesagentdetail::where('location_id',$request->locationid)->count();

        if(($userCount > 0 || $userLocationCount > 0 || $salesAgentCount > 0) && $request->status =='inactive') {
            return response()->json([ 'status' => 'error',  'message'=> 'Unable to deactivat of location, due to this location assigned to users.' ]);
        } else {

            $updateStatus = Salescenterslocations::where('id', $request->locationid)->update($data);

            if ($updateStatus) {
                if ($request->status =='active') {
                    $message='Sales center location successfully activated.';
                } else {
                    $message='Sales center location successfully deactivated';
                }
                return response()->json([ 'status' => 'success',  'message' => $message]);
            } else {
                return response()->json([ 'status' => 'error',  'message'=> 'Unable to update location' ]);
            }
        }
    }

    /**
     * This method is used to remove salescenter location
     */
    public function deleteLocation(Request $request)
    {
        try{
            $locationId = $request->location_id;
            DB::beginTransaction();
            $location = Salescenterslocations::find($locationId);

            if (!empty($location)) {
                $agentIds = Salesagentdetail::where('location_id',$locationId)->pluck('user_id')->toArray();

                // Will be deleted with related data
                $telesales = Telesales::whereIn('user_id',$agentIds)->get();
                foreach ($telesales as $key => $telesale) {
                    $telesale->delete();
                }
                $userIds = User::whereHas('locations', function ($query) use($locationId) {
                            $query->where('location_id',$locationId);
                        })
                        ->orWhere('location_id',$locationId)
                        ->pluck('id')->toArray();

                // multiple location assigned users
                $qaIds = User::whereIn('id',$userIds)
                        ->has('locations','>',1)
                        ->pluck('id')->toArray();

                $diffuserIds = array_diff($userIds,$qaIds);
                $allUserIds = array_unique(array_merge($agentIds,$diffuserIds));

                Log::info('all user ids: '.print_r($allUserIds,true));

                // Will be deleted with related data
                User::destroy($allUserIds);
                $location->delete();
                DB::commit();
                return response()->json([ 'status' => 'success',  'message' => "Sales center location successfully deleted."]);
            } else {
                return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.' ]);
            }
        } catch(\Exception $e) {
            DB::rollback();
            Log::error("Error while deleting sales center location: ".$e);
            return response()->json([ 'status' => 'error',  'message'=> $e->getMessage()]);
        }
    }

    /**
     * This method is used to create or update salescenter location
     */
    public function createOrUpdateLocation(Request $request)
    {
        $id=$request->id;
        /* Start Validation rule */
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:salescenterslocations,code,'.$id ,
            // 'channels' => 'required',
            // 'street' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            // 'country' => 'required',
            // 'zipcode' => 'required|numeric|digits:5',
            'zipcode' => 'nullable|numeric|digits:5',
            // 'contact_name' => 'required',
            'contact_number' => 'nullable|numeric|digits:10',
        ]);
        /* End Validation rule */

        try{
            $input = $request->except('_token','zipcode','channels');
            $input['zip'] =$request->zipcode;
            $input['created_by'] = Auth::user()->id;
            $input['client_id'] = $request->client_id;
            $input['salescenter_id'] = $request->salescenter_id;
            $location = Salescenterslocations::updateOrCreate(['id'=>$request->id],$input);
            $location->channels()->delete();
            foreach ($request->channels as $key => $channel) {
                $location->channels()->create(['channel'=>$channel]);
            }
            if (empty($id)) {
                return response()->json([ 'status' => 'success',  'message'=>'Sales center location successfully created.']);
            } else {
                return response()->json([ 'status' => 'success',  'message'=>'Sales center location successfully updated.']);
            }
        } catch(\Exception $e) {
            return response()->json([ 'status' => 'error',  'message'=> $e->getMessage()]);
        }
    }

    /**
     * This method is used to show salescenter location
     */
    public function showLocation(Request $request) {
        $location=Salescenterslocations::find($request->id);
        if(!empty($location)) {
            $channels = $location->channels->pluck('channel');
            return response()->json([ 'status' => 'success',  'data'=>$location ,'channels' => $channels ]);
        } else {
            return response()->json([ 'status' => 'error',  'message'=>'Something went wrong, please try again.' ]);
        }
    }

    /**
     * This method is used to export salescenter
     */
    public function exportSalesCenter(Request $request,$client_id, $salecenter_id) {
        print_r("id"); exit;
        $utilities = Salescenter::where('client_id',$request->client_id)->where('id', '=', $request->id)->get();
        print_r($utilities); exit;

        $filename = "salescenter.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('Commodity', 'Brand Name','Utility Provider','Zipcode'));

        foreach($utilities as $utility) {
            if(!$utility->utilityZipcodes->isEmpty()) {
                $zipcode =$utility->utilityZipcodes->pluck('zipCode.zipcode')->implode(', ');
            } else {
                $zipcode ='N/A';
            }
            fputcsv($handle, array(
                    $utility->commodity,
                    $utility->utilityname,
                    $utility->fullname,
                    $zipcode,
                ));
        }

        fclose($handle);
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        return \Response::download($filename, 'utility.csv', $headers);

    }

    /**
     * This method is used to get salescenter and commodity
     */
    public function getSalesCenterAndCommodity(Request $request) {
        $client = Client::find($request->client_id);
        if (!empty($client)) {
            /* check user access level */
            if(Auth::check() && Auth::user()->hasAccessLevels('salescenter')) {
                $salesCenters = getAllSalesCenter();
            } else {
                $salesCenters = $client->salesCenters()->orderBy('name')->get();
            }
            $commodity = $client->commodity()->orderBy('name')->get();

            return response()->json([
                "status" => true,
                "data" => [
                    'sales_centers' => $salesCenters,
                    'commodity' => $commodity
                ]
            ]);
        } else {
            $salesCenters = getAllSalesCenter();
            $commodity = Commodity::orderBy('name')->get();
            return response()->json([

                "status" => true,
                "data" => [
                    'sales_centers' => $salesCenters,
                    'commodity' => $commodity
                ]
            ]);
        }
    }

    /**
     * This method is used to get salescenter location
     */
    public function getSalesCenterLocations(Request $request)
    {
        $client_id = $request->client_id;
        $salescenter_id = $request->salescenter_id;
        if(Auth::user()->access_level == 'sales_center') {
            $salescenter_id = Auth::user()->salescenter_id;
        }

        $locations = Salescenterslocations::where('status','active')->orderBy('name');
        if(!empty($client_id)) {
            $locations->where('client_id',$client_id);
        }
        if(!empty($salescenter_id)) {
            $locations->where('salescenter_id',$salescenter_id);
        }
        /* check user has multiple locations */
        if (auth()->user()->hasMultiLocations()) {
            $locationIds = auth()->user()->locations->pluck('id');
            $locations->whereIn('id', $locationIds);
        }
        /* check location level restriction */
        if(Auth::user()->isLocationRestriction()) {
            $locationId = Auth::user()->location_id;
            $locations->where('id',$locationId);
        }
        $locations =$locations->get();
        $res_options = "";
        if(!empty($locations)){
            foreach($locations as $location){
                $res_options.="<option value=\"{$location->id}\" >".$location->name."</option>";
            }
        }
        $response = array(
            'status' => 'success',
            'options' =>  $res_options,
        );
        return \Response::json($response);
    }

    /**
     * This method is used to check code
     */
    public function checkCode($id, Request $request) {
        $code = $request->code;
        $exists = $this->checkSalesCenterCode($code);
        
        checkAgain:
        $finalCode = $exists > 0 ? $code . strtoupper(str_random(2)) : $code;
        $exists = $this->checkSalesCenterCode($finalCode);

        if ($exists > 0) {
            goto checkAgain;
        }

        return response()->json(['status' => true, 'code' => $finalCode]);
    }

    /**
     * This method is used to check salescenter code
     */
    public function checkSalesCenterCode($code) {
        $count = Salescenter::where('code', $code)->count();
        return $count;
    }

    /**
     * This method is used to check salescenter location code
     */
    public function checkLocationCode(Request $request) {
        $code = $request->code;
        $exists = Salescenterslocations::where('code', $code)->count();
        $finalCode = $exists > 0 ? $code . strtoupper(str_random(2)) : $code;
        return response()->json(['status' => true, 'code' => $finalCode]);
    }

    /**
     * This method is used to show salescenter bulkupload
     */
    public function bulkUpload($client_id, $salescenter_id)
    {
        $client = (new Client)->getClientinfo($client_id);
        $salescenter = (new Salescenter)->getSalescenterinfo($salescenter_id);
        return view('client.salescenter.salesagent.bulkupload',compact('client', 'salescenter_id', 'salescenter'));
    }

    /**
     * This method is used to download salescenter download sample file
     */
    public function downloadSample(Request $request) {
        $data =[[
            "Location" => "Intersoft",
            "First Name" => "John",
            "Last Name" => "Doe",
            "Email" => "john1@test.com",
            "Password" => "123456",
            "Agent Type" => "tele",
            "Certified" => "1",
            "Certification Date" => "2020-01-20",
            "Certification Exp Date" => "2021-01-22",
            "State Test" => "1",
            "State" => "NY,WA",
            "Background Check" => "1",
            "Drug Check" => "1",
            "External Id" => "T001",
            "Phone Number" => "1231231231",
            "Restrict State" => "NJ",
        ],[

            "Location" => "Intersoft",
            "First Name" => "Lisa",
            "Last Name" => "William",
            "Email" => "lisa1@test.com",
            "Password" => "123456",
            "Agent Type" => "d2d",
            "Certified" => "1",
            "Certification Date" => "2020-01-22",
            "Certification Exp Date" => "2021-01-22",
            "State Test" => "0",
            "State" => "CA,PH",
            "Background Check" => "0",
            "Drug Check" => "1",
            "External Id" => "T002",
            "Phone Number" => "1112223334",
            "Restrict State" => "NJ",
        ]];

        return Excel::create('salesagent_sample', function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download("csv");
    }

    /**
     * This method is used to import salescenter
     */
    public function import(Request $request) {
        $client_id=$request->client_id;
        $salescenter_id = $request->sid;

        $client_info = Client::findOrFail($request->client_id);
        /* Start Validation rule */
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
        /* End Validation rule */

        try {

            $path = $request->file('upload_file')->getRealPath();
            $data = Excel::load($path, function($reader) {
                $columns= [
                        "location",
                        "first_name",
                        "last_name",
                        "email",
                        "password",
                        "agent_type",
                        "certified",
                        "certification_date",
                        "certification_exp_date",
                        "state_test",
                        "state",
                        "background_check",
                        "drug_check",
                        "external_id",
                        "phone_number",
                        "restrict_state"
                    ];
                $reader->select($columns);
            })
            ->ignoreEmpty()
            ->get()
            ->toArray();

            $errors=$valid_data=array();
            if (empty($data)) {
                $errors[1][]='The file is empty or invalid data.';
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }

            // For check duplicate email exists or not in uploaded sheet, if duplicate email is exist than throw the error message with email list
            $getEmails = Excel::load($path, function($reader) {
                $columns = ['email'];
                $reader->select($columns);
            })
            ->get()
            ->toArray();
            $allEmails = array_column($getEmails, 'email');
            $duplicateEmails = array();
            foreach(array_count_values($allEmails) as $val => $r){
                if($r > 1){
                    $duplicateEmails[] = $val;
                }
            }
            if (!empty($duplicateEmails)) {
                $explodeEmail = implode(', ', $duplicateEmails);
                $errors[1][] = 'Duplicate email error :- Please use an email only once, Following email(s) are used more than one times : '.$explodeEmail;
                Log::info('Duplicate email error :- Following email(s) are used more than one times : '.$explodeEmail);
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }

            foreach ($data as $key => $agent_data) {
                $locationName = "";
                if (isset($agent_data['location']) && !empty($agent_data['location'])) {
                    $locationName = $agent_data['location'];
                }
                $location = Salescenterslocations::where('name',$locationName)->where('client_id',$client_id)->where('salescenter_id',$salescenter_id)->first();
                $locationId = !empty($location) ? $location->id : null;
                /* Start Validation rule */
                $dataValidator = Validator::make($agent_data,
                    [
                        'location'          => ['required','max:255',
                            Rule::exists('salescenterslocations','name')->where(function ($query) use ($client_id,$salescenter_id) {
                                $query->where('salescenter_id',$salescenter_id)->where('client_id',$client_id);
                            })],
                        'first_name'          => 'required|max:255',
                        'last_name'          => 'required|max:255',
                        'email'          => 'required|email|max:255|unique:users,email',
                        'password'          => 'required|max:255|min:6',
                        'agent_type'          => ['required',
                            Rule::exists('location_channels','channel')->where(function ($query) use ($locationId) {
                                $query->where('location_id',$locationId);
                            })],
                        'certified'          => 'required|in:0,1',
                        'state_test'          => 'required|in:0,1',
                        'background_check'          => 'required|in:0,1',
                        'drug_check'          => 'required|in:0,1',
                        // 'external_id'          => 'required|max:255',
                        'phone_number'      => 'nullable|numeric',
                        'restrict_state'    => 'nullable|max:255',
                    ]
                );
                    
                if ($dataValidator->fails()) {
                    foreach ($dataValidator->messages()->all() as  $value) {
                        $errors[$key+1][]=$value;
                    }
                /* End Validation rule */
                } else {
                    $valid_data[$key]['first_name']=$agent_data['first_name'];
                    $valid_data[$key]['last_name']=$agent_data['last_name'];
                    $valid_data[$key]['email']=$agent_data['email'];
                    $valid_data[$key]['password']=$agent_data['password'];
                    $valid_data[$key]['agent_type']=$agent_data['agent_type'];
                    $valid_data[$key]['certified']=$agent_data['certified'];
                    $valid_data[$key]['passed_state_test']=$agent_data['state_test'];
                    $valid_data[$key]['state']=$agent_data['state'];
                    $valid_data[$key]['location_id']= $locationId;
                    $valid_data[$key]['backgroundcheck']=$agent_data['background_check'];
                    $valid_data[$key]['drugtest']=$agent_data['drug_check'];
                    $valid_data[$key]['external_id']=isset($agent_data['external_id']) ? $agent_data['external_id'] : '';;
                    //echo '<pre>'; print_r($valid_data); die;
                    if(!empty($agent_data['certification_date'])) {
                        $valid_data[$key]['certification_date'] = date('Y-m-d',strtotime($agent_data['certification_date']));
                    }
                    if(!empty($agent_data['certification_exp_date'])) {
                        $valid_data[$key]['certification_exp_date'] = date('Y-m-d',strtotime($agent_data['certification_exp_date']));
                    }

                    $valid_data[$key]['phone_number'] = isset($agent_data['phone_number']) ? $agent_data['phone_number'] : null;
                    $valid_data[$key]['restrict_state'] = isset($agent_data['restrict_state']) ? $agent_data['restrict_state'] : null;
                }
            }

            if (!empty($errors)) {
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            } else {

                foreach ($valid_data as $key => $agent_data1) {
                    $verification_code = str_random(20);
                    $data['client_id'] = $client_id;
                    $data['first_name'] = $agent_data1['first_name'];
                    $data['last_name'] = $agent_data1['last_name'];
                    $data['email'] = $agent_data1['email'];
                    $data['salescenter_id'] = $salescenter_id;
                    $data['access_level'] = 'salesagent';
                    $data['status'] = 'active';
                    $data['verification_code'] = $verification_code ;
                    $data['password'] = Hash::make($agent_data['password']);
                    $user=User::create($data);
                    $user->userid = strtolower($agent_data1['first_name'][0]).$user->id;
                    $user->save();
                    
                    
                    $agent_details['user_id']=$user->id;
                    $agent_details['passed_state_test']=$agent_data1['passed_state_test'];
                    $agent_details['state']=$agent_data1['state'];
                    $agent_details['certified']=$agent_data1['certified'];
                    $agent_details['backgroundcheck']=$agent_data1['backgroundcheck'];
                    $agent_details['drugtest']=$agent_data1['drugtest'];
                    $agent_details['added_by'] = Auth::id();
                    $agent_details['location_id']=$agent_data1['location_id'];
                    $agent_details['agent_type'] = $agent_data1['agent_type'];
                    $agent_details['external_id']=isset($agent_data1['external_id']) ? $agent_data1['external_id'] : '';
                    
                    if(!empty($agent_data1['certification_date'])) {
                        $agent_details['certification_date'] = $agent_data1['certification_date'];
                    }
                    if(!empty($agent_data1['certification_exp_date'])) {
                        $agent_details['certification_exp_date'] = $agent_data1['certification_exp_date'];
                    }
                    $agent_details['phone_number'] = $agent_data1['phone_number'];
                    $agent_details['restrict_state'] = $agent_data1['restrict_state'];

                    Salesagentdetail::create($agent_details);
                    
                }
                
                $url = \URL::route('client.salescenter.show',[$client_id,$salescenter_id]). '#SalesAgent';
                // session()->put('success', 'Sales agents successfully imported.');
                return response()->json(['status' => 'success',  'message' =>'Sales agents successfully imported.', 'url' =>$url], 200);
            }
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }


    }

    /**
     * This method is used to export salesagent
     */
    public function exportAgnets(Request $request) {
        try {
            $client = Client::findOrFail($request->client_id);

            $clientName = str_replace(" ", "-", array_get($client, 'name'));

            if ($clientName != "") {
                $fileName = $clientName . "-" . "agents";
            } else {
                $fileName = "agents";
            }

            $fileName .= "-" . date('d_M_Y_H_i_A');

            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=". $fileName .".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
            $full_agent = array();
            //$agents = (New User)->getSalesagents($request->client_id,$request->sid,"" );
            $agents = User::where('client_id',$request->client_id)->where('salescenter_id',$request->sid)->where('access_level','salesagent')->with('salesAgentDetails')->get();
            foreach($agents as $key => $agent) {
                $location_name = '';
                $user_details = isset($agent->salesAgentDetails) ? $agent->salesAgentDetails : '';
                if(isset($agent->salesAgentDetails->location) && !empty($agent->salesAgentDetails->location->name)) {
                    $location_name = $agent->salesAgentDetails->location->name;
                }
                $full_agent[$key]['location'] = $location_name ;
                $full_agent[$key]['first_name'] = isset($agent->first_name) ? $agent->first_name : '';
                $full_agent[$key]['last_name'] = isset($agent->last_name) ? $agent->last_name : '';
                $full_agent[$key]['email'] = isset($agent->email) ? $agent->email : '';
                $full_agent[$key]['agent_type'] = isset($user_details->agent_type) ? $user_details->agent_type : '';
                $full_agent[$key]['certified'] = isset($user_details->certified) ? $user_details->certified : '0';
                $full_agent[$key]['certification_date'] = isset($user_details->certification_date) ? $user_details->certification_date : '';
                $full_agent[$key]['certification_exp_date'] = isset($user_details->certification_exp_date) ? $user_details->certification_exp_date : '';
                $full_agent[$key]['state'] = isset($user_details->state) ? $user_details->state : '';
                $full_agent[$key]['passed_state_test'] = isset($user_details->passed_state_test) ? $user_details->passed_state_test : '0';
                $full_agent[$key]['backgroundcheck'] = isset($user_details->backgroundcheck) ? $user_details->backgroundcheck : '0';
                $full_agent[$key]['drugtest'] = isset($user_details->drugtest) ? $user_details->drugtest : '0';
                $full_agent[$key]['external_id'] = isset($user_details->external_id) ? $user_details->external_id : '';

                // For Phone Number and Restrict State columns
                $otherDetails = Salesagentdetail::where('user_id', '=', $agent->id)->first();
                $full_agent[$key]['phone_number'] = isset($otherDetails->phone_number) ? $otherDetails->phone_number : '';
                $full_agent[$key]['restrict_state'] = isset($otherDetails->restrict_state) ? $otherDetails->restrict_state : '';

            }
           // echo '<pre>'; print_r($full_agent);die;
            
            $columns = array('Location', 'First Name', 'Last Name', 'Email',  'Agent Type', 'Certified', 'Certification Date', 'Certification Exp Date', 'State Test', 'State', 'Background Check', 'Drug Check','External ID', 'Phone Number', 'Restrict State');
                             
             $callback = function() use ($full_agent, $columns) {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, $columns);

                        foreach($full_agent as $agentdata) {
                              fputcsv($file, array($agentdata['location'], $agentdata['first_name'],$agentdata['last_name'],$agentdata['email'],$agentdata['agent_type'], $agentdata['certified'], $agentdata['certification_date'],$agentdata['certification_exp_date'] , $agentdata['passed_state_test'],$agentdata['state'], $agentdata['backgroundcheck'], $agentdata['drugtest'],$agentdata['external_id'], $agentdata['phone_number'], $agentdata['restrict_state']  ));
                            //fputcsv($file, array($agentdata['first_name'],'2','3','4','1','2','3','4','1','2','3')); 
                        }
                        fclose($file);
                    };
                    
            return \Response::stream($callback, 200, $headers);
        } catch(\Exception $e) {
            return redirect()->back()->with('error','Something went wrong, please try again.');
        }
    }

    /**
     * This method is used to store salescenter brand
     */
    public function saveBrands(Request $request)
    {
        $data = [];
        if(isset($request->brands) && count($request->brands) > 0)
        {
            $programs = $request->programs;
            foreach($request->brands as $key => $val)
            {
                $isExist = SalescentersBrands::where('salescenter_id',$request->salescenterId)->where('brand_id',$val)->first();
                if($isExist){
                    $this->storeBrandPrograms($isExist->id, $programs[$val]);
                    continue;
                }
                $data['salescenter_id'] = $request->salescenterId;
                $data['brand_id'] = $val;
                $salescenterBrand = SalescentersBrands::create($data);
                $this->storeBrandPrograms($salescenterBrand->id, $programs[$val]);
            }
        }
            
        if(isset($request->uncheckBrands) && count($request->uncheckBrands) > 0)
        {
            foreach($request->uncheckBrands as $key => $val)
            {
                    
                $isExist = SalescentersBrands::where('salescenter_id',$request->salescenterId)->where('brand_id',$val)->first();
                if(isset($isExist) && !empty($isExist)){
                    SalescenterBrandPrograms::where('salescenter_brand_id',$isExist->id)->delete();
                    $isExist->delete();
                }
            }
        }
        return $this->success('success','Salescenter brands updated successfully.');
    }

    /**
     * This method is used to store salescenter brand program
     */
    public function storeBrandPrograms($brandProgramId, $programs)
    {
        SalescenterBrandPrograms::where('salescenter_brand_id',$brandProgramId)->delete();
        if(!empty($programs)) {
            foreach($programs as $program) {
                SalescenterBrandPrograms::updateOrCreate(['salescenter_brand_id'=>$brandProgramId ,'program_id'=> $program]);
            }
        }
    }

    // public function exportAgnets(Request $request) {
    //     try {
    //         $client = Client::findOrFail($request->client_id);

    //         $clientName = str_replace(" ", "-", array_get($client, 'name'));

    //         if ($clientName != "") {
    //             $fileName = $clientName . "-" . "agents";
    //         } else {
    //             $fileName = "agents";
    //         }

    //         $fileName .= "-" . date('d_M_Y_H_i_A');

    //         $headers = array(
    //             "Content-type" => "text/csv",
    //             "Content-Disposition" => "attachment; filename=". $fileName .".csv",
    //             "Pragma" => "no-cache",
    //             "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
    //             "Expires" => "0"
    //         );

    //         $agents = (New User)->getSalesagents($request->client_id,$request->salecenter_id,"" );
            
    //         $columns = array('Location', 'First Name', 'Last Name', 'Email',  'Agent Type', 'Certified', 'Certification Date', 'State', 'State Test', 'Background Check', 'Drug Check');
             
                             
    //          $callback = function() use ($agents, $columns) {
    //                     $file = fopen('php://output', 'w');
    //                     fputcsv($file, $columns);

    //                     foreach($agents as $agent) {
    //                         //echo '<pre>'; print_r($agent); die;
    //                         //$user_details = (new Salesagentdetail)->getUserDetail($agent->id);
    //                         fputcsv($file, array('1','2','3','4','1','2','3','4','1','2','3'));                        
    //                     }
    //                     fclose($file);
    //                 };
                    
    //         return \Response::stream($callback, 200, $headers);
    //     } catch(\Exception $e) {
    //         return redirect()->back()->with('error','Something went wrong!. Please try again.');
    //     }
    // }
    
}
