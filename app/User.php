<?php

namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\models\Salesagentdetail;
use App\models\Salesagentlocation;
use App\models\SalesAgentActivity;
use App\models\UserLocation;
use DB;
use App\models\PermissionRoleClientSpecific;
use App\models\Permission;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;
    use HasApiTokens;
    use SoftDeletes { SoftDeletes::restore insteadof EntrustUserTrait; }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primarykey = 'id';
    protected $table = 'users';
    protected $fillable = [
        'first_name',
        'last_name',
        'email', 
        'password',
        'parent_id',
        'client_id',
        'access_level',
        'verification_code',
        'title',
        'status',
        'salescenter_id',
        'location_id',
        'userid',
        'last_activity',
        'deactivationreason',
        'hire_options',
        'profile_picture',       
        'is_block',
        'session_id',
        'location_id'
    ];

    

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['full_name'];

    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function salesAgentDetails() {
        return $this->hasOne("App\models\Salesagentdetail",'user_id');
    }
    public function salesAgentDetailsWithTrashed() {
        return $this->hasOne("App\models\Salesagentdetail",'user_id')->withTrashed();
    }

    // use for tpv agent language
    public function languages() {
        return $this->hasOne("App\models\TpvAgentLanguage",'user_id');
    }

    public function salesAgentActivities() {

        return $this->hasMany("App\models\SalesAgentActivity",'agent_id');
    }

    public function client() {
         return $this->belongsTo("App\models\Client",'client_id');
    }

    public function salescenter() {
         return $this->belongsTo("App\models\Salescenter",'salescenter_id');
    }

    public function location() {
         return $this->belongsTo("App\models\Salescenterslocations",'location_id');
    }

    public function locations() {
        return $this->belongsToMany("App\models\Salescenterslocations","user_locations","user_id","location_id")->withTimestamps();
    }

    /**
     *  for check user access level
     * @param null $accessLevels
     * @return bool
     */
    public function hasAccessLevels($accessLevels) {
        
        if (is_array($accessLevels)) {
            return in_array($this->access_level, $accessLevels);
        }
        return $this->access_level == $accessLevels;
    }

    /**
     * for check user access level client or below to client
     * @return bool
     */
    public function isAccessLevelToClient() {
        $accessLevels = ['client','salescenter','salesagent'];
        return in_array($this->access_level, $accessLevels);
    }

    /**
     * for check location level restriction
     * @return bool
     */
    public function isLocationRestriction() {
        return $this->hasRole(['sales_center_location_admin']);
    }

    /**
     * for check user has multiple locations
     * @return bool
     */
    public function hasMultiLocations() {
        return $this->hasRole(['sales_center_qa']);
    }

    /**
     * for get location name of user
     * @return string
     */
    public function getLocationName() {
        $location = '';
        if ($this->hasRole("sales_center_qa")) {
            $location = $this->locations->implode('name',', ');
        } else if($this->hasRole("sales_center_location_admin")) {
            $location = $this->location ? $this->location->name : '';
        }
        return $location;
    }
     
    public function getClientUsers($client_id,$userid){
       return $this->where([
            ['client_id', '=', $client_id],
            ['id', '=', $userid],
          ])->with('roles')->firstOrFail();
    }
    public function getSalesagents($client_id,$salescenter_id="",$location_id ="" ){
        $params = array(
                  array(
                    'client_id', '=', $client_id
                  ),
                  array(
                    'access_level', '=', 'salesagent'
                  )
        );
        if(!empty($salescenter_id)){
         $params[] =   array(
                'salescenter_id', '=', $salescenter_id
         );
        }
        if($location_id != ""){
            $params[] =   array(
                   'location_id', '=', $location_id
            );
           }
          
        return $this->where($params)->orderBy('id','DESC')->paginate(20);
     }
     public function createSalesagent($data)
     {   
        return $this->insertGetId(
            [ 
                'first_name' => $data['first_name'], 
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'parent_id' => $data['parent_id'],
                'client_id' => $data['client_id'],
                'salescenter_id' => $data['salescenter_id'],
                'verification_code' => $data['verification_code'],
                'access_level' => $data['access_level'],
                'location_id' => $data['location_id'],
                'userid' => $data['userid'],                
                'status' => $data['status'],
                'password' => $data['password'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                ]
        );
     } 
     public function getSalescenterUser($client_id,$salescenter_id,$userid){
        return $this->where([
            ['client_id', '=', $client_id],
            ['salescenter_id', '=', $salescenter_id],
            ['id', '=', $userid],
           ])->firstOrFail();
     }
     public function getUser($userid){
        return $this->where([
             ['id', '=', $userid],
           ])->with('roles')->firstOrFail();
     }
     public function updateSalesagent($id,$inputs)
   {
       $update_Array = array(
        'first_name' => $inputs['first_name'],
        'last_name' => $inputs['last_name'],
        'email' => $inputs['email'],
        'location_id' => $inputs['location'],
       );
       if(isset($inputs['password']))
        {
            $update_Array['password']  = $inputs['password'];
        }    
      return $this -> where('id',$id)
                 ->update( $update_Array  );
   } 
   public function updateUserStatus($id,$status,$reasonfordeactivation = null, $hire_option = null)
   {    
          
       if($status == 'active'){
                return $this -> where('id',$id)
                ->update( array(
                    'status' => $status,
                    'deactivationreason' => '',
                    'hire_options' => '', 
                ));     
       }else{

        if( !empty($reasonfordeactivation)){
            return $this -> where('id',$id)
                ->update( array(
                    'status' => $status,
                    'deactivationreason' => $reasonfordeactivation,
                    'hire_options' => $hire_option,
                ));

        }else{
            return $this -> where('id',$id)
                ->update( array(
                    'status' => $status
                ));

        }
                
       }
     
   } 
   
   public function updateUser($id,$inputs)
   {
      return $this -> where('id',$id)
                 ->update( $inputs  );
   } 
   public function nextAutoID()
   {
       $statement = DB::select("SHOW TABLE STATUS LIKE '".$this->table."'");
       $nextId = $statement[0]->Auto_increment;
       return $nextId;
   }
   
   public function createuser($data){
    return $this->insertGetId($data );
   }

   public function getClientSalesagents($client_id = null){
    $params = array(
              
              array(
                'access_level', '=', 'salesagent'
              )
    );
    if( !empty($client_id) ) {
        $params[] =   array(
            'client_id', '=', $client_id
        );
    }
    
         
    return $this->select('id','first_name','last_name','email','userid') 
    ->where($params)->orderBy('first_name','asc')->get();
 }

 public function getClientSalesagentsForReport($client_id = null, $client_status = null,$user_status = null ){
    $params = array(
              
              array(
                'users.access_level', '=', 'salesagent'
              )
    );
    if( !empty($client_id) ) {
        $params[] =   array(
            'users.client_id', '=', $client_id
        );
    }
    if( !empty($client_status) ) {
        $params[] =   array(
            'clients.status', '=', $client_status
        );
    }
    if( !empty($user_status) ) {
        $params[] =   array(
            'users.status', '=', $user_status
        );
    }
    
         
    return $this->select('users.id','users.first_name','users.last_name','users.email','users.userid') 
    ->leftJoin('clients', 'clients.id', '=', 'users.client_id')
    ->where($params)->orderBy('users.first_name','asc')->get();
 }

  


 public function verifyAgent($userid,$workspace_id, $clientId){
    return $this->select('users.id','users.first_name','users.last_name','users.email','users.userid','users.client_id','users.salescenter_id','users.location_id','clients.name as client_name','salescenters.name as salescenter_name' ,'salescenterslocations.name as location_name','salescenterslocations.code as locationcode','clients.code as clientcode','salescenters.code as salescentercode' )
    ->leftJoin('clients', 'clients.id', '=', 'users.client_id')
    ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
    ->leftJoin('salesagent_detail', 'salesagent_detail.user_id', '=', 'users.id')
    ->leftJoin('salescenterslocations', 'salescenterslocations.id', '=', 'salesagent_detail.location_id')
    ->leftJoin('client_twilio_workflowids', 'client_twilio_workflowids.client_id', '=', 'users.client_id')
//->leftJoin('salescenterslocations', 'salescenterslocations.id', '=', 'users.location_id')
    ->where('users.deleted_at', '=',null)
    ->where([
        ['users.userid', '=', $userid],
        ['users.status', '=', 'active'],
        ['users.access_level', '=', 'salesagent'],
        ['client_twilio_workflowids.workspace_id', '=', $workspace_id]
       ])->get();
 }

 public function getInactive($userid = ""){
    $date = date("Y-m-d H:i:s");
    $time = strtotime($date);
    $time = $time - (config('constants.INACTIVE_TPV_AGENT_TIMEOUT_MINS') * 60);

    $date = date("Y-m-d H:i:s", $time);
    
    return $this->select('id','last_activity','access_level', 'session_id')
           ->whereNotNull('last_activity')
           ->when($userid , function ($query) use ($userid) {
                return $query->where('id',$userid);
            })
           ->where('last_activity', '<=',$date )
           ->where('last_activity', '!=','0000-00-00 00:00:00' )
           ->get();
 }

 public function deleteuser($userid){
     return $this->where('id', '=',$userid )->delete();
 }

 public function getinactivesalesagents($startdate, $enddate){
    $user_ids = DB::select("   select distinct telesales.user_id from telesales where created_at not BETWEEN '".$startdate."' AND '".$enddate."' 
    and user_id not in ( select distinct t2 .user_id from telesales t2 where t2.created_at BETWEEN ''".$startdate."' AND '".$enddate."' and t2.user_id in ( SELECT id
        FROM `users` where access_level = 'salesagent' and status ='active')  ) ");
    return $user_ids;
 }

 public function makeinactive($userids =array()){
    return $this ->whereIn('id',$userids)
    ->update( array(
        'status' => 'inactive',
        'deactivationreason' => 'No sale made in 30 days.'
    ));
 }

 public function InactiveSalesAgentsList( $client_id = "",  $salecenter_id ="" , $location_id=""  ){
        return  DB::table('users')
            ->join('clients', 'clients.id', '=', 'users.client_id') 
            ->select('users.id', 'users.first_name', 'users.last_name','clients.name','users.userid','users.salescenter_id','users.email','users.status','users.deactivationreason', 'users.hire_options')
            ->where('clients.status','active')
            ->where('users.status','inactive')
            ->when($client_id , function ($query) use ($client_id) {
                return $query->where('users.client_id',$client_id);
            })
            ->when($salecenter_id , function ($query) use ($salecenter_id) {
                return $query->where('users.salescenter_id',$salecenter_id);
            })
            ->when($location_id , function ($query) use ($location_id) {
                return $query->where('users.location_id',$location_id);
            })
            ->paginate(20);
   }

public function InactiveSalesAgentsListForExport( $client_id = "",  $salecenter_id ="" , $location_id=""  ){
    return  DB::table('users')
        ->join('clients', 'clients.id', '=', 'users.client_id') 
        ->select(
            DB::raw( "users.first_name as FirstName"),
            DB::raw( "users.last_name as LastName"),
            DB::raw( "clients.name as ClientName"),
            DB::raw( "users.userid as UserID"),
            DB::raw( "users.email as Email"),
            DB::raw( "users.deactivationreason as ReasonToDeactivate"),          
            DB::raw( "users.hire_options as HireOption")          

         )
        ->where('clients.status','active')
        ->where('users.status','inactive')
        ->when($client_id , function ($query) use ($client_id) {
            return $query->where('users.client_id',$client_id);
        })
        ->when($salecenter_id , function ($query) use ($salecenter_id) {
            return $query->where('users.salescenter_id',$salecenter_id);
        })
        ->when($location_id , function ($query) use ($location_id) {
            return $query->where('users.location_id',$location_id);
        })
        ->get();
 }

 public function OnlineSalesagents($client_id = null){
    $params = array(
              
              array(
                'access_level', '=', 'salesagent'
              ),
              array(
                'session_id', '!=', ''
              ),
    );
    if( !empty($client_id) ) {
        $params[] =   array(
            'client_id', '=', $client_id
        );
    }
    
         
    return $this->select('users.id','users.first_name','users.last_name','users.email','users.userid', 'clients.name','salescenters.name as salescenter_name') 
    ->join('clients', 'clients.id', '=', 'users.client_id') 
    ->leftJoin('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
    ->where($params)->orderBy('first_name','asc')->get();
 }

     public function sendPasswordResetNotification($token)
    {
       $this->notify(new ResetPassword($token));
    }

    public static function boot()
    {
        parent::boot();
        static::updating(function($user)
        {

            if($user->isDirty('status') && $user->status == 'inactive' && $user->access_level == 'salesagent') {
                DB::table('oauth_access_tokens')
                    ->where('user_id', $user->id)
                    ->update([
                        'revoked' => true
                ]);
            }

        });
        
        static::updated(function ($model) {
            if($model->access_level == 'salesagent' && $model->status == 'inactive' && $model->session_id != NULL){
                return User::where('id',$model->id)
                    ->update( array(
                        'session_id' => ''
                    ));
            }
        });

        static::deleting(function($user)
        {
            \Log::info('deleting user related data...');
            UserLocation::where('user_id',$user->id)->delete();
            Salesagentdetail::where('user_id',$user->id)->delete();
            SalesAgentActivity::where('agent_id',$user->id)->delete();
            Salesagentlocation::where('salesagent_id',$user->id)->delete();
        });
        return true;
    }

    public function workers() {
        return $this->hasMany('App\models\UserTwilioId', 'user_id');
    }

    public function hasPermissionTo($permissions){
        $client_id = \Auth::user()->client_id;
        $user_roles = DB::table('role_user')->select('role_id')->where('user_id',\Auth::user()->id)->first();
        if (empty($user_roles)) {
            return false;
        }
        $role_id = $user_roles->role_id;
        $client_permissions = PermissionRoleClientSpecific::join('permissions','permissions.id','=','permission_role_client_specific.permission_id')->where('client_id',$client_id)->where('role_id',$role_id)->select('name')->pluck('name')->toArray();
        if(empty($client_permissions)){
            $client_permissions = Permission::join('permission_role','permission_role.permission_id','=','permissions.id')->where('role_id',$role_id)->select('name')->pluck('name')->toArray();
            $result = $this->checkPermissions($client_permissions,$permissions);
            return $result;
        }else{
            $result = $this->checkPermissions($client_permissions,$permissions);
            return $result;
        }
        
    }

    public function checkPermissions($client_permissions,$permissions){
        if(is_array($permissions)){
            foreach($permissions as $permission){
                $result = in_array($permission, $client_permissions);
                if($result){
                   return $result;
                }
            }
            return false;
       }else{
            return in_array($permissions, $client_permissions);
       }
    }

    public function can($permissions, $requiredAll = false){
        return $this->hasPermissionTo($permissions);        
    }

}
