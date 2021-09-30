<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\models\Role;
use App\models\Permission;
use App\models\Client;
use App\models\PermissionRoleClientSpecific;
use DB;
use Log;
use App\helper;

class RoleController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function __construct()
    {
        $this->middleware(['role:admin']);
    }
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('id','DESC')->paginate(20);
        $breadcrum = array(
               array('link' => '', 'text' => 'Role Management')
        );

        return view('roles.index',compact('roles','breadcrum'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $permissions = Permission::pluck('display_name','id');
        $breadcrum = array(
            array('link' => route('roles.index'), 'text' => 'Role Management')
     );
        return view('roles.create',compact('permissions','breadcrum')); //return the view with the list of permissions passed as an array
    }
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'display_name' => 'required',
            'description' => 'required',
            'permissions' => 'required',
        ]);
       
        //create the new role
        $role = new Role();
        $role->name = $request->input('name');
        $role->display_name = $request->input('display_name');
        $role->description = $request->input('description');
        $role->save();
        //attach the selected permissions
        foreach ($request->input('permissions') as $key => $value) {
            $role->attachPermission($value);
        }
        return redirect()->route('roles.index')
            ->with('success','Role successfully created.');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $role = Role::find($id); //Find the requested role
        //Get the permissions linked to the role
        $breadcrum = array(
            array('link' => route('roles.index'), 'text' => 'Role Management')
     );
        $permissions =
            Permission::join("permission_role","permission_role.permission_id","=","permissions.id")
            ->where("permission_role.role_id",$id)
            ->get();
        //return the view with the role info and its permissions
        return view('roles.show',compact('role','permissions','breadcrum'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
      
        $role = Role::find($id);//Find the requested role
        $permissions = Permission::get(); //get all permissions
        //Get the permissions ids linked to the role
        $breadcrum = array(
            array('link' => route('roles.index'), 'text' => 'Role Management')
     );


        $rolePermissions =
//            DB::table("permission_role")
//                ->where("permission_role.role_id",$id)
//                ->pluck('permission_role.permission_id','permission_role.permission_id')
//                ->toArray();
            DB::table("permission_role")
                ->where("role_id",$id)
                ->pluck('permission_id')
                ->toArray();
        return view('roles.edit',compact('role','permissions','rolePermissions','breadcrum'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'display_name' => 'required',
            'description' => 'required',
            'permissions' => 'required',
        ]);
        
        //Find the role and update its details
        $role = Role::find($id);
        $role->display_name = $request->input('display_name');
        $role->description = $request->input('description');
        $role->save();
        //delete all permissions currently linked to this role
        DB::table("permission_role")->where("role_id",$id)->delete();
        //attach the new permissions to the role
        foreach ($request->input('permissions') as $key => $value) {
            $role->attachPermission($value);
        }
        return redirect()->route('roles.index')
            ->with('success','Role successfully updated.');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        DB::table("roles")->where('id',$request->id)->delete();
        return redirect()->route('roles.index')
            ->with('success','Role successfully deleted.');
    }

    /**
     * show listing of assigned roles of permission
     * @param Request $request
     * @return array|\Illuminate\Contracts\View
     */
    public function getPermissions(Request $request)
    {
        $permissions = Permission::with('roles')->orderBy('group_order')->orderBy('id')->get();
        $permissions = $permissions->groupBy('group');
        $roles =Role::all();
        $roles_array = [];
       
        foreach($roles->toArray() as $key=>$role){
            
           if($role['name'] == "client_admin" || $role['name'] == "sales_center_admin" || $role['name'] == "sales_center_qa" || $role['name'] == "sales_center_location_admin"){
            unset($role[$key]);
           }else{
             $roles_array[$key] = $role;
           }
           
        }
       
        return view('roles.permissions',compact('permissions','roles','roles_array'));
    }

    /**
     * edit assigned roles of permission at access level
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory
     */
    public function editPermissionsRoles(Request $request)
    {
       
        $permissions = Permission::where('name','<>','deactivate-global-admin')->orderBy('group_order')->orderBy('id')->with('roles','accessLevels')->get();
        $permissions = $permissions->groupBy('group');
        $roles =Role::all();
        $roles_array = [];
       
        foreach($roles->toArray() as $key=>$role){
            
           if($role['name'] == "client_admin" || $role['name'] == "sales_center_admin" || $role['name'] == "sales_center_qa" || $role['name'] == "sales_center_location_admin"){
            unset($role[$key]);
           }else{
             $roles_array[$key] = $role;
           }
           
        }
        return view('roles.edit-permissions-roles',compact('permissions','roles','roles_array'));
    }

    /**
     * update  roles of permission at access level
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory
     */
    public function updatePermissionsRoles(Request $request)
    {
        
        $this->validate($request, [
            'permissions' => 'required',
        ]);
        try {

            foreach ($request->permissions as $key => $value) {
                $role =Role::where('name',$key)->first();
                $role->perms()->sync($value);
            }

            return redirect()->route('all.permissions')->with('success','Successfully updated roles.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error',$e->getMessage());
        }

    }

    // Client Specific permissions Methods
    // get client specific permissions

    public function getExternalPermissions(Request $request)
    {
        $permissions = Permission::with('roles')->orderBy('group_order')->orderBy('id')->get();
        $permissions = $permissions->groupBy('group');
        $roles = Role::all();
        $roles_array = [];
        foreach($roles->toArray() as $key=>$role){
           if($role['name'] == "admin" || $role['name'] == "tpv_admin" || $role['name'] == "tpv_qa"){
             unset($role[$key]);
           }else{
             $roles_array[$key] = $role;
           }
        }
        $client = Client::find(config('constants.DEFAULTS_CLIENT_ID_PERMISSION'));
        $permission_role_specific = PermissionRoleClientSpecific::where('client_id',array_get($client,'id'))->get();
        $clients = Client::select('id','name')->get()->toArray();
        return view('roles.external-permissions',compact('permissions','roles_array','clients','permission_role_specific'));
    }

    // get client specific permission on change of client 

    public function getClientExternalPermissions($client_id){
        $permissions = Permission::with('roles')->orderBy('group_order')->orderBy('id')->get();
        $permissions = $permissions->groupBy('group');
        $roles = Role::all();
        $roles_array = [];
        foreach($roles->toArray() as $key=>$role){
           if($role['name'] == "admin" || $role['name'] == "tpv_admin" || $role['name'] == "tpv_qa"){
             unset($role[$key]);
           }else{
             $roles_array[$key] = $role;
           }
        }
        $permission_role_specific = PermissionRoleClientSpecific::where('client_id',$client_id)->get();
        $clients = Client::select('id','name')->get()->toArray();
        return view('roles.external-permissions',compact('permissions','roles_array','clients','permission_role_specific'));

    }

    //edit client specific permissions

    public function editExternalPermissionsRoles(Request $request)
    {
        $client_id = $request->client_id;
        $permissions = Permission::where('name','<>','deactivate-global-admin')
       ->orderBy('group_order')->orderBy('id')->with('accessLevels')->get();
        $permission_role_specific = PermissionRoleClientSpecific::where('client_id',$request->client_id)->get();
        $permissions = $permissions->groupBy('group');
        $roles =Role::all();
        $roles_array = [];
        foreach($roles->toArray() as $key=>$role){
           if($role['name'] == "admin" || $role['name'] == "tpv_admin" || $role['name'] == "tpv_qa"){
             unset($role[$key]);
           }else{
             $roles_array[$key] = $role;
           }
        }
        $clients = Client::select('id','name')->get()->toArray();
        return view('roles.edit-external-permissions-roles',compact('clients','permissions','roles_array','permission_role_specific','client_id'));
    }
    
    //edit client specific permission on change of client dropdwon

    public function getExternalPermissionsRoles($client_id){
       
        $permissions = Permission::where('name','<>','deactivate-global-admin')
       ->orderBy('group_order')->orderBy('id')->with('accessLevels')->get();
         $permission_role_specific = PermissionRoleClientSpecific::where('client_id',$client_id)->get();
        $permissions = $permissions->groupBy('group');
        $roles =Role::all();
        $roles_array = [];
        foreach($roles->toArray() as $key=>$role){
           if($role['name'] == "admin" || $role['name'] == "tpv_admin" || $role['name'] == "tpv_qa"){
             unset($role[$key]);
           }else{
             $roles_array[$key] = $role;
           }
        }
        $clients = Client::select('id','name')->get()->toArray();
        return view('roles.edit-external-permissions-roles',compact('clients','permissions','roles_array','permission_role_specific','client_id'));

    }

    // update client specific permissions
    public function updateExternalPermissionsRoles(Request $request)
    {
        
        $this->validate($request, [
            'permissions' => 'required',
            'client_id' => 'required'
        ]);
        PermissionRoleClientSpecific::where('client_id',$request->client_id)->delete();
        try {
            foreach ($request->permissions as $key => $value) {
                $role =Role::where('name',$key)->first();
                for($i=0;$i< count($value);$i++)
                {
                    $data['client_id'] = $request->client_id;
                    $data['role_id'] = $role->id;
                    $data['permission_id'] = $value[$i];

                    $client_roles  = PermissionRoleClientSpecific::create($data);
                   
                }
            }

            return redirect()->route('external.permissions')->with('success','Successfully updated roles.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error',$e->getMessage());
        }

    }
}