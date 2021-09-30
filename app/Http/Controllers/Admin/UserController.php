<?php
 
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\models\ClientWorkspace;
use App\models\Client;
use App\models\Role;
use App\models\Telesales;
use DataTables;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\models\Salescenter;
use Log;
use App\models\Salescenterslocations;
use Illuminate\Validation\Rule;
use Validator;
use App\models\Salesagentdetail;
use App\models\CriticalLogsHistory;
use Hash;

class UserController extends Controller
{
    /**
     * This function is used to get salescenter user
     */
    public function getSalesCenterUser(Request $request) {

        $client_id='';
        if ($request->ajax()) {
            $sales_users = User::select(['users.*','roles.id as role','roles.display_name','roles.name as user_role'])

                ->leftJoin('role_user','users.id','=', 'role_user.user_id')
                ->leftjoin('roles','role_user.role_id','=', 'roles.id')
                ->where('access_level','salescenter')
                ->where('users.id','!=',Auth::id())
                ->with('client','salescenter','location','locations');
                /* check user access level client or below to client */
            if(Auth::user()->isAccessLevelToClient()) {
                $sales_users->where('users.client_id',Auth::user()->client_id);
            }
            /* check user access level */
            if(Auth::user()->hasAccessLevels('salescenter')) {
                $sales_users->where('users.salescenter_id',Auth::user()->salescenter_id);
            }
            /* check user has multiple locations */
            if (auth()->user()->hasMultiLocations()) {
                $locationIds = auth()->user()->locations->pluck('id');
                $sales_users->whereHas('locations', function ($query) use ($locationIds) {
                    $query->whereIn('location_id', $locationIds);
                });
            }
            /* check location level restriction */
            if(Auth::user()->isLocationRestriction()) {
                $sales_users->where('users.location_id',Auth::user()->location_id);
            }
            if(Auth::user()->hasRole(['sales_center_location_admin'])) {
                $sales_users->whereHas('roles', function ($query) {
                    $query->whereIn('name',['sales_center_location_admin', 'sales_center_qa']);
                });
            }
            if(Auth::user()->hasRole(['sales_center_qa'])) {
                $sales_users->whereHas('roles', function ($query) {
                    $query->where('name', 'sales_center_qa');
                });
            }
            // To filter by status
            if($request->status == "all"){
                // return both users (active/inactive)
            }elseif($request->status == "active"){
                $sales_users->where('users.status','=',"active");
            }elseif($request->status == "inactive"){
                $sales_users->where('users.status','=',"inactive");
            }

            return DataTables::of($sales_users)
                ->editColumn('profile_picture', function($user){
                    $icon = getProfileIcon($user);
                    return $icon;
                })
            	->addColumn('client_name', function($user){
            		$name = 'N/A';
            		if(!empty($user->client->name)) {
            			$name= $user->client->name;
            		}
            		return $name;
            	})
            	->addColumn('salescenter_name', function($user){
            		$name = 'N/A';
            		if(!empty($user->salescenter->name)) {
            			$name= $user->salescenter->name;
            		}
            		return $name;
            	})
                ->addColumn('location_name', function($user){
                    $locations = '';
                    if ($user->user_role == 'sales_center_qa') {
                        $locations = $user->locations->implode('name',', ');
                    } else if(!empty($user->location->name)) {
                        $locations= $user->location->name;
                    }
                    return $locations;
                })
                ->addColumn('action', function($user){
                    $viewBtn = $editBtn = $statusBtn = $deleteBtn = '';
                    if(auth()->user()->hasPermissionTo('view-sales-users')) {
                        $viewBtn = $this->getSalesCenterActionBtn($user, 'view');
                    }else{
                        $viewBtn = getDisabledBtn();
                    }
                    if(auth()->user()->hasPermissionTo('edit-sales-users') && $user->status == 'active' && isset($user->client) && $user->client->isActive() && isset($user->salescenter) && $user->salescenter->isActive()) {
                        $editBtn = $this->getSalesCenterActionBtn($user, 'edit');
                    }else{
                        $editBtn = getDisabledBtn('edit');
                    }

                    if((auth()->user()->hasPermissionTo('deactivate-sc-admin') && $user->hasRole(['sales_center_admin']) || auth()->user()->hasPermissionTo('deactivate-sc-qa') && $user->hasRole(['sales_center_qa'] || auth()->user()->hasPermissionTo('deactivate-sc-location-admin') && $user->hasRole(['sales_center_location_admin']))) && isset($user->client)  && $user->client->isActive() && isset($user->salescenter) && $user->salescenter->isActive()) {
                        
                        $statusBtn = $this->getSalesCenterActionBtn($user, 'status');
                    }else{
                        $statusBtn = getDisabledBtn('status');
                    }
                    if ((Auth::user()->hasPermissionTo('delete-sc-admin')) || (Auth::user()->hasPermissionTo('delete-sc-qa')) || (Auth::user()->hasPermissionTo('delete-sc-location-admin'))) {
                        $class = 'delete_salescenter_user';
                        $attributes = [
                            "data-original-title" => "Delete Sales Center User",
                            "data-id" => $user->id,
                            "data-name" => $user->full_name
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    } 

                    return '<div class="btn-group">'.$viewBtn.$editBtn.$statusBtn.$deleteBtn.'<div>';
                })
                ->rawColumns(['profile_picture','action'])
                ->make(true);
        }
        $roles = (new Role)->getRolesForSalesCenterUser();
        return view('admin.users.salescenter.index',compact('client_id','roles'));
    }
    
    /**
     * This function is used to get salescenter agent
     */
    public function getSalesCenterAgent(Request $request) {
        
        $client_id='';
        if ($request->ajax()) {

            $users=User::select('users.*','salescenterslocations.name as location','salesagent_detail.phone_number','salesagent_detail.agent_type','salesagent_detail.certified')
                ->with('client','salescenter','salesAgentDetails')
                ->leftJoin('salesagent_detail', 'salesagent_detail.user_id', '=', 'users.id')
                ->leftJoin('salescenterslocations', 'salescenterslocations.id', '=', 'salesagent_detail.location_id')
                ->where('access_level', 'salesagent');

            /* check user access level client or below to client */
            if(Auth::user()->isAccessLevelToClient()) {
                $client_id = Auth::user()->client_id;
            } else {
                $client_id = $request->client_id;
            }
            /* check user access level */
            if(Auth::user()->hasAccessLevels('salescenter')) {
                $salescenter_id = Auth::user()->salescenter_id;
            } else {
                $salescenter_id = $request->salescenter_id;
            }
            /* check user has multiple locations */
            if (auth()->user()->hasMultiLocations()) {
                $locationIds = auth()->user()->locations->pluck('id');
                $users->whereHas('salesAgentDetails', function ($query) use ($locationIds) {
                    $query->whereIn('location_id', $locationIds);
                });
            }
            /* check location level restriction */
            if(Auth::user()->isLocationRestriction()) {
                $locationId = Auth::user()->location_id;
                $users->where('salesagent_detail.location_id',$locationId);
            }

            if(!empty($client_id)) {
                $users->where('users.client_id',$client_id);
            }
            if (!empty($salescenter_id)) {
                $users->where('users.salescenter_id',$salescenter_id);
                
            }

            if (!empty($request->location_id)) {
                $users->where('salesagent_detail.location_id',$request->location_id);
            }


            // To filter by status
            if($request->status == "all"){
                // return both users (active/inactive)
            }elseif($request->status == "active"){
                $users->where('users.status','=',"active");
            }elseif($request->status == "inactive"){
                $users->where('users.status','=',"inactive");

            }
            
            return DataTables::of($users)
                ->editColumn('profile_picture', function($user){
                    $icon = getProfileIcon($user);
                    return $icon;
                })
                ->addColumn('agent_type', function($user){
                    $name = 'N/A';
                    if(!empty($user->salesAgentDetails->agent_type)) {
                        $name= $user->salesAgentDetails->agent_type;
                    }
                    return $name;
                })
                ->addColumn('external_id', function($user){
                    $name = '-';
                    if(!empty($user->salesAgentDetails->external_id)) {
                        $name= $user->salesAgentDetails->external_id;
                    }
                    return $name;
                })
                ->addColumn('certified', function($user){
                    $name = 'N/A';
                    if(!empty($user->certified)) {
                        $name= $user->certified == 1 ? 'Yes' : 'No';
                    }
                    return $name;
                })
                ->addColumn('client_name', function($user){
                    $name = 'N/A';
                    if(!empty($user->client->name)) {
                        $name= $user->client->name;
                    }
                    return $name;
                })
                ->addColumn('salescenter_name', function($user){
                    $name = 'N/A';
                    if(!empty($user->salescenter->name)) {
                        $name= $user->salescenter->name;
                    }
                    return $name;
                })
                ->addColumn('action', function($user){
                    $viewBtn = $editBtn = $statusBtn = $deleteBtn = '';
                    $isEnableEditBtn = true;

                    // for check settings is on or off for d2d app
                    $isOnD2Dapp = isOnSettings($user->client_id,'is_enable_d2d_app');
                    if (!empty($user->salesAgentDetails->agent_type) && $user->salesAgentDetails->agent_type == 'd2d'  && !$isOnD2Dapp) {
                        $isEnableEditBtn = false;
                    }
                    if(auth()->user()->hasPermissionTo('view-sales-agents')) {
                        $viewBtn = $this->getSalesAgentActionBtn($user,'view');
                    }else{
                        $viewBtn = '<button 
                            data-type="view"
                            title="View Sales Agent"   
                            class="btn cursor-none" 
                            >'.getimage("images/view-no.png").'</button>';
                    }
                    if(auth()->user()->hasPermissionTo('edit-sales-agents')  && $user->is_block != 1 && $user->status == 'active' && $isEnableEditBtn && isset($user->client) && $user->client->isActive() && isset($user->salescenter) && $user->salescenter->isActive())  {
                        $editBtn = $this->getSalesAgentActionBtn($user,'edit');
                    }else{
                        $editBtn = getDisabledBtn('edit');
                    }
                    if(auth()->user()->hasPermissionTo('deactivate-sales-agent') && isset($user->client) && $user->client->isActive() && isset($user->salescenter) && $user->salescenter->isActive()) {                    
                        $statusBtn = $this->getSalesAgentActionBtn($user,'status');
                    }else {
                        $statusBtn = getDisabledBtn('status');
                    }
                    if(Auth::user()->hasPermissionTo('delete-sales-agent'))
                    {
                        $class = 'delete_sales_agent';
                        $attributes = [
                            "data-original-title" => "Delete Sales Agent",
                            "data-id" => $user->id,
                            "data-name"=> $user->full_name,
                            "data-status" =>"delete",
                        ];
                        $deleteBtn = getDeleteBtn($class, $attributes);
                    }

                    return '<div class="btn-group">'.$viewBtn.$editBtn.$statusBtn.$deleteBtn.'<div>';
                })
                ->rawColumns(['profile_picture','action'])
                ->make(true);
        }
        $clients= getAllClients();
        $salesCenters= getAllSalesCenter();
        return view('admin.users.salesagent.index',compact('clients','salesCenters','client_id'));
    }

    /**
     * This function is used to get all users
     */
    public function getAllUsers(Request $request) {
        if ($request->ajax()) {

            $users = User::select(['users.*','roles.id as role','roles.display_name','roles.name as user_role'])
                ->leftJoin('role_user','users.id','=', 'role_user.user_id')
                ->leftjoin('roles','role_user.role_id','=', 'roles.id')->where('users.id','!=',Auth::id())->whereIn('users.access_level',['client','salescenter','tpv'])->with('client','salescenter','locations');

            // To filter by status
            if($request->status == "all"){
                // return both users (active/inactive)
            }elseif($request->status == "active"){
                $users->where('users.status','=',"active");
            }elseif($request->status == "inactive"){
                $users->where('users.status','=',"inactive");
            }


            return DataTables::of($users)
                // ->editColumn('access_level', function($user){
                //     if($user->access_level == 'salescenter') {
                //         return 'Sales Center';
                //     } else if($user->access_level == 'tpv') {
                //         return 'TPV';   
                //     } else {
                //         return 'Client';
                //     }
                // })
                ->editColumn('profile_picture', function($user){
                    $icon = getProfileIcon($user);
                    return $icon;
                })
                ->addColumn('action', function($user){
                    $viewBtn =$editBtn =  $statusBtn = $deleteBtn = '';
                    if($user->access_level == 'client') {
                        $viewBtn = $this->getClientActionBtn($user,'view');

                        if (isset($user->client) && $user->client->isActive()) {
                            if($user->is_block != 1 && $user->status == 'active') {
                                $editBtn = $this->getClientActionBtn($user,'edit');
                            }else{
                                $editBtn = getDisabledBtn('edit');
                            }
                            $statusBtn = $this->getClientActionBtn($user,'status');
                        } else {
                            $editBtn = getDisabledBtn('edit');
                            $statusBtn = getDisabledBtn('status');
                        }
                        if(auth()->user()->hasPermissionTo('delete-client-user')) {
                            $class = 'delete_tpv_agent';
                            $attributes = [
                                "data-original-title" => "Delete Client User",
                                "data-id" => $user->id,
                                "data-name" => $user->full_name,
                                "data-status" =>"delete",
                                "data-text-status"=>"deleted"
                                
                            ];
                            $deleteBtn = getDeleteBtn($class, $attributes);
                        } 
                        
                    }else if($user->access_level == 'salescenter') {
                        $viewBtn = $this->getSalesCenterActionBtn($user,'view');
                        if (isset($user->client) && $user->client->isActive() && isset($user->salescenter) && $user->salescenter->isActive()) {
                            if($user->is_block != 1 && $user->status == 'active') {
                                $editBtn = $this->getSalesCenterActionBtn($user,'edit');
                            }else{
                                $editBtn = getDisabledBtn('edit');
                            }
                            $statusBtn = $this->getSalesCenterActionBtn($user,'status');
                        } else {
                            $editBtn = getDisabledBtn('edit');
                            $statusBtn = getDisabledBtn('status');
                        }
                        if(auth()->user()->can('delete-client-user')) {
                            $class = 'delete_salescenter_user';
                            $attributes = [
                                "data-original-title" => "Delete Salescenter User",
                                "data-id" => $user->id,
                                "data-name" => $user->full_name,
                                "data-status" =>"delete",
                                "data-text-status"=>"deleted"
                                
                            ];
                            $deleteBtn = getDeleteBtn($class, $attributes);
                        } 
                    }else if($user->access_level == 'tpv') {
                        $viewBtn = $this->getTPVActionBtn($user,'view');
                        if($user->is_block != 1 && $user->status == 'active') {
                            $editBtn = $this->getTPVActionBtn($user,'edit');
                        }else{
                            $editBtn = getDisabledBtn('edit');
                        }
                        $statusBtn = $this->getTPVActionBtn($user,'status');
                        if(auth()->user()->can('delete-client-user')) {
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
                                    "data-name" => $user->full_name,
                                    "data-status" =>"delete",
                                    "data-text-status"=>"deleted"
                                    
                                ];
                                $deleteBtn = getDeleteBtn($class, $attributes);
                            }
                        } 
                    }                    
                    
                    
                    return '<div class="btn-group">'.$viewBtn.$editBtn.$statusBtn.$deleteBtn.'<div>';
                })
                ->rawColumns(['profile_picture','action'])
                ->make(true);
        }
        $client_id='';
        $clientList = Client::all();
        $roles = (new Role())->getRolesForTPV();
        $clientUserRoles = (new Role())->getRolesForClientUser();
        $salesCenterUserroles = (new Role)->getRolesForSalesCenterUser();
        return view('admin.users.all_users',compact('client_id','clientList','roles','clientUserRoles','salesCenterUserroles'));
    }

    /**
     * This function is used to get all agents
     */
    public function getAllAgents(Request $request) {
        
        if ($request->ajax()) {
            $users = User::select('users.*')->where('id','!=',Auth::id())->whereIn('access_level',['salesagent','tpvagent'])->with('salescenter','salesAgentDetails');

            // To filter by status
            if($request->status == "all"){
                // return both users (active/inactive)
            }elseif($request->status == "active"){
                $users->where('users.status','=',"active");
            }elseif($request->status == "inactive"){
                $users->where('users.status','=',"inactive");
            }

            return DataTables::of($users)
                ->editColumn('profile_picture', function($user){
                    $icon = getProfileIcon($user);
                    return $icon;
                })
                ->editColumn('access_level', function($user){
                    if($user->access_level == 'salesagent') {
                        $type = '';
                        if(!empty($user->salesAgentDetails) && !empty($user->salesAgentDetails->agent_type)) {
                            $agent_type = ($user->salesAgentDetails->agent_type) == 'd2d' ? 'D2D' : ucfirst($user->salesAgentDetails->agent_type);
                            $type = " (".$agent_type.")";
                        }
                        return 'Sales Agent'.$type;
                    } else {
                        return 'TPV Agent';   
                    }
                })
                ->addColumn('action', function($user){
                    $viewBtn =$editBtn =  $statusBtn = $deleteBtn ='';
                    if($user->access_level == 'salesagent') {
                        $isEnableEditBtn = true;

                        // for check settings is on or off for d2d app
                        $isOnD2Dapp = isOnSettings($user->client_id,'is_enable_d2d_app');
                        if (!empty($user->salesAgentDetails->agent_type) && $user->salesAgentDetails->agent_type == 'd2d'  && !$isOnD2Dapp) {
                            $isEnableEditBtn = false;
                        }

                        $viewBtn = $this->getSalesAgentActionBtn($user,'view');
                        if (isset($user->client) && $user->client->isActive() && isset($user->salescenter) && $user->salescenter->isActive()) {
                            if( $user->status == 'active' && $user->is_block != 1 && $isEnableEditBtn) {
                                $editBtn = $this->getSalesAgentActionBtn($user,'edit');
                            }else{
                                $editBtn = getDisabledBtn('edit');
                            }
                            $statusBtn = $this->getSalesAgentActionBtn($user,'status');
                        } else {
                            $editBtn = getDisabledBtn('edit');
                            $statusBtn = getDisabledBtn('status');
                        }
                        if(Auth::user()->hasPermissionTo('delete-sales-agent'))
                        {
                            $class = 'delete_sales_agent';
                            $attributes = [
                                "data-original-title" => "Delete Sales Agent",
                                "data-id" => $user->id,
                                "data-name"=> $user->full_name,
                                "data-status" =>"delete",
                            ];
                            $deleteBtn = getDeleteBtn($class, $attributes);
                        }
                    }else if($user->access_level == 'tpvagent') {
                        $viewBtn = $this->getTPVAgentActionBtn($user,'view');
                        if($user->status == 'active' && $user->is_block != 1 ) {
                            $editBtn = $this->getTPVAgentActionBtn($user,'edit');
                        }else{
                            $editBtn = getDisabledBtn('edit');
                        }
                        $statusBtn = $this->getTPVAgentActionBtn($user,'status');

                        if(Auth::user()->can('delete-tpv-agent'))
                        {
                            $class = 'delete_tpv_agent';
                            $attributes = [
                                "data-original-title" => "Delete TPV Agent",
                                "data-id" => $user->id,
                                "data-name"=> $user->full_name,
                                "data-status" =>"delete",
                            ];
                            $deleteBtn = getDeleteBtn($class, $attributes);
                        }
                    }                    
                    
                    
                    return '<div class="btn-group">'.$viewBtn.$editBtn.$statusBtn.$deleteBtn.'<div>';
                })
                ->rawColumns(['profile_picture','action'])
                ->make(true);
        }
        $client_id='';
        $clientList = Client::all();
        $client_workspaces = (new ClientWorkspace)->getallWorkspaceIds();
        return view('admin.users.all_agents',compact('client_id','clientList','client_workspaces'));
    }


    public function getClientActionBtnGreyed($user,$type)
    {
        $btn='';
        if($type == 'view') {
            $btn = '<a class="btn cursor-none" title="View Client User">'.getimage("images/view-no.png").'</a>';
        }else if($type == 'edit') {
            $btn = '<a class="btn cursor-none" title="Edit Client User">'.getimage("images/edit-no.png").'</a>';
        }else if($type == 'status') {
                $btn = '<a class="btn cursor-none" title="Activate Client User">'
                    .getimage("images/deactivate_new-no.png").'</a>';

        }

        return $btn;
    }

    public function getClientActionBtn($user,$type)
    {
        $btn='';
        if($type == 'view') {
            $btn = '<a
                class="client-user-modal btn"
                href="javascript:void(0)"
                data-toggle="tooltip"
                data-placement="top" data-container="body"
                data-type="view"
                data-original-title="View Client User"
                data-id="'.$user->id.'"
                >'.getimage("images/view.png").'</a>';
        }else if($type == 'edit') {
            $btn = '<a
                class="client-user-modal btn"
                href="javascript:void(0)"
                data-toggle="tooltip"
                data-placement="top" data-container="body"
                data-type="edit"
                data-original-title="Edit Client User"
                data-id="'.$user->id.'"
                >'.getimage("images/edit.png").'</a>';
        }else if($type == 'status') {
            if($user->status == 'active') {
                $btn = '<a
                    class="deactivate-client-user btn"
                    href="javascript:void(0)"
                    data-toggle="tooltip"
                    data-placement="top" data-container="body"
                    data-original-title="Deactivate Client User"
                    data-id="'.$user->id.'"
                    data-name="'.$user->full_name.'">'
                    .getimage("images/activate_new.png").'</a>';
            } else {
                $btn = '<a
                    class="activate-client-user btn"
                    href="javascript:void(0)"
                    data-toggle="tooltip"
                    data-placement="top" data-container="body"
                    data-original-title="Activate Client User"
                    data-id="'.$user->id.'"
                    data-is-block="'.$user->is_block.'"
                    data-name="'.$user->full_name.'">'
                    .getimage("images/deactivate_new.png").'</a>';
            }
        }

        return $btn;
    }

    public function getSalesCenterActionBtnGreyed($user,$type)
    {
        $btn='';
        if($type == 'view') {
            $btn .= '<a  
                title="View Sales Center User" 
                class="btn">'
                .getimage("images/view-no.png").'</a>';
        }else if($type == 'edit') {
            $btn .= '<a  
                title="Edit Sales Center User" 
                class="btn">'
                .getimage("images/edit-no.png").'</a>';
        }else if($type == 'status'){
                $btn .= '<a 
                class="btn"  
                title="Activate Sales Center User">'
                    .getimage("images/deactivate_new-no.png").'</a>';
        }
        return $btn;
    }
    public function getSalesCenterActionBtn($user,$type)
    {
        $btn='';
        if ($user->user_role == 'sales_center_qa') {
            $locations = $user->locations->implode('id',',');
        } else {
            $locations = $user->location_id;    
        }
        if($type == 'view') {
            $btn .= '<a  
                href="javascript:void(0)"  
                data-toggle="tooltip" 
                data-container="body" 
                data-placement="top" 
                data-original-title="View Sales Center User" 
                role="button" 
                data-type="view"
                data-id="'.$user->id.'" 
                data-client-id="'.$user->client_id.'" 
                data-salescenter-id="'.$user->salescenter_id.'" 
                data-salescenter-name="'.$user->salescenter->name.'"  
                data-first-name="'.$user->first_name.'" 
                data-last-name="'.$user->last_name.'" 
                data-email="'.$user->email.'"
                data-role="'.$user->role.'" 
                data-role-name="' . $user->user_role . '" 
                data-location="' . $locations . '" 
                data-status="' . $user->status . '" 
                data-reason="' . $user->deactivationreason . '" 
                class="btn salescenter-user-modal">'
                .getimage("images/view.png").'</a>';
        }else if($type == 'edit') {
            $btn .= '<a  
                href="javascript:void(0)"  
                data-toggle="tooltip" 
                data-container="body" 
                data-placement="top" 
                data-original-title="Edit Sales Center User" 
                role="button" 
                data-type="edit"
                data-id="'.$user->id.'" 
                data-client-id="'.$user->client_id.'" 
                data-salescenter-id="'.$user->salescenter_id.'" 
                data-salescenter-name="'.$user->salescenter->name.'"  
                data-first-name="'.$user->first_name.'" 
                data-last-name="'.$user->last_name.'" 
                data-email="'.$user->email.'" 
                data-role="'.$user->role.'" 
                data-role-name="' . $user->user_role . '" 
                data-location="' . $locations . '" 
                class="btn salescenter-user-modal">'
                .getimage("images/edit.png").'</a>';
        }else if($type == 'status'){
            if($user->status == 'active') {
                $btn .= '<a 
                    class="deactivate-salescenter-user btn"  
                    href="javascript:void(0)" 
                    data-container="body" 
                    data-toggle="tooltip" 
                    data-placement="top" 
                    title="" 
                    data-original-title="Deactivate Sales Center User" 
                    data-id="'.$user->id.'" 
                    data-name="'.$user->full_name.'">'
                    .getimage("images/activate_new.png").'</a>';
            } else {
                $btn .= '<a 
                class="activate-salescenter-user btn"  
                href="javascript:void(0)" 
                data-container="body" 
                data-toggle="tooltip" 
                data-placement="top" 
                title="" 
                data-original-title="Activate Sales Center User" 
                data-id="'.$user->id.'" 
                data-is-block="'.$user->is_block.'" 
                data-name="'.$user->full_name.'">'
                .getimage("images/deactivate_new.png").'</a>';
            }
        }
        return $btn;
    }

    public function getTPVActionBtnGreyed($user,$type)
    {
        $btn='';
        if($type == 'view') {
            $btn = '<a 
                class="btn cursor-none"  
                title="View TPV User" 
                >'.getimage("images/view-no.png").'</a>';
        }else if($type == 'edit') {
            $btn = '<a
                class="btn cursor-none"  
                title="Edit TPV user" 
                >'.getimage("images/edit-no.png").'</a>';
        }else if($type == 'status') {
                $btn = '<a class="btn cursor-none"
                    title="Activate TPV User">'
                    .getimage("images/deactivate_new-no.png").'</a>';
        }
        return $btn;
    }


    public function getTPVActionBtn($user,$type)
    {
        $btn='';
        if($type == 'view') {
            $btn = '<a 
                class="tpv-user-modal btn"  
                href="javascript:void(0)"
                data-container="body"  
                data-toggle="tooltip" 
                data-placement="top" 
                data-type="view" 
                data-original-title="View TPV User" 
                data-id="'.$user->id.'" 
                >'.getimage("images/view.png").'</a>';
        }else if($type == 'edit') {
            $btn = '<a
                class="tpv-user-modal btn"  
                href="javascript:void(0)" 
                data-container="body" 
                data-toggle="tooltip" 
                data-placement="top" 
                data-type="edit" 
                data-original-title="Edit TPV user" 
                data-id="'.$user->id.'" 
                >'.getimage("images/edit.png").'</a>';
        }else if($type == 'status') {
            if($user->status == 'active') {
                $btn = '<a
                    class="deactivate-client-user btn"
                    href="javascript:void(0)"
                    data-toggle="tooltip"
                    data-placement="top" data-container="body"
                    data-original-title="Deactivate TPV User"
                    data-id="'.$user->id.'"
                    data-name="'.$user->full_name.'">'
                    .getimage("images/activate_new.png").'</a>';
            } else {
                $btn = '<a
                    class="activate-client-user btn"
                    href="javascript:void(0)"
                    data-toggle="tooltip"
                    data-placement="top" data-container="body"
                    data-original-title="Activate TPV User"
                    data-id="'.$user->id.'"
                    data-is-block="'.$user->is_block.'"
                    data-name="'.$user->full_name.'">'
                    .getimage("images/deactivate_new.png").'</a>';
            }
        }
        return $btn;
    }

    public function getTPVAgentActionBtnGreyed($user,$type)
    {
        $btn='';
        if($type == 'view') {
            $btn = '<a 
                class="btn cursor-none"  
                title="View TPV Agent" 
                >'.getimage("images/view-no.png").'</a>';
        }else if($type == 'edit') {
            $btn = '<a
                class="btn cursor-none"  
                title="Edit TPV Agent" 
                >'.getimage("images/edit-no.png").'</a>';
        }else if($type == 'status') {
                $btn = '<a
                    class="btn cursor-none"
                    title="Activate TPV Agent">'
                    .getimage("images/deactivate_new-no.png").'</a>';
        }
        return $btn;
    }

    public function getTPVAgentActionBtn($user,$type)
    {
        $btn='';
        if($type == 'view') {
            $btn = '<a 
                class="tpv-agent-modal btn"  
                href="javascript:void(0)"
                data-container="body"  
                data-toggle="tooltip" 
                data-placement="top" 
                data-type="view" 
                data-original-title="View TPV Agent" 
                data-id="'.$user->id.'" 
                >'.getimage("images/view.png").'</a>';
        }else if($type == 'edit') {
            $btn = '<a
                class="tpv-agent-modal btn"  
                href="javascript:void(0)" 
                data-container="body" 
                data-toggle="tooltip" 
                data-placement="top" 
                data-type="edit" 
                data-original-title="Edit TPV Agent" 
                data-id="'.$user->id.'" 
                >'.getimage("images/edit.png").'</a>';
        }else if($type == 'status') {
            if($user->status == 'active') {
                $btn = '<a
                    class="deactivate-client-user btn"
                    href="javascript:void(0)"
                    data-toggle="tooltip"
                    data-placement="top" 
                    data-container="body"
                    data-original-title="Deactivate TPV Agent"
                    data-id="'.$user->id.'"
                    data-name="'.$user->full_name.'">'
                    .getimage("images/activate_new.png").'</a>';
            } else {
                $btn = '<a
                    class="activate-client-user btn"
                    href="javascript:void(0)"
                    data-toggle="tooltip"
                    data-placement="top" 
                    data-container="body"
                    data-original-title="Activate TPV Agent"
                    data-id="'.$user->id.'"
                    data-is-block="'.$user->is_block.'"
                    data-name="'.$user->full_name.'">'
                    .getimage("images/deactivate_new.png").'</a>';
            }
        }
        return $btn;
    }

    public function getSalesAgentActionBtn($user,$type)
    {
        $btn='';
        if($type == 'view') {
            $btn .= '<button  data-toggle="tooltip" 
                data-placement="top" 
                data-type="view"
                data-container="body" 
                data-original-title="View Sales Agent"   
                data-title="View Sales Agent"
                class="btn salesagent-modal" 
                data-status="'.$user->status.'" 
                data-reason="'.$user->deactivationreason.'" 
                data-id="'.$user->id.'" 
                 data-userid="' . $user->userid . '" 
                data-client-id="'.$user->client_id.'" 
                data-salescenter-id="'.$user->salescenter_id.'"
                data-phone-number="'.$user->phone_number.'" 
                data-salescenter-name="'.$user->salescenter->name.'",
                data-client-name="'.$user->client->name.'" ,  
                >'.getimage("images/view.png").'</button>';
        }else if($type == 'edit') {
            $btn .=  '<button 
                class="btn salesagent-modal" 
                data-type="edit" 
                data-toggle="tooltip" 
                data-container="body"
                data-placement="top" 
                data-original-title="Edit Sales Agent" 
                data-title="Edit Sales Agent"  
                data-status="'.$user->status.'" 
                data-reason="'.$user->deactivationreason.'" 
                data-is-block="'.$user->is_block.'" 
                data-id="'.$user->id.'" 
                 data-userid="' . $user->userid . '" 
                data-client-id="'.$user->client_id.'" 
                data-salescenter-id="'.$user->salescenter_id.'"
                data-phone-number="'.$user->phone_number.'" 
                data-salescenter-name="'.$user->salescenter->name.'",
                data-client-name="'.$user->client->name.'" ,
                >'.getimage("images/edit.png").'</button>';
        }else if($type == 'status'){
            if($user->status == 'active') {
                $btn .= '<button
                class="deactivate-salescentersaleuser btn" 
                data-toggle="tooltip"
                data-placement="top" 
                data-container="body" 
                data-original-title="Deactivate Sales Agent"
                data-id="'.$user->id.'"
                data-name="'.$user->full_name.'"
                data-sid="'.$user->salescenter_id.'" 
                >'.getimage("images/activate_new.png").'</button>';
            } else {
                $btn .= '<button
                class="activate-salescentersaleuser btn" 
                data-toggle="tooltip" 
                data-placement="top"  
                data-container="body" 
                data-original-title="Activate Sales Agent"
                data-id="'.$user->id.'"
                data-is-block="'.$user->is_block.'"
                data-name="'.$user->full_name.'"
                data-sid="'.$user->salescenter_id.'" 
                >'.getimage("images/deactivate_new.png").'</button>';
            }
        }
        return $btn;
    }

    public function getSalesAgentActionBtnGreyed($user,$type)
    {
        $btn='';
        if($type == 'view') {
            $btn .= '<button title="View Sales Agent"   
                class="btn cursor-none" 
                >'.getimage("images/view-no.png").'</button>';
        }else if($type == 'edit') {
            $btn .=  '<button 
                class="btn cursor-none" 
                title="Edit Sales Agent" 
                >'.getimage("images/edit-no.png").'</button>';
        }else if($type == 'status'){
                $btn .= '<button
                class="btn cursor-none" 
                title="Activate Sales Agent"
                >'.getimage("images/deactivate_new-no.png").'</button>';
        }
        return $btn;
    }
    
    /**
     * For return view page of bulk upload  
     */
    public function salesAgentBulkUpload()
    {
        return view('admin.users.salesagent.bulkupload');
    }

    /**
     * For validate and store imported sheet data in database
     * 
     * @param $request  
     */
    public function saveSalesAgentBulkUpload(Request $request)
    {
        Log::info("In saveSalesAgentBulkUpload function for import sheet data in db.");
        
        // For check extension of uploaded file is correct or not.
        $validator = \Validator::make(
            [
                'upload_file' => $request->hasFile('upload_file')? strtolower($request->file('upload_file')->getClientOriginalExtension()) : null,
            ],
            [
                'upload_file'      => 'required|in:csv,xlsx,xls',
            ]
        );
        if ($validator->fails()) {
            Log::info("Uploaded file is in wrong extension.");
            Log::info($validator->errors()->messages());
            return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
        }
        Log::info("Uploaded file is in correct extension.");

        try {

            Log::info("Load Excel from it's real path.");
            $path = $request->file('upload_file')->getRealPath();
            $data = Excel::load($path, function($reader) {
                $columns= [
                        "client",
                        "sales_center",
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
                        "restrict_state",
                    ];
                $reader->select($columns);
            })
            ->ignoreEmpty()
            ->get()
            ->toArray();

            $errors = $valid_data = array();
            if (empty($data)) {
                $errors[1][] = 'The file is empty or invalid data.';
                Log::info("The file is empty or invalid data.");
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

            Log::info("For fetch and validate sheet data.");
            foreach ($data as $key => $agent_data) {
                // For check Client
                $clientName = "";
                if (isset($agent_data['client']) && !empty($agent_data['client'])) {
                    $clientName = $agent_data['client'];
                }
                $checkClient = Client::where('name', '=', $clientName)
                                ->where('status', '=', 'active')
                                ->first();
                $clientId = !empty($checkClient) ? $checkClient->id : null;

                // For check SalesCenter
                $salesCenterName = "";
                if (isset($agent_data['sales_center']) && !empty($agent_data['sales_center'])) {
                    $salesCenterName = $agent_data['sales_center'];
                }
                $checkSalesCenter = Salescenter::where('name', '=', $salesCenterName)
                                    ->where('status', '=', 'active')
                                    ->where('client_id', '=', $clientId)
                                    ->first();
                $salesCenterId = !empty($checkSalesCenter) ? $checkSalesCenter->id : null; 
                
                // For check Location
                $locationName = "";
                if (isset($agent_data['location']) && !empty($agent_data['location'])) {
                    $locationName = $agent_data['location'];
                }
                $location = Salescenterslocations::where('name', '=', $locationName)
                            ->where('client_id', '=', $clientId)
                            ->where('salescenter_id', $salesCenterId)
                            ->first();
                $locationId = !empty($location) ? $location->id : null;

                // Data validation
                $dataValidator = Validator::make($agent_data,
                    [
                        'client'            => ['required', 'max:255',
                            Rule::exists('clients','name')],
                        'sales_center'      => ['required', 'max:255',
                            Rule::exists('salescenters','name')->where(function ($query) use ($clientId) {
                                $query->where('client_id', $clientId);
                            })],
                        'location'          => ['required','max:255',
                            Rule::exists('salescenterslocations','name')->where(function ($query) use ($clientId, $salesCenterId) {
                                $query->where('salescenter_id',$salesCenterId)->where('client_id', $clientId);
                            })],
                        'first_name'        => 'required|max:255',
                        'last_name'         => 'required|max:255',
                        'email'             => 'required|email|max:255|unique:users,email',
                        'password'          => 'required|max:255|min:6',
                        'agent_type'        => ['required',
                            Rule::exists('location_channels','channel')->where(function ($query) use ($locationId) {
                                $query->where('location_id', $locationId);
                            })],
                        'certified'         => 'required|in:0,1',
                        'state_test'        => 'required|in:0,1',
                        'background_check'  => 'required|in:0,1',
                        'drug_check'        => 'required|in:0,1',
                        'phone_number'      => 'nullable|numeric',
                        'restrict_state'    => 'nullable|max:255',
                    ]
                );
                
                if ($dataValidator->fails()) {
                    foreach ($dataValidator->messages()->all() as  $value) {
                        $errors[$key + 1][] = $value;
                    }
                } else {
                    $valid_data[$key]['client_id'] = $checkClient->id;
                    $valid_data[$key]['salescenter_id'] = $checkSalesCenter->id;
                    $valid_data[$key]['sales_center'] = $checkSalesCenter->name;

                    $valid_data[$key]['first_name'] = $agent_data['first_name'];
                    $valid_data[$key]['last_name'] = $agent_data['last_name'];
                    $valid_data[$key]['email'] = $agent_data['email'];
                    $valid_data[$key]['password'] = $agent_data['password'];
                    $valid_data[$key]['agent_type'] = $agent_data['agent_type'];
                    $valid_data[$key]['certified'] = $agent_data['certified'];
                    $valid_data[$key]['passed_state_test'] = $agent_data['state_test'];
                    $valid_data[$key]['state'] = $agent_data['state'];
                    $valid_data[$key]['location_id'] = $locationId;
                    $valid_data[$key]['backgroundcheck'] = $agent_data['background_check'];
                    $valid_data[$key]['drugtest'] = $agent_data['drug_check'];
                    $valid_data[$key]['external_id'] = isset($agent_data['external_id']) ? $agent_data['external_id'] : '';;
                    
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
                Log::info("Bulk Upload is failed,");
                Log::info($errors);
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            } else {

                Log::info("For get valid data and store in the database.");
                foreach ($valid_data as $key => $agent_data1) {

                    $verification_code = str_random(20);
                    $data['client_id'] = $agent_data1['client_id'];
                    $data['first_name'] = $agent_data1['first_name'];
                    $data['last_name'] = $agent_data1['last_name'];
                    $data['email'] = $agent_data1['email'];
                    $data['salescenter_id'] = $agent_data1['salescenter_id'];
                    $data['access_level'] = 'salesagent';
                    $data['status'] = 'active';
                    $data['verification_code'] = $verification_code ;
                    $data['password'] = Hash::make($agent_data['password']);
                    
                    // Store in users table
                    $user = User::create($data);
                    $user->userid = strtolower($agent_data1['first_name'][0]).$user->id;
                    $user->save();
                    
                    // Create data array for store agents details
                    $agent_details['user_id'] = $user->id;
                    $agent_details['passed_state_test'] = $agent_data1['passed_state_test'];
                    $agent_details['state'] = $agent_data1['state'];
                    $agent_details['certified'] = $agent_data1['certified'];
                    $agent_details['backgroundcheck'] = $agent_data1['backgroundcheck'];
                    $agent_details['drugtest'] = $agent_data1['drugtest'];
                    $agent_details['added_by'] = Auth::id();
                    $agent_details['location_id'] = $agent_data1['location_id'];
                    $agent_details['agent_type'] = $agent_data1['agent_type'];
                    $agent_details['external_id'] = isset($agent_data1['external_id']) ? $agent_data1['external_id'] : null;
                    if(!empty($agent_data1['certification_date'])) {
                        $agent_details['certification_date'] = $agent_data1['certification_date'];
                    }
                    if(!empty($agent_data1['certification_exp_date'])) {
                        $agent_details['certification_exp_date'] = $agent_data1['certification_exp_date'];
                    }
                    $agent_details['phone_number'] = $agent_data1['phone_number'];
                    $agent_details['restrict_state'] = $agent_data1['restrict_state'];

                    // Store data in salesagent_details table
                    Salesagentdetail::create($agent_details);
                }
                
                // Redirect to the sales agent listing page
                $url = \URL::route('admin.sales.agents');
                // session()->put('success', 'Sales agents successfully imported.');
                Log::info("Sales agents successfully imported.");
                return response()->json(['status' => 'success',  'message' =>'Sales agents successfully imported.', 'url' =>$url], 200);
            }
        } catch(\Exception $e) {
            Log::info("In Exception of saveSalesAgentBulkUpload : Something went wrong!");
            Log::error($e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
    }

    /**
     * For download sample sheet for bulk upload sales agents
     * 
     */
    public function downloadSalesAgentSampleSheet(Request $request) {
        $data =[[
            "Client" => "ABC Energy",
            "Sales Center" => "ABC Sales Center",
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
            "Client" => "XYZ Energy",
            "Sales Center" => "XYZ Sales Center",
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
            "Phone Number" => "1111122222",
            "Restrict State" => "NJ",
        ]];
        
        Log::info("Create sample sheet with above dummy data for sales agent bulk upload option.");

        return Excel::create('salesagent_sample', function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download("csv");
    }

    public function updateLead(Request $request){

        if($request->update_lead_flag){
            $referenceId = $request->reference_id;
            $verificationmethod = $request->verificationMethood;
            $status = $request->status;
            if(isset($request->disposition_id) && $request->disposition_id != ""){
                $dispositionId = $request->disposition_id;
            }else{
                $dispositionId = "";
            }
            // dd($request->all());die;
            $leadData = Telesales::where('refrence_id', $referenceId)->first();
            // echo "<pre>";print_r($leadData);die;
            try {
                if($leadData->id){
                    $data = array('updated_at' => date('Y-m-d H:i:s'));
                    $data['status'] = $status;
                    $data['disposition_id'] = $dispositionId;
                    \Log::info('Lead Status changed manually for Lead Id : '.$referenceId.' '.print_r($data,true));
                    $leadupdated = (new Telesales)->updatesale($referenceId, $data);
                    
                    $user_type = config('constants.USER_TYPE_CRITICAL_LOGS.2');
                    $error_type = config('constants.ERROR_TYPE_CRITICAL_LOGS.Non-critical');
                    if($status == 'verified'){
                        $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Verified');
                    }else{
                        $lead_status = config('constants.LEAD_STATUS_CRITICAL_LOGS.Declined');
                    }
                    $salesAgentId = null;
                    $message = __('critical_logs.messages.Event_Type_49');
                    $event_type = config('constants.EVENT_TYPE_CRITICAL_LOGS.Event_type_49');
                    $reviewedby_id = Auth::user()->id;
                    // dd($message);
                    (new CriticalLogsHistory)->createCriticalLogs($salesAgentId,$message,$leadData->id,null,null,$lead_status,$event_type,$error_type,$user_type,$reviewedby_id);


                    $response = array(
                        'status' => "200",
                        'message' => "Lead updated successsfully."
                    );
                    return redirect()->route('update.lead')->with('success','Successfully updated lead.');
                }else{
                    $response = array(
                        'status' => "200",
                        'message' => "Lead id invalid."
                    );
                    return redirect()->route('update.lead')->with('error','Invalid Lead.');
                }
                
            }
            catch (\Exception $e) {
                $response = array(
                    'status' => $e->getMessage(),
                    'message' => "Something went wrong, please try again."
                );
                return redirect()->back()->with('error','Something went wrong, please try again.');
            }
            

        }
        return view('admin.leads.update-lead');
    }

}
