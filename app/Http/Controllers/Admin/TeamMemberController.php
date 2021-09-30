<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;
use App\models\Role;
use Illuminate\Support\Facades\DB;
use Hash;
use DataTables;
use Mail;
use App\models\TextEmailStatistics;


class TeamMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $params = array(['users.access_level', '=', 'tpv']);
            if (Auth::user()->parent_id != 0) {
                $params[] = array('users.parent_id', '!=', 0);
            }
            $tpv_users = User::select('users.*')->whereNotIn('users.id', [Auth::user()->id])->where($params)->with('roles');
            if(!Auth::user()->hasRole('admin')) {
                $tpv_users->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['tpv_admin','tpv_qa']);
                });
            }

            // To filter by status
            if($request->status == "all"){
                // return both users (active/inactive)
            }elseif($request->status == "active"){
                $tpv_users->where('users.status','=',"active");
            }elseif($request->status == "inactive"){
                $tpv_users->where('users.status','=',"inactive");
            }

            return DataTables::of($tpv_users)
                ->editColumn('profile_picture', function($user){
                    $icon = getProfileIcon($user);
                    return $icon;
                })
                ->addColumn('role', function($user){
                    $role ='';
                    if(!$user->roles->isEmpty()) {
                        $role = $user->roles[0]->display_name;
                    }
                    return $role;
                })
                ->addColumn('action', function($user){
                    $viewBtn = $editBtn =  $statusBtn = $deleteBtn = '';
                    if (\auth()->user()->can('view-tpv-users')) {
                        $viewBtn = '<a 
                        class="tpv-user-modal btn"  
                        href="javascript:void(0)" 
                        data-toggle="tooltip" 
                        data-placement="top" data-container="body"
                        data-type="view" 
                        data-original-title="View TPV User" 
                        data-id="' . $user->id . '" 
                        >' . getimage("images/view.png") . '</a>';
                    }else{
                        $viewBtn = '<a 
                        class="btn cursor-none"  
                        title="View TPV User">' . getimage("images/view-no.png") . '</a>';
                    }
                    if (\auth()->user()->can('edit-tpv-users')  && $user->is_block != 1 && $user->status == 'active') {
                        $editBtn = '<a 
                        class="tpv-user-modal btn"  
                        href="javascript:void(0)" 
                        data-toggle="tooltip" 
                        data-placement="top" data-container="body"
                        data-type="edit" 
                        data-original-title="Edit TPV User" 
                        data-id="' . $user->id . '" 
                        >' . getimage("images/edit.png") . '</a>';
                    }else{
                        $editBtn = '<a 
                        class="btn cursor-none"  
                        title="Edit TPV User">' . getimage("images/edit-no.png") . '</a>';
                    }

                    // $role ='';
                    // if(!$user->roles->isEmpty()) {
                    //     $role = $user->roles[0]->name;
                    // }
                    if (auth()->user()->can(['deactivate-global-admin'])  && $user->hasRole(['admin']) || auth()->user()->can(['deactivate-tpv-admin'])  && $user->hasRole(['tpv_admin']) || auth()->user()->can(['deactivate-tpv-qa'])  && $user->hasRole(['tpv_qa'])) {

                        if ($user->status == 'active') {
                            $statusBtn = '<a
                                class="deactivate-client-user btn"
                                href="javascript:void(0)"
                                data-toggle="tooltip"
                                data-placement="top" data-container="body"
                                data-original-title="Deactivate TPV User"
                                data-id="' . $user->id . '"
                                data-name="' . $user->full_name . '">'
                                . getimage("images/activate_new.png") . '</a>';
                        } else {
                            $statusBtn = '<a
                                class="activate-client-user btn"
                                href="javascript:void(0)"
                                data-toggle="tooltip"
                                data-placement="top" data-container="body"
                                data-original-title="Activate TPV User"
                                data-id="' . $user->id . '"
                                data-is-block="' . $user->is_block . '"
                                data-name="' . $user->full_name . '">'
                                . getimage("images/deactivate_new.png") . '</a>';
                        }
                    }else{
                        $statusBtn = '<a class="btn cursor-none"
                                title="Activate TPV User">'
                            . getimage("images/deactivate_new-no.png") . '</a>';
                    }
                    if ((Auth::user()->can(['delete-tpv-admin'])) || (Auth::user()->can(['delete-tpv-qa']))) {
                        if($user->hasRole(['admin']))
                        {
                            $deleteBtn = "<button class='btn cursor-none' role='button' data-toggle='tooltip' data-placement='top' data-container='body'>".getimage('images/cancel-no.png')."</button>";
                        }
                        else
                        {
                            $class = 'delete_tpv_agent';
                            $attributes = [
                                "data-original-title" => "Delete TPV User",
                                "data-id" => $user->id,
                                "data-name" => $user->full_name
                            ];
                            $deleteBtn = getDeleteBtn($class, $attributes);
                        }
                    }
                     

                    return '<div class="btn-group">'.$viewBtn.$editBtn.$statusBtn.$deleteBtn.'<div>';
                })
                ->rawColumns(['profile_picture','action'])
                ->make(true);
        }
        $roles =(new Role())->getRolesForTPV();
        $params = array(['access_level', '=', 'tpv'], ['status', '=', 'active']);
        if (Auth::user()->parent_id != 0) {
            $params[] = array('parent_id', '!=', 0);
        }
        $tpv_users = User::whereNotIn('id', [Auth::user()->id])->where($params)->orderBy('id', 'DESC')->get();
        return view('tpvusers.index',compact('tpv_users','roles'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = Role::pluck('display_name','id'); 
        return view('teammembers.create',compact('roles')); //return the view with the list of roles passed as an array
    }
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $id= $request->id;
        /* Start Validation rule */
        $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id.',id,deleted_at,NULL',    
            'roles' => 'required',
        ],[
            'email.unique'=>'This email is taken',
            'first_name.required' => 'This field is required',
            'last_name.required' => 'This field is required',
            'email.required' => 'This field is required',
            'role.required' => 'This field is required'
        ]);
        /* End Validation rule */
        
        try{
            if (empty($id)) {
                $input = $request->only('first_name','last_name', 'email', 'password');
                $next_user_id = (new User)->nextAutoID();
                $input['parent_id'] = Auth::user()->id;
                $input['access_level'] = 'tpv';

                $input['userid'] = strtolower($request->first_name[0]).$next_user_id;
                $input['verification_code'] = str_random(20);
                $input['password'] = Hash::make(rand()); //Hash password
                $user = User::create($input);

                $user->attachRole($request->input('roles'));
                // for send verification email
                $this->sendVerificationEmail($user); 

                return response()->json([ 'status' => 'success',  'message'=>'User successfully created.']);
            }else{
                $input = $request->only('first_name','last_name', 'email');
                $user = User::find($id);
                $user->update($input); //update the user info
                //delete all roles currently linked to this user
                DB::table('role_user')->where('user_id',$id)->delete();
                //attach the new roles to the user
                /*foreach ($request->input('roles') as $key => $value) {
                    $user->attachRole($value);
                }*/
                $user->attachRole($request->input('roles'));
                Log::info("Successfully updated details of TPV user, id : ".$id);
                return response()->json([ 'status' => 'success',  'message'=>'User successfully updated.']);
            }

        } catch(\Exception $e) {
            Log::error("In Exception of store method : Something went wrong!");
            Log::error($e);
            return response()->json([ 'status' => 'error',  'errors'=> "Something went wrong, please try again."]);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('teammembers.show',compact('user'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::get(); //get all roles
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('teammembers.edit',compact('user','roles','userRoles'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update( $id,Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'confirmed',
            'roles' => 'required'
        ]);
        $input = $request->only( 'email', 'password','first_name','last_name');
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']); //update the password
        }else{
            $input = array_except($input,array('password')); //remove password from the input array
        }
        $user = User::find($id);
        $user->update($input); //update the user info
        //delete all roles currently linked to this user
        DB::table('role_user')->where('user_id',$id)->delete();
        //attach the new roles to the user
        foreach ($request->input('roles') as $key => $value) {
            $user->attachRole($value);
        }
        return redirect()->route('teammembers.index')
            ->with('success','User successfully updated.');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request)
    {
         
        (new User)->updateUserStatus($request->userid,'inactive');
        if ($request->ajax()) {
            return response()->json([ 'status' => 'success',  'message'=>'TPV User successfully deleted.']);
        }
        return redirect()->route('teammembers.index')->with('success','User successfully deleted.');
    }

    /**
     * This function is used to get tpv user details
     */
    public function getTpvUser(Request $request)
    {
        $user = User::find($request->user_id);
        //$userRoles = $user->roles->pluck('id')->toArray();
        $userRoles = $user->roles->first();
        return response()->json([ 'status' => 'success',  'data'=>$user,'userrole'=>$userRoles]);
    }

    /**
     * This function is used for change user status
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
        $data = [
            'status' => $request->status,
            'deactivationreason' => $request->comment,
            'is_block' => $request->input('is_block', 0)
        ];

        User::where('id',$request->id)->update($data);
        if ($request->status =='active') {
            $message='TPV user successfully activated.';
        } else {
            $message='TPV user successfully deactivated.';
        }
        return response()->json([ 'status' => 'success',  'message'=>$message]);

    }

 
 

}
